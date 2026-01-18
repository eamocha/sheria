<div id="milestone-form-main" class="primary-style">
    <div class="modal fade modal-container modal-resizable">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title"><?php echo htmlspecialchars($page_title); ?></h4>
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                </div>
                <div class="modal-body">
                    <div id="milestone-form-container" class="col-md-12 no-margin p-0 padding-10">
                        <?php echo form_open(current_url(), ["class" => "form-horizontal", "novalidate" => "", "name" => "milestone_form", "id" => "milestone-form", "method" => "post", "enctype" => "multipart/form-data", "target" => "hidden_upload"]); ?>
                        <?php
                        echo form_input(["name" => "contract_id", "value" => (string)$contract["id"], "id" => "contract-id", "type" => "hidden"]);
                        echo form_input(["name" => "id", "value" => (string)$milestone_id, "id" => "contract-id", "type" => "hidden"]);
                        ?>
                        <div class="row no-margin col-md-12 p-0">
                            <div class="form-group row col-md-12 p-0 col-xs-12">
                                <label class="col-form-label text-right col-md-3 pr-0 col-xs-3 required" for="title">
                                    <?php echo $this->lang->line("title"); ?>
                                </label>
                                <div class="col-md-9">
                                    <?php echo form_input("title", ($milestone_data["title"] ?? ""), "class=\"form-control first-input\" id=\"title\" required"); ?>
                                    <div data-field="title" class="inline-error d-none"></div>
                                </div>
                            </div>
                        </div>
                        <div class="row no-margin col-md-12 p-0">
                            <div class="form-group row col-md-12 p-0 col-xs-12">
                                <label class="col-form-label text-right col-md-3 pr-0 col-xs-3" for="number">
                                    <?php echo $this->lang->line("serial_number"); ?>
                                </label>
                                <div class="col-md-9">
                                    <?php echo form_input("serial_number", ($milestone_data["serial_number"] ?? ""), "class=\"form-control first-input\" id=\"serial-number\""); ?>
                                    <div data-field="number" class="inline-error d-none"></div>
                                </div>
                            </div>
                        </div>

                        <div class="row no-margin col-md-12 p-0">
                            <div class="form-group row col-md-12 p-0 col-xs-12">
                                <label class="col-form-label text-right col-md-3 pr-0 col-xs-3">
                                    <?php echo $this->lang->line(($this->is_auth->is_layout_rtl() ? "percentage" : "amount")); ?>
                                </label>
                                <div class="col-md-9 d-flex">
                                    <div class="chk-toggle tooltip-title pl-3" title="<?php echo $this->lang->line("set_milestone_target"); ?>">
                                        <input id="target" <?php echo (($target == "percentage") ? "checked" : "unchecked"); ?> name="target" type="checkbox" form="milestone-form"/>
                                        <label for="target" class="label-primary"></label>
                                    </div>
                                    <label class="col-form-label pl-3">
                                        <?php echo $this->lang->line(($this->is_auth->is_layout_rtl() ? "amount" : "percentage")); ?>
                                    </label>
                                </div>
                            </div>
                        </div>


                        <div class="row no-margin col-md-12 p-0 <?php echo (($target == "percentage") ? "d-none" : ""); ?>" id="amount_container">
                            <div class="form-group row col-md-12 p-0 col-xs-12">
                                <label class="col-form-label text-right col-md-3 pr-0 col-xs-3" for="amount"></label>
                                <div class="col-md-3">
                                    <?php echo form_input(["name" => "amount", "value" => (isset($milestone_data["amount"]) ? (double)$milestone_data["amount"] : ""), "class" => "form-control first-input", "id" => "amount", "type" => "number"]); ?>
                                    <div data-field="amount" class="inline-error d-none"></div>
                                </div>
                                <div class="col-md-2 pl-0">
                                    <?php echo form_dropdown("currency_id", $currencies, $milestone_data["currency_id"], "id=\"currency\" class=\"form-control select-picker\" data-live-search=\"true\" data-size=\"" . $this->session->userdata("max_drop_down_length") . "\""); ?>
                                    <div data-field="currency_id" class="inline-error d-none"></div>
                                </div>
                            </div>
                        </div>
                        <div class="row no-margin col-md-12 p-0 <?php echo (($target == "percentage") ? "" : "d-none"); ?>" id="percentage_container">
                            <div class="form-group row col-md-12 p-0 col-xs-12">
                                <label class="col-form-label text-right col-md-3 pr-0 col-xs-3" for="percentage"></label>
                                <div class="input-group col-md-5 pr-0">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text" id="validation-tooltip-username-prepend">%</span>
                                    </div>
                                    <div class="input-group-prepend">
                                    <?php echo form_input(["name" => "percentage", "value" => (isset($milestone_data["percentage"]) ? (double)$milestone_data["percentage"] : ""), "class" => "form-control first-input", "id" => "percentage", "type" => "number"]); ?>
                                    </div>
                                    <div data-field="percentage" class="inline-error d-none"></div>
                            </div>
                            </div>
                        </div>
                        <div class="row no-margin col-md-12 p-0">
                            <div class="form-group row col-md-12 p-0 col-xs-12">
                                <label class="col-form-label text-right col-md-3 pr-0 col-xs-3" for="deliverables">
                                    <?php echo $this->lang->line("deliverables"); ?>
                                </label>
                                <div class="col-md-9">
                                    <?php echo form_textarea(["name" => "deliverables", "id" => "deliverables", "value" => ($milestone_data["deliverables"] ?? ""), "class" => "form-control", "rows" => "5", "cols" => "0", "dir" => "auto"]); ?>
                                    <div data-field="deliverables" class="inline-error d-none"></div>
                                </div>
                            </div>
                        </div>
                        <div class="row no-margin col-md-12 p-0">
                            <div class="form-group row col-md-12 p-0 col-xs-12">
                                <label class="col-form-label text-right col-md-3 pr-0 col-xs-3" for="start-date-input">
                                    <?php echo $this->lang->line("start_date"); ?>
                                </label>
                                <div class="col-md-5 pr-0">
                                    <div id="start-date" class="input-group date" data-date-format="mm-dd-yyyy">
                                        <?php echo form_input(["name" => "start_date", "value" => ($milestone_data["start_date"] ?? ""), "id" => "start-date-input", "placeholder" => "YYYY-MM-DD", "class" => "form-control", "autocomplete" => "off"]); ?>
                                        <div class="input-group-append">
                                            <span role="button" class="input-group-text"><i class="fa-solid fa-calendar-days"></i></span>
                                        </div>
                                    </div>
                                    <div data-field="start_date" class="inline-error d-none"></div>
                                </div>
                            </div>
                        </div>
                        <div class="row no-margin col-md-12 p-0">
                            <div class="form-group row col-md-12 p-0 col-xs-12">
                                <label class="col-form-label text-right col-md-3 pr-0 col-xs-3" for="due-date-input">
                                    <?php echo $this->lang->line("due_date"); ?>
                                </label>
                                <div class="col-md-5 pr-0">
                                    <div id="due-date" class="input-group date" data-date-format="mm-dd-yyyy">
                                        <?php echo form_input(["name" => "due_date", "value" => ($milestone_data["due_date"] ?? ""), "id" => "due-date-input", "placeholder" => "YYYY-MM-DD", "class" => "form-control", "autocomplete" => "off"]); ?>
                                        <div class="input-group-append">
                                            <span role="button" class="input-group-text"><i class="fa-solid fa-calendar-days"></i></span>
                                        </div>
                                    </div>
                                    <div data-field="due_date" class="inline-error d-none"></div>
                                    <?php if (!isset($cp_milestone)): ?>
                                        <div class="row no-margin col-md-10 p-0" id="notify-me-before-link">
                                                <span class="assign-to-me-link-id-wrapper">
                                                    <a href="javascript:;" id="notify-me-link"
                                                       onclick="notifyMeBefore(jQuery('#milestone-form-main'));">
                                                        <?php echo $this->lang->line("notify_me_before"); ?>
                                                    </a>
                                                </span>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>

                        <div class="row no-margin col-md-12 p-0 d-none" id="notify-me-before-container">
                            <div class="form-group row col-md-12 p-0 col-xs-12">
                                <label class="col-form-label text-right col-md-3 pr-0 col-xs-3">
                                    <?php echo $this->lang->line("notify_me_before"); ?>
                                </label>
                                <div class="col-md-9 pr-0 col-xs-10 d-flex pl-0" id="notify-me-before">
                                    <div class="d-flex mb-10 col-md-5">
                                        <?php echo form_input(["name" => "notify_me_before[time]", "class" => "form-control", "value" => ($reminder_interval_date ?? $notify_before["time"] ?? ""), "id" => "notify-me-before-time", "disabled" => true]); ?>
                                        <?php echo form_dropdown("notify_me_before[time_type]", $notify_me_before_time_types, ($notify_before["time_type"] ?? ""), "class=\"form-control select-picker mx-2\" id=\"notify-me-before-time-type\" disabled"); ?>
                                        <label class="control-label"><?php echo $this->lang->line("reminder_by"); ?></label>
                                    </div>
                                    <?php echo form_dropdown("notify_me_before[type]", $notify_me_before_types, ($notify_before["type"] ?? ""), "class=\"form-control select-picker col-md-6 pb-0\" id=\"notify-me-before-type\" disabled"); ?>
                                    <a href="javascript:;" onclick="hideRemindMeBefore(jQuery('#milestone-form-main'));" class="btn btn-link align-middle">
                                        <i class="icon fa-solid fa-xmark"></i>
                                    </a>
                                </div>
                                <span class="col-form-label text-right col-md-3 pr-0 col-xs-3"></span>
                                <div data-field="notify_before" class="inline-error d-none pl-3"></div>
                            </div>
                        </div>
                        <div class="row no-margin col-md-12 p-0" id="attachments-container">
                            <label class="col-form-label text-right col-md-3 pr-0 col-xs-3"><i class="fa-solid fa-link purple_color"></i>&nbsp;
                                <?php echo $this->lang->line("attach_file"); ?>
                            </label>
                            <div id="milestone-attachments" class="col-md-9 p-0">
                                <div class="input-group col-md-8 pr-0">
                                    <input name="milestone_attachment_0" id="milestone-attachment-0" type="file" class="milestone-file" value="" />
                                </div>
                                <?php echo form_input(["name" => "milestone_attachments[]", "value" => "milestone_attachment_0", "type" => "hidden"]); ?>
                            </div>
                            <div class="row no-margin col-md-12 p-0">
                                <div class="col-form-label text-right col-md-3 pr-0 col-xs-3">&nbsp;</div>
                                <div class="col-md-9">
                                    <div class="col-md-10 p-0">
                                        <div data-field="file" class="inline-error d-none"></div>
                                        <button type="button" onclick="milestoneAttachFile()" class="btn-link p-0">
                                            <?php echo $this->lang->line("add_more"); ?>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <?php echo form_close(); ?>
                    </div>
                </div><div class="modal-footer">
                    <span class="loader-submit"></span>
                    <button type="button" class="btn btn-save modal-save-btn"
                            id="form-submit">
                        <?php echo $this->lang->line("save"); ?>
                    </button>
                    <button type="button" class="btn btn-link"
                            data-dismiss="modal">
                        <?php echo $this->lang->line("cancel"); ?>
                    </button>
                </div>
            </div></div></div></div>