<div class="col-md-12" id="workflows-management-container">
    <div class="row">
        <div class="col-md-12">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item" aria-current="page"><a href="dashboard/admin"><?php echo $this->lang->line("administration"); ?></a></li>
                    <li class="breadcrumb-item" aria-current="page"><?php echo $this->lang->line("workflows"); ?></li>
                    <li class="breadcrumb-item" aria-current="page">
                        <a href="<?php echo app_url("modules/contract/contract_statuses"); ?>">
                            <?php echo $this->lang->line("contract_statuses"); ?>
                        </a>
                    </li>
                </ol>
            </nav>
        </div>
        <?php if (empty($workflows)) { ?>
            <div class="col-md-12">
                <?php echo $this->lang->line("there_are_no_workflows"); ?>
                <a onclick="workflowForm();" href="javascript:;" title="<?php echo $this->lang->line("click_to_add"); ?>">
                    <i class="fas fa-plus-circle"></i>
                </a>
            </div>
        <?php } else { ?>
            <div class="col-md-10">
                <div class="margin-bottom">
                    <label class="board-title">
                        <?php echo $this->lang->line("contract_workflows"); ?>
                        &nbsp;&nbsp;
                    </label>
                    <a onclick="workflowForm();" href="javascript:;" title="<?php echo $this->lang->line("click_to_add"); ?>">
                        <i class="fas fa-plus-circle"></i>
                    </a>
                </div>
            </div>
            <div class="col-md-12" id="tabs">
                <ul class="col-md-2" id="tabs-li">
                    <?php foreach ($workflows as $workflow) { ?>
                        <li class="col-md-12">
                            <a href='modules/contract/contract_workflows/index<?php echo $workflow_id ? "/" . $workflow_id . "#" . $workflow["id"] : "#" . $workflow["id"]; ?>'>
                                <?php echo $workflow["name"]; ?>
                            </a>
                        </li>
                    <?php } ?>
                </ul>
                <div class="col-md-10 col-xs-12 table-responsive">
                    <?php foreach ($workflows as $workflow) { ?>
                        <div id="<?php echo $workflow["id"]; ?>">
                            <div class="row justify-content-between">
                                <div class="col-md-11 p-0">
                                    <?php if ($workflow["category"] != "system") { ?>
                                        <div class="col-form-label col-md-10 padding-top7">
                                            <b><?php echo $this->lang->line("contract_type"); ?></b>:&nbsp;&nbsp;
                                            <?php echo $workflow["contract_types_names"]; ?>
                                        </div>
                                    <?php } ?>
                                </div>
                                <?php if ($workflow["category"] != "system") { ?>
                                    <div class="col-md-1">
                                        <div class="dropdown more">
                                            <button class="btn" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                <i class="spr spr-gear"></i>
                                            </button>
                                            <div class="dropdown-menu dropdown-menu-right" aria-labelledby="dLabel" role="menu">
                                                <a class="dropdown-item" onclick="workflowForm('<?php echo $workflow["id"]; ?>')" href="javascript:;"><?php echo $this->lang->line("edit_workflow"); ?></a>
                                                <a class="dropdown-item"  href="modules/contract/contract_workflows/configure/<?php echo $workflow["id"] ?>"><?php echo $this->lang->line("configure"); ?></a>
                                                <a class="dropdown-item" onclick="confirmationDialog('confirm_delete_record', {resultHandler: deleteWorkflow, parm: '<?php echo $workflow["id"]; ?>'})" href="javascript:;"><?php echo $this->lang->line("delete_workflow"); ?></a>
                                            </div>
                                        </div>
                                    </div>
                                <?php } ?>
                            </div>
                            <div class="clearfix">&nbsp;</div>
                            <?php if (!empty($records[$workflow["id"]]["statuses"])) { ?>
                                <div class="col-md-10 p-0 form-group row">
                                    <a onclick="workflowStatusForm('<?php echo $workflow["id"]; ?>');" href="javascript:;">
                                        <?php echo $this->lang->line("add_new_workflow_status"); ?>
                                    </a>
                                </div>
                                <div class="col-md-12 p-0">
                                    <table class="table table-bordered table-striped table-hover">
                                        <tr>
                                            <th width="5%">&nbsp;</th>
                                            <th><?php echo $this->lang->line("step"); ?></th>
                                            <th><?php echo $this->lang->line("type"); ?></th>
                                            <th><?php echo $this->lang->line("transitions"); ?></th>
                                        </tr>
                                        <?php foreach ($records[$workflow["id"]]["statuses"] as $status) { ?>
                                            <?php $is_global = $status["is_global"] == 1 ? true : false; ?>
                                            <tr id="workflow-<?php echo $workflow["id"] . "-status-" . $status["id"]; ?>">
                                                <td>
                                                    <?php if ($status["start_point"] == 0 || $status["approval_start_point"] == 0) { ?>
                                                        <div class="dropdown more float-right">
                                                            <button class="btn" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                                <i class="spr spr-gear"></i>
                                                            </button>
                                                            <div class="dropdown-menu dropdown-menu-right" aria-labelledby="dLabel">
                                                                <?php if ($status["approval_start_point"] == 0) { ?>
                                                                    <a class="dropdown-item" href="javascript:;" onclick="setAsApprovalStartPoint('<?php echo $workflow["id"]; ?>', '<?php echo $status["id"]; ?>');">
                                                                        <?php echo $this->lang->line("set_as_approval_start_point"); ?>
                                                                    </a>
                                                                <?php } ?>
                                                                <?php if ($status["start_point"] == 0) { ?>
                                                                    <a class="dropdown-item" href="javascript:;" onclick="setAsStartPoint('<?php echo $workflow["id"]; ?>', '<?php echo $status["id"]; ?>');">
                                                                        <?php echo $this->lang->line("set_as_start_point"); ?>
                                                                    </a>
                                                                    <a class="dropdown-item" href="javascript:;" onclick="confirmationDialog('confirmation_delete_selected_record', {resultHandler: deleteWorkflowStatus, parm: '<?php echo $status["id"]; ?>'})">
                                                                        <?php echo $this->lang->line("delete"); ?>
                                                                    </a>
                                                                <?php } ?>
                                                            </div>
                                                        </div>
                                                    <?php } ?>
                                                </td>
                                                <td>
                                                    <?php echo $status["step_name"] . (0 < $status["start_point"] ? "&nbsp;(" . $this->lang->line("start_point") . ")" : "") . (0 < $status["approval_start_point"] ? "&nbsp;(" . $this->lang->line("approval_start_point") . ")" : ""); ?>
                                                    &nbsp;
                                                </td>
                                                <td> <?php echo $is_global ? $this->lang->line("global_status") : $this->lang->line("transitional_status"); ?></td>
                                                <td>
                                                    <?php if (!$is_global) { ?>
                                                        <div class="col-md-12 p-0 margin-bottom">
                                                            <a href="<?php echo site_url("contract_workflows/add_transition/" . $workflow["id"] . "/" . $status["id"]); ?>" title="<?php echo $this->lang->line("add_transition"); ?>">
                                                                <i class="fa-solid fa-circle-plus"></i>
                                                            </a>&nbsp;
                                                            <a href="javascript:;" onclick="statusTransitionsViewForm('<?php echo $status["id"]; ?>', '<?php echo $workflow["id"]; ?>');" title="<?php echo $this->lang->line("view_transitions"); ?>">
                                                                <i class="fa-solid fa-list"></i>
                                                            </a>
                                                        </div>
                                                    <?php } ?>
                                                    <?php if (!empty($records[$workflow["id"]]["transitions"])) { ?>
                                                        <?php foreach ($records[$workflow["id"]]["transitions"] as $transitions) { ?>
                                                            <?php if ($transitions["from_step"] === $status["id"]) { ?>
                                                                <div class="col-md-12 p-0" id="transition-<?php echo $transitions["id"]; ?>">
                                                                    <span><b><?php echo $transitions["name"]; ?></b>:</span>&nbsp;
                                                                    <span><?php echo $transitions["from_step_name"]; ?></span>&nbsp;
                                                                    <span class="fa-solid fa-arrow-right"></span>&nbsp;
                                                                    <span><?php echo $transitions["to_step_name"]; ?></span>&nbsp;&nbsp;
                                                                    <span>
                                                                        <a href="<?php echo site_url("contract_workflows/edit_transition/" . $transitions["id"]); ?>" title="<?php echo $this->lang->line("edit_transition"); ?>">
                                                                            <i class="fa-solid fa-pen-to-square"></i>
                                                                        </a>&nbsp;
                                                                        <a href="javascript:;" onclick="confirmationDialog('confirm_delete_record', {resultHandler: deleteTransition, parm: <?php echo $transitions["id"]; ?>} )" title="<?php echo $this->lang->line("delete_transition"); ?>">
                                                                            <i class="red fa-solid fa-trash-can"></i>
                                                                        </a>
                                                                    </span>
                                                                </div>
                                                            <?php } ?>
                                                        <?php } ?>
                                                    <?php } ?>
                                                </td>
                                            </tr>
                                        <?php } ?>
                                    </table>
                                </div>
                            <?php } else { ?>
                                <div class='col-md-10 p-0 center'>
                                    <?php echo $this->lang->line("no_workflow_statuses"); ?>
                                    <a onclick="workflowStatusForm('<?php echo $workflow["id"]; ?>');" href="javascript:;"><?php echo $this->lang->line("click_to_add"); ?></a>
                                </div>
                            <?php } ?>
                        </div>
                    <?php } ?>
                </div>
            </div>
        <?php } ?>
    </div>
</div>