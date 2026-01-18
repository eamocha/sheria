<!-- Modal -->
<div class="primary-style">
    <div class="modal fade modal-container modal-resizable">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title"><?php  echo $this->lang->line("case_intake_form");?></h4>
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                </div>
            <div class="modal-body ">
                <div id="litigation-add-form-container" class="col-md-12 m-0 p-0 padding-10">
                   <?php
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
                    <!-- Category / Broad Nature of Cases -->

                    <div class="form-group">
                        <label  class="control-label required" id="approval_step_label" for="approval_step">Source</label>
                        <select id="approval_step" name="approval_step" class="form-control select-picker"  onchange="updateForm()" required>
                            <option value="1">Complaints/Inquiry</option>
                            <option value="2">Surveillance/Detection</option>

                        </select>
                    </div>
                     <!-- Case Type Selection -->

                    <div class="col-md-12 p-0 mb-3">
                                <label class="control-label required col-xs-5">Category of offence</label>
                                <div class="d-flex pr-0 col-xs-12">
                                    <div class="p-0 margin-bottom-5 col-md-11 col-xs-10">
                                       <select name="case_type_id"  id="case_type_id" class="form-control select-picker" data-live-search="true" data-field="administration-case_types" data-size="<?php echo $this->session->userdata("max_drop_down_length");?>">
                                            <option value=""><?php echo $this->lang->line("none");?></option>
                                            <?php foreach ($litigationCaseTypes as $key => $value) {
                                                $selected = "";
                                                $caseTypeLitigationId= $systemPreferences["caseTypeLitigationId"]??"";
                                                $containerCaseType=$selected_values["container_common_fields"]["containerCaseType"]??"";
                                            if ($caseTypeLitigationId == $value["id"] || $containerCaseType == $value["id"]) {
                                                $selected = "selected='selected'";
                                            }
                                            ?>
                                            <option value="<?php echo $value["id"];?>" <?php echo $selected;?>     litigationSLA='<?php echo $value["litigationSLA"];?>' ><?php  echo $value["name"];?></option>
                                            <?php } ?>
                                        </select>
                                    </div>


                                    <div class="col-md-1 p-0 col-xs-2">
                                        <a href="javascript:;" onclick="quickAdministrationDialog('case_types', jQuery('#litigation-dialog'), true, 'criminal');" class="btn btn-link"><i class="fa-solid fa-square-plus p-1 font-18"> </i></a>
                                    </div>
                                </div>
                        <div data-field="case_type_id" class="inline-error d-none"></div>
                    </div>
                
                 <!--subcategory selection -->
<div class="form-group">
    <label class="control-label required" for="offence_subcategory_id">Offense Type</label>
    <?php
        echo form_dropdown(
            'offence_subcategory_id',[],'', 'id="offence_subcategory_id" class="form-control select-picker" data-live-search="true" required'
        );
    ?>
    <div data-field="offence_subcategory_id" class="inline-error d-none"></div>
</div>

                    <!-- subject case -->
                    <div class="col-md-12 p-0 mb-3">
                        <label class="control-label required mb-1">
                            <?php echo $this->lang->line("subject");?>
                        </label>
                        <div class="input-group">
                            <?php echo form_input([
                                "dir" => "auto",
                                "name" => "subject",
                                "id" => "subject",
                                "class" => "form-control",
                                "autocomplete" => "stop",
                                "value" => $selected_values["container_common_fields"]["containerSubject"] ?? ""
                            ]);?> </div>
                            <div data-field="subject" class="inline-error d-none"></div>

                    </div>

                    <!-- accused name -->
                    <div class="form-group">
                        <label class="control-label required" id="client-type-label">
                            Suspect Type
                        </label>
                        <div class="">
                            <div class="">
                                <select name="clientType" id="client-type" class="form-control select-picker" tabindex="-1">
                                    <option value="company" <?php echo isset($selected_values["container_common_fields"]["client_data"]["type"]) ? $selected_values["container_common_fields"]["client_data"]["type"] == "Company" ? "selected='selected'" : "" : "";?>><?php echo $this->lang->line("company_or_group");?></option>
                                    <option value="contact" <?php echo isset($selected_values["container_common_fields"]["client_data"]["type"]) ? $selected_values["container_common_fields"]["client_data"]["type"] == "Person" ? "selected='selected'" : "" : "";?>><?php echo $this->lang->line("contact");?></option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class=" form-group ">
                        <label class="control-label " id="client-name-label" >Suspect Name</label>
                        <div class="">
                            <div class="">
                                <?php echo form_input([
                                    "name" => "contact_company_id",
                                    "id" => "contact-company-id",
                                    "value" => $selected_values["container_common_fields"]["client_data"]["member_id"] ?? "",
                                    "type" => "hidden"
                                ]);?>
                                <?php echo form_input([
                                    "name" => "clientLookup",
                                    "id" => "client-lookup",
                                    "value" => $selected_values["container_common_fields"]["client_data"]["name"] ?? "",
                                    "class" => "form-control lookup",
                                    "title" => $this->lang->line("start_typing")
                                ]);?>
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

                    <div class="form-group d-none  ">
                        <label class="control-label">
                            Position
                        </label>
                        <div class="col-md-8 pr-0 d-flex col-xs-12">
                            <div class="col-md-11 p-0 col-xs-10">
                                <?php echo form_dropdown(
                                    "legal_case_client_position_id",
                                    $clientPositions,
                                    $selected_values["container_common_fields"]["containerClientPoisiton"] ?? "",
                                    'id="legal_case_client_position_id" class="form-control select-picker" data-live-search="true" data-field="administration-case_client_positions" data-size="'. $this->session->userdata("max_drop_down_length").'"'
                                );?>
                            </div>
                            <div class="col-md-1 p-0 col-xs-2">
                                <a href="javascript:;" onclick="quickAdministrationDialog('case_client_positions', jQuery('#litigation-dialog'), true);" class="btn btn-link">
                                    <i class="fa-solid fa-square-plus p-1 font-18"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                    <!-- complainant name -->
                    <div id="opponents-container">
                    <?php echo form_input(["name" => "opponentsCount", "id" => "opponents-count", "value" =>"1", "type" => "hidden"]);?>
                            <?php if (isset($selected_values["container_common_fields"]["opponent_data"]) && !empty($selected_values["container_common_fields"]["opponent_data"])) {
                            $count = 1;
                            foreach ($selected_values["container_common_fields"]["opponent_data"] as $opponent_record) {
                            ?>
                            <div class="opponent-div" id="opponent-<?php echo $count;?>">
                                <div class="form-group  margin-bottom-10">
                                    <label class="control-label  opponent-label">&nbsp</label>
                                    <div class="">
                                        <div class="">
                                            <select name="opponent_member_type[]" id="opponent-member-type" class="form-control">
                                                <option value="company" <?php  echo $opponent_record["opponent_member_type"] == "company" ? "selected='selected'" : "";?>><?php  echo $this->lang->line("company_or_group");?></option>
                                                <option value="contact" <?php echo $opponent_record["opponent_member_type"] == "contact" ? "selected='selected'" : "";?>><?php  echo $this->lang->line("contact");?></option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class=" form-group  margin-bottom-5">
                                    <label class="control-label  col-xs-12">&nbsp;</label>
                                    <div class=" ">
                                        <div class=" p-0">
                                            <?php  echo form_input(["name" => "opponent_member_id[]", "id" => "opponent-member-id", "value" => $opponent_record["opponent_member_id"], "type" => "hidden"]);?>
                                            <?php   echo form_input(["name" => "opponentLookup[]", "id" => "opponent-lookup", "value" => $opponent_record["opponentName"], "class" => "form-control lookup", "title" => $this->lang->line("start_typing")]);?>
                                        </div>
                                        <div class=" pr-0 padding-10 delete-icon <?php   echo $selected_values["container_common_fields"]["opponent_count"] == 1 ? "d-none" : "";?>">
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
                            <div class="col-md-12 p-0 form-group margin-bottom-10">
                                <label class="control-label "><?php    echo $this->lang->line("opponent_position");?></label>
                                <div class="col-md-8 pr-0 col-xs-12">
                                    <div class="col-md-7 p-0 col-xs-10"><?php  echo form_dropdown("opponent_position[]", $opponent_positions, $opponent_record["opponent_position"], 'id="opponent-position" class="form-control select-picker" data-live-search="true" data-field="administration-case_opponent_positions" data-field-id="opponent-position-" . $count . "" data-size="'. $this->session->userdata("max_drop_down_length").'"');?></div>
                                    <div class="col-md-1 p-0 col-xs-2">
                                        <a href="javascript:;" onclick="quickAdministrationDialog('case_opponent_positions', jQuery('#litigation-dialog'), true, false, false, jQuery('[data-field-id=opponent-position-<?php echo $count;?>]'));" class="btn btn-link opponent-position-quick-add"><i class="fa-solid fa-square-plus p-1 font-18"> </i></a>
                                    </div>
                                </div>
                            </div><?php         $count++;
                            }
                            } else {
                            ?>
                            <div class="opponent-div" id="opponent-1">
                                <div class=" form-group margin-bottom-10">
                                    <label class="control-label  opponent-label" id="opponent-type-label" >Complainant Type</label>
                                    <div class=" col-xs-10">
                                        <div class="col-md-12 p-0">
                                            <select name="opponent_member_type[]" id="opponent-member-type" class="form-control">
                                                <option value="company"><?php    echo $this->lang->line("company_or_group");?></option>
                                                <option value="contact"><?php     echo $this->lang->line("contact");?></option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class=" form-group  margin-bottom-5">
                                    <label class="control-label" id="opponent-name-label" >Complainant Name</label>
                                    <div class=" ">
                                        <div class="">
                                            <?php    echo form_input(["name" => "opponent_member_id[]", "id" => "opponent-member-id", "value" => "", "type" => "hidden"]);?>
                                            <?php  echo form_input(["name" => "opponentLookup[]", "id" => "opponent-lookup", "value" => "", "class" => "form-control lookup", "title" => $this->lang->line("start_typing")]);?>
                                        </div>
                                        <div class=" padding-10 delete-icon d-none">
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
                                <div class="col-md-12 p-0 form-group margin-bottom-10 d-none">
                                    <label class="control-label "><?php    echo $this->lang->line("opponent_position");?></label>
                                    <div class="col-md-8 pr-0 d-flex col-xs-12">
                                        <div class="col-md-11 p-0 col-xs-10">
                                            <?php    echo form_dropdown("opponent_position[]", $opponent_positions, "", 'id="opponent-position" class="form-control select-picker" data-live-search="true" data-field="administration-case_opponent_positions" data-field-id="opponent-position-1" data-size="'. $this->session->userdata("max_drop_down_length").'"');?></div>
                                        <div class="col-md-1 p-0 col-xs-2">
                                            <a href="javascript:;" onclick="quickAdministrationDialog('case_opponent_positions', jQuery('#litigation-dialog'), true, false, false, jQuery('[data-field-id=opponent-position-1]'));" class="btn btn-link opponent-position-quick-add"><i class="fa-solid fa-square-plus p-1 font-18"> </i></a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <?php }?>

                        </div>
                        <!-- assignee and assigned team -->
                        <div class="form-group " id="assigned-team-container">
                            <label class="control-label ">Responsible Team</label>
                            <div class="">
                                <div class=""><?php echo form_dropdown("provider_group_id", $Provider_Groups, $selected_values["container_common_fields"]["assigned_team"] ?? $assignments["assigned_team"], 'id="provider-group-id" class="form-control select-picker" data-live-search="true" data-size="'. $this->session->userdata("max_drop_down_length").'"');?>
                                    <div data-field="provider_group_id" class="inline-error d-none"></div>
                                </div>
                            </div>
                        </div>
                       <div class="form-group col-md-12 p-0 d-none " id="assignee-container">
                            <label class="control-label col-md-3 pr-0 col-xs-5"><?php echo $this->lang->line("assignee");?></label>
                            <div class="col-md-8 pr-0 col-xs-10">
                                <div class="col-md-12 p-0">
                                    <?php echo form_dropdown("user_id", $usersProviderGroup, $selected_values["container_common_fields"]["containerAssignee"] ?? (isset($assignments["user_id"]) ? $assignments["user_id"] : ""), 'id="user-id" data-live-search="true" class="form-control select-picker" data-size="'. $this->session->userdata("max_drop_down_length").'"');?>
                                    <div data-field="user_id" class="inline-error d-none"></div>
                                </div>
                            </div>
                        </div>
                        <!-- privacy container -->
                        <div class="col-md-12 form-group p-0 case-privacy-container d-none" id="case-privacy-container">
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
                    <!-- Filed On Date -->
                    <div class="form-group " id="arrival-date-container">
                        <label class="control-label required mb-0">
                            Date Received
                        </label>
                        <div class="">
                            <div class="row m-0">
                                <div class="input-group date col-md-5 p-0 date-picker" id="arrival-date-add-new">
                                    <?php echo form_input([
                                        "name" => "caseArrivalDate",
                                        "id" => "arrival-date-input",
                                        "placeholder" => "YYYY-MM-DD",
                                        "value" => $selected_values["container_common_fields"]["arrival_date"] ?? $selected_values["today"],
                                        "class" => "form-control"
                                    ]);?>
                                    <span class="input-group-addon">
                                        <i class="fa fa-calendar purple_color p-9 cursor-pointer-click"></i>
                                    </span>
                                </div>
                                <?php if ($systemPreferences["hijriCalendarConverter"]) { ?>
                                    <div class="col-md-3 p-0 col-xs-2 visualize-hijri-date">
                                        <a href="javascript:;" id="date-conversion" onclick="HijriConverter(jQuery('#arrival-date-add-new', '#litigation-dialog'));" title="<?php echo $this->lang->line("hijri_date_converter");?>" class="btn btn-link">
                                            <?php echo $this->lang->line("hijri");?>
                                        </a>
                                    </div>
                                <?php } ?>
                                <div data-field="caseArrivalDate" class="inline-error d-none"></div>
                            </div>
                        </div>
                    </div>

                    <!-- Case Summary -->
                    <div class=" form-group ">
                        <label class="control-label" for="description">
                            Case Summary
                        </label>
                        <?php echo form_textarea([
                            "name" => "description",
                            "id" => "description",
                            "class" => "form-control",
                            "rows" => "5",
                            "cols" => "0",
                            "value" => $selected_values["container_common_fields"]["containerDescription"] ?? ""
                        ]);?>
                    </div>

<!-- reference number -->
                     <div class="form-group ">
                            <label class="control-label  pr-0 col-xs-5">Case/Inquiry No.</label>
                            <div class=" pr-0 col-xs-10">
                                <div class="col-md-8 p-0"><?php echo form_input(["dir" => "auto", "name" => "internalReference", "class" => "form-control", "value" => $selected_values["container_common_fields"]["internal_reference"] ?? ""]);?> </div>
                                <div data-field="internalReference" class="inline-error d-none"></div>
                            </div>
                        </div>
                        <!--priority -->
                        <div class="form-group col-md-12  p-0 d-none">
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
                        <!-- police station -->
                        <div class="form-group">
                                <label class="control-label" for="police_station_reported">Police Station Reported</label>
                                <?php echo form_input([
                                    "name" => "police_station_reported",
                                    "id" => "police_station_reported",
                                    "class" => "form-control",
                                    "value" => $selected_values["police_station_reported"] ?? ""
                                ]); ?>
                                <div data-field="police_station_reported" class="inline-error d-none"></div>
                            </div>
                            <!-- police station ob number -->
                            <div class="form-group">
                                <label class="control-label" for="police_station_ob_number">Police Station OB Number</label>
                                <?php echo form_input([
                                    "name" => "police_station_ob_number",
                                    "id" => "police_station_ob_number",
                                    "class" => "form-control",
                                    "value" => $selected_values["police_station_ob_number"] ?? ""
                                ]); ?>
                                <div data-field="police_station_ob_number" class="inline-error d-none"></div>
                            </div>
                            <!-- police case file number -->
                            <div class="form-group">
                                <label class="control-label" for="police_case_file_number">Police Case File Number</label>
                                <?php echo form_input([
                                    "name" => "police_case_file_number",
                                    "id" => "police_case_file_number",
                                    "class" => "form-control",
                                    "value" => $selected_values["police_case_file_number"] ?? ""
                                ]); ?>
                                <div data-field="police_case_file_number" class="inline-error d-none"></div>
                            </div>
                            <!-- Criminal case status-->
                            <div class="form-group">
                                <label class="control-label" for="status_of_case">Criminal Case Status</label>
                                <?php
                                    echo form_dropdown(
                                        "status_of_case",
                                        $criminal_case_status,
                                        $selected_values["status_of_case"] ?? "",
                                        'id="status_of_case" class="form-control select-picker" data-live-search="true"'
                                    );
                                ?>
                                <div data-field="status_of_case" class="inline-error d-none"></div>
                            </div>

                        <!-- status comments -->
                <div class=" form-group">
                    <label  class="control-label" id="statusComments-label" for="statusComments">Remarks</label>
                    <textarea id="statusComments" name="statusComments" class="form-control"></textarea>
                </div>

                <!-- hidden fields -->
 <div class="form-group col-md-12  p-0 d-none">
                                <label class="control-label col-md-3 pr-0 col-xs-5 required" ><?php echo $this->lang->line("litigation_case_stage");?></label>
                                <div class="col-md-8 pr-0 d-flex col-xs-12">
                                    <div class="col-md-11 p-0 col-xs-10">
                                        <?php echo form_dropdown("legal_case_stage_id", $litigationCaseStages, "", 'class="form-control select-picker" id="legal_case_stage_id" data-live-search="true" required data-field="administration-case_stages" data-size="'. $this->session->userdata("max_drop_down_length").'"');?>
                                        <div data-field="legal_case_stage_id" class="inline-error d-none"></div>
                                    </div>
                                    <div class="col-md-1 p-0 col-xs-2">
                                        <a href="javascript:;" onclick="quickAdministrationDialog('case_stages', jQuery('#litigation-dialog'), true, 'litigation')" class="btn btn-link"><i class="fa-solid fa-square-plus p-1 font-18"> </i></a>
                                    </div>
                                </div>
                            </div>
                            
                   <!--attach file-->
                    <div class="clear clearfix clearfloat"></div>
                    <hr class="col-md-12 p-0"/>
                    <div class="p-0 row m-0" id="attachments-container">
                        <label class="control-label pr-0 col-xs-5"><i class="fa-solid fa-paperclip"></i>&nbsp;  Attach duly filled Complaint/Detection Form</label>
                        <div id="attachments" class=" col-xs-10 mb-10">
                            <div class="col-md-11">
                                <input id="attachment-0" name="attachment_0" type="file" value="" class="margin-top" />
                            </div>
                            <?php    echo form_input(["name" => "attachments[]", "value" => "attachment_0", "type" => "hidden"]);?>
                        </div>

                    </div>

                        <div class="modal-footer justify-content-between">
                            <?php $this->load->view("templates/send_email_option_template", ["type" => "add_litigation_case", "container" => "#litigation-dialog", "hide_show_notification" => $hide_show_notification]);?>
                            <div>
                                <span class="loader-submit"></span>
                                <button type="button" class="btn save-button modal-save-btn btn-info" id="case-submit"><?php  echo $this->lang->line("save");?></button>
                                <button type="button" class="close_model no_bg_button" data-dismiss="modal"><?php echo $this->lang->line("cancel");?></button>
                            </div>
                        </div>
                </form>
                </div>
            </div>
        </div>
    </div>
</div>
</div>


<script>
    attachmentCount = 0;
    function updateForm() {
    const approval_phase = document.getElementById("approval_step").value;
    const clientTypeLabel = document.getElementById("client-type-label");
    const clientNameLabel = document.getElementById("client-name-label");
    const opponentTypeLabel = document.getElementById("opponent-type-label");
    const opponentNameLabel = document.getElementById("opponent-name-label");
    const remarksLabel = document.getElementById("statusComments-label");

    // "2" is Surveillance/Detection
    if (approval_phase === "2") {
        if (clientTypeLabel) clientTypeLabel.textContent = "Operator Type";
        if (clientNameLabel) clientNameLabel.textContent = "Unauthorized Operator/Service Provider Name";
        if (opponentTypeLabel) opponentTypeLabel.textContent = "Contact Type";
        if (opponentNameLabel) opponentNameLabel.textContent = "Contact";
        if (remarksLabel) remarksLabel.textContent = "Findings/Current Status";
    } else {
        if (clientTypeLabel) clientTypeLabel.textContent = "Accused Type";
        if (clientNameLabel) clientNameLabel.textContent = "Accused Name";
        if (opponentTypeLabel) opponentTypeLabel.textContent = "Complainant Type";
        if (opponentNameLabel) opponentNameLabel.textContent = "Complainant Name";
        if (remarksLabel) remarksLabel.textContent = "Remarks";
    }
}
</script>
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

             jQuery('#case_type_id').on('change', function() {
                get_offense_types_by_case_type();
                });
                 addLitigationDemo();
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
   

    // Load subtypes when case_type_id changes
    function get_offense_types_by_case_type() {
        let caseTypeId = jQuery("#case_type_id").val();
        let $subtype = jQuery('#offence_subcategory_id');
        $subtype.empty().append('<option value="">Loading...</option>').selectpicker('refresh');
        if (caseTypeId) {
            jQuery.ajax({
                url: getBaseURL()+"cases/get_offense_types_based_on_case_type_id/"+caseTypeId,
                type: 'GET',
              
                dataType: 'json',
                beforeSend: function() {
                    // Show loading spinner on the select box
                    $subtype.empty().append('<option value=""><i class="fa fa-spinner fa-spin"></i> Loading...</option>').selectpicker('refresh');
                },
                success: function(response) {
                    $subtype.empty().append('<option value="">Select Sub Type</option>');
                    if (response && Object.keys(response).length > 0) {
                        jQuery.each(response, function(id, name) {
                            $subtype.append('<option value="' + id + '">' + name + '</option>');
                        });
                    }
                    $subtype.selectpicker('refresh');
                },
                error: function() {
                    $subtype.empty().append('<option value="">No Sub Types Found</option>').selectpicker('refresh');
                }
            });
        } else {
            $subtype.empty().append('<option value="">Select Sub Type</option>').selectpicker('refresh');
        }
    
}
</script>