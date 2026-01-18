<div class="col-md-12" id="attachmentFormContainer">
    <?php echo form_open($module_controller . "/upload_file", ["name" => "attachmentForm", "id" => "attachmentForm", "method" => "post", "enctype" => "multipart/form-data", "target" => "hidden_upload", "onsubmit" => "return attachmentDocumentFormSubmitAndStartUpload()"]); ?>
    <?php echo form_input(["id" => "module", "name" => "module", "value" => $module, "type" => "hidden"]); ?>
    <?php echo form_input(["id" => "module-record-id", "name" => "module_record_id", "value" => isset($module_record_id) ? $module_record_id : NULL, "type" => "hidden"]); ?>
    <div class="clear clearfix clearfloat"></div>
    <?php if (!empty($documentTypes)): ?>
        <div class="form-group col-md-12">
            <label class="control-label col-md-4"><?php echo $this->lang->line("document_type"); ?></label>
            <div class="col-md-7">
                <div>
                    <?php echo form_dropdown("document_type_id", $documentTypes, isset($systemPreferences[$module . "DocumentTypeId"]) ? $systemPreferences[$module . "DocumentTypeId"] : "", "id=\"document_type_id\" class=\"form-control\""); ?>
                </div>
            </div>
        </div>
    <?php endif; ?>
    <?php if (!empty($documentStatuses)): ?>
        <div class="form-group col-md-12 d-none">
            <label class="control-label col-md-4"><?php echo $this->lang->line("document_status"); ?></label>
            <div class="col-md-7">
                <div>
                    <?php echo form_dropdown("document_status_id", $documentStatuses, isset($systemPreferences[$module . "DocumentStatusId"]) ? $systemPreferences[$module . "DocumentStatusId"] : "", "id=\"document_status_id\" class=\"form-control\""); ?>
                </div>
            </div>
        </div>
    <?php endif; ?>
    <div class="form-group col-md-12 " id="uploadDocContainer">
        <label class="control-label col-md-4 required"><?php echo $this->lang->line("upload_document"); ?></label>
        <div class="col-md-7">
            <div>
                <input type="file" name="uploadDoc" id="uploadDoc" value="" class="" data-validation-engine="validate[required]" />
            </div>
        </div>
    </div>
    <div class="form-group col-md-12">
        <label class="control-label col-md-4"><?php echo $this->lang->line("remarks"); ?></label>
        <div class="col-md-7">
            <?php echo form_textarea(["name" => "comment", "id" => "comments", "class" => "form-control", "rows" => "2", "cols" => "0"]); ?>
        </div>
    </div>
    <?php echo form_close(); ?>
</div>
<div align="center" class="d-none" id="loading"><img src="assets/images/icons/16/loader-submit.gif" width="23" height="16" /></div>