<div class="col-md-12">
    <ul class="breadcrumb">
        <li class="breadcrumb-item">
            <a href="dashboard/admin"><?php echo $this->lang->line("administration"); ?></a>
        </li>


    </ul>

</div>
<div class="row">

    <div class="col-md-3">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title"><?php echo $this->lang->line("template_list"); ?></h4>
            </div>
            <ul class="list-group list-group-flush template-list-nav">

                <?php foreach ($templates as $template): ?>
                    <li class="list-group-item template-item"
                        data-id="<?php echo $template['id']; ?>">
                        <?php echo htmlspecialchars($template['template_name']); ?>
                    </li>
                <?php endforeach; ?>
                <?php if (empty($templates)): ?>
                    <li class="list-group-item text-center text-muted">No templates found.</li>
                <?php endif; ?>
            </ul>
        </div>
    </div>

    <div class="col-md-9">
        <div class="card" id="template-editor-area" style="display:none;">
            <div class="card-header">
                <h4 class="card-title"><?php echo $this->lang->line("edit_template"); ?>: <span id="current-template-name"></span></h4>
                <small id="current-template-key" class="text-muted"></small>
            </div>
            <div class="card-body">
                <form id="templateEditForm" method="POST">
                    <input type="hidden" name="id" id="template-id-input">

                    <div class="form-group mb-3">
                        <label for="template-subject"><?php echo $this->lang->line("subject"); ?></label>
                        <input type="text" class="form-control" id="template-subject" name="subject" required>
                    </div>

                    <div class="alert alert-info py-2">
                        <strong><?php echo $this->lang->line("variables"); ?>:</strong>
                        <?php echo $this->lang->line("template_variable_warning"); ?>
                        <span id="variable-count-display" class="font-weight-bold">0</span>.
                    </div>

                    <div class="form-group mb-4">
                        <label for="template-body-content" required="required"><?php echo $this->lang->line("body_content"); ?> (HTML)</label>
                        <textarea class="form-control html-editor" id="template-body-content" name="body_content" rows="15" ></textarea>
                    </div>

                    <div class="d-flex justify-content-between align-items-center">
                        <div class="form-check">
                            <input type="checkbox" class="form-check-input" id="template-is-active" name="is_active" value="1">
                            <label class="form-check-label" for="template-is-active"><?php echo $this->lang->line("is_active"); ?></label>
                        </div>
                        <button type="submit" class="btn btn-primary" id="save-template-btn">
                            <?php echo $this->lang->line("save_changes"); ?>
                        </button>
                    </div>
                </form>
            </div>
        </div>
        <div id="no-template-selected" class="card card-body text-center p-5">
            <p class="lead text-muted"><?php echo $this->lang->line("select_template_to_edit"); ?></p>
        </div>
    </div>
</div>

<script>

    var emailTemplatesData = {};

    jQuery(document).ready(function() {
        // --- 1. Initialize HTML Editor (e.g., TinyMCE/CKEditor) ---
        initTinyTemp('template-body-content', '#templateEditForm', 'core');



        function updateVariableCount(content) {
            // Count occurrences of '%s' in the HTML content
            const count = (content.match(/%s/g) || []).length;
            jQuery('#variable-count-display').text(count);
            return count;
        }

        // --- 2. Template Selection Logic ---
        jQuery('.template-item').on('click', function() {
            jQuery('.template-item').removeClass('active');
            jQuery(this).addClass('active');

            const templateId = jQuery(this).data('id');
            loadTemplateDetails(templateId);
        });

        function loadTemplateDetails(id) {
            jQuery('#template-editor-area').hide();
            jQuery('#no-template-selected').show();

            // AJAX call to the controller to fetch full details of the template
            jQuery.ajax({
                url: getBaseURL()+"email_templates/load_template_details/" + id,
                method: 'GET',
                dataType: 'json',
                success: function(template) {
                    if (template) {
                        // Populate Form Fields
                        jQuery('#template-id-input').val(template.id);
                        jQuery('#current-template-name').text(template.template_name);
                        jQuery('#current-template-key').text(template.template_key);
                        jQuery('#template-subject').val(template.subject);
                        jQuery('#template-is-active').prop('checked', template.is_active == 1);

                        // Set content in the HTML editor
                        tinymce.get('template-body-content').setContent(template.body_content || '');
                        updateVariableCount(template.body_content || '');

                        // Show the editor
                        jQuery('#no-template-selected').hide();
                        jQuery('#template-editor-area').show();
                    } else {
                        pinesMessageV2({ ty: 'error', m:"Error loading template." });
                    }
                },

                    error: defaultAjaxJSONErrorsHandler

            });
        }

        //  Form Submission (Save Logic) ---
        jQuery('#templateEditForm').on('submit', function(e) {
            e.preventDefault();

            const form = jQuery(this);
            const id = jQuery('#template-id-input').val();
            const bodyContent = tinymce.get('template-body-content').getContent();
            const actualVariableCount = updateVariableCount(bodyContent);

            // Rebuild POST data including editor content and validation metadata
            const postData = form.serializeArray().reduce((obj, item) => {
                obj[item.name] = item.value;
                return obj;
            }, {});

            postData['body_content'] = bodyContent;
            postData['variable_count'] = actualVariableCount; // Explicitly send the count

            jQuery('#save-template-btn').prop('disabled', true).text('Saving...');

            jQuery.ajax({
                url: getBaseURL()+"email_templates/save_template/" + id,
                method: 'POST',
                data: postData,
                dataType: 'json',
                success: function(response) {
                    jQuery('#save-template-btn').prop('disabled', false).text('Save Changes');
                    if (response.success) {
                        pinesMessageV2({ ty: 'success',m: 'Template updated successfully!'});

                    } else {
                        pinesMessageV2({ ty: 'error',m: response.message });
                    }
                },
                error: function() {
                    jQuery('#save-template-btn').prop('disabled', false).text('Save Changes');
                    pinesMessageV2({ ty: 'error', m:'Server error during save operation.'});
                }
            });
        });


        // Auto-select the first template on load
        jQuery('.template-item:first').trigger('click');
    });
</script>