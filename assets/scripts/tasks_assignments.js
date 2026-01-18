jQuery(document).ready(function () {
    jQuery('.record', '#task-assignment-container').each(function () {
        radioButtonsEvents(this);
    });
    if (jQuery('.default-row', '#task-assignment-container').hasClass('d-none')) {
        jQuery('input', jQuery('.default-row', '#task-assignment-container')).attr('disabled', 'disabled');
        jQuery('select', jQuery('.default-row', '#task-assignment-container')).attr('disabled', 'disabled');
    }
});
function updateFields(clonedContainer) {
    jQuery("input[type=radio][value='specific_user']", clonedContainer).attr('checked', 'checked');
    jQuery('input', clonedContainer).removeAttr('disabled');
}
function hideAssignee(container) {
    jQuery('.assignee-visibility', container).val(0);
    jQuery('.assignee-visibility-checkbox', container).prop("checked", false).attr('disabled', 'disabled');
}
function radioButtonsEvents(container) {
    jQuery('input[type=radio]', container).on('change', function () {
        if (this.value == 'specific_user') {
            jQuery('select', jQuery('.specific-user-lookup', container)).removeAttr('disabled');
            jQuery('.specific-user-lookup', container).removeClass('d-none');
            jQuery('.assignee-visibility-checkbox', container).removeAttr('disabled');
        } else {
            jQuery('select', jQuery('.specific-user-lookup', container)).attr('disabled', 'disabled');
            jQuery('.specific-user-lookup', container).addClass('d-none');
            jQuery('.assignee-visibility', container).val(0);
            jQuery('.assignee-visibility-checkbox', container).prop("checked", false).attr('disabled', 'disabled');
        }
    });
}
//check if there is no more row visible => then show the default row
function checkRowCount() {
    if (jQuery('tr.record', '#task-assignment-container').length === 1 && jQuery('tr.record', '#task-assignment-container').hasClass('d-none')) {
        jQuery('tr.record', '#task-assignment-container').removeClass('d-none');
        jQuery('input', jQuery('.default-row', '#task-assignment-container')).removeAttr('disabled');
        jQuery('select', jQuery('.default-row', '#task-assignment-container')).removeAttr('disabled');
    }
}