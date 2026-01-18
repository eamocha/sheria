<?php

if (!defined("BASEPATH")) {
    exit("No direct script access allowed");
}
require APPPATH . "controllers/Top_controller.php";
class Tasks extends Top_controller
{
    public $Task;
    public function __construct()
    {
        parent::__construct();
        $this->load->model("task", "taskfactory");
        $this->task = $this->taskfactory->get_instance();
        $this->currentTopNavItem = "tasks";
    }
   
    public function add($legal_case_id = 0, $hearing_id = 0, $stage_id = 0, $contract_id = 0)
    {
        $response = [];
        $result = false;
        $this->load->model("custom_field", "custom_fieldfactory");
        $this->custom_field = $this->custom_fieldfactory->get_instance();
        $this->load->model("task_status");
        $this->load->model("task_type", "task_typefactory");
        $this->task_type = $this->task_typefactory->get_instance();
        $this->load->library("TimeMask");
        $system_preferences = $this->session->userdata("systemPreferences");
        $this->load->model("legal_case", "legal_casefactory");
        $this->legal_case = $this->legal_casefactory->get_instance();
        $task_types = $this->task_type->load_list_per_language();
        $this->load->helper("revert_comment_html");
        $this->load->helper("format_comment_patterns");
        if (!$this->input->post(NULL)) {
            $this->load->model("system_preference");
            $system_preference = $this->system_preference->get_key_groups();
            $allow_matter_task_privacy = $system_preference["DefaultValues"]["taskPrivacyBasedOnMatterPrivacy"];
            $data["allowTaskPrivacy"] = $allow_matter_task_privacy;
            $data["archivedValues"] = array_combine($this->task->get("archivedValues"), $this->task->get("archivedValues"));
            $data["system_preferences"] = $this->session->userdata("systemPreferences");
            $data["loggedInUser"] = $this->session->userdata("AUTH_userProfileName");
            $data["type"] = $task_types;
            $data["status"] = $this->task_status->load_list();
            $data["toMeId"] = $this->is_auth->get_user_id();
            $data["toMeFullName"] = $this->is_auth->get_fullname();
            $data["priorities"] = array_combine($this->task->get("priorityValues"), [$this->lang->line("critical"), $this->lang->line("high"), $this->lang->line("medium"), $this->lang->line("low")]);
            $sDate = date("Y-m-d", time());
            $data["taskData"] = ["id" => "", "contract_id" => "", "contract_name" => "", "legal_case_id" => "", "caseSubject" => "", "caseCategory" => "", "user_id" => "", "reporter" => "", "title" => "", "assigned_to" => "", "due_date" => $sDate, "private" => "", "priority" => "", "task_location_id" => "", "location" => "", "estimated_effort" => "", "description" => "", "task_status_id" => "", "task_type_id" => "", "assignee_fullname" => "", "assignee_status" => "", "reporter_fullname" => "", "reporter_status" => "", "archived" => ""];
            $data["title"] = $this->lang->line("add_tasks");
            if ($legal_case_id != 0) {
                $this->legal_case->fetch(["id" => $legal_case_id]);
                $subject = $this->legal_case->get_field("subject");
                $data["case_subject"] = $this->legal_case->get("modelCode") . $legal_case_id . ": " . (42 < strlen($subject) ? mb_substr($subject, 0, 42) . "..." : $subject);
                $data["case_full_subject"] = $subject;
            }
            if ($contract_id != 0) {
                $this->load->model("contract", "contractfactory");
                $this->contract = $this->contractfactory->get_instance();
                $this->contract->fetch(["id" => $contract_id]);
                $contract_name = $this->contract->get_field("name");
                $data["contract_name"] = $this->contract->get("modelCode") . $contract_id . ": " . (42 < strlen($contract_name) ? mb_substr($contract_name, 0, 42) . "..." : $contract_name);
                $data["contract_full_name"] = $contract_name;
            }
            if ($hearing_id != 0) {
                $this->load->model("legal_case_hearing", "legal_case_hearingfactory");
                $this->legal_case_hearing = $this->legal_case_hearingfactory->get_instance();
                $this->legal_case_hearing->fetch($hearing_id);
                $cloned_date = $this->legal_case_hearing->get_field("postponedDate") ?? $this->legal_case_hearing->get_field("startDate");
                $data["cloned_date"] = $cloned_date;
                $hearingLawyers = $this->legal_case_hearing->load_extra_users_data($hearing_id);
                if (isset($hearingLawyers[0])) {
                    $data["cloned_assignee_name"] = reset($hearingLawyers[0]);
                    $data["cloned_assignee"] = key($hearingLawyers[0]);
                }
            }
            $data["assignments"] = $this->return_assignments_rules($data["system_preferences"]["taskTypeId"]);
            if ($data["assignments"]) {
                $data["taskData"]["assignee_fullname"] = $data["assignments"]["user"]["name"];
            }
            $this->load->model("email_notification_scheme");
            $data["hide_show_notification"] = $this->email_notification_scheme->get_hide_show_send_email_notification_by_trigger_action("add_tasks");
            $this->load->model("reminder", "reminderfactory");
            $this->reminder = $this->reminderfactory->get_instance();
            $data["notify_me_before_time_types"] = array_combine($this->reminder->get("notify_me_before_time_type"), [$this->lang->line("day_or_days"), $this->lang->line("week_or_weeks"), $this->lang->line("month")]);
            $data["notify_me_before_types"] = array_combine($this->reminder->get("notify_me_before_type"), [$this->lang->line("reminder_popup"), $this->lang->line("reminder_popup_and_email")]);
            $data["notify_before"] = false;
            $data["custom_fields"] = $this->custom_field->get_field_html($this->task->get("modelName"), 0);
            $data["litigation_stage_html"] = $this->return_litigation_stage_html($legal_case_id, $stage_id);
            $response["html"] = $this->load->view("tasks/form", $data, true);
        } else {
            $description = $this->input->post("description", true, "<h1><h2><h3><h4><h5><h6><strong><em><u><a><p><img>");
            $_POST["description"] = format_comment_patterns($this->regenerate_note($description));
            $is_clone = $this->input->post("clone");
            $post_data = $this->input->post(NULL);
            $this->task->set_fields($post_data);
            $this->task->set_field("description", $this->input->post("description", true, "<h1><h2><h3><h4><h5><h6><strong><em><u><a><p><img>"));
            $this->task->set_field("user_id", $this->is_auth->get_user_id());
            $this->task->set_field("archived", "no");
            $this->load->model("task_workflow", "task_workflowfactory");
            $this->task_workflow = $this->task_workflowfactory->get_instance();
            $workflow_applicable = $this->task_workflow->load_workflow_task_status_per_type($this->input->post("task_type_id")) ?: $this->task_workflow->load_default_system_workflow();
            $this->task->set_field("task_status_id", $workflow_applicable["status"]);
            $this->task->set_field("workflow", $workflow_applicable["workflow_id"]);
            $lookup_validate = $this->task->get_lookup_validation_errors($this->task->get("lookupInputsToValidate"), $post_data);
            $estimated_effort = $this->input->post("estimated_effort");
            $estimated_effort_value = 0;
            if (!empty($estimated_effort)) {
                $estimated_effort_value = $this->timemask->humanReadableToHours($estimated_effort);
            }
            $this->task->set_field("estimated_effort", $estimated_effort_value);
            $custom_field_validation = !empty($post_data["customFields"]) ? $this->custom_field->validate_custom_field($post_data["customFields"]) : "";
            if ($this->task->validate() && !$lookup_validate && (empty($post_data["customFields"]) || $custom_field_validation["result"])) {
                $notify_before = $this->input->post("notify_me_before");
                $due_date = $this->input->post("due_date");
                if ($notify_before && $due_date && (!$notify_before["time"] || !$notify_before["time_type"] || !$notify_before["type"] || ($is_not_nb = !is_numeric($notify_before["time"])))) {
                    if ($is_not_nb) {
                        $response["validationErrors"]["notify_before"] = sprintf($this->lang->line("is_numeric_rule"), $this->lang->line("notify_before"));
                    } else {
                        $response["validationErrors"]["notify_before"] = $this->lang->line("cannot_be_blank_rule");
                    }
                } else {
                    if ($this->input->post("assignment_id") && $this->input->post("user_relation") === $this->input->post("assigned_to")) {
                        $this->load->model("assignment", "assignmentfactory");
                        $this->assignment = $this->assignmentfactory->get_instance();
                        $this->assignment->fetch($this->input->post("assignment_id"));
                        if ($this->assignment->get_field("assignment_rule") == "rr_algorithm") {
                            if ($this->input->post("legal_case_id") && $this->legal_case->fetch($this->input->post("legal_case_id"))) {
                                $assigned_team = $this->legal_case->get_field("provider_group_id");
                                $this->load->model("provider_group");
                                $this->load->model("provider_group_user");
                                $this->provider_group->fetch($assigned_team);
                                $parameter_to_send = $this->provider_group->get_field("allUsers") != 1 && $this->provider_group_user->fetch(["provider_group_id" => $assigned_team]) ? $assigned_team : false;
                                $next_assignee = $this->assignment->load_next_task_assignee($this->input->post("assignment_id"), $parameter_to_send);
                            } else {
                                $next_assignee = $this->assignment->load_next_task_assignee($this->input->post("assignment_id"));
                            }
                            $this->load->model("assignments_relation");
                            $this->assignments_relation->set_field("relation", $this->input->post("assignment_id"));
                            if ($next_assignee["user_id"] !== $this->input->post("user_relation")) {
                                $this->task->set_field("assigned_to", $next_assignee["user_id"]);
                            }
                            $this->assignments_relation->set_field("user_relation", $next_assignee["relation_id"] ?? $next_assignee["user_id"]);
                            $this->assignments_relation->insert();
                        }
                    }
                    $result = $this->task->insert();
                    if ($result) {
                        $task_id = $this->task->get_field("id");
                        $this->task->update_recent_ids($task_id, "tasks");
                        $response["task_code"] = $this->task->get("modelCode") . $task_id;
                        $response["id"] = $task_id;
                        $case_id = $this->task->get_field("legal_case_id");
                        if ($case_id) {
                            $this->legal_case->set_field("id", $case_id);
                            $this->legal_case->touch_logs();
                            if ($this->task->get_field("stage")) {
                                $this->load->model("legal_case_litigation_detail", "legal_case_litigation_detailfactory");
                                $this->legal_case_litigation_detail = $this->legal_case_litigation_detailfactory->get_instance();
                                $this->legal_case_litigation_detail->update_stage_order($this->task->get_field("stage"));
                            }
                        }
                        if (!empty($post_data["customFields"]) && is_array($post_data["customFields"]) && count($post_data["customFields"])) {
                            foreach ($post_data["customFields"] as $key => $field) {
                                $post_data["customFields"][$key]["recordId"] = $task_id;
                            }
                            $this->custom_field->update_custom_fields($post_data["customFields"]);
                        }
                        $this->notify_me_before_due_date($task_id, $case_id);
                        $getting_started_settings = unserialize($this->user_preference->get_value("getting_started"));
                        $getting_started_settings["add_task_step_done"] = true;
                        $this->user_preference->set_value("getting_started", serialize($getting_started_settings), true);
                        $inserted = $this->insert_related_users($task_id, "add_tasks");
                        $response["totalNotifications"] = $inserted["total_notifications"];
                        if ($this->input->post("legal_case_event")) {
                            $this->load->model("legal_case_event_related_data", "legal_case_event_related_datafactory");
                            $this->legal_case_event_related_data = $this->legal_case_event_related_datafactory->get_instance();
                            $this->legal_case_event_related_data->set_field("event", $this->input->post("legal_case_event"));
                            $this->legal_case_event_related_data->set_field("related_id", $task_id);
                            $this->legal_case_event_related_data->set_field("related_object", $this->task->get("modelName"));
                            $this->legal_case_event_related_data->insert();
                        }
                        $this->load->library("dms");
                        $failed_uploads_count = 0;
                        foreach ($_FILES as $file_key => $file) {
                            if ($file["error"] != 4) {
                                $upload_response = $this->dms->upload_file(["module" => "task", "module_record_id" => $task_id, "lineage" => $this->input->post("lineage"), "upload_key" => $file_key]);
                                if (!$upload_response["status"]) {
                                    $failed_uploads_count++;
                                }
                            }
                        }
                        if (0 < $failed_uploads_count) {
                            $response["validationErrors"]["files"] = sprintf($this->lang->line("files_were_not_uploaded"), $failed_uploads_count);
                        }
                    }
                }
                $clone_result = true;
                if (isset($is_clone) && !strcmp($is_clone, "yes")) {
                    $task_id = $this->task->get_field("id");
                    $this->load->model(["task_status", "user_profile"]);
                    $this->load->model("task_type", "task_typefactory");
                    $this->task_type = $this->task_typefactory->get_instance();
                    $data = [];
                    $data["taskData"] = $this->task->load_task($task_id);
                    if ($data["taskData"]) {
                        $data["taskData"]["createdBy"] = "";
                        $data["taskData"]["id"] = "";
                        $this->user_profile->fetch(["user_id" => $data["taskData"]["assigned_to"]]);
                        $data["type"] = $this->task_type->load_list_per_language();
                        $data["status"] = $this->task_status->load_list();
                        $data["toMeId"] = $this->is_auth->get_user_id();
                        $data["toMeFullName"] = $this->is_auth->get_fullname();
                        $data["taskModelCode"] = $this->task->get("modelCode");
                        $data["priorities"] = array_combine($this->task->get("priorityValues"), [$this->lang->line("critical"), $this->lang->line("high"), $this->lang->line("medium"), $this->lang->line("low")]);
                        $data["taskUsers"] = $this->task->load_task_users($task_id);
                        $response["cloned"] = true;
                    }
                }
                $response["result"] = $result && $clone_result;
            } else {
                $response["validationErrors"] = $this->task->get_validation_errors($lookup_validate);
                if (!empty($post_data["customFields"]) && !$custom_field_validation["result"]) {
                    $response["validationErrors"] = $response["validationErrors"] + $custom_field_validation["validationErrors"];
                }
            }
        }
        $this->output->set_content_type("application/json");
        $this->output->set_output(json_encode($response));
    }
    public function edit($id = 0)
    {
        $response = [];
        $this->load->model("custom_field", "custom_fieldfactory");
        $this->custom_field = $this->custom_fieldfactory->get_instance();
        $this->load->model("system_preference");
        $system_preference = $this->system_preference->get_key_groups();
        $only_reporter_edit_meta_data = $system_preference["DefaultValues"]["onlyReporterEditMetaData"];
        $current_user = (int) $this->is_auth->get_user_id();
        $this->load->library("TimeMask");
        $post_data = $this->input->post(NULL);
        $this->load->model("legal_case", "legal_casefactory");
        $this->legal_case = $this->legal_casefactory->get_instance();
        $this->load->model("contract", "contractfactory");
        $this->contract = $this->contractfactory->get_instance();
        $this->load->helper("revert_comment_html");
        $this->load->helper("format_comment_patterns");
        if (empty($post_data)) {
            $this->load->model(["task_status", "user_profile"]);
            $this->load->model("task_type", "task_typefactory");
            $this->task_type = $this->task_typefactory->get_instance();
            $data = [];
            $data["taskData"] = $this->task->load_task($id);
            $this->load->model("email_notification_scheme");
            $data["hide_show_notification"] = $this->email_notification_scheme->get_hide_show_send_email_notification_by_trigger_action("edit_task");
            if ($data["taskData"]) {
                $allow_matter_task_privacy = $system_preference["DefaultValues"]["taskPrivacyBasedOnMatterPrivacy"];
                $data["allowTaskPrivacy"] = $allow_matter_task_privacy;
                $data["type"] = $this->task_type->load_list_per_language();
                $data["status"] = $this->task_status->load_list();
                $data["toMeId"] = $this->is_auth->get_user_id();
                $data["toMeFullName"] = $this->is_auth->get_fullname();
                $data["taskModelCode"] = $this->task->get("modelCode");
                $data["case_model_code"] = $this->legal_case->get("modelCode");
                $data["contract_model_code"] = $this->contract->get("modelCode");
                $data["priorities"] = array_combine($this->task->get("priorityValues"), [$this->lang->line("critical"), $this->lang->line("high"), $this->lang->line("medium"), $this->lang->line("low")]);
                $data["contributors"] = $this->validate_id($id) ? $this->task->load_task_contributors($id) : [];
                $data["taskUsers"] = $this->task->load_task_users($id);
                $data["system_preferences"] = $this->session->userdata("systemPreferences");
                $data["title"] = $this->lang->line("edit_task") . ": " . $data["taskModelCode"] . $data["taskData"]["TaskId"];
                $this->load->model("reminder", "reminderfactory");
                $this->reminder = $this->reminderfactory->get_instance();
                $data["notify_me_before_time_types"] = array_combine($this->reminder->get("notify_me_before_time_type"), [$this->lang->line("day_or_days"), $this->lang->line("week_or_weeks"), $this->lang->line("month")]);
                $data["notify_me_before_types"] = array_combine($this->reminder->get("notify_me_before_type"), [$this->lang->line("reminder_popup"), $this->lang->line("reminder_popup_and_email")]);
                $data["notify_before"] = $this->reminder->load_notify_before_data_to_related_object($id, $this->task->get("_table"));
                $data["custom_fields"] = $this->custom_field->get_field_html($this->task->get("modelName"), $id);
                $data["taskData"]["estimated_effort"] = $this->timemask->timeToHumanReadable($data["taskData"]["estimated_effort"]);
                $data["assignments"] = false;
                $this->task->update_recent_ids($id, "tasks");
                $response["stage_html"] = $this->return_litigation_stage_html($data["taskData"]["legal_case_id"], $data["taskData"]["stage"], $id);
                $reporter_user = (int) $data["taskData"]["reporter"];
                $response["restricted_by_task_reporter"] = $only_reporter_edit_meta_data == "1" && $current_user != $reporter_user ? true : false;
                $response["html"] = $this->load->view("tasks/form", $data, true);
            }
            $response["data"] = $data;
        } else {
            $task_id = $this->input->post("id");
            $description = $this->input->post("description", true, "<h1><h2><h3><h4><h5><h6><strong><em><u><a><p><img>");
            $_POST["description"] = format_comment_patterns($this->regenerate_note($description));
            $this->task->fetch(["id" => $task_id]);
            $restricted_by_task_reporter = false;
            if ($this->task->get_field("id")) {
                $reporter_user = (int) $this->task->get_field("reporter");
                $old_case_id = $this->task->get_field("legal_case_id");
                if ($only_reporter_edit_meta_data == "1" && $current_user != $reporter_user) {
                    $restricted_by_task_reporter = true;
                }
            }
            if ($restricted_by_task_reporter) {
                $task_details = $this->task->get_fields();
                $post_data["assigned_to"] = $task_details["assigned_to"];
                $post_data["reporter"] = $task_details["reporter"];
                $post_data["task_type_id"] = $task_details["task_type_id"];
                $post_data["description"] = $task_details["description"];
                $post_data["due_date"] = $task_details["due_date"];
            }
            if (!empty($post_data["task_type_id"]) && $post_data["task_type_id"] !== $this->task->get_field("task_type_id")) {
                $this->load->model("task_workflow", "task_workflowfactory");
                $this->task_workflow = $this->task_workflowfactory->get_instance();
                $workflow_applicable = $this->task_workflow->load_workflow_task_status_per_type($this->input->post("task_type_id")) ?: $this->task_workflow->load_default_system_workflow();
                $this->task->set_field("task_status_id", $workflow_applicable["status"]);
                $this->task->set_field("workflow", $workflow_applicable["workflow_id"]);
            }
            $this->task->set_field("private", $this->input->post("private") ?? "");
            $this->task->set_fields($post_data);
            $this->task->set_field("description", $this->input->post("description", true, "<h1><h2><h3><h4><h5><h6><strong><em><u><a><p><img>"));
            $this->task->set_field("estimated_effort", $this->input->post("estimated_effort") ?? $this->task->get_field("estimated_effort"));
            $lookup_validate = $this->task->get_lookup_validation_errors($this->task->get("lookupInputsToValidate"), $post_data);
            $estimated_effort = $this->input->post("estimated_effort");
            $estimated_effort_value = 0;
            if (!empty($estimated_effort)) {
                $estimated_effort_value = $this->timemask->humanReadableToHours($estimated_effort);
            }
            $this->task->set_field("estimated_effort", $estimated_effort_value);
            $custom_field_validation = !empty($post_data["customFields"]) ? $this->custom_field->validate_custom_field($post_data["customFields"]) : "";
            if ($this->task->validate() && !$lookup_validate && (empty($post_data["customFields"]) || $custom_field_validation["result"])) {
                $notify_before = $this->input->post("notify_me_before");
                $due_date = $this->input->post("due_date");
                if ($notify_before && $due_date && (!$notify_before["time"] || !$notify_before["time_type"] || !$notify_before["type"] || ($is_not_nb = !is_numeric($notify_before["time"])))) {
                    if ($is_not_nb) {
                        $response["validationErrors"]["notify_before"] = sprintf($this->lang->line("is_numeric_rule"), $this->lang->line("notify_before"));
                    } else {
                        $response["validationErrors"]["notify_before"] = $this->lang->line("cannot_be_blank_rule");
                    }
                } else {
                    $result = $this->task->update();
                    $case_id = $this->task->get_field("legal_case_id");
                    if ($result && $case_id) {
                        $this->legal_case->set_field("id", $case_id);
                        $this->legal_case->touch_logs();
                        if ($this->task->get_field("stage")) {
                            $this->load->model("legal_case_litigation_detail", "legal_case_litigation_detailfactory");
                            $this->legal_case_litigation_detail = $this->legal_case_litigation_detailfactory->get_instance();
                            $this->legal_case_litigation_detail->update_stage_order($this->task->get_field("stage"));
                        }
                    }
                    if ($result) {
                        $_taskID = $this->task->get_field("id");
                        $contributors = $this->input->post("contributors") ?? [];
                        $this->load->model("task_contributor");
                        $contributors_data = ["task_id" => $task_id, "users" => $contributors];
                        $this->task_contributor->insert_contributors($contributors_data);
                        if (!empty($post_data["customFields"]) && is_array($post_data["customFields"]) && count($post_data["customFields"])) {
                            foreach ($post_data["customFields"] as $key => $field) {
                                $post_data["customFields"][$key]["recordId"] = $_taskID;
                            }
                            $this->custom_field->update_custom_fields($post_data["customFields"]);
                        }
                        $inserted = $this->insert_related_users($task_id, "edit_task");
                        $response["totalNotifications"] = $inserted["total_notifications"];
                        $posted_case_id = $post_data["legal_case_id"];
                        if ($old_case_id != $posted_case_id) {
                            $this->load->model("task_document");
                            $this->task_document->delete_task_document($task_id);
                        }
                    }
                    $response["result"] = $result;
                }
            } else {
                $response["validationErrors"] = $this->task->get_validation_errors($lookup_validate);
                if (!empty($post_data["customFields"]) && !$custom_field_validation["result"]) {
                    $response["validationErrors"] = $response["validationErrors"] + $custom_field_validation["validationErrors"];
                }
            }
        }
        $this->output->set_content_type("application/json")->set_output(json_encode($response));
    }
   
}

?>