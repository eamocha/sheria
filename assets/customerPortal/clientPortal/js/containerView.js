jQuery(document).ready(function(){
    jQuery('[data-toggle="tooltip"]').tooltip();
    var container = jQuery('#modifiable-fields');
    lookupDetails = {'lookupField': jQuery('#lookup-watchers', container), 'lookupContainer': 'watcher-lookup-container', 'errorDiv': 'look_up_watchers', 'boxName': 'watchers', 'boxId': '#selected-watchers', 'onSelect': 'updateContainerWatchers'};
    lookUpCustomerPortalUsers(lookupDetails, jQuery(container), true);
});

function updateContainerWatchers() {
    var watchers_ids = [];
    jQuery("input[name*='watchers']", "#selected-watchers").each(function() {
        watchers_ids.push(jQuery(this).val());
    });
    jQuery.ajax({
        url: "modules/customer-portal/containers/update_container_watchers",
        type: "post",
        data: {container_id: jQuery("#container-id").val(), watchers: watchers_ids},
        dataType: "JSON",
        success: function (response) {
            if (response.status && response.user_container_permission == "none") {
                window.location.href = getBaseURL('customer-portal') + 'containers?ty=' + response.message.type + '&m=' + response.message.text;
            } else if (response.status != null) {
                pinesMessage({ty: response.message.type, m: response.message.text});
            }
        },
        error: defaultAjaxJSONErrorsHandler
    });
}
