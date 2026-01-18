<?php
$this->load->library("common_functions");
$systemPreferences = $this->session->userdata("systemPreferences");
$case_has_multi_containers = !empty($count_legal_case_related_containers["data"]["totalRows"]) && 1 < $count_legal_case_related_containers["data"]["totalRows"];
if ($case_has_multi_containers) {
    $this->load->view("cases/case_has_multi_containers_modal");
}
?>
    <div id="edit-legal-case-container">
            <?php                    $this->load->view("cases_criminal/top_nav", ["is_edit" => 1, "main_tab" => true]);                    ?>
        <div id="casesDetails" class="main-offcanvas main-offcanvas-left">
            <div id="newCaseFormDialog" class="col-xs-12 no-padding">
                <?php
                $this->load->view("partial/tabs_subnav_vertical", $tabsNLogs);
                ?>
                <div class="resp-main-body-width-70 no-padding flex-scroll-auto flex-grow" id="main-content-side">
                    <div class="main-content-section">
                        <div id="top-section-div"></div>
                                                <?php  //$this->load->view("cases/object_header");     ?>
                        <div class="row col-md-12 no-margin no-padding">
                            <div class="col-md-9">
                                <div class="col-md-12 no-padding matter">
                                    <?php
                                    echo form_open(current_url(), 'class="form-horizontal editmode" novalidate id="legalCaseAddForm" method="post"');
                                    echo form_input(["name" => "id", "id" => "id", "value" => $legalCase["id"], "data-field" => "case_id", "type" => "hidden"]);
                                    echo form_input(["name" => "send_notifications_email", "id" => "send_notifications_email", "value" => $hide_show_notification_edit_legal_case, "type" => "hidden"]);
                                    echo form_input(["name" => "allUsersProviderGroup", "id" => "all-users-provider-group", "value" => $allUsersProviderGroupId, "type" => "hidden"]);
                                    echo form_input(["name" => "externalizeLawyers", "id" => "externalizeLawyers", "value" => $legalCase["externalizeLawyers"], "type" => "hidden"]);
                                    echo form_input(["name" => "category", "id" => "category", "value" => $legalCase["category"], "type" => "hidden"]);
                                    echo form_input(["name" => "archived", "id" => "archived", "value" => $legalCase["archived"], "type" => "hidden"]);
                                    echo form_input(["name" => "visibleToCP", "id" => "visibleToCP", "value" => $legalCase["visibleToCP"], "type" => "hidden"]);
                                    echo form_input(["name" => "case_status_id", "id" => "archived", "value" => $legalCase["case_status_id"], "type" => "hidden"]);
                                    echo form_input(["name" => "stage", "id" => "litigation-stage-id", "value" => $legalCase["stage"], "type" => "hidden"]);
                                    echo form_input(["name" => "time_logs_cap_ratio", "id" => "time-logs-cap-ratio", "value" => $legalCase["time_logs_cap_ratio"], "type" => "hidden"]);
                                    echo form_input(["name" => "expenses_cap_ratio", "id" => "expenses-cap-ratio", "value" => $legalCase["expenses_cap_ratio"], "type" => "hidden"]);
                                    echo form_input(["name" => "cap_amount", "id" => "cap-amount", "value" => $legalCase["cap_amount"], "type" => "hidden"]);
                                    echo form_input(["name" => "cap_amount_enable", "id" => "cap-amount-enable", "value" => $legalCase["cap_amount_enable"], "type" => "hidden"]);
                                    echo form_input(["name" => "cap_amount_disallow", "id" => "cap-amount-disallow", "value" => $legalCase["cap_amount_disallow"], "type" => "hidden"]);
                                    echo form_input(["name" => "workflow", "value" => $legalCase["workflow"], "type" => "hidden"]);
                                    ?>
                                    <div class="box-section row margin-bottom-15" id="personal_info_div">
                                        <h2 class="box-title box-shadow_container ">
                                            <i class="spr spr-matter"></i>
                                            <?php echo $this->lang->line("case_public_info");?>
                                        </h2>
                                         <div class="col-md-12 box-shadow_container padding-15">

                                             <div class="row col-md-12 no-padding no-margin">
                                                <div class="form-group col-md-6 no-padding row no-margin">
                                                    <?php $case_scenario_general = "scenario_case_types_";
                                                    $case_scenario_type = $legalCase["category"] == "Criminal" ? "criminal" : "litigation";
                                                    ?>
                                                    <div class="col-md-4 no-padding">
                                                        <label class="form-label required">  Category </label>
                                                        <a href="javascript:void();" onclick="quickAdministrationDialog('case_types', jQuery('#newCaseFormDialog'), true, '<?php echo $case_scenario_type;?>');" class="btn btn-link px-0"><i class="fa fa-square-plus p-1 font-18 purple_color"> </i></a>
                                                    </div>
                                                    <div class="col-xs-12 col-md-8">
                                                        <?php echo form_dropdown("case_type_id", $Case_Types, $legalCase["case_type_id"], "id=\"case_type_id\" data-validation-engine=\"validate[required]\" class=\"form-control\" data-field=\"administration-case_types\" onchange=\"checkRelatedFields({case_id : '" . (int) $legalCase["id"] . "' ,category: '" . $legalCase["category"] . "', old_type: '" . $legalCase["case_type_id"] . "', new_type: jQuery(this).val()});\"");                         ?>
                                                        <div data-field="case_type_id" class="inline-error d-none"></div>
                                                    </div>
                                                </div>
                                                 <div class="form-group col-md-6 no-padding-left row no-margin">
                                                     <label class="form-label ">Court File Number</label>
                                                     <div class="col-xs-12 col-md-6"><?php echo form_input(["name" => "cfNo", "id" => "cfNo","class" => "form-control" ,"value" => "", "type" => "text"]);?>
                                                         <div data-field="cfNo" class="inline-error d-none"></div>
                                                     </div>
                                                 </div>
                                             </div>
                                             <div class="row col-md-12 no-padding no-margin">
                                                 <div class="form-group col-md-12 no-padding row no-margin">
                                                     <div class="col-md-2 no-padding">
                                                         <label class="form-label required">
                                                             Type
                                                         </label>
                                                     </div>
                                                     <div class="col-xs-12 col-md-10">
                                                         <?php
                                                         echo form_input("subject", $legalCase["subject"], 'id="subject" class="form-control" dir="auto" autocomplete="stop" data-validation-engine="validate[required,maxSize[254],funcCall[validateFormat]]"');
                                                         ?>
                                                         <div data-field="subject" class="inline-error d-none"></div>
                                                     </div>
                                                 </div>
                                             </div>
                                             <div class="row col-md-12 no-padding no-margin " id="otherRefNumbers">
                                                 <div class="form-group col-md-6 no-padding-left row no-margin">
                                                     <label class="form-label ">Police Station Number</label>
                                                     <div class="col-xs-12 col-md-6"><?php echo form_input(["name" => "pfNo", "id" => "pfNo","class" => "form-control", "value" => "", "type" => "text"]);?>
                                                         <div data-field="pfNo" class="inline-error d-none"></div>
                                                     </div>

                                                 </div>
                                                 <div class="form-group col-md-6 no-padding-left row no-margin">
                                                     <label class="form-label ">OB Number</label>
                                                     <div class="col-xs-12 col-md-6"><?php echo form_input(["name" => "obNo", "id" => "obNo", "value" => "","class" => "form-control", "type" => "text"]);?>
                                                         <div data-field="obNo" class="inline-error d-none"></div>
                                                     </div>
                                                 </div>
                                             </div>
                                             <div class="row col-md-12 no-padding no-margin d-none" id="workflow-status-container-design">
                                                        <div class="form-group col-md-6 no-padding-left row no-margin">
                                                            <label class="control-label col-md-4 no-padding"><?php echo $this->lang->line($category . "case_status");?></label>
                                                            <div class="col-xs-12 col-md-8 no-padding-right padding-left-30"><?php $currentStatus = $Case_Statuses[$legalCase["case_status_id"]];?>
                                                            <lable class="label-normal-style"><h5 style="width:190px;" title="<?php echo $currentStatus;?>" class="text-center big-text-font-size no-margin mt-10 bold-font-family label-normal-style trim-width-120 widget-status tooltip-title <?php echo str_replace(" ", "-", strtolower($legalCase["workflow_status_category"]));?>"
                                                            <?php echo $this->common_functions->detect_is_rtl($currentStatus) ? 'dir="rtl"' : "";?>>
                                                            <?php echo $currentStatus; ?> </h5>
                                                            </lable>
                                                            </div>
                                                        </div>
                                                        <div class="form-group col-md-6 no-padding-left row no-margin ">
                                                            <label class="control-label col-md-4 no-padding"><?php echo $this->lang->line("workflow_used_label");?></label>
                                                            <div class="col-xs-12 col-md-7"><?php $currentStatus = $Case_Statuses[$legalCase["case_status_id"]];?>
                                                            <lable class="label-normal-style">
                                                            <h5 class="font-12 no-margin mt-10 bold-font-family trim-width-17vw min-height-20 pl-10"><?php echo $workflow_applicable["name"];?> </h5>
                                                            </lable>
                                                            </div>
                                                            <div class="col-md-1 p-0">
                                                                <a class="btn" href="<?php echo site_url("manage_workflows/statuses/" . $workflow_applicable["id"]); ?>"><i class="fa fa-pencil purple_color" aria-hidden="true"></i></a>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="row col-md-12 no-padding no-margin row no-margin ">
                                                        <div class="form-group col-md-6 no-padding row no-margin">
                                                            <label class="control-label col-md-4 no-padding"><?php echo $this->lang->line("case_inquiry_no");?> </label>
                                                        <div class="col-xs-12 col-md-8">
                                                           <div class="input-group"><?php
                                                           echo form_input(["dir" => "auto", "name" => "internalReference", "id" => "internalReference", "class" => "form-control", "value" => $legalCase["internalReference"]]);
                                                           if (isset($systemPreferences["AllowInternalRefLink"]) && $systemPreferences["AllowInternalRefLink"] == 1) {   ?>
                                                           <span class="input-group-addon no-border">
                                                           <a href="javascript:void(0);" onclick="internalRefNumLink('<?php echo $systemPreferences["InternalRefLink"];?>');" class="icon-alignment no-border no-margin no-padding btn btn-link">  <i class="fa fa-external-link"></i></a>
                                                          </span><?php
                                                           }?>
                                                            <div data-field="internalReference" class="inline-error d-none"></div>
                                                         </div>
                                                            </div>
                                                        </div>
                                                        <div class="form-group col-md-6 no-padding-left row no-margin">
                                                            <label class="control-label col-md-4 no-padding"><?php echo $this->lang->line("matter_container");
                                                            if ($case_has_multi_containers) {   ?>
                                                                <span class="icon-alignment d-inline-block padding-10 purple_color">
                                                                    <span id="multi_containers_tooltip" class="tooltipTable" title='<?php   echo sprintf($this->lang->line("related_containers_warning_modal"), site_url("/case_containers/index/" . $id));?>'><i class="fa-solid fa-circle-question purple_color"></i></span>
                                                                </span>
                                                            <?php }
                                                            $legal_case_related_container_link_href = site_url("case_containers/edit/" . $legal_case_related_container["data"]["id"]??=NULL );?>
                                                                <a href="<?php echo $legal_case_related_container_link_href;?>" id="legal-case-related-container-link" target="_blank" class="icon-alignment no-padding btn btn-link <?php echo empty($legal_case_related_container["data"]["id"]) ? "d-none" : ""; ?>" title="<?php echo "MC" . $legal_case_related_container["data"]["id"]; ?>"><i class="fa fa-external-link"></i></a></label>
                                                            <div class="col-xs-12 col-md-8 no-padding-right"><?php
                                                                echo form_input(["name" => "legalCaseRelatedContainerId", "id" => "legal-case-related-container-id", "value" => $legal_case_related_container["data"]["id"] ?? NULL, "type" => "hidden"]);
                                                                echo form_input(["name" => "legalCaseRelatedSubject", "id" => "legal-case-related-container-lookup", "value" => $legal_case_related_container["data"]["subject"] ?? NULL, "class" => "lookup form-control", "placeholder" => $this->lang->line("start_typing"), "title" => $this->lang->line("start_typing")]);?>
                                                                <div data-field="legalCaseRelatedContainerId" class="inline-error d-none"></div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="row col-md-12 no-padding no-margin row no-margin">
                                                        <div class="form-group col-md-6 no-padding row no-margin d-none">
                                                            <label class="control-label col-md-4 no-padding"><?php echo $this->lang->line("risk_profile");?> </label>
                                                          <div class="col-xs-12 col-md-8">
                                                          <select name="priority" class="form-control select-picker" id="priority" data-live-search="true">
                                                          <?php $selected = "";
                                                          foreach ($priorities as $key => $value) {
                                                              if ($key == $legalCase["priority"]) {
                                                                  $selected = "selected";
                                                              } else {
                                                                  $selected = "";
                                                              }
                                                              ?>
                                                              <option data-icon="priority-<?php    echo $key;?>" <?php    echo $selected;?> value="<?php    echo $key;?>"><?php    echo $value;    ?> </option>
                                                               <?php }?>
                                                               </select>
                                                                <div data-field="priority" class="inline-error d-none"></div>
                                                            </div>
                                                        </div>
                                                        <!-- end of priority- -->
                                                        <!--start status-->
                                                        <div class="form-group col-md-6 no-padding-left row no-margin">
                                                            <label class="control-label col-md-4 no-padding"><?php echo $this->lang->line("status");?></label>
                                                            <div class="col-xs-12 col-md-8 no-padding-right padding-left-30"><?php $currentStatus = $legalCase["closedOn"]? "<a class='btn btn-danger' href='javascript:;'>".  $this->lang->line("closed")."</a>": "<a class='btn btn-success' href='javascript:;'>".  $this->lang->line("ongoing")."</a>";?>
                                                                <lable class="label-normal-style"><h5 style="width:190px;" title="<?php echo $currentStatus;?>" class="text-center big-text-font-size no-margin mt-10 bold-font-family label-normal-style trim-width-120 widget-status tooltip-title <?php echo str_replace(" ", "-", strtolower($legalCase["workflow_status_category"]));?>"
                                                                        <?php echo $this->common_functions->detect_is_rtl($currentStatus) ? 'dir="rtl"' : "";?>>
                                                                        <?php echo $currentStatus; ?> </h5>
                                                                </lable>
                                                            </div>
                                                        </div>
                                                        <div class="form-group col-md-6 no-padding-right no-padding-left row no-margin">
                                                        <?php echo form_input(["value" => $legalCase["legal_case_stage_id"], "id" => "current-case-stage-id", "type" => "hidden"]);
                                                        $case_scenario_general = "scenario_case_stages_";?>
                                                            <label class="control-label col-md-4 no-padding"><?php echo $this->lang->line("stage");?>
                                                                <a href="javascript:void();" onclick="quickAdministrationDialog('case_stages', jQuery('#newCaseFormDialog'), true, '<?php echo $case_scenario_type;?>'<?php echo $legalCase["category"] == "criminal" ? ", changeLitigationStage" : "";?>)" class="btn btn-link px-0"><i class="fa fa-square-plus p-1 font-18 purple_color"> </i></a>
                                                            </label>
                                                            <?php if (strtolower($legalCase["category"]) == "criminal") {   ?>
                                                            <div class="col-xs-12 col-md-8">
                                                                <?php
                                                                echo form_input(["name" => "legal_case_stage_id", "id" => "hiddenStageId", "value" => $legalCase["legal_case_stage_id"], "type" => "hidden"]);
                                                                echo form_input(["value" => "", "id" => "legal_case_stage_id", "type" => "hidden", "data-field" => "administration-case_stages"]);
                                                                ?>
                                                                <p class="btn-link no-padding pl-7 bold-font-family" href='javascript:void();' onclick="changeLitigationStage('legalCaseAddForm');" style="margin-top: 4px;" id="matter-stage">
                                                                    <?php    echo $caseStages[$legalCase["legal_case_stage_id"]];    ?>
                                                                </p>
                                                            </div>
                                                            <?php
                                                            } else {
                                                                ?>
                                                            <div class="col-xs-12 col-md-8">
                                                                <?php    echo form_dropdown("legal_case_stage_id", $caseStages, $legalCase["legal_case_stage_id"], 'id="legal_case_stage_id" class="form-control" data-field="administration-case_stages"');
                                                                ?>
                                                                <div data-field="legal_case_stage_id" class="inline-error d-none"></div>
                                                            </div>
                                                            <?php
                                                            }?>
                                                        </div>
                                                    </div>
                                             <div class="row col-md-12 no-padding no-margin row no-margin d-none">
                                                 <?php if ($legalCase["category"] == "Criminal") {   ?>
                                                     <div class="form-group col-md-6 no-padding row no-margin">
                                                         <label class="control-label col-md-4 no-padding"><?php    echo $this->lang->line("success_probability");    ?> </label>
                                                         <div class="col-xs-12 col-md-8">
                                                             <?php    echo form_dropdown("legal_case_success_probability_id", $successProbabilities, $legalCase["legal_case_success_probability_id"], 'id="legal_case_success_probability_id" class="form-control"');  ?>
                                                             <div data-field="legal_case_success_probability_id" class="inline-error d-none"></div>
                                                         </div>
                                                     </div>
                                                     <?php } else {
                                                     ?>
                                                 <div class="form-group col-md-6 no-padding row no-margin">
                                                     <label class="control-label col-md-4 no-padding">
                                                         <?php    echo $this->lang->line("complainant");
                                                           $companyLinkHref = site_url("companies/tab_company/" . $clientData["member_id"]??=1);
                                                           $typ= $clientData["type"]??=0;
                                                           $clientLinkHref =$typ == "Company" ? $companyLinkHref : site_url("contacts/edit/" . $clientData["member_id"]);   ?>
                                                         <a href="<?php    echo $clientLinkHref;?>" id="clientLinkId" class="<?php    echo $clientData["member_id"] && $clientCompanyCategory != "Group" ? "" : "d-none";?>"><i class="fa fa-external-link"></i></a>
                                                     </label>
                                                     <div class="row no-margin col-xs-12 col-md-8">
                                                         <div class="col-md-3 no-padding">
                                                             <select name="clientType" id="client-type" class="form-control company-contact-select" tabindex="-1" data-iconBase="fa" data-tickIcon="fa-check">
                                                                 <option data-content="<i class='fa fa-building purple_color' title='<?php    echo $this->lang->line("company");?>'></i>" value="company" <?php    echo $clientData["type"] == "Company" ? "selected='selected'" : "";?>></option>
                                                                 <option data-content="<i class='fa fa-user purple_color' title='<?php    echo $this->lang->line("contact");?>'></i>" value="contact" <?php    echo $clientData["type"] == "Person" ? "selected='selected'" : "";?>></option>
                                                             </select>
                                                         </div>
                                                         <div class="col-md-9">
                                                             <?php    echo form_input(["name" => "contact_company_id", "id" => "contact-company-id", "value" => $clientData["member_id"], "type" => "hidden"]);
                                                             echo form_input(["name" => "", "id" => "client-lookup", "value" => $clientData["name"]??=0, "class" => "form-control lookup", "placeholder" => $this->lang->line("start_typing"), "title" => $clientData["foreignName"]??=0, "onblur" => "checkLookupValidity(jQuery(this), jQuery('#contact_company_id', '#legalCaseAddForm')); if (this.value === '') { jQuery('#clientLinkId').addClass('d-none');jQuery(this).attr('title', ''); }"]);  ?>
                                                         </div>
                                                     </div>
                                                 </div>
                                                 <?php }?>
                                                 <div class="form-group col-md-6 no-padding row no-margin">
                                                     <label class="control-label col-md-4 no-padding">
                                                         <?php
                                                         echo $this->lang->line("caseValue");?>
                                                         <span class="smaller_font">
                                                             <?php echo isset($systemPreferences["caseValueCurrency"]) && $systemPreferences["caseValueCurrency"] != "" ? "(" . $systemPreferences["caseValueCurrency"] . ")" : "";?>
                                                         </span>
                                                     </label>
                                                     <div class="col-xs-12 col-md-8">
                                                         <?php echo form_input(["class" => "form-control", "id" => "caseValue", "name" => "caseValue"], $legalCase["caseValue"] + 0);?>
                                                         <div data-field="caseValue" class="inline-error d-none"></div>
                                                     </div>
                                                 </div>
                                             </div>

                                                <div class="row col-md-12 no-padding no-margin row no-margin">
                                                 <div class="form-group col-md-6 no-padding row no-margin">
                                                     <label class="control-label col-md-4 no-padding">
                                                         <?php    echo $this->lang->line("judgment_value");   ?>
                                                         <div class="smaller_font display-inline-important">
                                                             <?php
                                                             echo isset($systemPreferences["caseValueCurrency"]) && $systemPreferences["caseValueCurrency"] != "" ? "(" . $systemPreferences["caseValueCurrency"] . ")" : "";
                                                             ?>
                                                         </div>
                                                     </label>
                                                     <div class="col-xs-12 col-md-8">
                                                         <?php    echo form_input(["class" => "form-control", "id" => "judgmentValue", "name" => "judgmentValue"], $legalCase["judgmentValue"] + 0);   ?>
                                                         <div data-field="judgmentValue" class="inline-error d-none"></div>
                                                     </div>
                                                 </div>
                                                 <div class="form-group col-md-6 no-padding row no-margin">
                                                     <label class="control-label col-md-4 no-padding">
                                                         <?php    echo $this->lang->line("recovered_value");    ?>
                                                         <div class="display-inline-important smaller_font"><?php echo isset($systemPreferences["caseValueCurrency"]) && $systemPreferences["caseValueCurrency"] != "" ? "(" . $systemPreferences["caseValueCurrency"] . ")" : "";
                                                         ?>
                                                         </div>
                                                     </label>
                                                     <div class="col-xs-12 col-md-8">
                                                         <?php    echo form_input(["class" => "form-control", "id" => "recoveredValue", "name" => "recoveredValue"], $legalCase["recoveredValue"] + 0);   ?>
                                                         <div data-field="recoveredValue" class="inline-error d-none"></div>
                                                     </div>
                                                 </div>
                                                </div>
                                             <!-- additional fields -->
                                                <div class="row col-md-12 no-padding no-margin row no-margin">
                                                 <div class="form-group col-md-6 no-padding row no-margin ">
                                                     <label class="control-label col-md-4 no-padding tooltip-title" title="<?php echo $this->lang->line("risk_profile_helper_text"); ?>"> 
                                                         <?php    echo $this->lang->line("risk_profile");   ?> <i class="fa-solid fa-circle-question purple_color"></i></label>
                                                     <div class="col-xs-12 col-md-8">
                                                             <?php    echo form_dropdown("legal_case_risk_profile_id", $successProbabilities, $legalCase["legal_case_success_probability_id"], 'id="legal_case_risk_profile_id" class="form-control"');  ?>
                                                             <div data-field="legal_case_risk_profile_id" class="inline-error d-none"></div>
                                                        </div>
                                                 </div>
                                                 <div class="form-group col-md-6 no-padding row no-margin">
                                                     <label class="control-label col-md-4 no-padding tooltip-title" title="<?php echo $this->lang->line("approach_strategy_helper_text");?>">
                                                         <?php    echo $this->lang->line("approach_strategy");    ?> <i class="fa-solid fa-circle-question purple_color"></i> </label>
                                                         <div class="col-xs-12 col-md-8">
                                                             <?php    echo form_dropdown("legal_case_approach_strategy_id", $successProbabilities, $legalCase["legal_case_success_probability_id"], 'id="legal_case_approach_strategy_id" class="form-control"');  ?>
                                                             <div data-field="legal_case_risk_strategy_id" class="inline-error d-none"></div>
                                                         </div>
                                                 </div>
                                                </div>
                                              <!-- end additional fields -->
                                                 <div class="text-center">The Republic</div>
                                                 <div class="text-center"><h3>Versus</h3></div>
                                                <div class="row col-md-12 no-padding no-margin row no-margin">
                                                    <div class="form-group col-md-6 no-padding row no-margin">
                                                        <label class="control-label col-md-4 no-padding">
                                                            <?php
                                                            echo $this->lang->line("accused") . " (1)";

                                                            $memberId   = $clientData["member_id"]   ?? 0;
                                                            $type       = $clientData["type"]        ?? "";
                                                            $name       = $clientData["name"]        ?? "";
                                                            $foreignName= $clientData["foreignName"] ?? "";

                                                            $companyLinkHref = site_url("companies/tab_company/" . $memberId);
                                                            $clientLinkHref  = ($type === "Company")
                                                                ? $companyLinkHref
                                                                : site_url("contacts/edit/" . $memberId);
                                                            ?>
                                                            <a href="<?php echo $clientLinkHref; ?>"
                                                               id="clientLinkId"
                                                               class="icon-alignment <?php echo ($memberId && $clientCompanyCategory != "Group") ? "" : "d-none"; ?>">
                                                                <i class="fa fa-external-link"></i>
                                                            </a>
                                                        </label>

                                                        <div class="row m-0 col-md-8 no-padding">
                                                            <div class="col-md-3 no-padding-right">
                                                                <select name="clientType" id="client-type"
                                                                        class="form-control select-picker company-contact-select"
                                                                        tabindex="-1" data-iconBase="fa" data-tickIcon="fa-check">
                                                                    <option
                                                                            data-content="<i class='fa fa-building purple_color' title='<?php echo $this->lang->line("company"); ?>'></i>"
                                                                            value="company"
                                                                        <?php echo ($type === "Company") ? "selected='selected'" : ""; ?>>
                                                                    </option>
                                                                    <option
                                                                            data-content="<i class='fa fa-user purple_color' title='<?php echo $this->lang->line("contact"); ?>'></i>"
                                                                            value="contact"
                                                                        <?php echo ($type === "Person") ? "selected='selected'" : ""; ?>>
                                                                    </option>
                                                                </select>
                                                            </div>

                                                            <div class="col-md-9">
                                                                <?php
                                                                echo form_input([
                                                                    "name"  => "contact_company_id",
                                                                    "id"    => "contact-company-id",
                                                                    "value" => $memberId,
                                                                    "type"  => "hidden"
                                                                ]);

                                                                echo form_input([
                                                                    "name"        => "",
                                                                    "id"          => "client-lookup",
                                                                    "value"       => $name,
                                                                    "class"       => "form-control lookup",
                                                                    "placeholder" => $this->lang->line("start_typing"),
                                                                    "title"       => $foreignName,
                                                                    "onblur"      => "checkLookupValidity(
                                    jQuery(this), 
                                    jQuery('#contact_company_id', '#legalCaseAddForm')
                                 ); 
                                 if (this.value === '') { 
                                     jQuery('#clientLinkId').addClass('d-none'); 
                                     jQuery(this).attr('title', ''); 
                                 }"
                                                                ]);
                                                                ?>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="form-group col-md-6 no-padding row no-margin">
                                                     <label class="control-label col-md-4 no-padding flex-center-inline">
                                                         <?php
                                                         echo $this->lang->line("client_position");
                                                         $case_scenario_general = "scenario_case_client_positions";
                                                         ?>
                                                         <a href="javascript:;" onclick="quickAdministrationDialog('case_client_positions', jQuery('#newCaseFormDialog'), true, '<?php echo $case_scenario_type;?>')" class="icon-alignment btn btn-link px-0"><i class="fa fa-square-plus p-1 font-18 purple_color"> </i></a>
                                                     </label>
                                                     <div class="row m-0 col-md-8 no-padding">
                                                         <?php    echo form_dropdown("legal_case_client_position_id", $clientPositions, $legalCase["legal_case_client_position_id"], 'id="legal_case_client_position_id" class="form-control" data-field="administration-case_client_positions"');
                                                         ?>
                                                         <div data-field="legal_case_client_position_id" class="inline-error d-none"></div>
                                                     </div>
                                                 </div>
                                                </div>

                                             <div class="col-md-12 no-padding">
                                                 <div class="form-group no-padding">
                                                     <label class="control-label col-md-2 no-padding"><?php echo $this->lang->line("description_Case"); ?>&nbsp; <span class="tooltip-title" title="<?php echo $legalCase["category"] == "Litigation" ? $this->lang->line("litigation_description_help") : $this->lang->line("matter_description_help");?>"><i class="fa-solid fa-circle-question purple_color"></i></span></label>
                                                     <div class="col-md-12 no-padding">
                                                         <?php echo form_textarea(["name" => "description", "id" => "description", "class" => "form-control min-height-120 resize-vertical-only", "rows" => "5", "cols" => "0", "value" => $legalCase["description"]]);
                                                         ?>
                                                         <div data-field="description" class="inline-error d-none"></div>
                                                     </div>
                                                 </div>
                                             </div>
                                             <div class="col-md-12 no-padding">
                                                 <div class="form-group no-padding-left">
                                                     <label class="control-label col-md-3 no-padding">
                                                         <?php  echo $this->lang->line("latest_development");?> &nbsp;<i class="fa-solid fa-circle-question purple_color tooltip-title right-box-audit-title" title="<?php echo $this->lang->line("important_development_helper");?>"></i>
                                                     </label>
                                                     <div class="col-md-12 no-padding">
                                                         <div class="timestamp-parent">
                                                             <?php echo form_textarea(["name" => "latest_development", "id" => "latestDevelopment", "class" => "form-control min-height-120 latest-dev", "rows" => "5", "cols" => "0", "value" => $legalCase["latest_development"]]);
                                                             ?>
                                                             <a href="javascript:void(0)" id="add-timestamp" onclick="addLatestDevTimeStamp();" class="pull-right" style="text-decoration: none;"><i class="icon-alignment fa-solid fa-clock no-margin no-margin-left"></i>
                                                                 <?php echo $this->lang->line("add_timestamp");?>
                                                             </a>
                                                         </div>
                                                         <div data-field="latest_development" class="inline-error d-none"></div>
                                                     </div>
                                                 </div>
                                             </div>
                                                    <div class="col-md-12 no-padding d-none">
                                                        <div class="form-group no-padding">
                                                            <label class="control-label col-md-3 no-padding"><?php echo $this->lang->line("status_comments");?> </label>
                                                            <div class="col-md-12 no-padding">
                                                                <?php
                                                                echo form_textarea(["name" => "statusComments", "id" => "statusComments", "class" => "form-control min-height-120 resize-vertical-only", "value" => $legalCase["statusComments"], "rows" => "2", "cols" => "0"]);
                                                                ?>
                                                            </div>
                                                            <div data-field="statusComments" class="inline-error d-none"></div>
                                                        </div>
                                                    </div>
                                         </div>
                                        <?php
                                         echo form_fieldset_close();
                                        echo form_close();
                                        ?>
                                    </div>
                                    <div class="box-section row margin-bottom-15" id="custom_fields_div">
                                        <h2 class="box-title">
                                            <i class="spr spr-folder"></i>
                                            <span class="tooltip-title text-with-hint" title="<?php echo $this->lang->line("cases_custom_field_helper");?>">
                                                <?php echo $this->lang->line("custom_fields");?>
                                            </span>
                                        </h2>
                                        <?php
                                        if (!empty($custom_fields["main"])) {
                                            $this->load->view("custom_fields/form_custom_field_template_view", ["custom_fields" => $custom_fields["main"]]);
                                            } else {
                                            ?>
                                        <p class="text padding-15"> <?php  echo sprintf($this->lang->line("no_custom_fields"), base_url() . "custom_fields/cases");?>   </p>
                                        <?php }?>
                                    </div>

                                    <div class="box-section row margin-bottom-15" id="court_activities_div">
                                        <h2 class="box-title box-shadow_container ">
                                            <img src="assets/images/icons/court-activity.svg" height="20" width="20">
                                            <span class="text-with-hint tooltip-title" title="<?php echo $legalCase["category"] == "Litigation" ? $this->lang->line("litigation_activities_helper") : $this->lang->line("criminal_activities_helper");?>">
                                                <?php echo $this->lang->line("court_activities");?>
                                            </span>
                                        </h2>
                                        <div class="col-md-12 box-shadow_container padding-15 main-grid-container">
                                            <div class="col-md-12 no-padding grid-section">
                                                <!--   <a class="cursor-pointer-click btn btn-primary" onclick="legalCaseHearingForm(0, false, 29, true, function (){legalCaseEvents.goToPage('stages',{stageId: 10006, id: 29})}, '10006')">New Court Activity</a>
   -->
                                                <a class="cursor-pointer-click btn btn-primary"  onclick="legalCaseHearingForm()"><?php echo $this->lang->line("add_court_activity")?></a>
                                                <div class="row no-margin">&nbsp;</div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="box-section row margin-bottom-15" id="outsourcing_to_lawyers_div">
                                        <h2 class="box-title box-shadow_container ">
                                            <img src="assets/images/icons/outsourcing.svg" height="20" width="20">
                                            <span class="text-with-hint tooltip-title" title=<?=$this->lang->line("litigation_outsourcing_to_helper") ?>">
                                                Lawyers handling the case
                                            </span>
                                        </h2>
                                        <div class="col-md-12 box-shadow_container padding-15 main-grid-container">
                                            <div class="col-md-12 no-padding grid-section">
                                                <?php
                                                echo form_open("", 'name="outsourceSearchFilters" id="outsourceSearchFilters" method="post" class="no-margin"');
                                                echo form_input(["name" => "take", "value" => "5", "type" => "hidden"]);
                                                echo form_input(["name" => "legal_case_id", "value" => $legalCase["id"], "type" => "hidden"]);
                                                echo form_input(["name" => "skip", "value" => "0", "type" => "hidden"]);
                                                echo form_input(["name" => "page", "value" => "1", "type" => "hidden"]);
                                                echo form_input(["name" => "pageSize", "value" => "5", "type" => "hidden"]);
                                                echo form_input(["name" => "filter.logic", "id" => "defaultFilterLogic", "value" => "and", "type" => "hidden"]);
                                                ?>
                                                <div class="form-group d-none">
                                                    <div class="controls">
                                                        <?php
                                                        echo form_input(["name" => "filter.filters[0].filters[0].field", "value" => "legal_cases_contacts.case_id", "type" => "hidden"]);
                                                        echo form_input(["name" => "filter.filters[0].filters[0].operator", "value" => "eq", "type" => "hidden"]);
                                                        echo form_input(["name" => "filter.filters[0].filters[0].value", "value" => $id, "type" => "hidden"]);?>
                                                    </div>
                                                    <div class="controls">
                                                        <?php
                                                         echo form_input(["name" => "filter.filters[1].filters[0].field", "value" => "legal_cases_contacts.contactType", "type" => "hidden"]);
                                                         echo form_input(["name" => "filter.filters[1].filters[0].operator", "value" => "eq", "type" => "hidden"]);
                                                         echo form_input(["name" => "filter.filters[1].filters[0].value", "value" => "outsource", "type" => "hidden"]);
                                                         ?>
                                                    </div>
                                                </div>
                                                <?php echo form_close();?>
                                                <div <?php echo $this->session->userdata("AUTH_language") == "arabic" ? "class='k-rtl'" : "";?>>
                                                    <div id="outsource-grid" class="grid-container kendo-desktop grid-container-height-auto"></div>
                                                </div>
                                                <div class="row no-margin">&nbsp;</div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="box-section row margin-bottom-15" id="related_contributors_div">
                                        <h2 class="box-title box-shadow_container ">
                                            <img src="assets/images/icons/contribution.svg" height="20" width="20">
                                            <span class="text-with-hint tooltip-title" title="<?php echo  $this->lang->line("litigation_related_contributors_helper")?>">
                                                <?php echo $this->lang->line("contributors");?>
                                            </span>
                                        </h2>
                                        <div class="col-md-12 box-shadow_container padding-15 main-grid-container">
                                            <div class="col-md-12 no-padding grid-section">
                                                <?php
                                                echo form_open("", 'name="lawyersContributorsSearchFilters" id="lawyersContributorsSearchFilters" method="post" class="no-margin"');
                                                echo form_input(["name" => "take", "value" => "5", "type" => "hidden"]);
                                                echo form_input(["name" => "skip", "value" => "0", "type" => "hidden"]);
                                                echo form_input(["name" => "page", "value" => "1", "type" => "hidden"]);
                                                echo form_input(["name" => "pageSize", "value" => "5", "type" => "hidden"]);
                                                echo form_input(["name" => "filter.logic", "id" => "defaultFilterLogic", "value" => "and", "type" => "hidden"]);
                                                ?>
                                                <div class="form-group d-none">
                                                    <div class="controls">
                                                        <?php
                                                        echo form_input(["name" => "filter.filters[0].filters[0].field", "value" => "legal_cases_contacts.case_id", "type" => "hidden"]);
                                                        echo form_input(["name" => "filter.filters[0].filters[0].operator", "value" => "eq", "type" => "hidden"]);
                                                        echo form_input(["name" => "filter.filters[0].filters[0].value", "value" => $id, "type" => "hidden"]);
                                                        ?>
                                                    </div>
                                                    <div class="controls">  <?php echo form_input(["name" => "filter.filters[1].filters[0].field", "value" => "legal_cases_contacts.contactType", "type" => "hidden"]);
                                                    echo form_input(["name" => "filter.filters[1].filters[0].operator", "value" => "eq", "type" => "hidden"]);
                                                    echo form_input(["name" => "filter.filters[1].filters[0].value", "value" => "contributor", "type" => "hidden"]);
                                                    ?>
                                                    </div>
                                                </div>
                                                <?php echo form_close();?>
                                                <div <?php echo $this->session->userdata("AUTH_language") == "arabic" ? 'class="k-rtl"' : "";?>>
                                                    <div id="lawyersContributorsGrid" class="grid-container kendo-desktop grid-container-height-auto"></div>
                                                </div>
                                                <div class="row no-margin">&nbsp;</div>
                                            </div>
                                        </div>
                                    </div>
                                    <?php
                                    if (isset($legalCase["id"]) && is_numeric($legalCase["id"])) {?>
                                        <div class="box-section row margin-bottom-15" id="case_notes_tabs_btn_container">
                                            <h2 class="box-title box-shadow_container ">
                                                <i id="case_notes_tabs_toggle_icon" class="fa fa-sticky-note purple_color"></i>
                                                <?php     echo $this->lang->line("notes");?>
                                            </h2>
                                            <div class="col-md-12 box-shadow_container padding-15" id="case_notes_tabs">
                                                <ul class="nav nav-tabs" id="myTab" role="tablist">
                                                    <li id="case_notes_all_threads_list" class="nav-item" role="presentation">
                                                        <a onclick="fetch_case_comments_tab('<?php echo (int) $legalCase["id"];?>', jQuery('#case_notes_all_threads'), 'cases/get_all_comments', 1);" class="nav-link active" id="case_notes_all_threads-tab" data-toggle="tab" href='#case_notes_all_threads' role="tab" aria-controls="case_notes_all_threads" aria-selected="true">
                                                            <?php      echo $this->lang->line("case_notes_all_threads");    ?>
                                                        </a>
                                                    </li>
                                                    <li id="case_notes_core_and_cp_list" class="nav-item" role="presentation">
                                        <a onclick="fetch_case_comments_tab('<?php    echo (int) $legalCase["id"];?>', jQuery('#case_notes_core_and_cp'), 'cases/get_all_core_and_cp_comments', 2);" class="nav-link" id="case_notes_core_and_cp-tab" data-toggle="tab" href="#case_notes_core_and_cp" role="tab" aria-controls="case_notes_core_and_cp" aria-selected="false">
                                            <?php echo $this->lang->line("case_notes_core_and_cp");    ?>
                                        </a>
                                    </li>
                                    <li id="case_notes_emails_list" class="nav-item" role="presentation">
                                        <a onclick="fetch_case_comments_tab('<?php echo (int) $legalCase["id"];    ?>', jQuery('#case_notes_emails'), 'cases/get_all_email_comments', 3);" class="nav-link" id="case_notes_emails-tab" data-toggle="tab" href="#case_notes_emails" role="tab" aria-controls="case_notes_emails" aria-selected="false">
                                        <?php echo $this->lang->line("case_notes_emails");    ?>
                                          </a>
                                    </li>
                                </ul>
                                <div class="tab-content" id="myTabContent">
                                    </br>
                                    <div class="col-xs-12 no-padding">
                                        <?php    $legalCase["isCustomerPortal"] = $isCustomerPortal;   ?>
                                        <div class="col-md-12 add-note-bottom-container margin-bottom margin-top no-margin-left no-padding">
                                            <input type="button" value="<?php    echo $this->lang->line("add_note");?>" class="btn btn-default btn-info margin-right" id="add_comment" onclick="addCaseDocument('<?php    echo (int) $legalCase["id"];?>')" />
                                        </div>
                                    </div>
                                    <div class="activity-container case-notes-container tab-pane fade show active" id="case_notes_all_threads" role="tabpanel" aria-labelledby="case_notes_all_threads-tab"><div class="comments-lists-container"></div></div>
                                    <div class="activity-container case-notes-container tab-pane fade" id="case_notes_core_and_cp" role="tabpanel" aria-labelledby="case_notes_core_and_cp-tab"><div class="comments-lists-container"></div></div>
                                    <div class="activity-container case-notes-container tab-pane fade" id="case_notes_emails"  role="tabpanel" aria-labelledby="case_notes_emails-tab"><div class="comments-lists-container"></div></div>
                                </div>
                            </div>
                        </div>
                        <div class="box-section row margin-bottom-15" id="case_history_div">
                            <h2 class="box-title box-shadow_container ">
                                <img src="assets/images/icons/history.svg" height="20" width="20">
                                <?php    echo $this->lang->line("case_history");?>
                            </h2>
                            <div class="col-md-12 box-shadow_container padding-15 height-auto">

                                    <ul class="nav nav-tabs" id="history-tab" role="tablist">
                                        <li id="caseStagesHistoryList" class="nav-item" role="presentation">
                                            <a onclick="toggle_case_stages();" class="nav-link active" id="caseStagesHistoryList-tab" data-toggle="tab" href="#caseStagesHistory" role="tab" aria-controls="caseStagesHistory" aria-selected="true">
                                                <?php        echo $this->lang->line("case_stages");        ?>
                                            </a>
                                        </li>
                                        <li id="auditReportHistoryHref" class="nav-item" role="presentation">
                                            <a class="nav-link label-tooltip tooltip-title" id="auditReportHistoryHref-tab" data-toggle="tab" href="#auditReportHistory" role="tab" aria-controls="auditReportHistory" aria-selected="false" onclick="fetch_audit_report_history('<?php        echo (int) $legalCase["id"];?>', '<?php   echo $legalCase["category"] == "Litigation" ? "Litigation" : "Matter";?>');"    title="<?php echo $this->lang->line("case_history_audit_report_helper"); ?>" >
                                                <?php echo $this->lang->line("audit_report"); ?>
                                            </a>
                                        </li>
                                    </ul>
                                    <div class="tab-content" id="history-tab-content">
                                        <br>
                                        <div class="activity-container history-report-table tab-pane fade show active" id="caseStagesHistory" role="tabpanel" aria-labelledby="auditReportHistoryHref-tab"></div>
                                        <div class="activity-container history-report-table tab-pane fade" id="auditReportHistory" role="tabpanel" aria-labelledby="auditReportHistoryHref-tab"></div>
                                    </div>

                            </div>
                        </div>
                                        <div class="primary-style">
                                            <div class="modal fade" id="module_expired_modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
                                                <div class="modal-dialog" role="document">
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                            <h4 class="modal-title" id="myModalLabel">
                                                                <?php    echo $this->lang->line("case_comment_email_expiry_title");   ?>
                                                            </h4>
                                                            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                                        </div>
                                                        <div class="modal-body">
                                                            <?php    echo $this->lang->line("case_comment_email_expiry_body");   ?>
                                                        </div>
                                                        <div class="modal-footer">
                                                            <button type="button" class="close_model no_bg_button pull-right text-align-right" data-dismiss="modal">
                                                                <?php    echo $this->lang->line("close");   ?>
                                                            </button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    <?php }?>
                                </div>
                            </div>
                            <?php $this->load->view("cases/right_boxes", ["custom_fields" => $custom_fields]);?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="d-none" id="caseCommissionsFormDialog"></div>
<div class="d-none" id="slaShowLogsDialog"></div>
<div id="case-outsource-dialog">
    <?php
    if (!isset($data)) {
        $data = [];
    }
    $this->load->view("cases/outsource_add", compact("data"));
    ?>
</div>
<div id="caseStageHistoryDialog" class="d-none">
    <?php $this->load->view("cases/case_stage_history_form"); ?>
</div>


<script type="text/javascript">
    var disableMatter = '<?php echo $legalCase["archived"] == "yes" && isset($disableArchivedMatters) && $disableArchivedMatters ? true : false;?>';
    <?php if ($case_has_multi_containers) {?>
    var formReadyToSubmit = false;
    <?php }?>
    var assigneeInfo = {
        assigneeId: '<?php echo $legalCase["user_id"];?>',
        assigneeName: '<?php echo $legalCase["Assignee"];?>',
        assignedTeamId: '<?php echo $legalCase["provider_group_id"];?>'
    };
    var availableUsers = <?php echo json_encode($usersList);?>;
    var isPrivate = '<?php echo $legalCase["private"] == "yes";?>';
    jQuery(document).ready(function() {
        jQuery("#reason_for_reassignment_controls").hide();
        jQuery('#legal-case-add-form', jQuery("#edit-legal-case-container")).click(function(e) {
            e.preventDefault();
            window.onbeforeunload = null;
            jQuery(window).off('beforeunload');
            var container = jQuery("#edit-legal-case-container");
            if (/MSIE \\d|Trident.*rv:/.test(navigator.userAgent)) {
                var formData = jQuery('#main-content-side :input').serializeArray();
            } else {
                var formData = jQuery("form#legalCaseAddForm", container).serializeArray();
            }
            formData.push({
                name: "action",
                value: "save_case"
            }, {
                name: "legal_case_id",
                value: <?php echo (int) $legalCase["id"];?>
            });
            formData.forEach(function(item) {
                if (item.name === 'clientType') {
                    item.value = item.value === 'company' ? 'companies' : 'contacts';
                }
            });
            var url = getBaseURL() + 'cases/edit';
            jQuery.ajax({
                dataType: 'JSON',
                url: url,
                data: formData,
                type: 'POST',
                beforeSend: function() {
                    ajaxEvents.beforeActionEvents(container);
                },
                success: function(response) {
                    jQuery(".inline-error").addClass('d-none');
                    if (response.result) {
                        pinesMessageV2({
                            ty: 'success',
                            m: response.message
                        });
                    } else {
                        displayValidationErrors(response.validationErrors, container);
                    }
                },
                complete: function() {
                    formData.forEach(key => {
                        if (key.name == "subject") {
                            jQuery('#matter-title').html(key.value);
                            jQuery('#matter-title').tooltipster('content', key.value);
                        }
                    });
                    ajaxEvents.completeEventsAction(container, false, {}, '<i class="icon-alignment fa fa-floppy-o white-text"></i> ' + _lang.save);
                },
                error: defaultAjaxJSONErrorsHandler
            });
        });
        //privacy section
        var containerPrivacy = jQuery('#case-privacy-container');
        lookupPrivateUsers(jQuery('#lookup-case-users', containerPrivacy), 'Legal_Case_Watchers_Users', '#selected-watchers', 'case-privacy-container', containerPrivacy, 'legalCaseAddForm');
        //end privacy section
        if (jQuery('.delete-icon').length == 1) {
            jQuery('.delete-icon').addClass('d-none');
        }
        jQuery(".error").parents().closest('.foldable').removeClass('d-none')
        jQuery('input[name="send_notifications_email"]', '#legalCaseAddForm').removeAttr('checked');
        notifyMeBeforeEvent({
            'input': 'dueDate'
        }, jQuery('#legalCaseAddForm'));
        fetch_case_comments_tab(<?php echo (int) $legalCase["id"];?>, jQuery('#case_notes_all_threads'), null, 1);
        fetch_case_stages_history('<?php echo (int) $legalCase["id"];?>', true);
        new AutoNumeric('#caseValue', 'float');
        <?php if ($legalCase["category"] == "Litigation") {?>
        new AutoNumeric('#judgmentValue', 'float');
        new AutoNumeric('#recoveredValue', 'float');
        <?php }?>
        let caseCategory = <?php echo $legalCase["category"] == "Litigation" ? 1 : 0;?>;
        editCaseDemo(caseCategory);
    });
    var authIdLoggedIn = '<?php echo $this->is_auth->get_user_id();?>';
    var notificationsNoteTemplate = '<?php $this->load->view("notifications/wrapper", ["hide_show_notification" => $hide_show_notification, "container" => "commentDialog"]);?>';
    function reassignment_reason(){
//document.getElementById("reason_for_reassignment").value="";  
        jQuery("#reason_for_reassignment_controls").show();
}

    function validateNumbers(field, rules, i, options) {
        var val = field.val();
        var decimalPattern = /^[0-9]+(\\.[0-9]{1,2})?\$/;
        if (!decimalPattern.test(val)) {
            return _lang.decimalAllowed;
        }
    }

    function validateFormat(field, rules, i, options) {
        var val = field.val();
        var decimalPattern = /( ?%)\\d/;
        if (decimalPattern.test(val)) {
            return _lang.percentageNotAllowed;
        }
    }
    <?php
    if ($notify_before) {?>
    notifyMeBefore(jQuery('#legalCaseAddForm'));
    <?php }?>
    jQuery("#legalCaseRelatedContainerLookup").autocomplete({
        autoFocus: true,
        delay: 600,
        source: function(request, response) {
            request.term = request.term.trim();
            jQuery.ajax({
                url: getBaseURL() + 'case_containers/autocomplete',
                dataType: "json",
                data: request,
                error: defaultAjaxJSONErrorsHandler,
                success: function(data) {
                    if (data.length < 1) {
                        response([{
                            label: _lang.no_results_matched.sprintf([request.term]),
                            value: '',
                            record: {
                                id: -1,
                                term: request.term
                            }
                        }]);
                    } else {
                        response(jQuery.map(data, function(item) {
                            return {
                                label: item.subject,
                                value: item.subject,
                                record: item
                            }
                        }));
                    }
                }
            });
        },
        minLength: 3,
        select: function(event, ui) {
            if (ui.item.record.id > 0) {
                jQuery('#legalCaseRelatedContainerId').val(ui.item.record.id);
                jQuery('#legalCaseRelatedContainerLinkId').removeClass('d-none').attr('href', '<?php echo site_url("case_containers/edit/");?>' + ui.item.record.id).attr('title', 'MC' + ui.item.record.id);
            }
        }
    });
    var contactRoles = [<?php $jsArr = ["{value:'', text:'-'}"];
        if (!empty($contactGridRoles)) {
            foreach ($contactGridRoles as $value => $text) {
                $jsArr[] = "{value: " . (int) $value * 1 . ", text:'" . addslashes($text) . "'}";
            }
        }
        echo implode(", ", $jsArr);
        ?>];
    var companyRoles = [<?php $jsArr = ["{value:\"\", text:\"-\"}"];
        foreach ($companyGridRoles as $value => $text) {
            $jsArr[] = "{value: " . (int) $value * 1 . ", text:'" . addslashes($text) . "'}";
        }
        echo implode(", ", $jsArr);
        ?>];
    var container = jQuery("#edit-legal-case-container");
    positionMatterTitle = 'top-left';
    if (_lang.languageSettings['langDirection'] === 'rtl'){
        positionMatterTitle = 'top-right';
    }
    jQuery('#matter-title').tooltipster({
        position: positionMatterTitle,
        contentAsHTML: true,
        timer: 22800,
        animation: 'grow',
        delay: 200,
        theme: 'tooltipster-default',
        touchDevices: false,
        trigger: 'hover',
        maxWidth: 350,
        interactive: true
    });
    jQuery('.tooltip-title', container).tooltipster({
        contentAsHTML: true,
        timer: 22800,
        animation: 'grow',
        delay: 200,
        theme: 'tooltipster-default',
        touchDevices: false,
        trigger: 'hover',
        maxWidth: 350,
        interactive: true
    });
    <?php if ($case_has_multi_containers) {?>
    jQuery('#legalCaseAddForm').on('submit', function() {
        if (!formReadyToSubmit) {
            jQuery('#case_has_multi_containers_modal').modal('show');
        }
        return formReadyToSubmit;
    });
    jQuery(document).on('click', '#case-submit', function() {
        formReadyToSubmit = true;
        jQuery('#legalCaseAddForm').submit();
    });
    <?php }?>
    jQuery('#legalCaseRelatedContainerLinkId').tooltipster({
        contentAsHTML: true,
        timer: 22800,
        animation: 'grow',
        delay: 200,
        theme: 'tooltipster-default',
        touchDevices: false,
        trigger: 'hover',
        maxWidth: 350,
        interactive: true
    });
    jQuery('#case_type_id', container).selectpicker();
    jQuery('#priority', container).selectpicker();
    jQuery('#legal_case_stage_id', container).selectpicker();
    jQuery('#client-type', container).selectpicker();
    jQuery('#opponent_member_type', container).selectpicker();
    jQuery('#legal_case_success_probability_id', container).selectpicker();
    jQuery('#legal_case_client_position_id', container).selectpicker();
    jQuery('#opponent-position', container).selectpicker();

    setDatePicker('#case-arrival-date', container);
    setDatePicker('#arrival-date', container);
    setDatePicker('#due-date', container);
    setDatePicker('#closed-on', container);
    if (jQuery('.visualize-hijri-date', container).length > 0) {
        getHijriDate(jQuery('#case-arrival-date', container), jQuery('#arrival-date-container', container));
        getHijriDate(jQuery('#arrival-date', container), jQuery('#filed-on-date-container', container));
        getHijriDate(jQuery('#due-date', container), jQuery('#due-date-container', container));
        getHijriDate(jQuery('#closed-on', container), jQuery('#closed-on-container', container));
    }
    //date piker event registration end
    jQuery(document).ready(function() {
        caseResources.getCaseCompanies(<?php echo (int) $legalCase["id"];?>);
        caseResources.getCaseContacts(<?php echo (int) $legalCase["id"];?>);
    });
    var legalCaseIdView = <?php echo (int) $legalCase["id"];?>;
        jQuery('#case_type_id', container).change(function() {
            assignmentPerType(this.value, 'matter', container);
        });
    var lookupDetailsRequestedBy = {
        'lookupField': jQuery('#lookup-requested-by', container),
        'errorDiv': 'requestedBy',
        'hiddenId': '#requested-by',
        'resultHandler': setRequestedByToForm
    };
    lookUpContacts(lookupDetailsRequestedBy, container);
    var lookupDetailsReferredBy = {
        'lookupField': jQuery('#referredByLookup', container),
        'errorDiv': 'referredBy',
        'hiddenId': '#referredBy',
        'resultHandler': setReferredByToForm
    };
    lookUpContacts(lookupDetailsReferredBy, container);

    function internalRefNumLink(targeturl) {
        var internalReference = document.getElementById("internalReference");
        internalReference.select();
        internalReference.setSelectionRange(0, 99999); /* For mobile devices */
        document.execCommand("copy");
        window.open(targeturl, '_blank');
    }
   
</script>
<style>
    #main-content-side .main-content-section {
        height: calc(100vh - 110px);
        margin-top: 54px;
    }
    #sub-menu-sidebar .left-menu{
        height: calc(100vh - 116px);
        margin-top: 60px !important;
    }
    #sub-menu-sidebar .left-menu .sub-menu a{
        width: 100%;
        display: block;
    }
    #main-container{
        padding: 0 !important;
    }
    .wrap-margin-bottom{
        margin-bottom: 0 !important;
    }
    .main-offcanvas{
        margin-top: 0 !important;
    }
    <?php
    if ($this->session->userdata("AUTH_language") != "english") {?>
    @media only screen and (max-width:1550px){
        .btn-meet-now a{
            text-overflow: ellipsis;
            overflow: hidden;
            white-space: nowrap;
            width: 6vw;
            font-size: 9px;
            display: inline-block;
        }
    }
    @media only screen and (max-width:1500px){
        .btn-meet-now a{
            text-overflow: ellipsis;
            overflow: hidden;
            white-space: nowrap;
            width: 5vw;
            font-size: 9px;
            display: inline-block;
        }
    }
    @media only screen and (max-width:1400px){
        .btn-meet-now a{
            text-overflow: ellipsis;
            overflow: hidden;
            white-space: nowrap;
            width: 3.4vw;
            font-size: 9px;
            display: inline-block;
        }
    }
    @media only screen and (max-width:1300px){
        .btn-meet-now a{
            text-overflow: ellipsis;
            overflow: hidden;
            white-space: nowrap;
            width: 3.2vw;
            font-size: 9px;
            display: inline-block;
        }
    }
    @media only screen and (max-width:1280px){
        .btn-meet-now a{
            text-overflow: ellipsis;
            overflow: hidden;
            white-space: nowrap;
            width: 2.5vw;
            font-size: 9px;
            display: inline-block;
        }
    }
    @media only screen and (max-width:1240px){
        .btn-meet-now a{
            text-overflow: ellipsis;
            overflow: hidden;
            white-space: nowrap;
            width: 1.7vw;
            font-size: 9px;
            display: inline-block;
        }
    }
    <?php
    }
    ?>
    .priority-critical{
        height: 20px
    }
</style>