<div class="modal fade" id="editExhibitModal" tabindex="-1" aria-labelledby="editExhibitModalLabel" >

    <div class="modal-dialog modal-lg">
        <div class="modal-content rounded">
            <div class="modal-header">
                <h5 class="modal-title">Exhibit Record</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <?php
                // Assume $exhibit_data is an associative array containing exhibit details,
                // passed from your PHP controller (e.g., fetch_exhibit_record($id)).
                // If $exhibit_data is not set, initialize it as an empty array to prevent errors.
                if (!isset($exhibit_data)) {
                    $exhibit_data = [];
                }

                // Load CodeIgniter form helper (if not autoloaded)
                // $this->load->helper('form');

                // Define options for dropdowns
                $exhibit_status_options = [
                    '' => 'Select Status',
                    'ACTIVE' => 'ACTIVE',
                    'IN_LAB' => 'IN LAB',
                    'IN_COURT' => 'IN COURT',
                    'DISPOSED' => 'DISPOSED',
                    'TEMPORARY_REMOVAL' => 'TEMPORARY REMOVAL',
                ];

                $associated_party_type_options = [
                    '' => 'Select Type',
                    'Client' => 'Client',
                    'Opponent' => 'Opponent',
                    'Witness' => 'Witness',
                    'Suspect' => 'Suspect',
                ];

                echo form_open('', ['id' => 'exhibitForm']); // Action can be set here if needed
                ?>
                <?php echo form_input([ 'type' => 'hidden', 'name' => 'id', 'id' => 'id', 'value' => $exhibit_data['id']]); ?>
                <?php echo form_input([ 'type' => 'hidden', 'name' => 'case_id', 'id' => 'case_id', 'value' => $exhibit_data['case_id']]); ?>
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="exhibit_label">Exhibit Label/Name <span class="text-danger">*</span></label>
                            <?php echo form_input([
                                'type' => 'text',
                                'name' => 'exhibit_label',
                                'id' => 'exhibit_label',
                                'class' => 'form-control rounded',
                                'required' => 'required',
                                'maxlength' => '255',
                                'placeholder' => 'e.g., Laptop from Suspect A',
                                'value' => set_value('exhibit_label', $exhibit_data['exhibit_label'] ?? '')
                            ]); ?>
                            <small class="form-text text-muted">A concise label or name for the exhibit.</small>
                            <div data-field="exhibit_label" class="inline-error d-none text-danger"></div>
                        </div>
                        <div class="form-group">
                            <label for="description">Description <span class="text-danger">*</span></label>
                            <?php echo form_textarea([
                                'name' => 'description',
                                'id' => 'description',
                                'class' => 'form-control rounded',
                                'rows' => '3',
                                'required' => 'required',
                                'placeholder' => 'Detailed description of the exhibit...',
                                'value' => set_value('description', $exhibit_data['description'] ?? '')
                            ]); ?>
                            <small class="form-text text-muted">A comprehensive description of the exhibit.</small>
                            <div data-field="description" class="inline-error d-none text-danger"></div>
                        </div>
                        <div class="form-group">
                            <label for="date_received">Date Received <span class="text-danger">*</span></label>
                            <?php echo form_input([
                                'type' => 'text',
                                'name' => 'date_received',
                                'id' => 'date_received',
                                'class' => 'form-control rounded',
                                'required' => 'required',
                                'value' => set_value('date_received', $exhibit_data['date_received'] ?? '')
                            ]); ?>
                            <small class="form-text text-muted">The date the exhibit was officially received (YYYY-MM-DD).</small>
                            <div data-field="date_received" class="inline-error d-none text-danger"></div>
                        </div>
                        <div class="form-group">
                            <label for="exhibit_status">Exhibit Status</label>
                            <?php echo form_dropdown(
                                'exhibit_status',
                                $exhibit_status_options,
                                set_value('exhibit_status', $exhibit_data['exhibit_status'] ?? ''),
                                ['id' => 'exhibit_status', 'class' => 'form-control rounded']
                            ); ?>
                            <small class="form-text text-muted">The current status of the exhibit.</small>
                            <div data-field="exhibit_status" class="inline-error d-none text-danger"></div>
                        </div>
                        <div class="form-group">
                            <label for="current_location">Current Location</label>
                            <div class="col-md-12 p-0 row">
                                <div class="col-md-11">
                                    <?php echo form_input([ 'type' => 'hidden', 'name' => 'current_location_id', 'id' => 'current_location_id', 'value' => $exhibit_data['current_location_id'] ?? '']); ?>
                                    <?php echo form_input([
                                        'type' => 'text',
                                        'name' => 'current_location',
                                        'id' => 'current_location',
                                        'class' => 'form-control rounded typeahead',
                                        'maxlength' => '250',
                                        'placeholder' => 'e.g., Evidence Locker B-12',
                                        'value' => set_value('current_location', $exhibit_data['current_location_name'] ?? '')
                                    ]); ?>
                                </div>
                                <div class="col-md-1 p-0 col-xs-1">
                                    <a href="javascript:;" onclick="quickAdministrationDialog('exhibit_locations', jQuery('#exhibitForm', '#editExhibitModal'), true);" class="btn btn-link"><i class="fa-solid fa-square-plus p-1 font-18"></i></a>
                                </div>
                            </div>
                            <small class="form-text text-muted">The physical location where the exhibit is currently stored.</small>
                            <div data-field="current_location" class="inline-error d-none text-danger"></div>
                        </div>
                        <div class="form-group">
                            <label for="officers_involved">Officer Involved</label>
                            <?php echo form_input([ 'type' => 'hidden', 'name' => 'officers_involved_id', 'id' => 'officers_involved_id', 'value' => $exhibit_data['officers_involved_id'] ?? '']); ?>
                            <?php echo form_input([
                                'type' => 'text',
                                'name' => 'officers_involved',
                                'id' => 'officers_involved',
                                'class' => 'form-control rounded typeahead', // Added typeahead class
                                'maxlength' => '250',
                                'placeholder' => 'e.g., Det. John Doe, Sgt. Jane Smith',
                                'value' => set_value('officers_involved', $exhibit_data['officer_name'] ?? '')
                            ]); ?>
                            <small class="form-text text-muted">Names or IDs of officers involved with the exhibit.</small>
                            <div data-field="officers_involved" class="inline-error d-none text-danger"></div>
                        </div>
                        <div class="form-group">
                            <label for="officer_remarks">Officer Remarks</label>
                            <?php echo form_textarea([
                                'name' => 'officer_remarks',
                                'id' => 'officer_remarks',
                                'class' => 'form-control rounded',
                                'rows' => '3',
                                'placeholder' => 'Any additional remarks from officers...',
                                'value' => set_value('officer_remarks', $exhibit_data['officer_remarks'] ?? '')
                            ]); ?>
                            <small class="form-text text-muted">Additional notes or observations by officers.</small>
                            <div data-field="officer_remarks" class="inline-error d-none text-danger"></div>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="associated_party_type">Associated Party Type</label>
                            <?php echo form_dropdown(
                                'associated_party_type',
                                $associated_party_type_options,
                                set_value('associated_party_type', $exhibit_data['associated_party_type'] ?? ''),
                                ['id' => 'associated_party_type', 'class' => 'form-control rounded']
                            ); ?>
                            <small class="form-text text-muted">The type of party associated with the exhibit.</small>
                            <div data-field="associated_party_type" class="inline-error d-none text-danger"></div>
                        </div>
                        <div class="form-group">
                            <label for="associated_party">Associated Party ID</label>
                            <?php echo form_input([
                                'type' => 'number',
                                'name' => 'associated_party',
                                'id' => 'associated_party',
                                'class' => 'form-control rounded',
                                'placeholder' => 'e.g., 54321',
                                'value' => set_value('associated_party', $exhibit_data['associated_party'] ?? '')
                            ]); ?>
                            <small class="form-text text-muted">The ID of the associated party (e.g., client, suspect).</small>
                            <div data-field="associated_party" class="inline-error d-none text-danger"></div>
                        </div>

                        <div class="form-group pickup_location">
                            <label for="pickup_location">Pickup Location</label>
                            <div class="col-md-12 p-0 row">
                                <div class="col-md-11">
                                    <?php echo form_input([ 'type' => 'hidden', 'name' => 'pickup_location_id', 'id' => 'pickup_location_id', 'value' => $exhibit_data['pickup_location_id'] ?? '']); ?>
                                    <?php echo form_input([
                                        'type' => 'text', 'name' => 'pickup_location', 'id' => 'pickup_location', 'class' => 'form-control rounded typeahead', 'maxlength' => '250', 'placeholder' => 'e.g., Crime Scene - Apt 3B', 'value' => set_value('pickup_location', $exhibit_data['pickup_location_name'] ?? '')
                                    ]); ?>
                                </div>
                                <div class="col-md-1 p-0 col-xs-1">
                                    <a href="javascript:;" onclick="quickAdministrationDialog('exhibit_locations', jQuery('#pickup_location', '#exhibit-dialog'), true);" class="btn btn-link"><i class="fa-solid fa-square-plus p-1 font-18"></i></a>
                                </div>
                            </div>
                            <small class="form-text text-muted">The location where the exhibit was initially picked up.</small>
                            <div data-field="pickup_location" class="inline-error d-none text-danger"></div>
                        </div>
                        <div class="form-group">
                            <label for="status_on_pickup">Status on Pickup</label>
                            <?php echo form_input([
                                'type' => 'text',
                                'name' => 'status_on_pickup',
                                'id' => 'status_on_pickup',
                                'class' => 'form-control rounded',
                                'maxlength' => '250',
                                'placeholder' => 'e.g., Intact, Damaged',
                                'value' => set_value('status_on_pickup', $exhibit_data['status_on_pickup'] ?? '')
                            ]); ?>
                            <small class="form-text text-muted">The condition or status of the exhibit when it was picked up.</small>
                            <div data-field="status_on_pickup" class="inline-error d-none text-danger"></div>
                        </div>
                        <div class="form-group">
                            <label for="temporary_removals">Temporary Removals</label>
                            <?php echo form_textarea([
                                'name' => 'temporary_removals',
                                'id' => 'temporary_removals',
                                'class' => 'form-control rounded',
                                'rows' => '3',
                                'placeholder' => 'Details of any temporary removals...',
                                'value' => set_value('temporary_removals', $exhibit_data['temporary_removals'] ?? '')
                            ]); ?>
                            <small class="form-text text-muted">Record of temporary removals from storage.</small>
                            <div data-field="temporary_removals" class="inline-error d-none text-danger"></div>
                        </div>
                        <div class="form-group">
                            <label for="reason_for_temporary">Reason for Temporary Removal</label>
                            <?php echo form_input([
                                'type' => 'text',
                                'name' => 'reason_for_temporary',
                                'id' => 'reason_for_temporary',
                                'class' => 'form-control rounded',
                                'maxlength' => '250',
                                'placeholder' => 'e.g., Forensic Analysis, Court Presentation',
                                'value' => set_value('reason_for_temporary', $exhibit_data['reason_for_temporary'] ?? '')
                            ]); ?>
                            <small class="form-text text-muted">The reason for any temporary removal of the exhibit.</small>
                            <div data-field="reason_for_temporary" class="inline-error d-none text-danger"></div>
                        </div>
                        <div class="form-group">
                            <label for="date_approved_for_disposal">Date Approved for Disposal</label>
                            <?php echo form_input([
                                'type' => 'text',
                                'name' => 'date_approved_for_disposal',
                                'id' => 'date_approved_for_disposal',
                                'class' => 'form-control rounded',
                                'value' => set_value('date_approved_for_disposal', $exhibit_data['date_approved_for_disposal'] ?? '')
                            ]); ?>
                            <small class="form-text text-muted">The date disposal was approved, if applicable.</small>
                            <div data-field="date_approved_for_disposal" class="inline-error d-none text-danger"></div>
                        </div>
                        <div class="form-group">
                            <label for="date_disposed">Date Disposed</label>
                            <?php echo form_input([
                                'type' => 'text',
                                'name' => 'date_disposed',
                                'id' => 'date_disposed',
                                'class' => 'form-control rounded',
                                'value' => set_value('date_disposed', $exhibit_data['date_disposed'] ?? '')
                            ]); ?>
                            <small class="form-text text-muted">The actual date the exhibit was disposed of.</small>
                            <div data-field="date_disposed" class="inline-error d-none text-danger"></div>
                        </div>
                        <div class="form-group">
                            <label for="manner_of_disposal">Manner of Disposal</label>
                            <?php echo form_textarea([
                                'name' => 'manner_of_disposal',
                                'id' => 'manner_of_disposal',
                                'class' => 'form-control rounded',
                                'rows' => '3',
                                'placeholder' => 'How the exhibit was disposed...',
                                'value' => set_value('manner_of_disposal', $exhibit_data['manner_of_disposal'] ?? '')
                            ]); ?>
                            <small class="form-text text-muted">Details on how the exhibit was disposed.</small>
                            <div data-field="manner_of_disposal" class="inline-error d-none text-danger"></div>
                        </div>
                        <div class="form-group">
                            <label for="disposal_remarks">Disposal Remarks</label>
                            <?php echo form_textarea([
                                'name' => 'disposal_remarks',
                                'id' => 'disposal_remarks',
                                'class' => 'form-control rounded',
                                'rows' => '3',
                                'placeholder' => 'Any additional remarks about disposal...',
                                'value' => set_value('disposal_remarks', $exhibit_data['disposal_remarks'] ?? '')
                            ]); ?>
                            <small class="form-text text-muted">Additional notes or remarks related to the disposal.</small>
                            <div data-field="disposal_remarks" class="inline-error d-none text-danger"></div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary rounded" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary rounded">Submit Exhibit</button>
                </div>
                <?php echo form_close(); ?>
            </div>

        </div>
    </div>
</div>
<script type="text/javascript">
    jQuery(document).ready(function () {

        flatpickr("#date_received", {
            dateFormat: "Y-m-d H:i",
            time_24hr: true,
            enableTime: true,
        });
        flatpickr("#date_disposed", {
            dateFormat: "Y-m-d H:i",
            time_24hr: true,
            enableTime: true,
        });
        flatpickr("#date_approved_for_disposal", {
            dateFormat: "Y-m-d H:i",
            time_24hr: true,
            enableTime: true,
        });

        // --- Typeahead.js and Bloodhound for Locations ---
        var locations = new Bloodhound({
            datumTokenizer: Bloodhound.tokenizers.obj.whitespace('name'),
            queryTokenizer: Bloodhound.tokenizers.whitespace,
            remote: {
                url: getBaseURL()+"exhibit_locations/autocomplete?term=%QUERY",
                wildcard: '%QUERY'
            }
        });

       jQuery('#current_location').typeahead(null, {
            name: 'locations',
            display: 'location',
            source: locations,
            templates: {
                empty: [
                    '<div class="empty-message">',
                    'No matching locations found.',
                    '</div>'
                ].join('\n'),
                suggestion: function (data) {
                    return '<div><strong>' + data.location + '</strong></div>';
                }
            }
        }).on('typeahead:select', function (event, suggestion) {
            // When a suggestion is selected, set the hidden ID field
           jQuery('#current_location_id').val(suggestion.id);
        }).on('typeahead:autocomplete', function (event, suggestion) {
            // This event fires when a suggestion is autocompleted, but not explicitly selected
            // We'll also update the hidden ID here for convenience
           jQuery('#current_location_id').val(suggestion.id);
        }).on('change', function () {
            // Clear the hidden ID if the user types something that's not a suggestion
            if (!$(this).typeahead('val')) {
               jQuery('#current_location_id').val('');
            }
        });

       jQuery('#pickup_location').typeahead(null, {
            name: 'locations',
            display: 'location',
            source: locations,
            templates: {
                empty: [
                    '<div class="empty-message">',
                    'No matching locations found.',
                    '</div>'
                ].join('\n'),
                suggestion: function (data) {
                    return '<div><strong>' + data.location + '</strong></div>';
                }
            }
        }).on('typeahead:select', function (event, suggestion) {
           jQuery('#pickup_location_id').val(suggestion.id);
        }).on('typeahead:autocomplete', function (event, suggestion) {
           jQuery('#pickup_location_id').val(suggestion.id);
        }).on('change', function () {
            if (!$(this).typeahead('val')) {
               jQuery('#pickup_location_id').val('');
            }
        });

       // --- Typeahead.js and Bloodhound for Officers 
        var officers = new Bloodhound({
            datumTokenizer: Bloodhound.tokenizers.obj.whitespace('fullName'), // Tokenize on the combined name
            queryTokenizer: Bloodhound.tokenizers.whitespace,
            remote: {
                url: getBaseURL() + 'users/autocomplete/active?term=%QUERY', 
                wildcard: '%QUERY',
                transform: function(response) {
                    // Bloodhound expects an array of suggestion objects.
                    // Your API already returns this, but we need to create a 'fullName' field.
                    return $.map(response, function(user) {
                        user.fullName = user.firstName + ' ' + user.lastName; // Create a combined name field
                        return user;
                    });
                }
            }
        });

       jQuery('#officers_involved').typeahead(null, {
            name: 'officers',
            display: 'fullName', // Display the combined name
            source: officers,
            templates: {
                empty: [
                    '<div class="empty-message">',
                    'No matching officers found.',
                    '</div>'
                ].join('\n'),
                suggestion: function (data) {
                    // Display full name and potentially email or job title
                    return '<div><strong>' + data.fullName + '</strong> <small>(' + data.email + ')</small></div>';
                }
            }
        }).on('typeahead:select', function (event, suggestion) {
            // Set the hidden ID field with the 'id' from the API response
           jQuery('#officers_involved_id').val(suggestion.id);
            // Optionally, you might want to set the display value of the input to the full name
           jQuery(this).typeahead('val', suggestion.fullName);
        }).on('typeahead:autocomplete', function (event, suggestion) {
           jQuery('#officers_involved_id').val(suggestion.id);
           jQuery(this).typeahead('val', suggestion.fullName);
        }).on('change', function () {
            // Clear the hidden ID if the user types something that's not a suggestion
            if (!$(this).typeahead('val')) {
               jQuery('#officers_involved_id').val('');
            }
        });
    }); 
</script>