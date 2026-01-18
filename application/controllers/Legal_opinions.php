<?php

if (!defined("BASEPATH")) {
    exit("No direct script access allowed");
}
require APPPATH . "controllers/Top_controller.php";
class Legal_opinions extends Top_controller
{
    public $Opinion;
    
    public function __construct()
    {
        parent::__construct();
        $this->load->model("opinion", "opinionfactory");
        $this->opinion = $this->opinionfactory->get_instance();
        $this->currentTopNavItem = "Legal Opinions";

        if (!$this->is_auth->is_logged_in()) {
            redirect("users/login");
        }
    }

    //Opinions
    public function my_opinions()
    {
        $this->authenticate_exempted_actions();
        $this->pageTitle = sprintf($this->lang->line("sheria360_browser_title"), $this->lang->line("legal_opinions") . " | " . $this->lang->line("legal_opinions"));
        $this->index($this->session->userdata("AUTH_user_id"));
    }
    public function all_opinions()
    {
        $this->pageTitle = sprintf($this->lang->line("sheria360_browser_title"), $this->lang->line("all_conveyance_frameworks") . " | " . $this->lang->line("opinion_in_menu"));
        $this->index();
    }
    public function opinions_reported_by_me()
    {
        $this->authenticate_exempted_actions();
        $this->pageTitle = sprintf($this->lang->line("sheria360_browser_title"), $this->lang->line("opinions_reported_by_me") . " | " . $this->lang->line("opinion_in_menu"));
        $this->index($this->session->userdata("AUTH_user_id"), true);
    }
    private function index($id = 0, $reported = false, $contributed = false)
    {
        $this->load->model("legal_case", "legal_casefactory");
        $this->legal_case = $this->legal_casefactory->get_instance();
        $this->load->model("grid_saved_column");
        $this->load->model("custom_field", "custom_fieldfactory");
        $this->custom_field = $this->custom_fieldfactory->get_instance();
        $systemPreferences = $this->session->userdata("systemPreferences");
        $hijri_calendar_enabled = isset($systemPreferences["hijriCalendarFeature"]) && $systemPreferences["hijriCalendarFeature"] ? $systemPreferences["hijriCalendarFeature"] : 0;
        $data["model"] = "Opinion";
        $grid_details = $this->grid_saved_column->get_user_grid_details($data["model"]);
        if ($this->input->post(NULL)) {
            if (!$this->input->is_ajax_request()) {
                redirect("dashboard");
            }
            $response = [];
            $filter = $this->input->post("filter");
            $sortable = $this->input->post("sort");
            $response = $this->opinion->k_load_all_opinions($filter, $sortable, "", false, $hijri_calendar_enabled);
            if ($this->input->post("savePageSize") || $this->input->post("sortData")) {
                $_POST["model"] = $data["model"];
                $this->grid_saved_column->save();
            }
            $response["columns_html"] = $this->load->view("grid_saved_columns/dialog_columns_contents", $grid_details, true);
            $this->output->set_content_type("application/json");
            $this->output->set_output(json_encode($response));
        } else {
            $this->load->helper(["text"]);
            $this->load->model("opinion_status");
            $this->load->model("opinion_type", "opinion_typefactory");
            $this->opinion_type = $this->opinion_typefactory->get_instance();
            $data["types"] = $this->opinion_type->load_list_per_language();
            unset($data["types"][""]);
            $configStatus = ["value" => "name"];

            $data["statuses"] = $this->opinion_status->load_list([], $configStatus);
            $data["operators"]["text"] = $this->get_filter_operators("text");
            $data["operators"]["number"] = $this->get_filter_operators("number");
            $data["operators"]["number_only"] = $this->get_filter_operators("number_only");
            $data["operators"]["list"] = $this->get_filter_operators("list");
            $data["operators"]["date"] = $this->get_filter_operators("date");
            $data["operators"]["lookup"] = $this->get_filter_operators("lookUp");
            $data["operators"]["group_list"] = $this->get_filter_operators("groupList");
            $data["archivedValues"] = array_combine($this->opinion->get("archivedValues"), [$this->lang->line("either"), $this->lang->line("yes"), $this->lang->line("no")]);
            $systemPreferences = $this->session->userdata("systemPreferences");
            $data["systemPreferences"] = $systemPreferences;
            $data["priorityValues"] = array_combine($this->opinion->get("priorityValues"), [$this->lang->line("critical"), $this->lang->line("high"), $this->lang->line("medium"), $this->lang->line("low")]);
            $data["assignedToFixedFilter"] = "";
            $data["reported_by_me_auth"] = "";
            $data["contributed_by_me_auth"] = "";
            $data["authUserId"] = $this->session->userdata("AUTH_user_id") * 1;
            if ($id == $data["authUserId"] && !$reported && !$contributed) {
                $data["assignedToFixedFilter"] = $this->session->userdata("AUTH_userProfileName");
                $data["my_opinions"] = true;
                $data["reported_by_me"] = false;
                $data["contributed_by_me_opinions"] = false;
            } else {
                $data["authUserId"] = $this->session->userdata("AUTH_user_id") * 1;
                if ($id == $data["authUserId"] && $reported && !$contributed) {
                    $data["authUserId"] = 0;
                    $data["my_opinions"] = false;
                    $data["reported_by_me_auth"] = $this->session->userdata("AUTH_userProfileName");
                    $data["reported_by_me"] = true;
                    $data["contributed_by_me_opinions"] = false;
                } else {
                    $data["authUserId"] = $this->session->userdata("AUTH_user_id") * 1;
                    if ($id == $data["authUserId"] && !$reported && $contributed) {
                        $data["authUserId"] = 0;
                        $data["my_opinions"] = false;
                        $data["contributed_by_me_auth"] = $this->session->userdata("AUTH_user_id");
                        $data["reported_by_me"] = false;
                        $data["contributed_by_me_opinions"] = true;
                    } else {
                        $data["authUserId"] = 0;
                        $data["my_opinions"] = false;
                        $data["reported_by_me"] = false;
                        $data["contributed_by_me_opinions"] = false;
                    }
                }
            }
            $this->load->model("user", "userfactory");
            $this->user = $this->userfactory->get_instance();
            $data["usersList"] = $this->user->load_users_list();
            $this->load->model("opinion_status");
            $data["opinionStatusValues"] = $this->opinion_status->load_list();
            $this->load->model("user_preference");
            $data["opinionStatusesSavedFilters"] = $this->user_preference->get_value("opinionStatusesFilter");
            $data["defaultArchivedValue"] = "no";
            $data["custom_fields"] = $this->custom_field->load_list_per_language($this->opinion->get("modelName"));
            $data["businessWeekDays"] = $systemPreferences["businessWeekEquals"];
            $data["businessDayHours"] = $systemPreferences["businessDayEquals"];
            $this->includes("kendoui/js/kendo.web.min", "js");
            $this->includes("kendoui/styles/kendo.common-bootstrap.min", "css");
            $this->includes("kendoui/styles/kendo.bootstrap.min", "css");
            $this->includes("jquery/form2js", "js");
            $this->includes("jquery/toObject", "js");

                $this->includes("scripts/opinions", "js");

            $this->includes("scripts/advance_search_custom_field_template", "js");
            $this->includes(app_url("compressed_asset/system_defaults.js?a=" . random_string("alpha")), "js", true);
            $this->includes("jquery/timemask", "js");
            if ($this->is_auth->is_layout_rtl()) {
                $this->includes("styles/rtl/fixes", "css");
            }

            $this->load->view("partial/header");
           $this->load->view("opinions/index_opinions", $data + $grid_details);

            $this->load->view("partial/footer");
        }
    }
    public function add($legal_case_id = 0, $hearing_id = 0, $stage_id = 0, $contract_id = 0)
    {
        $response = [];
        $result = false;
        $this->load->model("custom_field", "custom_fieldfactory");
        $this->custom_field = $this->custom_fieldfactory->get_instance();
        $this->load->model("opinion_status");
        $this->load->model("opinion_type", "opinion_typefactory");
        $this->opinion_type = $this->opinion_typefactory->get_instance();
        $this->load->library("TimeMask");
        $system_preferences = $this->session->userdata("systemPreferences");
        $this->load->model("legal_case", "legal_casefactory");
        $this->legal_case = $this->legal_casefactory->get_instance();
        $opinion_types = $this->opinion_type->load_list_per_language();
        $this->load->helper("revert_comment_html");
        $this->load->helper("format_comment_patterns");
        if (!$this->input->post(NULL)) {
            $this->load->model("system_preference");
            $system_preference = $this->system_preference->get_key_groups();
            $system_preference["DefaultValues"]["opinionPrivacyBasedOnMatterPrivacy"]=0; //hardcoded temprary by ating to remove error of undefined
            $allow_matter_opinion_privacy = $system_preference["DefaultValues"]["opinionPrivacyBasedOnMatterPrivacy"];
            $data["allowOpinionPrivacy"] = $allow_matter_opinion_privacy;
            $data["archivedValues"] = array_combine($this->opinion->get("archivedValues"), $this->opinion->get("archivedValues"));
            $data["system_preferences"] = $this->session->userdata("systemPreferences");
            $data["loggedInUser"] = $this->session->userdata("AUTH_userProfileName");
            $data["type"] = $opinion_types;
            $data["status"] = $this->opinion_status->load_list();
            $data["toMeId"] = $this->is_auth->get_user_id();
            $data["toMeFullName"] = $this->is_auth->get_fullname();
            $data["priorities"] = array_combine($this->opinion->get("priorityValues"), [$this->lang->line("critical"), $this->lang->line("high"), $this->lang->line("medium"), $this->lang->line("low")]);
            $sDate = date("Y-m-d", time());
            $data["opinionData"] = ["id" => "", "contract_id" => "", "contract_name" => "", "legal_case_id" => "", "caseSubject" => "", "caseCategory" => "", "user_id" => "", "reporter" => "", "title" => "", "assigned_to" => "", "due_date" => $sDate, "private" => "", "priority" => "", "opinion_location_id" => "", "location" => "", "estimated_effort" => "", "detailed_info" => "","background_info"=>"","legal_question"=>"","requester"=>"", "opinion_status_id" => "", "opinion_type_id" => "", "assignee_fullname" => "", "assignee_status" => "", "reporter_fullname" => "", "reporter_status" => "", "archived" => ""];
            $data["title"] = $this->lang->line("add_opinions");
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
            $data["system_preferences"]["opinionTypeId"]=1;//addded by ating as this is not fetched
            $data["assignments"] = $this->return_assignments_rules($data["system_preferences"]["opinionTypeId"]);
            if ($data["assignments"]) {
                $data["opinionData"]["assignee_fullname"] = $data["assignments"]["user"]["name"];
            }
            $this->load->model("email_notification_scheme");
            $data["hide_show_notification"] = $this->email_notification_scheme->get_hide_show_send_email_notification_by_trigger_action("add_opinions");
            $this->load->model("reminder", "reminderfactory");
            $this->reminder = $this->reminderfactory->get_instance();
            $data["notify_me_before_time_types"] = array_combine($this->reminder->get("notify_me_before_time_type"), [$this->lang->line("day_or_days"), $this->lang->line("week_or_weeks"), $this->lang->line("month")]);
            $data["notify_me_before_types"] = array_combine($this->reminder->get("notify_me_before_type"), [$this->lang->line("reminder_popup"), $this->lang->line("reminder_popup_and_email")]);
            $data["notify_before"] = false;
            $data["custom_fields"] = $this->custom_field->get_field_html($this->opinion->get("modelName"), 0);
            $data["litigation_stage_html"] = $this->return_litigation_stage_html($legal_case_id, $stage_id);
            $response["html"] = $this->load->view("opinions/form", $data, true);
        } else {
            $detailed_info = $this->input->post("detailed_info", true, "<h1><h2><h3><h4><h5><h6><strong><em><u><a><p><img>");
            $_POST["detailed_info"] = format_comment_patterns($this->regenerate_note($detailed_info));
            $is_clone = $this->input->post("clone");
            $post_data = $this->input->post(NULL);
            $this->opinion->set_fields($post_data);
            $this->opinion->set_field("detailed_info", $this->input->post("detailed_info", true, "<h1><h2><h3><h4><h5><h6><strong><em><u><a><p><img>"));
            $this->opinion->set_field("user_id", $this->is_auth->get_user_id());
            $this->opinion->set_field("archived", "no");
            $this->opinion->set_field("opinion_status", 1);
            $this->load->model("opinion_workflow", "opinion_workflowfactory");
            $this->opinion_workflow = $this->opinion_workflowfactory->get_instance();
            $workflow_applicable = $this->opinion_workflow->load_workflow_opinion_status_per_type($this->input->post("opinion_type_id")) ?: $this->opinion_workflow->load_default_system_workflow();
            $this->opinion->set_field("opinion_status_id", $workflow_applicable["status"]);
            $this->opinion->set_field("workflow", $workflow_applicable["workflow_id"]);
            $lookup_validate = $this->opinion->get_lookup_validation_errors($this->opinion->get("lookupInputsToValidate"), $post_data);
            $estimated_effort = $this->input->post("estimated_effort");
            $estimated_effort_value = 0;
            if (!empty($estimated_effort)) {
                $estimated_effort_value = $this->timemask->humanReadableToHours($estimated_effort);
            }
            $this->opinion->set_field("estimated_effort", $estimated_effort_value);
            $custom_field_validation = !empty($post_data["customFields"]) ? $this->custom_field->validate_custom_field($post_data["customFields"]) : "";
            if ($this->opinion->validate() && !$lookup_validate && (empty($post_data["customFields"]) || $custom_field_validation["result"])) {
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
                                $next_assignee = $this->assignment->load_next_opinion_assignee($this->input->post("assignment_id"), $parameter_to_send);
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
                    $result = $this->opinion->insert();
                    if ($result) {
                        $opinion_id = $this->opinion->get_field("id");
                        $this->opinion->update_recent_ids($opinion_id, "opinions");
                        $response["opinion_code"] = $this->opinion->get("modelCode") . $opinion_id;
                        $response["id"] = $opinion_id;
                        $case_id = $this->opinion->get_field("legal_case_id");
                        if ($case_id) {
                            $this->legal_case->set_field("id", $case_id);
                            $this->legal_case->touch_logs();
                            if ($this->opinion->get_field("stage")) {
                                $this->load->model("legal_case_litigation_detail", "legal_case_litigation_detailfactory");
                                $this->legal_case_litigation_detail = $this->legal_case_litigation_detailfactory->get_instance();
                                $this->legal_case_litigation_detail->update_stage_order($this->opinion->get_field("stage"));
                            }
                        }
                        if (!empty($post_data["customFields"]) && is_array($post_data["customFields"]) && count($post_data["customFields"])) {
                            foreach ($post_data["customFields"] as $key => $field) {
                                $post_data["customFields"][$key]["recordId"] = $opinion_id;
                            }
                            $this->custom_field->update_custom_fields($post_data["customFields"]);
                        }
                        $this->notify_me_before_due_date($opinion_id, $case_id);
                        $getting_started_settings = unserialize($this->user_preference->get_value("getting_started"));
                        $getting_started_settings["add_opinion_step_done"] = true;
                        $this->user_preference->set_value("getting_started", serialize($getting_started_settings), true);
                        $inserted = $this->insert_related_users($opinion_id, "add_opinions");
                        $response["totalNotifications"] = $inserted["total_notifications"];
                        if ($this->input->post("legal_case_event")) {
                            $this->load->model("legal_case_event_related_data", "legal_case_event_related_datafactory");
                            $this->legal_case_event_related_data = $this->legal_case_event_related_datafactory->get_instance();
                            $this->legal_case_event_related_data->set_field("event", $this->input->post("legal_case_event"));
                            $this->legal_case_event_related_data->set_field("related_id", $opinion_id);
                            $this->legal_case_event_related_data->set_field("related_object", $this->opinion->get("modelName"));
                            $this->legal_case_event_related_data->insert();
                        }
                        $this->load->library("dmsnew");
                        $failed_uploads_count = 0;
                        foreach ($_FILES as $file_key => $file) {
                            if ($file["error"] != 4) {
                                $upload_response = $this->dmsnew->upload_file(["module" => "opinion", "module_record_id" => $opinion_id, "lineage" => $this->input->post("lineage"), "upload_key" => $file_key]);
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
                    $opinion_id = $this->opinion->get_field("id");
                    $this->load->model(["opinion_status", "user_profile"]);
                    $this->load->model("opinion_type", "opinion_typefactory");
                    $this->opinion_type = $this->opinion_typefactory->get_instance();
                    $data = [];
                    $data["opinionData"] = $this->opinion->load_opinion($opinion_id);
                    if ($data["opinionData"]) {
                        $data["opinionData"]["createdBy"] = "";
                        $data["opinionData"]["id"] = "";
                        $this->user_profile->fetch(["user_id" => $data["opinionData"]["assigned_to"]]);
                        $data["type"] = $this->opinion_type->load_list_per_language();
                        $data["status"] = $this->opinion_status->load_list();
                        $data["toMeId"] = $this->is_auth->get_user_id();
                        $data["toMeFullName"] = $this->is_auth->get_fullname();
                        $data["opinionModelCode"] = $this->opinion->get("modelCode");
                        $data["priorities"] = array_combine($this->opinion->get("priorityValues"), [$this->lang->line("critical"), $this->lang->line("high"), $this->lang->line("medium"), $this->lang->line("low")]);
                        $data["opinionUsers"] = $this->opinion->load_opinion_users($opinion_id);
                        $response["cloned"] = true;
                    }
                }
                $response["result"] = $result && $clone_result;
            } else {
                $response["validationErrors"] = $this->opinion->get_validation_errors($lookup_validate);
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
            $this->load->model(["opinion_status", "user_profile"]);
            $this->load->model("opinion_type", "opinion_typefactory");
            $this->opinion_type = $this->opinion_typefactory->get_instance();
            $data = [];
            $data["opinionData"] = $this->opinion->load_opinion($id);
            $this->load->model("email_notification_scheme");
            $data["hide_show_notification"] = $this->email_notification_scheme->get_hide_show_send_email_notification_by_trigger_action("edit_opinion");
            if ($data["opinionData"]) { 
                $system_preference["DefaultValues"]["opinionPrivacyBasedOnMatterPrivacy"]=0; //hardcoded temprary by ating to remove error of undefined
                $allow_matter_opinion_privacy = $system_preference["DefaultValues"]["opinionPrivacyBasedOnMatterPrivacy"];
                $data["allowOpinionPrivacy"] = $allow_matter_opinion_privacy;
                $data["type"] = $this->opinion_type->load_list_per_language();
                $data["status"] = $this->opinion_status->load_list();
                $data["toMeId"] = $this->is_auth->get_user_id();
                $data["toMeFullName"] = $this->is_auth->get_fullname();
                $data["opinionModelCode"] = $this->opinion->get("modelCode");
                $data["case_model_code"] = $this->legal_case->get("modelCode");
                $data["contract_model_code"] = $this->contract->get("modelCode");
                $data["priorities"] = array_combine($this->opinion->get("priorityValues"), [$this->lang->line("critical"), $this->lang->line("high"), $this->lang->line("medium"), $this->lang->line("low")]);
                $data["contributors"] = $this->validate_id($id) ? $this->opinion->load_opinion_contributors($id) : [];
                $data["opinionUsers"] = $this->opinion->load_opinion_users($id);
                $data["system_preferences"] = $this->session->userdata("systemPreferences");
                $data["title"] = $this->lang->line("edit_opinion") . ": " . $data["opinionModelCode"] . $data["opinionData"]["OpinionId"];
                $this->load->model("reminder", "reminderfactory");
                $this->reminder = $this->reminderfactory->get_instance();
                $data["notify_me_before_time_types"] = array_combine($this->reminder->get("notify_me_before_time_type"), [$this->lang->line("day_or_days"), $this->lang->line("week_or_weeks"), $this->lang->line("month")]);
                $data["notify_me_before_types"] = array_combine($this->reminder->get("notify_me_before_type"), [$this->lang->line("reminder_popup"), $this->lang->line("reminder_popup_and_email")]);
                $data["notify_before"] = $this->reminder->load_notify_before_data_to_related_object($id, $this->opinion->get("_table"));
                $data["custom_fields"] = $this->custom_field->get_field_html($this->opinion->get("modelName"), $id);
                $data["opinionData"]["estimated_effort"] = $this->timemask->timeToHumanReadable($data["opinionData"]["estimated_effort"]);
                $data["assignments"] = false;
                $this->opinion->update_recent_ids($id, "opinions");
                $response["stage_html"] = $this->return_litigation_stage_html($data["opinionData"]["legal_case_id"], $data["opinionData"]["stage"], $id);
                $reporter_user = (int) $data["opinionData"]["reporter"];
                $response["restricted_by_opinion_reporter"] = $only_reporter_edit_meta_data == "1" && $current_user != $reporter_user ? true : false;
                $response["html"] = $this->load->view("opinions/form", $data, true);
            }
            $response["data"] = $data;
        } else {
            $opinion_id = $this->input->post("id");
            $detailed_info = $this->input->post("detailed_info", true, "<h1><h2><h3><h4><h5><h6><strong><em><u><a><p><img><ul><ol><li>");
            $_POST["detailed_info"] = format_comment_patterns($this->regenerate_note($detailed_info));
            $this->opinion->fetch(["id" => $opinion_id]);
            $restricted_by_opinion_reporter = false;
            if ($this->opinion->get_field("id")) {
                $reporter_user = (int) $this->opinion->get_field("reporter");
                $old_case_id = $this->opinion->get_field("legal_case_id");
                if ($only_reporter_edit_meta_data == "1" && $current_user != $reporter_user) {
                    $restricted_by_opinion_reporter = true;
                }
            }
            if ($restricted_by_opinion_reporter) {
                $opinion_details = $this->opinion->get_fields();
                $post_data["assigned_to"] = $opinion_details["assigned_to"];
                $post_data["reporter"] = $opinion_details["reporter"];
                $post_data["opinion_type_id"] = $opinion_details["opinion_type_id"];
                $post_data["detailed_info"] = $opinion_details["detailed_info"];
                $post_data["due_date"] = $opinion_details["due_date"];
            }
            if (!empty($post_data["opinion_type_id"]) && $post_data["opinion_type_id"] !== $this->opinion->get_field("opinion_type_id")) {
                $this->load->model("opinion_workflow", "opinion_workflowfactory");
                $this->opinion_workflow = $this->opinion_workflowfactory->get_instance();
                $workflow_applicable = $this->opinion_workflow->load_workflow_opinion_status_per_type($this->input->post("opinion_type_id")) ?: $this->opinion_workflow->load_default_system_workflow();
                $this->opinion->set_field("opinion_status_id", $workflow_applicable["status"]);
                $this->opinion->set_field("workflow", $workflow_applicable["workflow_id"]);
            }
            $this->opinion->set_field("private", $this->input->post("private") ?? "");
            $this->opinion->set_fields($post_data);
            $this->opinion->set_field("detailed_info", $this->input->post("detailed_info", true, "<h1><h2><h3><h4><h5><h6><strong><em><u><a><p><img><ul><ol><li>"));
            $this->opinion->set_field("estimated_effort", $this->input->post("estimated_effort") ?? $this->opinion->get_field("estimated_effort"));
            $lookup_validate = $this->opinion->get_lookup_validation_errors($this->opinion->get("lookupInputsToValidate"), $post_data);
            $estimated_effort = $this->input->post("estimated_effort");
            $estimated_effort_value = 0;
            if (!empty($estimated_effort)) {
                $estimated_effort_value = $this->timemask->humanReadableToHours($estimated_effort);
            }
            $this->opinion->set_field("estimated_effort", $estimated_effort_value);
            $custom_field_validation = !empty($post_data["customFields"]) ? $this->custom_field->validate_custom_field($post_data["customFields"]) : "";
            if ($this->opinion->validate() && !$lookup_validate && (empty($post_data["customFields"]) || $custom_field_validation["result"])) {
                $notify_before = $this->input->post("notify_me_before");
                $due_date = $this->input->post("due_date");
                if ($notify_before && $due_date && (!$notify_before["time"] || !$notify_before["time_type"] || !$notify_before["type"] || ($is_not_nb = !is_numeric($notify_before["time"])))) {
                    if ($is_not_nb) {
                        $response["validationErrors"]["notify_before"] = sprintf($this->lang->line("is_numeric_rule"), $this->lang->line("notify_before"));
                    } else {
                        $response["validationErrors"]["notify_before"] = $this->lang->line("cannot_be_blank_rule");
                    }
                } else {
                    $result = $this->opinion->update();
                    $case_id = $this->opinion->get_field("legal_case_id");
                    if ($result && $case_id) {
                        $this->legal_case->set_field("id", $case_id);
                        $this->legal_case->touch_logs();
                        if ($this->opinion->get_field("stage")) {
                            $this->load->model("legal_case_litigation_detail", "legal_case_litigation_detailfactory");
                            $this->legal_case_litigation_detail = $this->legal_case_litigation_detailfactory->get_instance();
                            $this->legal_case_litigation_detail->update_stage_order($this->opinion->get_field("stage"));
                        }
                    }
                    if ($result) {
                        $_opinionID = $this->opinion->get_field("id");
                        $contributors = $this->input->post("contributors") ?? [];
                        $this->load->model("opinion_contributor");
                        $contributors_data = ["opinion_id" => $opinion_id, "users" => $contributors];
                        $this->opinion_contributor->insert_contributors($contributors_data);
                        if (!empty($post_data["customFields"]) && is_array($post_data["customFields"]) && count($post_data["customFields"])) {
                            foreach ($post_data["customFields"] as $key => $field) {
                                $post_data["customFields"][$key]["recordId"] = $_opinionID;
                            }
                            $this->custom_field->update_custom_fields($post_data["customFields"]);
                        }
                        $inserted = $this->insert_related_users($opinion_id, "edit_opinion");
                        $response["totalNotifications"] = $inserted["total_notifications"];
                        $posted_case_id = $post_data["legal_case_id"];
                        if ($old_case_id != $posted_case_id) {
                            $this->load->model("opinion_document");
                            $this->opinion_document->delete_opinion_document($opinion_id);
                        }
                    }
                    $response["result"] = $result;
                }
            } else {
                $response["validationErrors"] = $this->opinion->get_validation_errors($lookup_validate);
                if (!empty($post_data["customFields"]) && !$custom_field_validation["result"]) {
                    $response["validationErrors"] = $response["validationErrors"] + $custom_field_validation["validationErrors"];
                }
            }
        }
        $this->output->set_content_type("application/json")->set_output(json_encode($response));
    }

    public function delete()
    {
        $id = $this->input->post("opinionId");
        $response = [];
        $response["status"] = 101;
        if ($id) { 
            $this->opinion->fetch($id); 
            $timeTrackingOpinionNumRowsRelated = $this->opinion->count_related_time_tracking_contact($id);
            $related_expenses = 0;//$this->opinion->count_related_expenses($id); removed because no expenses related to this
            if (0 < $timeTrackingOpinionNumRowsRelated || 0 < $related_expenses) {
                $response["status"] = 303;
            } else { 
                $this->load->model("opinion_document");
                $attachments = $this->opinion_document->get_document_by_opinion_id($id);
                $this->load->library("dmsnew");
                foreach ($attachments as $attachment) {
                    $file_id = $attachment["document_id"];
                    $this->dmsnew->delete_document("case", $file_id);
                }
                $this->load->model("reminder", "reminderfactory");
                $this->reminder = $this->reminderfactory->get_instance();
                $this->reminder->dismiss_related_reminders_by_related_object_ids($id, "opinion_id");
                $response["status"] = $this->opinion->delete(["where" => ["opinions.id", $id]]) ? 202 : 101;
            }
            if ($response["status"] == 202) { 
                $this->load->model("legal_case_event_related_data", "legal_case_event_related_datafactory");
                $this->legal_case_event_related_data = $this->legal_case_event_related_datafactory->get_instance();
                $this->legal_case_event_related_data->delete_related_object($id, $this->opinion->get("modelName"));
                $this->load->model("custom_field", "custom_fieldfactory");
                $this->custom_field = $this->custom_fieldfactory->get_instance();
                $custom_fields = $this->custom_field->load_custom_fields($id, "opinion");
                $custom_fields_id = array_column($custom_fields, "id");
                if (isset($custom_fields_id) && !empty($custom_fields_id)) {
                    $this->db->where("recordId", $id)->where_in("custom_field_id", $custom_fields_id)->delete("custom_field_values");
                }
                if ($this->opinion->get_field("legal_case_id")) {
                    $this->load->model("legal_case", "legal_casefactory");
                    $this->legal_case = $this->legal_casefactory->get_instance();
                    $this->legal_case->set_field("id", $this->opinion->get_field("legal_case_id"));
                    $this->legal_case->touch_logs();
                }
            }
        }
        $this->output->set_content_type("application/json")->set_output(json_encode($response));
    }
    public function archive_unarchive_opinions()
    {
        $response = [];
        if (!$this->input->post(NULL)) {
            $systemPreferences = $this->session->userdata("systemPreferences");
            $affectedRows = $this->opinion->archieved_opinions_total_number();
            $this->db->where("opinions.opinion_status_id IN ( " . $systemPreferences["archiveOpinionStatus"] . ")")->update("opinions", ["archived" => "yes"]);
            $this->load->model("opinion_status");
            $archiveOpinionStatus = $this->opinion_status->load_list(["where" => [["id IN ( " . $systemPreferences["archiveOpinionStatus"] . ")", NULL, false]]]);
            $archiveOpinionStatusStr = implode(", ", array_values($archiveOpinionStatus));
            $this->set_flashmessage("information", sprintf($this->lang->line("feedback_message_archived_object"), $affectedRows, $this->lang->line("opinions"), $archiveOpinionStatusStr));
        } else {
            $gridData = $this->input->post("gridData");
            foreach ($gridData["opinionIds"] as $key => $id) {
                $this->opinion->fetch($id);
                $this->opinion->set_field("archived", "no");
                $result = $this->opinion->update();
                if ($result && $this->opinion->get_field("stage")) {
                    $this->load->model("legal_case_litigation_detail", "legal_case_litigation_detailfactory");
                    $this->legal_case_litigation_detail = $this->legal_case_litigation_detailfactory->get_instance();
                    $this->legal_case_litigation_detail->update_stage_order($this->opinion->get_field("stage"));
                }
            }
            $response["status"] = $result ? 202 : 101;
            if ($result && isset($gridData["case_id"])) {
                $this->load->model("legal_case", "legal_casefactory");
                $this->legal_case = $this->legal_casefactory->get_instance();
                $this->legal_case->set_field("id", $gridData["case_id"]);
                $this->legal_case->touch_logs();
            }
        }
        $this->output->set_content_type("application/json");
        $this->output->set_output(json_encode($response));
    }
    public function autocomplete()
    {
        if (!$this->input->is_ajax_request()) {
            show_404();
        }
        if (!$this->is_auth->is_logged_in()) {
            exit("login_needed");
        }
        $term = trim((string) $this->input->get("term"));
        $results = $this->opinion->lookup($term);
        $this->output->set_content_type("application/json");
        $this->output->set_output(json_encode($results));
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
        $langId = $this->language->get_id_by_session_lang();
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
        $notificationsData = ["toIds" => array_unique($toIds), "object" => $object, "object_id" => $opinion_id, "objectModelCode" => $this->opinion->get("modelCode"), "targetUser" => $assignee, "opinionData" => ["opinionID" => $opinion_id, "opinionType" => $opinionType, "opinionStatus" => $opinion_status, "priority" => $this->opinion->get_field("priority"), "dueDate" => $this->opinion->get_field("due_date"), "opinionDetailed_info" => nl2br($this->opinion->get_field("detailed_info")), "modifiedBy" => $this->email_notification_scheme->get_user_full_name($this->opinion->get_field("modifiedBy")), "assignee" => $assignee_profile_name, "legal_case" => $legal_case_subject, "legal_case_id" => $this->opinion->get_field("legal_case_id")], "attachments" => []];
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
            $notificationsData["fromLoggedUser"] = $this->is_auth->get_fullname();
            $this->email_notifications->notify($notificationsData);
        }
    }
    public function location_autocomplete()
    {
        if (!$this->is_auth->is_logged_in()) {
            redirect("users/login");
        }
        $term = trim((string) $this->input->get("term"));
        $results = $this->opinion->lookup_location($term);
        $this->output->set_content_type("application/json");
        $this->output->set_output(json_encode($results));
    }
    private function notify_me_before_due_date($opinion_id, $case_id)
    {
        $notify_before = $this->input->post("notify_me_before");
        $due_date = $this->input->post("due_date");
        $current_reminder = $this->reminder->load_notify_before_data_to_related_object($opinion_id, $this->opinion->get("_table"));
        if ($current_reminder && !$notify_before) {
            return $this->reminder->remind_before_due_date([], $current_reminder["id"]);
        }
        if ($notify_before && $due_date) {
            $reminder = ["user_id" => $this->is_auth->get_user_id(), "remindDate" => $due_date, "legal_case_id" => $case_id, "opinion_id" => $opinion_id, "related_id" => $opinion_id, "related_object" => $this->opinion->get("_table"), "notify_before_time" => $notify_before["time"], "notify_before_time_type" => $notify_before["time_type"], "notify_before_type" => $notify_before["type"]];
            $reminder["summary"] = sprintf($this->lang->line("notify_me_before_message"), $this->lang->line("opinion"), $this->opinion->get("modelCode") . $opinion_id, $due_date);
            return $this->reminder->remind_before_due_date($reminder, isset($notify_before["id"]) ? $notify_before["id"] : NULL);
        }
        return false;
    }
    public function view($id = 0, $comment = 0)
    {
        if (!$this->validate_id($id)) {
            $this->set_flashmessage("error", $this->lang->line("invalid_record"));
            redirect("legal_opinions/my_opinions");
        }
        $this->pageTitle = sprintf($this->lang->line("sheria360_browser_title"), $this->lang->line("opinion"));
        $data = [];
        $opinion_data = $this->opinion->load_opinion($id);
        if (!$opinion_data) {
            $this->set_flashmessage("error", $this->lang->line("permission_not_allowed"));
            redirect("legal_opinions/my_opinions");
        }
        $this->load->model("legal_case", "legal_casefactory");
        $this->legal_case = $this->legal_casefactory->get_instance();
        $this->load->model("contract", "contractfactory");
        $this->contract = $this->contractfactory->get_instance();
        $this->load->helper("revert_comment_html");
        $data["opinion_data"] = $opinion_data;
        $data["opinion_data"]["estimated_effort"] = (double) $data["opinion_data"]["estimated_effort"];
        $data["opinion_data"]["effectiveEffort"] = (double) $data["opinion_data"]["effectiveEffort"];
        $data["opinion_data"]["model_code"] = $this->opinion->get("modelCode");
        $data["opinion_data"]["case_model_code"] = $this->legal_case->get("modelCode");
        $data["opinion_data"]["contract_model_code"] = $this->contract->get("modelCode");
        $data["opinion_data"]["clientName"] = NULL;
        if (isset($opinion_data["opinion_legal_case"]) && !empty($opinion_data["opinion_legal_case"])) {
            $this->load->model("client");
            $case_client_details = $this->client->load_case_client_details($opinion_data["opinion_legal_case"]);
            if ($case_client_details) {
                $data["opinion_data"]["clientName"] = $case_client_details["clientName"];
                $data["opinion_data"]["clientId"] = $case_client_details["clientId"];
            }
        }
        $this->load->model("opinion_workflow_status_transition", "opinion_workflow_status_transitionfactory");
        $this->opinion_workflow_status_transition = $this->opinion_workflow_status_transitionfactory->get_instance();
        $transitions_accessible = $this->opinion_workflow_status_transition->load_available_steps($data["opinion_data"]["opinion_status_id"], $data["opinion_data"]["workflow"]);
        $data["available_statuses"] = $transitions_accessible["available_statuses"];
        $data["status_transitions"] = $transitions_accessible["status_transitions"];
        $data["time_tracking"] = $this->time_tracking_calculation($data["opinion_data"]);
        $data["docs"]["module_container"] = "opinions";
        $data["docs"]["directory"] = $this->config->item("files_path") . DIRECTORY_SEPARATOR . "attachments" . DIRECTORY_SEPARATOR . DIRECTORY_SEPARATOR . $data["docs"]["module_container"];
        $this->load->model("custom_field", "custom_fieldfactory");
        $this->custom_field = $this->custom_fieldfactory->get_instance();
        $custom_fields = $this->custom_field->load_custom_fields($id, $this->opinion->get("modelName"));
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

        $this->opinion->update_recent_ids($id, "opinions");
        $data["contributors"] = $this->opinion->load_opinion_contributors($id);
        $data["activeComment"] = $comment;

        $data["objName"] ="opinion";
        $data["module"] = "opinions";
        $data["module_record"] = "contract";
        $data["module_record_id"] = $id;
        $data["module_controller"] = "legal_opinions";
        $data["module_version"] ="";

        $this->load->model("document_management_system", "document_management_systemfactory");
        $this->document_management_system = $this->document_management_systemfactory->get_instance();
        $this->includes("jquery/dropzone", "js");
        $this->includes("jquery/css/dropzone", "css");
        $this->includes("scripts/opinion_view", "js");
        //atinga docs manage,ent test


        $this->includes("kendoui/js/kendo.web.min", "js");
        //$this->includes("scripts/opinion_documents_management_system", "js");//atinga test docs
        $this->includes("kendoui/js/kendo.web.min", "js");
        $this->includes("kendoui/styles/kendo.common-bootstrap.min", "css");
        $this->includes("kendoui/styles/kendo.bootstrap.min", "css");

        $this->load->library("TimeMask");
        $this->load->view("partial/header");
        $this->load->view("opinions/view/main_section", $data);
        $this->load->view("partial/footer");
    }
    public function conveyancingView($id = 0, $comment = 0)
    {
        if (!$this->validate_id($id)) {
            $this->set_flashmessage("error", $this->lang->line("invalid_record"));
            redirect("legal_opinions/conveyancing");
        }
        $this->pageTitle = sprintf($this->lang->line("sheria360_browser_title"), $this->lang->line("opinion"));
        $data = [];
        $opinion_data = $this->opinion->load_opinion($id);
        if (!$opinion_data) {
            $this->set_flashmessage("error", $this->lang->line("permission_not_allowed"));
            redirect("legal_opinions/conveyancing");
        }
        $this->load->model("legal_case", "legal_casefactory");
        $this->legal_case = $this->legal_casefactory->get_instance();
        $this->load->model("contract", "contractfactory");
        $this->contract = $this->contractfactory->get_instance();
        $this->load->helper("revert_comment_html");
        $data["opinion_data"] = $opinion_data;
        $data["opinion_data"]["estimated_effort"] = (double) $data["opinion_data"]["estimated_effort"];
        $data["opinion_data"]["effectiveEffort"] = (double) $data["opinion_data"]["effectiveEffort"];
        $data["opinion_data"]["model_code"] = $this->opinion->get("modelCode");
        $data["opinion_data"]["case_model_code"] = $this->legal_case->get("modelCode");
        $data["opinion_data"]["contract_model_code"] = $this->contract->get("modelCode");
        $data["opinion_data"]["clientName"] = NULL;
        if (isset($opinion_data["opinion_legal_case"]) && !empty($opinion_data["opinion_legal_case"])) {
            $this->load->model("client");
            $case_client_details = $this->client->load_case_client_details($opinion_data["opinion_legal_case"]);
            if ($case_client_details) {
                $data["opinion_data"]["clientName"] = $case_client_details["clientName"];
                $data["opinion_data"]["clientId"] = $case_client_details["clientId"];
            }
        }
        $this->load->model("opinion_workflow_status_transition", "opinion_workflow_status_transitionfactory");
        $this->opinion_workflow_status_transition = $this->opinion_workflow_status_transitionfactory->get_instance();
        $transitions_accessible = $this->opinion_workflow_status_transition->load_available_steps($data["opinion_data"]["opinion_status_id"], $data["opinion_data"]["workflow"]);
        $data["available_statuses"] = $transitions_accessible["available_statuses"];
        $data["status_transitions"] = $transitions_accessible["status_transitions"];
        $data["time_tracking"] = $this->time_tracking_calculation($data["opinion_data"]);
        $data["docs"]["module_container"] = "opinions";
        $data["docs"]["directory"] = $this->config->item("files_path") . DIRECTORY_SEPARATOR . "attachments" . DIRECTORY_SEPARATOR . DIRECTORY_SEPARATOR . $data["docs"]["module_container"];
        $this->load->model("custom_field", "custom_fieldfactory");
        $this->custom_field = $this->custom_fieldfactory->get_instance();
        $custom_fields = $this->custom_field->load_custom_fields($id, $this->opinion->get("modelName"));
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

        $this->opinion->update_recent_ids($id, "opinions");
        $data["contributors"] = $this->opinion->load_opinion_contributors($id);
        $data["activeComment"] = $comment;

        $data["objName"] ="opinion";
        $data["module"] = "opinions";
        $data["module_record"] = "contract";
        $data["module_record_id"] = $id;
        $data["module_controller"] = "legal_opinions";
        $data["module_version"] ="";

        $this->load->model("document_management_system", "document_management_systemfactory");
        $this->document_management_system = $this->document_management_systemfactory->get_instance();
        $this->includes("jquery/dropzone", "js");
        $this->includes("jquery/css/dropzone", "css");
        $this->includes("scripts/opinion_view", "js");
        //atmga docs manage,ent test


        $this->includes("kendoui/js/kendo.web.min", "js");
        $this->includes("scripts/opinion_documents_management_system", "js");//atinga test docs
        $this->includes("kendoui/js/kendo.web.min", "js");
        $this->includes("kendoui/styles/kendo.common-bootstrap.min", "css");
        $this->includes("kendoui/styles/kendo.bootstrap.min", "css");

        $this->load->library("TimeMask");
        $this->load->view("partial/header");
        $this->load->view("conveyancing/view/main_section", $data);
        $this->load->view("partial/footer");
    }
    public function opinion_load_documents()
    {
        $this->load_documents();
    }
    public function opinion_download_file()
    {

    }
    /////
    private function time_tracking_calculation($data)
    {
        $response["estimated_width"] = $data["estimated_effort"] ? 100 : 0;
        $response["logged_width"] = $data["estimated_effort"] && $data["effectiveEffort"] ? $data["effectiveEffort"] * 100 / $data["estimated_effort"] : ($data["effectiveEffort"] ? 100 : 0);
        $response["remaining_width"] = $response["logged_width"] ? 100 - $response["logged_width"] : 0;
        $response["estimated_status"] = $response["estimated_width"] ? "active" : "inactive";
        $response["logged_status"] = $response["remaining_width"] < 0 ? "warning" : ($response["logged_width"] ? "active" : "inactive");
        $response["remaining_status"] = 0 < $response["remaining_width"] ? "active" : "inactive";
        return $response;
    }
    public function comments()
    {
        if (!$this->input->is_ajax_request()) {
            show_404();
        }
        $this->load->helper("revert_comment_html");
        $this->load->helper("format_comment_patterns");
        $id = $this->input->get("id");
        $this->load->model("opinion_comment", "opinion_commentfactory");
        $this->opinion_comment = $this->opinion_commentfactory->get_instance();
        $this->load->helper("text");
        $this->load->model("email_notification_scheme");
        $data["hide_show_notification"] = $this->email_notification_scheme->get_hide_show_send_email_notification_by_trigger_action("add_opinion_note") == "1" ? "yes" : "";
        $data["id"] = $id;
        $data["comments"] = $this->opinion_comment->load_comments($id, $this->input->get("showAll"));
        if (!empty($data)) {
            $response["html"] = $this->load->view("opinions/view/comments/index", $data, true);
            $response["status"] = true;
        } else {
            $response["status"] = false;
        }
        $this->output->set_content_type("application/json");
        $this->output->set_output(json_encode($response));
    }
    public function add_comment()
    {
        if (!$this->input->is_ajax_request()) {
            show_404();
        }
        $this->load->model("opinion_comment", "opinion_commentfactory");
        $this->opinion_comment = $this->opinion_commentfactory->get_instance();
        $this->load->helper("revert_comment_html");
        $this->load->helper("format_comment_patterns");
        if ($this->input->get(NULL)) {
            $data = [];
            $data["comment"] = $this->opinion_comment->get_fields();
            $data["comment"]["opinion_id"] = $this->input->get("opinion_id");
            $data["title"] = $this->lang->line("add_comments");
            $response["html"] = $this->load->view("opinions/view/comments/form", $data, true);
        }
        if ($this->input->post(NULL)) {
            $_POST["comment"] = format_comment_patterns($this->regenerate_note($this->input->post("comment", true, true))); 
            $_POST["edited"] = 0;
            $this->opinion_comment->set_fields($this->input->post(NULL));
            $this->opinion_comment->set_field("comment", $this->input->post("comment", true, true));
            if ($this->opinion_comment->insert()) {
                $this->opinion->fetch($this->input->post("opinion_id"));
                $data["opinion_data"] = ["detailed_info" => $this->opinion->get_field("detailed_info"), "comment" => revert_comment_html($this->input->post("comment", true, true), false, true, $this->input->post("opinion_id"), $this->opinion_comment->get_field("id"))];
                $this->send_notification($this->input->post("opinion_id"), "add_opinion_note", $data);
                $response["result"] = true;
                $data["comment"] = $this->opinion_comment->load_comment($this->opinion_comment->get_field("id"));
                $response["html"] = $this->load->view("opinions/view/comments/display_form", $data, true);
                $response["data"] = ["modifiedBy" => $this->session->userdata("AUTH_user_id"), "modifier_full_name" => $this->session->userdata("AUTH_userProfileName"), "modifiedOn" => date("Y-m-d H:i:s")];
                $this->opinion->set_field("id", $data["comment"]["opinion_id"]);
                $this->opinion->touch_logs();
            } else {
                $response["validation_errors"] = $this->opinion_comment->get("validationErrors");
            }
        }
        $this->output->set_content_type("application/json")->set_output(json_encode($response));
    }
    public function edit_comment()
    {
        if (!$this->input->is_ajax_request()) {
            show_404();
        }
        $response = [];
        $this->load->model("opinion_comment", "opinion_commentfactory");
        $this->opinion_comment = $this->opinion_commentfactory->get_instance();
        $this->load->helper("revert_comment_html");
        $this->load->helper("format_comment_patterns");
        if ($this->input->get("id") && $this->input->get("opinion_id")) {
            $data = [];
            if ($this->opinion_comment->fetch(["id" => $this->input->get("id"), "opinion_id" => $this->input->get("opinion_id")])) {
                $data["comment"] = $this->opinion_comment->get_fields();
                $data["title"] = $this->lang->line("edit_comment");
                $response["html"] = $this->load->view("opinions/view/comments/form", $data, true);
            }
        }
        if ($this->input->post(NULL)) {
            $_POST["comment"] = format_comment_patterns($this->regenerate_note($this->input->post("comment", true, "<h1><h2><h3><h4><h5><h6><strong><em><u><a><p><img>")), true);
            $this->opinion_comment->fetch(["id" => $this->input->post("id"), "opinion_id" => $this->input->post("opinion_id")]);
            if ($this->input->post("comment", true, "<h1><h2><h3><h4><h5><h6><strong><em><u><a><p><img>") != $this->opinion_comment->get_field("comment")) {
                $_POST["edited"] = 1;
            }
            $this->opinion_comment->set_fields($this->input->post(NULL));
            $this->opinion_comment->set_field("comment", $this->input->post("comment", true, "<h1><h2><h3><h4><h5><h6><strong><em><u><a><p><img>"));
            if ($this->opinion_comment->update()) {
                $response["result"] = true;
                $response["data"] = ["modifiedBy" => $this->session->userdata("AUTH_user_id"), "modifier_full_name" => $this->session->userdata("AUTH_userProfileName"), "modifiedOn" => date("Y-m-d H:i:s")];
                $this->opinion->set_field("id", $this->input->post("opinion_id"));
                $this->opinion->touch_logs();
            } else {
                $response["validation_errors"] = $this->opinion_comment->get("validationErrors");
            }
        }
        $this->output->set_content_type("application/json")->set_output(json_encode($response));
    }
    public function delete_comment()
    {
        $id = $this->input->post("id");
        if (!$this->input->is_ajax_request() || !$this->validate_id($id)) {
            show_404();
        }
        $this->load->model("opinion_comment", "opinion_commentfactory");
        $this->opinion_comment = $this->opinion_commentfactory->get_instance();
        $response["result"] = false;
        if ($this->opinion_comment->fetch($id) && $this->opinion_comment->delete($id)) {
            $response["result"] = true;
            $response["data"] = ["modifiedBy" => $this->session->userdata("AUTH_user_id"), "modifier_full_name" => $this->session->userdata("AUTH_userProfileName"), "modifiedOn" => date("Y-m-d H:i:s")];
            $this->opinion->set_field("id", $this->input->post("module_record_id"));
            $this->opinion->touch_logs();
        }
        $this->output->set_content_type("application/json");
        $this->output->set_output(json_encode($response));
    }
    public function transition_screen_fields($opinion_id = 0, $transition = 0)
    {
        if (!$this->input->is_ajax_request()) {
            show_404();
        }
        $response["result"] = true;
        $this->load->model("opinion_fields", "opinion_fieldsfactory");
        $this->opinion_fields = $this->opinion_fieldsfactory->get_instance();
        $this->load->model("opinion_workflow_status_transition", "opinion_workflow_status_transitionfactory");
        $this->opinion_workflow_status_transition = $this->opinion_workflow_status_transitionfactory->get_instance();
        $this->opinion_workflow_status_transition->fetch($transition);
        $status = $this->opinion_workflow_status_transition->get_field("to_step");
        if (!$this->input->post(NULL)) {
            if ($this->opinion_workflow_status_transition->check_transition_allowed($opinion_id, $status, $this->is_auth->get_user_id())) {
                $data = $this->opinion_fields->return_screen_fields($opinion_id, $transition);
                if ($data) {
                    $data["title"] = $this->opinion_workflow_status_transition->get_field("name");
                    $response["html"] = $this->load->view("templates/screen_fields", $data, true);
                } else {
                    if (!$this->update_status($opinion_id, $status)) {
                        $response["result"] = false;
                        $response["display_message"] = $this->lang->line("workflowActionInvalid");
                    }
                    $response["display_message"] = sprintf($this->lang->line("status_updated_message"), $this->lang->line("opinion"));
                }
            } else {
                $response["result"] = false;
                $response["display_message"] = $this->lang->line("transition_not_allowed");
            }
        } else {
            $validation = $this->opinion_fields->validate_fields($transition);
            $response["result"] = $validation["result"];
            if (!$validation["result"]) {
                $response["validation_errors"] = $validation["errors"];
            } else {
                if ($this->validate_update_status($opinion_id, $status)) {
                    $save_result = $this->opinion_fields->save_fields($opinion_id);
                    if (!$save_result["result"]) {
                        $response["result"] = $save_result["result"];
                        $response["validation_errors"] = $save_result["validation_errors"];
                    } else {
                        $response["display_message"] = sprintf($this->lang->line("status_updated_message"), $this->lang->line("opinion"));
                        $this->update_status($opinion_id, $status);
                    }
                } else {
                    $response["result"] = false;
                    $response["display_message"] = $this->lang->line("workflowActionInvalid");
                }
            }
        }
        $this->output->set_content_type("application/json")->set_output(json_encode($response));
    }
    private function validate_update_status($opinion_id = 0, $status_id = 0)
    {
        $this->opinion->fetch($opinion_id);
        $this->opinion->set_field("opinion_status_id", $status_id);
        return $this->opinion->validate();
    }
    private function update_status($opinion_id = 0, $status_id = 0)
    {
        $this->opinion->fetch($opinion_id);
        $old_status = $this->opinion->get_field("opinion_status_id");
        $this->opinion->set_field("opinion_status_id", $status_id);
        if (!$this->opinion->update()) {
            return false;
        }
        if ($this->opinion->get_field("stage")) {
            $this->load->model("legal_case_litigation_detail", "legal_case_litigation_detailfactory");
            $this->legal_case_litigation_detail = $this->legal_case_litigation_detailfactory->get_instance();
            $this->legal_case_litigation_detail->update_stage_order($this->opinion->get_field("stage"));
        }
        $this->load->model("opinion_workflow_status_transition_history");
        $this->opinion_workflow_status_transition_history->log_transition_history($opinion_id, $old_status, $status_id, $this->is_auth->get_user_id());
        $this->load->model("opinion_status");
        $this->load->model("opinion_user");
        $users = $this->opinion_user->load_all(["where" => ["opinion_id", $opinion_id]]);
        $data["watchers"] = array_column($users, "user_id");
        $this->load->model("opinion_contributor");
        $contributors = $this->opinion_contributor->load_all(["where" => ["opinion_id", $opinion_id]]);
        $data["contributors"] = array_column($contributors, "user_id");
        $this->opinion_status->fetch($old_status);
        $old_status_name = $this->opinion_status->get_field("name");
        $this->opinion_status->reset_fields();
        $this->opinion_status->fetch($status_id);
        $new_status_name = $this->opinion_status->get_field("name");
        $data["opinion_data"] = ["old_status" => $old_status_name, "new_status" => $new_status_name, "on" => $this->opinion->get_field("modifiedOn")];
        $this->send_notification($opinion_id, "edit_opinion_status", $data);
        return true;
    }
    private function send_notification($id, $object, $data)
    { 
        $this->load->model("email_notification_scheme");
        $model = $this->opinion->get("_table"); 
        $model_data["id"] = $id;
        $data["contributors"] = $this->opinion->load_opinion_contributors($id);
        $model_data["contributors_ids"] = $data["contributors"] ? array_column($data["contributors"], "id") : [];
        $this->load->model("opinion_types_language");
        $this->load->model("language");
        $langId = $this->language->get_id_by_session_lang();
        $this->opinion_types_language->fetch(["opinion_type_id" => $this->opinion->get_field("opinion_type_id"), "language_id" => $langId]);
        $opinionType = $this->opinion_types_language->get_field("name");
        if ($this->input->post("send_notifications_email") || $object == "edit_opinion_status") {
            $this->load->library("email_notifications");
            $objectType = $object;
            $notifications_emails = $this->email_notification_scheme->get_emails($objectType, $model, $model_data);
            extract($notifications_emails);
            $notificationsData["to"] = $to_emails;
            $notificationsData["cc"] = $cc_emails;
            $notificationsData["object_id"] = (int) $id;
            $notificationsData["fromLoggedUser"] = $this->is_auth->get_fullname();
            $notificationsData["object"] = $objectType;
            $notificationsData["objectModelCode"] = $this->opinion->get("modelCode");
            $this->opinion->fetch($id);
            $data["opinion_data"]["opinion_id"] = $id;
            $data["opinion_data"]["priority"] = $this->opinion->get_field("priority");
            $data["opinion_data"]["dueDate"] = $this->opinion->get_field("due_date");
            $data["opinion_data"]["opiniondetailed_info"] = nl2br($this->opinion->get_field("detailed_info"));
            $data["opinion_data"]["opinionType"] = $opinionType;
            $data["opinion_data"]["assignee"] = $this->email_notification_scheme->get_user_full_name($this->opinion->get_field("assigned_to"));
            $data["opinion_data"]["created_by"] = $notificationsData["fromLoggedUser"];
            $notificationsData["opinionData"] = $data["opinion_data"];
            $this->email_notifications->notify($notificationsData);
        }
        $assignee_id = str_pad($this->opinion->get_field("assigned_to"), 10, "0", STR_PAD_LEFT);
        $reporter_id = str_pad($this->opinion->get_field("reporter"), 10, "0", STR_PAD_LEFT);
        $toIds = [$assignee_id, $reporter_id];
        if (!empty($model_data["contributors_ids"])) {
            $toIds = array_merge($toIds, $model_data["contributors_ids"]);
        }
        $notificationsData = ["toIds" => array_unique($toIds), "object" => $object, "object_id" => (int) $id, "objectModelCode" => $this->opinion->get("modelCode"), "targetUser" => $assignee_id, "opinionData" => ["opinionID" => (int) $id, "priority" => $this->opinion->get_field("priority"), "dueDate" => $this->opinion->get_field("due_date"), "opiniondetailed_info" => nl2br($this->opinion->get_field("detailed_info")), "modifiedBy" => $this->email_notification_scheme->get_user_full_name($this->opinion->get_field("modifiedBy"))], "attachments" => []];
        $this->load->library("system_notification");
        $this->system_notification->notification_add($notificationsData);
    }
    public function move_status($opinion_id, $status_id)
    {
        $this->load->model("opinion_workflow_status_transition", "opinion_workflow_status_transitionfactory");
        $this->opinion_workflow_status_transition = $this->opinion_workflow_status_transitionfactory->get_instance();
        if ($this->opinion_workflow_status_transition->check_transition_allowed($opinion_id, $status_id, $this->is_auth->get_user_id())) {
            if ($this->update_status($opinion_id, $status_id)) {
                $this->set_flashmessage("success", sprintf($this->lang->line("status_updated_message"), $this->lang->line("opinion")));
            } else {
                $this->set_flashmessage("error", $this->lang->line("move_status_invalid"));
            }
        } else {
            $this->set_flashmessage("error", $this->lang->line("permission_not_allowed"));
        }
        redirect("legal_opinions/view/" . $opinion_id);
    }
    public  function add_legal_opinion_file($opinion_id)
    {
        if (!$this->input->is_ajax_request()) {
        redirect("dashboard");
    }
        $response = [];
        $this->opinion->fetch($opinion_id);

        if ($this->input->post("action") == "return_html") {
            $data = $this->opinion->get_fields();
            $response["html"] = $this->load->view("opinions/view/upload_opinion_file_form", $data, true);
        } else {
            $this->load->library("dmsnew");
            $response = $this->dmsnew->upload_file(["module" => "legal_opinion", "module_record_id" => $opinion_id, "lineage" => $this->input->post("lineage"), "upload_key" => "uploadDoc", "document_type_id" => 0, "document_status_id" => 1, "comment" => $this->input->post("summary")]);
            //$response = $this->dmsnew->upload_file(["module" => "legal_opinion", "module_record_id" => $opinion_id, "lineage" => $this->input->post("lineage"), "upload_key" => "uploadDoc"]);

            $this->opinion->set_field("opinion_file", $this->input->post("opinion_file"));
            $response["result"] = $this->opinion->update();
        }
        $this->output->set_content_type("application/json")->set_output(json_encode($response));

    }
    public function upload_file()
    {
        $this->load->library("dmsnew");
        $response = $this->dmsnew->upload_file(["module" => $this->input->post("module"), "module_record_id" => $this->input->post("module_record_id"), "lineage" => $this->input->post("lineage"), "upload_key" => "uploadDoc"]);

        if ($this->input->post("dragAndDrop")) {
            if (isset($response["file"])) {
                $this->opinion->set_field("id", $response["file"]["module_record_id"]);
                $this->opinion->touch_logs();
            }
            $this->output->set_content_type("application/json");
            $this->output->set_output(json_encode($response));
        } else {
            $html = "<html>\r\n                <head>\r\n                    <meta http-equiv=\"Content-Type\" content=\"text/html; charset=utf-8\" />\r\n                    <script type=\"text/javascript\">\r\n                        if(window.top.uploadDocumentDone) window.top.uploadDocumentDone('" . $response["message"] . "', '" . ($response["status"] ? "success" : "error") . "');\r\n                    </script>\r\n                </head>\r\n            </html>";
            $this->output->set_content_type("text/html")->set_output($html);
        }
    }
    public function matter_upload_file()
    {
        $this->load->library("dmsnew");
        $opinion_id = $this->input->post("opinion_record_id");
        $_FILES["uploadDoc"]["name"] = "O" . $opinion_id . "_" . $_FILES["uploadDoc"]["name"];
        $response = $this->dmsnew->upload_file(["module" => $this->input->post("module"), "module_record_id" => $this->input->post("module_record_id"), "opinion_record_id" => $this->input->post("opinion_record_id"), "container_name" => "Opinion Attachments", "upload_key" => "uploadDoc"]);
        if (isset($response["file"])) {
            $this->load->model("opinion_document");
            $this->opinion_document->set_field("opinion_id", !empty($opinion_id) ? $opinion_id : $this->opinion->get_field("id"));
            $this->opinion_document->set_field("document_id", $response["file"]["id"]);
            $this->opinion_document->insert();
        }
        if ($this->input->post("dragAndDrop")) {
            if (isset($response["file"])) {
                $this->opinion->set_field("id", $response["file"]["module_record_id"]);
                $this->opinion->touch_logs();
            }
            $this->output->set_content_type("application/json");
            $this->output->set_output(json_encode($response));
        } else {
            $html = "<html>\r\n                <head>\r\n                    <meta http-equiv=\"Content-Type\" content=\"text/html; charset=utf-8\" />\r\n                    <script type=\"text/javascript\">\r\n                        if(window.top.uploadDocumentDone) window.top.uploadDocumentDone('" . $response["message"] . "', '" . ($response["status"] ? "success" : "error") . "');\r\n                    </script>\r\n                </head>\r\n            </html>";
            $this->output->set_content_type("text/html")->set_output($html);
        }
    }
    public function matter_load_documents()
    {
        $this->load->library("dmsnew");
        $response = [];
        $opinion_id = $this->input->get("opinion_record_id");
        $this->load->model("opinion_document");
        $attachments = $this->opinion_document->get_document_by_opinion_id($opinion_id);
        foreach ($attachments as $attachment) {
            $file_id = $attachment["document_id"];
            $document_data = $this->dmsnew->load_document_by_id($file_id);
            array_push($response, $document_data);
        }
        if ($this->input->is_ajax_request()) {
            $this->output->set_content_type("application/json");
            $this->output->set_output(json_encode($response));
        } else {
            return $response;
        }
    }
    public function matter_download_file($file_id)
    {
        if (!$this->is_auth->is_logged_in()) {
            redirect("users/login");
        }
        $this->load->library("dmsnew");
        $this->dmsnew->download_file("case", $file_id);
    }
    public function matter_delete_document()
    {
        $document_id = $this->input->post("document_id");
        $this->load->library("dmsnew");
        $this->opinion->set_field("id", $this->input->post("module_record_id"));
        $this->opinion->touch_logs();
        $response = $this->dmsnew->delete_document("case", $document_id);
        $response["data"] = ["modifiedOn" => date("Y-m-d H:i:s"), "modifiedBy" => $this->session->userdata("AUTH_user_id"), "modifier_full_name" => $this->session->userdata("AUTH_userProfileName")];
        $this->output->set_content_type("application/json")->set_output(json_encode($response));
    }
    public function return_matter_doc_thumbnail($id = 0, $name = 0)
    {
        if (!$this->is_auth->is_logged_in()) {
            redirect("users/login");
        }
        if ($id) {
            $this->load->library("dmsnew");
            $response = $this->dmsnew->get_file_download_data("case", $id);
            $content = $response["data"]["file_content"];
            if ($content) {
                $this->load->helper("download");
                force_download($name ? $name : $id, $content);
            }
        }
    }
    public function load_documents()
    {
        $this->load->library("dmsnew");
        $response = $this->dmsnew->load_documents(["module" => $this->input->get("module"), "module_record_id" => $this->input->get("module_record_id"), "lineage" => $this->input->get("lineage")]);
        if ($this->input->is_ajax_request()) {
            $this->output->set_content_type("application/json");
            $this->output->set_output(json_encode($response));
        } else {
            return $response;
        }
    }
    public function delete_document()
    {
        $document_id = $this->input->post("document_id");
        $this->load->library("dmsnew");
        $this->opinion->set_field("id", $this->input->post("module_record_id"));
        $this->opinion->touch_logs();
        $response = $this->dmsnew->delete_document("opinion", $document_id);
        $response["data"] = ["modifiedOn" => date("Y-m-d H:i:s"), "modifiedBy" => $this->session->userdata("AUTH_user_id"), "modifier_full_name" => $this->session->userdata("AUTH_userProfileName")];
        $this->output->set_content_type("application/json")->set_output(json_encode($response));
    }
    public function download_file($file_id, $newest_version = false)
    {
        if (!$this->is_auth->is_logged_in()) {
            redirect("users/login");
        }
        $this->load->library("dmsnew");
        $this->dmsnew->download_file("opinion", $file_id, $newest_version);
    }
    private function insert_related_users($opinion_id, $action)
    {
        $this->load->model("notification", "notificationfactory");
        $this->notification = $this->notificationfactory->get_instance();
        $this->load->model("opinion_user");
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
        if (!in_array($this->is_auth->get_user_id(), $watchers)) {
            $watchers[] = $this->is_auth->get_user_id();
        }
        $watchers_data["users"] = ["opinion_id" => $opinion_id, "users" => $watchers];
        $this->opinion->insert_opinion_users($watchers_data);
        $users_to_notify = ["contributors" => $contributors];
        $this->send_notifications($action, $users_to_notify);
        $total_notifications["total_notifications"] = $this->notification->update_pending_notifications($watchers);
        return $total_notifications;
    }
    public function return_doc_thumbnail($id = 0, $name = 0)
    {
        if (!$this->is_auth->is_logged_in()) {
            redirect("users/login");
        }
        if ($id) {
            $this->load->library("dmsnew");
            $response = $this->dmsnew->get_file_download_data("opinion", $id);
            $content = $response["data"]["file_content"];
            if ($content) {
                $this->load->helper("download");
                force_download($name ? $name : $id, $content);
            }
        }
    }
    private function return_assignments_rules($type)
    {
        $response = false;
        $this->load->model("assignment", "assignmentfactory");
        $this->assignment = $this->assignmentfactory->get_instance();
        if ($type && $this->assignment->fetch(["category" => "opinion", "type" => $type])) {
            $response = $this->assignment->get_fields();
        }
        if ($response) {
            $response["assignment_relation"] = "";
            switch ($response["assignment_rule"]) {
                case "rr_algorithm":
                    $next_assignee = $this->assignment->load_next_opinion_assignee($response["id"]);
                    $user_id = $next_assignee["user_id"] ?: "";
                    break;
                default:
                    $user_id = $response["assignment_rule"];
            }
                    $response["user"] = $this->user->get_name_by_id($user_id);

        }
        return $response;
    }
    private function return_litigation_stage_html($case_id, $stage_id = 0, $object_id = 0)
    {
        $this->load->model("legal_case_litigation_detail", "legal_case_litigation_detailfactory");
        $this->legal_case_litigation_detail = $this->legal_case_litigation_detailfactory->get_instance();
        $data["stage"] = $object_id && !$stage_id ? [] : $this->legal_case_litigation_detail->load_stage_metadata($case_id, $stage_id);
        $data["case_history_stages"] = $this->legal_case_litigation_detail->load_all(["where" => ["legal_case_id", $case_id]]);
        $systemPreferences = $this->session->userdata("systemPreferences");
        $data["hijri_calendar_enabled"] = isset($systemPreferences["hijriCalendarFeature"]) && $systemPreferences["hijriCalendarFeature"] ? $systemPreferences["hijriCalendarFeature"] : 0;
        return $this->load->view("cases/litigation/selected_stage_metadata", $data, true);
    }
    public function export_opinion_to_word($id = 0)
    {
        if (!$this->validate_id($id)) {
            $this->set_flashmessage("error", $this->lang->line("invalid_record"));
            redirect("opinions/my_opinions");
        }
        $data = [];
        $opinion_data = $this->opinion->load_opinion($id);
        if (!$opinion_data) {
            $this->set_flashmessage("error", $this->lang->line("permission_not_allowed"));
            redirect("opinions/my_opinions");
        }
        $this->load->model("legal_case", "legal_casefactory");
        $this->legal_case = $this->legal_casefactory->get_instance();
        $this->load->model("contract", "contractfactory");
        $this->contract = $this->contractfactory->get_instance();
        $this->load->helper("revert_comment_html");
        $data["opinion"] = $opinion_data; 
        $data["opinion"]["title"] = $data["opinion"]["title"];
        $data["opinion"]["detailed_info"] = strip_tags(str_replace(["<br>", "<br class=\"Apple-interchange-newline\">"], "\\n", str_replace("&nbsp;", "", $data["opinion"]["detailed_info"])));
        $data["opinion"]["estimated_effort"] = (double) $data["opinion"]["estimated_effort"];
        $data["opinion"]["effectiveEffort"] = (double) $data["opinion"]["effectiveEffort"];
        $data["opinion"]["model_code"] = $this->opinion->get("modelCode");
        $data["opinion"]["priority"] = $this->lang->line($data["opinion"]["priority"]);
        $lang_at = $this->lang->line("at");
        $data["opinion"]["createdOn"] = str_replace(" ", " " . $lang_at . " ", $data["opinion"]["createdOn"]);
        $data["opinion"]["modifiedOn"] = str_replace(" ", " " . $lang_at . " ", $data["opinion"]["modifiedOn"]);
        strlen($data["opinion"]["legal_case_id"]);
        0 < strlen($data["opinion"]["legal_case_id"]) ? $data["opinion"]["case_model_code"] : $data["opinion"]["case_model_code"];
        strlen($data["opinion"]["contract_id"]);
        0 < strlen($data["opinion"]["contract_id"]) ? $data["opinion"]["contract_model_code"] : $data["opinion"]["contract_model_code"];
        $this->load->model("custom_field", "custom_fieldfactory");
        $this->custom_field = $this->custom_fieldfactory->get_instance();
        $custom_fields = $this->custom_field->load_custom_fields($id, $this->opinion->get("modelName"));
        $section_types = $this->custom_field->section_types;
        if (!empty($custom_fields)) {
            foreach ($custom_fields as $field) {
                if ($field["type"] === "lookup") {
                    $field["value"] = implode(", ", $this->custom_field->get_lookup_data($field));
                }
                $custom_fields[$section_types[$field["type"]]][] = $field;
            }
        }
        $data["tbl|custom_fields_main"] = $custom_fields["main"] ?? [];
        $data["tbl|custom_fields_date"] = $custom_fields["date"] ?? [];
        $data["tbl|custom_fields_people"] = $custom_fields["people"] ?? [];
        foreach ($data["tbl|custom_fields_date"] as $index => $date) {
            if ($date["type"] == "date_time") {
                $data["tbl|custom_fields_date"][$index]["at"] = $this->lang->line("at");
            } else {
                $data["tbl|custom_fields_date"][$index]["at"] = "";
            }
        }
        $data["tbl|shared_with"] = [];
        if ($opinion_data["private"] === "yes") {
            $data["tbl|shared_with"] = $this->opinion->load_opinion_users($id);
        }
        $data["tbl|contributors"] = $this->opinion->load_opinion_contributors($id);
        $this->load->model("opinion_comment", "opinion_commentfactory");
        $this->opinion_comment = $this->opinion_commentfactory->get_instance();
        $comments = $this->opinion_comment->load_comments($id, true);
        $data["tbl|comments"] = [];
        if (!empty($comments["records"])) {
            foreach ($comments["records"] as $index => $comment) {
                $comments["records"][$index]["comment"] = strip_tags(str_replace(["<br>", "<br class=\"Apple-interchange-newline\">"], "\\n", str_replace("&nbsp;", "", $comment["comment"])));
            }
            $data["tbl|comments"] = $comments["records"];
        }
        switch ($this->license_package) {
            case "core":
                $data["tbl|related_objects"]["matter"] = ["label" => $this->lang->line("related_case"), "value" => $data["opinion"]["case_model_code"] . $data["opinion"]["legal_case_id"] . "-" . $data["opinion"]["caseSubject"]];
                break;
            case "contract":
                $data["tbl|related_objects"]["contract"] = ["label" => $this->lang->line("related_contract"), "value" => $data["opinion"]["contract_model_code"] . $data["opinion"]["contract_id"] . "-" . $data["opinion"]["contract_name"]];
                break;
            case "core_contract":
                $data["tbl|related_objects"]["matter"] = ["label" => $this->lang->line("related_case"), "value" => $data["opinion"]["case_model_code"] . $data["opinion"]["legal_case_id"] . "-" . $data["opinion"]["caseSubject"]];
                $data["tbl|related_objects"]["contract"] = ["label" => $this->lang->line("related_contract"), "value" => $data["opinion"]["contract_model_code"] . $data["opinion"]["contract_id"] . "-" . $data["opinion"]["contract_name"]];
                break;
            default:
                $data["tbl|related_objects"] = [];
        }
        $this->load->library("word_template_manipulator");
        $docx = $this->word_template_manipulator->get_template_docx_object("opinion_details");
        $this->word_template_manipulator->set_template_data($docx, $data);
        $corepath = substr(COREPATH, 0, -12);
        $temp_directory = $this->config->item("files_path") . DIRECTORY_SEPARATOR . "tmp";
        if (!is_dir($temp_directory)) {
            @mkdir($temp_directory, 493);
        }
        $file_name = $this->lang->line("opinion") . "_" . $id . "_" . date("YmdHi");
        $docx->createDocx($temp_directory . "/" . $file_name);
        $this->load->helper("download");
        $content = file_get_contents($temp_directory . "/" . $file_name . ".docx");
        unlink($temp_directory . "/" . $file_name . ".docx");
        $file_name_encoded = $this->downloaded_file_name_by_browser($file_name . ".docx");
        force_download($file_name_encoded, $content);
        exit;

}
    public function export_opinion_to_word_for_clients($id = 0)
    {
        if (!$this->validate_id($id)) {
            $this->set_flashmessage("error", $this->lang->line("invalid_record"));
            redirect("opinions/my_opinions");
        }
        $data = [];
        $opinion_data = $this->opinion->load_opinion($id);
        if (!$opinion_data) {
            $this->set_flashmessage("error", $this->lang->line("permission_not_allowed"));
            redirect("opinions/my_opinions");
        }
        $this->load->model("legal_case", "legal_casefactory");
        $this->legal_case = $this->legal_casefactory->get_instance();
        $this->load->helper("revert_comment_html");
        $data["opinion"] = $opinion_data;
        $data["opinion"]["title"] = $data["opinion"]["title"];
        $data["opinion"]["detailed_info"] = strip_tags(str_replace(["<br>", "<br class=\"Apple-interchange-newline\">"], "\\n", str_replace("&nbsp;", "", $data["opinion"]["detailed_info"])));
        $data["opinion"]["model_code"] = $this->opinion->get("modelCode");
        strlen($data["opinion"]["legal_case_id"]);
        0 < strlen($data["opinion"]["legal_case_id"]) ? $data["opinion"]["case_model_code"] : $data["opinion"]["case_model_code"];
        $this->load->model("opinion_comment", "opinion_commentfactory");
        $this->opinion_comment = $this->opinion_commentfactory->get_instance();
        $comments = $this->opinion_comment->load_comments($id, true);
        $data["tbl|comments"] = [];
        if (!empty($comments["records"])) {
            foreach ($comments["records"] as $index => $comment) {
                $comments["records"][$index]["comment"] = strip_tags(str_replace(["<br>", "<br class=\"Apple-interchange-newline\">"], "\\n", str_replace("&nbsp;", "", $comment["comment"])));
            }
            $data["tbl|comments"] = $comments["records"];
        }
        switch ($this->license_package) {
            case "core":
                $data["tbl|related_objects"]["matter"] = ["label" => $this->lang->line("related_case"), "value" => $data["opinion"]["case_model_code"] . $data["opinion"]["legal_case_id"] . " - " . $data["opinion"]["caseSubject"]];
                break;
            case "contract":
                $data["tbl|related_objects"]["contract"] = ["label" => $this->lang->line("related_contract"), "value" => $data["opinion"]["contract_model_code"] . $data["opinion"]["contract_id"] . " - " . $data["opinion"]["contract_name"]];
                break;
            case "core_contract":
                $data["tbl|related_objects"]["matter"] = ["label" => $this->lang->line("related_case"), "value" => $data["opinion"]["case_model_code"] . $data["opinion"]["legal_case_id"] . " - " . $data["opinion"]["caseSubject"]];
                $data["tbl|related_objects"]["contract"] = ["label" => $this->lang->line("related_contract"), "value" => $data["opinion"]["contract_model_code"] . $data["opinion"]["contract_id"] . " - " . $data["opinion"]["contract_name"]];
                break;
            default:
                $data["tbl|related_objects"] = [];
        }
        $this->load->library("word_template_manipulator");
        $docx = $this->word_template_manipulator->get_template_docx_object("opinion_client_details");
        $this->word_template_manipulator->set_template_data($docx, $data);
        $corepath = substr(COREPATH, 0, -12);
        $temp_directory = $this->config->item("files_path") . DIRECTORY_SEPARATOR . "tmp";
        if (!is_dir($temp_directory)) {
            @mkdir($temp_directory, 493);
        }
        $file_name = $this->lang->line("client_opinion_report") . "_" . $id . "_" . date("YmdHi");
        $docx->createDocx($temp_directory . "/" . $file_name);
        $this->load->helper("download");
        $content = file_get_contents($temp_directory . "/" . $file_name . ".docx");
        unlink($temp_directory . "/" . $file_name . ".docx");
        $file_name_encoded = $this->downloaded_file_name_by_browser($file_name . ".docx");
        force_download($file_name_encoded, $content);
        exit;
    }
    public function check_case_privacy()
    {
        if (!$this->input->is_ajax_request()) {
            show_404();
        } else {
            if ($this->input->get("case_id")) {
                $case_id = $this->input->get("case_id");
                $this->load->model("system_preference");
                $system_preference = $this->system_preference->get_key_groups();
                $system_preference["DefaultValues"]["opinionPrivacyBasedOnMatterPrivacy"]=0; //hardcoded temprary by ating to remove error of undefined
                $allow_matter_opinion_privacy = $system_preference["DefaultValues"]["opinionPrivacyBasedOnMatterPrivacy"];
                if ($allow_matter_opinion_privacy == 1) {
                    $this->load->model("legal_case", "legal_casefactory");
                    $this->legal_case = $this->legal_casefactory->get_instance();
                    if ($this->legal_case->fetch($case_id)) {
                        $private_matter = $this->legal_case->get_field("private");
                        if ($private_matter === "yes") {
                            $legal_case_users = $this->legal_case->load_opinion_legal_case_users($case_id);
                            $response["users"] = $legal_case_users;
                            $response["msg"] = sprintf($this->lang->line("opinion_related_to_private_case"), "M" . $case_id);
                            $response["result"] = true;
                        } else {
                            $response["result"] = false;
                        }
                    } else {
                        $response["result"] = false;
                    }
                } else {
                    $response["result"] = false;
                }
            } else {
                $response["result"] = false;
            }
            $this->output->set_content_type("application/json")->set_output(json_encode($response));
        }
    }
    public function opinions_contributed_by_me()
    {
        $this->pageTitle = sprintf($this->lang->line("sheria360_browser_title"), $this->lang->line("contributed_by_me_opinions") . " | " . $this->lang->line("opinion_in_menu"));
        $this->index($this->session->userdata("AUTH_user_id"), false, true);
    }
    public function pro_contributed_by_me()
    {
        $this->pageTitle = sprintf($this->lang->line("sheria360_browser_title"), $this->lang->line("contributed_by_me_opinions") . " | " . $this->lang->line("opinion_in_menu"));
        $this->index("conveyancing",$this->session->userdata("AUTH_user_id"), false, true);
    }
    public function view_document($id = 0)
    {
        $response = [];
        $this->load->library("dmsnew");
        if (0 < $id) {
            echo $this->dmsnew->get_document_content($id);
            exit;
        }
        $id = $this->input->post("id");
        if (!empty($id)) {
            $response["document"] = $this->dmsnew->get_document_details(["id" => $id]);
            $response["document"]["url"] = BASEURL . "opinions/view_document/" . $id;
            if (!empty($response["document"]["extension"]) && in_array($response["document"]["extension"], $this->document_management_system->image_types)) {
                $response["iframe_content"] = $this->load->view("documents_management_system/view_image_document", ["url" => $response["document"]["url"]], true);
            }
        }
        $response["html"] = $this->load->view("documents_management_system/view_document", [], true);
        $this->output->set_content_type("application/json")->set_output(json_encode($response));
    }
    public function preview_document($id = 0)
    {
        $response = [];
        $this->load->library("dmsnew");
        if (0 < $id) {
            echo $this->dmsnew->get_preview_document_content($id);
            exit;
        }
        $id = $this->input->post("id");
        if (!empty($id)) {
            $response["document"] = $this->dmsnew->get_document_details(["id" => $id]);
            $response["document"]["url"] = app_url("opinions/preview_document/" . $id);
        }
        $response["html"] = $this->load->view("documents_management_system/view_document", ["mode" => "preview"], true);
        $this->output->set_content_type("application/json")->set_output(json_encode($response));
    }
}

?>