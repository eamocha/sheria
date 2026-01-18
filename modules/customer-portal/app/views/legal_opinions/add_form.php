<div class="modal fade primary-style" id="addRequestModal" tabindex="-1" role="dialog" aria-labelledby="addRequestModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addRequestModalLabel">Add New Legal Opinion Request</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <?php echo form_open("", 'name="opinionForm" id="opinion-form" method="post" class="form-horizontal" onsubmit="return validateLegalOpinionForm()"'); ?>
                <?php echo form_input(["name" => "id", "id" => "id", "value" => $opinionData["id"] ?? 0, "type" => "hidden"]); ?>
                <?php echo form_input(["name" => "archived", "value" => $opinionData["archived"] ?? "no", "type" => "hidden"]); ?>
                <?php echo form_input(["name" => "opinion_type_id", "value" => $opinionData["opinion_type_id"] ?? 1, "type" => "hidden"]); ?>


                <!-- Subject Field -->
                <div class="form-group">
                    <label for="title" class="required-field">Subject</label>
                    <input type="text" class="form-control required" id="title" name="title" required
                           data-error-message="Subject is required">
                    <div data-field="title" class="inline-error d-none"></div>
                </div>

                <!-- Background Info Field -->
                <div class="form-group">
                    <label for="background_info" class="required-field">Introduction/Background</label>
                    <textarea class="form-control required" id="background_info" name="background_info" rows="3" required
                              data-error-message="Background information is required"></textarea>
                    <div data-field="background_info" class="inline-error d-none"></div>
                </div>

                <!-- Detailed Info Field -->
                <div class="form-group">
                    <label for="detailed_info" class="required-field">Detailed Information</label>
                    <textarea class="form-control required" id="detailed_info" name="detailed_info" rows="5" required data-error-message="Detailed information is required"></textarea>
                    <div data-field="detailed_info" class="inline-error d-none"></div>
                </div>

                <!-- Legal Question Field -->
                <div class="form-group">
                    <label for="legal_question" class="required-field">Legal Issue/Question</label>
                    <textarea class="form-control required" id="legal_question" name="legal_question" rows="3" required
                              data-error-message="Legal question is required"></textarea>
                    <div data-field="legal_question" class="inline-error d-none"></div>
                </div>

                <!-- Due Date Field -->
                <div class="datepair col-md-12 p-0 d-none" data-language="javascript" id="due-date-container">
                    <div class="col-md-12 form-group p-0 row m-0 mb-10">
                        <label class="control-label col-md-3 pr-0 required col-xs-5 restriction-tooltip" id="dueDateLabelId"><?php echo $this->lang->line("due_date");?></label>
                        <div class="col-md-8 pr-0 col-xs-10 " id="due-date-wrapper">
                            <div class="row m-0">
                                <div class="form-group input-group date date-picker col-md-9 p-0 mb-3" id="form-due-date">
                                    <?php echo form_input(["name" => "due_date", "id" => "form-due-date-input", "placeholder" => "YYYY-MM-DD", "value" => $cloned_date ?? $opinionData["due_date"], "class" => "date start form-control"]);?>
                                    <span class="input-group-addon input-group-text"><i class="fa fa-calendar purple_color p-9 cursor-pointer-click"></i></span>
                                </div>
                            </div>
                            <div data-field="due_date" class="inline-error d-none"></div>
                        </div>
                    </div>
                </div>

                <!-- Priority Field -->
                <div class="form-group d-none">
                    <label for="priority">Priority</label>
                    <select class="form-control" id="priority" name="priority" required>
                        <option value="low">Low</option>
                        <option value="medium" selected>Medium</option>
                        <option value="high">High</option>
                        <option value="critical">Critical</option>
                    </select>
                    <div data-field="priority" class="inline-error d-none"></div>
                </div>

                <!-- Opinion Type Field -->
                <div class="form-group d-none">
                    <label for="opinion_type_id">Type/Category</label>
                    <select class="form-control" id="opinion_type_id" name="opinion_type_id" required>
                        <option value="1">Client Portal</option>
                        <option value="2">Office Admin</option>
                        <option value="3">Case</option>
                        <option value="4">Other</option>
                    </select>
                    <div data-field="opinion_type_id" class="inline-error d-none"></div>
                </div>

                <!-- Attachments Field -->
                <div class="form-group">
                    <label>Attachments</label>
                    <div class="custom-file">
                        <input type="file" class="custom-file-input" id="requestAttachments" name="attachments[]" multiple>
                        <label class="custom-file-label" for="requestAttachments">Choose files</label>
                    </div>
                    <div data-field="attachments" class="inline-error d-none"></div>
                    <small class="form-text text-muted">You can attach multiple files if needed</small>
                </div>

                <!-- Notify Requester Checkbox -->
               <div class="form-group row align-items-center"> <div class="col-auto"> <input type="checkbox" class="form-check-input" id="send_notifications_email" name="notifyRequester" checked>
    </div>
    <div class="col-auto">
        <label class="form-check-label" for="send_notifications_email">Send Notification by email</label>
    </div>
    <div data-field="send_notifications_email" class="inline-error d-none col-12 mt-1"></div>
</div>

                <?php echo form_close() ?>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                <button type="submit" class="btn btn-primary" id="save-opinion-btn">Submit Request</button>
            </div>
        </div>
    </div>
</div>

<!-- Add this JavaScript validation -->
<script>
    function validateLegalOpinionForm() {
        let isValid = true;

        // Clear previous errors
        $('.inline-error').addClass('d-none').text('');

        // Validate each required field
        $('.required').each(function() {
            const field = $(this);
            const errorDiv = $(`[data-field="${field.attr('name')}"]`);

            if (!field.val().trim()) {
                errorDiv.text(field.data('error-message')).removeClass('d-none').addClass('text-danger');
                field.addClass('is-invalid');
                isValid = false;
            } else {
                field.removeClass('is-invalid');
            }
        });

        // Additional validation for TinyMCE fields if needed
        if (typeof tinymce !== 'undefined') {
            const tinyFields = ['detailed_info', 'legal_question'];
            tinyFields.forEach(fieldId => {
                const content = tinymce.get(fieldId)?.getContent() || '';
                if (!content.trim()) {
                    $(`[data-field="${fieldId}"]`)
                        .text(`${fieldId.replace('_', ' ')} is required`)
                        .removeClass('d-none')
                        .addClass('text-danger');
                    isValid = false;
                }
            });
        }

        return isValid;
    }

    // Initialize form validation on submit
    $(document).ready(function() {
       jQuery('#opinion-form').on('submit', function(e) {
            if (!validateLegalOpinionForm()) {
                e.preventDefault();
                // Scroll to first error
                jQuery('.is-invalid').first().focus();
            }
        });

        // Change button type to submit
        jQuery('#save-opinion-btn').attr('type', 'submit');
    });
</script>

<style>
    .required-field::after {
        content: " *";
        color: #dc3545;
    }
    .is-invalid {
        border-color: #dc3545;
    }
</style>