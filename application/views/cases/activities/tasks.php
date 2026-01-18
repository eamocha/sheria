<div class="row m-0 col-md-12 no-margin mt-10 mb-10 no-padding case-tasks-container" id="tasks-<?php echo $stage_id; ?>-inside">
    <div id="events-details-container-toggel" class="no-padding flex-stages mb-10 w-100">
        <div onclick="toggleElements(jQuery('#tasks-container-<?php echo $stage_id; ?>-container-icon', this), jQuery('#tasks-container-<?php echo $stage_id; ?>', '.case-tasks-container'))">
            <span class="close-open-icon">
                <i id="tasks-container-<?php echo $stage_id; ?>-container-icon" class="fa-solid fa-chevron-down font-18"></i>
            </span>
        </div>
        <div class="flex-stages no-padding cursor-pointer-click">
            <label class="font-700 big-text-font-size mt-10"><?php echo $this->lang->line("tasks"); ?></label>
        </div>
        <div class="flex-end-item flex-stages no-padding prl-5 action-add-btn">
            <div class="ml-5 cursor-pointer-click disable-anchor" onclick="taskAddForm('<?php echo $case_id; ?>', '<?php echo !empty($stage_id) ? $stage_id : false; ?>', function (){legalCaseEvents.openTaskTab('<?php echo !empty($stage_id) ? $stage_id : "null"; ?>','<?php echo $case_id; ?>', true)}, <?php echo !empty($stage_id) ? false : true; ?>);">
                <span class="close-open-icon center-round-icon">
                    <i class="fa fa fa-plus font-16"></i>
                </span>
                <span class="back-title-color"><?php echo $this->lang->line("add_new_task"); ?></span>
            </div>
        </div>
    </div>
    <div id="tasks-events-details-container" class="col-md-12 no-padding">
        <div id="all-tasks">
            <div class="col-md-12 no-padding" id="tasks-container-<?php echo $stage_id; ?>">
                <?php if (!empty($tasks)): ?>
                    <?php foreach ($tasks as $key_task => $task): ?>
                        <?php
                        if (!empty($task["due_date"])) {
                            $task_date = new DateTime($task["due_date"]);
                            $task_date = $task_date->setTime(12, 0, 0);
                            $current_date = new DateTime();
                            $current_date = $current_date->setTime(12, 0, 0);
                            $is_due_task = $current_date < $task_date;
                            $due_to_task_diff = $task_date->diff($current_date);
                            $due_to_task_diff = $due_to_task_diff->days;
                            $task_color = $is_due_task ? "back-title-color" : ($due_to_task_diff == 0 ? "green_date" : "grey_91");
                        }
                        ?>
                        <div class="col-md-12 flex-row mb-10 no-padding list-card-container">
                            <div class="orange-card-border flex-stretch center-all border-radius-5px-ar">
                                <span style="color: white"></span>
                            </div>
                            <div class="list-card task-list-card">
                                <div class="row m-0 col-md-12 no-padding">
                                    <div class="col-md-10 no-padding mb-2">
                                        <span class="bold-title font-18 <?php echo $task_color; ?> cursor-pointer-click trim-width-95-per tooltip-title bg-eee-click" title="<?php echo $task["title"]; ?>">
                                            <a target="_blank" href="<?php echo base_url() . "tasks/view/" . $task["id"]; ?>">
                                                <?php echo (int) $task["id"]; ?> - <?php echo $task["title"]; ?>
                                            </a>
                                        </span>
                                    </div>
                                    <div class="col-md-2 no-padding">
                                        <div class="purple_color pull-right flex-stages no-margin-important no-padding font-15">
                                            <div class="dropdown more">
                                                <a class="btn btn-default btn-xs no-outline no-border no-padding font-18" data-toggle="dropdown" href="">
                                                    <i class="purple_color icon-alignment fa fa-ellipsis-v cursor-pointer-click"></i>
                                                </a>
                                                <ul class="dropdown-menu dropdown-right pull-right" role="menu" aria-labelledby="dLabel">
                                                    <a class="dropdown-item bg-eee-click" href="javascript:;" onclick="taskEditForm('<?php echo $task["id"]; ?>', false, function (){legalCaseEvents.openTaskTab('<?php echo !empty($stage_id) ? $stage_id : "null"; ?>','<?php echo $case_id; ?>', true)})" >
                                                        <?php echo $this->lang->line("view_edit"); ?>
                                                    </a>
                                                    <a class="dropdown-item bg-eee-click" href="javascript:;" onclick="recordRelatedExpense('<?php echo sprintf("%08d", $case_id); ?>','task','<?php echo $task["id"]; ?>')" >
                                                        <?php echo $this->lang->line("record_expense"); ?>
                                                    </a>
                                                    <a class="dropdown-item bg-eee-click" href="javascript:;" onclick="recordRelatedExpense('<?php echo sprintf("%08d", $case_id); ?>','task','<?php echo $task["id"]; ?>', true)" >
                                                        <?php echo $this->lang->line("bulk_expenses"); ?>
                                                    </a>
                                                </ul>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="d-flex mb-10">
                                    <div class="col-md-4 no-padding">
                                        <div class="img-circle profile-bg profile-bg-17" style="background-image: url(<?php echo "users/get_profile_picture/" . $task["assignedToId"] . "/1"; ?>)"></div>
                                        <span class="grey-color mrl-5 label-activities"><?php echo $this->lang->line("assigned_to") . ":"; ?></span>
                                        <span class="label-value-grey trim-width-250 v-al-n-5 tooltip-title" title="<?php echo $task["assigned_to"]; ?>"><?php echo $task["assigned_to"]; ?></span>
                                    </div>
                                    <div class="col-md-4 no-padding">
                                        <i class="icon-alignment spr3 postponed_until pull-right-arabic"></i><span class="mrl-5 label-activities"><?php echo $this->lang->line("due_date") . ":"; ?></span><?php echo $task["due_date"]; ?>
                                    </div>
                                    <div class="col-md-4 no-padding">
                                        <i class="icon-alignment spr3 document_blue pull-right-arabic"></i><span class="mrl-5 label-activities"><?php echo $this->lang->line("documents") . ":"; ?></span>
                                        <?php if (0 < $task["tasks_docs_count"]): ?>
                                            <span class="cursor-pointer-click" id="document-item-count-<?php echo (int) $task["id"]; ?>" data-count="<?php echo $task["tasks_docs_count"]; ?>" onclick="legalCaseEvents.documentsDialog('task', '<?php echo $task["id"]; ?>')">
                                                <a><?php echo $task["tasks_docs_count"]; ?> <?php echo $this->lang->line("one_or_more_documents"); ?></a>
                                            </span>
                                        <?php else: ?>
                                            <span class="label-value-grey">
                                                <?php echo $task["tasks_docs_count"]; ?> <?php echo $this->lang->line("one_or_more_documents"); ?>
                                            </span>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                <div class="d-flex mb-10">
                                    <div class="col-md-4 no-padding">
                                        <i class="icon-alignment spr3 blue_priority pull-right-arabic"></i><span class="mrl-5 label-activities"><?php echo $this->lang->line("priority") . ":"; ?><i class="priority-<?php echo $task["priority"]; ?> vertical-align-bottom"></i><span class="label-value-grey"><?php echo ucfirst($this->lang->line($task["priority"])); ?></span>
                                    </div>
                                    <div class="col-md-4 no-padding">
                                        <i class="icon-alignment spr3 green_aknowledged pull-right-arabic"></i>
                                        <span class="mrl-5 label-activities"><?php echo $this->lang->line("task_status") . ":"; ?></span><?php echo $task["taskStatus"]; ?>
                                    </div>
                                    <div class="col-md-4 no-padding">
                                        <i class="icon-alignment fa-solid fa-tag dar_blue pull-right-arabic"></i>
                                        <span class="mrl-5 label-activities"><?php echo $this->lang->line("task_type") . ":"; ?></span><?php echo $task["type"]; ?>
                                    </div>
                                </div>
                                <div class="d-flex mb-10">
                                    <p class="mrl-20">
                                        <?php echo 150 < strlen(strip_tags($task["taskFullDescription"])) ? mb_substr(strip_tags($task["taskFullDescription"]), 0, 150) . "..." : strip_tags($task["taskFullDescription"]); ?>
                                    </p>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                    <div id="task-pagination-page-nb<?php echo !empty($stage_id) ? $stage_id : "null"; ?>-wrapper" class="pull-right no-padding pagination-box mrl-5">
                        <?php echo form_dropdown("task-pagination-page-nb-" . (!empty($stage_id) ? $stage_id : "null"), ["10" => 10, "20" => 20, "50" => 50], $page_limit, "id=\"task-pagination-page-nb-" . (!empty($stage_id) ? $stage_id : "null") . "\" class=\"form-control pagination-select\" onchange=\"legalCaseEvents.changePageLimit(this," . (!empty($stage_id) ? $stage_id : "null") . "," . $case_id . ", 'tasks')\""); ?>
                    </div>
                    <div id="tasks-pagination-box-<?php echo !empty($stage_id) ? $stage_id : "null"; ?>" class="pull-right no-padding pagination-box"></div>
                <?php else: ?>
                    <p class="font-700 big-text-font-size grey-color text-center"><?php echo sprintf($this->lang->line("no_related_record_found"), $this->lang->line("task")); ?></p>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
<script>
    jQuery('#tasks-pagination-box-<?php echo !empty($stage_id) ? $stage_id : "null"; ?>').paginationX({
        page: <?php echo $page_number; ?>,
        limit: <?php echo $page_limit; ?>,
        total: '<?php echo $total_rows; ?>',
        pageShow: 3,
        min: 1,
        max: 3,
        visible: ['start','end','last','next','number'],
        clickFun: function (page, limit, total, pageTotal, pageShow) {
            legalCaseEvents.openTaskTab('<?php echo !empty($stage_id) ? $stage_id : "null"; ?>','<?php echo $case_id; ?>', true, page, <?php echo $page_limit; ?>);
        }
    });
    var disableMatter = '<?php echo $legalCase["archived"] == "yes" && isset($systemPreferences["disableArchivedMatters"]) && $systemPreferences["disableArchivedMatters"] ? true : false; ?>';
    let actionAddBtn = jQuery('.action-add-btn');
    if('undefined' !== typeof(disableMatter) && disableMatter){
        disableAnchors(actionAddBtn);
    }
</script>