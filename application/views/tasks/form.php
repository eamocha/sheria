<div class="primary-style">
    <div class="modal fade modal-container modal-resizable" id="task-modal">
        <div class="modal-dialog task-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 id="title" class="modal-title"><?php echo $title; ?></h4>
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                </div>
                <div class="modal-body" id="task-modal-wrapper">
                    <div id="taskContainer" class="col-md-12 m-0 p-0 padding-10">
                        <?php echo form_open("", "name=\"taskForm\" id=\"task-form\" method=\"post\" class=\"form-horizontal\""); ?>
                        <?php echo form_input(["name" => "id", "id" => "id", "value" => $taskData["id"], "type" => "hidden"]); ?>
                        <?php echo form_input(["name" => "user_id", "value" => $taskData["user_id"], "type" => "hidden"]); ?>
                        <?php echo form_input(["name" => "archived", "value" => $taskData["archived"], "type" => "hidden"]); ?>
                        <?php echo form_input(["name" => "task_location_id", "id" => "task_location_id", "value" => $taskData["task_location_id"], "type" => "hidden"]); ?>
                        <?php echo form_input(["name" => "send_notifications_email", "id" => "send_notifications_email", "value" => ($hide_show_notification == "1" ? "yes" : ""), "type" => "hidden"]); ?>
                        <?php echo form_input(["name" => "clone", "id" => "clone", "value" => "no", "type" => "hidden"]); ?>
                        <?php echo form_input(["name" => "assignment_id", "id" => "assignment-id", "value" => $assignments["id"]??0, "type" => "hidden"]); ?>
                        <?php echo form_input(["name" => "user_relation", "id" => "user-relation", "value" => $assignments["user"]["id"]??0, "type" => "hidden"]); ?>

                        <div class="col-md-12 p-0" id="required-fields-task-demo">
                            <!-- Title Field -->
                            <div class="col-md-12 p-0">
                                <div class="form-group col-md-12 p-0 row m-0 mb-10">
                                    <label class="control-label required col-md-3 pr-0 col-xs-5">
                                        <?php echo $this->lang->line("title"); ?>
                                    </label>
                                    <div class="col-md-8 pr-0 row m-0">
                                        <div class="col-md-11 p-0 col-xs-10">
                                            <?php echo form_input([
                                                "name" => "title", 
                                                "value" => $taskData["title"] ?? NULL, 
                                                "id" => "title", 
                                                "class" => "m-0 form-control", 
                                                "autofocus" => "autofocus"
                                            ]); ?>
                                        </div>
                                        <div data-field="title" class="inline-error d-none padding-5"></div>
                                    </div>
                                </div>
                            </div>

                            <!-- Task Type Field -->
                            <div class="col-md-12 p-0">
                                <div class="form-group col-md-12 p-0 row m-0 mb-10" id="type-wrapper">
                                    <label class="control-label col-md-3 pr-0 required col-xs-5 restriction-tooltip">
                                        <?php echo $this->lang->line("task_type"); ?>
                                    </label>
                                    <div class="col-md-8 pr-0 row m-0">
                                        <div class="col-md-11 p-0 col-xs-10">
                                            <?php echo form_dropdown(
                                                "task_type_id", 
                                                $type, 
                                                $taskData["task_type_id"] ?: $system_preferences["taskTypeId"], 
                                                "id=\"type\" class=\"form-control select-picker\" data-live-search=\"true\" data-field=\"administration-task_types\" data-size=\"" . $this->session->userdata("max_drop_down_length") . "\""
                                            ); ?>
                                        </div>
                                        <div class="col-md-1 p-0 col-xs-1">
                                            <a id="quickAddButton" href="javascript:;" onclick="quickAdministrationDialog('task_types', jQuery('#task-form', '#task-dialog'), true);" class="btn btn-link padding-all-8">
                                                <i class="fa-solid fa-square-plus p-1 font-18"></i>
                                            </a>
                                        </div>
                                        <div data-field="task_type_id" class="inline-error d-none padding-5"></div>
                                    </div>
                                </div>
                            </div>

                            <!-- Description Field -->
                            <div class="col-md-12 p-0">
                                <div class="form-group col-md-12 p-0 row m-0 mb-10" id="description-wrapper">
                                    <label class="control-label col-md-3 pr-0 col-xs-5 restriction-tooltip">
                                        <?php echo $this->lang->line("description"); ?>
                                    </label>
                                    <div class="col-md-8 pr-0 col-xs-11">
                                        <?php echo form_textarea("description", $taskData["description"], [
                                            "id" => "description", 
                                            "class" => "form-control", 
                                            "rows" => "5", 
                                            "dir" => "auto"
                                        ]); ?>
                                        <div data-field="description" class="inline-error d-none"></div>
                                    </div>
                                </div>
                            </div>

                            <!-- Assignee Field -->
                            <div class="col-md-12 p-0 assignee-container <?php echo (!isset($assignments["visible_assignee"]) || $assignments["visible_assignee"] == 1) ? "" : "d-none"; ?>">
                                <div class="form-group col-md-12 p-0 row m-0 mb-10" id="assignee-wrapper">
                                    <label class="control-label col-md-3 pr-0 required col-xs-5 restriction-tooltip" id="assignedToLabelId">
                                        <?php echo $this->lang->line("assigned_to"); ?>
                                    </label>
                                    <div class="col-md-8 pr-0 col-xs-10">
                                        <div class="input-group col-md-12 p-0 users-lookup-container mb-3" id="assigne-to-id-wrapper">
                                            <?php echo form_input([
                                                "name" => "assigned_to", 
                                                "id" => "assignedToId", 
                                                "value" => $cloned_assignee ?? ($taskData["assigned_to"] ?: $assignments["user"]["id"]??0),
                                                "type" => "hidden"
                                            ]); ?>
                                            <?php echo form_input([
                                                "name" => "assignedToLookUp", 
                                                "id" => "assignedToLookUp", 
                                                "value" => $cloned_assignee_name ?? ($taskData["assignee_status"] == "Inactive" ? $taskData["assignee_fullname"] . "(" . $this->lang->line("Inactive") . ")" : $taskData["assignee_fullname"]), 
                                                "class" => "form-control users-lookup", 
                                                "title" => $this->lang->line("start_typing")
                                            ]); ?>
                                            <span class="input-group-addon bs-caret users-lookup-icon" onclick="jQuery('#assignedToLookUp').focus();">
                                                <span class="caret"></span>
                                            </span>
                                        </div>
                                        <span class="assign-to-me-link-id-wrapper">
                                            <a href="javascript:;" id="assignToMeLinkId" onclick="addMe({hidden_id: jQuery('#assignedToId', '#task-form'), lookup_field: jQuery('#assignedToLookUp', '#task-form'), lookup_container: jQuery('.assignee-container', '#task-form'), container: jQuery('#task-form')});">
                                                <?php echo $this->lang->line("assign_to_me"); ?>
                                            </a>
                                        </span>
                                        <div data-field="assigned_to" class="inline-error d-none"></div>
                                    </div>
                                </div>
                            </div>

                            <!-- Reporter Field -->
                            <div class="col-md-12 p-0 reporter-container">
                                <div class="form-group col-md-12 p-0 reporter row m-0 mb-10" id="reporter-wrapper">
                                    <label class="control-label col-md-3 pr-0 required col-xs-5 restriction-tooltip" id="reporterLabelId">
                                        <?php echo $this->lang->line("requested_by"); ?>
                                    </label>
                                    <div class="col-md-8 pr-0 col-xs-10">
                                        <div class="input-group col-md-12 p-0 users-lookup-container">
                                            <?php echo form_input([
                                                "name" => "reporter", 
                                                "id" => "reporter-id", 
                                                "value" => $taskData["id"] ? $taskData["reporter"] : $toMeId, 
                                                "type" => "hidden"
                                            ]); ?>
                                            <?php echo form_input([
                                                "name" => "reporterLookUp", 
                                                "id" => "reporterLookUp", 
                                                "value" => $taskData["id"] ? ($taskData["reporter_status"] == "Inactive" ? $taskData["reporter_fullname"] . "(" . $this->lang->line("Inactive") . ")" : $taskData["reporter_fullname"]) : $toMeFullName, 
                                                "class" => "form-control users-lookup", 
                                                "title" => $this->lang->line("start_typing")
                                            ]); ?>
                                            <span class="input-group-addon bs-caret users-lookup-icon" onclick="jQuery('#reporterLookUp').focus();">
                                                <span class="caret"></span>
                                            </span>
                                        </div>
                                        <div data-field="reporter" class="inline-error d-none"></div>
                                    </div>
                                    <div class="col-md-3 pr-0 col-xs-2">&nbsp;</div>
                                    <div class="col-md-8 pr-0 col-xs-10">
                                        <div class="inline-text"><?php echo $this->lang->line("helper_autocomplete"); ?></div>
                                    </div>
                                </div>
                            </div>

                            <!-- Priority Field -->
                            <div class="col-md-12 p-0">
                                <div class="form-group col-md-12 p-0 row m-0 mb-10" id="priority-wrapper">
                                    <label class="control-label col-md-3 pr-0 required col-xs-5">
                                        <?php echo $this->lang->line("priority"); ?>
                                    </label>
                                    <div class="col-md-8 pr-0 col-xs-10">
                                        <div class="col-md-12 p-0">
                                            <select name="priority" class="form-control select-picker" id="priority" data-live-search="true">
                                                <?php foreach ($priorities as $key => $value): ?>
                                                    <option data-icon="priority-<?php echo $key; ?>" 
                                                        <?php echo ($taskData["priority"] && $taskData["priority"] == $key) || (!$taskData["priority"] && $key == "medium") ? "selected" : ""; ?> 
                                                        value="<?php echo $key; ?>">
                                                        <?php echo $value; ?>
                                                    </option>
                                                <?php endforeach; ?>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Due Date Field -->
                            <div class="datepair col-md-12 p-0" data-language="javascript" id="due-date-container">
                                <div class="col-md-12 form-group p-0 row m-0 mb-10">
                                    <label class="control-label col-md-3 pr-0 required col-xs-5 restriction-tooltip" id="dueDateLabelId">
                                        <?php echo $this->lang->line("due_date"); ?>
                                    </label>
                                    <div class="col-md-8 pr-0 col-xs-10" id="due-date-wrapper">
                                        <div class="row m-0">
                                            <div class="form-group input-group date date-picker col-md-9 p-0 mb-3" id="form-due-date">
                                                <?php echo form_input([
                                                    "name" => "due_date", 
                                                    "id" => "form-due-date-input", 
                                                    "placeholder" => "YYYY-MM-DD", 
                                                    "value" => $cloned_date ?? $taskData["due_date"], 
                                                    "class" => "date start form-control"
                                                ]); ?>
                                                <span class="input-group-addon">
                                                    <i class="fa fa-calendar purple_color p-9 cursor-pointer-click"></i>
                                                </span>
                                            </div>
                                            <?php if ($system_preferences["hijriCalendarConverter"]): ?>
                                                <div class="col-md-3 mt-3 col-xs-2 visualize-hijri-date">
                                                    <span class="assign-to-me-link-id-wrapper">
                                                        <a href="javascript:;" id="date-conversion" onclick="HijriConverter(jQuery('#form-due-date', '#task-dialog'));" title="<?php echo $this->lang->line("hijri_date_converter"); ?>">
                                                            <?php echo $this->lang->line("hijri"); ?>
                                                        </a>
                                                    </span>
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                        <div class="row col-md-10 <?php echo $taskData["due_date"] && !$notify_before ? "" : "d-none"; ?>" id="notify-me-before-link">
                                            <span class="assign-to-me-link-id-wrapper">
                                                <a href="javascript:;" id="notify-me-link" onclick="notifyMeBefore(jQuery('#task-form'));">
                                                    <?php echo $this->lang->line("notify_me_before"); ?>
                                                </a>
                                            </span>
                                        </div>
                                        <div data-field="due_date" class="inline-error d-none"></div>
                                    </div>
                                </div>
                            </div>

                            <!-- Notify Before Field -->
                            <div class="col-md-12 p-0 d-none mb-10" id="notify-me-before-container">
                                <div class="form-group col-md-12 p-0 row m-0">
                                    <label class="control-label col-md-3 pr-0 col-xs-5">
                                        <?php echo $this->lang->line("notify_me_before"); ?>
                                    </label>
                                    <div class="col-md-8 d-flex justify-content-between col-xs-10" id="notify-me-before">
                                        <?php echo form_input([
                                            "name" => "notify_me_before[id]", 
                                            "value" => $notify_before["id"]??"", 
                                            "disabled" => true, 
                                            "type" => "hidden"
                                        ]); ?>
                                        <?php echo form_input([
                                            "name" => "notify_me_before[time]", 
                                            "class" => "form-control", 
                                            "value" => $notify_before["time"] ?? $system_preferences["reminderIntervalDate"], 
                                            "id" => "notify-me-before-time", 
                                            "disabled" => true
                                        ]); ?>
                                        <?php echo form_dropdown(
                                            "notify_me_before[time_type]", 
                                            $notify_me_before_time_types, 
                                            $notify_before["time_type"]??"", 
                                            "class=\"form-control select-picker\" id=\"notify-me-before-time-type\" disabled"
                                        ); ?>
                                        <label class="control-label"><?php echo $this->lang->line("reminder_by"); ?></label>
                                        <?php echo form_dropdown(
                                            "notify_me_before[type]", 
                                            $notify_me_before_types, 
                                            $notify_before["type"]??"", 
                                            "class=\"form-control select-picker\" id=\"notify-me-before-type\" disabled"
                                        ); ?>
                                        <a href="javascript:;" onclick="hideRemindMeBefore(jQuery('#task-form'));" class="btn btn-link my-auto">
                                            <i class="fa-solid fa-xmark"></i>
                                        </a>
                                        <div data-field="notify_before" class="inline-error d-none"></div>
                                    </div>
                                </div>
                            </div>

                            <!-- Related Case Field -->
                            <div class="col-md-12 p-0 related-case-container core-access">
                                <?php echo form_input([
                                    "name" => "legal_case_id", 
                                    "id" => "caseLookupId", 
                                    "value" => $taskData["legal_case_id"], 
                                    "data-field" => "case_id", 
                                    "type" => "hidden"
                                ]); ?>
                                <div class="form-group col-md-12 p-0 row m-0">
                                    <label class="control-label col-md-3 pr-0 col-xs-5">
                                        <?php echo $this->lang->line("related_case"); ?>
                                    </label>
                                    <?php echo form_input([
                                        "value" => $allowTaskPrivacy, 
                                        "id" => "allow-task-privacy", 
                                        "type" => "hidden"
                                    ]); ?>
                                    <div class="col-md-8 pr-0 col-xs-10">
                                        <?php echo form_input([
                                            "name" => "caseLookup", 
                                            "id" => "caseLookup", 
                                            "title" => isset($case_full_subject) && $case_full_subject ? $case_full_subject : $taskData["caseSubject"], 
                                            "value" => isset($taskData["legal_case_id"]) && $taskData["legal_case_id"] ? $case_model_code . $taskData["legal_case_id"] . ": " . (strlen($taskData["caseSubject"]) > 42 ? mb_substr($taskData["caseSubject"], 0, 42) . "..." : $taskData["caseSubject"]) : (isset($case_subject) && $case_subject ? $case_subject : ""), 
                                            "class" => "form-control search", 
                                            "placeholder" => $this->lang->line("internalReference") . ", " . $this->lang->line("client_or_matter_placeholder")
                                        ]); ?>
                                        <div data-field="legal_case_id" class="inline-error d-none"></div>
                                    </div>
                                    <?php if ($taskData["id"]): ?>
                                        <div id="case-subject" class="help-inline col-md-1 p-0 <?php echo $taskData["legal_case_id"] ? "" : "d-none"; ?>">
                                            <a target="_blank" id="case-link" class="btn btn-link" href="<?php echo $taskData["caseCategory"] == "IP" ? "intellectual_properties/edit/" : "cases/edit/"; ?><?php echo $taskData["legal_case_id"]; ?>">
                                                <i class="purple_color <?php echo $this->is_auth->is_layout_rtl() ? "fa-solid fa-arrow-left" : "fa-solid fa-arrow-right"; ?>"></i>
                                            </a>
                                        </div>
                                    <?php endif; ?>
                                    <div class="col-md-3 pr-0 col-xs-2">&nbsp;</div>
                                    <div class="col-md-8 pr-0 col-xs-10">
                                        <div class="inline-text"><?php echo $this->lang->line("helper_case_autocomplete"); ?></div>
                                    </div>
                                </div>
                            </div>

                            <!-- Related Contract Field -->
                            <div class="col-md-12 p-0 related-contract-container contract-access">
                                <div class="form-group col-md-12 p-0 row m-0">
                                    <label class="control-label col-md-3 pr-0 col-xs-5">
                                        <?php echo $this->lang->line("related_contract"); ?>
                                    </label>
                                    <div class="col-md-8 pr-0 col-xs-10">
                                        <?php echo form_input([
                                            "name" => "contract_id", 
                                            "id" => "lookup-contract-id", 
                                            "value" => $taskData["contract_id"], 
                                            "type" => "hidden"
                                        ]); ?>
                                        <?php echo form_input([
                                            "name" => "contractLookup", 
                                            "id" => "lookup-contract", 
                                            "title" => isset($contract_full_name) && $contract_full_name ? $contract_full_name : $taskData["contract_name"], 
                                            "value" => isset($taskData["contract_id"]) && $taskData["contract_id"] ? $contract_model_code . $taskData["contract_id"] . ": " . (strlen($taskData["contract_name"]) > 42 ? mb_substr($taskData["contract_name"], 0, 42) . "..." : $taskData["contract_name"]) : (isset($contract_name) && $contract_name ? $contract_name : ""), 
                                            "class" => "form-control search"
                                        ]); ?>
                                        <div data-field="contract_id" class="inline-error d-none"></div>
                                    </div>
                                    <?php if ($taskData["id"]): ?>
                                        <div id="contract-name" class="help-inline col-md-1 p-0 <?php echo $taskData["contract_id"] ? "" : "d-none"; ?>">
                                            <a target="_blank" id="contract-link" class="btn btn-link" href="<?php echo site_url("modules/contract/contracts/view/" . $taskData["contract_id"]); ?>">
                                                <i class="purple_color <?php echo $this->is_auth->is_layout_rtl() ? "fa-solid fa-arrow-left" : "fa-solid fa-arrow-right"; ?>"></i>
                                            </a>
                                        </div>
                                    <?php endif; ?>
                                    <div class="col-md-3 pr-0 col-xs-2">&nbsp;</div>
                                    <div class="col-md-8 pr-0 col-xs-10">
                                        <div class="inline-text"><?php echo $this->lang->line("helper_autocomplete"); ?></div>
                                    </div>
                                </div>
                            </div>

                            <!-- Stage Div -->
                            <div id="stage-div">
                                <?php echo isset($litigation_stage_html) ? $litigation_stage_html : ""; ?>
                            </div>

                            <!-- Show More Fields Link -->
                            <div class="col-md-12 p-0 show-rest-fields">
                                <div class="form-group col-md-12 p-0 row m-0">
                                    <div class="col-md-3 pr-0">&nbsp;</div>
                                    <div class="col-md-8 pr-0">
                                        <a href="javascript:;" onclick="showMoreFields(jQuery('#task-form'), jQuery('.estimated_effort', '#task-form'));">
                                            <?php echo $this->lang->line("more_fields"); ?>
                                        </a>
                                    </div>
                                </div>
                            </div>

                            <!-- Hidden Fields Container -->
                            <div class="d-none container-hidden-fields">
                                <div class="col-md-12 less-field-divider">
                                    <hr>
                                </div>

                                <!-- Estimated Effort Field -->
                                <div class="col-md-12 p-0">
                                    <div class="form-group p-0 row">
                                        <label class="control-label col-md-3 pr-0 estimated_effort col-xs-5" title="<?php echo $this->lang->line("estimated_effort"); ?>">
                                            <?php echo $this->lang->line("est_effort"); ?>
                                        </label>
                                        <div class="col-md-8 d-flex pr-0 col-xs-10">
                                            <div class="col-md-8 pl-0">
                                                <?php echo form_input([
                                                    "name" => "estimated_effort", 
                                                    "value" => $taskData["estimated_effort"], 
                                                    "id" => "estimatedEffortHour", 
                                                    "class" => "m-0 form-control", 
                                                    "onblur" => "convertMinsToHrsMins(jQuery(this));"
                                                ]); ?>
                                            </div>
                                            <div class="col-md-4 col-sm-5 col-xs-12 estimated-effort-hour vertical-aligned-tooltip-icon-container p-0 d-flex">
                                                <span>(e.g. 1h 20m)</span>
                                                <span role="button" class="tooltip-title effective-effort-tooltip ml-1 align-middle" title="<?php echo $this->lang->line("supported_time_units"); ?>">
                                                    <i class="fa-solid fa-circle-question purple_color"></i>
                                                </span>
                                            </div>
                                            <div data-field="estimated_effort" class="inline-error d-none padding-5"></div>
                                        </div>

                                        <?php if ($taskData["id"] && isset($taskData["effectiveEffort"]) && $taskData["effectiveEffort"] != ""): ?>
                                            <div class="col-md-12 p-0">
                                                <div class="col-md-3 pr-0">&nbsp;</div>
                                                <div class="col-md-8 pr-0">
                                                    <span class="eff-effort">
                                                        <?php echo $this->lang->line("effectiveEffort"); ?>: 
                                                        <?php echo $this->timemask->timeToHumanReadable($taskData["effectiveEffort"]); ?>
                                                        <?php $caseIdFilter = $taskData["legal_case_id"] ? $taskData["legal_case_id"] : 0; ?>
                                                        <a href="<?php echo site_url("time_tracking/my_time_logs/" . $caseIdFilter . "/" . $taskData["id"]); ?>" target="_blank" class="btn btn-default btn-link font-12">
                                                            <?php echo $this->lang->line("show_details"); ?>
                                                        </a>
                                                    </span>
                                                </div>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                </div>

                                <!-- Contributors Field -->
                                <div class="col-md-12 p-0" id="contributors-container">
                                    <div class="form-group p-0 row">
                                        <label class="control-label col-md-3 pr-0 col-xs-5">
                                            <?php echo $this->lang->line("contributors"); ?>
                                        </label>
                                        <div class="col-md-8 pr-0 col-xs-10 users-lookup-container">
                                            <div class="input-group col-md-12 p-0 margin-bottom-5">
                                                <?php echo form_input("contributors_lookup", "", "id=\"contributors-lookup\" class=\"form-control users-lookup\""); ?>
                                                <span class="input-group-addon bs-caret users-lookup-icon" onclick="jQuery('#contributors-lookup').focus();">
                                                    <span class="caret"></span>
                                                </span>
                                            </div>
                                            <div id="selected-contributors" class="height-auto m-0">
                                                <?php if (!empty($contributors)): ?>
                                                    <?php $select_options_name = "contributors"; ?>
                                                    <?php foreach ($contributors as $key => $value): ?>
                                                        <div class="row multi-option-selected-items m-0" id="<?php echo $select_options_name . $value["id"]; ?>">
                                                            <span id="<?php echo $value["id"]; ?>">
                                                                <?php echo $value["status"] === "Inactive" ? $value["name"] . "(" . $this->lang->line("Inactive") . ")" : $value["name"]; ?>
                                                            </span>
                                                            <?php echo form_input([
                                                                "value" => $value["id"], 
                                                                "name" => $select_options_name . "[]", 
                                                                "type" => "hidden"
                                                            ]); ?>
                                                            <a href="javascript:;" class="btn btn-default btn-xs btn-link pull-right remove-button" tabindex="-1" onclick="removeBoxElement(jQuery(this.parentNode), '#selected-contributors', 'contributors-container', '#task-dialog');">
                                                                <i class="fa-solid fa-trash-can red"></i>
                                                            </a>
                                                        </div>
                                                    <?php endforeach; ?>
                                                <?php endif; ?>
                                            </div>
                                            <div data-field="contributors" class="inline-error d-none"></div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Location Field -->
                                <div class="col-md-12 p-0">
                                    <div class="form-group p-0 row mb-10">
                                        <label class="control-label pr-0 col-md-3 col-xs-5">
                                            <?php echo $this->lang->line("location"); ?>
                                        </label>
                                        <div class="col-md-8 p-0 d-flex col-xs-10">
                                            <div class="col-md-11">
                                                <?php echo form_input([                                                    "name" => "location", 
                                                    "id" => "location", 
                                                    "class" => "form-control lookup", 
                                                    "data-field" => "administration-task_locations", 
                                                    "title" => $this->lang->line("start_typing"), 
                                                    "value" => $taskData["location"]
                                                ]); ?>
                                            </div>
                                            <div class="col-md-1 p-0 col-xs-1">
                                                <a href="javascript:;" onclick="quickAdministrationDialog('task_locations', jQuery('#task-form', '#task-dialog'), true);" class="btn btn-link">
                                                    <i class="fa-solid fa-square-plus p-1 font-18"></i>
                                                </a>
                                            </div>
                                            <div data-field="task_location_id" class="inline-error d-none"></div>
                                        </div>

                                        <div class="col-md-12 p-0 row m-0">
                                            <div class="col-md-3 p-0 col-xs-2">&nbsp;</div>
                                            <div class="col-md-8 p-0 col-xs-10">
                                                <div class="inline-text m-0"><?php echo $this->lang->line("helper_autocomplete"); ?></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Custom Fields -->
                                <div class="col-md-12 p-0">
                                    <div id="custom_fields_div">
                                        <?php $this->load->view("custom_fields/dialog_form_custom_field_template", ["custom_fields" => $custom_fields]); ?>
                                    </div>
                                </div>

                                <!-- Private Task Field -->
                                <div class="col-md-12 p-0 users-lookup-container">
                                    <div class="form-group p-0 row m-0">
                                        <label class="control-label col-md-3 pr-0 col-xs-5"></label>
                                        <div class="col-md-8 p-0 col-xs-10">
                                            <div class="checkbox">
                                                <label>
                                                    <?php echo form_checkbox(
                                                        "private", 
                                                        "yes", 
                                                        $taskData["private"] == "yes", 
                                                        "id=\"private\" onclick=\"enableDisableTaskUsersLookup()\""
                                                    ); ?>
                                                    <?php echo $this->lang->line("set_as_private"); ?>
                                                </label>
                                            </div>
                                            <div class="d-none task-users-container margin-top-5">
                                                <label id="taskUsersLabelId"><?php echo $this->lang->line("shared_with"); ?></label>
                                                <div class="margin-bottom">
                                                    <?php echo form_input([
                                                        "id" => "lookupTaskUsers", 
                                                        "class" => "form-control lookup", 
                                                        "title" => $this->lang->line("start_typing")
                                                    ]); ?>
                                                </div>
                                                <div id="selected_task_users" class="row height-99 m-0">
                                                    <?php if (!empty($taskUsers)): ?>
                                                        <?php $select_options_name = "Task_Users"; ?>
                                                        <?php foreach ($taskUsers as $user): ?>
                                                            <div class="row multi-option-selected-items m-0" id="<?php echo $select_options_name . $user["id"]; ?>">
                                                                <span id="<?php echo $user["id"]; ?>">
                                                                    <?php echo $user["status"] === "Inactive" ? $user["name"] . "(" . $this->lang->line("Inactive") . ")" : $user["name"]; ?>
                                                                </span>
                                                                <?php echo form_input([
                                                                    "value" => $user["id"], 
                                                                    "name" => $select_options_name . "[]", 
                                                                    "type" => "hidden"
                                                                ]); ?>
                                                                <a href="javascript:;" class="btn btn-default btn-xs btn-link pull-right" tabindex="-1" onclick="unsetNewCaseMultiOption(this.parentNode);">
                                                                    <i class="fa-solid fa-trash-can red"></i>
                                                                </a>
                                                            </div>
                                                        <?php endforeach; ?>
                                                    <?php endif; ?>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <?php if (!$taskData["id"]): ?>
                                    <!-- Attachments Field -->
                                    <div class="clear clearfix clearfloat"></div>
                                    <hr class="col-md-12 p-0"/>
                                    <div class="p-0 row m-0" id="attachments-container">
                                        <label class="control-label col-md-3 pr-0 col-xs-5">
                                            <i class="fa-solid fa-paperclip"></i>&nbsp;<?php echo $this->lang->line("attach_file"); ?>
                                        </label>
                                        <div id="task-attachments" class="col-md-8 pr-0 col-xs-10 mb-10">
                                            <div class="col-md-11">
                                                <input id="task-attachment-0" name="task_attachment_0" type="file" value="" class="margin-top" />
                                            </div>
                                            <?php echo form_input([
                                                "name" => "task_attachments[]", 
                                                "value" => "task_attachment_0", 
                                                "type" => "hidden"
                                            ]); ?>
                                        </div>
                                        <div class="col-md-12">
                                            <div class="offset-md-3 pr-0 col-xs-5">
                                                <div class="col-md-7 col-xs-10">
                                                    <div data-field="file" class="inline-error d-none"></div>
                                                    <a href="javascript:;" onclick="dialogObjectAttachFile('task', jQuery('#task-dialog'))" class="btn-link">
                                                        <?php echo $this->lang->line("add_more"); ?>
                                                    </a>
                                                </div>
                                            </div>
                                            <div data-field="files" class="inline-error d-none"></div>
                                        </div>
                                    </div>
                                <?php endif; ?>
                            </div>

                            <!-- Show Less Fields Link -->
                            <div class="col-md-12 p-0 hide-rest-fields d-none">
                                <div class="form-group p-0 row m-0">
                                    <div class="col-md-3 p-0">&nbsp;</div>
                                    <div class="col-md-8 p-0">
                                        <a href="javascript:;" onclick="showLessFields(jQuery('#task-form'));">
                                            <i class="fa fa-angle-double-up"></i>&nbsp;<?php echo $this->lang->line("less_fields"); ?>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <?php echo form_close(); ?>
                    </div>
                </div>
                <div class="modal-footer justify-content-between">
                    <?php $this->load->view("templates/send_email_option_template", [
                        "container" => "#taskContainer", 
                        "hide_show_notification" => $hide_show_notification
                    ]); ?>
                    <div>
                        <span class="loader-submit"></span>
                        <div class="btn-group">
                            <button type="button" class="btn btn-save btn-add-dropdown modal-save-btn" id="save-task-btn">
                                <?php echo $this->lang->line("save"); ?>
                            </button>
                            <?php if (!$taskData["id"]): ?>
                                <button type="button" class="btn btn-save dropdown-toggle btn-add-dropdown modal-save-btn" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    <span class="caret"></span>
                                </button>
                                <div class="dropdown-menu dropdown-menu-right">
                                    <a class="dropdown-item" href="javascript:;" onclick="cloneDialog(jQuery('#task-dialog'), taskFormSubmit);">
                                        <?php echo $this->lang->line("create_another"); ?>
                                    </a>
                                </div>
                            <?php endif; ?>
                        </div>
                        <button type="button" class="btn btn-link" data-dismiss="modal">
                            <?php echo $this->lang->line("cancel"); ?>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
    userLoggedInName = "<?php echo addslashes($this->is_auth->get_fullname()); ?>";
    authIdLoggedIn = '<?php echo $this->is_auth->get_user_id(); ?>';
    attachmentCount = 0;
    
    <?php if ($notify_before): ?>
        notifyMeBefore(jQuery('#task-dialog'));
    <?php endif; ?>
    
    jQuery(document).ready(function () {
        jQuery('.effective-effort-tooltip').tooltipster({
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
        
        if(jQuery('#allow-task-privacy','#task-modal').val() == 1){
            jQuery('#caseLookup','#task-modal').bind('typeahead:select', function (ev, suggestion) {
                jQuery.ajax({
                    dataType: 'JSON',
                    url: getBaseURL() + 'tasks/check_case_privacy',
                    type: 'GET',
                    data: {case_id: suggestion.id},
                    success: function (response) {
                        if (response.result) {
                            jQuery('#private','#task-modal').prop("checked", true);
                            enableDisableTaskUsersLookup();
                            var select_options_name = 'Task_Users';
                            var legal_case_users = '';
                            
                            for (var user of response.users) {
                                var username = user.status === 'Inactive' ? (user.name + '(' + _lang.custom.Inactive + ')') : user.name;
                                legal_case_users += '<div class="row multi-option-selected-items m-0" id="' + select_options_name + user.id + '">' +
                                '<span id="' + user.id + '">' + username + '</span>' +
                                '<input type="hidden" value="'+ user.id +'" name="' + select_options_name + '[]">'+
                                '<a href = "javascript:;" class = "btn btn-default btn-xs btn-link pull-right" tabindex = "-1" onclick = "unsetNewCaseMultiOption(this.parentNode);">'+
                                '<i class = "fa-solid fa-trash-can red"></i></a>'+
                                '</div>';                     
                            }
                            
                            jQuery('#selected_task_users','#task-modal').html(legal_case_users);
                            pinesMessage({ty: 'information', m: response.msg});
                            showMoreFields(jQuery('#task-form', '#task-modal'));
                        } else {
                            jQuery('#private','#task-modal').prop("checked", false);
                            jQuery('#selected_task_users','#task-modal').empty();
                            enableDisableTaskUsersLookup();
                            showLessFields(jQuery('#task-form', '#task-modal'));
                        }                              
                    },
                    error: defaultAjaxJSONErrorsHandler
                }); 
            });
        }
    });
    
    addTaskDemo();
</script>

<style>
    .datepicker-dropdown.datepicker-orient-top:after, 
    .datepicker-dropdown.datepicker-orient-left:before {
        bottom: auto !important;
    }
</style>