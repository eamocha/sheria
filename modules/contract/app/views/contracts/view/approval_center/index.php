<div class="row no-margin col-md-12" id="approval-center-tab">
    <?php if (isset($approval_center)) { ?>
        <div class="row no-margin col-md-12">
            <div class="table no-border">
                <div class="text-align-left">
                    <h4 class="no-margin">
                        <b><?php echo $this->lang->line("approval_criteria"); ?>:</b>
                        <?php if ($overall_status == "drafting") { ?>
                            <span title="<?php echo $this->lang->line("approval_status_should_be_awaiting_approval_in_order_to_approve") . ($contract["approval_start_point"] ? ", " . sprintf($this->lang->line("click_approval_start_point"), $contract["approval_start_point"]) : ", " . sprintf($this->lang->line("set_approval_start_point_then_click_it"), base_url() . "contract_workflows/index" . (isset($contract["workflow_id"]) ? "#" . $contract["workflow_id"] : ""))); ?>" class="tooltip-title">
                                <i class="fas fa-question-circle"></i>
                            </span>
                        <?php } ?>
                    </h4>
                    <h4 class="mt-4">
                        <span><?php echo $this->lang->line("approval_status"); ?>:</span>
                        <span id="approval-overall-status" class="approval-status status-<?php echo $overall_status; ?>">
                            <?php echo ucfirst($this->lang->line($overall_status)); ?>
                        </span>
                    </h4>
                    <hr>
                    <button type="button" class="btn btn-info" <?php echo $overall_status === "approved" ? "disabled = \"disabled\"" : ""; ?> onclick="contractApproverForm('<?php echo $contract["id"]; ?>')">
                        <i class="fa fa-plus" aria-hidden="true"></i>&nbsp;<?php echo $this->lang->line("add_approvers"); ?>
                    </button>
                    <a class="btn btn-light d-none" href="<?php echo site_url("export/contract_approval_sheet/" . $contract["id"]); ?>">
                        <?php echo $this->lang->line("export_contract_approval_sheet"); ?>
                    </a>
                </div>
                <div class="row">
                    <?php if ($approval_center) { ?>
                        <?php $this->load->view("contracts/view/approval_center/approvers_body", ["module" => "contract"]); ?>
                    <?php } ?>
                </div>
            </div>
            <div class="row margin-top-15 col-md-12 p-0">
                <?php if ($approval_history) { ?>
                    <?php $this->load->view("contracts/view/approval_center/history", ["module" => "contract"]); ?>
                <?php } ?>
            </div>
        </div>
    <?php } else { ?>
        <div class="row no-margin col-md-12 no-padding">
            <h5><?php echo $this->lang->line("no_criteria"); ?></h5>&nbsp;
        </div>
        <div class="row no-margin col-md-12 no-padding">
            <button type="button" class="btn btn-light mr-2 ml-2" onclick="contractApproverForm('<?php echo $contract["id"]; ?>')">
                <?php echo $this->lang->line("add_approvers"); ?>
            </button>
            <?php if (isset($enable_approve_all) && $enable_approve_all) { ?>
                <button type="button" class="btn btn-light" onclick="confirmationDialog('confirmation_approve_contract', {resultHandler:contractApproveAll, parm: {'contract_id': '<?php echo $contract["id"]; ?>'}});">
                    <?php echo $this->lang->line("approve_all"); ?>
                </button>
            <?php } ?>
        </div>
    <?php } ?>
</div>

<?php
$this->load->view("documents_management_system/document_editor_modal", []);
$this->load->view("documents_management_system/document_editor_installation_modal", []);
?>