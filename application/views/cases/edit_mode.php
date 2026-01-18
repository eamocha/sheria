
                                    <h2 class="box-title box-shadow_container ">
                                        <i class="spr spr-matter"></i>
                                        <?php echo $this->lang->line("case_public_info");?>
                                   </h2>
                                    <div class="col-md-12 box-shadow_container padding-15">
                                        <div class="row col-md-12 no-padding no-margin">
                                            <div class="form-group col-md-6 no-padding row no-margin">
                                                <label class="control-label col-md-4 no-padding"><?php echo $this->lang->line($category . "reference_case");?> </label>
                                                <div class="col-xs-12 col-md-8">
                                                    <div class="input-group">
                                                        <?php echo form_input(["dir" => "auto", "name" => "internalReference", "id" => "internalReference", "class" => "form-control", "value" => $legalCase["internalReference"]]);
                                                        if (isset($systemPreferences["AllowInternalRefLink"]) && $systemPreferences["AllowInternalRefLink"] == 1) {   ?>
                                                            <span class="input-group-addon no-border">
                                                                            <a href="javascript:void(0);" onclick="internalRefNumLink('<?php echo $systemPreferences["InternalRefLink"];?>');" class="icon-alignment no-border no-margin no-padding btn btn-link">  <i class="fa fa-external-link"></i></a>
                                                                        </span>
                                                            <?php
                                                        }?>
                                                        <div data-field="internalReference" class="inline-error d-none"></div>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="form-group col-md-6 no-padding-left row no-margin">
                                                <label class="control-label col-md-4 no-padding"><?php echo $this->lang->line("status");?></label>
                                                <div class="col-xs-12 col-md-8 no-padding-right padding-left-30"><?php $currentStatus = $legalCase["closedOn"]? "<a class='btn btn-danger' href='javascript:;'>".  $this->lang->line("closed")."</a>": "<a class='btn btn-success' href='javascript:;'>".  $this->lang->line("ongoing")."</a>";?>
                                                    <lable class="label-normal-style"><h5 style="width:190px;" title="<?php echo $currentStatus;?>" class="text-center big-text-font-size no-margin mt-10 bold-font-family label-normal-style trim-width-120 widget-status tooltip-title <?php echo str_replace(" ", "-", strtolower($legalCase["workflow_status_category"]));?>"
                                                            <?php echo $this->common_functions->detect_is_rtl($currentStatus) ? 'dir="rtl"' : "";?>>
                                                            <?php echo $currentStatus; ?> </h5>
                                                    </lable>
                                                </div>
                                            </div>

                                        </div>
                                    </div>

                                    <div class="form-group col-md-12 no-padding">
                                        <div class="row col-md-12 no-padding no-margin">
                                            <div class="form-group col-md-12 no-padding row no-margin">
                                                <div class="col-md-1 no-padding">
                                                    <label class="control-label required">
                                                        <?php echo $this->lang->line("subject_Case");?>
                                                    </label>
                                                </div>
                                                <div class="col-xs-12 col-md-11">
                                                    <?php echo form_input("subject", $legalCase["subject"], 'id="subject" class="form-control" dir="auto" autocomplete="stop" data-validation-engine="validate[required,maxSize[254],funcCall[validateFormat]]"');?>
                                                    <div data-field="subject" class="inline-error d-none"></div>
                                                </div>
                                            </div>
                                            <div class="form-group col-md-12 no-padding row no-margin">
                                                <?php $case_scenario_general = "scenario_case_types_"; $case_scenario_type = $legalCase["category"] == "Litigation" ? "litigation" : "corporate"; ?>
                                                <div class="col-md-2 no-padding">
                                                    <label class="control-label required">  <?php echo $case_category== "Matter"?$this->lang->line("adr_type"):$this->lang->line("case_type_case");?></label>
                                                    <a href="javascript:void();" onclick="quickAdministrationDialog('case_types', jQuery('#newCaseFormDialog'), true, '<?php echo $case_scenario_type;?>');" class="btn btn-link px-0"><i class="fa fa-square-plus p-1 font-18 purple_color"> </i></a>
                                                </div>
                                                <div class="col-xs-12 col-md-9">
                                                    <?php echo form_dropdown("case_type_id", $Case_Types, $legalCase["case_type_id"], "id=\"case_type_id\" data-validation-engine=\"validate[required]\" class=\"form-control\" data-field=\"administration-case_types\" onchange=\"checkRelatedFields({case_id : '" . (int) $legalCase["id"] . "' ,category: '" . $legalCase["category"] . "', old_type: '" . $legalCase["case_type_id"] . "', new_type: jQuery(this).val()});\"");?>
                                                    <div data-field="case_type_id" class="inline-error d-none"></div>
                                                </div>
                                            </div>
                                        </div>
                                        <!--- parties-->

                                        <?php $parties['parties']=$relatedOpponentData;
                                        $parties['case_id']=$case_id;
                                      //  $this->load->view("cases/parties/view", $parties);   ?>
                                        <div class="row col-md-12 no-padding no-margin row no-margin">

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
                                            <div class="form-group col-md-6 no-padding row no-margin">
                                                <label class="control-label col-md-4 no-padding">
                                                    <?php     echo $this->lang->line("client_name");
                                                    $companyLinkHref = site_url("companies/tab_company/" . $clientData["member_id"]??=0);
                                                    $clientLinkHref = $clientData["type"]== "Company" ? $companyLinkHref : site_url("contacts/edit/" . $clientData["member_id"]??=0);
                                                    ?>
                                                    <a href="<?php    echo $clientLinkHref;?>" id="clientLinkId" class="icon-alignment <?php    echo $clientData["member_id"] && $clientCompanyCategory != "Group" ? "" : "d-none";?>"><i class="fa fa-external-link"></i></a>
                                                </label>
                                                <div class="row m-0 col-md-8 no-padding">
                                                    <div class="col-md-3 no-padding-right">
                                                        <select name="clientType" id="client-type" class="form-control select-picker company-contact-select" tabindex="-1" data-iconBase="fa" data-tickIcon="fa-check">
                                                            <option data-content="<i class='fa fa-building purple_color' title='<?php echo $this->lang->line("company");?>'></i>" value="company" <?php    echo $clientData["type"] == "Company" ? "selected='selected'" : "";?>></option>
                                                            <option data-content="<i class='fa fa-user purple_color' title='<?php    echo $this->lang->line("contact");?>'></i>" value="contact" <?php    echo $clientData["type"] == "Person" ? "selected='selected'" : "";?>></option>
                                                        </select>
                                                    </div>
                                                    <div class="col-md-9">
                                                        <?php
                                                        echo form_input(["name" => "contact_company_id", "id" => "contact-company-id", "value" => $clientData["member_id"], "type" => "hidden"]);
                                                        echo form_input(["name" => "", "id" => "client-lookup", "value" => $clientData["name"]??=null, "class" => "form-control lookup", "placeholder" => $this->lang->line("start_typing"), "title" => $clientData["foreignName"]??=null, "onblur" => "checkLookupValidity(jQuery(this), jQuery('#contact_company_id', '#legalCaseAddForm')); if (this.value === '') { jQuery('#clientLinkId').addClass('d-none');jQuery(this).attr('title', ''); }"]);
                                                        ?>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <h5 class="d-flex justify-content-center">Vs.</h5>
                                        <div class="form-group col-md-12 no-padding" id="opponents-container">
                                            <?php    echo form_input(["name" => "opponentsCount", "id" => "opponents-count", "value" => count($relatedOpponentData), "type" => "hidden"]);    $count = 1;    foreach ($relatedOpponentData as $opponentData) {       ?>
                                                <div class="row m-0 col-md-12 no-padding opponent-div" id="opponent-<?php echo $count;?>">

                                                    <div class="row m-0 form-group col-md-6 no-padding">
                                                        <label class="control-label col-md-4 no-padding flex-center-inline">
                                                            <?php        echo $this->lang->line("opponent_position");       ?>
                                                            <a href="javascript:void(0)" onclick="quickAdministrationDialog('case_opponent_positions', jQuery('#newCaseFormDialog'), true, false, false, jQuery('[data-field-id=opponent-position-<?php echo $count;?>]'));" class="icon-alignment btn btn-link px-0 opponent-position-quick-add"><i class="icon fa fa-square-plus p-1 font-18 purple_color"> </i></a>
                                                        </label>
                                                        <div class="col-md-8 no-padding">
                                                            <?php
                                                            echo form_dropdown("opponent_position[]", $opponent_positions, $opponentData["opponent_position"], 'id="opponent-position" class="form-control select-picker" data-live-search="true" data-field="administration-case_opponent_positions" data-field-id="opponent-position-" . $count . "');
                                                            ?>
                                                        </div>
                                                    </div>
                                                    <div class="row m-0 form-group col-md-6 no-padding">
                                                        <label class="control-label col-md-4 no-padding">
                                                             <span class="opponent-label">
                                                                 <?php        echo $this->lang->line("opponent") . " (" . $count;?>)
                                                             </span>
                                                            <?php
                                                            $companyLinkHref = site_url("companies/tab_company/" . $opponentData["opponent_member_id"]);
                                                            $opponentLinkHref = $opponentData["opponent_member_type"] == "company" ? $companyLinkHref : site_url("contacts/edit/" . $opponentData["opponent_member_id"]);
                                                            ?>
                                                            <a href="<?php echo $opponentLinkHref;?>" class="icon-alignment opponentLinkId <?php  echo $opponentData["opponent_member_id"] && $opponentData["opponentCompanyCategory"] != "Group" ? "" : "d-none";?>"><i class="fa fa-external-link"></i></a>
                                                            <a href="javascript:void(0);" class="icon-alignment btn btn-link delete-opponent delete-icon no-padding-left no-padding-right no-padding-top" onclick="opponentDelete('<?php echo $count;?>', '#edit-legal-case-container', event);"><i class="fa fa-trash light_red-color"></i></a>
                                                        </label>
                                                        <div class="row m-0 col-md-8 no-padding">
                                                            <div class="col-md-3 no-padding-right">
                                                                <select name="opponent_member_type[]" id="opponent-member-type" class="form-control company-contact-select" tabindex="-1" data-iconBase="fa" data-tickIcon="fa-check">
                                                                    <option data-content="<i class='fa fa-building purple_color' title='<?php echo $this->lang->line("company");?>'></i>" value="company" <?php echo $opponentData["opponent_member_type"] == "company" ? "selected='selected'" : "";?>></option>
                                                                    <option data-content="<i class='fa fa-user purple_color' title='<?php echo $this->lang->line("contact");?> '></i>" value="contact" <?php echo $opponentData["opponent_member_type"] == "contact" ? "selected='selected'" : "";?>></option>
                                                                </select>
                                                            </div>
                                                            <div class="col-md-9">
                                                                <?php echo form_input(["name" => "opponent_member_id[]", "id" => "opponent-member-id", "value" => $opponentData["opponent_member_id"], "type" => "hidden"]);
                                                                echo form_input(["name" => "", "id" => "opponent-lookup", "value" => $opponentData["opponentName"], "class" => "form-control lookup", "placeholder" => $this->lang->line("start_typing"), "title" => $opponentData["opponentForeignName"], "onblur" => "if (this.value === '') { jQuery('.opponentLinkId','#opponent-" . $count . "').addClass('d-none');jQuery(this).attr('title', ''); }"]);
                                                                ?>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div data-field="opponent_member_id_<?php echo $count;?>" class="inline-error d-none"></div>
                                                <?php
                                                $count++;
                                            }   ?>
                                            <div class="col-md-12 no-padding">
                                                <a href="javascript:;" onclick="opponentAddContainer('#edit-legal-case-container', event, '<?php    echo $max_opponents;?>');" class=""><i class="fa fa-plus no-padding"> </i> <?php    echo $this->lang->line("add_another_opponent");    ?> </a>
                                            </div>
                                        </div>
                                        <!-- case summary -->
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
                                      <?php if ($legalCase["category"] == "Litigation") { ?>
                                         <div class="col-md-12 no-padding ">
                                            <div class="form-group no-padding">
                                                <label class="control-label col-md-3 no-padding"><?php echo $this->lang->line("instructions");?> </label>
                                                <div class="col-md-12 no-padding">
                                                    <?php echo form_textarea(["name" => "statusComments", "id" => "statusComments", "class" => "form-control min-height-120 resize-vertical-only", "value" => $legalCase["statusComments"], "rows" => "2", "cols" => "0"]); ?>
                                                </div>
                                                <div data-field="statusComments" class="inline-error d-none"></div>
                                            </div>
                                        </div>
                                        <?php } ?>

                                        <div class="row col-md-12 no-padding no-margin row no-margin">

                                            <div class="form-group col-md-6 no-padding-left row no-margin">
                                                <label class="control-label col-md-4 no-padding"><?php
                                                    echo $this->lang->line("matter_container");
                                                    if ($case_has_multi_containers) {   ?>
                                                        <span class="icon-alignment d-inline-block padding-10 purple_color">
                                                        <span id="multi_containers_tooltip" class="tooltipTable" title='<?php   echo sprintf($this->lang->line("related_containers_warning_modal"), site_url("/case_containers/index/" . $id));?>'><i class="fa-solid fa-circle-question purple_color"></i></span>
                                                        </span><?php
                                                    }
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
                                            <div class="form-group col-md-6 no-padding row no-margin">
                                                <label class="control-label col-md-4 no-padding"><?php echo $this->lang->line("priority");?> </label>
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
                                            <div class="form-group col-md-6 no-padding-right no-padding-left row no-margin">
                                                <?php echo form_input(["value" => $legalCase["legal_case_stage_id"], "id" => "current-case-stage-id", "type" => "hidden"]);
                                                $case_scenario_general = "scenario_case_stages_";?>
                                                <label class="control-label col-md-4 no-padding"><?php echo $this->lang->line($category . "case_stage");?>
                                                    <a href="javascript:void();" onclick="quickAdministrationDialog('case_stages', jQuery('#newCaseFormDialog'), true, '<?php echo $case_scenario_type;?>'<?php echo $legalCase["category"] == "Litigation" ? ", changeLitigationStage" : "";?>)" class="btn btn-link px-0"><i class="fa fa-square-plus p-1 font-18 purple_color"> </i></a>
                                                </label>
                                                <?php if (strtolower($legalCase["category"])== "litigation") {   ?>
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
                                            <?php if ($legalCase["category"] == "Litigation") {   ?>
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
                                                        <?php    echo $this->lang->line("client_name");    $companyLinkHref = site_url("companies/tab_company/" . $clientData["member_id"]);    $clientLinkHref = $clientData["type"] == "Company" ? $companyLinkHref : site_url("contacts/edit/" . $clientData["member_id"]);   ?>
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
                                                            <?php
                                                            echo form_input(["name" => "contact_company_id", "id" => "contact-company-id", "value" => $clientData["member_id"], "type" => "hidden"]);
                                                            echo form_input(["name" => "", "id" => "client-lookup", "value" => $clientData["name"], "class" => "form-control lookup", "placeholder" => $this->lang->line("start_typing"), "title" => $clientData["foreignName"], "onblur" => "checkLookupValidity(jQuery(this), jQuery('#contact_company_id', '#legalCaseAddForm')); if (this.value === '') { jQuery('#clientLinkId').addClass('d-none');jQuery(this).attr('title', ''); }"]);  ?>
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
                                        <?php if ($legalCase["category"] == "Litigation") {?>
                                            <div class="row col-md-12 no-padding no-margin row no-margin d-none">
                                                <div class="form-group col-md-6 no-padding row no-margin">
                                                    <label class="control-label col-md-4 no-padding">
                                                        <?php    echo $this->lang->line("judgment_value");   ?>
                                                        <div class="smaller_font display-inline-important">
                                                            <?php echo isset($systemPreferences["caseValueCurrency"]) && $systemPreferences["caseValueCurrency"] != "" ? "(" . $systemPreferences["caseValueCurrency"] . ")" : "";?>
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
                                                        <div class="display-inline-important smaller_font"><?php echo isset($systemPreferences["caseValueCurrency"]) && $systemPreferences["caseValueCurrency"] != "" ? "(" . $systemPreferences["caseValueCurrency"] . ")" : "";                                           ?>
                                                        </div>
                                                    </label>
                                                    <div class="col-xs-12 col-md-8">
                                                        <?php    echo form_input(["class" => "form-control", "id" => "recoveredValue", "name" => "recoveredValue"], $legalCase["recoveredValue"] + 0);   ?>
                                                        <div data-field="recoveredValue" class="inline-error d-none"></div>
                                                    </div>
                                                </div>
                                            </div>
                                            <!-- additional fields -->
                                            <div class="row col-md-12 no-padding no-margin row no-margin ">
                                                <div class="form-group col-md-6 no-padding row no-margin">
                                                    <label class="control-label col-md-4 no-padding tooltip-title" title="<?php echo $this->lang->line("risk_profile_helper_text"); ?>">
                                                        <?php    echo $this->lang->line("risk_profile");   ?> <i class="fa-solid fa-circle-question purple_color"></i></label>
                                                    <div class="col-xs-12 col-md-8">
                                                        <?php    echo form_dropdown("legal_case_risk_profile_id", $successProbabilities, $legalCase["legal_case_success_probability_id"], 'id="legal_case_risk_profile_id" class="form-control"');  ?>
                                                        <div data-field="legal_case_risk_profile_id" class="inline-error d-none"></div>
                                                    </div>
                                                </div>
                                                <div class="form-group col-md-6 no-padding row no-margin ">
                                                    <label class="control-label col-md-4 no-padding tooltip-title" title="<?php echo $this->lang->line("approach_strategy_helper_text");?>">
                                                        <?php    echo $this->lang->line("approach_strategy");    ?> <i class="fa-solid fa-circle-question purple_color"></i> </label>
                                                    <div class="col-xs-12 col-md-8">
                                                        <?php    echo form_dropdown("legal_case_approach_strategy_id", $successProbabilities, $legalCase["legal_case_success_probability_id"], 'id="legal_case_approach_strategy_id" class="form-control"');  ?>
                                                        <div data-field="legal_case_risk_strategy_id" class="inline-error d-none"></div>
                                                    </div>
                                                </div>
                                            </div>
                                            <!-- end additional fields -->
                                        <?php } ?>

                                    </div>
                                    <?php
                                    echo form_fieldset_close();
                                    echo form_close();                                    ?>


                                    <!-- start custom fields -->
                                    <div class="box-section row margin-bottom-15" id="custom_fields_div">
                                        <h2 class="box-title d-none">
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
                                    </div><!-- end custom fields -->
                              