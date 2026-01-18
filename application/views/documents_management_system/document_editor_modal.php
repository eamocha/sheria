<div class="primary-style">
    <div id="document-editor-modal-inner-container">
        <div class="modal fade modal-container modal-resizable vertically-centered-modal">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header px-4">
                        <h4 id="title" class="modal-title">
                            <?= $this->lang->line("inline_edit_installation"); ?>
                        </h4>
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                    </div>
                    <div class="modal-body">
                        <div class="col-md-12 no-margin content-container">
                            <p>
                                <?= $this->lang->line("inline_edit_installation_description"); ?>
                            </p>
                            <p class="installation-steps-title">
                                <?= $this->lang->line("inline_edit_installation_steps_title"); ?>
                            </p>
                            <ul>
                                <li><?= $this->lang->line("inline_edit_installation_step_1"); ?></li>
                                <li><?= $this->lang->line("inline_edit_installation_step_2"); ?></li>
                                <li><?= $this->lang->line("inline_edit_installation_step_3"); ?></li>
                            </ul>
                            <p>
                                <?= sprintf(
                                    $this->lang->line("inline_edit_installation_support"),
                                    "https://documentation.sheria360.com/display/A4L/Sheria360+Document+Editor",
                                    "https://collaboration.sheria360.com/servicedesk/customer/portal/4"
                                ); ?>
                            </p>
                            <div class="col-md-12">
                                <div class="alert alert-warning no-margin">
                                    <?= $this->lang->line("already_installed"); ?>
                                </div>
                            </div>
                        </div>
                    </div><!-- /.modal-body -->
                    <div class="modal-footer">
                        <div>
                            <div class="btn-group">
                                <button
                                        type="button"
                                        class="btn btn-save btn-add-dropdown modal-save-btn"
                                        id="install-inline-edit"
                                        onclick="updateInlineEditingToolCookie();"
                                >
                                    <?= $this->lang->line("install"); ?>
                                </button>
                            </div>
                        </div>
                        <button
                                type="button"
                                class="btn btn-link close_model no_bg_button pull-right text-align-right flex-end-item"
                                data-dismiss="modal"
                        >
                            <?= $this->lang->line("cancel"); ?>
                        </button>
                    </div><!-- /.modal-footer -->
                </div><!-- /.modal-content -->
            </div><!-- /.modal-dialog -->
        </div><!-- /.modal -->
    </div>
</div>
