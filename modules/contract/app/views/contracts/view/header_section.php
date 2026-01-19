<?php $cont_id=$contract["id"]?>
<div class="row no-margin col-md-12 header-contract" id="header-section">
        <div class="col-md-5 col-sm-12 col-xs-12">
            <h4>
                <a href="modules/contract/contracts/view/<?php echo $cont_id;?>"><?php echo $model_code . $cont_id; ?></a> -
                <span title="<?php echo $contract["name"];?>">
                    <?php echo 117 < strlen($contract["name"]) ? mb_substr($contract["name"], 0, 117) . "..." : $contract["name"];?></span>
                &nbsp;&nbsp;
                <span class="archived-flag"><?php echo $contract["archived"] == "yes" ? "(" . $this->lang->line("archived") . ")" : "";?></span>
                &nbsp;&nbsp;
                <span <?php  echo $visible_to_cp ? " class=\"circle green cp-status\" title=\"" . $this->lang->line("visibleFromCP") . "\"" : " class=\"circle gray cp-status\" title=\"" . $this->lang->line("invisibleFromCP") . "\""; ?>></span>

            </h4>
            <?php echo form_input(["id" => "is-edit-mode", "value" => isset($is_edit_mode) ? $is_edit_mode : 0, "type" => "hidden"]);?>
            <?php echo form_input(["id" => "contract-id", "value" => $cont_id, "type" => "hidden"]);?>
        </div>
        <div class="col-md-7 col-sm-12 col-xs-12 action-buttons">
            <div class="btn-group float-right">
                <a href="#" class="link-actions" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><img src="assets/images/setting_icon.png"> </a>
                <div class="dropdown-menu dropdown-menu-right" id="header-actions-section">
                    <?php $this->load->view("contracts/view/header/actions_section");?>
                </div>
            </div>
            <ul>
                <li class="edit-btn" id="edit-btn">
                    <a id="edit-btn-link" href="<?php echo site_url("contracts/view/" . $cont_id);?>" onclick="contractEditForm('<?php echo $cont_id;?>', event);" title="<?php echo $this->lang->line("edit");?>">
                        <img src="assets/images/contract/edit.svg"></a>
                </li>
              <?php if(isset($enableContractRenewalFeature)&& $enableContractRenewalFeature==1)
              {?>
                <li class="edit-btn">
                    <a onclick="contractRenewForm('<?php echo $cont_id;?>');"  title="<?php echo $this->lang->line("renew");?>"> <img  src="assets/images/contract/renew.svg"> </a>
                </li>
             <?php  } ?>
                <li class="edit-btn">
                    <a onclick="contractAmendmentForm('<?php echo $cont_id;?>', event);" title="<?php echo $this->lang->line("create_amendment");?>">
                        <img src="assets/images/contract/amend.svg"> </a>
                </li>
            </ul>
            <ul id="statuses-section">
                <?php $this->load->view("contracts/view/header/statuses_section");?>
            </ul>
            <ul class="">
<!--                <span class="col-md-4" id="contract-status-label" ?>--><?php //echo $this->lang->line($contract["status"]);?><!--</span>-->
                <?php echo form_dropdown('contract_status', $statusValues??"", $defaultStatusValue??"Active", ['id' => 'contract-status', 'class' => 'form-control select-picker status-dropdown ', 'onchange' => "activateDeactivate(this, '{$cont_id}');", 'data-style' => 'btn-primary', 'data-width' => 'fit']); ?>
                <label for="contract-status" class="label-primary"></label>
            </ul>
        </div>
</div>