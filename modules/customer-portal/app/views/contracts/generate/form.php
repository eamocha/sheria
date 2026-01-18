<div class="modal fade modal-container modal-resizable">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header d-flex flex-wrap">
                <div class="row col-md-12">
                    <h4 class="modal-title"><?php  echo htmlspecialchars($title); ?></h4>
                    <button type="button" class="close pt-0" data-dismiss="modal" aria-hidden="true">×</button>
                </div>
                <div class="row col-md-12 no-margin p-0 d-none" id="progress-bar">
                    <div class="col-md-10 no-margin pl-0 mt-1">
                        <div class="progress">
                            <div class="progress-bar" role="progressbar" progress=""></div>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <span><?php echo $this->lang->line("page"); ?></span>
                        <span id="current-page">1</span>
                        <span><?php echo $this->lang->line("of"); ?></span>
                        <span id="pages-count"></span>
                    </div>
                </div>
            </div>

            <div class="modal-body first-step">
                <div id="contract-generate-form" class="col-md-12 no-margin p-0">
                    <fieldset id="fieldset1">
                        <?php
                        echo form_open("", "novalidate id=\"form1\"");
                        $this->load->view("contracts/generate/options");
                        echo form_close();
                        ?>
                    </fieldset>

                    <fieldset id="fieldset2">
                        <?php echo form_open("", "novalidate id=\"form2\""); ?>
                        <?php echo form_close(); ?>
                    </fieldset>

                    <fieldset id="fieldset3">
                        <?php echo form_open("", "novalidate id=\"form3\""); ?>
                        <?php echo form_close(); ?>
                    </fieldset>
                </div>
            </div><!-- /.modal-body -->

            <div class="modal-footer d-none justify-content-between" data-field="1">
                <div>
                    <span class="loader-submit"></span>
                    <button type="button" class="save-button btn-info previous d-none">
                        <img src="assets/images/contract/next.svg" width="14" height="14">
                        <?php echo $this->lang->line("previous"); ?>
                    </button>

                    <button type="button" class="save-button btn-info next margin-left-btn-save">
                        <?php echo $this->lang->line("next"); ?>
                        <img src="assets/images/contract/next.svg" width="14" height="14">
                    </button>

                    <button type="button" class="save-button btn-info next-page d-none">
                        <?php echo $this->lang->line("next"); ?>
                        <img src="assets/images/contract/next.svg" width="14" height="14">
                    </button>

                    <button type="button" class="save-button btn-info d-none margin-left-btn-save" id="form-submit">
                        <?php echo $this->lang->line("submit"); ?>
                    </button>

                    <?php if ($show_notification): ?>
                        <span class="label-notification-checkbox pt-10 d-inline-block v-al-n-5 d-none" id="notification-div">
                            <?php $this->load->view("notifications/wrapper", [
                                "hide_show_notification" => $hide_show_notification,
                                "container" => "'#contract-generate-container'",
                                "hide_label" => false
                            ]); ?>
                        </span>
                    <?php endif; ?>
                </div>

                <button type="button" class="close_model no_bg_button float-right text-right" data-dismiss="modal">
                    <?php echo $this->lang->line("cancel"); ?>
                </button>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->