jQuery(document).ready(function(){
    jQuery('[data-toggle="tooltip"]').tooltip();
    var container = jQuery('#ticket-modifiable-fields');
    var lookupDetails = {'lookupField': jQuery('#lookup-requested-by', container), 'errorDiv': 'requestedBy', 'hiddenId': '#requested-by', 'onSelect': 'updateTicketRequester'};
    lookUpCustomerPortalUsers(lookupDetails, container);
    lookupDetails = {'lookupField': jQuery('#lookup-watchers', container), 'lookupContainer': 'watcher-lookup-container', 'errorDiv': 'lookupWatchers', 'boxName': 'watchers', 'boxId': '#selected-watchers', 'onSelect': 'updateTicketWatchers'};
    lookUpCustomerPortalUsers(lookupDetails, jQuery(container), true);
});

/*
* clear requestedBy if the user removed did empty the field or typed a none valuid requester
*/
function clearRequestedBy() {
    if (jQuery('#requested-by').val() == '') {
        updateTicketRequester();
    }
}

/*
* update the ticket requester when user select data
*/
function updateTicketRequester() {
    jQuery.ajax({
        url: 'modules/customer-portal/tickets/update_ticket_requester',
        type: "post",
        data: {ticketId: jQuery("#ticket-id").val(), requestedBy: jQuery('#requested-by').val()},
        dataType: "JSON",
        success: function (response) {
            if (response.status && response.user_ticket_permission == "none") {
                window.location.href = getBaseURL('customer-portal') + 'tickets?ty=' + response.message.type + '&m=' + response.message.text;
            } else if (response.status && response.user_ticket_permission == "read") {
                window.location.reload();
            } else if (response.status != null) {
                if (typeof response.modifiedOn !== undefined) {
                    jQuery('#last-update').html(response.modifiedOn);
                }
                pinesMessage({ty: response.message.type, m: response.message.text});
            }
        },
        error: defaultAjaxJSONErrorsHandler
    });
}

/*
* update ticket watchers when user select data
*/
function updateTicketWatchers() {
    var watchers_ids = [];
    jQuery("input[name*='watchers']", "#selected-watchers").each(function() {
        watchers_ids.push(jQuery(this).val());
    });
    jQuery.ajax({
        url: "modules/customer-portal/tickets/update_ticket_watchers",
        type: "post",
        data: {ticketId: jQuery("#ticket-id").val(), watchers: watchers_ids},
        dataType: "JSON",
        success: function (response) {
            if (response.status && response.user_ticket_permission == "none") {
                window.location.href = getBaseURL('customer-portal') + 'tickets?ty=' + response.message.type + '&m=' + response.message.text;
            } else if (response.status != null) {
                pinesMessage({ty: response.message.type, m: response.message.text});
            }
        },
        error: defaultAjaxJSONErrorsHandler
    });
}
