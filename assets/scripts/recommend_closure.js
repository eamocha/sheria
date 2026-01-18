let recommendCaseClosure = (function() {
    'use strict';


    function submitForm() {
        var showClosureUrl = getBaseURL().concat('cases/save_recommend_case_closure/');
        var showClosureForm = jQuery('#show-case-closure-form');
        var showClosureModal = jQuery('#show-case-closure-modal');
        var showClosureFormData = showClosureForm.serialize();
        jQuery.ajax({
            url: showClosureUrl,
            type: 'POST',
            dataType: 'JSON',
            data: showClosureFormData,
            beforeSend: function () {
                jQuery('#loader-global').show();
            },
            success: function (response) {
                if(response.result){
                    // 1. check if saving was successful
                    // 2. check if was successiful, check if it was an appeal or case file closure
                    // 3. it was an appeal, show success message, hide modal and initiate the openning of the new case file modal
                    //else if it was a file closure, show success message, hide modal.
                    // else show relevant error message
                   
                       
            
                    
                    jQuery('.modal', '#show-customer-dialog').modal('hide');
                    pinesMessageV2({ty: 'success', m: _lang.showInCustomerPortalSuccess.sprintf(response.category === 'Litigation' ? [_lang.litigation.toLowerCase()] :  [_lang.matter.toLowerCase()])});
                } else {
                    jQuery(".inline-error").addClass('d-none');
                    if(response.info){
                        pinesMessageV2({ty: 'information', m: response.info});
                    }
                    displayValidationErrors(response.validation_errors, showClosureModal);
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
        jQuery('#lookup-closure-requested-by').val(name);
        jQuery('#closure-requested-by-hidden').val(record.id);
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
        var container = jQuery('#recommend-case-closure-fields');
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