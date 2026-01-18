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
                url: getBaseURL() + 'customer_portal/customer_portal_users_autocomplete?user_id=%USER_ID&term=%QUERY&object_category=' + jQuery('#object-category').val() +'&object_id=' + jQuery('#ticket-id').val(),
                filter: function (data) {
                    return data;
                },
                replace: function (url, uriEncodedQuery) {
                    let requestedBy = jQuery('#requested-by-hidden');
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

    function submitForm() {
        var showCustomerPortalURL = getBaseURL().concat('cases/save_show_hide_customer_portal/');
        var showCustomerPortalForm = jQuery('#show-customer-portal-form');
        var showCustomerDialogModal = jQuery('#show-customer-dialog-modal');
        var showCustomerPortalFormData = showCustomerPortalForm.serialize();
        jQuery.ajax({
            url: showCustomerPortalURL,
            type: 'POST',
            dataType: 'JSON',
            data: showCustomerPortalFormData,
            beforeSend: function () {
                jQuery('#loader-global').show();
            },
            success: function (response) {
                if(response.result){
                   
                         // edit form
                         var legalCaseTopHeaderProfile = jQuery("#legal-case-top-header-profile", '#legal-case-top-header-container');
                         legalCaseTopHeaderProfile.removeClass(response.visible ? 'label-normal-style' : 'light_green').addClass(response.visible ? 'light_green' : 'label-normal-style');
                         var cpIcon = jQuery("#cp-icon", '#legal-case-top-header-container');
                         cpIcon.removeClass(response.visible ? 'client-portal-grey' : 'client-portal-blue').addClass(response.visible ? 'client-portal-blue' : 'client-portal-grey');
                         jQuery('#visibleToCP', jQuery('#legalCaseAddForm')).val(response.visible ? 1 : 0);
                         jQuery('#show-hide-btn', '#edit-legal-case-container').text(response.visible ? _lang.hideInCustomerPortal : _lang.showInCustomerPortal);
                         if(legalCaseTopHeaderProfile.hasClass("tooltipstered")){
                            legalCaseTopHeaderProfile.tooltipster("destroy");
                        }
                         legalCaseTopHeaderProfile.attr("title", response.visible ?  _lang.matterVisibleFromCP : _lang.hiddenInCP).tooltipster({
                             contentAsHTML: true,
                             timer: 22800,
                             animation: 'grow',
                             delay: 200,
                             theme: 'tooltipster-default',
                             touchDevices: false,
                             trigger: 'hover',
                             maxWidth: 350,
                             interactive: true
                         });
                         if (jQuery('#legalCaseGrid').length) 
                         {
                             jQuery('#legalCaseGrid').data('kendoGrid').dataSource.read();
                            }
                    
                    jQuery('.modal', '#show-customer-dialog').modal('hide');
                    pinesMessageV2({ty: 'success', m: _lang.showInCustomerPortalSuccess.sprintf(response.category === 'Litigation' ? [_lang.litigation.toLowerCase()] :  [_lang.matter.toLowerCase()])});
                } else {
                    jQuery(".inline-error").addClass('d-none');
                    if(response.info){
                        pinesMessageV2({ty: 'information', m: response.info});
                    }
                    displayValidationErrors(response.validation_errors, showCustomerDialogModal);
                }
            },
            complete: function () {
                jQuery('#loader-global').hide();
            },
            error: defaultAjaxJSONErrorsHandler
        });
    }

    function requestedByLookupCallback(record) {
        var name = (record.father ? record.firstName + ' ' + record.father + ' ' + record.lastName : record.firstName + ' ' + record.lastName);
        jQuery('#lookup-requested-by').val(name);
        jQuery('#requested-by').val(record.id);
    }
    
    function eraseMultiSelectOptions() {
        let watcherLookupContainer = jQuery(".watcher-lookup-container");
        var container = jQuery('#ticket-modifiable-fields');
        var boxContainer = jQuery("#selected-watchers", container);
        boxContainer.html("");
        jQuery('.lookup-box-container', watcherLookupContainer).removeClass('margin-bottom');
        jQuery('.autocomplete-helper', watcherLookupContainer).removeClass('d-none');
        boxContainer.css('border', 'none');
        boxContainer.removeClass('border');
    }
    
    function onEraseRequestedByLookup() {
        var container = jQuery('#ticket-modifiable-fields');
        let lookupWatcher = jQuery("#lookup-watchers", container);
        lookupWatcher.typeahead('destroy');
        let lookupDetails = {'lookupField': jQuery('#lookup-watchers', container), 'lookupContainer': 'watcher-lookup-container', 'errorDiv': 'lookupWatchers', 'boxName': 'watchers', 'boxId': '#selected-watchers', 'onSelect': 'showCustomerPortal.updateTicketWatchers'};
        showCustomerPortal.lookUpCustomerPortalUsers(lookupDetails, jQuery(container), true, false);
    }

    function onChangeRequestedByLookup() {
        showCustomerPortal.eraseMultiSelectOptions();
        showCustomerPortal.onEraseRequestedByLookup();
    }

    return {
        lookUpCustomerPortalUsers: lookUpCustomerPortalUsers,
        handleMultiselectLookupInput: handleMultiselectLookupInput,
        requestedByLookupCallback: requestedByLookupCallback,
        submitForm: submitForm,
        eraseMultiSelectOptions: eraseMultiSelectOptions,
        onEraseRequestedByLookup: onEraseRequestedByLookup,
        onChangeRequestedByLookup: onChangeRequestedByLookup
    };
}());