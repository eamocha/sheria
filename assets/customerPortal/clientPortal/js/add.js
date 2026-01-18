jQuery(document).ready(function () {
    jQuery('input[type="text"], textarea, select').addClass('form-control');
    jQuery('div[requiredField="yes"]').each(function () {
        var container = jQuery(this);
        jQuery('.cp-screen-field', container).attr('required', '');
    });
    var container = jQuery('#add-screen-form');
    var prefixId="cp-date-";
    jQuery("[id^=" + prefixId + "]", container).each(function () {
        var id = jQuery(this).attr('id');
        switch (jQuery(this).attr('field-type')) {
            case 'date':
                setDatePicker(jQuery(this).parent(), container, datePickerOptions);
                break;
            case 'date_time':
                if (jQuery(this).parent().hasClass('date-picker')) {
                    setDatePicker(jQuery(this).parent(), container, datePickerOptions);
                }
                if (jQuery(this).hasClass('time')) {
                    jQuery(this).timepicker({
                        'timeFormat': 'H:i',
                    });
                }
                break;
            }
        });
    jQuery('.cp-screen-list-field').chosen({
        no_results_text: _lang.no_results_matched,
        placeholder_text: _lang.choose,
        width: '100%'
    });
    if (jQuery('#provider_group_id', '#add-screen-form').length > 0 && jQuery('#assignee', '#add-screen-form').length > 0) {
        jQuery('#provider_group_id', '#add-screen-form').change(function () {
            if (this.value > 0) {
                reloadUsersListByAssignedTeam(this.value, jQuery('#assignee', '#add-screen-form'));
            }
        });
    }
    if (jQuery('.related-assigned-team', '#add-screen-form').length > 0 && jQuery('.related-assigned-team', '#add-screen-form').val() > 0 && jQuery('#assignee', '#add-screen-form').length > 0) {
        reloadUsersListByAssignedTeam(jQuery('.related-assigned-team', '#add-screen-form').val(), jQuery('#assignee', '#add-screen-form'));
    }
});
function reloadUsersListByAssignedTeam(pGId, userListFieldId) {
    jQuery.ajax({
        url: getBaseURL() + 'modules/customer-portal/users/autocomplete/active',
        dataType: 'JSON',
        data: {
            join: ['provider_groups_users'],
            more_filters: {'provider_group_id': pGId},
            term: ''
        },
        success: function (results) {
            var newOptions = '<option value="">' + _lang.chooseUsers + '</option>';
            if (typeof results != "undefined" && results != null && results.length > 0) {
                for (i in results) {
                    newOptions += '<option value="' + results[i].id + '">' + results[i].firstName + ' ' + results[i].lastName + '</option>';
                }
            }
            userListFieldId.html(newOptions);
            jQuery(userListFieldId).trigger("chosen:updated");
        }, error: defaultAjaxJSONErrorsHandler});
}
