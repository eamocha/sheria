<div class="container-fluid object-display-form" id="opinion-display-form">
    <div class="row opinion" id="header-content"><?php echo form_input(["id" => "id", "class" => "opinion-id", "value" => $opinion_data["id"], "type" => "hidden"]);?>
        <div class="col-md-12">
            <div class="col-md-8 no-padding">
                <span id="title-description"><h4 class="opinion-subject"></bdi>
                        <?php
                       $id=$opinion_data["id"];
                      
                         echo $opinion_data["model_code"];
                        echo $opinion_data["id"];?>: <?php echo $opinion_data["title"];
                        echo $this->session->userdata("AUTH_language") == "arabic" ? "&lrm;" : "&rlm;";?></bdi></h4>
                </span>
            </div>
        </div>
        <div class="col-md-12">
            <button type="button" class="btn btn-default navbar-btn action-bar-btn" onclick="opinionEditForm('<?php echo $opinion_data["id"];?>', event);"><?php echo $this->lang->line("edit"); ?>&nbsp;&nbsp; <i class="fa fa-pencil"></i></button>
            <button type="button" class="btn btn-default navbar-btn action-bar-btn" onclick="opinionCommentFormInline('<?php echo $opinion_data["id"];?>');"><?php echo $this->lang->line("comment");?>&nbsp;&nbsp; <i class="fa fa-comment"></i> </button>
            <?php
            $first_statuses = array_slice($available_statuses, 0, 3, true);
            $other_statuses = array_slice($available_statuses, 3, NULL, true);
            foreach ($first_statuses as $status_id => $status_name) {
                $step = isset($status_transitions[$status_id]) ? $status_transitions[$status_id] : false;
                $step_name = $step["name"] ? $step["name"] : $status_name;
                if (isset($step["id"]) && $step["id"]) {?>
                    <a href="javascript:;" onclick="screenTransitionForm('<?php  echo $id?>', '<?php   echo $step["id"];?>', 'legal_opinions');" title=" <?php         echo isset($step["comments"]) && $step["comments"] !== "" ? $step["comments"] : $step_name; ?>" class="case-move-status-link btn btn-default">
                        <?php  echo $step_name;?>  </a>&nbsp;<?php    } else {?> <a href="<?php  echo site_url("legal_opinions/move_status/" . $opinion_data["id"] . "/" . $status_id); ?>" title="<?php echo isset($step["comments"]) && $step["comments"] !== "" ? $step["comments"] : $step_name;?>" class="case-move-status-link btn btn-default submit-with-loader">
                    <?php echo $step_name;?></a>&nbsp;<?php } }?>
            <div class="caseStatusesVisibleList">
                <div class="dropdown"><?php if (!empty($other_statuses)) {?>
                        <button class="btn btn-default dropdown-toggle" type="button" id="dropdownMenuMoreStatuses" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">
                            <?php   echo $this->lang->line("more");?>
                            <span class="caret"></span>
                        </button>
                        <div class="dropdown-menu dropdownMenuMoreStatusesList" aria-labelledby="dropdownMenuMoreStatuses"><?php
                        foreach ($other_statuses as $status_id => $status_name) {
                            $step = isset($status_transitions[$status_id]) ? $status_transitions[$status_id] : false;
                            $step_name = $step["name"] ? $step["name"] : $status_name;
                            if (isset($step["id"]) && $step["id"]) {?>
                                <a class="dropdown-item" href="javascript:;" onclick="screenTransitionForm('<?php echo $id;?>', '<?php echo $step["id"];?>', 'legal_opinions');" title=" <?php  echo isset($step["comments"]) && $step["comments"] !== "" ? $step["comments"] : $step_name;?>"><?php  echo $step_name;?></a><?php  echo $step_name; ?></a>
                            <?php   }
                            else {?>
                            <a class="dropdown-item submit-with-loader" href="<?php  echo site_url("legal_opinions/move_status/" . $opinion_data["id"] . "/" . $status_id);?>" title="<?php echo isset($step["comments"]) && $step["comments"] !== "" ? $step["comments"] : $step_name;?>" ><?php echo $step_name;?> </a>
                                <?php
                            }
                        }?>
                        </div>
                    <?php }?>
                </div>
            </div>
            <div class="dropdown more pull-right margin-right10"><a  class="dropdown-toggle btn btn-default btn-xs" data-toggle="dropdown" aria-expanded="true"><i class="fa fa-gears"></i> Settings <span class="caret no-margin"></span></a>
                <ul class="dropdown-menu "><li class="   dropdown-item"><a href="opinion_workflows/index">Workflows</a></li>
                    <li class=" dropdown-item"><a href="opinion_statuses/index">Workflow Status</a></li>
                    <li class=" dropdown-item"><a href="opinion_assignment">Assignment Rules</a></li>
                    <li class=" dropdown-item"><a href="opinion_locations">Locations</a></li>
                    <li class=" dropdown-item"><a href="opinion_custom_fields">Custom fields</a></li>
                </ul>
            </div>
            <?php
            /*          <div class="opinion-export float-dir">                        <div>
                                                <a href="#" class="link-actions" data-toggle="dropdown" aria-haspopup="true"    aria-expanded="false"><img src="assets/images/contract/export.svg"></a>
                                         <div class="dropdown-menu dropdown-actions" id="header-actions-section">
                                                    <a class="dropdown-item" href="<?php echo site_url("legal_opinions/export_opinion_to_word/" . $opinion_data["id"]);?>" title="<?php echo $this->lang->line("export_to_word");?>">
                    <?php echo $this->lang->line("export_to_word");?></a>
                                                    <a class="dropdown-item" href="<?php echo site_url("legal_opinions/export_opinion_to_word_for_clients/" . $opinion_data["id"]);?>" title="<?php echo $this->lang->line("export_to_word_for_clients");?>"><?php echo $this->lang->line("export_to_word_for_clients");?></a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                              "*/ ?>
            <div class="d-flex mt-20" id="body-content">
                <div class="col-md-8 col-xs-8" id="main-section">
                    <div id="details-module" class="module toggle-wrap">
                        <div id="details-module-heading" class="d-flex" onclick="collapse('details-module-heading', 'details-module-body');">
                            <a href="javascript:;" class="toggle-title p-1" >  <i class="fa fa-angle-down black_color font-18">&nbsp;</i> </a>
                            <h4 class="toggle-title px-2"><?php echo $this->lang->line("public_info");?></h4>
                        </div>
                        <div class="mod-content" id="details-module-body">
                            <div id="details">
                                <ul class="property-list two-cols">
                                    <li class="item d-none">
                                        <div class="wrap">
                                            <span class="name"><?php echo $this->lang->line("type");?>:</span>    <span id="type-val"> <?php echo $opinion_data["type"];?> </span>
                                        </div>
                                    </li>
                                    <li class="item">
                                        <div class="wrap">
                                            <span class="name"><?php echo $this->lang->line("title");?>:</span>   <span id="title-val"><?php echo $opinion_data["title"];?> </span>
                                        </div>
                                    </li>
                                    <li class="item item-right">
                                        <div class="wrap">
                                            <span class="name"><?php echo $this->lang->line("workflow_status");?>:</span>
                                            <span id="status-val" class="value"> <span class="max-width-medium"> <?php echo $opinion_data["status"];?></span>
                                            </span>
                                        </div>
                                    </li>
                                    <li class="item">
                                        <div class="wrap">
                                            <span class="name"><?php echo $this->lang->line("priority");?>:</span>
                                            <span class="priority-<?php echo $opinion_data["priority"];?>"></span>
                                            <span id="type-val"><?php echo $this->lang->line($opinion_data["priority"]);?>
                                            </span>
                                        </div>
                                    </li>
                                    <li class="item item-right">
                                        <div class="wrap">
                                            <span class="name"><?php echo $this->lang->line("signed_copy");?>:</span>
                                            <span id="status-val" class="value"> <span class="max-width-medium"> <i class="fa-solid fa-paperclip"></i> &nbsp <?php $id=$opinion_data["id"]; echo $opinion_data["opinion_file"] ?"<a href='javascript:;' >Download</a>": "<a class='btn btn-link' href='javascript:;' onclick='Attach_legal_opinion(".$opinion_data["id"].");'>".$this->lang->line("attach_signed_opinion")."</a>";?></span>
                                            </span>
                                        </div>
                                    </li>
                                </ul>
                                <ul class="property-list two-cols">
                                    <?php if ($this->license_package == "core" || $this->license_package == "core_contract") {?>
                                        <li class="item">
                                            <div class="wrap">
                                                <span class="name"><?php echo $this->lang->line("related_case");?>:</span>
                                                <span id="status-val" class="value">
                                                    <span class="max-width-medium"> <?php     if (isset($opinion_data["caseSubject"]) && $opinion_data["caseSubject"]) {?>
                                                            <a href="<?php echo site_url(($opinion_data["caseCategory"] == "IP" ? "intellectual_properties" : "cases") . "/edit/" . $opinion_data["legal_case_id"]); ?>" target="_blank">
                                                                <?php echo $opinion_data["case_model_code"] . $opinion_data["legal_case_id"] . " - " . $opinion_data["caseSubject"]; ?>
                                                            </a>
                                                        <?php } else {  echo $this->lang->line("none");    }?>
                                                    </span>
                                                </span>
                                            </div>
                                        </li>
                                    <?php }
                                    if ($this->license_package == "contract" || $this->license_package == "core_contract") {?>
                                    <li class="item">
                                        <div class="wrap">
                                            <span class="name"><?php     echo $this->lang->line("related_contract");?>:</span> <span id="status-val" class="value"> <span class="max-width-medium">
                                                    <?php     if (isset($opinion_data["contract_name"]) && $opinion_data["contract_name"]) {?>
                                                        <a href="<?php   echo site_url("modules/contract/contracts/view/" . $opinion_data["contract_id"]);?>" target="_blank"><?php   echo $opinion_data["contract_model_code"] . $opinion_data["contract_id"] . " - " . $opinion_data["contract_name"];?></a>
                                                    <?php    }
                                                    else {  echo $this->lang->line("none");
                                                    }?>
                                                </span>
                                            </span>
                                        </div>
                                    </li>
                                </ul>
                                <?php }?>
                            </div>
                            <div id="custom-fields-details-module" class="module"><?php $this->load->view("opinions/view/custom_fields/details_section");?>
                            </div>
                        </div>
                    </div>
                    <div id="description-module" class="module "><?php $this->load->view("opinions/view/description_section");?> </div>

                   <?php $relatedToMatter=isset($opinion_data["caseSubject"]) && $opinion_data["caseSubject"]??false;
                  ?>
                    <div id="attachments-module" class="module"><?php $this->load->view("opinions/view/attachments_section");?></div>


                    <?php if ($relatedToMatter) {
                        ?>
                        <div id="matter-attachments-module" class="module"><?php $this->load->view("opinions/view/matter_attachments_section");?> </div>          
                        <?php } ?>

                    <div id="activity-module" class="module"><?php $this->load->view("opinions/view/activity_section");?></div>
                    <button type="button" class="btn btn-default navbar-btn" onclick="opinionCommentFormInline('<?php echo $id;?>');"><?php echo $this->lang->line("comment");?>&nbsp;&nbsp;<i class="fa-solid fa-comment"></i></button>
                </div>
                <div class="px-3" id="side-section"><?php $this->load->view("opinions/view/side_section");?> </div>
            </div>
        </div>

        <script>
            var allowedUploadSizeMegabite = '<?php echo $this->config->item("allowed_upload_size_megabite");?>';
            var attachmentsDir = '<?php echo $docs["directory"];?>';
            var activeComment = <?php echo $activeComment;?>;
            //var viewableExtensions = <?php echo json_encode($this->document_management_system->viewable_documents_extensions);?>;
        </script>
