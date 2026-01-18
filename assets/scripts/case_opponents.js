 function open_party_form(caseId, opponent_id=null) {

        var data = {
            caseId: caseId,
            party_id:opponent_id
        };

        jQuery.ajax({
            url: getBaseURL() + "cases/open_party_form",
            type: "POST",
            dataType: "JSON",
            data: data,   
            beforeSend: function() {
                jQuery('#loader-global').show();
            },
            success: function(response) {
                if (response.html) {
                    if (jQuery('#partyForm-dialog').length <= 0) {
                        jQuery('<div id="partyForm-dialog"></div>').appendTo("body");
                        var partyFromDialog = jQuery('#partyForm-dialog');
                        partyFromDialog.html(response.html);
                        commonModalDialogEvents(partyFromDialog);
                        initializeModalSize(partyFromDialog,0.3,"auto")

                        jQuery("#submitPartyBtn", partyFromDialog).click(function() {
                            partyFormSubmit(partyFromDialog,caseId,party_id);
                        });
                        jQuery(partyFromDialog).find('input').keypress(function(e) {
                            // Enter pressed?
                            if (e.which == 13) {
                                partyFormSubmit(partyFromDialog,caseId,party_id);
                            }
                        });
                    }
                } else {
                    pinesMessage({ ty: 'error', m: _lang.feedback_messages.updatesFailed });
                }
            },
            complete: function() {
                jQuery('#loader-global').hide();
            },
            error: defaultAjaxJSONErrorsHandler
        });
    }

    function partyFormSubmit(partyFromDialog, case_id,party_id) {
        var form = jQuery('#partyForm', partyFromDialog);
        var partyId=jQuery("#party-member-id").val();
          

        if (!form.length) {
            console.error('partyForm not found');
            return;
        }
         
    var formData = form.serializeArray();    
    formData.push({ name: "caseContactId", value: partyId }); 
     formData.push({ name: "caseCompanyId", value: partyId }); 

        jQuery.ajax({
            url: getBaseURL() + 'cases/save_party',
            type: 'POST',
            data: formData,
            dataType: 'json',
            beforeSend: function () {
                jQuery("#loader-global").show(); // Optional loading indicator
            },
            success: function (response) {
                if (response.success) {
                    pinesMessage({ ty: 'success',  m: response.message || 'Party saved successfully.'   });
                            
                   jQuery(".modal", partyFromDialog).modal("hide");

                    if (typeof refreshPartyList === 'function') {
                        refreshPartyList(case_id); // Optional function if implemented
                    }
                } else {
                    pinesMessage({ ty: 'error',   m: response.message || 'Failed to save party.' });
                }
            },
            error: function (xhr, status, error) {
                pinesMessage({ ty: 'error',   m: 'An error occurred: ' + error  });
            },
            complete: function () {
                jQuery("#loader-global").hide();
            }
        });
    }
    function refreshPartyList(case_id) {
         var data = {
            case_id: case_id
        };

        jQuery.ajax({
            url: getBaseURL() + 'cases/refresh_party_list',
             data: data,
            type: 'POST',
            success: function (response) {
              if (response.html) { 
                jQuery('#casePartiesBox').replaceWith(response.html);
            } else {
                pinesMessage({ ty: 'error', m: 'Failed to retrieve updated party list.' });
            }
            }
        });
    }