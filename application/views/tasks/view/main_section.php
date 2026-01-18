<div class="container-fluid object-display-form" id="task-display-form">
    <div class="row task" id="header-content">
        <?php echo form_input(["id" => "id", "class" => "task-id", "value" => $task_data["id"], "type" => "hidden"]); ?>
        <div class="col-md-12">
            <div class="col-md-8 no-padding">
                <span id="title-description"><h4 class="task-subject"><bdi><?php 
                    echo $task_data["model_code"] . $task_data["id"] . ": " . $task_data["title"];
                    echo $this->session->userdata("AUTH_language") == "arabic" ? "&lrm;" : "&rlm;";
                ?></bdi></h4></span>
            </div>
        </div>
        <div class="col-md-12">
            <button type="button" class="btn btn-default navbar-btn action-bar-btn" onclick="taskEditForm('<?php echo $task_data["id"]; ?>', event);">
                <?php echo $this->lang->line("edit"); ?>&nbsp;&nbsp;
                <i class="fa fa-pencil"></i>
            </button>
            <button type="button" class="btn btn-default navbar-btn action-bar-btn" onclick="taskCommentFormInline('<?php echo $task_data["id"]; ?>');">
                <?php echo $this->lang->line("comment"); ?>&nbsp;&nbsp;
                <i class="fa fa-comment"></i>
            </button>
            
            <?php 
            $first_statuses = array_slice($available_statuses, 0, 3, true);
            $other_statuses = array_slice($available_statuses, 3, NULL, true);
            
            foreach ($first_statuses as $status_id => $status_name) {
                $step = isset($status_transitions[$status_id]) ? $status_transitions[$status_id] : false;
                $step_name = isset($step["name"])&& $step["name"] ? $step["name"] : $status_name;
                
                if (isset($step["id"]) && $step["id"]) { ?>
                    <a href="javascript:;" onclick="screenTransitionForm('<?php echo $task_data["id"]; ?>', '<?php echo $step["id"]; ?>', 'tasks');" title="<?php echo isset($step["comments"]) && $step["comments"] !== "" ? $step["comments"] : $step_name; ?>" class="case-move-status-link btn btn-default">
                        <?php echo $step_name; ?>
                    </a>&nbsp;
                <?php } else { ?>
                    <a href="<?php echo site_url("tasks/move_status/" . $task_data["id"] . "/" . $status_id); ?>" title="<?php echo isset($step["comments"]) && $step["comments"] !== "" ? $step["comments"] : $step_name; ?>" class="case-move-status-link btn btn-default submit-with-loader">
                        <?php echo $step_name; ?>
                    </a>&nbsp;
                <?php }
            } ?>
            
            <div class="caseStatusesVisibleList">
                <div class="dropdown">
                    <?php if (!empty($other_statuses)) { ?>
                        <button class="btn btn-default dropdown-toggle" type="button" id="dropdownMenuMoreStatuses" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">
                            <?php echo $this->lang->line("more"); ?>
                            <span class="caret"></span>
                        </button>
                        <div class="dropdown-menu dropdownMenuMoreStatusesList" aria-labelledby="dropdownMenuMoreStatuses">
                            <?php foreach ($other_statuses as $status_id => $status_name) {
                                $step = isset($status_transitions[$status_id]) ? $status_transitions[$status_id] : false;
                                $step_name = $step["name"] ? $step["name"] : $status_name;
                                
                                if (isset($step["id"]) && $step["id"]) { ?>
                                    <a class="dropdown-item" href="javascript:;" onclick="screenTransitionForm('<?php echo $task_data["id"]; ?>', '<?php echo $step["id"]; ?>', 'tasks');" title="<?php echo isset($step["comments"]) && $step["comments"] !== "" ? $step["comments"] : $step_name; ?>">
                                        <?php echo $step_name; ?>
                                    </a>
                                <?php } else { ?>
                                    <a class="dropdown-item" href="<?php echo site_url("tasks/move_status/" . $task_data["id"] . "/" . $status_id); ?>" title="<?php echo isset($step["comments"]) && $step["comments"] !== "" ? $step["comments"] : $step_name; ?>" class="submit-with-loader">
                                        <?php echo $step_name; ?>
                                    </a>
                                <?php }
                            } ?>
                        </div>
                    <?php } ?>
                </div>
            </div>
            
            <div class="task-export float-dir">
                <div>
                    <a href="#" class="link-actions" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><img src="assets/images/contract/export.svg"></a>
                    <div class="dropdown-menu dropdown-actions" id="header-actions-section">
                        <a class="dropdown-item" href="<?php echo site_url("tasks/export_task_to_word/" . $task_data["id"]); ?>" title="<?php echo $this->lang->line("export_to_word"); ?>">
                            <?php echo $this->lang->line("export_to_word"); ?>
                        </a>
                        <a class="dropdown-item" href="<?php echo site_url("tasks/export_task_to_word_for_clients/" . $task_data["id"]); ?>" title="<?php echo $this->lang->line("export_to_word_for_clients"); ?>">
                            <?php echo $this->lang->line("export_to_word_for_clients"); ?>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="d-flex mt-20" id="body-content">
        <div class="col-md-8 col-xs-8" id="main-section">
            <div id="details-module" class="module toggle-wrap">
                <div id="details-module-heading" class="d-flex" onclick="collapse('details-module-heading', 'details-module-body');">
                    <a href="javascript:;" class="toggle-title p-1">
                        <i class="fa fa-angle-down black_color font-18">&nbsp;</i>
                    </a>
                    <h4 class="toggle-title px-2"><?php echo $this->lang->line("public_info"); ?></h4>
                </div>
                <div class="mod-content" id="details-module-body">
                    <div id="details">
                        <ul class="property-list two-cols">
                            <li class="item">
                                <div class="wrap">
                                    <span class="name"><?php echo $this->lang->line("type"); ?>:</span>
                                    <span id="type-val"><?php echo $task_data["type"]; ?></span>
                                </div>
                            </li>
                            <li class="item">
                                <div class="wrap">
                                    <span class="name"><?php echo $this->lang->line("title"); ?>:</span>
                                    <span id="title-val"><?php echo $task_data["title"]; ?></span>
                                </div>
                            </li>
                            <li class="item item-right">
                                <div class="wrap">
                                    <span class="name"><?php echo $this->lang->line("workflow_status"); ?>:</span>
                                    <span id="status-val" class="value">
                                        <span class="max-width-medium"><?php echo $task_data["status"]; ?></span>
                                    </span>
                                </div>
                            </li>
                            <li class="item">
                                <div class="wrap">
                                    <span class="name"><?php echo $this->lang->line("priority"); ?>:</span>
                                    <span class="priority-<?php echo $task_data["priority"]; ?>"></span>
                                    <span id="type-val"><?php echo $this->lang->line($task_data["priority"]); ?></span>
                                </div>
                            </li>
                            <li class="item item-right">
                                <div class="wrap">
                                    <span class="name"><?php echo $this->lang->line("location"); ?>:</span>
                                    <span id="status-val" class="value">
                                        <span class="max-width-medium"><?php echo $task_data["location"] ?? $this->lang->line("none"); ?></span>
                                    </span>
                                </div>
                            </li>
                        </ul>
                        
                        <ul class="property-list two-cols">
                            <?php if ($this->license_package == "core" || $this->license_package == "core_contract") { ?>
                                <li class="item">
                                    <div class="wrap">
                                        <span class="name"><?php echo $this->lang->line("related_case"); ?>:</span>
                                        <span id="status-val" class="value">
                                            <span class="max-width-medium">
                                                <?php if (isset($task_data["caseSubject"]) && $task_data["caseSubject"]) { ?>
                                                    <a href="<?php echo site_url(($task_data["caseCategory"] == "IP" ? "intellectual_properties" : "cases") . "/edit/" . $task_data["legal_case_id"]); ?>" target="_blank">
                                                        <?php echo $task_data["case_model_code"] . $task_data["legal_case_id"] . " - " . $task_data["caseSubject"]; ?>
                                                    </a>
                                                <?php } else { 
                                                    echo $this->lang->line("none");
                                                } ?>
                                            </span>
                                        </span>
                                    </div>
                                </li>
                            <?php } ?>
                            
                            <?php if ($this->license_package == "contract" || $this->license_package == "core_contract") { ?>
                                <li class="item">
                                    <div class="wrap">
                                        <span class="name"><?php echo $this->lang->line("related_contract"); ?>:</span>
                                        <span id="status-val" class="value">
                                            <span class="max-width-medium">
                                                <?php if (isset($task_data["contract_name"]) && $task_data["contract_name"]) { ?>
                                                    <a href="<?php echo site_url("modules/contract/contracts/view/" . $task_data["contract_id"]); ?>" target="_blank">
                                                        <?php echo $task_data["contract_model_code"] . $task_data["contract_id"] . " - " . $task_data["contract_name"]; ?>
                                                    </a>
                                                <?php } else { 
                                                    echo $this->lang->line("none");
                                                } ?>
                                            </span>
                                        </span>
                                    </div>
                                </li>
                            <?php } ?>
                        </ul>
                    </div>
                    
                    <div id="custom-fields-details-module" class="module">
                        <?php $this->load->view("tasks/view/custom_fields/details_section"); ?>
                    </div>
                </div>
            </div>
            
            <div id="description-module" class="module">
                <?php $this->load->view("tasks/view/description_section"); ?>
            </div>
            
            <div id="attachments-module" class="module">
                <?php $this->load->view("tasks/view/attachments_section"); ?>
            </div>
            
            <?php if (isset($task_data["caseSubject"]) && $task_data["caseSubject"]) { ?>
                <div id="matter-attachments-module" class="module">
                    <?php $this->load->view("tasks/view/matter_attachments_section"); ?>
                </div>
            <?php } ?>
            
            <div id="activity-module" class="module">
                <?php $this->load->view("tasks/view/activity_section"); ?>
            </div>
            
            <button type="button" class="btn btn-default navbar-btn" onclick="taskCommentFormInline('<?php echo $task_data["id"]; ?>');">
                <?php echo $this->lang->line("comment"); ?>&nbsp;&nbsp;<i class="fa-solid fa-comment"></i>
            </button>
        </div>
        
        <div class="px-3" id="side-section">
            <?php $this->load->view("tasks/view/side_section"); ?>
        </div>
    </div>
</div>

<script>
    var allowedUploadSizeMegabite = '<?php echo $this->config->item("allowed_upload_size_megabite"); ?>';
    var attachmentsDir = '<?php echo $docs["directory"]; ?>';
    var activeComment = <?php echo $activeComment; ?>;
    var viewableExtensions = <?php echo json_encode($this->document_management_system->viewable_documents_extensions); ?>;
</script>