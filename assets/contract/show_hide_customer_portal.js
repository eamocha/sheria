let showCustomerPortal = (function() {
    'use strict';

    /**
     * Get Customer Portal User by term query
     *
     * @param lookupDetails
     * @param container
     * @param isBoxContainer
     */
    function lookUpCustomerPortalUsers(lookupDetails, container, isBoxContainer) {
        isBoxContainer = isBoxContainer || false;
        // Instantiate the Bloodhound suggestion engine
        var mySuggestion = new Bloodhound({
            datumTokenizer: function (datum) {
                return Bloodhound.tokenizers.whitespace('');
            },
            queryTokenizer: Bloodhound.tokenizers.whitespace,
            remote: {
                url: getBaseURL() + 'customer_portal/customer_portal_users_autocomplete?user_id=%USER_ID&term=%QUERY&object_category=contract&object_id=' + jQuery('#contract-id').val(),
                filter: function (data) {
                    return data;
                },
                replace: function (url, uriEncodedQuery) {
                    let requestedBy = jQuery('#requester-id');
                    if (requestedBy.val()) {
                        var keyValue = encodeURIComponent(requestedBy.val());
                        return url.replace('%QUERY', uriEncodedQuery).replace('%USER_ID', keyValue);
                    }
                    return url.replace('%QUERY', uriEncodedQuery).replace('%USER_ID', '');
                },
                'cache': false,
                wildcard: '%QUERY',
            }
        });
        // Initialize the Bloodhound suggestion engine
        mySuggestion.initialize();
        // Instantiate the Typeahead UI
        jQuery(lookupDetails['lookupField']).typeahead({
                hint: false,
                highlight: true,
                minLength: 3
            },
            {
                source: mySuggestion.ttAdapter(),
                display: function (item) {
                    if (!isBoxContainer) {
                        return item.firstName + ' ' + item.lastName;
                    }
                },
                templates: {
                    empty: [
                        '<div class="empty"> ' + _lang.noMatchesFound + '</div>'].join('\n'),
                    suggestion: function (data) {
                        return '<div>' + data.firstName + ' ' + data.lastName + '</div>'
                    }
                }
            }).on('typeahead:selected', function (obj, datum) {
            if (isBoxContainer) {
                jQuery("div", jQuery('.' + lookupDetails['lookupContainer'], container)).find("[data-field=" + lookupDetails['errorDiv'] + "]").addClass('d-none');
                lookupBoxContainerDesign(jQuery('.' + lookupDetails['lookupContainer'], container));
                setNewBoxElement(lookupDetails['boxId'], lookupDetails['lookupContainer'], '#' + container.attr('id'), {id: datum.id, value: datum.firstName + ' ' + datum.lastName, name: lookupDetails['boxName']}, lookupDetails['onSelect'], lookupDetails['onSelectParameters']);
            } else {
                if (typeof lookupDetails['onSelect'] !== "undefined") {
                    typeof lookupDetails['onSelectParameters'] !== "undefined" ? window[lookupDetails['onSelect']](lookupDetails['onSelectParameters']) : window[lookupDetails['onSelect']]();
                }
            }
        }).on('typeahead:asyncrequest', function () {
        });
        if (!isBoxContainer) {
            lookupCommonFunctions(lookupDetails['lookupField'], lookupDetails['hiddenId'], lookupDetails['errorDiv'], container);
        }
    }

    function handleMultiselectLookupInput(lookupId) {
        if (jQuery(lookupId).val() != '') {
            jQuery(lookupId).typeahead('val', '');
        }
    }

    /*
    * update the ticket requester when user select data
    */
    function updateTicketRequester() {
        jQuery.ajax({
            url: getBaseURL() + 'customer_portal/update_ticket_requester',
            type: "post",
            data: {ticketId: jQuery("#ticket-id").val(), requestedBy: jQuery('#requester-id').val()},
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

    function requestedByLookupCallback(record) {
        var name = (record.father ? record.firstName + ' ' + record.father + ' ' + record.lastName : record.firstName + ' ' + record.lastName);
        jQuery('#requester-lookup').val(name);
        jQuery('#requester-id').val(record.id);
    }
    
    function eraseMultiSelectOptions() {
        let watcherLookupContainer = jQuery(".watcher-lookup-container");
        let formContainer = jQuery("#show-customer-portal-form");
        var boxContainer = jQuery("#selected-watchers", formContainer);
        boxContainer.html("");
        jQuery('.lookup-box-container', watcherLookupContainer).removeClass('margin-bottom');
        jQuery('.autocomplete-helper', watcherLookupContainer).removeClass('d-none');
        boxContainer.css('border', 'none');
        boxContainer.removeClass('border');
    }
    
    function onEraseRequestedByLookup() {
        let formContainer = jQuery("#show-customer-portal-form");
        let lookupWatcher = jQuery("#lookup-watchers", formContainer);
        var container = jQuery('#fields-to-modify');
        lookupWatcher.typeahead('destroy');
        let lookupDetails = {'lookupField': jQuery('#lookup-watchers', container), 'lookupContainer': 'watcher-lookup-container', 'errorDiv': 'lookupWatchers', 'boxName': 'watchers', 'boxId': '#selected-watchers', 'onSelect': 'showCustomerPortal.updateTicketWatchers'};
        showCustomerPortal.lookUpCustomerPortalUsers(lookupDetails, jQuery(container), true, false);
    }

    return {
        lookUpCustomerPortalUsers: lookUpCustomerPortalUsers,
        handleMultiselectLookupInput: handleMultiselectLookupInput,
        updateTicketRequester: updateTicketRequester,
        requestedByLookupCallback: requestedByLookupCallback,
        // submitForm: submitForm,
        eraseMultiSelectOptions: eraseMultiSelectOptions,
        onEraseRequestedByLookup: onEraseRequestedByLookup
    };
}());