<?php
/**
 * Workflows Management View
 *
 * Displays contract workflows and their statuses with management options
 */
?>
<div class="col-md-12" id="workflows-management-container">
    <div class="row">
        <div class="col-md-12">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item" aria-current="page">
                        <a href="dashboard/admin"><?php echo $this->lang->line("administration"); ?></a>
                    </li>
                    <li class="breadcrumb-item" aria-current="page">
                        <?php echo $this->lang->line("workflows"); ?>
                    </li>
                    <li class="breadcrumb-item" aria-current="page">
                        <a href="<?php echo app_url("modules/contract/contract_statuses"); ?>">
                            <?php echo $this->lang->line("contract_statuses"); ?>
                        </a>
                    </li>
                </ol>
            </nav>
        </div>

        <?php if (empty($workflows)): ?>
            <div class="col-md-12">
                <?php echo $this->lang->line("there_are_no_workflows"); ?>
                <a onclick="workflowForm();" href="javascript:;" title="<?php echo $this->lang->line("click_to_add"); ?>">
                    <i class="fas fa-plus-circle"></i>
                </a>
            </div>
        <?php else: ?>
            <div class="col-md-10">
                <div class="margin-bottom">
                    <label class="board-title"><?php echo $this->lang->line("contract_workflows"); ?>&nbsp;&nbsp;</label>
                    <a onclick="workflowForm();" href="javascript:;" title="<?php echo $this->lang->line("click_to_add"); ?>">
                        <i class="fas fa-plus-circle"></i>
                    </a>
                </div>
            </div>

            <div class="col-md-12" id="tabs">
                <ul class="col-md-2" id="tabs-li">
                    <?php foreach ($workflows as $workflow): ?>
                        <li class="col-md-12">
                            <a href='modules/contract/contract_workflows/index<?php echo $workflow_id ? "/" . $workflow_id . "#" . $workflow["id"] : "#" . $workflow["id"]; ?>'>
                                <?php echo $workflow["name"]; ?>
                            </a>
                        </li>
                    <?php endforeach; ?>
                </ul>

                <div class="col-md-10 col-xs-12 table-responsive">
                    <?php foreach ($workflows as $workflow): ?>
                        <div id="<?php echo $workflow["id"]; ?>">
                            <div class="row justify-content-between">
                                <div class="col-md-11 p-0">
                                    <?php if ($workflow["category"] != "system"): ?>
                                        <div class="col-form-label col-md-10 padding-top7">
                                            <b><?php echo $this->lang->line("contract_type"); ?>:</b>&nbsp;&nbsp;
                                            <?php echo $workflow["contract_types_names"]; ?>
                                        </div>
                                    <?php endif; ?>
                                </div>

                                <?php if ($workflow["category"] != "system"): ?>
                                    <div class="col-md-1">
                                        <div class="dropdown more">
                                            <button class="btn" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                <i class="spr spr-gear"></i>
                                            </button>
                                            <div class="dropdown-menu dropdown-menu-right" aria-labelledby="dLabel" role="menu">
                                                <a class="dropdown-item" onclick="workflowForm('<?php echo $workflow["id"]; ?>')" href="javascript:;">
                                                    <?php echo $this->lang->line("edit_workflow"); ?>
                                                </a>
                                                <a class="dropdown-item" onclick="confirmationDialog('confirm_delete_record', {resultHandler: deleteWorkflow, parm: '<?php echo $workflow["id"]; ?>'})" href="javascript:;">
                                                    <?php echo $this->lang->line("delete_workflow"); ?>
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                <?php endif; ?>
                            </div>

                            <div class="clearfix">&nbsp;</div>

                            <?php if (!empty($records[$workflow["id"]]["statuses"])): ?>
                                <div class="col-md-10 p-0 form-group row">
                                    <a onclick="workflowStatusForm('<?php echo $workflow["id"]; ?>');" href="javascript:;">
                                        <?php echo $this->lang->line("add_new_workflow_status"); ?>
                                    </a>
                                </div>

                                <div class="col-md-12 p-0">
                                    <table class="table table-bordered table-striped table-hover">
                                        <tr>
                                            <th width="5%">&nbsp;</th>
                                            <th><?php echo $this->lang->line("status"); ?></th>
                                            <th><?php echo $this->lang->line("type"); ?></th>
                                            <th><?php echo $this->lang->line("transitions"); ?></th>
                                        </tr>

                                        <?php foreach ($records[$workflow["id"]]["statuses"] as $status):
                                            $is_global = $status["is_global"] == 1 ? true : false;
                                            ?>
                                            <tr id="workflow-<?php echo $workflow["id"] . "-status-" . $status["id"]; ?>">
                                                <td>
                                                    <?php if ($status["start_point"] == 0 || $status["approval_start_point"] == 0): ?>
                                                        <div class="dropdown more float-right">
                                                            <button class="btn" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                                <i class="spr spr-gear"></i>
                                                            </button>
                                                            <div class="dropdown-menu dropdown-menu-right" aria-labelledby="dLabel">
                                                                <?php if ($status["approval_start_point"] == 0): ?>
                                                                    <a class="dropdown-item" href="javascript:;" onclick="setAsApprovalStartPoint('<?php echo $workflow["id"]; ?>', '<?php echo $status["id"]; ?>');">
                                                                        <?php echo $this->lang->line("set_as_approval_start_point"); ?>
                                                                    </a>
                                                                <?php endif; ?>

                                                                <?php if ($status["start_point"] == 0): ?>
                                                                    <a class="dropdown-item" href="javascript:;" onclick="setAsStartPoint('<?php echo $workflow["id"]; ?>', '<?php echo $status["id"]; ?>');">
                                                                        <?php echo $this->lang->line("set_as_start_point"); ?>
                                                                    </a>
                                                                    <a class="dropdown-item" href="javascript:;" onclick="confirmationDialog('confirmation_delete_selected_record', {resultHandler: deleteWorkflowStatus, parm: '<?php echo $status["id"]; ?>'})">
                                                                        <?php echo $this->lang->line("delete"); ?>
                                                                    </a>
                                                                <?php endif; ?>
                                                            </div>
                                                        </div>
                                                    <?php endif; ?>
                                                </td>
                                                <td>
                                                    <?php
                                                    echo $status["name"] .
                                                        (0 < $status["start_point"] ? "&nbsp;(" . $this->lang->line("start_point") . ")" : "") .
                                                        (0 < $status["approval_start_point"] ? "&nbsp;(" . $this->lang->line("approval_start_point") . ")" : "");
                                                    ?>&nbsp;
                                                </td>
                                                <td>
                                                    <?php echo $is_global ? $this->lang->line("global_status") : $this->lang->line("transitional_status"); ?>
                                                </td>
                                                <td>
                                                    <?php if (!$is_global): ?>
                                                        <div class="col-md-12 p-0 margin-bottom">
                                                            <a href="<?php echo site_url("contract_workflows/add_transition/" . $workflow["id"] . "/" . $status["id"]); ?>" title="<?php echo $this->lang->line("add_transition"); ?>">
                                                                <i class="fa-solid fa-circle-plus"></i>
                                                            </a>&nbsp;
                                                            <a href="javascript:;" onclick="statusTransitionsViewForm('<?php echo $status["id"]; ?>', '<?php echo $workflow["id"]; ?>');" title="<?php echo $this->lang->line("view_transitions"); ?>">
                                                                <i class="fa-solid fa-list"></i>
                                                            </a>
                                                        </div>
                                                    <?php endif; ?>

                                                    <?php if (!empty($records[$workflow["id"]]["transitions"])):
                                                        foreach ($records[$workflow["id"]]["transitions"] as $transitions):
                                                            if ($transitions["from_step"] === $status["id"]):
                                                                ?>
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
                                                            <?php endif;
                                                        endforeach;
                                                    endif; ?>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </table>
                                </div>
                            <?php else: ?>
                                <div class='col-md-10 p-0 center'>
                                    <?php echo $this->lang->line("no_workflow_statuses"); ?>
                                    <a onclick="workflowStatusForm('<?php echo $workflow["id"]; ?>');" href="javascript:;">
                                        <?php echo $this->lang->line("click_to_add"); ?>
                                    </a>
                                </div>
                            <?php endif; ?>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>

<script>
    /**
     * Initialize workflow management functionality
     */
    $(document).ready(function() {
        // Initialize tabs if needed
        if ($('#tabs').length) {
            $('#tabs').tabs();
        }
    });

    /**
     * Open workflow form
     * @param {string|null} workflowId - ID of workflow to edit (null for new workflow)
     */
    function workflowForm(workflowId = null) {
        const url = workflowId ?
            '<?php echo site_url("contract_workflows/edit"); ?>/' + workflowId :
            '<?php echo site_url("contract_workflows/add"); ?>';

        // Using Kendo UI window for modal
        const workflowWindow = $("<div/>").kendoWindow({
            title: workflowId ? '<?php echo $this->lang->line("edit_workflow"); ?>' : '<?php echo $this->lang->line("add_workflow"); ?>',
            content: url,
            modal: true,
            width: "800px",
            height: "auto",
            visible: false,
            actions: ["Close"],
            close: function() {
                this.destroy();
            }
        }).data("kendoWindow");

        workflowWindow.center().open();
    }

    /**
     * Open workflow status form
     * @param {string} workflowId - ID of parent workflow
     */
    function workflowStatusForm(workflowId) {
        const url = '<?php echo site_url("contract_workflows/add_status"); ?>/' + workflowId;

        const statusWindow = $("<div/>").kendoWindow({
            title: '<?php echo $this->lang->line("add_workflow_status"); ?>',
            content: url,
            modal: true,
            width: "600px",
            height: "auto",
            visible: false,
            actions: ["Close"],
            close: function() {
                this.destroy();
            }
        }).data("kendoWindow");

        statusWindow.center().open();
    }

    /**
     * View status transitions
     * @param {string} statusId - ID of status
     * @param {string} workflowId - ID of parent workflow
     */
    function statusTransitionsViewForm(statusId, workflowId) {
        const url = '<?php echo site_url("contract_workflows/view_transitions"); ?>/' + statusId + '/' + workflowId;

        const transitionsWindow = $("<div/>").kendoWindow({
            title: '<?php echo $this->lang->line("view_transitions"); ?>',
            content: url,
            modal: true,
            width: "800px",
            height: "600px",
            visible: false,
            actions: ["Close"],
            close: function() {
                this.destroy();
            }
        }).data("kendoWindow");

        transitionsWindow.center().open();
    }

    /**
     * Set status as start point
     * @param {string} workflowId
     * @param {string} statusId
     */
    function setAsStartPoint(workflowId, statusId) {
        $.post('<?php echo site_url("contract_workflows/set_start_point"); ?>', {
            workflow_id: workflowId,
            status_id: statusId
        }, function(response) {
            if (response.success) {
                location.reload();
            } else {
                alert(response.message || '<?php echo $this->lang->line("error_occurred"); ?>');
            }
        }, 'json');
    }

    /**
     * Set status as approval start point
     * @param {string} workflowId
     * @param {string} statusId
     */
    function setAsApprovalStartPoint(workflowId, statusId) {
        $.post('<?php echo site_url("contract_workflows/set_approval_start_point"); ?>', {
            workflow_id: workflowId,
            status_id: statusId
        }, function(response) {
            if (response.success) {
                location.reload();
            } else {
                alert(response.message || '<?php echo $this->lang->line("error_occurred"); ?>');
            }
        }, 'json');
    }

    /**
     * Delete workflow status
     * @param {string} statusId
     */
    function deleteWorkflowStatus(statusId) {
        $.post('<?php echo site_url("contract_workflows/delete_status"); ?>', {
            id: statusId
        }, function(response) {
            if (response.success) {
                $('#workflow-status-' + statusId).remove();
                location.reload();
            } else {
                alert(response.message || '<?php echo $this->lang->line("error_occurred"); ?>');
            }
        }, 'json');
    }

    /**
     * Delete workflow
     * @param {string} workflowId
     */
    function deleteWorkflow(workflowId) {
        $.post('<?php echo site_url("contract_workflows/delete"); ?>', {
            id: workflowId
        }, function(response) {
            if (response.success) {
                location.reload();
            } else {
                alert(response.message || '<?php echo $this->lang->line("error_occurred"); ?>');
            }
        }, 'json');
    }

    /**
     * Delete transition
     * @param {string} transitionId
     */
    function deleteTransition(transitionId) {
        $.post('<?php echo site_url("contract_workflows/delete_transition"); ?>', {
            id: transitionId
        }, function(response) {
            if (response.success) {
                $('#transition-' + transitionId).remove();
            } else {
                alert(response.message || '<?php echo $this->lang->line("error_occurred"); ?>');
            }
        }, 'json');
    }
</script>