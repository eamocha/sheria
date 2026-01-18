jQuery(function () {
    var formObj = jQuery('#formContainer');
    formObj.validationEngine({validationEventTrigger: "submit", autoPositionUpdate: true, promptPosition: 'bottomRight', scroll: false});
    

    initializeSelectNotifications(jQuery('#contract-sla-management-form'));
    partyAutocompleteMultiOption ('lookup-contacts', 2, setContactCompanySlaSelectedUser,true);


    jQuery('.sla-statuses').chosen({
        no_results_text: _lang.no_results_matched,
        placeholder_text: _lang.chooseCaseStatus,
        width: '100%',
        onResultsShow: function () {
            var selects = jQuery('.sla-statuses');
            var selected = [];
            selects.find("option").each(function () {
                if (this.selected) {
                    selected[this.value] = this;
                }
            }).each(function () {
                this.disabled = selected[this.value] && selected[this.value] !== this;
            });
            selects.trigger("chosen:updated");
        }
    });
    let workflowId = jQuery('#workflow-id').val();
    let sla_id = jQuery('#id').val();
    jQuery.ajax({ 
        url: getBaseURL('contract') + 'sla_management/get_contract_type_by_workflow',
        type: "POST",
        data: {workflow_id:workflowId,
        sla_id:sla_id},
        dataType: "JSON",
        beforeSend: function () {
            jQuery("#loader-global").show();
        },
        success: function (response) {
            jQuery('#sla-contract-type').html(response.html_case_types);

            if (response.result) {
            }
        }, complete: function () {
            jQuery("#loader-global").hide();
        },
        error: defaultAjaxJSONErrorsHandler
    });

    jQuery("#workflow-id").change(function () {
        var workflow_id= jQuery(this).val();  
        loadStatuses(workflow_id,jQuery('.sla-statuses'));    
         jQuery.ajax({ 
            url: getBaseURL('contract') + 'sla_management/get_contract_type_by_workflow',
            type: "POST",
            data: {workflow_id:workflow_id},
            dataType: "JSON",
            beforeSend: function () {
                jQuery("#loader-global").show();
            },
            success: function (response) {
                jQuery('#sla-contract-type').html(response.html_case_types);
            }, complete: function () {
                jQuery("#loader-global").hide();
            },
            error: defaultAjaxJSONErrorsHandler
        });

    });

    //contract_types form
    let radioInputAdd = 'input:radio[id=radio-type]';
    jQuery(radioInputAdd).change(function() {
    jQuery('#radio-type-hidden').val(''+jQuery(this).val());
    if (jQuery(this).val() == 'custom') {
    jQuery('#sla-contract-type').removeClass('d-none');
    } else {
   jQuery('#sla-contract-type').addClass('d-none');
   }
   });     
   if(jQuery('#radio-type-hidden').val() == 'any'){

    jQuery('#sla-contract-type').addClass('d-none');
   }

   //parties-form
   let radioInputAddParty = 'input:radio[id=radio-party]';
    jQuery(radioInputAddParty).change(function() {
    jQuery('#radio-party-hidden').val(''+jQuery(this).val());
    if (jQuery(this).val() == 'custom') {
    jQuery('#sla-contract-party').removeClass('d-none');
    } else {
   jQuery('#sla-contract-party').addClass('d-none');
   }
   });     
   if(jQuery('#radio-party-hidden').val() == 'any'){

    jQuery('#sla-contract-party').addClass('d-none');
   }
});

function setContactCompanySlaSelectedUser(contact){
    var container = jQuery('#contract-sla-management-form');
    var theWrapper = jQuery('#selected-parties', container);
    var name = (contact.contact_id)?(contact.firstName + ' ' + contact.lastName ): contact.name;
    var contact_type = (contact.contact_id)? "contact" : "company";
    if (contact.id && !jQuery('#contact-party' + contact.id, theWrapper).length ) {
        theWrapper.append(jQuery('<div class="flex-item-box row multi-option-selected-items no-margin w-100 height-30px" id="' + contact_type + '-' + contact.id + '">' + name + ' - ' + contact_type + '</div>').append(jQuery('<input type="hidden" value="' + contact.id + '" name="parties[id][]" /><input type="hidden" value="' + contact_type +'" name="parties[type][]" />')).append(jQuery('<a href="javascript:;" class="btn btn-default btn-sm btn-link pull-right flex-end-item" tabindex="-1" onclick="jQuery(this.parentNode).remove();"><i class="fa-solid fa-xmark"></i></a>')));
    }
    jQuery('#lookup-contacts', container).text("");
}

function submitScreenForm(id) {
    id = id || false;
    var container = jQuery('#sla_form');
    var formData = container.serializeArray();

    jQuery.ajax({
        url: getBaseURL('contract') + 'sla_management/' + (id ? 'edit' : 'add'),
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
                (id)? pinesMessage({ty: 'success', m: _lang.feedback_messages.updatesSavedSuccessfully}):pinesMessage({ty: 'success', m: _lang.feedback_messages.slaAddedSuccessfully}) ;
                window.location = getBaseURL('contract') + 'sla_management/index';
            } else {
                if (typeof response.validation_errors !== 'undefined') {
                    displayValidationErrors(response.validation_errors, container);
                }
            }
        }, complete: function () {
            jQuery('#form-submit', container).removeAttr('disabled');
            jQuery('.loader-submit', container).removeClass('loading');
        },
    });
}


function loadStatuses(workflowId, subTypeContainer, selectedId) {
    selectedId = selectedId || 0;
    jQuery.ajax({
        url: getBaseURL('contract') + 'sla_management/get_status_by_workflow',
        dataType: 'JSON',
        data: { 'workflow_id': workflowId },
        type: 'GET',
        success: function (results) {
            var newOptions='';
            if (typeof results != "undefined" && results != null && Object.keys(results).length > 0) {
                for (const key in results) {
                    newOptions += '<option value="' + key + '">' + results[key] + '</option>';
                }
            }
            subTypeContainer.empty(); //remove all child nodes
            subTypeContainer.append(newOptions);
            subTypeContainer.trigger("chosen:updated");
                       
        }, error: defaultAjaxJSONErrorsHandler
    });
}

function contractTypeSelectize(){
    jQuery('.contract-type-selectize').selectize({
        plugins: [
            'remove_button'
        ],
        valueField: 'id',
        labelField: 'name',
        searchField: ['name'],
        options: availableTypes,
        createOnBlur: true,
        groups: [
        ],
        optgroupField: 'class',
        render: {
            option: function(item, escape) {
                    if (jQuery.inArray(item.id,disabledOptions)!== -1) {
                    return '<div style="pointer-events: none; color: #aaaaaa;">' + escape(item.name) + '</div>';
                }
                return '<div>' + escape(item.name) + '</div>';
            }
        }
    });
}