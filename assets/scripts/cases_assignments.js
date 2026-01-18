jQuery(document).ready(function () {
    jQuery('.record', '#matter-assignment-container').each(function () {
        radioButtonsEvents(this);
        assignedTeamEvents(this);
    });
    jQuery('.record', '#litigation-assignment-container').each(function () {
        radioButtonsEvents(this);
        assignedTeamEvents(this);
    });
});
function assignedTeamEvents(container) {
    jQuery('select[data-id=assigned-team-id]', container).on('change', function () {
        reloadUsersListByAssignedTeam(this.value, jQuery('select[data-id=user-id]', container));
    });
}
function updateFields(clonedContainer, category, rowCount) {
    jQuery('.tooltipstered', clonedContainer).remove();
    jQuery("input[type=radio][value='no']", clonedContainer).attr('checked', 'checked');
    jQuery('.assigned-user', clonedContainer).attr('name', category + '[user_id][' + rowCount + ']');
    jQuery('.specific-user-lookup', clonedContainer).addClass('d-none');
    jQuery('select', jQuery('.specific-user-lookup', clonedContainer)).attr('disabled', 'disabled');
    jQuery('.category', clonedContainer).attr('name', category + '[category][' + rowCount + ']').val(category);
    jQuery('.all-types', clonedContainer).remove();
    jQuery('.assigned-team', clonedContainer).attr('name', category + '[assigned_team][' + rowCount + ']');
    jQuery('.assigned-team-visibility', clonedContainer).attr('name', category + '[visible_assigned_team][' + rowCount + ']').val(1);
    jQuery("input[type=checkbox]", jQuery('.assigned-team-container', clonedContainer)).attr('onclick', '');
    jQuery("input[type=checkbox]", jQuery('.assigned-team-container', clonedContainer)).on('click', function () {
        updateHiddenFields(this, jQuery('.assigned-team-visibility', clonedContainer), '#' + category + '-assignment-container')
    });
    assignedTeamEvents(clonedContainer);
}