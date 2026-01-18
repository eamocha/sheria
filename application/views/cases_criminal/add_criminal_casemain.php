<div class="primary-style">
    <div class="modal fade modal-container modal-resizable">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title"><?php  echo $this->lang->line("case_intake_form");?></h4>
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                </div>
                <div class="modal-body" id="modal-body-add-litigation">
                    <div id="litigation-add-form-container" class="col-md-12 m-0 p-0 padding-10"><?php
                        echo form_open(current_url(), 'class="form-horizontal" novalidate id="litigation-add-form"');
                        echo form_input(["id" => "action", "value" => "add_criminal_case", "type" => "hidden"]);
                        echo form_input(["name" => "send_notifications_email", "id" => "send_notifications_email", "value" => $hide_show_notification, "type" => "hidden"]);
                        echo form_input(["id" => "all-users-provider-group", "value" => $allUsersProviderGroupId, "type" => "hidden"]);
                        echo form_input(["name" => "externalizeLawyers", "id" => "externalizeLawyers", "value" => "no", "type" => "hidden"]);
                        echo form_input(["name" => "category", "id" => "category", "value" => $selected_values["category"], "type" => "hidden"]);
                        echo form_input(["id" => "arrival-date-hidden", "value" => $selected_values["today"], "type" => "hidden"]);
                        echo form_input(["id" => "filed-on-hidden", "type" => "hidden"]);
                        echo form_input(["id" => "due-date-hidden", "value" => isset($selected_values["dueDate"]) ? $selected_values["dueDate"] : "", "type" => "hidden"]);
                        echo form_input(["name" => "assignment_id", "id" => "assignment-id", "value" => $assignments["id"]??=0, "type" => "hidden"]);
                        echo form_input(["name" => "assignment_relation", "id" => "assignment-relation", "value" => $assignments["assignment_relation"]??=0, "type" => "hidden"]);
                        echo form_input(["name" => "user_relation", "id" => "user-relation", "value" => $assignments["user_id"]??=0, "type" => "hidden"]);
                        echo form_input(["id" => "related-assigned-team", "value" => $assignments["assigned_team"], "type" => "hidden"]);
                        ?>
                        <div class="col-md-12 p-0" id="required-fields-litigation-demo">
                            <div class="form-group col-md-12 d-flex p-0">
                                <label class="control-label col-md-3 pr-0 col-xs-5"><?php echo $this->lang->line("case_inquiry_no");?></label>
                                <div class="col-md-8 pr-0 col-xs-10">
                                    <div class="col-md-5 p-0"><?php echo form_input(["dir" => "auto", "name" => "internalReference", "class" => "form-control", "value" => $selected_values["container_common_fields"]["internal_reference"] ?? ""]);?> </div>
                                    <div data-field="internalReference" class="inline-error d-none"></div>
                                </div>
                            </div>
                            <div class="form-group col-md-12 p-0 d-flex autocomplete-helper">
                                <div class="col-md-3 pr-0 col-xs-1" >&nbsp;</div>
                                <div class="col-md-8 pr-0 col-xs-10">
                                    <div class="inline-text"><?php echo $this->lang->line("helper_reference");?></div>
                                </div>
                            </div>

                            <div class="col-md-12 p-0">
                                <div class="form-group col-md-12 p-0 col-xs-12 row m-0 mb-10" id="required-fields-litigation-demo">
                                    <label class="control-label col-md-3 pr-0 required col-xs-5"><?php echo $this->lang->line("criminal_case_subject");?></label>
                                    <div class="col-md-8 pr-0"><?php echo form_input(["dir" => "auto", "name" => "subject", "id" => "subject", "class" => "form-control", "autocomplete" => "stop", "value" => $selected_values["container_common_fields"]["containerSubject"] ?? ""]);?>
                                        <div data-field="subject" class="inline-error d-none"></div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-12 p-0 form-group row m-0 mb-10">
                                <label class="control-label col-md-3 pr-0 required col-xs-5"><?php echo $this->lang->line("criminal_case_type");?></label>
                                <div class="col-md-8 d-flex pr-0 col-xs-12">
                                    <div class="p-0 margin-bottom-5 col-md-11 col-xs-10">
                                        <select name="case_type_id"  id="case_type_id" class="form-control select-picker" data-live-search="true" data-field="administration-case_types" data-size="<?php echo $this->session->userdata("max_drop_down_length");?>">
                                            <option value=""><?php echo $this->lang->line("none");?></option>
                                            <?php foreach ($litigationCaseTypes as $key => $value) {
                                                $selected = "";
                                                if ($systemPreferences["caseTypeLitigationId"] == $value["id"] ||
                                                    $selected_values["container_common_fields"]["containerCaseType"]??=0 == $value["id"])
                                                {
                                                    $selected = "selected='selected'";
                                                }
                                                ?>
                                                <option value="<?php echo $value["id"];?>" <?php echo $selected;?>     litigationSLA='<?php echo $value["litigationSLA"];?>' ><?php  echo $value["name"];?></option>
                                            <?php } ?>
                                        </select>
                                        <div data-field="case_type_id" class="inline-error d-none"></div>
                                    </div>
                                    <div class="col-md-1 p-0 col-xs-2">
                                        <a href="javascript:;" onclick="quickAdministrationDialog('case_types', jQuery('#litigation-dialog'), true, 'litigation');" class="btn btn-link"><i class="fa-solid fa-square-plus p-1 font-18"> </i></a>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group col-md-12 p-0 row m-0 mb-10" id="arrival-date-container">
                                <label class="control-label col-md-3 pr-0 col-xs-5 required"><?php echo $this->lang->line("date_reported");?></label>
                                <div class="col-md-8 pr-0 col-xs-10">
                                    <div class="row m-0">
                                        <div class="input-group date col-md-5 p-0 date-picker" id="arrival-date-add-new">
                                            <?php echo form_input(["name" => "caseArrivalDate", "id" => "arrival-date-input", "placeholder" => "YYYY-MM-DD", "value" => $selected_values["container_common_fields"]["arrival_date"] ?? $selected_values["today"], "class" => "form-control"]);?>
                                            <span class="input-group-addon"><i class="fa fa-calendar purple_color p-9 cursor-pointer-click"></i></span>
                                        </div><?php  if ($systemPreferences["hijriCalendarConverter"]) {?>
                                            <div class="col-md-3 p-0 col-xs-2 visualize-hijri-date"><a href="javascript:;" id="date-conversion" onclick="HijriConverter(jQuery('#arrival-date-add-new', '#litigation-dialog'));" title="<?php echo $this->lang->line("hijri_date_converter");?>" class="btn btn-link"><?php     echo $this->lang->line("hijri");?></a></div>
                                            <?php
                                        }?>
                                        <div data-field="caseArrivalDate" class="inline-error d-none"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-12 p-0 row m-0 mb-10" id="optional-fields-litigation-demo">
                            <div class="form-group col-md-12 p-0 col-xs-12 row m-0 mb-10">
                                <label class="control-label col-md-3 pr-0 col-xs-5"><?php echo $this->lang->line("complaint_inquiry_details"); ?>&nbsp; <span class="tooltip-title" title="<?php echo $this->lang->line("criminal_case_details_help");?>"><i class="fa-solid fa-circle-question purple_color"></i></span></label>
                                <div class="col-md-8 pr-0">
                                    <?php echo form_textarea(["name" => "description", "id" => "description", "class" => "form-control", "rows" => "5", "cols" => "0", "value" => $selected_values["container_common_fields"]["containerDescription"] ?? ""]);?>
                                    <!-- <span class="input-group-addon"><i class="fa fa-calendar purple_color p-9 cursor-pointer-click"></i></span> -->
                                    <div data-field="description" class="inline-error d-none"></div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-12 p-0 row m-0 mb-10" id="optional-fields-litigation-demo">
                            <div class="form-group col-md-12 p-0 col-xs-12 row m-0 mb-10">
                                <label class="control-label col-md-3 pr-0 col-xs-5"><?php echo $this->lang->line("action_taken"); ?>&nbsp; <span class="tooltip-title" title="<?php echo $this->lang->line("action_taken_help");?>"><i class="fa-solid fa-circle-question purple_color"></i></span></label>
                                <div class="col-md-8 pr-0">
                                    <?php echo form_textarea(["name" => "statusComments", "id" => "statusComments", "class" => "form-control min-height-120 resize-vertical-only", "value" => $selected_values["container_common_fields"]["containerDescription"] ?? "", "rows" => "2", "cols" => "0"]); ?>
                                </div>
                                <div data-field="statusComments" class="inline-error d-none"></div>
                            </div>
                        </div>

                        <div class="form-group col-md-12 p-0 row m-0 mb-10">
                            <label class="control-label col-md-3 pr-0 col-xs-5"><?php echo $this->lang->line("complainant");?></label>
                            <div class="col-md-8 pr-0 col-xs-10">
                                <div class="col-md-12 p-0">
                                    <select name="clientType" id="client-type" class="form-control select-picker" tabindex="-1">
                                        <option value="company" <?php echo isset($selected_values["container_common_fields"]["client_data"]["type"]) ? $selected_values["container_common_fields"]["client_data"]["type"] == "Company" ? "selected='selected'" : "" : "";?>><?php echo $this->lang->line("company_or_group");?></option>
                                        <option value="contact" <?php echo isset($selected_values["container_common_fields"]["client_data"]["type"]) ? $selected_values["container_common_fields"]["client_data"]["type"] == "Person" ? "selected='selected'" : "" : "";?>><?php echo $this->lang->line("contact");?></option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-12 form-group p-0 row m-0 mb-10">
                            <label class="control-label pr-0 col-md-3 col-xs-12">&nbsp;</label>
                            <div class="col-md-8 pr-0 col-xs-10">
                                <div class="col-md-12 p-0"><?php echo form_input(["name" => "contact_company_id", "id" => "contact-company-id", "value" => $selected_values["container_common_fields"]["client_data"]["member_id"] ?? "", "type" => "hidden"]);?>
                                    <?php echo form_input(["name" => "clientLookup", "id" => "client-lookup", "value" => $selected_values["container_common_fields"]["client_data"]["name"] ?? "", "class" => "form-control lookup", "title" => $this->lang->line("start_typing")]);?>
                                    <div data-field="contact_company_id" class="inline-error d-none"></div>
                                </div>
                            </div>
                            <div class="col-md-12 p-0 d-flex autocomplete-helper">
                                <div class="col-md-3 pr-0 col-xs-1" >&nbsp;</div>
                                <div class="col-md-8 pr-0 col-xs-10">
                                    <div class="inline-text"><?php echo $this->lang->line("helper_autocomplete");?></div>
                                </div>
                            </div>
                        </div>
                        <div class="form-group col-md-12 p-0 row m-0 mb-10 d-none">
                            <label class="control-label col-md-3 pr-0 col-xs-5"><?php echo $this->lang->line("client_position");?></label>
                            <div class="col-md-8 pr-0 d-flex col-xs-12">
                                <div class="col-md-11 p-0 col-xs-10">
                                    <?php echo form_dropdown("legal_case_client_position_id", $clientPositions, $selected_values["container_common_fields"]["containerClientPoisiton"] ?? "", 'id="legal_case_client_position_id" class="form-control select-picker" data-live-search="true" data-field="administration-case_client_positions" data-size="'. $this->session->userdata("max_drop_down_length").'"');?>
                                </div>
                                <div class="col-md-1 p-0 col-xs-2">
                                    <a href="javascript:;"  onclick="quickAdministrationDialog('case_client_positions', jQuery('#litigation-dialog'), true);" class="btn btn-link"><i class="fa-solid fa-square-plus p-1 font-18"> </i></a>
                                </div>
                            </div>
                        </div>
                        <div id="opponents-container"><?php echo form_input(["name" => "opponentsCount", "id" => "opponents-count", "value" => isset($selected_values["container_common_fields"]["opponent_count"]) && 0 < $selected_values["container_common_fields"]["opponent_count"] ? $selected_values["container_common_fields"]["opponent_count"] : "1", "type" => "hidden"]);?>
                            <?php if (isset($selected_values["container_common_fields"]["opponent_data"]) && !empty($selected_values["container_common_fields"]["opponent_data"])) {
                                $count = 1;
                                foreach ($selected_values["container_common_fields"]["opponent_data"] as $opponent_record) {
                                    ?>
                                    <div class="opponent-div" id="opponent-<?php echo $count;?>">
                                        <div class="col-md-12 p-0 form-group d-flex margin-bottom-10">
                                            <label class="control-label col-md-3 pr-0 col-xs-5 opponent-label"><?php echo $this->lang->line("accused") . " (" . $count;?>)</label>
                                            <div class="col-md-8 pr-0 col-xs-10">
                                                <div class="col-md-5 p-0">
                                                    <select name="opponent_member_type[]" id="opponent-member-type" class="form-control">
                                                        <option value="company" <?php  echo $opponent_record["opponent_member_type"] == "company" ? "selected='selected'" : "";?>><?php  echo $this->lang->line("company_or_group");?></option>
                                                        <option value="contact" <?php echo $opponent_record["opponent_member_type"] == "contact" ? "selected='selected'" : "";?>><?php  echo $this->lang->line("contact");?></option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-12 form-group p-0 margin-bottom-5">
                                            <label class="control-label pr-0 col-md-3 col-xs-12">&nbsp;</label>
                                            <div class="col-md-8 pr-0 col-xs-10">
                                                <div class="col-md-7 p-0"><?php  echo form_input(["name" => "opponent_member_id[]", "id" => "opponent-member-id", "value" => $opponent_record["opponent_member_id"], "type" => "hidden"]);?>
                                                    <?php   echo form_input(["name" => "opponentLookup[]", "id" => "opponent-lookup", "value" => $opponent_record["opponentName"], "class" => "form-control lookup", "title" => $this->lang->line("start_typing")]);?>
                                                </div>
                                                <div class="col-sm-13 col-xs-4 col-md-1 pr-0 padding-10 delete-icon <?php   echo $selected_values["container_common_fields"]["opponent_count"] == 1 ? "d-none" : "";?>">
                                                    <a href="javascript:;" class="delete-opponent" onclick="opponentDelete('<?php   echo $count;?>', '#litigation-dialog', event);"><i class="fa-solid fa-xmark red"></i></a>
                                                </div>
                                                <div data-field="opponent_member_id_<?php   echo $count;?>" class="inline-error d-none"></div>
                                            </div>
                                            <div class="form-group col-md-12 p-0 autocomplete-helper">
                                                <div class="col-md-3 pr-0 col-xs-1" >&nbsp;</div>
                                                <div class="col-md-8 pr-0 col-xs-10">
                                                    <div class="inline-text"><?php         echo $this->lang->line("helper_autocomplete");?></div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-12 p-0 form-group margin-bottom-10 d-none">
                                    <label class="control-label col-md-3 pr-0 col-xs-5"><?php    echo $this->lang->line("opponent_position");?></label>
                                    <div class="col-md-8 pr-0 col-xs-12">
                                        <div class="col-md-7 p-0 col-xs-10"><?php  echo form_dropdown("opponent_position[]", $opponent_positions, $opponent_record["opponent_position"], 'id="opponent-position" class="form-control select-picker" data-live-search="true" data-field="administration-case_opponent_positions" data-field-id="opponent-position-" . $count . "" data-size="'. $this->session->userdata("max_drop_down_length").'"');?></div>
                                        <div class="col-md-1 p-0 col-xs-2">
                                            <a href="javascript:;" onclick="quickAdministrationDialog('case_opponent_positions', jQuery('#litigation-dialog'), true, false, false, jQuery('[data-field-id=opponent-position-<?php echo $count;?>]'));" class="btn btn-link opponent-position-quick-add"><i class="fa-solid fa-square-plus p-1 font-18"> </i></a>
                                        </div>
                                    </div>
                                    </div><?php
                                    $count++;
                                }
                            } else {
                                ?>
                                <div class="opponent-div" id="opponent-1">
                                    <div class="col-md-12 p-0 d-flex form-group margin-bottom-10">
                                        <label class="control-label col-md-3 pr-0 col-xs-5 opponent-label"><?php echo $this->lang->line("accused") . " (1)";?></label>
                                        <div class="col-md-8 pr-0 col-xs-10">
                                            <div class="col-md-12 p-0">
                                                <select name="opponent_member_type[]" id="opponent-member-type" class="form-control">
                                                    <option value="company"><?php    echo $this->lang->line("company_or_group");?></option>
                                                    <option value="contact"><?php     echo $this->lang->line("contact");?></option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-12 form-group d-flex p-0 margin-bottom-5">
                                        <label class="control-label pr-0 col-md-3 col-xs-12">&nbsp;</label>
                                        <div class="col-md-8 pr-0 d-flex col-xs-10">
                                            <div class="col-md-11 p-0"><?php    echo form_input(["name" => "opponent_member_id[]", "id" => "opponent-member-id", "value" => "", "type" => "hidden"]);?>
                                                <?php  echo form_input(["name" => "opponentLookup[]", "id" => "opponent-lookup", "value" => "", "class" => "form-control lookup", "title" => $this->lang->line("start_typing")]);?>
                                            </div>
                                            <div class="col-sm-13 col-xs-4 col-md-1 pr-0 padding-10 delete-icon d-none">
                                                <a href="javascript:;" class="delete-opponent" onclick="opponentDelete('1', '#litigation-dialog', event);"><i class="fa-solid fa-xmark red"></i></a>
                                            </div>
                                            <div data-field="opponent_member_id_1" class="inline-error d-none"></div>
                                        </div>
                                    </div>
                                    <div class="form-group col-md-12 p-0 d-flex autocomplete-helper" id="more-demo-option">
                                        <div class="col-md-3 pr-0 col-xs-1" >&nbsp;</div>
                                        <div class="col-md-8 pr-0 col-xs-10">
                                            <div class="inline-text"><?php     echo $this->lang->line("helper_autocomplete");?></div>
                                        </div>
                                    </div>
                                    <div class="col-md-12 p-0 form-group d-flex margin-bottom-10 ">
                                        <label class="control-label col-md-3 pr-0 col-xs-5 d-none"><?php    echo $this->lang->line("opponent_position");?></label>
                                        <div class="col-md-8 pr-0 d-flex col-xs-12 ">
                                            <div class="col-md-11 p-0 col-xs-10 d-none">
                                                <?php    echo form_dropdown("opponent_position[]", $opponent_positions, "", 'id="opponent-position" class="form-control select-picker" data-live-search="true" data-field="administration-case_opponent_positions" data-field-id="opponent-position-1" data-size="'. $this->session->userdata("max_drop_down_length").'"');?></div>
                                            <div class="col-md-1 p-0 col-xs-2 d-none">
                                                <a href="javascript:;" onclick="quickAdministrationDialog('case_opponent_positions', jQuery('#litigation-dialog'), true, false, false, jQuery('[data-field-id=opponent-position-1]'));" class="btn btn-link opponent-position-quick-add"><i class="fa-solid fa-square-plus p-1 font-18"> </i></a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php }?>
                            <div class="col-md-12 p-0 form-group d-flex margin-bottom-5">
                                <div class="col-md-3 pr-0 col-xs-1" >&nbsp;</div>
                                <div class="col-md-8 col-xs-10 add-more-link">
                                    <a href ="javascript:;" onclick="opponentAddContainer('#litigation-dialog', event, '<?php echo $max_opponents;?>');"><?php echo $this->lang->line("add_more");?></a>
                                </div>
                            </div>
                        </div>
                        <div class="form-group col-md-12 p-0 d-flex <?php echo $assignments["visible_assigned_team"]??="" ? "" : "d-none";?>" id="assigned-team-container">
                            <label class="control-label col-md-3 pr-0 col-xs-5"><?php echo $this->lang->line("provider_group");?></label>
                            <div class="col-md-8 pr-0 col-xs-10">
                                <div class="col-md-12 p-0"><?php echo form_dropdown("provider_group_id", $Provider_Groups, $selected_values["container_common_fields"]["assigned_team"] ?? $assignments["assigned_team"], 'id="provider-group-id" class="form-control select-picker" data-live-search="true" data-size="'. $this->session->userdata("max_drop_down_length").'"');?>
                                    <div data-field="provider_group_id" class="inline-error d-none"></div>
                                </div>
                            </div>
                        </div>
                        <div class="form-group col-md-12 p-0 d-flex <?php echo $assignments["visible_assignee"]??="" ? "" : "d-none";?>" id="assignee-container">
                            <label class="control-label col-md-3 pr-0 col-xs-5"><?php echo $this->lang->line("assignee");?></label>
                            <div class="col-md-8 pr-0 col-xs-10">
                                <div class="col-md-12 p-0">
                                    <?php echo form_dropdown("user_id", $usersProviderGroup, $selected_values["container_common_fields"]["containerAssignee"] ?? (isset($assignments["user_id"]) ? $assignments["user_id"] : ""), 'id="user-id" data-live-search="true" class="form-control select-picker" data-size="'. $this->session->userdata("max_drop_down_length").'"');?>
                                    <div data-field="user_id" class="inline-error d-none"></div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-12 form-group p-0 case-privacy-container" id="case-privacy-container">
                            <div class="d-flex">
                                <label class="control-label col-md-3 col-xs-12 pr-0"><?php echo $this->lang->line("shared_with");?></label>
                                <div class="col-md-6 col-xs-8 pr-0">
                                    <label class="shared-with-label padding-top7"><?php echo $this->lang->line("everyone");?></label>
                                    <div class="lookup-box-container margin-bottom input-group col-md-12 p-0 d-none users-lookup-container"><?php echo form_input(["id" => "lookup-case-users", "name" => "lookupCaseUsers", "class" => "form-control users-lookup", "title" => $this->lang->line("start_typing")]);?>
                                        <span class="input-group-addon bs-caret users-lookup-icon" id="shared-with-users" onclick="jQuery('#lookup-case-users', '#litigation-dialog').focus();"><span class="caret"></span></span>
                                    </div>
                                    <div id="selected-watchers" class="height-auto m-0 d-none">
                                        <div class="flex-item-box row multi-option-selected-items no-margin w-100 height-30px">
                                            <span> <?php echo $this->lang->line("case_creator");?></span>
                                        </div>
                                        <div class="flex-item-box row multi-option-selected-items no-margin w-100 height-30px">
                                            <span><?php echo $this->lang->line("assignee");?></span>
                                        </div>
                                    </div>
                                    <div data-field="case_watchers" class="inline-error d-none padding-5"></div>
                                </div>
                                <div class="col-md-2 p-0 col-xs-4">
                                    <a class="btn btn-default btn-link float-right" id="privateLink" href="javascript:;" onclick="setAsPrivate(jQuery('#case-privacy-container', '#litigation-dialog'), 'cases', '', '', '', true);"><?php echo $this->lang->line("set_as_private");?></a>
                                    <a class="d-none btn btn-default btn-link float-right" id="publicLink" href="javascript:;" onclick="setAsPublic(jQuery('#case-privacy-container', '#litigation-dialog'), 'cases', true);"><?php echo $this->lang->line("set_as_public");?></a>
                                    <?php echo form_input(["id" => "private", "name" => "private", "value" => "no", "type" => "hidden"]);?>
                                </div>
                            </div>
                            <div class="form-group col-md-12 p-0 d-flex autocomplete-helper d-none">
                                <div class="col-md-3 pr-0 col-xs-1" >&nbsp;</div>
                                <div class="col-md-8 pr-0 col-xs-10">
                                    <div class="inline-text"><?php echo $this->lang->line("helper_autocomplete");?></div>
                                </div>
                            </div>
                        </div>
                        <div class="form-group col-md-12 d-flex p-0" id="filed-on-date-container">
                            <label class="control-label col-md-3 pr-0 col-xs-5 d-none">
                                <?php echo $this->lang->line("filed_on");?>&nbsp; <span class="tooltip-title" title="<?php echo $this->lang->line("litigation_filed_on_helper");?>"><i class="fa-solid fa-circle-question purple_color"></i></span>
                            </label>
                            <div class="col-md-8 pr-0 col-xs-10 d-none">
                                <div class="row m-0">
                                    <div class="input-group date col-md-5 p-0 date-picker" id="filed-on"><?php echo form_input(["name" => "arrivalDate", "id" => "filed-on-input", "placeholder" => "YYYY-MM-DD", "value" => $selected_values["today"], "class" => "form-control"]);?>
                                        <span class="input-group-addon"><i class="fa fa-calendar purple_color p-9 cursor-pointer-click"></i></span>
                                    </div>
                                    <?php if ($systemPreferences["hijriCalendarConverter"]) {?>
                                        <div class="col-md-3 p-0 col-xs-2 visualize-hijri-date"><a href="javascript:;" id="date-conversion" onclick="HijriConverter(jQuery('#filed-on', '#litigation-dialog'));" title="<?php echo $this->lang->line("hijri_date_converter");?>" class="btn btn-link"><?php echo $this->lang->line("hijri");?></a></div>
                                    <?php } ?>
                                    <div data-field="arrivalDate" class="inline-error d-none"></div>
                                </div>
                            </div>
                        </div>
                        <div class="form-group col-md-12 d-flex p-0" id="due-date-container">
                            <label class="control-label col-md-3 pr-0 col-xs-5 d-none"><?php echo $this->lang->line("due_date");?></label>
                            <div class="col-md-8 pr-0 col-xs-10 d-none">
                                <div class="input-group date col-md-5 p-0 date-picker" id="due-date-add-new">
                                    <?php echo form_input(["name" => "dueDate", "id" => "due-date-input", "placeholder" => "YYYY-MM-DD", "class" => "form-control", "value" => isset($selected_values["dueDate"]) ? $selected_values["dueDate"] : ""]);?>
                                    <span class="input-group-addon"><i class="fa fa-calendar purple_color p-9 cursor-pointer-click"></i></span>
                                </div>
                                <?php if ($systemPreferences["hijriCalendarConverter"]) {?>
                                    <div class="col-md-3 p-0 col-xs-2 visualize-hijri-date d-none"><a href="javascript:;" id="date-conversion" onclick="HijriConverter(jQuery('#due-date-add-new', '#litigation-dialog'));" title="<?php echo $this->lang->line("hijri_date_converter");?>" class="btn btn-link"><?php echo $this->lang->line("hijri");?></a></div>
                                <?php } ?>
                                <div class="row m-0 p-0 col-md-10 <?php echo isset($selected_values["dueDate"]) ? "" : "d-none";?>" id="notify-me-before-link">
                                    <a href="javascript:;" onclick="notifyMeBefore(jQuery('#litigation-dialog'));"><?php echo $this->lang->line("notify_me_before");?></a>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-12 p-0 mb-10 d-none" id="notify-me-before-container">
                            <div class="form-group col-md-12 p-0 row m-0 d-none">
                                <label class="control-label col-md-3 pr-0 col-xs-5"><?php echo $this->lang->line("notify_me_before");?></label>
                                <div class="col-md-8 d-flex justify-content-between pr-0 col-xs-10" id="notify-me-before">
                                    <div class="d-flex mb-10 d-none">
                                        <?php echo form_input(["name" => "notify_me_before[time]", "class" => "form-control", "value" => $systemPreferences["reminderIntervalDate"], "id" => "notify-me-before-time", "disabled" => true]);?>
                                        <?php echo form_dropdown("notify_me_before[time_type]", $notify_me_before_time_types, "", 'class="form-control select-picker mx-2" id="notify-me-before-time-type" disabled');?>
                                        <label class="control-label"><?php echo $this->lang->line("reminder_by");?></label>
                                    </div>
                                    <?php echo form_dropdown("notify_me_before[type]", $notify_me_before_types, "", 'class="form-control select-picker" id="notify-me-before-type" disabled');?>
                                    <a href="javascript:;" onclick="hideRemindMeBefore(jQuery('#litigation-dialog'));" class="btn btn-link p-0"> <i class="icon fa-solid fa-xmark"></i> </a>
                                </div>
                            </div>
                            <div data-field="notify_before" class="inline-error "></div>
                        </div>

                        <div class="form-group col-md-12 d-flex p-0">
                            <label class="control-label col-md-3 pr-0 col-xs-5"><?php echo $this->lang->line("case_priority");?></label>
                            <div class="col-md-8 pr-0 col-xs-10">
                                <div class="col-md-12 p-0">
                                    <select name="priority" class="form-control select-picker" id="priority" data-live-search="true">
                                        <?php $selected = "";
                                        foreach ($priorities as $key => $value) {    if ($key == $selected_values["priority"]) { $selected = "selected"; } else {   $selected = ""; }?>
                                            <option data-icon="priority-<?php    echo $key;?>" <?php    echo $selected;?> value="<?php    echo $key;?>"><?php    echo $value;?></option><?php
                                        }?>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-12 p-0 show-rest-fields">
                            <div class="form-group col-md-12 d-flex p-0">
                                <div class="col-md-3 pr-0">&nbsp;</div>
                                <div class="col-md-8 pr-0">
                                    <a href="javascript:;" onclick="showMoreFields(jQuery('#litigation-dialog'), jQuery('.hide-rest-fields', '#litigation-dialog'));"><?php echo $this->lang->line("more_fields");?></a>
                                </div>
                            </div>
                        </div>
                        <div class="d-none container-hidden-fields">
                            <div class="col-md-12 less-field-divider">
                                <hr>
                            </div>
                            <div class="form-group col-md-12 d-flex p-0">
                                <label class="control-label col-md-3 pr-0 col-xs-5"><?php echo $this->lang->line("matter_container");?></label>
                                <div class="col-md-8 pr-0 col-xs-10">
                                    <div class="col-md-7 p-0"><?php echo form_input(["name" => "legalCaseRelatedContainerId", "id" => "legal-case-related-container-id", "type" => "hidden"]);?>
                                        <?php echo form_input(["name" => "legalCaseRelatedSubject", "id" => "legal-case-related-container-lookup", "value" => $selected_values["container_common_fields"]["containerSubject"] ?? "", "class" => "lookup form-control", "placeholder" => $this->lang->line("start_typing"), "title" => $this->lang->line("start_typing"), "onblur" => "checkLookupValidity(jQuery(this), jQuery('#legalCaseRelatedContainerId')); if (this.value === '') { jQuery('#legal-case-related-container-link').addClass('d-none'); }"]);?>
                                    </div>
                                    <div class="col-md-1 p-0 col-xs-2 text-center">
                                        <a href="javascript:;" id="legal-case-related-container-link" target=\"_blank" class="btn btn-link d-none"></a>
                                    </div>
                                    <div data-field="legal-case-related-container-id" class="inline-error d-none"></div>
                                </div>
                            </div>
                            <div class="form-group col-md-12 p-0 autocomplete-helper">
                                <div class="col-md-3 pr-0 col-xs-1" >&nbsp;</div>
                                <div class="col-md-8 pr-0 col-xs-10">
                                    <div class="inline-text"><?php echo $this->lang->line("helper_autocomplete");?></div>
                                </div>
                            </div>
                            <div class="form-group col-md-12 d-flex p-0">
                                <label class="control-label col-md-3 pr-0 col-xs-5"><?php echo $this->lang->line("litigation_case_stage");?></label>
                                <div class="col-md-8 pr-0 d-flex col-xs-12">
                                    <div class="col-md-11 p-0 col-xs-10">
                                        <?php echo form_dropdown("legal_case_stage_id", $litigationCaseStages, "", 'class="form-control select-picker" id="legal_case_stage_id" data-live-search="true" data-field="administration-case_stages" data-size="'. $this->session->userdata("max_drop_down_length").'"');?>
                                        <div data-field="legal_case_stage_id" class="inline-error d-none"></div>
                                    </div>
                                    <div class="col-md-1 p-0 col-xs-2">
                                        <a href="javascript:;" onclick="quickAdministrationDialog('case_stages', jQuery('#litigation-dialog'), true, 'litigation')" class="btn btn-link"><i class="fa-solid fa-square-plus p-1 font-18"> </i></a>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group col-md-12 d-flex p-0">
                                <label class="control-label col-md-3 pr-0 col-xs-5"><?php echo $this->lang->line("requested_by");?></label>
                                <div class="col-md-8 pr-0 col-xs-10">
                                    <div class="col-md-12 p-0"><?php echo form_input(["name" => "requestedBy", "id" => "requested-by", "value" => "", "type" => "hidden"]);?>
                                        <?php echo form_input(["name" => "requestedByName", "id" => "lookup-requested-by", "value" => "", "class" => "form-control lookup", "title" => $this->lang->line("start_typing")]);?>
                                    </div>
                                    <div data-field="requestedBy" class="inline-error d-none"></div>
                                </div>
                            </div>
                            <div class="form-group col-md-12 p-0 d-flex autocomplete-helper">
                                <div class="col-md-3 pr-0 col-xs-1" >&nbsp;</div>
                                <div class="col-md-8 pr-0 col-xs-10">
                                    <div class="inline-text"><?php echo $this->lang->line("helper_autocomplete");?></div>
                                </div>
                            </div>
                            <div class="form-group col-md-12 d-flex p-0">
                                <label class="control-label col-md-3 pr-0 col-xs-5"><?php echo $this->lang->line("caseValue");?>
                                    <div class="help-block smaller_font"><?php echo isset($systemPreferences["caseValueCurrency"]) && $systemPreferences["caseValueCurrency"] != "" ? "(" . $systemPreferences["caseValueCurrency"] . ")" : "";?></div>
                                </label>
                                <div class="col-md-8 pr-0 col-xs-10">
                                    <div class="col-md-5 p-0">
                                        <?php echo form_input(["value" => "", "id" => "caseValue", "class" => "form-control", "name" => "caseValue"]);?>
                                    </div>
                                    <div data-field="caseValue" class="inline-error d-none"></div>
                                </div>
                            </div>
                            <div class="form-group col-md-12 d-flex p-0">
                                <label class="control-label col-md-3 pr-0 col-xs-5"><?php echo $this->lang->line("estimated_effort");?></label>
                                <div class="col-md-8 pr-0 col-xs-12">
                                    <div class="col-md-5 col-sm-7 col-xs-12 p-0"><?php echo form_input(["name" => "estimatedEffort", "class" => "form-control", "onblur" => "convertMinsToHrsMins(jQuery(this));"]);?> </div>
                                    <div class="col-md-7 col-sm-5 col-xs-12 estimated-effort-hour vertical-aligned-tooltip-icon-container"> <span>(e.g. 1h 20m)</span><span class="tooltip-title" title="<?php echo $this->lang->line("supported_time_units");?>"><i class="fa-solid fa-circle-question purple_color"></i></span> </div>
                                    <div data-field="estimatedEffort" class="inline-error d-none"></div>
                                </div>
                            </div>
                            <div class="col-md-12 p-0 hide-rest-fields d-none">
                                <div class="form-group col-md-12 d-flex p-0 ">
                                    <div class="col-md-3 pr-0">&nbsp;</div>
                                    <div class="col-md-8 pr-0">
                                        <a href="javascript:;" onclick="showLessFields(jQuery('#litigation-dialog'));"><i class="fa fa-angle-double-up"></i>&nbsp;<?php echo $this->lang->line("less_fields");?></a>
                                    </div>
                                </div>
                            </div><?php form_close();?>
                        </div>
                    </div>
                </div><!-- /.modal-body -->
                <div class="modal-footer justify-content-between">
                    <?php $this->load->view("templates/send_email_option_template", ["type" => "add_litigation_case", "container" => "#litigation-dialog", "hide_show_notification" => $hide_show_notification]);?>
                    <div>
                        <span class="loader-submit"></span>
                        <button type="button" class="btn save-button modal-save-btn btn-info" id="case-submit"><?php  echo $this->lang->line("save");?></button>
                        <button type="button" class="close_model no_bg_button" data-dismiss="modal"><?php echo $this->lang->line("cancel");?></button>
                    </div>
                </div>
            </div><!-- /.modal-content -->
        </div><!-- /.modal-dialog -->
    </div><!-- /.modal -->
</div>
<script>
    jQuery(document).ready(function(){
        jQuery('.tooltip-title').tooltipster({
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
        let case_types_due_conditions = <?php echo $case_type_due_conditions;?>;
        jQuery('#case_type_id').on('change', function() {
            let dueIn =   setDueDateWithConditions(case_types_due_conditions,jQuery('#case_type_id').find(":selected").val(),
                jQuery('#contact-company-id').val(),jQuery('#client-type').find(":selected").val(),jQuery('#priority').find(":selected").val());
            setDueDate( dueIn  , '#litigation-dialog')
        });
        jQuery('#contact-company-id').on('change', function() {
            let dueIn = setDueDateWithConditions(case_types_due_conditions,jQuery('#case_type_id').find(":selected").val(),
                jQuery('#contact-company-id').val(),jQuery('#client-type').find(":selected").val(),jQuery('#priority').find(":selected").val());
            setDueDate( dueIn  , '#litigation-dialog')
        });
        jQuery('#client-type').on('change', function() {
            let dueIn = setDueDateWithConditions(case_types_due_conditions,jQuery('#case_type_id').find(":selected").val(),
                jQuery('#contact-company-id').val(),jQuery('#client-type').find(":selected").val(),jQuery('#priority').find(":selected").val());
            setDueDate( dueIn  , '#litigation-dialog')
        });
        jQuery('#priority').on('change', function() {
            let dueIn = setDueDateWithConditions(case_types_due_conditions,jQuery('#case_type_id').find(":selected").val(),
                jQuery('#contact-company-id').val(),jQuery('#client-type').find(":selected").val(),jQuery('#priority').find(":selected").val());
            setDueDate( dueIn  , '#litigation-dialog')
        });
    });
    function setDueDateWithConditions(case_types_due_conditions,caseTypeId,clientId,clientTypeId,priority){
        let returned_due_in = -1;
        let isDueInSet = false ;
        for (let i = 0; i < case_types_due_conditions.length; i++) {
            if(case_types_due_conditions[i].id == caseTypeId){
                if(case_types_due_conditions[i].due_conditions != null && case_types_due_conditions[i].due_conditions.length > 0){
                    for (let j = 0; j < case_types_due_conditions[i].due_conditions.length; j++) {
                        let due_condition = case_types_due_conditions[i].due_conditions[j];
                        if( ( (parseInt(due_condition.company_id) == parseInt(clientId) && clientTypeId == 'company') ||(parseInt(due_condition.contact_id) == parseInt(clientId) && clientTypeId == 'contact') || (due_condition.client_id == null && clientId == '')) && (due_condition.priority == priority || due_condition.priority == 'all')){
                            returned_due_in =  due_condition.due_in;
                            return returned_due_in;
                            isDueInSet = true;
                            return ;
                        }
                    }
                }else if(isDueInSet == false){
                    returned_due_in =  case_types_due_conditions[i].litigationSLA;//return default
                }
                returned_due_in =  case_types_due_conditions[i].litigationSLA ;
            }
        }//end of for loop for each case type
        if(returned_due_in == null || returned_due_in == -1){
            returned_due_in =0;
        }
        return returned_due_in;
        setDueDate( returned_due_in  , '#litigation-dialog')

    }//end of set due date new
    addLitigationDemo();
</script>