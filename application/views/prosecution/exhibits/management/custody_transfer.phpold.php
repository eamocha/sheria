<<div class="modal fade" id="custodyTransferModal" tabindex="-1" role="dialog" aria-labelledby="custodyTransferModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="custodyTransferModalLabel">Record Custody Transfer</h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body" id="exhibitTransferContainer">
                <?php echo form_open_multipart('', ['id' => 'custodyTransferForm']); ?>

                <?php echo form_input([
                    'type' => 'hidden',
                    'name' => 'id',
                    'id' => 'id'
                ]); ?>

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <?php echo form_label('Transfer Date/Time*', 'transfer_datetime'); ?>
                            <?php echo form_input([
                                'type' => 'text',
                                'class' => 'form-control',
                                'id' => 'transfer_datetime',
                                'name' => 'transfer_datetime',
                                'required' => 'required'
                            ]); ?>
                        </div>

                        <div class="form-group">
                            <?php echo form_label('Transfer From*', 'transfer_from'); ?>
                            <div class="d-flex align-items-center">
                                <?php echo form_input([
                                    'type' => 'text',
                                    'class' => 'form-control location-typeahead',
                                    'id' => 'transfer_from',
                                    'name' => 'transfer_from',
                                    'required' => 'required'
                                ]); ?>
                                <?php echo form_input([
                                    'type' => 'hidden',
                                    'id' => 'transfer_from_id', // ID for JavaScript to target
                                    'name' => 'transfer_from_id', // Name for form submission
                                    'value' => ''
                                ]); ?>
                                <a href="javascript:;" onclick="quickAdministrationDialog('exhibit_locations', jQuery('#custodyTransferForm', '#custodyTransferModal'), true);" class="btn btn-link ml-1">
                                    <i class="fa-solid fa-square-plus p-1 font-18"></i>
                                </a>
                            </div>
                        </div>

                        <div class="form-group">
                            <?php echo form_label('Transfer To*', 'transfer_to'); ?>
                            <div class="d-flex align-items-center">
                                <?php echo form_input([
                                    'type' => 'text',
                                    'class' => 'form-control location-typeahead',
                                    'id' => 'transfer_to',
                                    'name' => 'transfer_to',
                                    'required' => 'required'
                                ]); ?>
                                <?php echo form_input([
                                    'type' => 'hidden',
                                    'id' => 'transfer_to_id', // ID for JavaScript to target
                                    'name' => 'transfer_to_id', // Name for form submission
                                    'value' => ''
                                ]); ?>
                                <a href="javascript:;" onclick="quickAdministrationDialog('exhibit_locations', jQuery('#custodyTransferForm', '#custodyTransferModal'), true);" class="btn btn-link ml-1">
                                    <i class="fa-solid fa-square-plus p-1 font-18"></i>
                                </a>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-group">
                            <?php echo form_label('Purpose*', 'transfer_purpose'); ?>
                            <?php echo form_dropdown(
                                'transfer_purpose',
                                [
                                    '' => 'Select purpose',
                                    'Analysis' => 'Analysis',
                                    'Court' => 'Court Presentation',
                                    'Storage' => 'Storage Transfer',
                                    'Disposal' => 'Disposal',
                                    'Return' => 'Return to Owner',
                                    'Other' => 'Other'
                                ],
                                '',
                                [
                                    'class' => 'form-control',
                                    'id' => 'transfer_purpose',
                                    'required' => 'required'
                                ]
                            ); ?>
                        </div>

                        <div class="form-group">
                            <?php echo form_label('Notes', 'transfer_notes'); ?>
                            <?php echo form_textarea([
                                'class' => 'form-control',
                                'id' => 'transfer_notes',
                                'name' => 'transfer_notes',
                                'rows' => '3'
                            ]); ?>
                        </div>

                        <div class="form-group">
                            <?php echo form_label('Attachments', 'transfer_attachments'); ?>
                            <div class="custom-file">
                                <?php echo form_upload([
                                    'class' => 'custom-file-input',
                                    'id' => 'transfer_attachments',
                                    'name' => 'attachments[]',
                                    'multiple' => 'multiple'
                                ]); ?>
                                <label class="custom-file-label" for="transfer_attachments">Choose files</label>
                            </div>
                            <small class="form-text text-muted">Upload transfer documentation</small>
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <div class="form-check">
                        <?php echo form_checkbox([
                            'class' => 'form-check-input',
                            'id' => 'transfer_condition_check',
                            'name' => 'condition_check',
                            'required' => 'required'
                        ]); ?>
                        <?php echo form_label('I have verified the exhibit condition matches the records*', 'transfer_condition_check', ['class' => 'form-check-label']); ?>
                    </div>
                </div>

                <div class="modal-footer justify-content-between">
                     <?php $this->load->view("templates/send_email_option_template", ["container" => "#exhibitTransferContainer", "hide_show_notification" => ""]);//$hide_show_notification]);?>
                   <div class="pull-right>">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <?php echo form_submit('submit', 'Record Transfer', ['class' => 'btn btn-dark']); ?>
                    </div>
                </div>

                <?php echo form_close(); ?>
                    
            </div>
        </div>
    </div>
</div>
<script type="text/javascript">
    jQuery(document).ready(function () {
        // Flatpickr initialization 
        flatpickr("#transfer_datetime", {
            enableTime: true,
            dateFormat: "Y-m-d H:i",
            time_24hr: true,
            appendTo: document.body
        });

        // --- Bloodhound and Typeahead for Location Fields ---

        // 1. Initialize Bloodhound engine
        const locationSuggestions = new Bloodhound({
            datumTokenizer: Bloodhound.tokenizers.whitespace,
            queryTokenizer: Bloodhound.tokenizers.whitespace,
            remote: {
                url: '<?php echo site_url("exhibit_locations/autocomplete"); ?>?term=%QUERY',
                wildcard: '%QUERY',
                // IMPORTANT: Transform the response to ensure Bloodhound gets an array of objects
                // as expected. Your provided format is already an array of objects.
                transform: function(response) {
                    //  PHP returns an array of objects like:
                    // [{"id": "1", "location": "Evidence Room A"}, ...]
                    // Bloodhound expects an array of datums.
                    return response;
                }
            }
        });

        // 2. Initialize Typeahead on relevant input fields
        jQuery('.location-typeahead').each(function() {
            const $input = jQuery(this);
            const inputId = $input.attr('id'); // e.g., 'transfer_from' or 'transfer_to'
            const hiddenIdFieldId = inputId + '_id'; // e.g., 'transfer_from_id' or 'transfer_to_id'

            $input.typeahead({
                hint: true,
                highlight: true,
                minLength: 1
            }, {
                name: 'location-names',
                source: locationSuggestions,
                display: 'location', // Tell Typeahead to display the 'location' property from the suggestion object
                templates: {
                    empty: [
                        '<div class="empty-message">',
                        'No matching locations found.',
                        '</div>'
                    ].join('\n'),
                    suggestion: function(data) {
                        // Display the 'name' property for each suggestion
                        return '<div>' + data.location + '</div>';
                    }
                }
            });

            // 3. Listen for the 'typeahead:select' event to populate the hidden ID field
            $input.on('typeahead:select', function(ev, suggestion) {
                // 'suggestion' is the selected object (e.g., {id: "1", location: "Evidence Room A"})
                jQuery('#' + hiddenIdFieldId).val(suggestion.id);
            });

            // 4. Listen for the 'change' event on the typeahead input to clear the hidden ID if text is manually changed
            $input.on('change', function() {
                // If the text in the visible input doesn't match a selected suggestion, clear the hidden ID
                // This handles cases where user types something that isn't a valid suggestion or clears the field
                const selectedLocationName = jQuery(this).val();
                if (!selectedLocationName) {
                    // If the field is empty, clear the hidden ID
                    jQuery('#' + hiddenIdFieldId).val('');
                } else {
                    // if selectedLocationName is not in your current suggestions, clear the ID.
                    // For now, if user types and doesn't select, we assume it's a new or invalid entry.
                    // The 'typeahead:select' will overwrite this if a valid suggestion is chosen.
                    // A better approach would be to only set the hidden ID on 'select' and clear on 'change' if not a select.
                }
            });
        });


        // --- Handle Modal Reset for Typeahead fields ---
        jQuery('#custodyTransferModal').on('show.bs.modal', function () {
            // Reset regular form fields
            jQuery('#custodyTransferForm')[0].reset();
            jQuery('.custom-file-label').html('Choose files'); // Reset file label

            // Clear Flatpickr date/time
            flatpickr("#transfer_datetime").clear();

            // Clear Typeahead fields (both the visible input and the hidden hint input)
            jQuery('.location-typeahead').each(function() {
                const $input = jQuery(this);
                const hiddenIdFieldId = $input.attr('id') + '_id';

                $input.typeahead('val', ''); // Clear the visible input
                $input.typeahead('close');   // Close any open suggestion menus
                jQuery('#' + hiddenIdFieldId).val(''); // Clear the hidden ID field
            });
        });

        // Handle custom file input label update
        jQuery('.custom-file-input').on('change', function() {
            let fileName = jQuery(this).val().split('\\').pop();
            jQuery(this).next('.custom-file-label').html(fileName || 'Choose files');
        });
    });
</script>