<div class="row col-md-12">
    <div class="col-md-12 no-margin" id="milestone-container">
        <div class="row m-0 col-md-12 no-margin no-padding mt-10 mb-10 milestone-container" id="milestones-<?php echo $contract["id"]; ?>-inside">
            <?php
            echo form_input(["id" => "contract-id", "value" => $contract["id"], "data-field" => "contract_id", "type" => "hidden"]);
            echo form_input(["id" => "contract-value", "value" => $contract["value"], "data-field" => "value", "type" => "hidden"]);
            echo form_input(["id" => "contract-currency", "value" => $contract["currency_id"], "data-field" => "currency_id", "type" => "hidden"]);
            ?>
            <div class="flex-stages no-padding cursor-pointer-click">
                <label class="font-700 big-text-font-size mt-10 <?php echo (!empty($milestones) ? "" : "d-none"); ?>">
                    <?php echo $this->lang->line("milestones"); ?>
                </label>
                <?php if ($visible_to_cp): ?>
                    <div class="mx-2 col-6 btn-no-border">
                        <input id="toggle-one" type="checkbox" <?php echo (($milestone_visible_to_cp == 0) ? "checked" : ""); ?> data-toggle="toggle"
                               data-on="<img width='25px' src='assets/images/contract/cp_icon.svg'> <?php echo $this->lang->line("show_in_cp"); ?>"
                               data-off="<img width='25px' src='assets/images/contract/cp_icon.svg'> <?php echo $this->lang->line("hide_in_cp"); ?>"
                               data-style="ios">
                    </div>
                <?php endif; ?>
            </div>
            <div class="flex-end-item flex-stages no-padding prl-5 bg-eee-click action-add-btn">
                <div class="ml-5 cursor-pointer-click disable-anchor" onclick="contractMilestoneForm('<?php echo $contract["id"]; ?>')">
                    <span class="close-open-icon center-round-icon">
                        <i class="fa fa fa-plus font-16"></i>
                    </span>
                    <span class="back-title-color"><?php echo $this->lang->line("add_milestone"); ?></span>
                </div>
            </div>
        </div>
        <div class="control-section <?php echo (!empty($milestones) ? "" : "d-none"); ?>">
            <div class="content-wrapper">
                <div id="milestones-gantt-container" class="col-md-12 no-padding">
                </div>
            </div>
        </div>
        <div id="milestones-events-details-container" class="col-md-12 no-padding">
            <div class="col-md-12 no-padding">
                <div class="flex-stages no-padding cursor-pointer-click">
                    <label class="font-700 big-text-font-size mt-10"><?php echo $this->lang->line("milestones"); ?></label>
                </div>
                <div>
                    <div class="col-md-12 no-padding">
                        <?php if (!empty($milestones)): ?>
                            <?php foreach ($milestones as $key => $milestone): ?>
                                <div class="col-md-12 flex-row mb-10 no-padding list-card-container" id="milestone-container-<?php echo $milestone["id"]; ?>">
                                    <div class="<?php echo $milestone["status"]; ?>-card-border flex-stretch center-all border-radius-5px-ar" id="card-border-<?php echo $milestone["id"]; ?>">
                                        <span style="color: white" id='list-key-<?php echo $milestone["id"]; ?>'><?php echo ($key + 1); ?></span>
                                    </div>
                                    <div class="list-card milestone-list-card flex-stretch width--36px">
                                        <div class="row mx-0 px-0 pb-3" onclick="collapseDownUp('milestone-sub-details-<?php echo $milestone["id"]; ?>','milestone-header-<?php echo $milestone["id"]; ?>', false)">
                                            <div class="col-md-6 no-padding d-flex justify-content-start">
                                                <span class="bold-title font-18 grey_91 cursor-pointer-click bg-eee-click pull-right-arabic trim-width-80per pl-0 col-md-12">
                                                   <span class="pull-right-arabic" title="<?php echo $milestone["title"]; ?>"> <?php echo $milestone["title"]; ?></span>
                                                </span>
                                            </div>
                                            <div class="col-md-6 col-xs-12 no-padding pull-right-arabic d-flex justify-content-end milestone-side-actions">
                                                <div class="col-md-4 col-xs-12 pr-0 mr-0 progress-status-container" id='status-container-<?php echo $milestone["id"]; ?>' current-status='<?php echo $milestone["status"]; ?>'>
                                                    <select class="form-control select-picker progress-status" data-show-content="true" id='milestone-status-<?php echo $milestone["id"]; ?>' onchange="changeMilestoneStatus(this, <?php echo $milestone["id"]; ?>)" style="border-radius: 40px;">
                                                        <?php foreach ($progress_statuses as $status): ?>
                                                            <option value='<?php echo $status["value"]; ?>' <?php echo (($milestone["status"] == $status["value"]) ? "selected" : ""); ?> data-content="<?php echo $status["data-content"]; ?>"></option>
                                                        <?php endforeach; ?>
                                                    </select>
                                                </div>
                                                <div class="col-md-5 col-xs-12">
                                                    <select class="form-control select-picker financial-status" data-show-content="true" id="financial-status-<?php echo $milestone["id"]; ?>" onchange="changeFinancialStatus(this, '<?php echo $milestone["id"]; ?>')">
                                                        <?php foreach ($financial_statuses as $status): ?>
                                                            <option value='<?php echo $status["value"]; ?>' <?php echo (($milestone["financial_status"] == $status["value"]) ? "selected" : ""); ?> data-content="<?php echo $status["data-content"]; ?>"></option>
                                                        <?php endforeach; ?>
                                                    </select>
                                                </div>
                                                <ul class="col-md-1 p-0 m-0 pl-1 col-xs-12">
                                                    <div class="purple_color flex-stages pull-right no-margin-important no-padding font-15">
                                                        <div class="dropdown more">
                                                            <a class="btn btn-default btn-xs no-outline no-border no-padding font-18" data-toggle="dropdown" href="">
                                                                <i class="purple_color icon-alignment fa fa-ellipsis-v cursor-pointer-click"></i>
                                                            </a>
                                                            <div class="dropdown-menu dropdown-menu-right pull-right" role="menu" aria-labelledby="dLabel">
                                                                <a class="dropdown-item bg-eee-click" href="javascript:;" onclick="contractMilestoneForm('<?php echo $contract["id"]; ?>', '<?php echo $milestone["id"]; ?>')"><?php echo $this->lang->line("view_edit"); ?></a>
                                                                <a class="dropdown-item bg-eee-click" href="javascript:;" onclick="confirmationDialog('confirm_delete_record', {resultHandler: deleteMilestone, parm: {'milestoneId' :'<?php echo $milestone["id"]; ?>', 'contractId' :'<?php echo $contract["id"]; ?>'}})">
                                                                    <?php echo $this->lang->line("delete"); ?>
                                                                </a>
                                                            </div>
                                                        </div>
                                                        <a href="javascript:;" class="ml-3"> <i id="milestone-header-<?php echo $milestone["id"]; ?>" class="fa fa-angle-right icon font-18 purple_color">&nbsp;</i></a>
                                                    </div>
                                                </ul>
                                            </div>
                                        </div>

                                        <div class="milestone-sub-details display-none" id="milestone-sub-details-<?php echo $milestone["id"]; ?>">
                                            <div class="row mx-0 px-0 mb-3 pb-1">
                                                <div class="col-md-6 no-padding <?php echo (($milestone["amount"] == null) ? (($milestone["percentage"] == null) ? "" : "d-none") : ""); ?>" id="amount-container">
                                                    <i class="pull-right-arabic icon fa-solid fa-solid fa-money-bill fa-lg"></i>
                                                    <span class="grey-color mrl-5 label-activities"><?php echo $this->lang->line("amount") . ":"; ?></span>
                                                    <span class="label-value-grey trim-width-60per v-al-n-5 tooltip-title" title="<?php echo (double)$milestone["amount"]; ?>"><?php echo (double)$milestone["amount"] . " " . $milestone["currency"]; ?></span>
                                                </div>
                                                <div class="col-md-6 no-padding <?php echo (($milestone["percentage"] == "") ? "d-none" : ""); ?>" id="percentage-container">
                                                    <i class="pull-right-arabic icon fa-solid fa-solid fa-percent fa-lg"></i>
                                                    <span class="grey-color mrl-5 label-activities"><?php echo $this->lang->line("percentage") . ":"; ?></span>
                                                    <span class="label-value-grey trim-width-60per v-al-n-5 tooltip-title" title="<?php echo (double)$milestone["percentage"]; ?>"><?php echo (double)$milestone["percentage"]; ?> %</span>
                                                </div>
                                            </div>
                                            <div class="row mx-0 px-0 mb-3 pb-1">
                                                <div class="col-md-6 no-padding">
                                                    <i class="pull-right-arabic icon fa-solid fa-calendar-day fa-lg"></i>
                                                    <span class="grey-color mrl-5 label-activities"><?php echo $this->lang->line("start_date") . ":"; ?></span>
                                                    <span class="label-value-grey trim-width-60per v-al-n-5 tooltip-title" title="<?php echo ($milestone["start_date"] ? date_format(date_create($milestone["start_date"]), "d-m-Y") : ""); ?>"><?php echo ($milestone["start_date"] ? date_format(date_create($milestone["start_date"]), "d-m-Y") : $this->lang->line("none")); ?></span>
                                                </div>
                                                <div class="col-md-6 no-padding">
                                                    <i class="pull-right-arabic icon fa-solid fa-calendar-check fa-lg"></i>
                                                    <span class="grey-color mrl-5 label-activities"><?php echo $this->lang->line("due_date") . ":"; ?></span>
                                                    <span class="label-value-grey trim-width-60per v-al-n-5 tooltip-title" title="<?php echo ($milestone["due_date"] ? date_format(date_create($milestone["due_date"]), "d-m-Y") : ""); ?>"><?php echo ($milestone["due_date"] ? date_format(date_create($milestone["due_date"]), "d-m-Y") : $this->lang->line("none")); ?></span>
                                                </div>
                                            </div>
                                            <div class="row mx-0 px-0 mb-3 pb-1">
                                                <div class="pull-right-arabic icon col-md-6 no-padding">
                                                    <i class="pull-right-arabic icon fa-solid fa-solid fa-folder-plus fa-lg"></i>
                                                    <span class="grey-color mrl-5 label-activities"><?php echo $this->lang->line("attachments") . ":"; ?></span>
                                                    <?php if ((0 < $milestone["milestone_docs_count"])): ?>
                                                        <span class="cursor-pointer-click" id="document-item-count-<?php echo $milestone["id"]; ?>" data-count="<?php echo $milestone["milestone_docs_count"]; ?>" onclick="documentsDialog('<?php echo $milestone["id"]; ?>','<?php echo $contract["id"]; ?>')">
                                                            <a><?php echo $milestone["milestone_docs_count"]; ?> <?php echo $this->lang->line("one_or_more_documents"); ?></a>
                                                        </span>
                                                    <?php else: ?>
                                                        <span class="label-value-grey">
                                                            <?php echo $milestone["milestone_docs_count"]; ?> <?php echo $this->lang->line("one_or_more_documents"); ?>
                                                        </span>
                                                    <?php endif; ?>
                                                </div>
                                            </div>
                                            <div class="row mx-0 px-0 mb-3 pb-1">
                                                <div class="col-md-6 no-padding">
                                                    <i class="pull-right-arabic icon fa-solid fa-list-check fa-lg"></i>
                                                    <span class="grey-color mrl-5 label-activities"><?php echo $this->lang->line("deliverables") . ":"; ?></span>
                                                    <span class="label-value-grey tooltip-title v-al-n-5 trim-width-60per" title="<?php echo ($milestone["deliverables"] ? $milestone["deliverables"] : ""); ?>">
                                                        <?php echo ($milestone["deliverables"] ? $milestone["deliverables"] : $this->lang->line("none")); ?>
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <p class="font-700 big-text-font-size grey-color text-center"><?php echo sprintf($this->lang->line("no_related_record_found"), $this->lang->line("milestones")); ?></p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
    jQuery(function() {
        jQuery('#toggle-one').bootstrapToggle({
            width: '200px',
            height: '34px'
        });
    })
    jQuery("#toggle-one").change(function(){
        if(jQuery(this).prop("checked") == true){
            showHideMilestoneCp('<?php echo $contract["id"]; ?>' , 0);
        }else{
            showHideMilestoneCp('<?php echo $contract["id"]; ?>' , 1);
        }
    });
</script>