var container;
var availableUsers, availableUserGroups, operators, fieldsDetails;
jQuery(document).ready(function () {
    container = jQuery('.process-container', '#signature-center-container');
    initializeSelectPermissions(container);
    if (jQuery('#signature-id', container).val() == '') {
        lookupApproversSignees(jQuery('#lookup-signees1', container), 'signature[signees][1][signees][]', 'signature[signees][1][signees_types][]', '#selected-signees1', 'signees-container1', container, '', 'signature', true);
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
            var name = 'signature[criteria][' + count + '][value][]';
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
    jQuery('.id', clonedRow).attr('name', 'signature[criteria][' + rowCount + '][id]').val('');
    jQuery('select.field', clonedRow).attr('name', 'signature[criteria][' + rowCount + '][field]');
    jQuery('select.field', clonedRow).attr('onChange', 'getFieldDetails(this, \'' + rowCount + '\')');
    jQuery('select.operator', clonedRow).html('<option></option>').selectpicker('refresh').attr('name', 'signature[criteria][' + rowCount + '][operator]');
    jQuery('.value-container', clonedRow).html('<div data-field="value" class="inline-error d-none"></div>');
    jQuery('.inline-error', clonedRow).addClass('d-none');
    jQuery('tbody', container).append(clonedRow);
    jQuery('tbody', container).attr('data-count-row', rowCount);
}

function addAssigneeRow(container) {
    var rowCount = jQuery('tbody', container).attr('data-count-row');
    rowCount++;
    var clonedRow = jQuery('#default-signees tr').clone();
    clonedRow.attr('class', 'signees-row-' + rowCount);
    jQuery('.remove-row', clonedRow).removeClass('d-none').attr('onclick', 'removeRow(this,  \'' + rowCount + '\', \'signees\')');
    jQuery('select.users-selectized', clonedRow).attr('name', 'signature[signees][' + rowCount + '][users][]');
    jQuery('select.collaborators-selectized', clonedRow).attr('name', 'signature[signees][' + rowCount + '][collaborators][]');
    jQuery('select.user-groups-selectized', clonedRow).attr('name', 'signature[signees][' + rowCount + '][user_groups][]');
    jQuery('.signees-label input', clonedRow).attr('name', 'signature[signees][' + rowCount + '][label]');
    jQuery('.signees-order input', clonedRow).attr('name', 'signature[signees][' + rowCount + '][rank]');
    jQuery('input.board-member-checkbox', clonedRow).attr('id', 'bm-'+rowCount);
    jQuery('label.label-selected', jQuery('.signees-board-member', clonedRow)).attr('for', 'bm-'+rowCount);
    jQuery('input.board-member-checkbox', clonedRow).attr('onclick', 'boardDirectorRolesForm(this, \'#is-board-member\', jQuery(".signees-row-' + rowCount + '", "#signees-section"), \'signature_center\');');
    jQuery('input#is-board-member', clonedRow).attr('name', 'signature[signees][' + rowCount + '][is_board_member]');
    jQuery('input#board-member-role', clonedRow).attr('name', 'signature[signees][' + rowCount + '][board_member_role]');
    jQuery('input.shareholder-checkbox', clonedRow).attr('id', 'sh-'+rowCount);
    jQuery('label.label-selected', jQuery('.signees-shareholder', clonedRow)).attr('for', 'sh-'+rowCount);
    jQuery('input.shareholder-checkbox', clonedRow).attr('onclick', 'toggleCheckbox(this, \'#is-shareholder\', jQuery(".signees-row-' + rowCount + '", "#signees-section"));');
    jQuery('input#is-shareholder', clonedRow).attr('name', 'signature[signees][' + rowCount + '][is_shareholder]');
    jQuery('input.requester-manager-checkbox', clonedRow).attr('id', 'rm-'+rowCount);
    jQuery('label.label-selected', jQuery('.signees-requester-manager', clonedRow)).attr('for', 'rm-'+rowCount);
    jQuery('input.requester-manager-checkbox', clonedRow).attr('onclick', 'toggleCheckbox(this, \'#is-requester-manager\', jQuery(".signees-row-' + rowCount + '", "#signees-section"));');
    jQuery('input#is-requester-manager', clonedRow).attr('name', 'signature[signees][' + rowCount + '][is_requester_manager]');
    initializeSelectPermissions(clonedRow);
    jQuery('.signees-container', clonedRow).attr('id', 'signees-container' + rowCount);
    jQuery('.selected-signees', clonedRow).attr('id', 'selected-signees' + rowCount);
    jQuery('.lookup-signees', clonedRow).attr('id', 'lookup-signees' + rowCount);
    lookupApproversSignees(jQuery('#lookup-signees' + rowCount, clonedRow), 'signature[signees][' + rowCount + '][signees][]', 'signature[signees][' + rowCount + '][signees_types][]', '#selected-signees' + rowCount, 'signees-container' + rowCount, jQuery(container), '', 'signature', true);
    jQuery('.inline-error', clonedRow).addClass('d-none');
    jQuery('tbody', container).append(clonedRow);
    jQuery('tbody', container).attr('data-count-row', rowCount);
}


function deleteRecord(params) {
    jQuery.ajax({
        url: getBaseURL('contract') + 'signature_center/delete_details',
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

function signatureCenterSubmit(id) {
    id = id || false;
    var formData = jQuery('#signature-center-form', '#signature-center-container').serializeArray();
    jQuery.ajax({
        url: getBaseURL('contract') + 'signature_center/' + (id ? 'edit' : 'add'),
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
                    window.location = getBaseURL('contract') + 'signature_center'; 
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