<div class="primary-style">
    <div class="modal fade modal-container modal-resizable" data-backdrop="false">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title"><?php echo $title; ?></h4>
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                </div>
                <div class="modal-body">
                    <?php
                    echo form_open("cases/hearings", "id=\"hearing-form\" name=\"hearingsForm\" method=\"post\" enctype=\"multipart/form-data\" target=\"hearing_hidden_upload\" class=\"form-horizontal\"");
                    $system_preferences = $this->session->userdata("systemPreferences");
                    ?>

                    <?php echo form_input(["name" => "action", "value" => "submitHearingForm", "type" => "hidden"]); ?>
                    <?php echo form_input(["name" => "trigger-create-task", "id" => "trigger-create-task", "type" => "hidden"]); ?>
                    <?php echo form_input(["name" => "trigger-create-another", "id" => "trigger-create-another", "type" => "hidden"]); ?>
                    <?php echo form_input(["name" => "id", "value" => $hearings_data["id"], "id" => "id", "type" => "hidden"]); ?>
                    <?php echo form_input(["name" => "task_id", "value" => $hearings_data["task_id"], "id" => "event-id", "type" => "hidden"]); ?>
                    <?php echo form_input(["id" => "legal_case_id", "name" => "legal_case_id", "value" => $legal_case_id, "data-field" => "case_id", "type" => "hidden"]); ?>
                    <?php echo form_input(["name" => "send_notifications_email", "id" => "send_notifications_email", "value" => $hide_show_notification, "type" => "hidden"]); ?>

                    <?php if(0<$legal_case_id){ //hide if its within a case ?>
                    <div class="col-md-12 p-0 d-none">
                        <?php }else{?>
                        <div class="col-md-12 p-0">
                            <?php }?>
                            <div class="form-group row col-md-12 p-0 mx-0">
                                <label class="control-label col-md-3 pr-0 required">
                                    <?php echo $this->lang->line("litigation_case_subject"); ?>
                                </label>
                                <div class="col-md-8">
                                    <?php
                                    $case_value = $legal_case_id ? $case_model_code . $legal_case_id . ": " . (42 < strlen($case_subject) ? mb_substr($case_subject, 0, 42) . "..." : $case_subject) : "";
                                    echo form_input(["id" => "caseLookup", "name" => "related_case", "value" => $case_value, "placeholder" => $this->lang->line("client_or_litigation_placeholder"), "title" => $this->lang->line("client_or_litigation_placeholder"), "class" => "form-control search first-input", "autocomplete" => "stop"]); ?>
                                    <div class="inline-text">
                                        <?php echo $this->lang->line("helper_case_autocomplete"); ?>
                                    </div>
                                    <div data-field="legal_case_id" class="inline-error d-none"></div>
                                </div>
                                <div id="case-subject" class="help-inline col-md-1 p-0 <?php echo $legal_case_id ? "" : "d-none"; ?>">
                                    <?php $case_url = $case_category == "IP" ? "intellectual_properties/edit/" : "cases/edit/"; ?>
                                    <a target="_blank" id="case-link" class="btn btn-link" href="<?php echo $case_url . $legal_case_id; ?>">
                                        <i class="purple_color <?php echo $this->is_auth->is_layout_rtl() ? "fa-solid fa-arrow-left" : "fa-solid fa-arrow-right"; ?>"></i>
                                    </a>
                                </div>
                            </div>
                        </div>

                        <div id="stage-div"></div>

                        <div class="col-md-12 p-0">
                            <div class="form-group row col-md-12 p-0 mx-0">
                                <label class="control-label col-md-3 pr-0 col-xs-5"><?php echo $case_category == "Litigation" ? $this->lang->line("hearing_type"): $this->lang->line("purpose"); ?></label>
                                <div class="row mx-0 col-md-9 pr-0">
                                    <div class="col-md-5 p-0 col-xs-10">
                                        <?php echo form_dropdown("type", $types, $hearings_data["type"], "id=\"type\" class=\"form-control select-picker\" data-field=\"administration-hearing_types\""); ?>
                                        <div data-field="type" class="inline-error d-none"></div>
                                    </div>
                                    <div class="col-md-1 p-0 col-xs-1">
                                        <a id="quickAddButton" href="javascript:;" onclick="quickAdministrationDialog('hearing_types', jQuery('#hearing-form-container'), true);" class="btn btn-link">
                                            <i class="fa-solid fa-square-plus p-1 font-18"></i>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <?php if ($hijri_calendar_enabled) { ?>
                            <div id="date-pair" class="col-md-12 p-0">
                                <div class="col-md-12 p-0" id="start-date-hijri-container">
                                    <div class="form-group row col-md-12 p-0 mx-0">
                                        <label class="control-label col-md-3 pr-0 col-xs-5 required"><?php echo $case_category == "Litigation" ? $this->lang->line("hearing_date"):$this->lang->line("session_date"); ?></label>
                                        <div class="row no-margin col-md-9 pr-0 col-xs-12">
                                            <?php echo form_input(["id" => "start-date-gregorian", "value" => 0 < $hearings_data["id"] ? $hearings_data["startDate"] : date("Y-m-d", time()), "class" => "d-none", "type" => "hidden"]); ?>
                                            <div class="input-group date col-md-5 col-xs-8 p-0 date-picker hijri-date-picker-container">
                                                <?php echo form_input(["id" => "start-date-hijri", "name" => "startDate", "value" => 0 < $hearings_data["id"] ? $hearings_data["startDate"] : date("Y-m-d", time()), "placeholder" => "YYYY-MM-DD", "class" => "date form-control hijri-date-picker", "autocomplete" => "stop", "title" => ""]); ?>
                                                <span class="input-group-addon"><i class="fa fa-calendar purple_color p-9 cursor-pointer-click"></i>                                            </span>
                                            </div>
                                            <div class="col-md-2 col-xs-3">
                                                <?php echo form_input(["name" => "startTime", "id" => "sTime", "placeholder" => "HH:MM", "value" => 0 < $hearings_data["id"] ? $hearings_data["startTime"] : "10:00", "class" => "time form-control", "autocomplete" => "stop"]); ?>
                                            </div>
                                            <div class="col-md-2 col-xs-3">
                                                <input type="checkbox" value="all-day" name="allDay" id="allDayCheck" onclick="EnableDisablesTime(this)" class="form-check-input">
                                                <label for="allDayCheck" class="custom-control-off">All Day</label>
                                            </div>
                                            <?php if ($system_preferences["hijriCalendarConverter"]) { ?>
                                                <div class="col-md-3 p-0 col-xs-2 visualize-hijri-date">
                                                    <a href="javascript:;" id="date-conversion" onclick="HijriConverter(jQuery('#start-date-hijri', '#hearing-form-container'), true, true);" title="<?php echo $this->lang->line("hijri_date_converter"); ?>" class="btn btn-link">
                                                        <?php echo $this->lang->line("hijri_date_converter"); ?>
                                                    </a>
                                                </div>
                                            <?php } ?>
                                            <div data-field="startDate" class="inline-error d-none"></div>
                                            <div data-field="startTime" class="inline-error d-none"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-12 p-0 assignees-container" id="assignees-container">
                                <div class="form-group row col-md-12 p-0 mx-0">
                                    <label class="control-label col-md-3 pr-0 col-xs-5">
                                        <?php echo $this->lang->line("lawyers_for_hearing"); ?>
                                    </label>
                                    <div class="col-md-7 pr-0 col-xs-10 users-lookup-container">
                                        <div class="input-group col-md-12 p-0 margin-bottom-5">
                                            <?php echo form_input("lookupHearingLawyers", "", "id=\"lookupHearingLawyers\" class=\"form-control users-lookup\""); ?>
                                            <span class="input-group-addon bs-caret users-lookup-icon" onclick="jQuery('#lookupHearingLawyers').focus();">
                                            <span class="caret"></span>
                                        </span>
                                        </div>
                                        <div id="selected-assignees" class="height-auto no-margin">
                                            <?php
                                            $select_options_name = "Hearing_Lawyers";
                                            if (!empty($hearingLawyersUsers)) {
                                                foreach ($hearingLawyersUsers as $id => $name) {?>
                                                    <div class="row multi-option-selected-items no-margin" id="<?php echo $select_options_name . $id; ?>">
                                                        <span id="<?php echo $id; ?>"><?php echo $name; ?></span>
                                                        <?php echo form_input(["value" => $id, "name" => $select_options_name . "[]", "type" => "hidden"]); ?>
                                                        <a href="javascript:;"
                                                           class="btn btn-default btn-xs btn-link pull-right remove-button flex-end-item"
                                                           tabindex="-1"
                                                           onclick="removeBoxElement(jQuery(this.parentNode),'#selected-assignees','assignees-container','#assignees-container');">
                                                            <i class="fa fa-remove"></i>
                                                        </a>
                                                    </div>
                                                    <?php
                                                }
                                            } ?>
                                        </div>
                                        <span class="assign-to-me-link-id-wrapper">
                                        <a href="javascript:;" id="assign-to-me-link" onclick="setNewBoxElement('#selected-assignees', 'users-lookup-container', '#hearing-form-container', {id: '<?php echo $this->is_auth->get_user_id(); ?>', value: '<?php echo addslashes($this->is_auth->get_fullname()); ?>', name: 'Hearing_Lawyers'});">
                                            <?php echo $this->lang->line("assign_to_me"); ?>
                                        </a>
                                    </span>
                                        <div data-field="Hearing_Lawyers" class="inline-error d-none"></div>
                                    </div>
                                </div>
                            </div>
                        <?php } else { ?>
                            <div id="date-pair" class="col-md-12 p-0">
                                <div class="col-md-12 p-0" id="start-date-container">
                                    <div class="form-group row col-md-12 p-0 mx-0">
                                        <label class="control-label col-md-3 pr-0 col-xs-5 required">
                                            <?php echo $case_category == "Litigation" ? $this->lang->line("hearing_date"):$this->lang->line("session_date"); ?>
                                        </label>
                                        <div class="row no-margin col-md-9 pr-0 col-xs-12">
                                            <div class="input-group date col-md-5 col-xs-8 p-0 date-picker" id="start-date">
                                                <?php echo form_input(["name" => "startDate", "id" => "start-date-input", "placeholder" => "YYYY-MM-DD", "value" => 0 < $hearings_data["id"] ? $hearings_data["startDate"] : date("Y-m-d", time()), "class" => "date form-control", "autocomplete" => "stop"]); ?>
                                                <span class="input-group-addon">
                                                <i class="fa fa-calendar purple_color p-9 cursor-pointer-click"></i>
                                            </span>
                                            </div>
                                            <div class="col-md-3 col-xs-4">
                                                <?php echo form_input(["name" => "startTime", "id" => "sTime", "placeholder" => "HH:MM", "value" => 0 < $hearings_data["id"] ? $hearings_data["startTime"] : "10:00", "class" => "time form-control", "autocomplete" => "stop"]); ?>
                                            </div>
                                            <div class="col-md-2 col-xs-3">
                                                <input type="checkbox" value="all-day" name="allDay" id="allDayCheck" onclick="toggleTimerField('sTime','allDayCheck')" class="form-check-input">
                                                <label for="allDayCheck" class="custom-control-off">All Day</label>
                                            </div>
                                            <?php if ($system_preferences["hijriCalendarConverter"]) { ?>
                                                <div class="col-md-3 p-0 col-xs-2 visualize-hijri-date">
                                                    <a href="javascript:;" id="date-conversion" onclick="HijriConverter(jQuery('#start-date-input', '#hearing-form-container'), true);" title="<?php echo $this->lang->line("hijri_date_converter"); ?>" class="btn btn-link">
                                                        <?php echo $this->lang->line("hijri"); ?>
                                                    </a>
                                                </div>
                                            <?php } ?>
                                            <div data-field="startDate" class="inline-error d-none"></div>
                                            <div data-field="startTime" class="inline-error d-none"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <?php if (!empty($hearingAdvisorUsers)) { ?>
                                <div class="col-md-12 row p-0 assignees-container" id="assignees-container">
                                    <div class="form-group col-md-12 p-0">
                                        <label class="control-label col-md-3 pr-0 col-xs-5">
                                            <?php echo $this->lang->line("advisors_for_hearing"); ?>
                                        </label>
                                        <div class="col-md-7 pr-0 col-xs-10 users-lookup-container">
                                            <div class="margin-bottom-5">
                                                <?php foreach ($hearingAdvisorUsers as $id => $name) { ?>
                                                    <p class="padding-top7"><?php echo $name; ?></p>
                                                <?php } ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php } ?>
                            <div class="col-md-12 p-0 assignees-container" id="assignees-container">
                                <div class="form-group row col-md-12 p-0 mx-0">
                                    <label class="control-label col-md-3 pr-0 col-xs-5"><?php echo $this->lang->line("lawyers_for_hearing"); ?></label>
                                    <div class="col-md-7 pr-0 col-xs-10 users-lookup-container">
                                        <div class="input-group col-md-12 p-0 margin-bottom-5">
                                            <?php echo form_input("lookupHearingLawyers", "", "id=\"lookupHearingLawyers\" class=\"form-control users-lookup\""); ?>
                                            <span class="input-group-addon bs-caret users-lookup-icon" onclick="jQuery('#lookupHearingLawyers').focus();">
                                            <span class="caret"></span>
                                        </span>
                                        </div>
                                        <div id="selected-assignees" class="height-auto no-margin">
                                            <?php
                                            $select_options_name = "Hearing_Lawyers";
                                            if (!empty($hearingLawyersUsers)) {
                                                foreach ($hearingLawyersUsers as $id => $name) {?>
                                                    <div class="row multi-option-selected-items no-margin" id="<?php echo $select_options_name . $id; ?>">
                                                        <span id="<?php echo $id; ?>"><?php echo $name; ?></span>
                                                        <?php echo form_input(["value" => $id, "name" => $select_options_name . "[]", "type" => "hidden"]); ?>
                                                        <a href="javascript:;"
                                                           class="btn btn-default btn-xs btn-link pull-right remove-button flex-end-item"
                                                           tabindex="-1"
                                                           onclick="removeBoxElement(jQuery(this.parentNode),'#selected-assignees','assignees-container','#assignees-container');">
                                                            <i class="fa fa-remove"></i>
                                                        </a>
                                                    </div>
                                                    <?php
                                                }
                                            }
                                            ?>
                                        </div>
                                        <span class="assign-to-me-link-id-wrapper">
                                        <a href="javascript:;" id="assign-to-me-link" onclick="setNewBoxElement('#selected-assignees', 'users-lookup-container', '#hearing-form-container', {id: '<?php echo $this->is_auth->get_user_id(); ?>', value: '<?php echo addslashes($this->is_auth->get_fullname()); ?>', name: 'Hearing_Lawyers'});">
                                            <?php echo $this->lang->line("assign_to_me"); ?>
                                        </a>
                                    </span>
                                        <div data-field="Hearing_Lawyers" class="inline-error d-none"></div>
                                    </div>
                                </div>
                            </div>
                        <?php } ?>

                        <div class="col-md-12 p-0 <?php echo $hearings_data["startDate"] <= date("Y-m-d", time()) ? "" : "d-none"; ?> d-none" id="time-spent-container">
                            <div class="form-group row col-md-12 p-0 mx-0">
                                <label class="control-label col-md-3 pr-0" title="<?php echo $this->lang->line("hearing_time_spent_title"); ?>"><?php echo $this->lang->line("time_spent"); ?></label>
                                <div class="col-md-9 pr-0">
                                    <div class="col-md-5 p-0 col-xs-10">
                                        <?php echo form_input(["name" => "timeSpent", "id" => "hearing-time-spent-value", "value" => $hearings_data["startDate"] <= date("Y-m-d", time()) ? "1:00" : "", "class" => "form-control", "onblur" => "convertMinsToHrsMins(jQuery(this));"]); ?>
                                        <span class="focus-bg"></span>
                                        <span class="<?php echo $this->is_auth->is_layout_rtl() ? "question-mark-on-input-rtl" : "question-mark-on-input"; ?>">
                                        <span class="fas fa-question-circle tooltip-title effort-time-tooltip" title="<?php echo $this->lang->line("supported_time_units"); ?>"></span>
                                    </span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="clear clearfix clearfloat"></div>

                        <?php if (0 < $hearings_data["id"]) { ?>
                            <hr class="col-md-12 p-0 hide-on-add-future-hearing"/>
                            <div class="datepair hide-on-add-future-hearing">
                                <div class="row no-margin col-md-12 p-0" id="<?php echo $hijri_calendar_enabled ? "postponed-date-hijri-container" : "postponed-date-container"; ?>">
                                    <div class="form-group row col-md-12 p-0 mx-0">
                                        <label class="control-label col-md-3 col-xs-5"><?php echo $this->lang->line("postponed_until"); ?></label>
                                        <div class="row col-md-9 col-xs-12 m-0">
                                            <?php if ($hijri_calendar_enabled) { ?>
                                                <?php echo form_input(["id" => "postponed-date-gregorian", "value" => $hearings_data["postponedDate"], "class" => "d-none", "type" => "hidden"]); ?>
                                                <div class="input-group date col-md-5 col-xs-8 p-0 date-picker hijri-date-picker-container">
                                                    <?php echo form_input(["name" => "postponedDate", "id" => "postponed-date-hijri", "value" => "", "class" => "date form-control hijri-date-picker", "placeholder" => "YYYY-MM-DD"]); ?>
                                                    <span class="input-group-addon"><i class="fa fa-calendar purple_color p-9 cursor-pointer-click"></i></span>
                                                </div>
                                            <?php } else { ?>
                                                <div class="input-group date col-md-5 col-xs-8 p-0 date-picker" id="postponed-date">
                                                    <?php echo form_input(["name" => "postponedDate", "id" => "postponed-date-input", "value" => $hearings_data["postponedDate"], "class" => "date form-control", "placeholder" => "YYYY-MM-DD"]); ?>
                                                    <span class="input-group-addon"><i class="fa fa-calendar purple_color p-9 cursor-pointer-click"></i></span>
                                                </div>
                                            <?php } ?>
                                            <div class="col-md-3 col-xs-4">
                                                <?php echo form_input(["name" => "postponedTime", "id" => "postponedTime", "placeholder" => "HH:MM", "value" => $hearings_data["postponedTime"], "class" => "time form-control", "autocomplete" => "stop"]); ?></div>
                                            <?php if ($system_preferences["hijriCalendarConverter"] && $hearings_data["id"] && !$hijri_calendar_enabled) { ?>
                                                <div class="col-md-3 p-0 col-xs-2 visualize-hijri-date">
                                                    <a href="javascript:;" id="date-conversion" onclick="HijriConverter(jQuery('#postponed-date-input', '#hearing-form-container'), true);" title="<?php echo $this->lang->line("hijri_date_converter"); ?>" class="btn btn-link"><?php echo $this->lang->line("hijri"); ?></a>
                                                </div>
                                            <?php } ?>
                                            <div data-field="postponedDate" class="inline-error d-none"></div>
                                            <div data-field="postponedTime" class="inline-error d-none"></div>
                                             <div class="col-md-2 col-xs-3">
                                                <input type="checkbox" value="all-day" name="postPoneAllDay" id="postPoneAllDay" onclick="toggleTimerField('postponedTime','postPoneAllDay')" class="form-check-input">
                                                <label for="postPoneAllDay" class="custom-control-off">All Day</label>
                                            </div>
                                            <div class="col-md-12 p-0 d-none" id="new-hearing-div">
                                                <div class="col-md-8 p-0">
                                                    <label><input type="checkbox" title="Add new Hearing" value="yes" name="add_new_hearing" id="add-new-hearing"><?php echo $this->lang->line("add_new_hearing"); ?></label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-12 p-0 hide-on-add-future-hearing d-none">
                                <div class="form-group row col-md-12 p-0 mx-0" id="reasons-of-postponement-container">
                                    <label class="control-label col-md-3 pr-0"><?php echo $this->lang->line("reasons_of_postponement"); ?></label>
                                    <div class="col-md-8 pr-0">
                                        <?php echo form_textarea(["name" => "reasons_of_postponement", "id" => "reasons_of_postponement", "rows" => "1", "cols" => "0", "class" => "form-control", "value" => $hearings_data["reasons_of_postponement"]]); ?>
                                    </div>
                                </div>
                            </div>
                        <?php } ?>

                        <div class="clear clearfix clearfloat"></div>
                        <!--  <hr class="col-md-12 p-0 hide-on-add-future-hearing "/> -->

                        <div class="col-md-12 p-0"><!--invisible-->
                            <div class="form-group row col-md-12 p-0 mx-0">
                                <label class="control-label col-md-3 pr-0"><?php echo $this->lang->line("hearing_comments"); ?><span class="tooltip-title" title="<?php echo $this->lang->line("hearing_comments_help"); ?>"><i class="fa-solid fa-circle-question purple_color"></i></span>
                                </label>
                                <div class="col-md-8 pr-0">
                                    <?php echo form_textarea(["name" => "comments", "id" => "comments", "rows" => "2", "cols" => "0", "class" => "form-control", "value" => $hearings_data["comments"]]); ?>
                                </div>
                            </div>
                        </div>

                        <?php if ($verification_process_enabled) { ?>
                            <div class="col-md-12 p-0 hide-on-add-future-hearing">
                                <div class="form-group row col-md-12 p-0 mx-0">
                                    <label class="control-label col-md-3 pr-0"><?php echo $this->lang->line("summary_by_lawyer"); ?><span class="tooltip-title" title="<?php echo $this->lang->line("summary_by_lawyer_help"); ?>"><i class="fa-solid fa-circle-question purple_color"></i></span></label>
                                    <div class="col-md-8 pr-0">
                                        <?php echo form_textarea(["name" => "summary", "id" => "summary", "rows" => "6", "cols" => "0", "class" => "form-control", "value" => $hearings_data["summary"]]); ?>
                                    </div>
                                </div>
                            </div>


                            <div class="col-md-12 p-0 hide-on-add-future-hearing">
                                <div class="form-group row col-md-12 p-0 mx-0">
                                    <label class="control-label col-md-3 pr-0"><?php echo $this->lang->line("summary_to_client"); ?><span class="tooltip-title" title="<?php echo $this->lang->line("summary_to_client_help") . (!$hasAccessToVerify ? " " . $this->lang->line("hearing_verification_process_disabled_fields") : ""); ?>"><i class="fa-solid fa-circle-question purple_color"></i></span></label>
                                    <div class="col-md-8 pr-0">
                                        <div class="summary-to-client-container">
                                            <?php echo form_textarea(["name" => "summaryToClient", "id" => "summary-to-client", "rows" => "6", "cols" => "0", "class" => "form-control", "value" => $hearings_data["summaryToClient"]]); ?>
                                            <?php if (0 < $hearings_data["id"] && $hearings_data["verifiedSummary"] != 1) { ?>
                                                <a id="copy-summary-id" href="javacript:;" title="<?php echo !$hasAccessToVerify ? " " . $this->lang->line("hearing_verification_process_disabled_copy") : $this->lang->line("copy_from_summary_by_lawyer_help"); ?>"<?php echo $hasAccessToVerify ? "onClick=\"copyHearingSummaryFromLawyerToClient();\"" : ""; ?>class="no-margin no-margin-left pull-right tooltip-title" style="text-decoration: none;"><img src="assets/images/icons/paste.svg" height="20" width="20" /><?php echo $this->lang->line("copy_from_summary_by_lawyer"); ?></a>
                                            <?php } ?>
                                        </div>
                                        <br />
                                        <?php if ($hearings_data["verifiedSummary"] == 1) { ?>
                                            <br />
                                            <img src="assets/images/icons/verified-hearing.svg" height="30" width="30" id="verified-hearing-icon" />
                                            <span id="verified-hearing-text"><?php echo $this->lang->line("hearing_verified_tooltip"); ?></span>
                                        <?php } else if (0 < $hearings_data["id"]) { ?>
                                            <br />
                                            <img id="unverified-summary-icon" src="assets/images/icons/unverified-hearing.svg" height="30" width="30" />
                                            <span id="unverified-summary-label" class="tooltip-title" title="<?php echo $this->lang->line("hearing_not_verified_tooltip"); ?>"><?php echo $this->lang->line("hearing_not_verified_label"); ?></span>
                                            <button type="button" id="verify-summary-id"<?php echo $hasAccessToVerify ? "onClick=\"verifyHearingSummary();\"" : "onClick=\"return false;\""; ?>title="<?php echo !$hasAccessToVerify ? $this->lang->line("hearing_verification_process_disabled_verify") : $this->lang->line("click_to_verify_summary"); ?>" class="btn btn-success tooltip-title pull-right <?php echo !$hasAccessToVerify ? "cursor-not-allowed " : ""; ?>"><i class="fa fa-check"></i> <?php echo $this->lang->line("verify"); ?></button>
                                        <?php } ?>
                                    </div>
                                </div>
                            </div>
                        <?php } ?>

                        <?php if ($ability_set_latest_development) { ?>
                            <div class="form-group row col-md-12 p-0 mx-0" id="latest-development-form-hearing">
                                <label class="control-label col-md-3 pr-0"><?php echo $this->lang->line("latest_development"); ?></label>
                                <div class="col-md-8 pr-0">
                                    <div class="timestamp-parent">
                                        <?php echo form_textarea(["name" => "latest_development", "id" => "latest_development", "rows" => "6", "cols" => "0", "class" => "form-control", "value" => $latest_development]); ?>
                                        <a href="javascript:;" id="add-timestamp" onclick="addLatestDevTimeStamp('#latest-development-form-hearing', '#latest_development');" class="pull-right" style="text-decoration: none;"><i class="icon-alignment fa-solid fa-clock no-margin no-margin-left"></i><?php echo $this->lang->line("add_timestamp"); ?></a>
                                    </div>
                                </div>
                            </div>
                        <?php } ?>

                        <?php if (0 < $hearings_data["id"]) { ?>
                            <div class="col-md-12 p-0 hide-on-add-future-hearing">
                                <div class="form-group row col-md-12 p-0 mx-0">
                                    <label class="control-label col-md-3 pr-0"><?php echo $this->lang->line("judged"); ?></label>
                                    <div class="col-md-9 pr-0">
                                        <img id="hearing-judged-icon" src="assets/images/icons/judged-<?php echo $hearings_data["judged"] == "yes" ? "yes" : "no"; ?>.svg" height="30" width="30" />
                                        <span id="hearing-judged-label">
                                        <?php echo $hearings_data["judged"] == "yes" ? $this->lang->line("yes") : $this->lang->line("no"); ?>
                                    </span>
                                        <a href="javascript:;" onclick="hearingSetJudgment('<?php echo $hearings_data["id"]; ?>','<?php echo $legal_case_id; ?>');"><?php echo $this->lang->line("show_more"); ?></a>
                                    </div>
                                </div>
                            </div>
                        <?php } ?>

                        <div class="clear clearfix clearfloat"></div>
                        <!--invisible CA <hr class="col-md-12 p-0"/>-->

                        <div class="row col-md-12 p-0 mx-0  d-none" id="attachments-container"> <!--invisible CA-->
                            <label class="control-label col-md-3 pl-0">
                                <i class="fa-solid fa-link purple_color"></i>
                                <?php echo $this->lang->line("attach_file"); ?>
                            </label>
                            <div id="hearing-attachments" class="col-md-9 p-0">
                                <div class="col-md-10 p-0">
                                    <input id="hearing-attachment-0" name="hearing_attachment_0" type="file" value="" class="margin-top" />
                                </div>
                                <?php echo form_input(["name" => "hearing_attachments[]", "value" => "hearing_attachment_0", "type" => "hidden"]); ?>
                            </div>
                            <div class="row col-md-12 p-0">
                                <div class="col-md-3"></div>
                                <div class="col-md-9">
                                    <div class="col-md-10 p-0">
                                        <div data-field="file" class="inline-error d-none"></div>
                                        <button type="button" onclick="hearingAttachFile()" class="btn-link p-0"><?php echo $this->lang->line("add_more"); ?></button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <?php echo form_close(); ?>
                    </div><!-- /.modal-body -->
                    <div class="modal-footer justify-content-between">
                        <?php $this->load->view("templates/send_email_option_template", ["type" => "add_hearing", "container" => "#hearing-form-container", "hide_show_notification" => $hide_show_notification]); ?>
                        <div>
                            <span class="loader-submit"></span>
                            <div class="btn-group">
                                <button type="button" class="btn btn-save btn-add-dropdown modal-save-btn save-button btn-info" id="form-submit"><?php echo $this->lang->line("save"); ?></button>
                                <button type="button" class="btn btn-save dropdown-toggle btn-add-dropdown modal-save-btn create-another-button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><span class="caret"></span></button>
                                <div class="dropdown-menu create-hearing-task-button">
                                    <a class="dropdown-item" href="javascript:;" id="create-another-hearing"><?php echo $this->lang->line("create_another"); ?></a>
                                    <a class="dropdown-item" href="javascript:;" id="create-hearing-task"><?php echo $this->lang->line("create_hearing_task"); ?></a>
                                </div>
                                <button type="button" class="close_model no_bg_button pull-right text-align-right flex-end-item" data-dismiss="modal"><?php echo $this->lang->line("cancel"); ?></button>
                            </div>
                        </div>
                    </div>
                </div><!-- /.modal-content -->
            </div><!-- /.modal-dialog -->
        </div><!-- /.modal -->
    </div>

    <script>
        jQuery(document).ready(function(){
            var hasAccessToVerify = '<?php echo $hasAccessToVerify; ?>';
            var hearingVerificationProcessDisabledFields = "<?php echo $this->lang->line("hearing_verification_process_disabled_fields"); ?>";

            if(!hasAccessToVerify){
                jQuery('#summary-to-client', '#hearing-form').attr('disabled', 'disabled').attr('title', hearingVerificationProcessDisabledFields);
            }

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
        });
       

            function toggleTimerField(timerId,allDayCheckId) {
            var checkbox = document.getElementById(allDayCheckId);
            var timeInput = document.getElementById(timerId);
            if (checkbox.checked) {
                timeInput.readOnly = true;
                timeInput.classList.add("readonly");
            } else {
                timeInput.readOnly = false;
                timeInput.classList.remove("readonly");
            }
            
        }

    </script>