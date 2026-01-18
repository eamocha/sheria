<<div class="modal fade" id="custodyTransferModal" tabindex="-1" role="dialog" aria-labelledby="custodyTransferModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="custodyTransferModalLabel">Record Custody Transfer</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body" id="exhibitTransferContainer">
                <?php echo form_open_multipart('', ['id' => 'custodyTransferForm']); ?>

                <?php echo form_input([
                    'type' => 'hidden',
                    'name' => 'exhibit_id',
                    'id' => 'id',
                    'value'=>$id
                ]); ?>

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <?php echo form_label('Transfer Date/Time*', 'transfer_datetime'); ?>
                            <?php echo form_input([
                                'type' => 'text',
                                'class' => 'form-control',
                                'id' => 'transfer_datetime',
                                'name' => 'action_date_time',
                                'required' => 'required'
                            ]); ?>
                        </div>

                        <div class="form-group" id="t_from">
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
                                <a href="javascript:;" onclick="quickAdministrationDialog('exhibit_locations', jQuery('#t_from', '#custodyTransferModal'), true);" class="btn btn-link ml-1">
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
 
    // Store Flatpickr instances in a dedicated object for easy management
    const flatpickrInstances = {}; 

    jQuery(document).ready(function () {
        // Flatpickr initialization for custodyTransferModal (keep as is)
        flatpickrInstances.transferDateTime = flatpickr("#transfer_datetime", {
            enableTime: true,
            dateFormat: "Y-m-d H:i",
            time_24hr: true,
            appendTo: document.body
        });

        // --- Bloodhound and Typeahead for Location Fields ---
        const locationSuggestions = new Bloodhound({
            datumTokenizer: Bloodhound.tokenizers.whitespace,
            queryTokenizer: Bloodhound.tokenizers.whitespace,
            remote: {
                url: '<?php echo site_url("exhibit_locations/autocomplete"); ?>?term=%QUERY',
                wildcard: '%QUERY',
                transform: function(response) { return response; }
            }
        });

        jQuery('.location-typeahead').each(function() {
            const $input = jQuery(this);
            const inputId = $input.attr('id');
            const hiddenIdFieldId = inputId + '_id';

            $input.typeahead({
                hint: true,
                highlight: true,
                minLength: 1
            }, {
                name: 'location-names',
                source: locationSuggestions,
                display: 'location',
                templates: {
                    empty: function(context) {
                        const currentQuery = encodeURIComponent(context.query);
                        // The quickAddDialog call for Typeahead's empty template
                        return [
                            '<div class="empty-message text-center p-2">',
                            'No matching locations found.',
                            `<div><a href="javascript:void(0);" onclick="quickAddDialog('exhibit_locations', '${currentQuery}', function() { jQuery('#${inputId}').typeahead('val', decodeURIComponent('${currentQuery}')).focus(); });" class="btn btn-sm btn-outline-primary mt-2">`,
                            `<i class="fa-solid fa-plus-circle mr-1"></i> Add "${context.query}"`,
                            '</a></div>',
                            '</div>'
                        ].join('\n');
                    },
                    suggestion: function(data) { return '<div>' + data.location + '</div>'; }
                }
            });

            $input.on('typeahead:select', function(ev, suggestion) {
                jQuery('#' + hiddenIdFieldId).val(suggestion.id);
            });

            $input.on('change', function() {
                if (!jQuery(this).val()) {
                    jQuery('#' + hiddenIdFieldId).val('');
                }
            });
        });

        // --- Modal Reset for custodyTransferModal (keep as is) ---
        jQuery('#custodyTransferModal').on('show.bs.modal', function () {
            jQuery('#custodyTransferForm')[0].reset();
            jQuery('.custom-file-label').html('Choose files');
            flatpickrInstances.transferDateTime.clear(); // Use the stored instance
            jQuery('.location-typeahead').each(function() {
                const $input = jQuery(this);
                const hiddenIdFieldId = $input.attr('id') + '_id';
                $input.typeahead('val', '');
                $input.typeahead('close');
                jQuery('#' + hiddenIdFieldId).val('');
            });
        });

        // Handle custom file input label update
        jQuery('.custom-file-input').on('change', function() {
            let fileName = jQuery(this).val().split('\\').pop();
            jQuery(this).next('.custom-file-label').html(fileName || 'Choose files');
        });
    });

    /**
     * General function to open a modal for quick adding new items.
     *
     * @param {string} dataType - The type of data to add (e.g., 'exhibit_locations', 'users').
     * This string is used to construct backend URLs.
     * @param {string} [prefillValue=''] - Optional value to pre-fill a field (e.g., 'name') in the loaded form.
     * @param {function} [successCallback=null] - Optional callback function to run on successful form submission.
     * Useful for refreshing Typeahead, dropdowns, etc.
     */
    function quickAddDialog(dataType, prefillValue = '', successCallback = null) {
        const $modal = jQuery('#quickAddModal');
        const $modalBody = $modal.find('.modal-body');
        const $modalTitle = $modal.find('.modal-title');

        // Set modal title (can be more dynamic based on dataType)
        $modalTitle.text('Add New ' + dataType.replace(/_/g, ' ').replace(/\b\w/g, l => l.toUpperCase())); // Basic title formatting

        // Show loading spinner while fetching form
        $modalBody.html(`
            <div class="text-center p-4">
                <i class="fa fa-spinner fa-spin fa-3x fa-fw"></i>
                <span class="sr-only">Loading...</span>
                <p class="mt-2">Loading form...</p>
            </div>
        `);

        $modal.modal('show'); // Show the modal immediately

        // Fetch the form HTML from the backend
        jQuery.ajax({
            url: `<?php echo site_url("exhibit_locations/add"); ?>`, // Backend endpoint to get the form
            method: 'GET',
            data: { prefill: prefillValue,quick_add_form: dataType }, // Pass prefill value to backend if needed for initial form rendering
            success: function(response) {
                $modalBody.html(response); // Load the form into the modal body

                // If prefillValue is provided, attempt to set it on an input named 'name' or with id 'name'
                if (prefillValue) {
                    const decodedPrefill = decodeURIComponent(prefillValue);
                    $modalBody.find('input[name="name"], #name').val(decodedPrefill);
                }

                // Re-initialize any JS components within the newly loaded form (e.g., another Flatpickr if present)
                // This is a placeholder; you'd add specific initializations here if your quick add forms have them.
                // For example, if your quick add form for locations has a 'created_date' Flatpickr:
                // flatpickr($modalBody.find("#created_date")[0], { dateFormat: "Y-m-d" });

                // Attach submit handler to the dynamically loaded form
                $modalBody.find('form').on('submit', function(e) {
                    e.preventDefault(); // Prevent default form submission

                    const $form = jQuery(this);
                    const formData = new FormData($form[0]); // Use FormData for file uploads

                    // Show a loading indicator on the submit button
                    const $submitBtn = $form.find('button[type="submit"]');
                    const originalBtnText = $submitBtn.html();
                    $submitBtn.html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Saving...').prop('disabled', true);

                    jQuery.ajax({
                        url: `<?php echo site_url("exhibit_locations/add"); ?>/${dataType}`, // Backend endpoint to save the item
                        method: 'POST',
                        data: formData,
                        processData: false, // Important for FormData
                        contentType: false, // Important for FormData
                        dataType: 'json', // Expect JSON response from backend
                        success: function(response) {
                            if (response.status === 'success') {
                                // Close the modal
                                $modal.modal('hide');
                                // Show success message (e.g., using a toast notification library)
                                alert('Item added successfully!'); // Replace with a better notification

                                // Execute success callback if provided
                                if (successCallback && typeof successCallback === 'function') {
                                    successCallback(response.data); // Pass any returned data to callback
                                }
                            } else {
                                // Display validation errors or general error message
                                alert('Error: ' + (response.message || 'Failed to add item.')); // Replace with better error display
                                // You might want to display specific field errors from response.errors
                            }
                        },
                        error: function(xhr, status, error) {
                            console.error("AJAX Error:", status, error, xhr.responseText);
                            alert('An unexpected error occurred. Please try again.'); // Replace with better error display
                        },
                        complete: function() {
                            // Re-enable button
                            $submitBtn.html(originalBtnText).prop('disabled', false);
                        }
                    });
                });
            },
            error: function(xhr, status, error) {
                console.error("Error loading quick add form:", status, error, xhr.responseText);
                $modalBody.html('<div class="alert alert-danger">Failed to load form. Please try again.</div>');
            }
        });
    }
</script>