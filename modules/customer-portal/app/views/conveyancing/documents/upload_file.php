<div class="modal fade modal-container modal-resizable">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title"><?php echo htmlspecialchars($title); ?></h4>
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
            </div>
            <div class="modal-body">
                <div id="upload-form-container" class="col-md-12 no-margin p-0 padding-10">
                    <?php echo form_open(current_url(), 'class="form-horizontal" novalidate name="attachment_form" id="attachment-form" method="post" enctype="multipart/form-data" target="hidden_upload"'); ?>

                    <?php echo form_input(["id" => "module", "name" => "module", "value" => $module, "type" => "hidden"]); ?>
                    <?php echo form_input(["id" => "module-record-id", "name" => "module_record_id", "value" => isset($module_record_id) ? $module_record_id : NULL, "type" => "hidden"]); ?>
                    <?php echo form_input(["id" => "lineage", "name" => "lineage", "type" => "hidden"]); ?>

                    <?php if (isset($contract_signature_status_id)) { ?>
                        <?php echo form_input(["id" => "contract-signature-status-id", "name" => "contract_signature_status_id", "value" => $contract_signature_status_id, "type" => "hidden"]); ?>
                    <?php } ?>

                    <div class="col-md-12 p-0 form-group row margin-bottom-10">
                        <label class="control-label col-md-3 pr-0 col-xs-5">
                            <?php echo $this->lang->line("document_type"); ?>
                        </label>
                        <div class="col-md-9 pr-0 col-xs-12">
                            <div class="col-md-8 p-0 col-xs-10">
                                <?php echo form_dropdown("document_type_id", $document_types, "", 'id="document_type_id" class="form-control select-picker"'); ?>
                            </div>
                            <div data-field="document_type_id" class="inline-error d-none"></div>
                        </div>
                    </div>

                    <div class="col-md-12 p-0 form-group row margin-bottom-10">
                        <label class="control-label col-md-3 pr-0 col-xs-5">
                            <?php echo $this->lang->line("document_status"); ?>
                        </label>
                        <div class="col-md-9 pr-0 col-xs-12">
                            <div class="col-md-8 p-0 col-xs-10">
                                <?php echo form_dropdown("document_status_id", $document_statuses, "", 'id="document_status_id" class="form-control select-picker"'); ?>
                            </div>
                            <div data-field="document_status_id" class="inline-error d-none"></div>
                        </div>
                    </div>

                    <div class="col-md-12 p-0">
                        <div class="form-group row col-md-12 p-0 col-xs-12">
                            <label class="control-label col-md-3 pr-0 col-xs-5">
                                <?php echo $this->lang->line("keywords"); ?>
                            </label>
                            <div class="col-md-9 pr-0">
                                <?php echo form_textarea(["name" => "comment", "id" => "comments", "class" => "form-control", "rows" => "2", "cols" => "0"]); ?>
                                <div data-field="comment" class="inline-error d-none"></div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-12 p-0">
                        <div class="form-group row col-md-12 p-0 col-xs-12">
                            <label class="control-label col-md-3 pr-0 required col-xs-5">
                                <?php echo $this->lang->line("upload_document"); ?>
                            </label>
                            <div class="col-md-9 pr-0">
                                <input type="file" name="uploadDoc" id="uploadDoc" value="" class="margin-top-5" />
                                <div data-field="uploadDoc" class="inline-error d-none"></div>
                            </div>
                        </div>
                    </div>

                    <?php echo form_close(); ?>
                </div>
            </div> <!-- /.modal-body -->

            <div class="modal-footer">
                <span class="loader-submit"></span>
                <button type="button" class="btn btn-save modal-save-btn" id="form-submit">
                    <?php echo $this->lang->line("save"); ?>
                </button>
                <button type="button" class="btn btn-link" data-dismiss="modal">
                    <?php echo $this->lang->line("cancel"); ?>
                </button>
            </div>
        </div> <!-- /.modal-content -->
    </div> <!-- /.modal-dialog -->
</div> <!-- /.modal -->
