var gridSize5=5;
function nominate_counsel(conveyancingInstrumentId) {
    jQuery.ajax({
        url: getBaseURL() + 'conveyancing/nominate_counsel/'+conveyancingInstrumentId,
        type: 'GET',
        dataType: 'JSON', // This is correct, as your backend returns JSON with the HTML
        beforeSend: function () {
            jQuery('#loader-global').show();
        },
        success: function (response) {
            // DEBUGGING STEP 1: Check if response.html exists and is a string
            console.log("Response received. Is HTML property present:", !!response.html);
            if (response.html) {
                // Ensure no old modal or container is lingering
                jQuery('#nominate-external-counsel-container').remove();
                jQuery('#nominateExternalCounselModal').remove();

                let $modalContainer = jQuery('<div id="nominate-external-counsel-container"></div>').appendTo("body");
                $modalContainer.html(response.html); // Inject the HTML string

                var nominateCounselModal = jQuery('#nominateExternalCounselModal');

                // DEBUGGING STEP 2: Check if the modal element is found in the DOM
                console.log("Modal element found in DOM:", nominateCounselModal.length > 0);

                if (nominateCounselModal.length > 0) { // Only proceed if modal element exists
                    nominateCounselModal.find('#conveyancing_instrument_id_for_nomination').val(conveyancingInstrumentId);

                    // Call the initialization function AFTER the HTML is in the DOM
                    // This is where your previous ReferenceError occurred.
                    // Assuming initNominateExternalCounselModalJS is now in conveyancing_view.js
                    initNominateExternalCounselModalJS();

                    // DEBUGGING STEP 3: Check visibility before .modal('show')
                    console.log("Modal visibility before show:", nominateCounselModal.is(':visible'));
                    console.log("Body has modal-open before show:", jQuery('body').hasClass('modal-open'));

                    // Show the correct modal
                    nominateCounselModal.modal('show');

                    // DEBUGGING STEP 4: Check visibility after .modal('show')
                    // Use a small timeout to allow Bootstrap's JS to apply changes
                    setTimeout(function() {
                        console.log("Modal visibility AFTER show:", nominateCounselModal.is(':visible'));
                        console.log("Body has modal-open AFTER show:", jQuery('body').hasClass('modal-open'));
                    }, 500); // Wait 0.5 seconds

                    // Clean up the modal when it's hidden
                    nominateCounselModal.on('hidden.bs.modal', function () {
                        jQuery(this).remove();
                        $modalContainer.remove();
                    });
                } else {
                    console.error("Error: Modal element #nominateExternalCounselModal not found after injection.");
                    pinesMessageV2({ ty: 'error', m: 'Error: Modal not found in HTML response.' });
                }

            } else if (typeof response.error !== 'undefined' && response.error) {
                pinesMessageV2({ ty: 'error', m: response.error });
            }
        },
        complete: function () {
            jQuery('#loader-global').hide();
        },
        error: defaultAjaxJSONErrorsHandler
    });
}

// --- MOVE THESE FUNCTIONS INTO conveyancing_view.js ---

// Function to initialize Typeahead and other JS for the modal
function initNominateExternalCounselModalJS() {
    // Bloodhound for Companies
    var companiesBloodhound = new Bloodhound({
        datumTokenizer: Bloodhound.tokenizers.obj.whitespace('name'),
        queryTokenizer: Bloodhound.tokenizers.whitespace,
        remote: {
            url: getBaseURL() + 'companies/autocomplete?term=%QUERY',
            wildcard: '%QUERY',
            transform: function(response) {
                return jQuery.map(response, function(company) {
                    return company;
                });
            }
        }
    });

    var externalCounselInput = jQuery('#external_counsel_name');
    var externalCounselHiddenId = jQuery('#external_counsel_id');

    // Destroy existing Typeahead instances to prevent re-initialization issues
    if (externalCounselInput.data('tt-typeahead')) {
        externalCounselInput.typeahead('destroy');
    }

    externalCounselInput.typeahead({
        highlight: true,
        minLength: 1
    }, {
        name: 'companies-suggestions',
        display: 'name',
        source: companiesBloodhound,
        templates: {
            empty: [
                '<div class="empty-message">',
                'No matching companies found. <a href="javascript:;" onclick="companyAddForm();">Add New Company</a>',
                '</div>'
            ].join('\n'),
            suggestion: function (data) {
                return '<div><strong>' + data.name + '</strong> <small>(' + (data.shortName || '') + ')</small></div>';
            }
        }
    }).on('typeahead:select typeahead:autocomplete', function (event, suggestion) {
        externalCounselHiddenId.val(suggestion.id);
        externalCounselInput.typeahead('val', suggestion.name);
    }).on('change', function () {
        if (!jQuery(this).typeahead('val')) {
            externalCounselHiddenId.val('');
        }
    });

    jQuery('#submitNominationBtn').off('click').on('click', function() {
        submitExternalCounselNomination();
    });
}

// Placeholder for the submission function
function submitExternalCounselNomination() {
    var form = jQuery('#nominateExternalCounselForm')[0];
    var conveyancingInstrumentId = jQuery('#conveyancing_instrument_id_for_nomination').val();
    var externalCounselId = jQuery('#external_counsel_id').val();
    var externalCounselName = jQuery('#external_counsel_name').val();
    var nominationNotes = jQuery('#nomination_notes').val();

    if (!externalCounselId || !externalCounselName) {
        jQuery('[data-field="external_counsel_name"]').text('Please select an external counsel from the suggestions.').removeClass('d-none');
        return;
    } else {
        jQuery('[data-field="external_counsel_name"]').addClass('d-none').text('');
    }

    var formData = new FormData(form);

    jQuery.ajax({
        url: getBaseURL() + 'conveyancing/nominate_counsel/' + conveyancingInstrumentId,
        type: 'POST',
        dataType: 'json',
        processData: false,
        contentType: false,
        data: formData,
        beforeSend: function() {
            jQuery('#submitNominationBtn').attr('disabled', 'disabled').text('Nominating...');
        },
        success: function(response) {
            if (response.result) {
                pinesMessage({ty: 'success', m: 'External counsel nominated successfully!'});
                jQuery('#nominateExternalCounselModal').modal('hide');
             //    jQuery('#external-counsel-name-container').html("Name");
            } else {
                pinesMessage({ty: 'error', m: response.message || 'Failed to nominate external counsel.'});
                if (response.validationErrors) {
                    if (response.validationErrors.external_counsel_name) {
                        jQuery('[data-field="external_counsel_name"]').text(response.validationErrors.external_counsel_name).removeClass('d-none');
                    }
                }
            }
        },
        error: function(xhr, status, error) {
            pinesMessage({ty: 'error', m: 'An AJAX error occurred: ' + error});
            console.error("AJAX Error:", xhr.responseText);
        },
        complete: function() {
            jQuery('#submitNominationBtn').removeAttr('disabled').text('Nominate');
        }
    });
}


 
 
function loadConveyancingDocuments(conveyancingInstrumentId) {
    jQuery.ajax({
        url: getBaseURL() + 'conveyancing/load_documents',
        type: 'POST',
        data: { module: "conveyancing", module_record_id: conveyancingInstrumentId },
        dataType: 'json',
        beforeSend: function () {
            jQuery('#loader-global').show();
        },
        success: function (response) {
            if (response.html) {
                jQuery('#conveyancing-documents-container').html(response.html);
            } else {
                 jQuery('#conveyancing-documents-container').html("No Related Documents found.");
            }
        },
        error: function(xhr, status, error) {
            pinesMessageV2({ ty: 'error', m: 'Failed to load documents: ' + error });
        },
        complete: function () {
            jQuery('#loader-global').hide();
        }
    });
}




