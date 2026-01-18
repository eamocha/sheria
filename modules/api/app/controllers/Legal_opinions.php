<?php

require "Top_controller.php";
class Legal_opinions extends Top_controller
{
    public $responseData;
    public function __construct()
    {
        parent::__construct();
        $this->load->model("opinion", "opinionfactory");
        $this->opinion = $this->opinionfactory->get_instance();
        $this->responseData = default_response_data();
        $this->load->library("TimeMask");
        $this->load->model("system_preference");
    }
    public function load_data()
    {
        $response = $this->responseData;
        $response["success"]["data"] = $this->_load_data();
        $this->render($response);
    }
    private function _load_data()
    {
        $this->load->model("opinion_type", "opinion_typefactory");
        $this->opinion_type = $this->opinion_typefactory->get_instance();
        $data = [];
        $lang = $this->get_lang_code();
        $data["opinionTypes"] = $this->opinion_type->api_load_list_per_language($lang);
        $data["toMeId"] = $this->user_logged_in_data["user_id"];
        $data["toMeFullName"] = $this->user_logged_in_data["profileName"];
        $data["opinionPriorities"] = array_combine($this->opinion->get("priorityValues"), [$this->lang->line("critical"), $this->lang->line("high"), $this->lang->line("medium"), $this->lang->line("low")]);
        $systemPreferences = $this->system_preferences["DefaultValues"];
        $data["default_values"] = ["opinion_type_id" => isset($systemPreferences["opinionTypeId"]) && !empty($systemPreferences["opinionTypeId"]) ? $systemPreferences["opinionTypeId"] : "", "interval_date" => $this->system_preferences["Reminders"]["reminderIntervalDate"]];
        $data["userLoggedInFullName"] = $this->user_logged_in_data["profileName"];
        $this->load->model("reminder", "reminderfactory");
        $this->reminder = $this->reminderfactory->get_instance();
        $data["notify_me_before_time_types"] = array_combine($this->reminder->get("notify_me_before_time_type"), [$this->lang->line("day_or_days"), $this->lang->line("week_or_weeks"), $this->lang->line("month")]);
        $data["notify_me_before_types"] = array_combine($this->reminder->get("notify_me_before_type"), [$this->lang->line("reminder_popup"), $this->lang->line("reminder_popup_and_email")]);
        $data["assignments"] = $this->return_rule_per_type($data["default_values"]["opinion_type_id"]);
        
        // Load opinion-specific data
        $this->load->model("opinion_location", "opinion_locationfactory");
       // $this->opinion_location = $this->opinion_locationfactory->get_instance();
        $data["opinionLocations"] =[];// $this->opinion_location->load_all();
        
        $this->load->model("opinion_status", "opinion_statusfactory");
       // $this->opinion_status = $this->opinion_statusfactory->get_instance();
        $data["opinionStatuses"] =[];// $this->opinion_status->load_all();
        
        return $data;
    }
    public function add()
    {
        $this->check_license_availability();
        $response = $this->responseData;
        if ($this->input->post(NULL)) {
            if (is_null($this->input->post("title"))) {
                $max_title_length = 250;
                $opinion_title = substr(strip_tags($this->input->post("legal_question")), 0, $max_title_length);
                if (strlen($opinion_title) == $max_title_length) {
                    $opinion_title .= "...";
                }
                $this->opinion->set_field("title", $opinion_title);
            }
            $this->opinion->set_fields($this->input->post(NULL));
            $this->opinion->set_field("user_id", $this->user_logged_in_data["user_id"]);
            $this->opinion->set_field("archived", "no");
            $this->opinion->set_field("createdBy", $this->user_logged_in_data["user_id"]);
            $this->opinion->set_field("createdOn", date("Y-m-d H:i:s"));
            $this->opinion->set_field("modifiedOn", date("Y-m-d H:i:s"));
            $this->opinion->set_field("modifiedBy", $this->user_logged_in_data["user_id"]);
            $this->load->model("opinion_workflow", "opinion_workflowfactory");
            $this->opinion_workflow = $this->opinion_workflowfactory->get_instance();
            $workflow_applicable = $this->opinion_workflow->load_workflow_opinion_status_per_type($this->input->post("opinion_type_id")) ?: $this->opinion_workflow->load_default_system_workflow();
            $this->opinion->set_field("opinion_status_id", $workflow_applicable["status"]);
            $this->opinion->set_field("workflow", $workflow_applicable["workflow_id"]);
            $this->opinion->disable_builtin_logs();
            if ($this->input->post("estimated_effort")) {
                $estimated_effort = $this->timemask->humanReadableToHours($this->input->post("estimated_effort"));
                $this->opinion->set_field("estimated_effort", $estimated_effort);
            } else {
                $this->opinion->set_field("estimated_effort", 0);
            }
            if ($this->opinion->validate()) {
                $notify_before = $this->input->post("notify_me_before");
                if ($notify_before && $this->input->post("due_date") && (!isset($notify_before["time"]) || !$notify_before["time"] || !isset($notify_before["time_type"]) || !$notify_before["time_type"] || !isset($notify_before["type"]) || !$notify_before["type"] || ($is_not_nb = !is_numeric($notify_before["time"])))) {
                    $response["error"] = [];
                    if ($is_not_nb) {
                        $response["error"]["notify_before"] = sprintf($this->lang->line("is_numeric_rule"), $this->lang->line("notify_before"));
                    } else {
                        $response["error"]["notify_before"] = $this->lang->line("cannot_be_blank_rule");
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
                                $this->provider_group->fetch($assigned_team);
                                $next_assignee = $this->assignment->load_next_opinion_assignee($this->input->post("assignment_id"), $this->provider_group->get_field("allUsers") != 1 ? $assigned_team : false);
                            } else {
                                $next_assignee = $this->assignment->load_next_opinion_assignee($this->input->post("assignment_id"));
                            }
                            $this->load->model("assignments_relation");
                            $this->assignments_relation->set_field("relation", $this->input->post("assignment_id"));
                            if ($next_assignee["user_id"] !== $this->input->post("user_relation")) {
                                $this->opinion->set_field("assigned_to", $next_assignee["user_id"]);
                            }
                            $this->assignments_relation->set_field("user_relation", $next_assignee["relation_id"] ?? $next_assignee["user_id"]);
                            $this->assignments_relation->insert();
                        }
                    }
                    if ($this->opinion->insert()) {
                        $opinion_id = $this->opinion->get_field("id");
                        $caseId = $this->opinion->get_field("legal_case_id");
                        $this->notify_me_before_due_date($opinion_id, $caseId);
                        $response["success"]["msg"] = $this->lang->line("new_opinion_added_successfully");
                        $response["success"]["data"]["opinion_id"] = $opinion_id;
                        if ($caseId) {
                            if ($this->opinion->get_field("stage")) {
                                $this->load->model("legal_case_litigation_detail", "legal_case_litigation_detailfactory");
                                $this->legal_case_litigation_detail = $this->legal_case_litigation_detailfactory->get_instance();
                                $this->legal_case_litigation_detail->update_stage_order($this->opinion->get_field("stage"), $this->user_logged_in_data["user_id"]);
                            }
                            $this->load->model("legal_case", "legal_casefactory");
                            $this->legal_case = $this->legal_casefactory->get_instance();
                            $this->legal_case->set_field("id", $caseId);
                            $this->legal_case->touch_logs("update", [], $this->user_logged_in_data["user_id"], $this->user_logged_in_data["channel"]);
                        }
                        $this->insert_related_users($opinion_id, "add_opinions");
                    } else {
                        $response["error"] = [];
                    }
                }
            } else {
                $response["error"] = $this->opinion->get("validationErrors");
            }
        } else {
            $response["error"] = $this->lang->line("data_missing");
        }
        $this->render($response);
    }
    public function list_my_opinions()
    {
        $this->load_opinions(false);
    }
    public function list_all_opinions()
    {
        $this->load_opinions(true, $this->input->post("user_id"));
    }
    private function load_opinions($all_opinions = true, $opinions_by_user_id = 0, $contributed = false)
    {
        $response = $this->responseData;
        $user_id = $this->user_logged_in_data["user_id"];
        $this->user_profile->fetch(["user_id" => $user_id]);
        $overridePrivacy = $this->user_profile->get_field("overridePrivacy");
        $this->load->model("user_preference");
        $this->user_preference->fetch(["user_id" => $user_id, "keyName" => "language"]);
        $authLang = $this->user_preference->get_field("keyValue");
        $pageSize = strcmp($this->input->post("pageSize"), "") ? $this->input->post("pageSize") : 20;
        $pageNb = strcmp($this->input->post("pageNb"), "") ? $this->input->post("pageNb") : 1;
        $skip = ($pageNb - 1) * $pageSize;
        $term = trim((string) $this->input->post("term"));
        $_POST["filters"] = $this->input->post("filters") ? $this->input->post("filters") : [];
        $contributors_ids = $this->input->post("contributors_ids");
        $response["success"] = $this->opinion->api_load_all_opinions($user_id, $overridePrivacy, $authLang, $pageSize, $skip, $term, false, $this->input->post("filters"), $all_opinions, $opinions_by_user_id, $contributed, $contributors_ids);
        if (isset($response["success"]["data"]) && !empty($response["success"]["data"])) {
            foreach ($response["success"]["data"] as $key => $value) {
                $response["success"]["data"][$key]["estimated_effort"] = $response["success"]["data"][$key]["estimated_effort"] == ".00" ? "" : $this->timemask->timeToHumanReadable($response["success"]["data"][$key]["estimated_effort"]);
                if ($value["assignedToStatus"] == "Inactive") {
                    $response["success"]["data"][$key]["assigned_to"] = $response["success"]["data"][$key]["assigned_to"] . "(" . $this->lang->line("Inactive") . ")";
                }
                if ($value["reportedByStatus"] == "Inactive") {
                    $response["success"]["data"][$key]["reporter"] = $response["success"]["data"][$key]["reporter"] . "(" . $this->lang->line("Inactive") . ")";
                }
            }
        }
        $response["success"]["dbDriver"] = $this->getDBDriver();
        $this->render($response);
    }
    public function list_opinions_reported_by_me()
    {
        $response = $this->responseData;
        $user_id = $this->user_logged_in_data["user_id"];
        $this->user_profile->fetch(["user_id" => $user_id]);
        $overridePrivacy = $this->user_profile->get_field("overridePrivacy");
        $this->load->model("user_preference");
        $this->user_preference->fetch(["user_id" => $user_id, "keyName" => "language"]);
        $authLang = $this->user_preference->get_field("keyValue");
        $pageSize = strcmp($this->input->post("pageSize"), "") ? $this->input->post("pageSize") : 20;
        $pageNb = strcmp($this->input->post("pageNb"), "") ? $this->input->post("pageNb") : 1;
        $skip = ($pageNb - 1) * $pageSize;
        $term = trim((string) $this->input->post("term"));
        $_POST["filters"] = $this->input->post("filters") ? $this->input->post("filters") : [];
        $response["success"] = $this->opinion->api_load_all_opinions($user_id, $overridePrivacy, $authLang, $pageSize, $skip, $term, true, $this->input->post("filters"), false, NULL, false, NULL);
        if (isset($response["success"]["data"]) && !empty($response["success"]["data"])) {
            foreach ($response["success"]["data"] as $key => $value) {
                $response["success"]["data"][$key]["estimated_effort"] = $response["success"]["data"][$key]["estimated_effort"] == ".00" ? "" : $this->timemask->timeToHumanReadable($response["success"]["data"][$key]["estimated_effort"]);
                if ($value["assignedToStatus"] == "Inactive") {
                    $response["success"]["data"][$key]["assigned_to"] = $response["success"]["data"][$key]["assigned_to"] . "(" . $this->lang->line("Inactive") . ")";
                }
                if ($value["reportedByStatus"] == "Inactive") {
                    $response["success"]["data"][$key]["reporter"] = $response["success"]["data"][$key]["reporter"] . "(" . $this->lang->line("Inactive") . ")";
                }
            }
        }
        $response["success"]["dbDriver"] = $this->getDBDriver();
        $this->render($response);
    }
    public function autocomplete()
    {
        $response = $this->responseData;
        $term = trim((string) $this->input->post("term"));
        $this->lookup_term_validation($term);
        if (!empty($term)) {
            $user_id = $this->user_logged_in_data["user_id"];
            $this->user_profile->fetch(["user_id" => $user_id]);
            $overridePrivacy = $this->user_profile->get_field("overridePrivacy");
            $response["success"]["data"] = $this->opinion->api_lookup($term, $user_id, $overridePrivacy, $this->input->post("legal_case_id"));
            if (!empty($response["success"]["data"])) {
                foreach ($response["success"]["data"] as $key => $value) {
                    $response["success"]["data"][$key]->estimated_effort = $response["success"]["data"][$key]->estimated_effort == ".00" ? "" : $this->timemask->timeToHumanReadable($response["success"]["data"][$key]->estimated_effort);
                }
            }
        } else {
            $response["error"] = $this->lang->line("data_missing");
        }
        $this->render($response);
    }
    public function edit($opinion_id = 0)
    {
        $this->check_license_availability();
        $response = $this->responseData;
        $this->load->model("system_preference");
        $system_preference = $this->system_preference->get_key_groups();
        $only_reporter_edit_meta_data = $system_preference["DefaultValues"]["onlyReporterEditMetaData"];
        $loggedUserId = $this->user_logged_in_data["user_id"];
        if (0 < $opinion_id) {
            $data = [];
            $data = $this->_load_data();
            $response["success"]["data"]["formData"] = $data;
            $data = [];
            $this->user_profile->fetch(["user_id" => $loggedUserId]);
            $overridePrivacy = $this->user_profile->get_field("overridePrivacy");
            $data = $this->opinion->api_load_opinion($opinion_id, $loggedUserId, $overridePrivacy);
            if ($data) {
                $data["contributors"] = $this->opinion->load_opinion_contributors($opinion_id);
                $data["estimated_effort"] = $data["estimated_effort"] == ".00" ? "" : $this->timemask->timeToHumanReadable($data["estimated_effort"]);
                $data["notify_before"] = $this->reminder->load_notify_before_data_to_related_object($opinion_id, $this->opinion->get("_table"), $loggedUserId);
                $this->load->model("opinion_workflow_status_transition", "opinion_workflow_status_transitionfactory");
                $this->opinion_workflow_status_transition = $this->opinion_workflow_status_transitionfactory->get_instance();
                $transitions_accessible = $this->opinion_workflow_status_transition->load_available_steps($data["opinion_status_id"], $data["workflow"], $this->user_logged_in_data["user_id"], $this->user_logged_in_data["user_group_id"]);
                $data["available_statuses"] = $transitions_accessible["available_statuses"];
                $data["status_transitions"] = $transitions_accessible["status_transitions"];
                $data["modifiedByName"] = "";
                if (0 < $data["modifiedBy"]) {
                    $this->load->model("user_profile");
                    $this->user_profile->fetch(["user_id" => $data["modifiedBy"]]);
                    $data["modifiedByName"] = $this->user_profile->get_field("firstName") . " " . $this->user_profile->get_field("lastName") . ($this->user_profile->get_field("status") == "Inactive" ? "(" . $this->lang->line("Inactive") . ")" : "");
                }
                $data["workflowStatusByName"] = "";
                if (0 < $data["opinion_status_id"]) {
                    $this->load->model("opinion_status");
                    $this->opinion_status->fetch($data["opinion_status_id"]);
                    $data["workflowStatusByName"] = $this->opinion_status->get_field("name");
                }
                $response["success"]["data"]["opinionValues"] = $data;
                $reporter_user = (int) $response["success"]["data"]["opinionValues"]["reporter"];
                $response["success"]["data"]["restricted_by_opinion_reporter"] = $only_reporter_edit_meta_data == "1" && $loggedUserId != $reporter_user ? true : false;
                $response["success"]["data"]["disables_fields"] = $response["success"]["data"]["restricted_by_opinion_reporter"] ? ["title", "legal_question", "background_info", "detailed_info", "assigned_to", "reporter", "due_date", "opinion_type_id", "opinion_location_id"] : "";
                $lang = $this->get_lang_code();
                $this->load->model("legal_case_litigation_detail", "legal_case_litigation_detailfactory");
                $this->legal_case_litigation_detail = $this->legal_case_litigation_detailfactory->get_instance();
                $response["success"]["data"]["stage"] = !empty($data["stage"]) ? $this->legal_case_litigation_detail->load_stage_metadata($data["legal_case_id"], $data["stage"], $lang) : [];
                $response["success"]["data"]["showStageEditLink"] = $this->legal_case_litigation_detail->load_all(["where" => ["legal_case_id", $data["legal_case_id"]]]) ? true : false;
            } else {
                unset($response["success"]);
                $response["error"] = $this->lang->line("opinion_id_not_exists");
            }
        } else {
            if ($this->input->post(NULL)) {
                if (!$this->input->post("id") || !$this->input->post("id")) {
                    $response["error"] = "missing opinion id";
                } else {
                    if ($this->opinion->fetch($this->input->post("id"))) {
                        $reporter_user = (int) $this->opinion->get_field("reporter");
                        $restricted_by_opinion_reporter = $only_reporter_edit_meta_data == "1" && $loggedUserId != $reporter_user ? true : false;
                        $old_status = $this->opinion->get_field("opinion_status_id");
                        $old_type = $this->opinion->get_field("opinion_type_id");
                        $post_data = $this->input->post(NULL);
                        $post_data["stage"] = $this->input->post("stage") ? $post_data["stage"] : "";
                        if ($restricted_by_opinion_reporter) {
                            if (isset($post_data["legal_question"])) {
                                unset($post_data["legal_question"]);
                            }
                            if (isset($post_data["background_info"])) {
                                unset($post_data["background_info"]);
                            }
                            if (isset($post_data["detailed_info"])) {
                                unset($post_data["detailed_info"]);
                            }
                            if (isset($post_data["assigned_to"])) {
                                unset($post_data["assigned_to"]);
                            }
                            if (isset($post_data["reporter"])) {
                                unset($post_data["reporter"]);
                            }
                            if (isset($post_data["due_date"])) {
                                unset($post_data["due_date"]);
                            }
                            if (isset($post_data["opinion_type_id"])) {
                                unset($post_data["opinion_type_id"]);
                            }
                            if (isset($post_data["opinion_location_id"])) {
                                unset($post_data["opinion_location_id"]);
                            }
                        }
                        $this->opinion->set_fields($post_data);
                        $this->opinion->set_field("opinion_status_id", $old_status);
                        if ($this->input->post("opinion_type_id") !== $old_type) {
                            $this->load->model("opinion_workflow", "opinion_workflowfactory");
                            $this->opinion_workflow = $this->opinion_workflowfactory->get_instance();
                            $workflow_applicable = $this->opinion_workflow->load_workflow_opinion_status_per_type($this->input->post("opinion_type_id")) ?: $this->opinion_workflow->load_default_system_workflow();
                            $this->opinion->set_field("opinion_status_id", $workflow_applicable["status"]);
                            $this->opinion->set_field("workflow", $workflow_applicable["workflow_id"]);
                        }
                        $this->opinion->set_field("modifiedOn", date("Y-m-d H:i:s"));
                        $this->opinion->set_field("modifiedBy", $this->user_logged_in_data["user_id"]);
                        $this->opinion->disable_builtin_logs();
                        if ($this->input->post("estimated_effort")) {
                            $estimated_effort = $this->timemask->humanReadableToHours($this->input->post("estimated_effort"));
                            $this->opinion->set_field("estimated_effort", $estimated_effort);
                        }
                        if ($this->opinion->validate()) {
                            $notify_before = $this->input->post("notify_me_before");
                            if ($notify_before && ($this->input->post("due_date") || $this->opinion->get_field("due_date")) && (!isset($notify_before["time"]) || !$notify_before["time"] || !isset($notify_before["time_type"]) || !$notify_before["time_type"] || !isset($notify_before["type"]) || !$notify_before["type"])) {
                                $response["error"] = [];
                                $response["error"]["notify_before"] = $this->lang->line("cannot_be_blank_rule");
                            } else {
                                if ($this->opinion->update()) {
                                    if ($this->opinion->get_field("stage")) {
                                        $this->load->model("legal_case_litigation_detail", "legal_case_litigation_detailfactory");
                                        $this->legal_case_litigation_detail = $this->legal_case_litigation_detailfactory->get_instance();
                                        $this->legal_case_litigation_detail->update_stage_order($this->opinion->get_field("stage"), $this->user_logged_in_data["user_id"]);
                                    }
                                    $opinion_id = $this->opinion->get_field("id");
                                    $this->notify_me_before_due_date($opinion_id, $this->opinion->get_field("legal_case_id"));
                                    $this->insert_related_users($opinion_id, "edit_opinion");
                                    $response["success"]["msg"] = sprintf($this->lang->line("record_save_successfull"), " " . $this->opinion->get("modelCode") . $opinion_id);
                                } else {
                                    $response["error"] = [];
                                }
                            }
                        } else {
                            $response["error"] = $this->opinion->get("validationErrors");
                        }
                    } else {
                        $response["error"] = $this->lang->line("opinion_id_not_exists");
                    }
                }
            } else {
                $response["error"] = $this->lang->line("data_missing");
            }
        }
        $this->render($response);
    }
    private function notify_me_before_due_date($opinion_id, $case_id)
    {
        $notify_before = $this->input->post("notify_me_before");
        $due_date = $this->input->post("due_date") ? $this->input->post("due_date") : $this->opinion->get_field("due_date");
        if (empty($this->reminder)) {
            $this->load->model("reminder", "reminderfactory");
            $this->reminder = $this->reminderfactory->get_instance();
        }
        $current_reminder = $this->reminder->load_notify_before_data_to_related_object($opinion_id, $this->opinion->get("_table"), $this->user_logged_in_data["user_id"]);
        if ($current_reminder && (!isset($notify_before["id"]) || !$notify_before["id"])) {
            return $this->reminder->remind_before_due_date([], $current_reminder["id"]);
        }
        if ($notify_before && $due_date) {
            $reminder = ["user_id" => $this->user_logged_in_data["user_id"], "remindDate" => $due_date, "legal_case_id" => $case_id, "opinion_id" => $opinion_id, "related_id" => $opinion_id, "related_object" => $this->opinion->get("_table"), "notify_before_time" => $notify_before["time"], "notify_before_time_type" => $notify_before["time_type"], "notify_before_type" => $notify_before["type"]];
            $reminder["summary"] = sprintf($this->lang->line("notify_me_before_message"), $this->lang->line("opinion"), $this->opinion->get("modelCode") . $opinion_id, $due_date);
            return $this->reminder->remind_before_due_date($reminder, isset($notify_before["id"]) ? $notify_before["id"] : NULL);
        }
        return false;
    }
    private function insert_related_users($opinion_id, $action)
    {
        $this->load->model("notification", "notificationfactory");
        $this->notification = $this->notificationfactory->get_instance();
        $reporter = $this->input->post("reporter");
        $assigned_to = $this->input->post("assigned_to");
        $contributors = $this->input->post("contributors") ?? [];
        if ($contributors) {
            $this->load->model("opinion_contributor");
            $contributors_data = ["opinion_id" => $opinion_id, "users" => $contributors];
            $this->opinion_contributor->insert_contributors($contributors_data);
        }
        $this->load->model("opinion_user");
        $watchers = $this->input->post("Opinion_Users", true) ?? [];
        if ($contributors) {
            $watchers = array_merge($watchers, array_diff($contributors, $watchers));
        }
        if (!in_array($assigned_to, $watchers)) {
            $watchers[] = $assigned_to;
        }
        if (!in_array($reporter, $watchers)) {
            $watchers[] = $reporter;
        }
        if (!in_array($this->user_logged_in_data["user_id"], $watchers)) {
            $watchers[] = $this->user_logged_in_data["user_id"];
        }
        $watchers_data["users"] = ["opinion_id" => $opinion_id, "users" => $watchers];
        $this->opinion->insert_opinion_users($watchers_data);
        $users_to_notify = ["contributors" => $contributors];
        $this->send_notifications($action, $users_to_notify);
        $total_notifications["total_notifications"] = $this->notification->update_pending_notifications($watchers);
        return $total_notifications;
    }
    public function view($id)
    {
        $this->check_license_availability();
        $response = $this->responseData;
        $data = [];
        $logged_user = $this->user_logged_in_data["user_id"];
        $this->user_profile->fetch(["user_id" => $logged_user]);
        $override_privacy = $this->user_profile->get_field("overridePrivacy");
        $opinion_data = $this->opinion->api_load_opinion($id, $logged_user, $override_privacy);
        $data["opinion_data"] = $opinion_data;
        $data["opinion_data"]["estimated_effort"] = $this->timemask->timeToHumanReadable($data["opinion_data"]["estimated_effort"]);
        $data["opinion_data"]["model_code"] = $this->opinion->get("modelCode");
        $this->load->model("opinion_workflow_status_transition", "opinion_workflow_status_transitionfactory");
        $this->opinion_workflow_status_transition = $this->opinion_workflow_status_transitionfactory->get_instance();
        $transitions_accessible = $this->opinion_workflow_status_transition->load_available_steps($data["opinion_data"]["opinion_status_id"], $data["opinion_data"]["workflow"], $this->user_logged_in_data["user_id"], $this->user_logged_in_data["user_group_id"]);
        $data["available_statuses"] = $transitions_accessible["available_statuses"];
        $data["status_transitions"] = $transitions_accessible["status_transitions"];
        $this->load->model("custom_field", "custom_fieldfactory");
        $this->custom_field = $this->custom_fieldfactory->get_instance();
        $custom_fields = $this->custom_field->load_custom_fields($id, $this->opinion->get("modelName"), $this->get_lang_code());
        $section_types = $this->custom_field->section_types;
        if (!empty($custom_fields)) {
            foreach ($custom_fields as $field) {
                if ($field["type"] === "lookup") {
                    $field["value"] = $this->custom_field->get_lookup_data($field);
                }
                $data["custom_fields"][$section_types[$field["type"]]][] = $field;
            }
        }
        if ($opinion_data["private"] === "yes") {
            $data["watchers"] = $this->opinion->load_opinion_users($id);
        }
        $data["contributors"] = $this->opinion->load_opinion_contributors($id);
        $response["success"]["data"] = $data;
        $this->render($response);
    }
    public function move_status($opinion_id = "", $status_id = "")
    {
        $this->check_license_availability();
        $response = $this->responseData;
        if (!$opinion_id || !$status_id) {
            $response["error"] = "missing data";
        } else {
            $this->load->model("opinion_workflow_status_transition", "opinion_workflow_status_transitionfactory");
            $this->opinion_workflow_status_transition = $this->opinion_workflow_status_transitionfactory->get_instance();
            if (!$this->opinion_workflow_status_transition->check_transition_allowed($opinion_id, $status_id, $this->user_logged_in_data["user_id"], $this->user_logged_in_data["user_group_id"])) {
                $response["error"] = $this->lang->line("transition_not_allowed");
            } else {
                $lang = $this->get_lang_code();
                $this->load->model("opinion_fields", "opinion_fieldsfactory");
                $this->opinion_fields = $this->opinion_fieldsfactory->get_instance();
                $this->opinion_fields->load_all_fields($lang);
                $this->opinion->fetch($opinion_id);
                $old_status = $this->opinion->get_field("opinion_status_id");
                $workflow_applicable = 0 < $this->opinion->get_field("workflow") ? $this->opinion->get_field("workflow") : 1;
                $this->opinion_workflow_status_transition->fetch(["workflow_id" => $workflow_applicable, "from_step" => $old_status, "to_step" => $status_id]);
                $transition = $this->opinion_workflow_status_transition->get_field("id");
                $data = $this->opinion_fields->return_screen_fields($opinion_id, $transition, $lang);
                if ($data) {
                    $response["success"]["transition_id"] = $transition;
                    $response["success"]["data"] = $data;
                } else {
                    $this->opinion->set_field("opinion_status_id", $status_id);
                    if (!$this->opinion->update()) {
                        $response["error"] = $this->lang->line("workflowActionInvalid");
                    } else {
                        if ($this->opinion->get_field("stage")) {
                            $this->load->model("legal_case_litigation_detail", "legal_case_litigation_detailfactory");
                            $this->legal_case_litigation_detail = $this->legal_case_litigation_detailfactory->get_instance();
                            $this->legal_case_litigation_detail->update_stage_order($this->opinion->get_field("stage"), $this->user_logged_in_data["user_id"]);
                        }
                        $response["success"]["msg"] = $this->lang->line("record_saved");
                    }
                }
            }
        }
        $this->render($response);
    }
    public function add_screen_transition()
    {
        $response = $this->responseData;
        $opinion_id = $this->input->post("opinion_id");
        $transition = $this->input->post("transition_id");
        if (!$opinion_id || !$transition || !$this->legal_case->fetch($opinion_id)) {
            $response["error"] = "missing data";
        } else {
            $this->load->model("opinion_fields", "opinion_fieldsfactory");
            $this->opinion_fields = $this->opinion_fieldsfactory->get_instance();
            $lang = $this->get_lang_code();
            $this->opinion_fields->load_all_fields($lang);
            $this->load->model("opinion_workflow_status_transition", "opinion_workflow_status_transitionfactory");
            $this->opinion_workflow_status_transition = $this->opinion_workflow_status_transitionfactory->get_instance();
            $this->opinion_workflow_status_transition->fetch($transition);
            $status = $this->opinion_workflow_status_transition->get_field("to_step");
            $validation = $this->opinion_fields->validate_fields($transition);
            if (!$validation["result"]) {
                $response["error"] = $validation["errors"];
            } else {
                $this->opinion->set_field("opinion_status_id", $status);
                if (!$this->opinion->update()) {
                    $response["error"] = $this->lang->line("workflowActionInvalid");
                } else {
                    $save_result = $this->opinion_fields->save_fields($opinion_id, $this->user_logged_in_data["user_id"]);
                    if (!$save_result["result"]) {
                        $response["error"] = $save_result["validation_errors"];
                    } else {
                        $response["success"]["msg"] = $this->lang->line("record_saved");
                    }
                }
            }
        }
        $this->render($response);
    }
    public function add_comment()
    {
        $response = $this->responseData;
        $this->load->helper("format_comment_patterns");
        if ($this->input->post(NULL)) {
            $this->load->model("opinion_comment", "opinion_commentfactory");
            $this->opinion_comment = $this->opinion_commentfactory->get_instance();
            $_POST["comment"] = format_comment_patterns($this->regenerate_note($this->input->post("comment")));
            $_POST["edited"] = 0;
            $this->opinion_comment->set_fields($this->input->post(NULL));
            $this->opinion_comment->set_field("createdBy", $this->user_logged_in_data["user_id"]);
            $this->opinion_comment->set_field("createdOn", date("Y-m-d H:i:s"));
            $this->opinion_comment->set_field("modifiedBy", $this->user_logged_in_data["user_id"]);
            $this->opinion_comment->set_field("modifiedOn", date("Y-m-d H:i:s"));
            $this->opinion_comment->disable_builtin_logs();
            if ($this->opinion_comment->insert()) {
                $response["success"]["msg"] = $this->lang->line("comment_added_successfully");
            } else {
                $response["error"] = $this->opinion_comment->get("validationErrors");
            }
        } else {
            $response["error"] = $this->lang->line("data_missing");
        }
        $this->render($response);
    }
    public function edit_comment($comment_id = "", $opinion_id = "")
    {
        $response = $this->responseData;
        $this->load->helper("format_comment_patterns");
        $this->load->helper("revert_comment_html");
        $this->load->model("opinion_comment", "opinion_commentfactory");
        $this->opinion_comment = $this->opinion_commentfactory->get_instance();
        if ($comment_id && $opinion_id) {
            $this->opinion_comment->fetch(["id" => $comment_id]);
            $response["success"]["data"] = $this->opinion_comment->get_fields();
            $response["success"]["data"]["comment_html"] = revert_comment_html($response["success"]["data"]["comment"], true);
            $response["success"]["data"]["comment"] = strip_tags(trim($response["success"]["data"]["comment"]));
            $response["success"]["data"]["attachments"] = $this->revert_comment_attachments($response["success"]["data"]["comment"]);
            $this->render($response);
        } else {
            if ($this->input->post(NULL)) {
                $comment_id = $this->input->post("comment_id");
                $opinion_id = $this->input->post("opinion_id");
                $this->opinion_comment->fetch(["id" => $comment_id, "opinion_id" => $opinion_id]);
                $_POST["comment"] = format_comment_patterns($this->regenerate_note($this->input->post("comment")), true, true);
                if ($this->input->post("comment") != $this->opinion_comment->get_field("comment")) {
                    $_POST["edited"] = 1;
                }
                $this->opinion_comment->set_fields($this->input->post(NULL));
                $this->opinion_comment->set_field("modifiedBy", $this->user_logged_in_data["user_id"]);
                $this->opinion_comment->set_field("modifiedOn", date("Y-m-d H:i:s"));
                $this->opinion_comment->disable_builtin_logs();
                if ($this->opinion_comment->update()) {
                    $response["success"]["msg"] = sprintf($this->lang->line("record_save_successfull"), $this->lang->line("comment"));
                } else {
                    $response["error"] = $this->opinion_comment->get("validationErrors");
                }
                $this->render($response);
            }
        }
    }
    public function comments()
    {
        $response = $this->responseData;
        $id = $this->input->get("opinion_id");
        $this->load->model("opinion_comment", "opinion_commentfactory");
        $this->opinion_comment = $this->opinion_commentfactory->get_instance();
        $this->load->helper("revert_comment_html");
        $comments = $this->opinion_comment->load_comments($id, $this->input->get("showAll"));
        if (0 < $comments["count"]) {
            foreach ($comments["records"] as $key => $value) {
                if (isset($value["total_rows"])) {
                    unset($value["total_rows"]);
                }
                $value["comment_html"] = revert_comment_html($value["comment"], true);
                $value["comment"] = strip_tags(trim($value["comment"]));
                $value["attachments"] = $this->revert_comment_attachments($value["comment"]);
                $response["success"]["data"]["comments"]["records"][] = $value;
            }
            $response["success"]["data"]["comments"]["count"] = $comments["count"];
        }
        $this->render($response);
    }
    public function upload_file()
    {
        $this->load->library("dms", ["channel" => $this->user_logged_in_data["channel"], "user_id" => $this->user_logged_in_data["user_id"]]);
        $upload_success_files = $upload_failed_files = [];
        foreach ($_FILES as $file_key => $file) {
            if (!empty($file["name"])) {
                $upload_response = $this->dms->upload_file(["module" => "opinion", "module_record_id" => $this->input->post("opinion_id"), "upload_key" => $file_key]);
                if ($upload_response["status"]) {
                    $upload_success_files[] = ["id" => $upload_response["file"]["id"], "name" => $upload_response["file"]["full_name"]];
                } else {
                    $upload_failed_files[] = ["name" => $file["name"], "message" => $upload_response["message"]];
                }
            }
        }
        if (!empty($upload_success_files) && !empty($upload_failed_files)) {
            $response["success"]["data"]["files"] = $upload_success_files;
            $response["success"]["msg"] = $this->lang->line("files_uploaded_successfully");
            $response["error"]["data"]["files"] = $upload_failed_files;
            $response["error"]["msg"] = $this->lang->line("files_upload_failed");
        } else {
            if (!empty($upload_success_files)) {
                $response["success"]["data"]["files"] = $upload_success_files;
                $response["success"]["msg"] = $this->lang->line("files_uploaded_successfully");
            } else {
                $response["error"]["data"]["files"] = $upload_failed_files;
                $response["error"]["msg"] = $this->lang->line("files_upload_failed");
            }
        }
        $this->render($response);
    }
    public function download_file($file_id)
    {
        if ($file_id) {
            $this->load->library("dms");
            $response = $this->dms->get_file_download_data("opinion", $file_id);
            $content = $response["data"]["file_content"];
            if ($content) {
                $this->load->helper("download");
                force_download($file_id, $content);
            }
        }
    }
    public function return_doc_thumbnail($id = 0)
    {
        if ($id) {
            $this->load->model("document_management_system", "document_management_systemfactory");
            $this->document_management_system = $this->document_management_systemfactory->get_instance();
            $this->document_management_system->fetch($id);
            $documents_root_direcotry = $this->config->item("files_path") . DIRECTORY_SEPARATOR . "attachments" . DIRECTORY_SEPARATOR;
            $image_dir = $documents_root_direcotry . "opinions" . $this->document_management_system->get_field("lineage");
            $tmp_image = $this->config->item("files_path") . DIRECTORY_SEPARATOR . "tmp" . DIRECTORY_SEPARATOR . rand(1000, 9999) . $this->document_management_system->get_field("name") . "." . $this->document_management_system->get_field("extension");
            copy($image_dir, $tmp_image);
            $image_config = ["quality" => "100", "maintain_ratio" => false, "source_image" => $tmp_image, "new_image" => $tmp_image, "width" => 100, "height" => 100];
            $this->load->library("image_lib");
            $this->image_lib->clear();
            $this->image_lib->initialize($image_config);
            $this->image_lib->resize();
            $content = file_get_contents($tmp_image);
            if ($content) {
                unlink($tmp_image);
                $this->load->helper("download");
                force_download($id, $content);
            }
        }
    }
    private function revert_comment_attachments($comment)
    {
        $attachments_links = [];
        $attachments_images = [];
        preg_match_all("/\\[.*?\\]/", $comment, $matches_link);
        if (isset($matches_link) && !empty($matches_link)) {
            foreach ($matches_link[0] as $key => $value) {
                $file_name_link[$key] = explode("|", $value);
                if (isset($file_name_link[$key][1]) && is_numeric(str_replace("]", "", $file_name_link[$key][1]))) {
                    $attachments_links[$key]["file_id"] = str_replace("]", "", $file_name_link[$key][1]);
                    $attachments_links[$key]["download_link"] = base_url() . "legal_opinions/download_file/" . str_replace("]", "", $file_name_link[$key][1]);
                    $attachments_links[$key]["file_type"] = "file";
                    $attachments_links[$key]["file_name"] = str_replace("[", "", $file_name_link[$key][0]);
                }
            }
        }
        preg_match_all("/\\!.*?\\!/", $comment, $images_tag);
        if (isset($images_tag) && !empty($images_tag)) {
            foreach ($images_tag[0] as $k => $val) {
                $file_name_image[$k] = explode("|", $val);
                if (isset($file_name_image[$k][1]) && is_numeric(str_replace("!", "", $file_name_image[$k][1]))) {
                    $attachments_images[$k]["file_id"] = str_replace("!", "", $file_name_image[$k][1]);
                    $attachments_images[$k]["download_link"] = base_url() . "legal_opinions/return_doc_thumbnail/" . str_replace("!", "", $file_name_image[$k][1]);
                    $attachments_images[$k]["file_type"] = "image";
                    $attachments_images[$k]["file_name"] = str_replace("!", "", $file_name_image[$k][0]);
                }
            }
        }
        return array_merge($attachments_links, $attachments_images);
    }
    public function return_assignment_rules($type)
    {
        $response = $this->responseData;
        $response["success"]["data"] = $this->return_rule_per_type($type);
        $this->render($response);
    }
    private function return_rule_per_type($type)
    {
        $this->load->model("assignment", "assignmentfactory");
        $this->assignment = $this->assignmentfactory->get_instance();
        $data = [];
        if ($type && $this->assignment->fetch(["category" => "opinion", "type" => $type])) {
            $data = $this->assignment->get_fields();
            $data["assignment_id"] = $data["id"];
        }
        if ($data) {
            switch ($data["assignment_rule"]) {
                case "rr_algorithm":
                    $next_assignee = $this->assignment->load_next_opinion_assignee($data["id"]);
                    $user_id = $next_assignee["user_id"] ?: "";
                    break;
                default:
                    $user_id = $data["assignment_rule"];
                    $data["user"] = $this->user->get_name_by_id($user_id);
                    $data["user_relation"] = $user_id;
                    unset($data["id"]);
                    unset($data["assigned_team"]);
                    unset($data["assignment_rule"]);
                    unset($data["visible_assigned_team"]);
            }
        }
        return $data;
    }
    public function load_stage_data()
    {
        $response = $this->responseData;
        $legalCase_id = $this->input->post("caseId", true);
        if (!empty($legalCase_id)) {
            if ($this->legal_case->fetch($legalCase_id)) {
                $this->load->model("legal_case_litigation_detail", "legal_case_litigation_detailfactory");
                $this->legal_case_litigation_detail = $this->legal_case_litigation_detailfactory->get_instance();
                $response["success"]["data"]["stage"] = $this->legal_case_litigation_detail->load_stage_metadata($legalCase_id, false, $this->get_lang_code());
                $response["success"]["data"]["showStageEditLink"] = $this->legal_case_litigation_detail->load_all(["where" => ["legal_case_id", $legalCase_id]]) ? true : false;
                $response["error"] = "";
            } else {
                $response["error"] = $this->lang->line("case_id_not_exist");
            }
        } else {
            $response["error"] = $this->lang->line("data_missing");
        }
        $this->render($response);
    }
    private function send_notifications($object, $users = [])
    {
        $contributors = $users["contributors"];
        $opinion_id = $this->opinion->get_field("id");
        $assignee = $this->opinion->get_field("assigned_to");
        $assignee_id = str_pad($this->opinion->get_field("assigned_to"), 10, "0", STR_PAD_LEFT);
        $reporter_id = str_pad($this->opinion->get_field("reporter"), 10, "0", STR_PAD_LEFT);
        $toIds = [$assignee_id, $reporter_id];
        if (!empty($contributors)) {
            $toIds = array_merge($toIds, $contributors);
        }
        $this->load->model("opinion_types_language");
        $this->load->model("language");
        $lang = $this->system_preferences["SystemValues"]["systemLanguage"];
        $langId = $this->language->load(["where" => [["fullName", $lang]]])["id"];
        $this->opinion_types_language->fetch(["opinion_type_id" => $this->opinion->get_field("opinion_type_id"), "language_id" => $langId]);
        $opinionType = $this->opinion_types_language->get_field("name");
        $this->load->model("opinion_status");
        $this->opinion_status->fetch($this->opinion->get_field("opinion_status_id"));
        $opinion_status = $this->opinion_status->get_field("name");
        $this->load->library("system_notification");
        $this->load->library("email_notifications");
        $this->load->model("email_notification_scheme");
        $assignee_profile_name = $this->email_notification_scheme->get_user_full_name($assignee);
        $legal_case_subject = "";
        if ($this->opinion->get_field("legal_case_id") != 0) {
            $this->legal_case->fetch(["id" => $this->opinion->get_field("legal_case_id")]);
            $subject = $this->legal_case->get_field("subject");
            $legal_case_subject = $this->legal_case->get("modelCode") . $this->opinion->get_field("legal_case_id") . ": " . (42 < strlen($subject) ? mb_substr($subject, 0, 42) . "..." : $subject);
        }
        $notificationsData = ["toIds" => array_unique($toIds), "object" => $object, "object_id" => $opinion_id, "objectModelCode" => $this->opinion->get("modelCode"), "targetUser" => $assignee, "opinionData" => ["opinionID" => $opinion_id, "opinionType" => $opinionType, "opinionStatus" => $opinion_status, "priority" => $this->opinion->get_field("priority"), "dueDate" => $this->opinion->get_field("due_date"), "title" => $this->opinion->get_field("title"), "legalQuestion" => nl2br($this->opinion->get_field("legal_question")), "modifiedBy" => $this->email_notification_scheme->get_user_full_name($this->opinion->get_field("modifiedBy")), "assignee" => $assignee_profile_name, "legal_case" => $legal_case_subject, "legal_case_id" => $this->opinion->get_field("legal_case_id")], "attachments" => []];
        $this->system_notification->notification_add($notificationsData);
        if ($this->input->post("send_notifications_email")) {
            $model = $this->opinion->get("_table");
            $model_data["id"] = $opinion_id;
            $model_data["contributors_ids"] = $contributors;
            $notifications_emails = $this->email_notification_scheme->get_emails($object, $model, $model_data);
            extract($notifications_emails);
            $notificationsData["to"] = $to_emails;
            $notificationsData["cc"] = $cc_emails;
            $notificationsData["object_id"] = (int) $opinion_id;
            $notificationsData["fromLoggedUser"] = $this->user_logged_in_data["profileName"];
            $this->email_notifications->notify($notificationsData);
        }
    }
    public function opinions_contributed_by_me()
    {
        $this->load_opinions(false, 0, true);
    }
}

