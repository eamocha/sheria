function activateTabs() {
    jQuery('#instruments-table').DataTable({
        /* Disable initial sort */
        "aaSorting": []
    });
    setActiveTab('contracts');
}
function conveyancingInstrumentForm(module,add_edit="add",id=0) {
    jQuery.ajax({
        dataType: 'JSON',
        url: getBaseURL(module) + 'conveyancing/'+add_edit+'/'+id,
        
        data: {id: id},
        beforeSend: function () {
            jQuery('#loader-global').show();
        },
        success: function (response) {
            if (response.result) {
                if (response.html) {
                    if (jQuery('#conveyancing-instrument-container').length === 0) {
                        jQuery('<div id="conveyancing-instrument-container" class="primary-style"></div>').appendTo("body");
                    }
                    var conveyancingInstrumentContainer = jQuery('#conveyancing-instrument-container');
                    conveyancingInstrumentContainer.html(response.html);

                    var newConveyancingModal = conveyancingInstrumentContainer.find('#newConveyancingModal');

                    // --- Typeahead.js and Bloodhound for Parties and Staff Name ---

                    // Bloodhound for Contacts (reusable for staff and parties when type is contact)
                    var contactsBloodhound = new Bloodhound({
                        datumTokenizer: Bloodhound.tokenizers.obj.whitespace('fullName'),
                        queryTokenizer: Bloodhound.tokenizers.whitespace,
                        remote: {
                            url: getBaseURL() + 'contacts/autocomplete?term=%QUERY',
                            wildcard: '%QUERY',
                            transform: function(response) {
                                return jQuery.map(response, function(contact) {
                                    // Assuming contacts/autocomplete returns firstName and lastName
                                    contact.fullName = contact.firstName + ' ' + contact.lastName;
                                    return contact;
                                });
                            }
                        }
                    });

                    // Bloodhound for Companies (for parties when type is company)
                    var companiesBloodhound = new Bloodhound({
                        datumTokenizer: Bloodhound.tokenizers.obj.whitespace('name'),
                        queryTokenizer: Bloodhound.tokenizers.whitespace,
                        remote: {
                            url: getBaseURL() + 'companies/autocomplete?term=%QUERY',
                            wildcard: '%QUERY',
                            transform: function(response) {
                                return jQuery.map(response, function(company) {
                                    // Company data has a 'name' field directly
                                    return company;
                                });
                            }
                        }
                    });

                    // Function to initialize Typeahead for 'parties' based on 'contact_type'
                    function initializePartiesTypeahead() {
                        var partiesInput = newConveyancingModal.find('#parties');
                        var partiesHiddenId = newConveyancingModal.find('#parties_id');
                        var contactType = newConveyancingModal.find('#contact_type').val();

                        // Destroy existing Typeahead instance to prevent issues
                        if (partiesInput.data('tt-typeahead')) {
                            partiesInput.typeahead('destroy');
                        }

                        // Clear values when type changes
                        // partiesInput.val('');
                        // partiesHiddenId.val('');

                        var source;
                        var displayKey;
                        var emptyTemplate;

                        if (contactType === 'company') {
                            source = companiesBloodhound;
                            displayKey = 'name';
                            emptyTemplate = [
                                '<div class="empty-message">',
                                'No matching companies found. <a href="javascript:;" onclick="companyAddForm();">Add Company</a>',
                                '</div>'
                            ].join('\n');
                        } else { // 'contact'
                            source = contactsBloodhound;
                            displayKey = 'fullName';
                            emptyTemplate = [
                                '<div class="empty-message">',
                                'No matching contacts found. <a href="javascript:;" onclick="contactAddForm(true);">Add Contact</a>',
                                '</div>'
                            ].join('\n');
                        }

                        partiesInput.typeahead({
                            highlight: true,
                            minLength: 1
                        }, {
                            name: 'parties-suggestions',
                            display: displayKey,
                            source: source,
                            templates: {
                                empty: emptyTemplate,
                                suggestion: function (data) {
                                    // Customize suggestion display based on type
                                    if (contactType === 'company') {
                                        return '<div><strong>' + data.name + '</strong></div>';
                                    } else {
                                        return '<div><strong>' + data.fullName + '</strong> <small>(' +  (data.email ? data.email : 'No email')  + ')</small></div>';
                                    }
                                }
                            }
                        }).on('typeahead:select typeahead:autocomplete', function (event, suggestion) {
                            partiesHiddenId.val(suggestion.id);
                            partiesInput.typeahead('val', suggestion[displayKey]);
                        }).on('change', function () {
                            if (!jQuery(this).typeahead('val')) {
                                partiesHiddenId.val('');
                            }
                        });
                    }

                    // Initialize Typeahead for 'parties' on modal load
                    initializePartiesTypeahead();

                    // Re-initialize Typeahead for 'parties' when 'contact_type' changes
                    newConveyancingModal.find('#contact_type').on('change', function() {
                        initializePartiesTypeahead();
                    });


                    // Typeahead for Staff Name (initiated_by)
                    var initiatedByInput = newConveyancingModal.find('#initiated_by');
                    var initiatedByHiddenId = newConveyancingModal.find('input[name="initiated_by_id"]');

                    initiatedByInput.typeahead({
                        highlight: true,
                        minLength: 1
                    }, {
                        name: 'staff-suggestions',
                        display: 'fullName',
                        source: contactsBloodhound,
                        templates: {
                            empty: [
                                '<div class="empty-message">',
                                'No matching staff found. <a href="javascript:;" onclick="contactAddForm(true);">Add Staff</a>', // MODIFIED LINE
                                '</div>'
                            ].join('\n'),
                            suggestion: function (data) {
                              
                                return '<div><strong>' + data.fullName + '</strong> <small>(' + (data.email ? data.email : 'No email') + ')</small></div>';

                            }
                        }
                    }).on('typeahead:select typeahead:autocomplete', function (event, suggestion) {
                        initiatedByHiddenId.val(suggestion.id);
                        initiatedByInput.typeahead('val', suggestion.fullName);
                    }).on('change', function () {
                        if (!jQuery(this).typeahead('val')) {
                            initiatedByHiddenId.val('');
                        }
                    });


                    // --- Existing Modal Initialization and Event Binding ---
                    newConveyancingModal.find('.select-picker').selectpicker();


                    newConveyancingModal.find('.custom-file-input').on('change', function() {
                        var files = jQuery(this)[0].files;
                        var label = files.length > 1 ? files.length + ' files selected' : files[0].name;
                        jQuery(this).next('.custom-file-label').html(label);
                    });


                    initializeModalSize(conveyancingInstrumentContainer);
                    commonModalDialogEvents(conveyancingInstrumentContainer);

                    jQuery("#save-conveyancing-btn", conveyancingInstrumentContainer).off('click').on('click', function () {
                        submitConveyancingForm(module, conveyancingInstrumentContainer,add_edit,id);
                    });

                    newConveyancingModal.modal('show');

                    newConveyancingModal.on('hidden.bs.modal', function () {
                        jQuery(this).remove();
                        jQuery('#conveyancing-instrument-container').remove();
                    });

                } else {
                    pinesMessage({ty: 'error', m: _lang.feedback_messages.conveyancingInstrumentError});
                }
            } else {
                if (typeof response.display_message !== 'undefined') {
                    pinesMessage({ty: 'warning', m: response.display_message});
                }
            }
        },
        complete: function () {
            jQuery('#loader-global').hide();
        },
        error: defaultAjaxJSONErrorsHandler
    });
}

function submitConveyancingForm(module, container,add_edit,id) {

    const form = document.getElementById('conveyancing-form');
    const formData = new FormData(form);
    jQuery.ajax({
        url: getBaseURL(module) + 'conveyancing/'+add_edit+'/'+id,
        dataType: 'JSON',
        type: 'POST',
        contentType: false,
        cache: false,
        processData: false,
        data: formData,
        beforeSend: function () {
             jQuery('#loader-global').show();
            jQuery('.loader-submit', container).addClass('loading');
            jQuery('.modal-save-btn').attr('disabled', 'disabled');
        },
        success: function (response) {
            jQuery('.inline-error', container).addClass('d-none');
            if (response.result) {
                window.location.href = getBaseURL(module) + 'conveyancing/view/' + response.id;
            } else {
                displayValidationErrors(response.validationErrors, container);
            }
        },
        complete: function () {
             jQuery('#loader-global').hide();
            jQuery('.modal-save-btn').removeAttr('disabled');
            jQuery('.loader-submit', container).removeClass('loading');
        },
        error: defaultAjaxJSONErrorsHandler
    });
}



function commonModalDialogEvents(container){
    setDatePicker('#date_initiated', container);


}