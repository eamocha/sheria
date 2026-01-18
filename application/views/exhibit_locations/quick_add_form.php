<div class="modal fade" id="locationModal" tabindex="-1" role="dialog" aria-labelledby="locationModalLabel">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="locationModalLabel"><?php echo $this->lang->line("location") ?></h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <?php echo form_open(current_url(), ["id" => "locationForm"]) ?>

            <div class="modal-body">
                <?php echo form_input([
                    "name" => "id",
                    "id" => "id",
                    "value" => $this->exhibit_location->get_field("id"),
                    "type" => "hidden"
                ]) ?>

                <div class="row">
                    <div class="form-group col-md-12">
                        <label class="control-label">
                            <?php echo $this->lang->line("name") ?>
                        </label>
                        <?php echo form_input([
                            "name" => "name",
                            "id" => "name",
                            "placeholder" => $this->lang->line("name"),
                            "class" => "form-control",
                            "maxlength" => "255",
                            "required" => "required",
                            "value" => $this->exhibit_location->get_field("name")
                        ]) ?>
                        <div class="margin-top">
                            <?php echo $this->exhibit_location->get_error("name", "<div class='help-inline error'>", "</div>") ?>
                        </div>
                    </div>
                </div>

                <div class="toggle-content">
                    <div class="row">
                        <div class="form-group col-md-12">
                            <label class="control-label">
                                <?php echo $this->lang->line("latitude") ?>
                            </label>
                            <?php echo form_input([
                                "name" => "latitude",
                                "id" => "latitude",
                                "placeholder" => $this->lang->line("latitude"),
                                "class" => "form-control",
                                "maxlength" => "50",
                                "value" => $this->exhibit_location->get_field("latitude")
                            ]) ?>
                            <div class="margin-top">
                                <?php echo $this->exhibit_location->get_error("latitude", "<div class='help-inline error'>", "</div>") ?>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="form-group col-md-12">
                            <label class="control-label">
                                <?php echo $this->lang->line("longitude") ?>
                            </label>
                            <?php echo form_input([
                                "name" => "longitude",
                                "id" => "longitude",
                                "placeholder" => $this->lang->line("longitude"),
                                "class" => "form-control",
                                "maxlength" => "50",
                                "value" => $this->exhibit_location->get_field("longitude")
                            ]) ?>
                            <div class="margin-top">
                                <?php echo $this->exhibit_location->get_error("longitude", "<div class='help-inline error'>", "</div>") ?>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="form-group col-md-12">
                            <label class="control-label">
                                <?php echo $this->lang->line("description") ?>
                            </label>
                            <?php echo form_textarea([
                                "name" => "description",
                                "id" => "description",
                                "placeholder" => $this->lang->line("description"),
                                "class" => "form-control",
                                "rows" => "3",
                                "value" => $this->exhibit_location->get_field("description")
                            ]) ?>
                        </div>
                    </div>
                </div>
                <div class="text-center mt-3">
                    <a href="#" id="toggleMoreFields" class="btn btn-link">
                        Show More <i class="fa fa-angle-down"></i>
                    </a>
                </div>

            </div>

            <div class="modal-footer">
                <?php echo form_submit([
                    "name" => "submitBtn",
                    "id" => "submitBtn",
                    "value" => $this->lang->line("save"),
                    "class" => "btn btn-primary"
                ]) ?>
                <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo $this->lang->line("close") ?></button>
            </div>

            <?php echo form_close() ?>

        </div>
    </div>
</div>

<script>
jQuery(document).ready(function($) {
    // Hide the toggle content initially
    jQuery('.toggle-content').hide();

    // Handle form submission
    jQuery('#locationForm').on('submit', function(e) {
        e.preventDefault(); // Prevent default form submission

        // Clear previous error messages
        jQuery('.error').remove(); // This removes any div with class 'error'
        jQuery('.help-inline.error').remove(); // If CodeIgniter injects these
        jQuery('.alert-danger').remove(); // Clear any general alert messages

        jQuery.ajax({
            url: jQuery(this).attr('action'),
            type: 'POST',
            data: jQuery(this).serialize(),
            dataType: 'json',
            beforeSend: function() {
                // Show loading indicator
                jQuery('#submitBtn').prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Processing...');
            },
            success: function(response) {
                if(response.success) {
                    // Success case
                    jQuery('#locationModal').modal('hide');
                   
                  
                } else {
                    // Show validation errors
                    if (response.errors) {
                        jQuery.each(response.errors, function(key, value) {
                            var targetElement = jQuery('#'+key);
                            if (targetElement.length) {
                                targetElement.after('<div class="error text-danger">'+value+'</div>');
                            } else {
                                // Fallback for general errors not tied to a specific field
                                jQuery('#locationModal .modal-body').prepend('<div class="alert alert-danger fade show" role="alert">' + value + '</div>');
                            }
                        });
                        // If there are errors in the hidden fields, ensure they are shown
                        if (jQuery('.toggle-content .error').length > 0 && jQuery('.toggle-content').is(':hidden')) {
                            jQuery('#toggleMoreFields').click(); // Programmatically click to show fields
                            jQuery('#locationModal .modal-body').prepend('<div class="alert alert-warning fade show" role="alert">Please check the highlighted fields below.</div>');
                        }
                    } else {
                        // Generic error if no specific errors are returned
                        jQuery('#locationModal .modal-body').prepend('<div class="alert alert-danger fade show" role="alert">An unknown error occurred.</div>');
                    }
                }
            },
            error: function(xhr, status, error) {
                // Handle AJAX errors
                console.error("AJAX Error:", status, error, xhr.responseText);
                jQuery('#locationModal .modal-body').prepend('<div class="alert alert-danger fade show" role="alert">An error occurred while communicating with the server. Please try again.</div>');
            },
            complete: function() {
                jQuery('#submitBtn').prop('disabled', false).html('<?php echo $this->lang->line("save") ?>');
            }
        });
    });

    // Toggle "Show More / Show Less" functionality
    jQuery('#toggleMoreFields').on('click', function(e) {
        e.preventDefault(); // Prevent default link behavior

        var toggleContent = jQuery('.toggle-content');
        var toggleLink = jQuery(this);
        var icon = toggleLink.find('i');

        toggleContent.slideToggle(function() { // Use slideToggle for a smooth animation
            if (toggleContent.is(':visible')) {
                toggleLink.html('Show Less <i class="fa fa-angle-up"></i>');
            } else {
                toggleLink.html('Show More <i class="fa fa-angle-down"></i>');
            }
        });
    });

    // Ensure that when the modal is hidden, previous errors are cleared and fields are reset to hidden
    jQuery('#locationModal').on('hidden.bs.modal', function () {
        jQuery('.error').remove();
        jQuery('.help-inline.error').remove();
        jQuery('.alert-danger').remove(); // Remove any general alerts
        jQuery('.toggle-content').hide(); // Hide the content again
        jQuery('#toggleMoreFields').html('Show More <i class="fa fa-angle-down"></i>'); // Reset the link text
    });

    // If modal is opened and there's an ID (meaning editing an existing record), show all fields by default
    // This assumes the 'id' hidden input value is populated when editing.
    jQuery('#locationModal').on('show.bs.modal', function () {
        if (jQuery('#id').val() !== '' && jQuery('#id').val() !== '0') { // Check if ID has a value
            jQuery('.toggle-content').show();
            jQuery('#toggleMoreFields').html('Show Less <i class="fa fa-angle-up"></i>');
        }
    });

});
</script>