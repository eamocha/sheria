<div class="col-md-12" id="workflow-transition-container">
    <div class="row">
        <div class="col-md-12">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item" aria-current="page">
                        <a href="dashboard/admin"><?php echo $this->lang->line("administration"); ?></a>
                    </li>
                    <li class="breadcrumb-item" aria-current="page">
                        <a href="<?php echo app_url("modules/contract/contract_workflows/index#" . $workflow_id); ?>">
                            <?php echo $this->lang->line("contract_workflows"); ?>
                        </a>
                    </li>
                    <li class="breadcrumb-item active">
                        <?php echo $transition["id"] ? $this->lang->line("edit_transition") : $this->lang->line("add_transition"); ?>
                    </li>
                </ol>
            </nav>
        </div>
        <div class="col-md-12">
            <?php echo form_open("", "novalidate id=\"workflow-transition-form\""); ?>
            <div class="col-md-6 p-0 form-group row">
                <?php $language = strtolower(substr($this->session->userdata("AUTH_language"), 0, 2)); ?>
                <div class="col-md-3 p-0" <?php echo $language === "fr" ? "style=\"min-width: 26%;\"" : ""; ?>>
                    <h4><?php echo $transition["id"] ? $this->lang->line("edit_transition") : $this->lang->line("add_transition"); ?></h4>
                </div>
                <div class="col-md-1 p-0 transition-form-title-help d-none">
                    <span tooltipTitle="<?php echo $this->lang->line("contract_transition_title_help"); ?>"
                          class="tooltipTable float-right">
                        <i class="fas fa-question-circle"></i>
                    </span>
                </div>
            </div>
            <?php echo form_input(["name" => "id", "id" => "id", "value" => $transition["id"] ? $transition["id"] : "", "type" => "hidden"]); ?>
            <?php echo form_input(["name" => "workflow_id", "id" => "workflow-id", "value" => $workflow_id, "type" => "hidden"]); ?>
            <?php echo form_input(["name" => "from_step", "value" => $transition["from_step"], "type" => "hidden"]); ?>

            <div class="form-group row col-md-7 p-0">
                <label class="col-form-label text-right col-md-2 required"><?php echo $this->lang->line("action_button_label"); ?></label>
                <div class="col-md-4 p-0">
                    <?php echo form_input(["name" => "name", "id" => "name", "placeholder" => $this->lang->line("name"), "class" => "form-control", "maxlength" => "255", "value" => $transition["name"]]); ?>
                    <div data-field="name" class="inline-error d-none"></div>
                </div>
            </div>

            <div class="form-group row col-md-7 p-0">
                <label class="col-form-label text-right col-md-2"><?php echo $this->lang->line("fromStatus"); ?></label>
                <div class="col-md-6 p-0">
                    <p class="form-control-plaintext"><?php echo $from_step_name; ?></p>
                    <div data-field="from_step" class="inline-error d-none"></div>
                </div>
            </div>

            <div class="form-group row col-md-7 p-0">
                <label class="col-form-label text-right col-md-2 required"><?php echo $this->lang->line("toStatus"); ?></label>
                <div class="col-md-4 p-0">
                    <?php echo form_dropdown("to_step", $to_steps, $transition["to_step"], "id=\"to-step\" class=\"form-control select-picker\""); ?>
                    <div data-field="to_step" class="inline-error d-none"></div>
                </div>
                <div class="col-md-1 transitionToStepHelpTitle">
                    <span title="<?php echo $this->lang->line("transition_to_step_title"); ?>"
                          class="tooltipTable float-right">
                        <i class="fas fa-question-circle"></i>
                    </span>
                </div>
            </div>

            <div class="form-group row col-md-7 p-0">
                <label class="col-form-label text-right col-md-2"><?php echo $this->lang->line("description"); ?></label>
                <div class="col-md-8 p-0">
                    <?php echo form_textarea("comment", $transition["comment"], ["class" => "form-control", "rows" => "3"]); ?>
                </div>
                <div data-field="comment" class="inline-error d-none"></div>
            </div>

            <div class="col-md-12 tabs-container">
                <ul class="nav nav-pills nav-tabs">
                    <li class="breadcrumb-item nav-item" aria-current="page">
                        <a data-toggle="pill" href="#screen-container" class="nav-link active">
                            <?php echo $this->lang->line("screen"); ?>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a data-toggle="pill" href="#notifications-container" id="notifications-tab" class="nav-link">
                            <?php echo $this->lang->line("notifications"); ?>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a data-toggle="pill" href="#permissions-container" class="nav-link">
                            <?php echo $this->lang->line("permissions"); ?>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a data-toggle="pill" href="#approval-process-container" class="nav-link">
                            <?php echo $this->lang->line("approvals"); ?>
                        </a>
                    </li>
                </ul>

                <div class="tab-content">
                    <div id="screen-container" class="tab-pane fade in active show">
                        <?php $this->load->view("workflows/transition/screen_fields", ["object" => "contract"]); ?>
                    </div>
                    <div id="notifications-container" class="tab-pane fade">
                        <?php $this->load->view("workflows/transition/notifications"); ?>
                    </div>
                    <div id="permissions-container" class="tab-pane fade">
                        <?php $this->load->view("workflows/transition/permissions"); ?>
                    </div>
                    <div id="approval-process-container" class="tab-pane fade">
                        <?php if ($allow_advanced_settings) : ?>
                            <div class="form-group row">
                                <label class="col-md-5 col-sm-2 col-form-label text-right" for="checkbox">
                                    <?php echo $this->lang->line("transition_approval_note"); ?>
                                </label>
                                <div class="col-md-1 col-sm-7 pl-0 pt-2">
                                    <?php echo form_input(["name" => "approval_needed", "id" => "approval-needed", "value" => $transition["approval_needed"] ? "yes" : "no", "type" => "hidden"]); ?>
                                    <?php echo form_checkbox("", "", $transition["approval_needed"] ? true : false, "id=\"approval-checkbox\" onclick=\"toggleCheckbox(jQuery('#approval-needed','#workflow-transition-container'),jQuery(this,'#workflow-transition-container'));\" class=\"checkbox\""); ?>
                                </div>
                            </div>
                        <?php else : ?>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="alert alert-warning margin-top-15" role="alert">
                                        <?php echo $plan_feature_warning_msg; ?>
                                    </div>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <div class="clearfix">&nbsp;</div>

            <div class="form-group row col-md-12 p-0">
                <span class="loader-submit"></span>
                <?php echo form_button("", $this->lang->line("save"), "class=\"btn btn-light btn-info\" id=\"form-submit\""); ?>
            </div>
            <?php echo form_close(); ?>
        </div>
    </div>
</div>

<script type="text/javascript">
    availableUsers = <?php echo json_encode($users_list); ?>;
    availableEmails = <?php echo json_encode($users_emails); ?>;
    availableUserGroups = <?php echo json_encode($user_groups_list); ?>;
    externalToEmails = <?php echo $notifications["notify_to"] ? json_encode($notifications["notify_to"]) : "null"; ?>;
    externalCCEmails = <?php echo $notifications["notify_cc"] ? json_encode($notifications["notify_cc"]) : "null"; ?>;
</script>