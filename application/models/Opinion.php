<?php

if (!defined("BASEPATH")) {
    exit("No direct script access allowed");
}
class Opinion extends My_Model_Factory
{
}
class mysql_Opinion extends My_Model
{
    protected $modelName = "Opinion";
    protected $modelCode = "O";
    protected $_table = "opinions";
    protected $_listFieldName = "legal_case_id";
    protected $_fieldsNames = ["id", "title", "user_id", "legal_case_id", "reporter", "opinion_type_id", "assigned_to", "due_date", "private", "detailed_info" , "background_info","requester","legal_question",  "opinion_status_id", "createdBy", "createdOn", "modifiedBy", "modifiedOn", "archived", "hideFromBoard", "estimated_effort", "priority", "opinion_location_id", "workflow", "stage", "contract_id","opinion_file","channel","is_visible_to_cp"];
    protected $allowedNulls = ["legal_case_id", "private", "opinion_location_id", "estimated_effort", "stage", "contract_id"];
    protected $archivedValues = ["", "yes", "no"];
    protected $priorityValues = ["critical", "high", "medium", "low"];
    protected $priorityValuesSla = ["all", "critical", "high", "medium", "low"];
    protected $validate = [];
    protected $builtInLogs = true;
    protected $detailed_infoSubstringStartingPosition = 1;
    protected $detailed_infoSubstringLength = 50;
    protected $lookupInputsToValidate = [["input_name" => "assignedToLookUp", "error_field" => "assigned_to", "message" => ["main_var" => "not_exists", "lookup_for" => "user"]], ["input_name" => "reporterLookUp", "error_field" => "reporter", "message" => ["main_var" => "not_exists", "lookup_for" => "user"]], ["input_name" => "caseLookup", "error_field" => "legal_case_id", "message" => ["main_var" => "not_exists2", "lookup_for" => "case"]], ["input_name" => "location", "error_field" => "opinion_location_id", "message" => ["main_var" => "not_exists", "lookup_for" => "location"]], ["input_name" => "contributors_lookup", "error_field" => "contributors[]", "message" => ["main_var" => "not_exists", "lookup_for" => "user"]]];

    protected $statusValues = ["Active", "Inactive"];
    protected $categoryValues = ["opinion", "conveyancing"];

    public function __construct()
    {
        parent::__construct();
        $this->validate = [
            "title" => ["isRequired" => ["required" => true, "allowEmpty" => false, "rule" => ["minLength", 1], "message" => $this->ci->lang->line("cannot_be_blank_rule")], "maxLength" => ["rule" => ["maxLength", 255], "message" => sprintf($this->ci->lang->line("max_characters"), 255)]],
            "detailed_info" => ["isRequired" => ["required" => true, "allowEmpty" => false, "rule" => ["minLength", 30], "message" => $this->ci->lang->line("cannot_be_blank_min_30_rule")], "maxLength" => ["rule" => ["maxLength", 1000], "message" => sprintf($this->ci->lang->line("max_characters"), 1000)]],
			"legal_question" => ["isRequired" => ["required" => true, "allowEmpty" => false, "rule" => ["minLength", 30], "message" => $this->ci->lang->line("cannot_be_blank_min_30_rule")], "maxLength" => ["rule" => ["maxLength", 1000], "message" => sprintf($this->ci->lang->line("max_characters"), 1000)]],
			"background_info" => ["isRequired" => ["required" => true, "allowEmpty" => false, "rule" => ["minLength", 30], "message" => $this->ci->lang->line("cannot_be_blank_min_30_rule")], "maxLength" => ["rule" => ["maxLength", 1000], "message" => sprintf($this->ci->lang->line("max_characters"), 1000)]],
            "user_id" => ["required" => true, "allowEmpty" => false, "rule" => "numeric", "message" => $this->ci->lang->line("cannot_be_blank_rule")], 
            "legal_case_id" => ["required" => false, "allowEmpty" => true, "rule" => "numeric", "message" => sprintf($this->ci->lang->line("is_numeric_rule"), $this->ci->lang->line("case"))],
            "assigned_to" => ["required" => true, "allowEmpty" => false, "rule" => "numeric", "message" => $this->ci->lang->line("cannot_be_blank_rule")], 
            "due_date" => ["isRequired" => ["required" => true, "allowEmpty" => false, "rule" => ["minLength", 1], "message" => $this->ci->lang->line("cannot_be_blank_rule")],
            "date" => ["required" => false, "allowEmpty" => true, "rule" => $this->custom_validation_rules["date"], "message" => sprintf($this->ci->lang->line("required_date_rule"), $this->ci->lang->line("due_date"))]],
            "opinion_status_id" => ["required" => false, "allowEmpty" => false, "rule" => "numeric", "message" => sprintf($this->ci->lang->line("is_numeric_rule"), $this->ci->lang->line("opinion_status"))],
            "reporter" => ["required" => true, "allowEmpty" => false, "rule" => "numeric", "message" => $this->ci->lang->line("cannot_be_blank_rule")],
            "opinion_type_id" => ["required" => true, "allowEmpty" => false, "rule" => "numeric", "message" => $this->ci->lang->line("cannot_be_blank_rule")], 
            "archived" => ["required" => true, "allowEmpty" => false, "rule" => ["inList", $this->archivedValues], "message" => sprintf($this->ci->lang->line("required_list_values"), implode(", ", $this->archivedValues))], 
            "hideFromBoard" => ["required" => false, "allowEmpty" => true, "rule" => ["maxLength", 3], "message" => sprintf($this->ci->lang->line("max_characters"), 3)], 
            "estimated_effort" => ["maxLength" => ["required" => false, "allowEmpty" => true, "rule" => ["maxLengthDecimal", 6, 2], "message" => sprintf($this->ci->lang->line("max_characters"), 6)], "numeric" => ["required" => false, "allowEmpty" => true, "rule" => $this->custom_validation_rules["decimal"], "message" => $this->ci->lang->line("decimal_allowed")], "timeFormat" => ["rule" => "time_format_validation", "message" => sprintf($this->ci->lang->line("form_validation_time_entry_invalid_format"), $this->ci->lang->line("estimatedEffort"))]], 
            "priority" => ["required" => true, "allowEmpty" => false, "rule" => ["inList", $this->priorityValues], "message" => sprintf($this->ci->lang->line("required_list_values"), implode(", ", $this->priorityValues))], 
            "opinion_location_id" => ["required" => false, "allowEmpty" => true, "rule" => ["maxLength", 255], "message" => sprintf($this->ci->lang->line("is_numeric_rule"), $this->ci->lang->line("location"))], 
            "contract_id" => ["required" => false, "allowEmpty" => true, "rule" => "numeric", "message" => sprintf($this->ci->lang->line("is_numeric_rule"), $this->ci->lang->line("contract"))]];
        $this->logged_user_id = $this->ci->is_auth->get_user_id();
        $this->override_privacy = $this->ci->is_auth->get_override_privacy();
    }
    public function load_opinion($id)
    {
        $query = [];
        $this->ci->load->model("legal_case", "legal_casefactory");
        $this->ci->legal_case = $this->ci->legal_casefactory->get_instance();
        $where_condition = $this->ci->legal_case->get_matter_privacy_conditions($this->logged_user_id, $this->override_privacy, false);
        $query["select"] = ["opinions.*,opinions.createdBy as createdById, tee.effectiveEffort, opinions.id as OpinionId, opinion.assigned_to as assignedToId, opinion.reporter as reporterById, types.name as type,opinion_statuses.name as status,CONCAT(req.firstName,' ',req.lastName) as requestedBy,CONCAT(assigned.firstName,' ',assigned.lastName) as assignee_fullname, opinions.due_date, opinions.detailed_info, CONCAT( created.firstName, ' ', created.lastName ) as createdBy, CONCAT( modified.firstName, ' ', modified.lastName ) as modifiedByName, legal_cases.subject as caseSubject, legal_cases.category as caseCategory, opinion_locations.name AS location, CONCAT(reporter.firstName,' ',reporter.lastName) as reporter_fullname, assigned.status as assignee_status, reporter.status as reporter_status, contract.name as contract_name ,legal_cases.id as opinion_legal_case, legal_cases.client_id as clientId", false];
        $query["where"][] = ["opinions.id", $id];
        $query["where"][] = ["(opinions.private IS NULL OR opinions.private = 'no' OR(opinions.private = 'yes' AND (" . "opinions.createdBy = '" . $this->logged_user_id . "' OR opinions.assigned_to = '" . $this->logged_user_id . "' OR " . "opinions.id IN (SELECT opinion_id FROM opinion_users WHERE user_id = '" . $this->logged_user_id . "') OR '" . $this->override_privacy . "' = 'yes'" . ")" . ") OR" . "(" . "opinions.legal_case_id IS NOT NULL AND " . "((opinions.private IS NULL OR opinions.private = 'no') AND " . $where_condition . ")" . ")" . ")"];
        $this->ci->load->model("language");
        $lang_id = $this->ci->language->get_id_by_session_lang();
        $query["join"] = [["legal_cases", "legal_cases.id = opinions.legal_case_id", "left"], ["contract", "contract.id = opinions.contract_id", "left"], ["user_profiles assigned", "assigned.user_id = opinions.assigned_to", "left"],["user_profiles req", "req.user_id = opinions.requester", "left"], ["user_profiles reporter", "reporter.user_id = opinions.reporter", "left"], ["user_profiles created", "created.user_id = opinions.createdBy", "left"], ["user_profiles modified", "modified.user_id = opinions.modifiedBy", "left"], ["opinion_effective_effort AS tee", "tee.opinion_id = opinions.id", "left"], ["opinion_locations", "opinion_locations.id = opinions.opinion_location_id", "left"], ["opinion_statuses", "opinion_statuses.id = opinions.opinion_status_id", "left"], ["opinion_types_languages as types", "types.opinion_type_id = opinions.opinions_type_id and types.language_id = '" . $lang_id . "'", "left"]];
        return $this->ci->opinion->load($query);
    }
    public function k_load_all_opinions($filter, $sortable, $page_number = "", $with_user_preferences = false, $hijri_calendar_enabled = false)
    {
        $query = [];
        $_table = $this->_table;
        $this->_table = "opinions_detailed_view as opinions";
        $response = [];
        $caseIdFilter = $this->ci->input->post("caseIdFilter", true);
        $opinionStatuses = $this->ci->input->post("OpinionStatusesFilter", true);
        $select = "opinions.*,case when opinions.assignee_status='inactive' then CONCAT( opinions.assigned_to, ' ',  ' (','" . $this->ci->lang->line("inactive") . "' ,')') else opinions.assigned_to END as assigned_to, case when opinions.reporter_status='inactive' then CONCAT( opinions.reporter, ' ',  ' (','" . $this->ci->lang->line("inactive") . "' ,')') else opinions.reporter END as reporter,case when opinions.creator_status='inactive' then CONCAT( opinions.createdBy, ' ',  ' (','" . $this->ci->lang->line("inactive") . "' ,')') else opinions.createdBy END as createdBy, case when opinions.modifier_status='inactive' then CONCAT( opinions.modifiedByName, ' ',  ' (','" . $this->ci->lang->line("inactive") . "' ,')') else opinions.modifiedByName END as modifiedByName, opinion_types_languages.name as opinionType";
        if (empty($this->ci->custom_field)) {
            $this->ci->load->model("custom_field", "custom_fieldfactory");
            $this->ci->custom_field = $this->ci->custom_fieldfactory->get_instance();
        }
        if (is_array($filter)) {
            if (isset($filter["filters"])) {
                foreach ($filter["filters"] as $_filter) {
                    if (!empty($_filter["filters"][0]["field"]) && in_array($_filter["filters"][0]["field"], ["opinions.effectiveEffort", "opinions.estimated_effort"])) {
                        $system_preferences = $this->ci->session->userdata("systemPreferences");
                        $this->ci->load->library("TimeMask");
                        $_filter["filters"][0]["value"] = $this->ci->timemask->humanReadableToHours($_filter["filters"][0]["value"]);
                    }
                    $this->prep_k_filter($_filter, $query, $filter["logic"], $hijri_calendar_enabled);
                }
                unset($_filter);
            }
            if (isset($filter["customFields"]) && !empty($filter["customFields"])) {
                $this->ci->custom_field->prep_custom_field_filters($this->modelName, $filter["customFields"], $query);
            }
        }
        if ($caseIdFilter) {
            $query["where"][] = ["legal_case_id", $this->ci->input->post("caseIdFilter", true)];
        }
        if ($opinionStatuses) {
            $query["where_in"][] = ["opinion_status_id", $opinionStatuses];
            $this->ci->load->model("user_preference");
            $saveOpinionStatuses = $this->ci->user_preference->set_value("opinionStatusesFilter", $opinionStatuses, true);
        } else {
            if (!isset($caseIdFilter) && !$with_user_preferences) {
                $this->ci->load->model("user_preference");
                $opinionStatusesSavedFilters = $this->ci->user_preference->get_value("opinionStatusesFilter");
                if ($opinionStatusesSavedFilters) {
                    $query["where_in"][] = ["opinion_status_id", $opinionStatusesSavedFilters];
                }
            }
        }
        $this->ci->load->model("legal_case", "legal_casefactory");
        $this->ci->legal_case = $this->ci->legal_casefactory->get_instance();
        $where_condition = $this->ci->legal_case->get_matter_privacy_conditions($this->logged_user_id, $this->override_privacy, false);
        $query["where"][] = ["(opinions.private IS NULL OR opinions.private = 'no' OR(opinions.private = 'yes' AND (" . "opinions.createdBy = '" . $this->logged_user_id . "' OR opinions.assigned_to = '" . $this->logged_user_id . "' OR " . "opinions.id IN (SELECT opinion_id FROM opinion_users WHERE user_id = '" . $this->logged_user_id . "') OR '" . $this->override_privacy . "' = 'yes'" . ")" . ") OR" . "(" . "opinions.legal_case_id IS NOT NULL AND " . "((opinions.private IS NULL OR opinions.private = 'no') AND " . $where_condition . ")" . ")" . ")"];
        $this->ci->load->model("language");
        $langId = $this->ci->language->get_id_by_session_lang();
        $query["join"] = [["opinion_types_languages", "opinion_types_languages.opinion_type_id = opinions.opinion_type_id and opinion_types_languages.language_id = '" . $langId . "'", "left"], ["legal_cases", "legal_cases.id = opinions.legal_case_id", "left"], ["opinion_contributors", "opinion_contributors.opinion_id = opinions.id", "left"]];
        $this->prep_query($query);
        $this->ci->db->count_all_results($this->_table);
        $query["group_by"] = ["opinions.id"];
        if (is_array($sortable) && !empty($sortable)) {
            foreach ($sortable as $_sort) {
                $query["order_by"][] = [$_sort["field"], $_sort["dir"]];
            }
        } else {
            $query["order_by"] = ["opinions.id desc"];
        }
        if ($page_number != "") {
            $query["limit"] = [10000, ($page_number - 1) * 10000];
        }
        if ($limit = $this->ci->input->post("take", true)) {
            $query["limit"] = [$this->ci->input->post("take", true), $this->ci->input->post("skip", true)];
        }
        $custom_fields_select = $this->ci->custom_field->load_grid_custom_fields($this->modelName, $_table);
        $select .= $custom_fields_select;
        $query["select"] = [$select, false];
        $response["data"] = $this->load_all($query);
        $response["totalRows"] = count($response["data"]);
        $this->_table = $_table;
        return $response;
    }
    public function touch_logs($action = "update")
    {
        $this->log_built_in_last_action($this->_fields["id"]);
        $this->log_action($action, $this->_fields["id"]);
    }
    public function archieved_opinions_total_number($opinion_status_ids = false, $filter = false, $update = false, $hide = false)
    {
        $systemPreferences = $this->ci->session->userdata("systemPreferences");
        $ids = $opinion_status_ids ? $opinion_status_ids : $systemPreferences["archiveOpinionStatus"];
        $query = [];
        $query["select"] = ["opinions.id"];
        $where_condition = $hide ? "opinions.hideFromBoard IS NULL" : "opinions.archived = 'no'";
        $query["where"] = [["opinions.opinion_status_id IN ( " . $ids . ")"], [$where_condition]];
        if (isset($filter["filter"]) && is_array($filter["filter"]) && isset($filter["filter"]["filters"])) {
            foreach ($filter["filter"]["filters"] as $_filter) {
                $this->prep_k_filter($_filter, $query, $filter["filter"]["logic"]);
            }
            unset($_filter);
        }
        $this->prep_query($query);
        if (!$update) {
            $total_rows = $this->ci->db->get($this->_table)->num_rows();
        } else {
            $update_condition = $filter["archiving_type"] === "archive" ? "archived" : "hideFromBoard";
            $total_rows = $this->ci->db->update($this->_table, [$update_condition => "yes"]);
        }
        return $total_rows;
    }
    public function load_daily_agenda_opinions()
    {
        $query = [];
        $query["select"] = ["opinions.id, opinions.detailed_info, opinions.title, opinions.legal_case_id as case_id, users.email, opinion_statuses.category", false];
        $query["join"] = [["users", "users.id = opinions.assigned_to"], ["opinion_statuses", "opinion_statuses.id = opinions.opinion_status_id", "left"], ["legal_cases lc", "lc.id = opinions.legal_case_id and lc.isDeleted = '0'", "left"]];
        $query["where"][] = ["opinions.archived = 'no' AND CURRENT_DATE = opinions.due_date AND opinions.opinion_status_id NOT IN (SELECT id from opinion_statuses WHERE category = 'done')", NULL, false];
        $query["where"][] = ["opinions.legal_case_id is null or lc.isDeleted = 0", NULL, false];
        return $this->load_all($query);
    }
    public function opinions_per_assignee()
    {
        $this->ci->load->model("language");
        $langId = $this->ci->language->get_id_by_session_lang();
        $query["select"] = ["opinions.*, concat( '" . "$this->modelCode" . "', opinions.id ) as opinionId, concat( '" . $this->ci->legal_case->get("modelCode") . "', opinions.legal_case_id ) as caseId, legal_cases.category as caseCategory, legal_cases.subject as case_subject, opinion_types_languages.name as opinionType, ts.name as opinionStatus, SUBSTRING( opinions.detailed_info, " . $this->detailed_infoSubstringStartingPosition . ", " . $this->detailed_infoSubstringLength . " ) as detailed_info", false];
        $query["join"] = [["opinion_types_languages", "opinion_types_languages.opinion_type_id = opinions.opinion_type_id and opinion_types_languages.language_id = '" . $langId . "'", "left"], ["opinion_statuses ts", "ts.id = opinions.opinion_status_id", "left"], ["legal_cases", "legal_cases.id = opinions.legal_case_id", "left"]];
        $query["where"] = [["opinions.assigned_to", $this->ci->is_auth->get_user_id()], ["opinions.archived", "no"]];
        $this->ci->load->model("legal_case", "legal_casefactory");
        $this->ci->legal_case = $this->ci->legal_casefactory->get_instance();
        $where_condition = $this->ci->legal_case->get_matter_privacy_conditions($this->logged_user_id, $this->override_privacy, false);
        $query["where"][] = ["(opinions.private IS NULL OR opinions.private = 'no' OR(opinions.private = 'yes' AND (" . "opinions.createdBy = '" . $this->logged_user_id . "' OR opinions.assigned_to = '" . $this->logged_user_id . "' OR " . "opinions.id IN (SELECT opinion_id FROM opinion_users WHERE user_id = '" . $this->logged_user_id . "') OR '" . $this->override_privacy . "' = 'yes'" . ")" . ") OR" . "(" . "opinions.legal_case_id IS NOT NULL AND " . "((opinions.private IS NULL OR opinions.private = 'no') AND " . $where_condition . ")" . ")" . ")"];
        $query["order_by"] = ["id desc"];
        return parent::load_all($query);
    }
    public function user_todays_opinions($is_api = false)
    {
        $this->ci->load->model("language");
        if ($is_api) {
            $user_id = $this->ci->user_logged_in_data["user_id"];
            $this->ci->load->model("user_preference");
            $this->ci->user_preference->fetch(["user_id" => $user_id, "keyName" => "language"]);
            $language = $this->ci->user_preference->get_field("keyValue");
            $this->ci->language->fetch(["fullName" => $language]);
            $langId = $this->ci->language->get_field("id");
            $this->ci->user_profile->fetch(["user_id" => $user_id]);
            $override_privacy = $this->ci->user_profile->get_field("overridePrivacy");
        } else {
            $user_id = $this->logged_user_id;
            $langId = $this->ci->language->get_id_by_session_lang();
            $override_privacy = $this->override_privacy;
        }
        $query["select"] = ["opinions.id, opinions.title, opinions.priority, opinions.detailed_info, concat( '" . $this->modelCode . "', opinions.id ) as opinion_id, opinion_types_languages.name as opinion_type, ts.name as opinion_status, ts.category as status_category", false];
        $query["join"] = [["opinion_types_languages", "opinion_types_languages.opinion_type_id = opinions.opinion_type_id and opinion_types_languages.language_id = '" . $langId . "'", "left"], ["opinion_statuses ts", "ts.id = opinions.opinion_status_id", "left"], ["legal_cases", "legal_cases.id = opinions.legal_case_id", "left"]];
        $query["where"] = [["opinions.assigned_to", $user_id], ["opinions.archived", "no"], ["opinions.due_date = CURDATE()", NULL, false]];
        $this->ci->load->model("legal_case", "legal_casefactory");
        $this->ci->legal_case = $this->ci->legal_casefactory->get_instance();
        $where_condition = $this->ci->legal_case->get_matter_privacy_conditions($user_id, $override_privacy, false);
        $query["where"][] = ["(opinions.private IS NULL OR opinions.private = 'no' OR(opinions.private = 'yes' AND (" . "opinions.createdBy = '" . $user_id . "' OR opinions.assigned_to = '" . $user_id . "' OR " . "opinions.id IN (SELECT opinion_id FROM opinion_users WHERE user_id = '" . $user_id . "') OR '" . $override_privacy . "' = 'yes'" . ")" . ") OR" . "(" . "opinions.legal_case_id IS NOT NULL AND " . "((opinions.private IS NULL OR opinions.private = 'no') AND " . $where_condition . ")" . ")" . ")"];
        $query["order_by"] = ["id desc"];
        return parent::load_all($query);
    }
    public function opinions_per_reporter()
    {
        $this->ci->load->model("language");
        $langId = $this->ci->language->get_id_by_session_lang();
        $query["select"] = ["opinions.*, concat( '" . $this->modelCode . "', opinions.id ) as opinionId, concat( '" . $this->ci->legal_case->get("modelCode") . "', opinions.legal_case_id ) as caseId, legal_cases.category as caseCategory, legal_cases.subject as case_subject, opinion_types_languages.name as opinionType, ts.name as opinionStatus, SUBSTRING( opinions.detailed_info, " . $this->detailed_infoSubstringStartingPosition . ", " . $this->detailed_infoSubstringLength . " ) as detailed_info", false];
        $query["join"] = [["opinion_types_languages", "opinion_types_languages.opinion_type_id = opinions.opinion_type_id and opinion_types_languages.language_id = '" . $langId . "'", "left"], ["opinion_statuses ts", "ts.id = opinions.opinion_status_id", "left"], ["legal_cases", "legal_cases.id = opinions.legal_case_id", "left"]];
        $query["where"] = [["opinions.reporter", $this->logged_user_id], ["opinions.archived", "no"]];
        $this->ci->load->model("legal_case", "legal_casefactory");
        $this->ci->legal_case = $this->ci->legal_casefactory->get_instance();
        $where_condition = $this->ci->legal_case->get_matter_privacy_conditions($this->logged_user_id, $this->override_privacy, false);
        $query["where"][] = ["(ts.category NOT IN ('cancelled', 'done') AND(opinions.private IS NULL OR opinions.private = 'no' OR(opinions.private = 'yes' AND (" . "opinions.createdBy = '" . $this->logged_user_id . "' OR opinions.assigned_to = '" . $this->logged_user_id . "' OR " . "opinions.id IN (SELECT opinion_id FROM opinion_users WHERE user_id = '" . $this->logged_user_id . "') OR '" . $this->override_privacy . "' = 'yes'" . ")" . ") OR" . "(" . "opinions.legal_case_id IS NOT NULL AND " . "((opinions.private IS NULL OR opinions.private = 'no') AND " . $where_condition . ")" . ")" . ")" . ")"];
        $query["where"][] = ["opinions.due_date >=", date("Y-m-d")];
        $query["order_by"] = ["opinions.due_date asc"];
        return parent::load_all($query);
    }
    public function count_all_opinions()
    {
        $user_id = $this->ci->user_logged_in_data["user_id"];
        $this->ci->user_profile->fetch(["user_id" => $user_id]);
        $override_privacy = $this->ci->user_profile->get_field("overridePrivacy");
        $query["select"] = ["COUNT(0) as opinions", false];
        $query["join"] = [["legal_cases", "legal_cases.id = opinions.legal_case_id", "left"]];
        $this->ci->load->model("legal_case", "legal_casefactory");
        $this->ci->legal_case = $this->ci->legal_casefactory->get_instance();
        $where_condition = $this->ci->legal_case->get_matter_privacy_conditions($user_id, $override_privacy, false);
        $query["where"][] = ["(opinions.private IS NULL OR opinions.private = 'no' OR(opinions.private = 'yes' AND (" . "opinions.createdBy = '" . $user_id . "' OR opinions.assigned_to = '" . $user_id . "' OR " . "opinions.id IN (SELECT opinion_id FROM opinion_users WHERE user_id = '" . $user_id . "') OR '" . $override_privacy . "' = 'yes'" . ")" . ") OR" . "(" . "opinions.legal_case_id IS NOT NULL AND " . "((opinions.private IS NULL OR opinions.private = 'no') AND " . $where_condition . ")" . ")" . ")"];
        $query["where"][] = ["opinions.archived", "no"];
        return $this->load($query)["opinions"];
    }
    public function dashboard_opinions_per_status($filters, $widget)
    {
        $this->ci->load->model("opinion_status");
        if (0 < $filters["type"]) {
            $this->ci->load->model("opinion_workflow_type", "opinion_workflow_typefactory");
            $this->ci->opinion_workflow_type = $this->ci->opinion_workflow_typefactory->get_instance();
            $workflow_id = $this->ci->opinion_workflow_type->load(["where" => ["type_id", $filters["type"]]])["workflow_id"];
            $workflow_statuses = $this->ci->opinion_status->load_list_workflow_statuses($workflow_id != NULL ? $workflow_id : 1);
        } else {
            $workflow_statuses = $this->ci->opinion_status->load_list();
        }
        $response["statuses"] = [];
        $response["values"] = [];
        foreach ($workflow_statuses as $status_id => $status) {
            $query = [];
            $query["select"] = ["opinions.id, concat('" . $this->modelCode . "', opinions.id, ': ', SUBSTRING(opinions.title, 1, 100)) as title", false];
            $query["where"][] = ["opinions.opinion_status_id", $status_id];
            if (0 < $filters["type"]) {
                $query["where"][] = ["opinions.opinion_type_id", $filters["type"]];
                $query["where"][] = ["opinions.workflow", $workflow_id != NULL ? $workflow_id : 1];
            }
            if (0 < $filters["year"]) {
                $query["where"][] = ["YEAR(opinions.createdOn)", $filters["year"]];
            }
            if (0 < $filters["month"]) {
                $query["where"][] = ["MONTH(opinions.createdOn)", $filters["month"]];
            }
            if ($widget == "opinions_assigned_to_me") {
                $query["where"][] = ["opinions.assigned_to", $this->logged_user_id];
            } else {
                $query["where"][] = ["opinions.reporter", $this->logged_user_id];
            }
            $data = $this->load_all($query);
            if (0 < count($data)) {
                $response["statuses"][] = $status;
                $response["values"][] = count($data);
                $response["names"][] = implode(",", array_column($data, "title"));
            }
        }
        return $response;
    }
    public function dashboard_opinions_assigned_to_auth_user($is_api = false)
    {
        $this->ci->load->model("language");
        if ($is_api) {
            $user_id = $this->ci->user_logged_in_data["user_id"];
            $this->ci->load->model("user_preference");
            $this->ci->user_preference->fetch(["user_id" => $user_id, "keyName" => "language"]);
            $language = $this->ci->user_preference->get_field("keyValue");
            $this->ci->language->fetch(["fullName" => $language]);
            $langId = $this->ci->language->get_field("id");
            $this->ci->user_profile->fetch(["user_id" => $user_id]);
            $override_privacy = $this->ci->user_profile->get_field("overridePrivacy");
        } else {
            $user_id = $this->logged_user_id;
            $langId = $this->ci->language->get_id_by_session_lang();
            $override_privacy = $this->override_privacy;
        }
        $query["select"] = ["opinions.*, concat( '" . $this->modelCode . "', opinions.id ) as opinionId, concat( '" . $this->ci->legal_case->get("modelCode") . "', opinions.legal_case_id ) as caseId, legal_cases.category as caseCategory, legal_cases.subject as case_subject, opinion_types_languages.name as opinionType, ts.name as opinionStatus, SUBSTRING( opinions.detailed_info, " . $this->detailed_infoSubstringStartingPosition . ", " . $this->detailed_infoSubstringLength . " ) as detailed_info", false];
        $query["join"] = [["opinion_types_languages", "opinion_types_languages.opinion_type_id = opinions.opinion_type_id and opinion_types_languages.language_id = '" . $langId . "'", "left"], ["opinion_statuses ts", "ts.id = opinions.opinion_status_id", "left"], ["legal_cases", "legal_cases.id = opinions.legal_case_id", "left"]];
        $query["where"] = [["opinions.assigned_to", $user_id], ["opinions.archived", "no"]];
        $this->ci->load->model("legal_case", "legal_casefactory");
        $this->ci->legal_case = $this->ci->legal_casefactory->get_instance();
        $where_condition = $this->ci->legal_case->get_matter_privacy_conditions($user_id, $override_privacy, false);
        $query["where"][] = ["(ts.category NOT IN ('cancelled', 'done') AND(opinions.private IS NULL OR opinions.private = 'no' OR(opinions.private = 'yes' AND (" . "opinions.createdBy = '" . $user_id . "' OR opinions.assigned_to = '" . $user_id . "' OR " . "opinions.id IN (SELECT opinion_id FROM opinion_users WHERE user_id = '" . $user_id . "') OR '" . $override_privacy . "' = 'yes'" . ")" . ") OR" . "(" . "opinions.legal_case_id IS NOT NULL AND " . "((opinions.private IS NULL OR opinions.private = 'no') AND " . $where_condition . ")" . ")" . ")" . ")"];
        $query["order_by"] = ["due_date asc"];
        return parent::load_all($query);
    }
    public function universal_search($q, $pagingOn = true)
    {
        if (!isset($this->ci->legal_case) || !is_object($this->ci->legal_case)) {
            $this->ci->load->model("legal_case", "legal_casefactory");
            $this->ci->legal_case = $this->ci->legal_casefactory->get_instance();
        }
        $where_condition = $this->ci->legal_case->get_matter_privacy_conditions($this->logged_user_id, $this->override_privacy, false);
        $q = addslashes(trim((string) $q));
        $this->ci->load->model("language");
        $langId = $this->ci->language->get_id_by_session_lang();
        $query["select"] = ["opinions.*, concat( '" . $this->modelCode . "', opinions.id ) as opinionId, concat( '" . $this->ci->legal_case->get("modelCode") . "', opinions.legal_case_id ) as caseId, opinion_types_languages.name as opinionType, ts.name as opinionStatus, SUBSTRING( opinions.detailed_info, " . $this->detailed_infoSubstringStartingPosition . ", " . $this->detailed_infoSubstringLength . " ) as detailed_info,CONCAT(assigned.firstName,' ',assigned.lastName) as assigned_to, legal_cases.category as case_category", false];
        $query["join"] = [["opinion_types_languages", "opinion_types_languages.opinion_type_id = opinions.opinion_type_id and opinion_types_languages.language_id = '" . $langId . "'", "left"], ["opinion_statuses ts", "ts.id = opinions.opinion_status_id", "left"], ["legal_cases", "legal_cases.id = opinions.legal_case_id", "left"], ["user_profiles assigned", "assigned.user_id = opinions.assigned_to", "left"]];
        $query["where"][] = ["(opinions.title LIKE '%" . $q . "%' OR opinions.detailed_info LIKE '%" . $q . "%' OR opinions.id = if(SUBSTRING('" . $q . "', 1, 1) = '" . $this->modelCode . "', SUBSTRING('" . $q . "', 2), '" . $q . "'))"];
        $query["where"][] = ["(opinions.private IS NULL OR opinions.private = 'no' OR(opinions.private = 'yes' AND (" . "opinions.createdBy = '" . $this->logged_user_id . "' OR opinions.assigned_to = '" . $this->logged_user_id . "' OR " . "opinions.id IN (SELECT opinion_id FROM opinion_users WHERE user_id = '" . $this->logged_user_id . "') OR '" . $this->override_privacy . "' = 'yes'" . ")" . ") OR" . "(" . "opinions.legal_case_id IS NOT NULL AND " . "((opinions.private IS NULL OR opinions.private = 'no') AND " . $where_condition . ")" . ")" . ")"];
        $query["order_by"] = ["opinions.id desc"];
        return $pagingOn ? parent::paginate($query, ["urlPrefix" => ""]) : parent::load_all($query);
    }
    public function load_case_opinions($legal_case_id, $auth_lang = false)
    {
        $query = [];
        $this->ci->load->model("language");
        $langId = $this->ci->language->get_id_by_session_lang($auth_lang);
        $query["select"] = ["opinions.*, concat( '" . $this->modelCode . "', opinions.id ) as opinionId, opinion_types_languages.name as opinionType, ts.name as opinionStatus,CONCAT(assigned.firstName,' ',assigned.lastName) as assigned_to,assigned.status as assignedToStatus", false];
        $query["join"] = [["opinion_types_languages", "opinion_types_languages.opinion_type_id = opinions.opinion_type_id and opinion_types_languages.language_id = '" . $langId . "'", "left"], ["opinion_statuses ts", "ts.id = opinions.opinion_status_id", "left"], ["user_profiles assigned", "assigned.user_id = opinions.assigned_to", "left"], ["legal_cases", "legal_cases.id = opinions.legal_case_id", "left"]];
        $this->ci->load->model("legal_case", "legal_casefactory");
        $this->ci->legal_case = $this->ci->legal_casefactory->get_instance();
        $where_condition = $this->ci->legal_case->get_matter_privacy_conditions($this->logged_user_id, $this->override_privacy, false);
        $query["where"][] = ["legal_case_id", $legal_case_id];
        $query["where"][] = ["(opinions.private IS NULL OR opinions.private = 'no' OR(opinions.private = 'yes' AND (" . "opinions.createdBy = '" . $this->logged_user_id . "' OR opinions.assigned_to = '" . $this->logged_user_id . "' OR " . "opinions.id IN (SELECT opinion_id FROM opinion_users WHERE user_id = '" . $this->logged_user_id . "') OR '" . $this->override_privacy . "' = 'yes'" . ")" . ") OR" . "(" . "opinions.legal_case_id IS NOT NULL AND " . "((opinions.private IS NULL OR opinions.private = 'no') AND " . $where_condition . ")" . ")" . ")"];
        $query["order_by"] = ["opinions.id desc"];
        return $this->load_all($query);
    }
    public function insert_opinion_users($opinionUsers)
    {
        $sqlInsert = "";
        $sqlDelete = [];
        $rows = [];
        foreach ($opinionUsers as $key => $opinionUser) {
            extract($opinionUser);
            if (is_array($users)) {
                $subDelete = "(opinion_id = '" . $opinion_id . "' and user_id NOT IN (0";
                foreach ($users as $user_id) {
                    if (strcmp($user_id, "")) {
                        $rows[] = compact("opinion_id", "user_id");
                        $subDelete .= ", '" . $user_id . "'";
                    }
                }
                $subDelete .= "))";
            } else {
                $subDelete = "(opinion_id = '" . $opinion_id . "')";
            }
            $sqlDelete[] = [$subDelete];
        }
        $this->prep_query(["or_where" => $sqlDelete]);
        $this->ci->db->delete("opinion_users");
        $this->reset_write();
        if (count($rows)) {
            $tableName = $this->_table;
            $this->_table = "opinion_users";
            $this->insert_on_duplicate_update_batch($rows, ["opinion_id", "user_id"]);
            $this->_table = $tableName;
        }
        return true;
    }
    public function load_opinion_users($opinion_id)
    {
        $table = $this->_table;
        $this->_table = "opinion_users";
        $query["select"] = ["UP.user_id as id, CONCAT( UP.firstName, ' ', UP.lastName ) as name,UP.status as status", false];
        $query["join"] = ["user_profiles UP", "UP.user_id = opinion_users.user_id", "inner"];
        $query["where"] = ["opinion_users.opinion_id", $opinion_id];
        $response = $this->load_all($query);
        $this->_table = $table;
        return $response;
    }
    public function count_related_time_tracking_contact($id)
    {
        $table = $this->_table;
        $this->_table = "user_activity_logs ual";
        $query = [];
        $query["select"] = ["COUNT(0) as numRows"];
        $query["where"] = ["ual.opinion_id", $id];
        $this->prep_query($query);
        $data = $this->load($query);
        $this->_table = $table;
        return $data["numRows"];
    }
    public function lookup($term)
    {
        $configList = ["key" => "opinions.id", "value" => "name"];
        $configQury["select"][] = ["opinions.*, CONCAT('" . $this->modelCode . "',opinions.id) as opinion_id,IF(LENGTH(opinions.detailed_info) > 45, CONCAT(SUBSTR(opinions.detailed_info, 1, 45), ' ', '...'), opinions.detailed_info) as detailed_info, legal_cases.category, CONCAT(SUBSTRING(legal_cases.subject, 1, 72), '...') as caseSubject, clients_view.id as client_id, clients_view.name as client_name, concat(`modified`.`firstName`, ' ', `modified`.`lastName`) AS `modifiedByName`, concat(`user_profiles`.`firstName`, ' ', `user_profiles`.`lastName`) AS `createdByName`, ts.name as opinionStatus", false];
        $configQury["join"] = [["legal_cases", "legal_cases.id = opinions.legal_case_id", "left"], ["user_profiles", "user_profiles.user_id = opinions.createdBy", "left"], ["user_profiles modified", "modified.user_id = opinions.modifiedBy", "left"], ["opinion_statuses as ts", "ts.id = opinions.opinion_status_id", "left"], ["clients_view", "clients_view.id = legal_cases.client_id AND clients_view.model = 'clients'", "left"]];
        $this->ci->load->model("legal_case", "legal_casefactory");
        $this->ci->legal_case = $this->ci->legal_casefactory->get_instance();
        $where_condition = $this->ci->legal_case->get_matter_privacy_conditions($this->logged_user_id, $this->override_privacy, false);
        $configQury["where"][] = ["(opinions.private IS NULL OR opinions.private = 'no' OR(opinions.private = 'yes' AND (" . "opinions.createdBy = '" . $this->logged_user_id . "' OR opinions.assigned_to = '" . $this->logged_user_id . "' OR " . "opinions.id IN (SELECT opinion_id FROM opinion_users WHERE user_id = '" . $this->logged_user_id . "') OR '" . $this->override_privacy . "' = 'yes'" . ")" . ") OR" . "(" . "opinions.legal_case_id IS NOT NULL AND " . "((opinions.private IS NULL OR opinions.private = 'no') AND " . $where_condition . ")" . ")" . ")"];
        if (!empty($term)) {
            $modelCode = substr($term, 0, 1);
            $opinion_id = substr($term, 1);
            if (strcasecmp($modelCode, $this->get("modelCode")) === 0 && is_numeric($opinion_id)) {
                $configQury["where"][] = ["opinions.id = " . $opinion_id, NULL, false];
            } else {
                $term = $this->ci->db->escape_like_str($term);
                $configQury["where"][] = ["opinions.detailed_info LIKE '%" . $term . "%' OR opinions.title LIKE '%" . $term . "%'", NULL, false];
            }
        }
        if ($moreFilters = $this->ci->input->get("more_filters")) {
            foreach ($moreFilters as $_field => $_term) {
                $configQury["where"][] = [$_field, $_term];
            }
        }
        $this->ci->load->model("user_group_permission");
        $user_permission = $this->ci->user_group_permission->get_permission_data($this->ci->session->userdata("AUTH_user_group_id"));
        if (is_array($user_permission) && array_key_exists("core", $user_permission)) {
            $user_core_permissions = $user_permission["core"];
            if (count($user_core_permissions) != 1 && !in_array("/opinions/all_opinions/", $user_core_permissions)) {
                $configQury["where"][] = ["opinions.assigned_to = " . $this->ci->session->userdata("AUTH_user_id"), NULL, false];
            }
        }
        return $this->load_all($configQury, $configList);
    }
    public function lookup_location($term)
    {
        $term = $this->ci->db->escape_like_str($term);
        $table = $this->_table;
        $this->_table = "opinion_locations";
        $configList = ["key" => "opinion_locations.id", "value" => "location"];
        $configQury["select"] = ["opinion_locations.id, opinion_locations.name as location", false];
        $configQury["where"] = [["opinion_locations.name LIKE '%" . $term . "%'", NULL, false], ["opinion_locations.name <> ''", NULL, false]];
        $return = $this->load_all($configQury, $configList);
        $this->_table = $table;
        return $return;
    }
    public function api_load_all_opinions($user_id, $overridePrivacy, $authLang, $take = 20, $skip = 0, $term = "", $get_reported_opinions = false, $search_filters, $all_opinions = true, $opinions_by_user_id, $contributed, $contributors_ids)
    {
        $query = [];
        $_table = $this->_table;
        $this->_table = "opinions_detailed_view as opinions";
        $query["select"] = ["opinions.id"];
        if ($user_id && !$all_opinions && !$contributed) {
            if (!$get_reported_opinions) {
                $query["where"][] = ["opinions.assignedToId", $user_id];
            } else {
                $query["where"][] = ["opinions.reportedById", $user_id];
            }
        } else {
            if ($opinions_by_user_id && $all_opinions && !$contributed) {
                if (!$get_reported_opinions) {
                    $query["where"][] = ["opinions.assignedToId", $opinions_by_user_id];
                } else {
                    $query["where"][] = ["opinions.reportedById", $opinions_by_user_id];
                }
            }
        }
        $query["where"][] = ["opinions.archived", "no"];
        if ($term != "") {
            $term = $this->ci->db->escape_like_str($term);
            $query["where"][] = [" ( opinionFulldetailed_info  LIKE '%" . $term . "%' OR opinions.title LIKE '%" . $term . "%' )", NULL, false];
        }
        $this->ci->load->model("legal_case", "legal_casefactory");
        $this->ci->legal_case = $this->ci->legal_casefactory->get_instance();
        $where_condition = $this->ci->legal_case->get_matter_privacy_conditions($user_id, $overridePrivacy, false);
        $query["where"][] = ["(opinions.private IS NULL OR opinions.private = 'no' OR(opinions.private = 'yes' AND (" . "opinions.createdBy = '" . $user_id . "' OR opinions.assigned_to = '" . $user_id . "' OR " . "opinions.id IN (SELECT opinion_id FROM opinion_users WHERE user_id = '" . $user_id . "') OR '" . $overridePrivacy . "' = 'yes'" . ")" . ") OR" . "(" . "opinions.legal_case_id IS NOT NULL AND " . "((opinions.private IS NULL OR opinions.private = 'no') AND " . $where_condition . ")" . ")" . ")"];
        if ($contributed) {
            $query["where"][] = ["opinion_contributors.user_id IN (" . $contributors_ids . ")"];
        }
        $this->ci->load->model("language");
        $langId = $this->ci->language->get_id_by_session_lang($authLang);
        $query["join"] = [["legal_cases", "legal_cases.id = opinions.legal_case_id", "left"], ["opinion_contributors", "opinion_contributors.opinion_id = opinions.id", "left"]];
        $query = $this->filter_builder($query, $search_filters, $get_reported_opinions);
        $this->prep_query($query);
        $query["group_by"] = ["opinions.id"];
        $this->ci->db->count_all_results($this->_table);
        $temp_result = $this->load_all($query);
        $ids = [];
        foreach ($temp_result as $row) {
            array_push($ids, $row["id"]);
        }
        if (empty($ids)) {
            $ids = "0";
        } else {
            $ids = implode(", ", $ids);
        }
        $query = [];
        $query["select"] = ["opinions.*,assigned_to.status as assignedToStatus, reporter.status as reportedByStatus, opinion_types_languages.name as opinionType", false];
        $query["where"] = ["opinions.id IN (" . $ids . ")"];
        $query["join"] = [["user_profiles assigned_to", "assigned_to.user_id = opinions.assignedToId", "left"], ["user_profiles reporter", "reporter.user_id = opinions.reportedById", "left"], ["opinion_types_languages", "opinion_types_languages.opinion_type_id = opinions.opinion_type_id and opinion_types_languages.language_id = '" . $langId . "'", "left"]];
        $query["order_by"] = ["opinions.due_date desc"];
        $query["limit"] = [$take, $skip];
        $response = [];
        $response["data"] = $this->load_all($query);
        $response["totalRows"] = count($response["data"]);
        $this->_table = $_table;
        for ($n = 0; $n < count($response["data"]); $n++) {
            $response["data"][$n]["opinionFullDetailed_info"] = strip_tags(trim($response["data"][$n]["opinionFullDetailed_info"]));
            $response["data"][$n]["detailed_info"] = strip_tags(trim($response["data"][$n]["detailed_info"]));
        }
        return $response;
    }
    protected function filter_builder($query, $search_filters, $get_reported_opinions)
    {
        foreach ($search_filters as $key => $value) {
            if ($key === "assignee") {
                if ($get_reported_opinions) {
                    $query["where"][] = [" ( opinions." . $key . " = '" . $value . "' )", NULL, false];
                }
            } else {
                if ($key === "dueDate") {
                    $value["operator"] = $value["operator"] == "" ? "=" : $value["operator"];
                    if ($value["operator"] === "between") {
                        $query["where"][] = [" ( opinions.due_date >= '" . $value["date"]["from"] . "' AND opinions.due_date <= '" . $value["date"]["to"] . "' )", NULL, false];
                    } else {
                        $query["where"][] = [" ( opinions.due_date " . $value["operator"] . " '" . $value["date"] . "' )", NULL, false];
                    }
                } else {
                    if (gettype($value) === "array") {
                        if ($key === "priority") {
                            $value = implode("','", $value);
                            $query["where"][] = [" ( opinions." . $key . " in ('" . $value . "') )", NULL, false];
                        } else {
                            $value = implode(",", $value);
                            $query["where"][] = [" ( opinions." . $key . " in (" . $value . ") )", NULL, false];
                        }
                    } else {
                        $query["where"][] = [" ( opinions." . $key . " = '" . $value . "' )", NULL, false];
                    }
                }
            }
        }
        return $query;
    }
    public function api_lookup($term, $user_id, $overridePrivacy, $case_id = 0)
    {
        $configList = ["key" => "opinions.id", "value" => "name"];
        $configQury["select"][] = ["opinions.*, concat(`modified`.`firstName`, ' ', `modified`.`lastName`) AS `modifiedByName`, concat(`user_profiles`.`firstName`, ' ', `user_profiles`.`lastName`) AS `createdByName`, ts.name as opinionStatus, legal_cases.subject as relatedMatterName,        legal_cases.client_id as clientId,        CASEWHEN c.contact_id IS NOT NULL                THEN concat(`contacts`.`firstName`, ' ', `contacts`.`lastName`)                ELSE co.name END AS `clientName`", false];
        $configQury["join"] = [["legal_cases", "legal_cases.id = opinions.legal_case_id", "left"], ["user_profiles", "user_profiles.user_id = opinions.createdBy", "left"], ["user_profiles modified", "modified.user_id = opinions.modifiedBy", "left"], ["opinion_statuses as ts", "ts.id = opinions.opinion_status_id", "left"], ["clients as c", "c.id = legal_cases.client_id", "left"], ["companies as co", "co.id = c.company_id", "left"], ["contacts", "contacts.id = c.contact_id", "left"]];
        $this->ci->load->model("legal_case", "legal_casefactory");
        $this->ci->legal_case = $this->ci->legal_casefactory->get_instance();
        $where_condition = $this->ci->legal_case->get_matter_privacy_conditions($user_id, $overridePrivacy, false);
        $configQury["where"][] = ["(opinions.private IS NULL OR opinions.private = 'no' OR(opinions.private = 'yes' AND (" . "opinions.createdBy = '" . $user_id . "' OR opinions.assigned_to = '" . $user_id . "' OR " . "opinions.id IN (SELECT opinion_id FROM opinion_users WHERE user_id = '" . $user_id . "') OR '" . $overridePrivacy . "' = 'yes'" . ")" . ") OR" . "(" . "opinions.legal_case_id IS NOT NULL AND " . "((opinions.private IS NULL OR opinions.private = 'no') AND " . $where_condition . ")" . ")" . ")"];
        if ($case_id) {
            $configQury["where"][] = ["opinions.legal_case_id", $case_id, NULL, false];
        }
        if (!empty($term)) {
            $modelCode = substr($term, 0, 1);
            $opinion_id = substr($term, 1);
            if (strcasecmp($modelCode, $this->get("modelCode")) === 0 && is_numeric($opinion_id)) {
                $configQury["where"][] = ["opinions.id = " . $opinion_id, NULL, false];
            } else {
                $term = $this->ci->db->escape_like_str($term);
                $configQury["where"][] = ["opinions.detailed_info LIKE '%" . $term . "%' OR opinions.title LIKE '%" . $term . "%'", NULL, false];
            }
        }
        if ($user_id) {
            $configQury["where"][] = ["opinions.assigned_to", $user_id, NULL, false];
        }
        return $this->load_all($configQury, $configList);
    }
    public function api_load_opinion($id, $user_id, $overridePrivacy)
    {
        $query = [];
        $this->ci->load->model("legal_case", "legal_casefactory");
        $this->ci->legal_case = $this->ci->legal_casefactory->get_instance();
        $where_condition = $this->ci->legal_case->get_matter_privacy_conditions($user_id, $overridePrivacy, false);
        $query["select"] = ["opinions.*, tee.effectiveEffort,CASE WHEN assigned.status='Inactive' THEN CONCAT(assigned.firstName,' ',assigned.lastName,' (Inactive)') ELSE CONCAT(assigned.firstName,' ',assigned.lastName) END as assignedToLookUp,CASE WHEN reporter.status='inactive' THEN CONCAT(reporter.firstName,' ',reporter.lastName,' (Inactive)') ELSE CONCAT(reporter.firstName,' ',reporter.lastName) END as reporterLookUp,CONCAT( created.firstName, ' ', created.lastName ) as createdBy, legal_cases.subject as caseSubject, legal_cases.category as caseCategory, opinion_locations.name AS location, contract.name as contract_name", false];
        $query["where"][] = ["opinions.id", $id];
        $query["where"][] = ["(opinions.private IS NULL OR opinions.private = 'no' OR(opinions.private = 'yes' AND (" . "opinions.createdBy = '" . $user_id . "' OR opinions.assigned_to = '" . $user_id . "' OR " . "opinions.id IN (SELECT opinion_id FROM opinion_users WHERE user_id = '" . $user_id . "') OR '" . $overridePrivacy . "' = 'yes'" . ")" . ") OR" . "(" . "opinions.legal_case_id IS NOT NULL AND " . "((opinions.private IS NULL OR opinions.private = 'no') AND " . $where_condition . ")" . ")" . ")"];
        $query["join"] = [["legal_cases", "legal_cases.id = opinions.legal_case_id", "left"], ["contract", "contract.id = opinions.contract_id", "left"], ["user_profiles assigned", "assigned.user_id = opinions.assigned_to", "left"], ["user_profiles reporter", "reporter.user_id = opinions.reporter", "left"], ["user_profiles created", "created.user_id = opinions.createdBy", "left"], ["opinion_effective_effort AS tee", "tee.opinion_id = opinions.id", "left"], ["opinion_locations", "opinion_locations.id = opinions.opinion_location_id", "left"]];
        return $this->ci->opinion->load($query);
    }
    public function user_rates_per_opinions_per_assignees($organizationID, $filter = [], $post_user_filter = false)
    {
        $query = [];
        $response = [];
        $this->ci->load->model(["money_preference", "language"]);
        $userGroupsRate = $this->ci->money_preference->get_value_by_key("userGroupsAppearInUserRatePerHourGrid");
        $userGroupsRate = $userGroupsRate["keyValue"];
        $money_preference = $this->ci->money_preference->get_value_by_key("userRatePerHour");
        $userRatePerHour = 0;
        if (isset($money_preference["keyValue"])) {
            $userRatesPerHour = unserialize($money_preference["keyValue"]);
        }
        if (isset($userRatesPerHour[$organizationID])) {
            $userRatePerHour = $userRatesPerHour[$organizationID];
        }
        $systemPreferences = $this->ci->session->userdata("systemPreferences");
        $kpiUserGroups = $systemPreferences["kpiUserGroups"];
        if (empty($kpiUserGroups)) {
            return $response;
        }
        $table = $this->_table;
        $this->_table = "user_activity_logs_full_details as ual";
        if (!empty($userGroupsRate) && $userGroupsRate != "" && !empty($userRatePerHour) && $userRatePerHour != "") {
            $query["select"] = ["ual.id,\r\n\t\t\tCASE\r\n\t\t\t\tWHEN urph.ratePerHour IS NOT NULL\r\n\t\t\t\tTHEN urph.ratePerHour\r\n\t\t\t\tELSE\r\n\t\t\t\t\t(CASE\r\n\t\t\t\t\t\tWHEN u.user_group_id IN (" . $userGroupsRate . ")\r\n\t\t\t\t\t\tTHEN " . $userRatePerHour . "\r\n\t\t\t\t\t\tELSE 0\r\n\t\t\t\t\tEND)\r\n\t\t\t\tEND AS ratePerHour,\r\n\t\t\t\tual.user_id AS userId, ual.effectiveEffort, ual.comments, createdBy, createdOn, opinionId, opinionSummary, opinion_title, legalCaseId, legalCaseSummary, legalCaseDetailed_info, worker, inserter, timeStatus, billingStatus", false];
        } else {
            $query["select"] = ["ual.id,\r\n\t\t\t\tual.user_id AS userId, ual.effectiveEffort, ual.comments, createdBy, createdOn, opinionId, opinionSummary, opinion_title, legalCaseId, legalCaseSummary, legalCaseDetailed_info, worker, inserter, timeStatus, billingStatus", false];
        }
        if (is_array($filter) && isset($filter["filters"])) {
            foreach ($filter["filters"] as $_filter) {
                if (!empty($_filter["filters"][0]["field"]) && in_array($_filter["filters"][0]["field"], ["opinions.effectiveEffort", "opinions.estimated_effort"])) {
                    $system_preferences = $this->ci->session->userdata("systemPreferences");
                    $this->ci->load->library("TimeMask");
                    $_filter["filters"][0]["value"] = $this->ci->timemask->humanReadableToHours($_filter["filters"][0]["value"]);
                }
                $this->prep_k_filter($_filter, $query, $filter["logic"]);
            }
            unset($_filter);
        }
        $query["join"] = [["users u", "u.id = ual.user_id", "left"], ["user_rate_per_hour urph", "urph.user_id = ual.user_id AND urph.organization_id = " . $organizationID, "left"]];
        $query["where"][] = ["ual.legal_case_id IS NULL"];
        $query["where"][] = ["u.user_group_id IN (" . $kpiUserGroups . ")"];
        if ($post_user_filter) {
            $query["where"][] = ["ual.user_id", $post_user_filter];
        }
        $this->prep_query($query);
        $response["totalRows"] = $this->ci->db->count_all_results($this->_table);
        $query["order_by"] = ["ual.logDate desc"];
        if ($limit = $this->ci->input->post("take", true)) {
            $query["limit"] = [$this->ci->input->post("take", true), $this->ci->input->post("skip", true)];
        }
        $response["data"] = $this->load_all($query);
        $this->_table = $table;
        return $response;
    }
    public function count_related_expenses($id)
    {
        $table = $this->_table;
        $this->_table = "expenses";
        $query = [];
        $query["select"] = ["COUNT(0) as numRows"];
        $query["where"] = ["expenses.opinion", $id];
        $data = $this->load($query);
        $this->_table = $table;
        return $data["numRows"];
    }
    public function load_opinion_contributors($id)
    {
        $table = $this->_table;
        $this->_table = "opinion_contributors";
        $query["select"] = ["UP.user_id as id, CONCAT( UP.firstName, ' ', UP.lastName ) as name,UP.status as status", false];
        $query["join"] = ["user_profiles UP", "UP.user_id = opinion_contributors.user_id", "inner"];
        $query["where"] = ["opinion_contributors.opinion_id", $id];
        $response = $this->load_all($query);
        $this->_table = $table;
        return $response;
    }
    public function roll_session($filter, $sortable, $export = false, &$query = [], &$stringQuery = "")
    {
        $_table = $this->_table;
        $this->_table = "opinions_detailed_view AS opinions";
        $query = [];
        $response = [];
        $this->ci->load->model("language");
        $langId = $this->ci->language->get_id_by_session_lang();
        $query["select"] = ["opinions.id, opinions.title, opinions.opinionFullDetailed_info,opinions.assignedToId as assigned_to,opinions.opinion_type_id, opinions.opinion_status_id,opinions.legal_case_id as legal_case_id,opinions.caseId,opinions.assigned_to as assigned_to_name, opinions.opinionStatus,opinions.reporter,stages.name as stage_name, IF( ISNULL(clients.company_id), CASE WHEN con.father != ' ' THEN CONCAT_WS( ' ', con.firstName, con.father, con.lastName) ELSE CONCAT_WS(' ', con.firstName, con.lastName) END, com.name) AS client,GROUP_CONCAT(DISTINCT lcler.number SEPARATOR ',' ) as stage_ext_reference,opinions.due_date,opinion_types_languages.name as opinionType,GROUP_CONCAT(DISTINCT legal_case_containers_full_details.containerId SEPARATOR ',' ) as containerID, court_types.name as court_type,court_degrees.name as court_degree,court_regions.name as court_region,courts.name as court, opinions.createdOn as createdOn,legal_case_litigation_details.id AS stage_id,if(isnull(clients.company_id), concat_ws(' ', con.foreignFirstName, con.foreignLastName), com.foreignName) AS client_foreign_name,(SELECT GROUP_CONCAT(CASE WHEN legal_case_opponents.opponent_member_type IS NULL THEN NULL ELSE (CASE  WHEN opponent_positions.name != '' THEN (CASE WHEN legal_case_opponents.opponent_member_type = 'company' THEN IFNULL(CONCAT(opponentCompany.foreignName, ' - ', opponent_positions.name), CONCAT(opponentCompany.name, ' - ', opponent_positions.name)) ELSE ( CONCAT(IFNULL(opponentContact.foreignFirstName, opponentContact.firstName), ' ', IFNULL(opponentContact.foreignLastName, opponentContact.lastName), ' - ', opponent_positions.name) ) END)  ELSE (CASE WHEN legal_case_opponents.opponent_member_type = 'company'  THEN IFNULL(opponentCompany.foreignName, opponentCompany.name) ELSE ( CONCAT(IFNULL(opponentContact.foreignFirstName, opponentContact.firstName), ' ', IFNULL(opponentContact.foreignLastName, opponentContact.lastName)) ) END) END) END order by opinions.legal_case_id ASC SEPARATOR ', ') FROM legal_case_opponents LEFT JOIN opponents ON opponents.id = legal_case_opponents.opponent_id LEFT JOIN companies AS opponentCompany  ON opponentCompany.id = opponents.company_id AND legal_case_opponents.opponent_member_type = 'company' LEFT JOIN contacts AS opponentContact  ON opponentContact.id = opponents.contact_id AND legal_case_opponents.opponent_member_type = 'contact' LEFT JOIN legal_case_opponent_position_languages AS opponent_positions  ON opponent_positions.legal_case_opponent_position_id = legal_case_opponents.opponent_position and opponent_positions.language_id = '" . $langId . "' WHERE legal_case_opponents.case_id = opinions.legal_case_id) AS opponent_foreign_name, legal_cases.category as case_category, legal_cases.subject as case_subject, " . "(SELECT GROUP_CONCAT(CASE WHEN `legal_case_opponents`.`opponent_member_type` IS NULLTHEN  NULLELSE (                                CASE WHEN opponent_positions.name != ''                                    THEN                                         (CASE WHEN `legal_case_opponents`.`opponent_member_type` = 'company'                                            THEN CONCAT(`opponentCompany`.`name`, ' - ', opponent_positions.name)                                            ELSE (CASE WHEN opponentContact.father!='' THEN CONCAT(opponentContact.firstName, ' ', opponentContact.father, ' ', opponentContact.lastName, ' - ', opponent_positions.name) ELSE CONCAT(opponentContact.firstName, ' ', opponentContact.lastName, ' - ', opponent_positions.name) END)                                        END)                                    ELSE                                        (CASE WHEN `legal_case_opponents`.`opponent_member_type` = 'company'                                            THEN `opponentCompany`.`name`                                            ELSE (CASE WHEN opponentContact.father!='' THEN CONCAT(opponentContact.firstName, ' ', opponentContact.father, ' ', opponentContact.lastName) ELSE CONCAT(opponentContact.firstName, ' ', opponentContact.lastName) END)                                        END)END                        )END order by `legal_cases`.`id` ASC SEPARATOR ',' )from  `legal_case_opponents`LEFT JOIN `opponents` ON `opponents`.`id` = `legal_case_opponents`.`opponent_id`LEFT JOIN `companies` AS `opponentCompany` ON `opponentCompany`.`id` = `opponents`.`company_id` AND `legal_case_opponents`.`opponent_member_type` = 'company'LEFT JOIN `contacts` AS `opponentContact` ON `opponentContact`.`id` = `opponents`.`contact_id` AND `legal_case_opponents`.`opponent_member_type` = 'contact'LEFT JOIN legal_case_opponent_position_languages AS opponent_positions ON opponent_positions.legal_case_opponent_position_id = legal_case_opponents.opponent_position and opponent_positions.language_id = '" . $langId . "'\r\n                        WHERE `legal_case_opponents`.`case_id` = `legal_cases`.`id` ) AS opponents", false];
        if (is_array($filter) && isset($filter["filters"])) {
            foreach ($filter["filters"] as $_filter) {
                if (!empty($_filter["filters"][0]["field"]) && in_array($_filter["filters"][0]["field"], ["opinions.effectiveEffort", "opinions.estimated_effort"])) {
                    $system_preferences = $this->ci->session->userdata("systemPreferences");
                    $this->ci->load->library("TimeMask");
                    $_filter["filters"][0]["value"] = $this->ci->timemask->humanReadableToHours($_filter["filters"][0]["value"]);
                }
                $this->prep_k_filter($_filter, $query, $filter["logic"]);
            }
            unset($_filter);
        }
        $this->ci->load->model("system_configuration");
        $excluded_statuses = $this->ci->system_configuration->get_value_by_key("opinionReportExcludedStatuses");
        if (!empty($excluded_statuses)) {
            $query["where_not_in"][] = ["opinions.opinion_status_id", $excluded_statuses];
        }
        $fetch_only_opinions_related_to_matters = $this->ci->system_configuration->get_value_by_key("opinionReportFetchOnlyRelatedToMatters");
        if ($fetch_only_opinions_related_to_matters == "yes") {
            $query["where"][] = ["opinions.legal_case_id is not null", NULL, false];
        }
        $this->ci->load->model("language");
        $langId = $this->ci->language->get_id_by_session_lang();
        $query["join"] = [["legal_cases", "legal_cases.id = opinions.legal_case_id", "left"], ["legal_case_containers_full_details", "legal_case_containers_full_details.legal_case_id = legal_cases.id", "left"], ["legal_case_litigation_details", "legal_case_litigation_details.id = opinions.stage", "left"], ["legal_case_stages", "legal_case_stages.id = legal_case_litigation_details.legal_case_stage", "left"], ["legal_case_stage_languages as stages", "stages.legal_case_stage_id = legal_case_stages.id and stages.language_id = '" . $langId . "'", "left"], ["legal_case_litigation_external_references as lcler", "lcler.stage=legal_case_litigation_details.id", "left"], ["clients", "clients.id = legal_cases.client_id", "left"], ["companies com", "com.id = clients.company_id", "left"], ["contacts con", "con.id = clients.contact_id", "left"], ["opinion_types_languages", "opinion_types_languages.opinion_type_id = opinions.opinion_type_id and opinion_types_languages.language_id = '" . $langId . "'", "left"], ["courts", "courts.id = legal_case_litigation_details.court_id", "left"], ["court_types", "court_types.id = legal_case_litigation_details.court_type_id", "left"], ["court_degrees", "court_degrees.id = legal_case_litigation_details.court_degree_id", "left"], ["court_regions", "court_regions.id = legal_case_litigation_details.court_region_id", "left"]];
        $query["group_by"] = ["opinions.id"];
        $this->prep_query($query);
        $response["totalRows"] = $this->ci->db->get($this->get("_table"))->num_rows();
        foreach ($sortable as $_sort) {
            $query["order_by"][] = [$_sort["field"], $_sort["dir"]];
        }
        $paginationConf = [];
        if (!$export) {
            $query["limit"] = [$this->ci->input->post("take", true), $this->ci->input->post("skip", true)];
            $paginationConf = ["urlPrefix" => ""];
            $paginationConf["inPage"] = $this->ci->input->post("take", true);
        }
        $response["data"] = $export ? parent::load_all($query) : parent::paginate($query, $paginationConf);
        $stringQuery = $this->ci->db->last_query();
        $this->_table = $_table;
        return $response;
    }
    public function load_opinions_on_stage_directly($legal_case_id, $order_by = [], $return_attachments = false, $stage_id = false, $page = "", $get_count = false, $page_limit = 10, $get_count_only = false)
    {
        $this->ci->load->model("language");
        $lang_id = $this->ci->language->get_id_by_session_lang();
        $select = "opinions.id,\r\n                   opinions.title,\r\n                   concat( '" . $this->modelCode . "', opinions.id )       AS opinionId,\r\n                   SUBSTRING( opinions.detailed_info, " . $this->detailed_infoSubstringStartingPosition . ", " . $this->detailed_infoSubstringLength . " ) as opinionFullDetailed_info,\r\n                   opinions.detailed_info,\r\n                   CONCAT( assigned_p.firstName, ' ', assigned_p.lastName ) AS assigned_to,\r\n                   opinions.priority,\r\n                   ts.name AS opinionStatus,\r\n                   opinions.due_date,\r\n                   opinions.assigned_to AS assignedToId,\r\n                   types.name as type";
        $query["join"][] = ["user_profiles assigned_p", "assigned_p.user_id = opinions.assigned_to", "left"];
        $query["join"][] = ["opinion_statuses ts", "ts.id = opinions.opinion_status_id", "left"];
        $query["join"][] = ["opinion_types_languages as types", "types.opinion_type_id = opinions.opinion_type_id and types.language_id = '" . $lang_id . "'", "left"];
        $query["where"][] = ["opinions.id NOT IN (select related_id from legal_case_events_related_data where related_object='Opinion')", NULL, false];
        $query["where"][] = ["opinions.legal_case_id", $legal_case_id];
        $query["where"][] = $stage_id ? ["opinions.stage", $stage_id] : ["opinions.stage is null"];
        $query["order_by"][] = !empty($order_by) ? $order_by : ["opinions.due_date desc"];
        if (!empty($page)) {
            $query["limit"] = [$page_limit, $page * $page_limit - $page_limit];
        }
        $query["select"] = [$select, false];
        if ($return_attachments) {
            $_select = $select . ", (select count(*) from documents_management_system where system_document = 1 and documents_management_system.name = concat( 'opinion_', opinions.id * 1 )) as opinions_docs_count";
            $query["select"] = [$_select, false];
        }
        if ($get_count_only) {
            $response = $this->count_total_matching_rows($query);
        } else {
            if ($get_count) {
                $response["opinions"] = parent::load_all($query);
                $response["totalRows"] = $this->count_total_matching_rows($query);
            } else {
                $response = parent::load_all($query);
            }
        }
        return $response;
    }
    public function load_opinions_documents($module_record_id)
    {
        $this->ci->load->library("dms");
        $response = $this->ci->dms->load_documents(["module" => "opinion", "module_record_id" => $module_record_id]);
        return $response;
    }
    public function delete_opinions($data, $data_is_query)
    {
        $return = false;
        if (!$data_is_query && !empty($data)) {
            $this->ci->db->select("id");
            $this->ci->db->from("opinions");
            $this->ci->db->where("opinions.id IN (" . $data . ")", NULL, false);
            $opinions_where_clause = $this->ci->db->get_compiled_select();
            $this->ci->db->reset_query();
        } else {
            $opinions_where_clause = $data;
        }
        if (!empty($opinions_where_clause)) {
            $this->ci->db->where("reminders.opinion_id IN (" . $opinions_where_clause . ")", NULL, false);
            $this->ci->db->delete("reminders");
            $this->ci->db->reset_query();
            $this->ci->db->where("opinion_users.opinion_id IN (" . $opinions_where_clause . ")", NULL, false);
            $this->ci->db->delete("opinion_users");
            $this->ci->db->reset_query();
            $this->ci->db->where("opinion_contributors.opinion_id IN (" . $opinions_where_clause . ")", NULL, false);
            $this->ci->db->delete("opinion_contributors");
            $this->ci->db->reset_query();
            $this->ci->db->where("opinion_comments.opinion_id IN (" . $opinions_where_clause . ")", NULL, false);
            $this->ci->db->delete("opinion_comments");
            $this->ci->db->reset_query();
            $this->ci->db->where("opinion_workflow_status_transition_history.opinion_id IN (" . $opinions_where_clause . ")", NULL, false);
            $this->ci->db->delete("opinion_workflow_status_transition_history");
            $this->ci->db->reset_query();
            $this->ci->db->set("opinion_id", NULL);
            $this->ci->db->where("user_activity_logs.opinion_id IN (" . $opinions_where_clause . ")", NULL, false);
            $this->ci->db->update("user_activity_logs");
            $this->ci->db->reset_query();
            $this->ci->db->set("opinion", NULL);
            $this->ci->db->where("expenses.opinion IN (" . $opinions_where_clause . ")", NULL, false);
            $this->ci->db->update("expenses");
            $this->ci->db->reset_query();
            $opinion_records = $this->ci->db->query($opinions_where_clause);
            if ($opinion_records->result()) {
                $opinion_ids = [];
                foreach ($opinion_records->result() as $row) {
                    $opinion_ids[] = $row->id ?? $row->related_id;
                }
                $opinion_ids_string_list = implode(", ", $opinion_ids);
                $this->ci->db->where("opinions.id IN (" . $opinion_ids_string_list . ")", NULL, false);
                $return = $this->ci->db->delete("opinions");
                $this->ci->db->reset_query();
            }
        }
        return $return;
    }
    public function get_opinion_details($case_subject, $case_category, $legal_case_id)
    {
        return $this->ci->lang->line("related_case") . ": " . $case_subject . " <a target=\"_blank\" href=\"" . base_url() . (!empty($case_category) || $case_category != "IP" ? "cases" : "intellectual_properties") . "/edit/" . $legal_case_id . "\">" . $this->ci->lang->line("goto") . "</a>";
    }
    public function dashboard_recent_opinions($category = "opinions", $api_params = [])
    {
        $logged_user_id = $api_params["user_id"] ?? $this->logged_user_id;
        $this->ci->load->model("user_preference");
        $recent_opinions = unserialize($this->ci->user_preference->get_value_by_user("recent_cases", $logged_user_id));
        $response = [];
        if (isset($recent_opinions[$category])) {
            $recent_opinions = $recent_opinions[$category];
            foreach ($recent_opinions as $key => $val) {
                if ($val == 0) {
                    unset($recent_opinions[$key]);
                }
            }
            $recent_opinions = implode(",", array_map("intval", $recent_opinions));
            if (!empty($recent_opinions)) {
                if (!isset($this->ci->legal_case) || !is_object($this->ci->legal_case)) {
                    $this->ci->load->model("legal_case", "legal_casefactory");
                    $this->ci->legal_case = $this->ci->legal_casefactory->get_instance();
                }
                $where_condition = $this->ci->legal_case->get_matter_privacy_conditions($logged_user_id, $this->override_privacy, false);
                $query["select"] = ["opinions.*, concat( '" . $this->modelCode . "', opinions.id ) as opinionId, '" . $category . "' AS module", false];
                $query["join"] = [["legal_cases", "legal_cases.id = opinions.legal_case_id", "left"]];
                $query["where"][] = ["(opinions.private IS NULL OR opinions.private = 'no' OR(opinions.private = 'yes' AND (" . "opinions.modifiedBy = '" . $logged_user_id . "' OR opinions.assigned_to = '" . $logged_user_id . "' OR " . "opinions.id IN (SELECT opinion_id FROM opinion_users WHERE user_id = '" . $logged_user_id . "') OR '" . $this->override_privacy . "' = 'yes'" . ")" . ") OR" . "(" . "opinions.legal_case_id IS NOT NULL AND " . "((opinions.private IS NULL OR opinions.private = 'no') AND " . $where_condition . ")" . ")" . ")"];
                $query["where"][] = ["opinions.id IN (" . $recent_opinions . ")", NULL, false];
                $query["order_by"] = ["FIELD(opinions.id, " . $recent_opinions . ")"];
                $response = $this->load_all($query);
            }
        }
        return $response;
    }
}
class mysqli_Opinion extends mysql_Opinion
{
}
class sqlsrv_Opinion extends mysql_Opinion
{
    public function k_load_all_opinions($filter, $sortable, $page_number = "", $with_user_preferences = false, $hijri_calendar_enabled = false)
    {
        $_table = $this->_table;
        $this->_table = "opinions_detailed_view as opinions";
        $select = "SELECT opinions.contract_name, opinions.contract_id, opinions.id, opinions.title, opinions.opinionId, opinions.caseId, opinions.legal_case_id, opinions.user_id, opinions.due_date, opinions.assignedToId, opinions.reportedById, opinions.private, opinions.priority, opinions.opinion_location_id, opinions.location, opinions.opinionFullDetailed_info,opinions.background_info,opinions.legal_question, opinions.opinion_status_id, opinions.opinion_type_id, opinions.createdOn, opinions.modifiedOn, opinions.modifiedBy, opinions.archived, case when opinions.assignee_status='inactive' then ( opinions.assigned_to+ ' '+  ' ('+'" . $this->ci->lang->line("inactive") . "' +')') else opinions.assigned_to END as assigned_to, case when opinions.reporter_status='inactive' then ( opinions.reporter+ ' '+  ' ('+'" . $this->ci->lang->line("inactive") . "' +')') else opinions.reporter END as reporter, case when opinions.creator_status='inactive' then ( opinions.createdBy+ ' '+  ' ('+'" . $this->ci->lang->line("inactive") . "' +')') else opinions.createdBy END as createdBy, case when opinions.modifier_status='inactive' then ( opinions.modifiedByName+ ' '+  ' ('+'" . $this->ci->lang->line("inactive") . "' +')') else opinions.modifiedByName END as modifiedByName, opinions.createdById, opinions.opinionStatus, opinions.archivedOpinions, opinions.detailed_info, opinions.caseSubject, opinions.caseFullSubject, opinions.caseCategory, CAST( opinions.estimated_effort AS nvarchar ) as estimated_effort,CAST( opinions.effectiveEffort AS nvarchar ) as effectiveEffort, opinion_types_languages.name as opinionType, opinions.contributors as contributors";
        $response = [];
        $caseIdFilter = $this->ci->input->post("caseIdFilter", true);
        $opinionStatuses = $this->ci->input->post("OpinionStatusesFilter", true);
        $query = [];
        if (empty($this->ci->custom_field)) {
            $this->ci->load->model("custom_field", "custom_fieldfactory");
            $this->ci->custom_field = $this->ci->custom_fieldfactory->get_instance();
        }
        if (is_array($filter)) {
            if (isset($filter["filters"])) {
                foreach ($filter["filters"] as $_filter) {
                    if (!empty($_filter["filters"][0]["field"]) && in_array($_filter["filters"][0]["field"], ["opinions.effectiveEffort", "opinions.estimated_effort"])) {
                        $system_preferences = $this->ci->session->userdata("systemPreferences");
                        $this->ci->load->library("TimeMask");
                        $_filter["filters"][0]["value"] = $this->ci->timemask->humanReadableToHours($_filter["filters"][0]["value"]);
                    }
                    $this->prep_k_filter($_filter, $query, $filter["logic"], $hijri_calendar_enabled, true);
                }
                unset($_filter);
            }
            if (isset($filter["customFields"]) && !empty($filter["customFields"])) {
                $this->ci->custom_field->prep_custom_field_filters($this->modelName, $filter["customFields"], $query);
            }
        }
        if ($caseIdFilter) {
            $query["where"][] = ["legal_case_id", $this->ci->input->post("caseIdFilter", true)];
        }
        if ($opinionStatuses) {
            $query["where_in"][] = ["opinion_status_id", $opinionStatuses];
            $this->ci->load->model("user_preference");
            $this->ci->user_preference->set_value("opinionStatusesFilter", $opinionStatuses, true);
        } else {
            if (!isset($caseIdFilter) && !$with_user_preferences) {
                $this->ci->load->model("user_preference");
                $opinionStatusesSavedFilters = $this->ci->user_preference->get_value("opinionStatusesFilter");
                if ($opinionStatusesSavedFilters) {
                    $query["where_in"][] = ["opinion_status_id", $opinionStatusesSavedFilters];
                }
            }
        }
        $this->ci->load->model("legal_case", "legal_casefactory");
        $this->ci->legal_case = $this->ci->legal_casefactory->get_instance();
        $where_condition = $this->ci->legal_case->get_matter_privacy_conditions($this->logged_user_id, $this->override_privacy, false);
        $query["where"][] = ["(opinions.private IS NULL OR opinions.private = 'no' OR(opinions.private = 'yes' AND (" . "opinions.createdBy = '" . $this->logged_user_id . "' OR opinions.assigned_to = '" . $this->logged_user_id . "' OR " . "opinions.id IN (SELECT opinion_id FROM opinion_users WHERE user_id = '" . $this->logged_user_id . "') OR '" . $this->override_privacy . "' = 'yes'" . ")" . ") OR" . "(" . "opinions.legal_case_id IS NOT NULL AND " . "((opinions.private IS NULL OR opinions.private = 'no') AND " . $where_condition . ")" . ")" . ")"];
        if (isset($query["where"])) {
            $where = " WHERE ";
            foreach ($query["where"] as $key => $condition) {
                $where .= count($condition) == 2 ? $condition[0] . " " . "'" . $condition[1] . "'" : $condition[0];
                if (count($query["where"]) - 1 !== $key) {
                    $where .= " AND ";
                }
            }
        }
        if (isset($query["where_in"])) {
            $where = isset($where) ? $where . " AND " : " WHERE ";
            foreach ($query["where_in"] as $key => $condition) {
                $ids = implode("','", $condition[1]);
                $where .= count($condition) == 2 ? $condition[0] . " IN  " . "('" . $ids . "')" : $condition[0];
                if (count($query["where_in"]) - 1 !== $key) {
                    $where .= " AND ";
                }
            }
        }
        $this->ci->load->model("language");
        $langId = $this->ci->language->get_id_by_session_lang();
        $query["join"] = [["opinion_types_languages", "opinion_types_languages.opinion_type_id = opinions.opinion_type_id and opinion_types_languages.language_id = '" . $langId . "'", "left"], ["legal_cases", "legal_cases.id = opinions.legal_case_id", "left"], ["opinion_contributors", "opinion_contributors.opinion_id = opinions.id", "left"]];
        $this->prep_query($query);
        $this->ci->db->count_all_results($this->_table);
        $order_by = " ORDER BY ";
        if (is_array($sortable) && !empty($sortable)) {
            foreach ($sortable as $index => $_sort) {
                $order_by .= $_sort["field"] . " " . $_sort["dir"] . (count($sortable) - 1 !== $index ? ", " : "");
            }
        } else {
            $order_by .= "opinions.id desc";
        }
        $group_by_select = "SELECT opinions.id FROM opinions_detailed_view as opinions";
        $group_by_select .= " LEFT JOIN opinion_contributors ON opinion_contributors.opinion_id = opinions.id";
        $group_by_select .= " LEFT JOIN legal_cases ON legal_cases.id = opinions.legal_case_id";
        if (isset($where)) {
            $group_by_select .= $where;
        }
        $group_by_select .= " GROUP BY opinions.id";
        $temp_result = $this->ci->db->query($group_by_select)->result_array();
        $ids = [];
        foreach ($temp_result as $row) {
            array_push($ids, $row["id"]);
        }
        if (empty($ids)) {
            $ids = "0";
        } else {
            $ids = implode(", ", $ids);
        }
        $custom_fields_select = $this->ci->custom_field->load_grid_custom_fields($this->modelName, $_table);
        $select .= $custom_fields_select;
        $select .= " from opinions_detailed_view as opinions";
        $select .= " LEFT JOIN opinion_types_languages ON opinion_types_languages.opinion_type_id = opinions.opinion_type_id and opinion_types_languages.language_id = '" . $langId . "'";
        $select .= " WHERE opinions.id IN (" . $ids . ")";
        $select .= $order_by;
        if ($page_number != "") {
            $limit = [10000, ($page_number - 1) * 10000];
            $select .= " OFFSET  " . $limit[1] . " ROWS FETCH NEXT " . $limit[0] . " ROWS ONLY";
        }
        if ($limit = $this->ci->input->post("take", true)) {
            $select .= " OFFSET  " . $this->ci->input->post("skip", true) . " ROWS FETCH NEXT " . $limit . " ROWS ONLY";
        }
        $response["data"] = $this->ci->db->query($select)->result_array();
        $response["totalRows"] = count($response["data"]);
        $this->_table = $_table;
        return $response;
    }
	
	 public function k_load_all_cp_opinions($cp_userId,$filter, $sortable, $page_number = "", $with_user_preferences = false, $hijri_calendar_enabled = false)
    {
        $_table = $this->_table;
        $this->_table = "opinions_detailed_view as opinions";
        $select = "SELECT opinions.contract_name, opinions.contract_id, opinions.id, opinions.title, opinions.opinionId, opinions.caseId, opinions.legal_case_id, opinions.user_id, opinions.due_date, opinions.assignedToId, opinions.reportedById, opinions.private, opinions.priority, opinions.opinion_location_id, opinions.location, opinions.opinionFullDetailed_info, opinions.opinion_status_id, opinions.opinion_type_id, opinions.createdOn, opinions.modifiedOn, opinions.modifiedBy, opinions.archived, case when opinions.assignee_status='inactive' then ( opinions.assigned_to+ ' '+  ' ('+'" . $this->ci->lang->line("inactive") . "' +')') else opinions.assigned_to END as assigned_to, case when opinions.reporter_status='inactive' then ( opinions.reporter+ ' '+  ' ('+'" . $this->ci->lang->line("inactive") . "' +')') else opinions.reporter END as reporter, case when opinions.creator_status='inactive' then ( opinions.createdBy+ ' '+  ' ('+'" . $this->ci->lang->line("inactive") . "' +')') else opinions.createdBy END as createdBy, case when opinions.modifier_status='inactive' then ( opinions.modifiedByName+ ' '+  ' ('+'" . $this->ci->lang->line("inactive") . "' +')') else opinions.modifiedByName END as modifiedByName, opinions.createdById, opinions.opinionStatus, opinions.archivedOpinions, opinions.detailed_info, opinions.caseSubject, opinions.caseFullSubject, opinions.caseCategory, CAST( opinions.estimated_effort AS nvarchar ) as estimated_effort,CAST( opinions.effectiveEffort AS nvarchar ) as effectiveEffort, opinion_types_languages.name as opinionType, opinions.contributors as contributors";
        $response = [];
        $caseIdFilter = $this->ci->input->post("caseIdFilter", true);
        $opinionStatuses = $this->ci->input->post("OpinionStatusesFilter", true);
        $query = [];
        if (empty($this->ci->custom_field)) {
            $this->ci->load->model("custom_field", "custom_fieldfactory");
            $this->ci->custom_field = $this->ci->custom_fieldfactory->get_instance();
        }

        if (is_array($filter)) {
            if (isset($filter["filters"])) {
                foreach ($filter["filters"] as $_filter) {
                    if (!empty($_filter["filters"][0]["field"]) && in_array($_filter["filters"][0]["field"], ["opinions.effectiveEffort", "opinions.estimated_effort"])) {
                        $system_preferences = $this->ci->session->userdata("systemPreferences");
                        $this->ci->load->library("TimeMask");
                        $_filter["filters"][0]["value"] = $this->ci->timemask->humanReadableToHours($_filter["filters"][0]["value"]);
                    }
                    $this->prep_k_filter($_filter, $query, $filter["logic"], $hijri_calendar_enabled, true);
                }
                unset($_filter);
            }
            if (isset($filter["customFields"]) && !empty($filter["customFields"])) {
                $this->ci->custom_field->prep_custom_field_filters($this->modelName, $filter["customFields"], $query);
            }
        }
        if ($caseIdFilter) {
            $query["where"][] = ["legal_case_id", $this->ci->input->post("caseIdFilter", true)];
        }
        if ($opinionStatuses) {
            $query["where_in"][] = ["opinion_status_id", $opinionStatuses];
           
        }
        if ($cp_userId) {
            $query["where"][] = [ "opinions.channel=", "CP"];
            $query["where"][] = [ "opinions.requester=",$cp_userId];
            $query["where"][] = [ "opinions.is_visible_to_cp=", 1];
        }

		
        $this->ci->load->model("legal_case", "legal_casefactory");
        $this->ci->legal_case = $this->ci->legal_casefactory->get_instance();
        $where_condition = $this->ci->legal_case->get_matter_privacy_conditions($this->logged_user_id, $this->override_privacy, false);
        $query["where"][] = ["(opinions.private IS NULL OR opinions.private = 'no' OR(opinions.private = 'yes' AND (" . "opinions.createdBy = '" . $this->logged_user_id . "' OR opinions.assigned_to = '" . $this->logged_user_id . "' OR " . "opinions.id IN (SELECT opinion_id FROM opinion_users WHERE user_id = '" . $this->logged_user_id . "') OR '" . $this->override_privacy . "' = 'yes'" . ")" . ") OR" . "(" . "opinions.legal_case_id IS NOT NULL AND " . "((opinions.private IS NULL OR opinions.private = 'no') AND " . $where_condition . ")" . ")" . ")"];
        if (isset($query["where"])) {
            $where = " WHERE ";
            foreach ($query["where"] as $key => $condition) {
                $where .= count($condition) == 2 ? $condition[0] . " " . "'" . $condition[1] . "'" : $condition[0];
                if (count($query["where"]) - 1 !== $key) {
                    $where .= " AND ";
                }
            }
        }
        if (isset($query["where_in"])) {
            $where = isset($where) ? $where . " AND " : " WHERE ";
            foreach ($query["where_in"] as $key => $condition) {
                $ids = implode("','", $condition[1]);
                $where .= count($condition) == 2 ? $condition[0] . " IN  " . "('" . $ids . "')" : $condition[0];
                if (count($query["where_in"]) - 1 !== $key) {
                    $where .= " AND ";
                }
            }
        }
        $this->ci->load->model("language");
        $langId = $this->ci->language->get_id_by_session_lang();
        $query["join"] = [["opinion_types_languages", "opinion_types_languages.opinion_type_id = opinions.opinion_type_id and opinion_types_languages.language_id = '" . $langId . "'", "left"], ["legal_cases", "legal_cases.id = opinions.legal_case_id", "left"], ["opinion_contributors", "opinion_contributors.opinion_id = opinions.id", "left"]];
        $this->prep_query($query);
        $this->ci->db->count_all_results($this->_table);
        $order_by = " ORDER BY ";
        if (is_array($sortable) && !empty($sortable)) {
            foreach ($sortable as $index => $_sort) {
                $order_by .= $_sort["field"] . " " . $_sort["dir"] . (count($sortable) - 1 !== $index ? ", " : "");
            }
        } else {
            $order_by .= "opinions.id desc";
        }
        $group_by_select = "SELECT opinions.id FROM opinions_detailed_view as opinions";
        $group_by_select .= " LEFT JOIN opinion_contributors ON opinion_contributors.opinion_id = opinions.id";
        $group_by_select .= " LEFT JOIN legal_cases ON legal_cases.id = opinions.legal_case_id";
        if (isset($where)) {
            $group_by_select .= $where;
        }
        $group_by_select .= " GROUP BY opinions.id";
        $temp_result = $this->ci->db->query($group_by_select)->result_array();
        $ids = [];
        foreach ($temp_result as $row) {
            array_push($ids, $row["id"]);
        }
        if (empty($ids)) {
            $ids = "0";
        } else {
            $ids = implode(", ", $ids);
        }
        $custom_fields_select = $this->ci->custom_field->load_grid_custom_fields($this->modelName, $_table);
        $select .= $custom_fields_select;
        $select .= " from opinions_detailed_view as opinions";
        $select .= " LEFT JOIN opinion_types_languages ON opinion_types_languages.opinion_type_id = opinions.opinion_type_id and opinion_types_languages.language_id = '" . $langId . "'";
        $select .= " WHERE opinions.id IN (" . $ids . ")";
        $select .= $order_by;
        if ($page_number != "") {
            $limit = [10000, ($page_number - 1) * 10000];
            $select .= " OFFSET  " . $limit[1] . " ROWS FETCH NEXT " . $limit[0] . " ROWS ONLY";
        }
        if ($limit = $this->ci->input->post("take", true)) {
            $select .= " OFFSET  " . $this->ci->input->post("skip", true) . " ROWS FETCH NEXT " . $limit . " ROWS ONLY";
        }
        $response = $this->ci->db->query($select)->result_array();
      //  $response["totalRows"] = count($response["data"]);
        $this->_table = $_table;
      return $response;
    }
   
   public function load_daily_agenda_opinions()
    {
        $query = [];
        $query["select"] = ["opinions.id, opinions.title, opinions.detailed_info, opinions.legal_case_id as case_id, users.email, opinion_statuses.category", false];
        $query["join"] = [["users", "users.id = opinions.assigned_to"], ["opinion_statuses", "opinion_statuses.id = opinions.opinion_status_id", "left"], ["legal_cases lc", "lc.id = opinions.legal_case_id", "left"]];
        $query["where"][] = ["opinions.archived = 'no' AND CONVERT(varchar, getdate(), 23) = opinions.due_date AND opinions.opinion_status_id NOT IN (SELECT id from opinion_statuses WHERE category = 'done')", NULL, false];
        $query["where"][] = ["opinions.legal_case_id is null or lc.isDeleted = 0", NULL, false];
        return $this->load_all($query);
    }
    public function opinions_per_assignee()
    {
        $this->ci->load->model("language");
        $langId = $this->ci->language->get_id_by_session_lang();
        $query["select"] = ["opinions.*, ( '" . $this->modelCode . "' + CAST( opinions.id AS nvarchar ) ) as opinionId,\r\nCASE WHEN opinions.legal_case_id IS NULL THEN '' ELSE ('" . $this->ci->legal_case->get("modelCode") . "' + CAST( opinions.legal_case_id AS nvarchar ) ) END as caseId,legal_cases.category as caseCategory, legal_cases.subject as case_subject, opinion_types_languages.name as opinionType, ts.name as opinionStatus,\r\nSUBSTRING( opinions.detailed_info, " . $this->detailed_infoSubstringStartingPosition . ", " . $this->detailed_infoSubstringLength . " ) as detailed_info", false];
        $query["join"] = [["opinion_types_languages", "opinion_types_languages.opinion_type_id = opinions.opinion_type_id and opinion_types_languages.language_id = '" . $langId . "'", "left"], ["opinion_statuses ts", "ts.id = opinions.opinion_status_id", "left"], ["legal_cases", "legal_cases.id = opinions.legal_case_id", "left"]];
        $query["where"] = [["opinions.assigned_to", $this->ci->is_auth->get_user_id()], ["opinions.archived", "no"]];
        $where_condition = $this->ci->legal_case->get_matter_privacy_conditions($this->logged_user_id, $this->override_privacy, false);
        $query["where"][] = ["(opinions.private IS NULL OR opinions.private = 'no' OR(opinions.private = 'yes' AND (" . "opinions.createdBy = '" . $this->logged_user_id . "' OR opinions.assigned_to = '" . $this->logged_user_id . "' OR " . "opinions.id IN (SELECT opinion_id FROM opinion_users WHERE user_id = '" . $this->logged_user_id . "') OR '" . $this->override_privacy . "' = 'yes'" . ")" . ") OR" . "(" . "opinions.legal_case_id IS NOT NULL AND " . "((opinions.private IS NULL OR opinions.private = 'no') AND " . $where_condition . ")" . ")" . ")"];
        $query["order_by"] = ["opinions.id desc"];
        return parent::load_all($query);
    }
    public function user_todays_opinions($is_api = false)
    {
        $this->ci->load->model("language");
        if ($is_api) {
            $user_id = $this->ci->user_logged_in_data["user_id"];
            $this->ci->load->model("user_preference");
            $this->ci->user_preference->fetch(["user_id" => $user_id, "keyName" => "language"]);
            $language = $this->ci->user_preference->get_field("keyValue");
            $this->ci->language->fetch(["fullName" => $language]);
            $langId = $this->ci->language->get_field("id");
            $this->ci->user_profile->fetch(["user_id" => $user_id]);
            $override_privacy = $this->ci->user_profile->get_field("overridePrivacy");
        } else {
            $user_id = $this->logged_user_id;
            $langId = $this->ci->language->get_id_by_session_lang();
            $override_privacy = $this->override_privacy;
        }
        $query["select"] = ["opinions.id, opinions.title, opinions.priority, opinions.detailed_info, ( '" . $this->modelCode . "' + CAST( opinions.id AS nvarchar ) ) as opinion_id, opinion_types_languages.name as opinion_type, ts.name as opinion_status, ts.category as status_category", false];
        $query["join"] = [["opinion_types_languages", "opinion_types_languages.opinion_type_id = opinions.opinion_type_id and opinion_types_languages.language_id = '" . $langId . "'", "left"], ["opinion_statuses ts", "ts.id = opinions.opinion_status_id", "left"], ["legal_cases", "legal_cases.id = opinions.legal_case_id", "left"]];
        $query["where"] = [["opinions.assigned_to", $user_id], ["opinions.archived", "no"]];
        $this->ci->load->model("legal_case", "legal_casefactory");
        $this->ci->legal_case = $this->ci->legal_casefactory->get_instance();
        $where_condition = $this->ci->legal_case->get_matter_privacy_conditions($user_id, $override_privacy, false);
        $query["where"][] = ["(opinions.private IS NULL OR opinions.private = 'no' OR(opinions.private = 'yes' AND (" . "opinions.createdBy = '" . $user_id . "' OR opinions.assigned_to = '" . $user_id . "' OR " . "opinions.id IN (SELECT opinion_id FROM opinion_users WHERE user_id = '" . $user_id . "') OR '" . $override_privacy . "' = 'yes'" . ")" . ") OR" . "(" . "opinions.legal_case_id IS NOT NULL AND " . "((opinions.private IS NULL OR opinions.private = 'no') AND " . $where_condition . ")" . ")" . ")"];
        $query["order_by"] = ["opinions.id desc"];
        return parent::load_all($query);
    }
    public function opinions_per_reporter()
    {
        $this->ci->load->model("language");
        $langId = $this->ci->language->get_id_by_session_lang();
        $query["select"] = ["opinions.*, ( '" . $this->modelCode . "' + CAST( opinions.id AS nvarchar ) ) as opinionId, CASE WHEN opinions.legal_case_id IS NULL \r\n                            THEN '' ELSE ('" . $this->ci->legal_case->get("modelCode") . "' + CAST( opinions.legal_case_id AS nvarchar ) ) END as caseId,\r\n                            legal_cases.category as caseCategory, legal_cases.subject as case_subject, opinion_types_languages.name as opinionType, ts.name as opinionStatus,\r\n                            SUBSTRING( opinions.detailed_info, " . $this->detailed_infoSubstringStartingPosition . ", " . $this->detailed_infoSubstringLength . " ) as detailed_info", false];
        $query["join"] = [["opinion_types_languages", "opinion_types_languages.opinion_type_id = opinions.opinion_type_id and opinion_types_languages.language_id = '" . $langId . "'", "left"], ["opinion_statuses ts", "ts.id = opinions.opinion_status_id", "left"], ["legal_cases", "legal_cases.id = opinions.legal_case_id", "left"]];
        $query["where"] = [["opinions.reporter", $this->logged_user_id], ["opinions.archived", "no"]];
        $this->ci->load->model("legal_case", "legal_casefactory");
        $this->ci->legal_case = $this->ci->legal_casefactory->get_instance();
        $where_condition = $this->ci->legal_case->get_matter_privacy_conditions($this->logged_user_id, $this->override_privacy, false);
        $query["where"][] = ["(opinions.private IS NULL OR opinions.private = 'no' OR(opinions.private = 'yes' AND (" . "opinions.createdBy = '" . $this->logged_user_id . "' OR opinions.assigned_to = '" . $this->logged_user_id . "' OR " . "opinions.id IN (SELECT opinion_id FROM opinion_users WHERE user_id = '" . $this->logged_user_id . "') OR '" . $this->override_privacy . "' = 'yes'" . ")" . ") OR" . "(" . "opinions.legal_case_id IS NOT NULL AND " . "((opinions.private IS NULL OR opinions.private = 'no') AND " . $where_condition . ")" . ")" . ")"];
        $query["where"][] = ["opinions.due_date >=", date("Y-m-d")];
        $query["order_by"] = ["opinions.due_date asc"];
        return parent::load_all($query);
    }
    public function load_case_opinions($legal_case_id, $auth_lang = false)
    {
        $query = [];
        $this->ci->load->model("language");
        $langId = $this->ci->language->get_id_by_session_lang($auth_lang);
        $query["select"] = ["opinions.*, ( '" . $this->modelCode . "' + CAST( opinions.id AS nvarchar ) ) as opinionId, opinion_types_languages.name as opinionType, ts.name as opinionStatus, ( assigned.firstName + ' ' + assigned.lastName) as assigned_to", false];
        $query["join"] = [["opinion_types_languages", "opinion_types_languages.opinion_type_id = opinions.opinion_type_id and opinion_types_languages.language_id = '" . $langId . "'", "left"], ["opinion_statuses ts", "ts.id = opinions.opinion_status_id", "left"], ["user_profiles assigned", "assigned.user_id = opinions.assigned_to", "left"], ["legal_cases", "legal_cases.id = opinions.legal_case_id", "left"]];
        $this->ci->load->model("legal_case", "legal_casefactory");
        $this->ci->legal_case = $this->ci->legal_casefactory->get_instance();
        $where_condition = $this->ci->legal_case->get_matter_privacy_conditions($this->logged_user_id, $this->override_privacy, false);
        $query["where"][] = ["legal_case_id", $legal_case_id];
        $query["where"][] = ["(opinions.private IS NULL OR opinions.private = 'no' OR(opinions.private = 'yes' AND (" . "opinions.createdBy = '" . $this->logged_user_id . "' OR opinions.assigned_to = '" . $this->logged_user_id . "' OR " . "opinions.id IN (SELECT opinion_id FROM opinion_users WHERE user_id = '" . $this->logged_user_id . "') OR '" . $this->override_privacy . "' = 'yes'" . ")" . ") OR" . "(" . "opinions.legal_case_id IS NOT NULL AND " . "((opinions.private IS NULL OR opinions.private = 'no') AND " . $where_condition . ")" . ")" . ")"];
        $query["order_by"] = ["opinions.id desc"];
        return $this->load_all($query);
    }
    public function load_opinion($id)
    {
        $query = [];
        $query["select"] = ["opinions.*,opinions.createdBy as createdById ,tee.effectiveEffort, opinions.id as OpinionId, opinions.assigned_to as assignedToId, opinions.reporter as reporterById,  types.name as type,opinion_statuses.name as status, (assigned.firstName + ' ' + assigned.lastName) as assignee_fullname,(req.firstName + ' ' + req.lastName) as requestedBy, (reporter.firstName + ' ' + reporter.lastName) as reporter_fullname, opinions.due_date, opinions.detailed_info, ( created.firstName + ' ' + created.lastName ) as createdBy, ( modified.firstName + ' ' + modified.lastName ) as modifiedByName, legal_cases.subject as caseSubject, legal_cases.category as caseCategory, opinion_locations.name AS location, (reporter.firstName + ' ' + reporter.lastName) as reporter_fullname, assigned.status as assignee_status, reporter.status as reporter_status, contract.name as contract_name ,legal_cases.id as opinion_legal_case, legal_cases.client_id as clientId", false];
        $query["where"][] = ["opinions.id", $id];
        $this->ci->load->model("legal_case", "legal_casefactory");
        $this->ci->legal_case = $this->ci->legal_casefactory->get_instance();
        $where_condition = $this->ci->legal_case->get_matter_privacy_conditions($this->logged_user_id, $this->override_privacy, false);
        $query["where"][] = ["(opinions.private IS NULL OR opinions.private = 'no' OR(opinions.private = 'yes' AND (" . "opinions.createdBy = '" . $this->logged_user_id . "' OR opinions.assigned_to = '" . $this->logged_user_id . "' OR " . "opinions.id IN (SELECT opinion_id FROM opinion_users WHERE user_id = '" . $this->logged_user_id . "') OR '" . $this->override_privacy . "' = 'yes'" . ")" . ") OR" . "(" . "opinions.legal_case_id IS NOT NULL AND " . "((opinions.private IS NULL OR opinions.private = 'no') AND " . $where_condition . ")" . ")" . ")"];
        $this->ci->load->model("language");
        $lang_id = $this->ci->language->get_id_by_session_lang();
        $query["join"] = [["legal_cases", "legal_cases.id = opinions.legal_case_id", "left"], ["contract", "contract.id = opinions.contract_id", "left"], ["user_profiles req", "req.user_id = opinions.requester", "left"],["user_profiles assigned", "assigned.user_id = opinions.assigned_to", "left"], ["user_profiles reporter", "reporter.user_id = opinions.reporter", "left"], ["user_profiles created", "created.user_id = opinions.createdBy", "left"], ["user_profiles modified", "modified.user_id = opinions.modifiedBy", "left"], ["opinion_effective_effort AS tee", "tee.opinion_id = opinions.id", "left"], ["opinion_locations", "opinion_locations.id = opinions.opinion_location_id", "left"], ["opinion_statuses", "opinion_statuses.id = opinions.opinion_status_id", "left"], ["opinion_types_languages as types", "types.opinion_type_id = opinions.opinion_type_id and types.language_id = '" . $lang_id . "'", "left"]];
        return $this->ci->opinion->load($query);
    }
    public function universal_search($q, $pagingOn = true)
    {
        if (!isset($this->ci->legal_case) || !is_object($this->ci->legal_case)) {
            $this->ci->load->model("legal_case", "legal_casefactory");
            $this->ci->legal_case = $this->ci->legal_casefactory->get_instance();
        }
        $this->ci->load->model("language");
        $langId = $this->ci->language->get_id_by_session_lang();
        $q2 = $this->escape_universal_search_keyword($q);
        $query["select"] = ["opinions.id, opinions.legal_case_id, opinions.user_id, opinions.due_date, opinions.private, opinions.priority, opinions.opinion_location_id, opinions.opinion_status_id, opinions.opinion_type_id, opinions.estimated_effort, opinions.createdOn, opinions.createdBy, opinions.modifiedOn, opinions.modifiedBy, opinions.archived, opinions.reporter, ( '" . $this->modelCode . "' + CAST( opinions.id AS nvarchar ) ) as opinionId, ( '" . $this->ci->legal_case->get("modelCode") . "' + CAST( opinions.legal_case_id AS nvarchar ) ) as caseId, opinion_types_languages.name as opinionType, ts.name as opinionStatus, SUBSTRING( opinions.detailed_info, " . $this->detailed_infoSubstringStartingPosition . ", " . $this->detailed_infoSubstringLength . " ) as detailed_info, opinions.title, (assigned.firstName + ' ' + assigned.lastName) as assigned_to, legal_cases.category as case_category", false];
        $query["join"] = [["opinion_types_languages", "opinion_types_languages.opinion_type_id = opinions.opinion_type_id and opinion_types_languages.language_id = '" . $langId . "'", "left"], ["opinion_statuses ts", "ts.id = opinions.opinion_status_id", "left"], ["legal_cases", "legal_cases.id = opinions.legal_case_id", "left"], ["user_profiles assigned", "assigned.user_id = opinions.assigned_to", "left"]];
        $this->ci->load->model("legal_case", "legal_casefactory");
        $this->ci->legal_case = $this->ci->legal_casefactory->get_instance();
        $where_condition = $this->ci->legal_case->get_matter_privacy_conditions($this->logged_user_id, $this->override_privacy, false);
        $query["where"][] = ["(opinions.title LIKE '%" . $q2 . "%' OR opinions.detailed_info LIKE '%" . $q2 . "%' escape '\\' OR opinions.id = CASE WHEN SUBSTRING('" . $q2 . "', 1, 1) = '" . $this->modelCode . "' THEN (CASE WHEN ISNUMERIC(SUBSTRING('" . $q2 . "', 2, 9)) = 1 THEN SUBSTRING('" . $q2 . "', 2, 9) ELSE 0 END) ELSE (CASE WHEN ISNUMERIC('" . $q2 . "') = 1 THEN CAST('" . $q2 . "' as bigint) ELSE 0 END) END)"];
        $query["where"][] = ["(opinions.private IS NULL OR opinions.private = 'no' OR(opinions.private = 'yes' AND (" . "opinions.createdBy = '" . $this->logged_user_id . "' OR opinions.assigned_to = '" . $this->logged_user_id . "' OR " . "opinions.id IN (SELECT opinion_id FROM opinion_users WHERE user_id = '" . $this->logged_user_id . "') OR '" . $this->override_privacy . "' = 'yes'" . ")" . ") OR" . "(" . "opinions.legal_case_id IS NOT NULL AND " . "((opinions.private IS NULL OR opinions.private = 'no') AND " . $where_condition . ")" . ")" . ")"];
        $query["order_by"] = ["opinions.id desc"];
        return $pagingOn ? parent::paginate($query, ["urlPrefix" => ""]) : parent::load_all($query);
    }
    public function load_opinion_users($opinion_id)
    {
        $table = $this->_table;
        $this->_table = "opinion_users";
        $query["select"] = ["UP.user_id as id, ( UP.firstName + ' ' + UP.lastName ) as name,UP.status as status", false];
        $query["join"] = ["user_profiles UP", "UP.user_id = opinion_users.user_id", "inner"];
        $query["where"] = ["opinion_users.opinion_id", $opinion_id];
        $response = $this->load_all($query);
        $this->_table = $table;
        return $response;
    }
    public function lookup($term)
    {
        $configList = ["key" => "opinions.id", "value" => "name"];
        $configQury["select"][] = ["opinions.*, ('" . $this->modelCode . "' + CAST( opinions.id AS nvarchar )) as opinion_id, CASE WHEN Datalength(opinions.detailed_info) > 45 THEN (SUBSTRING(opinions.detailed_info, 1, 45) + ' ' + '...' ) ELSE opinions.detailed_info END as detailed_info, legal_cases.category, (SUBSTRING(legal_cases.subject, 1, 72) + '...') as caseSubject, clients_view.id as client_id, clients_view.name as client_name, ( user_profiles.firstName + ' ' + user_profiles.lastName ) as createdByName,( modified.firstName + ' ' + modified.lastName ) AS modifiedByName, ts.name as opinionStatus", false];
        $configQury["join"] = [["legal_cases", "legal_cases.id = opinions.legal_case_id", "left"], ["user_profiles", "user_profiles.user_id = opinions.createdBy", "left"], ["user_profiles modified", "modified.user_id = opinions.modifiedBy", "left"], ["opinion_statuses as ts", "ts.id = opinions.opinion_status_id", "left"], ["clients_view", "clients_view.id = legal_cases.client_id AND clients_view.model = 'clients'", "left"]];
        $this->ci->load->model("legal_case", "legal_casefactory");
        $this->ci->legal_case = $this->ci->legal_casefactory->get_instance();
        $where_condition = $this->ci->legal_case->get_matter_privacy_conditions($this->logged_user_id, $this->override_privacy, false);
        $configQury["where"][] = ["(opinions.private IS NULL OR opinions.private = 'no' OR(opinions.private = 'yes' AND (" . "opinions.createdBy = '" . $this->logged_user_id . "' OR opinions.assigned_to = '" . $this->logged_user_id . "' OR " . "opinions.id IN (SELECT opinion_id FROM opinion_users WHERE user_id = '" . $this->logged_user_id . "') OR '" . $this->override_privacy . "' = 'yes'" . ")" . ") OR" . "(" . "opinions.legal_case_id IS NOT NULL AND " . "((opinions.private IS NULL OR opinions.private = 'no') AND " . $where_condition . ")" . ")" . ")"];
        if (!empty($term)) {
            $modelCode = substr($term, 0, 1);
            $opinion_id = substr($term, 1);
            if (strcasecmp($modelCode, $this->get("modelCode")) === 0 && is_numeric($opinion_id)) {
                $configQury["where"][] = ["opinions.id = " . $opinion_id, NULL, false];
            } else {
                $term = $this->ci->db->escape_like_str($term);
                $configQury["where"][] = ["opinions.detailed_info LIKE '%" . $term . "%' OR opinions.title LIKE '%" . $term . "%'", NULL, false];
            }
        }
        if ($moreFilters = $this->ci->input->get("more_filters")) {
            foreach ($moreFilters as $_field => $_term) {
                $configQury["where"][] = [$_field, $_term];
            }
        }
        $this->ci->load->model("user_group_permission");
        $user_permission = $this->ci->user_group_permission->get_permission_data($this->ci->session->userdata("AUTH_user_group_id"));
        if (is_array($user_permission) && array_key_exists("core", $user_permission)) {
            $user_core_permissions = $user_permission["core"];
            if (count($user_core_permissions) != 1 && !in_array("/opinions/all_opinions/", $user_core_permissions)) {
                $configQury["where"][] = ["opinions.assigned_to = " . $this->ci->session->userdata("AUTH_user_id"), NULL, false];
            }
        }
        return $this->load_all($configQury, $configList);
    }
    public function api_load_opinion($id, $user_id, $overridePrivacy)
    {
        $query = [];
        $query["select"] = ["opinions.*, tee.effectiveEffort, CASE WHEN assigned.status='inactive' THEN  (assigned.firstName + ' ' + assigned.lastName+' (Inactive)') ELSE (assigned.firstName + ' ' + assigned.lastName) END as assignedToLookUp, CASE WHEN reporter.status='inactive' THEN (reporter.firstName + ' ' + reporter.lastName + ' (Inactive)') ELSE (reporter.firstName + ' ' + reporter.lastName) END as reporterLookUp, ( created.firstName + ' ' + created.lastName ) as createdBy, legal_cases.subject as caseSubject, legal_cases.category as caseCategory, opinion_locations.name AS location, contract.name as contract_name", false];
        $query["where"][] = ["opinions.id", $id];
        $this->ci->load->model("legal_case", "legal_casefactory");
        $this->ci->legal_case = $this->ci->legal_casefactory->get_instance();
        $where_condition = $this->ci->legal_case->get_matter_privacy_conditions($user_id, $overridePrivacy, false);
        $query["where"][] = ["(opinions.private IS NULL OR opinions.private = 'no' OR(opinions.private = 'yes' AND (" . "opinions.createdBy = '" . $user_id . "' OR opinions.assigned_to = '" . $user_id . "' OR " . "opinions.id IN (SELECT opinion_id FROM opinion_users WHERE user_id = '" . $user_id . "') OR '" . $overridePrivacy . "' = 'yes'" . ")" . ") OR" . "(" . "opinions.legal_case_id IS NOT NULL AND " . "((opinions.private IS NULL OR opinions.private = 'no') AND " . $where_condition . ")" . ")" . ")"];
        $query["join"] = [["legal_cases", "legal_cases.id = opinions.legal_case_id", "left"], ["contract", "contract.id = opinions.contract_id", "left"], ["user_profiles assigned", "assigned.user_id = opinions.assigned_to", "left"], ["user_profiles created", "created.user_id = opinions.createdBy", "left"], ["opinion_effective_effort AS tee", "tee.opinion_id = opinions.id", "left"], ["opinion_locations", "opinion_locations.id = opinions.opinion_location_id", "left"], ["user_profiles reporter", "reporter.user_id = opinions.reporter", "left"]];
        return $this->ci->opinion->load($query);
    }
    public function load_opinion_contributors($id)
    {
        $table = $this->_table;
        $this->_table = "opinion_contributors";
        $query["select"] = ["UP.user_id as id, ( UP.firstName + ' ' + UP.lastName ) as name,UP.status as status", false];
        $query["join"] = ["user_profiles UP", "UP.user_id = opinion_contributors.user_id", "inner"];
        $query["where"] = ["opinion_contributors.opinion_id", $id];
        $response = $this->load_all($query);
        $this->_table = $table;
        return $response;
    }
    public function roll_session($filter, $sortable, $export = false, &$query = [], &$stringQuery = "")
    {
        $_table = $this->_table;
        $this->_table = "opinions_detailed_view AS opinions";
        $query = ["select" => "opinions.id"];
        if (is_array($filter) && isset($filter["filters"])) {
            foreach ($filter["filters"] as $_filter) {
                $this->prep_k_filter($_filter, $query, $filter["logic"]);
            }
            unset($_filter);
        }
        $this->ci->load->model("system_configuration");
        $excluded_statuses = $this->ci->system_configuration->get_value_by_key("opinionReportExcludedStatuses");
        if (!empty($excluded_statuses)) {
            $query["where_not_in"][] = ["opinions.opinion_status_id", $excluded_statuses];
        }
        $fetch_only_opinions_related_to_matters = $this->ci->system_configuration->get_value_by_key("opinionReportFetchOnlyRelatedToMatters");
        if ($fetch_only_opinions_related_to_matters == "yes") {
            $query["where"][] = ["opinions.legal_case_id is not null", NULL, false];
        }
        $this->ci->load->model("language");
        $langId = $this->ci->language->get_id_by_session_lang();
        $query["group_by"] = ["opinions.id, opinions.legal_case_id"];
        $ids = $this->load_all($query, "query");
        $this->_table = "opinions";
        $query = [];
        $response = [];
        $query["select"] = ["opinions.id, opinions.title, opinions.detailed_info as opinionFullDetailed_info,opinions.assigned_to,opinions.opinion_type_id, opinions.opinion_status_id,opinions.legal_case_id,CASE WHEN opinions.legal_case_id IS NULL THEN '' ELSE ('M' + CAST(opinions.legal_case_id AS nvarchar)) END as caseId,(assigned.firstName + ' ' + assigned.lastName) as assigned_to_name, ts.name as opinionStatus,opinions.reporter,stages.name as stage_name, CASE WHEN clients.company_id IS NULL THEN (CASE WHEN cont.father!='' THEN cont.firstName + ' ' + cont.father + ' ' + cont.lastName ELSE cont.firstName+' '+cont.lastName END) ELSE comp.name END AS client,CASE WHEN clients.company_id IS NULL THEN cont.foreignFirstName + ' ' + cont.foreignLastName ELSE comp.foreignName END as client_foreign_name, court_types.name as court_type,court_degrees.name as court_degree,court_regions.name as court_region,courts.name as court, opinions.createdOn as createdOn,legal_case_litigation_details.id AS stage_id, legal_cases.category as case_category, legal_cases.subject as case_subject, " . "opponents = STUFF(\r\n\t\t\t(SELECT ', ' +\r\n\t\t\t(\r\n                            CASE WHEN opponent_positions.name IS NOT NULL\r\n                                THEN\r\n                                    CASE WHEN legal_case_opponents.opponent_member_type = 'company'\r\n                                        THEN\r\n                                            (opponentCompany.name + ' - ' + opponent_positions.name)\r\n                                        ELSE\r\n                                            opponentContact.firstName + ' ' + opponentContact.lastName + ' - ' + opponent_positions.name\r\n                                    END\r\n                                ELSE\r\n                                    CASE WHEN legal_case_opponents.opponent_member_type = 'company'\r\n                                        THEN\r\n                                            opponentCompany.name\r\n                                        ELSE\r\n                                            opponentContact.firstName + ' ' + opponentContact.lastName\r\n                                    END\r\n                            END\r\n\t\t\t)\r\n\t\t\t FROM legal_case_opponents\r\n\t\t\t INNER JOIN opponents ON opponents.id = legal_case_opponents.opponent_id\r\n\t\t\t LEFT JOIN companies AS opponentCompany ON opponentCompany.id = opponents.company_id AND legal_case_opponents.opponent_member_type = 'company'\r\n\t\t\t LEFT JOIN contacts AS opponentContact ON opponentContact.id = opponents.contact_id AND legal_case_opponents.opponent_member_type = 'contact'\r\n                         LEFT JOIN legal_case_opponent_position_languages AS opponent_positions ON opponent_positions.legal_case_opponent_position_id = legal_case_opponents.opponent_position and opponent_positions.language_id = '" . $langId . "'\r\n\t\t\t WHERE legal_case_opponents.case_id = legal_cases.id\r\n\t\t\tFOR XML PATH('')), 1, 1, ''),\r\n            opponent_foreign_name = STUFF((SELECT ', ' +\r\n                (\r\n                    CASE WHEN opponent_positions.name != '' THEN\r\n                    (\r\n                        CASE WHEN legal_case_opponents.opponent_member_type = 'company'\r\n                        THEN (CASE WHEN opponentCompany.foreignName IS NULL THEN opponentCompany.name ELSE opponentCompany.foreignName END) + ' - ' + opponent_positions.name\r\n                        ELSE (CASE WHEN opponentContact.foreignFirstName IS NULL THEN opponentContact.firstName ELSE opponentContact.foreignFirstName END) + ' ' + (CASE WHEN opponentContact.foreignLastName IS NULL THEN opponentContact.lastName ELSE opponentContact.foreignLastName END) + ' - ' + opponent_positions.name END\r\n                    )\r\n                    ELSE\r\n                    (\r\n                        CASE WHEN legal_case_opponents.opponent_member_type = 'company'\r\n                        THEN (CASE WHEN opponentCompany.foreignName IS NULL THEN opponentCompany.name ELSE opponentCompany.foreignName END)\r\n                        ELSE (CASE WHEN opponentContact.foreignFirstName IS NULL THEN opponentContact.firstName ELSE opponentContact.foreignFirstName END) + ' ' + (CASE WHEN opponentContact.foreignLastName IS NULL THEN opponentContact.lastName ELSE opponentContact.foreignLastName END) END\r\n                    )\r\n                    END\r\n                )\r\n                 FROM legal_case_opponents\r\n                 INNER JOIN opponents ON opponents.id = legal_case_opponents.opponent_id\r\n                 LEFT JOIN companies AS opponentCompany ON opponentCompany.id = opponents.company_id AND legal_case_opponents.opponent_member_type = 'company'\r\n                 LEFT JOIN contacts AS opponentContact ON opponentContact.id = opponents.contact_id AND legal_case_opponents.opponent_member_type = 'contact'\r\n                 LEFT JOIN legal_case_opponent_position_languages AS opponent_positions ON opponent_positions.legal_case_opponent_position_id = legal_case_opponents.opponent_position and opponent_positions.language_id = '" . $langId . "'\r\n                 WHERE legal_case_opponents.case_id = legal_cases.id\r\n                FOR XML PATH('')), 1, 1, '')," . "stage_ext_reference = STUFF((SELECT ' ; ' + lcler.number FROM legal_case_litigation_external_references lcler WHERE lcler.stage=legal_case_litigation_details.id FOR XML PATH('')), 1, 3, '')," . "containerID = STUFF((SELECT ' ; ' + lccfd.containerId FROM legal_case_containers_full_details as lccfd where lccfd.legal_case_id = opinions.legal_case_id FOR XML PATH('')), 1, 3, '')," . "opinions.due_date,opinion_types_languages.name as opinionType", false];
        $query["where"][] = ["opinions.id IN (" . $ids . ")", NULL, false];
        $query["join"] = [["user_profiles as assigned", "assigned.user_id = opinions.assigned_to", "left"], ["opinion_statuses as ts", "ts.id = opinions.opinion_status_id", "left"], ["legal_cases", "legal_cases.id = opinions.legal_case_id", "left"], ["legal_case_litigation_details", "legal_case_litigation_details.id = opinions.stage", "left"], ["legal_case_stages", "legal_case_stages.id = legal_case_litigation_details.legal_case_stage", "left"], ["legal_case_stage_languages as stages", "stages.legal_case_stage_id = legal_case_stages.id and stages.language_id = '" . $langId . "'", "left"], ["opinion_types_languages", "opinion_types_languages.opinion_type_id = opinions.opinion_type_id and opinion_types_languages.language_id = '" . $langId . "'", "left"], ["courts", "courts.id = legal_case_litigation_details.court_id", "left"], ["court_types", "court_types.id = legal_case_litigation_details.court_type_id", "left"], ["court_degrees", "court_degrees.id = legal_case_litigation_details.court_degree_id", "left"], ["court_regions", "court_regions.id = legal_case_litigation_details.court_region_id", "left"], ["clients", "clients.id = legal_cases.client_id", "left"], ["companies comp", "comp.id = clients.company_id", "left"], ["contacts cont", "cont.id = clients.contact_id", "left"]];
        foreach ($sortable as $_sort) {
            $query["order_by"][] = [$_sort["field"], $_sort["dir"]];
        }
        $paginationConf = [];
        if (!$export) {
            $query["limit"] = [$this->ci->input->post("take", true), $this->ci->input->post("skip", true)];
            $paginationConf = ["urlPrefix" => ""];
            $paginationConf["inPage"] = $this->ci->input->post("take", true);
        }
        $response["data"] = $export ? parent::load_all($query) : parent::paginate($query, $paginationConf);
        $stringQuery = $this->ci->db->last_query();
        $this->_table = $_table;
        $response["totalRows"] = $this->count_total_matching_rows($query);
        return $response;
    }
    public function load_opinions_on_stage_directly($legal_case_id, $order_by = [], $return_attachments = false, $stage_id = false, $page = "", $get_count = false, $page_limit = 10, $get_count_only = false)
    {
        $this->ci->load->model("language");
        $lang_id = $this->ci->language->get_id_by_session_lang();
        $select = "opinions.id,\r\n                   opinions.title,\r\n                   ('" . $this->modelCode . "' + CAST(opinions.id AS NVARCHAR))             AS opinionId,\r\n                   SUBSTRING(opinions.detailed_info, 1, 50)            AS detailed_info,\r\n                   opinions.detailed_info                                 AS opinionFullDetailed_info,\r\n                   (assigned_p.firstName + ' ' + assigned_p.lastName) AS assigned_to,\r\n                   opinions.priority,\r\n                   ts.name AS opinionStatus,\r\n                   opinions.due_date,\r\n                   opinions.assigned_to AS assignedToId,\r\n                   types.name as type";
        $query["join"][] = ["user_profiles assigned_p", "assigned_p.user_id = opinions.assigned_to", "left"];
        $query["join"][] = ["opinion_statuses ts", "ts.id = opinions.opinion_status_id", "left"];
        $query["join"][] = ["opinion_types_languages as types", "types.opinion_type_id = opinions.opinion_type_id and types.language_id = '" . $lang_id . "'", "left"];
        $query["where"][] = ["opinions.id NOT IN (select related_id from legal_case_events_related_data where related_object='Opinion')", NULL, false];
        $query["where"][] = ["opinions.legal_case_id", $legal_case_id];
        $query["where"][] = $stage_id ? ["opinions.stage", $stage_id] : ["opinions.stage is null"];
        $query["order_by"][] = !empty($order_by) ? $order_by : ["opinions.due_date desc"];
        if (!empty($page)) {
            $query["limit"] = [$page_limit, $page * $page_limit - $page_limit];
        }
        $query["select"] = [$select, false];
        if ($return_attachments) {
            $_select = $select . ", (select count(*) from documents_management_system where system_document = 1 and documents_management_system.name = 'opinion_' + CAST(opinions.id AS NVARCHAR)) as opinions_docs_count";
            $query["select"] = [$_select, false];
        }
        if ($get_count_only) {
            $response["totalRows"] = $this->count_total_matching_rows($query);
        } else {
            if ($get_count) {
                $response["opinions"] = parent::load_all($query);
                $response["totalRows"] = $this->count_total_matching_rows($query);
            } else {
                $response = parent::load_all($query);
            }
        }
        return $response;
    }
    public function api_lookup($term, $user_id, $overridePrivacy, $case_id = 0)
    {
        $configList = ["key" => "opinions.id", "value" => "name"];
        $configQury["select"][] = ["opinions.*, ( user_profiles.firstName + ' ' + user_profiles.lastName ) as createdByName,( modified.firstName + ' ' + modified.lastName ) AS modifiedByName, ts.name as opinionStatus,\r\n        legal_cases.subject as relatedMatterName,\r\n        legal_cases.client_id as clientId,\r\n        CASE\r\n\t\t\t\tWHEN c.contact_id IS NOT NULL\r\n                THEN (contacts.firstName + ' ' + contacts.lastName)\r\n                ELSE co.name END AS clientName", false];
        $configQury["join"] = [["legal_cases", "legal_cases.id = opinions.legal_case_id", "left"], ["user_profiles", "user_profiles.user_id = opinions.createdBy", "left"], ["user_profiles modified", "modified.user_id = opinions.modifiedBy", "left"], ["opinion_statuses as ts", "ts.id = opinions.opinion_status_id", "left"], ["clients as c", "c.id = legal_cases.client_id", "left"], ["companies as co", "co.id = c.company_id", "left"], ["contacts", "contacts.id = c.contact_id", "left"]];
        $this->ci->load->model("legal_case", "legal_casefactory");
        $this->ci->legal_case = $this->ci->legal_casefactory->get_instance();
        $where_condition = $this->ci->legal_case->get_matter_privacy_conditions($user_id, $overridePrivacy, false);
        $configQury["where"][] = ["(opinions.private IS NULL OR opinions.private = 'no' OR(opinions.private = 'yes' AND (" . "opinions.createdBy = '" . $this->logged_user_id . "' OR opinions.assigned_to = '" . $this->logged_user_id . "' OR " . "opinions.id IN (SELECT opinion_id FROM opinion_users WHERE user_id = '" . $this->logged_user_id . "') OR '" . $this->override_privacy . "' = 'yes'" . ")" . ") OR" . "(" . "opinions.legal_case_id IS NOT NULL AND " . "((opinions.private IS NULL OR opinions.private = 'no') AND " . $where_condition . ")" . ")" . ")"];
        if ($case_id) {
            $configQury["where"][] = ["opinions.legal_case_id", $case_id, NULL, false];
        }
        if (!empty($term)) {
            $modelCode = substr($term, 0, 1);
            $opinion_id = substr($term, 1);
            if (strcasecmp($modelCode, $this->get("modelCode")) === 0 && is_numeric($opinion_id)) {
                $configQury["where"][] = ["opinions.id = " . $opinion_id, NULL, false];
            } else {
                $term = $this->ci->db->escape_like_str($term);
                $configQury["where"][] = ["opinions.detailed_info LIKE '%" . $term . "%' OR opinions.title LIKE '%" . $term . "%'", NULL, false];
            }
        }
        if ($user_id) {
            $configQury["where"][] = ["opinions.assigned_to", $user_id, NULL, false];
        }
        return $this->load_all($configQury, $configList);
    }
    public function dashboard_recent_opinions($category = "opinions", $api_params = [])
    {
        $logged_user_id = $api_params["user_id"] ?? $this->logged_user_id;
        $this->ci->load->model("user_preference");
        $recent_opinions = unserialize($this->ci->user_preference->get_value_by_user("recent_cases", $logged_user_id));
        $response = [];
        if (isset($recent_opinions[$category])) {
            $recent_opinions = $recent_opinions[$category];
            $order_by = "CASE opinions.id";
            foreach ($recent_opinions as $key => $val) {
                if ($val == 0) {
                    unset($recent_opinions[$key]);
                } else {
                    $order_by .= " when '" . $val . "' then " . $key;
                }
            }
            $order_by .= " end";
            if (!empty($recent_opinions)) {
                $recent_opinions = implode(",", array_map("intval", $recent_opinions));
                if (!isset($this->ci->legal_case) || !is_object($this->ci->legal_case)) {
                    $this->ci->load->model("legal_case", "legal_casefactory");
                    $this->ci->legal_case = $this->ci->legal_casefactory->get_instance();
                }
                $where_condition = $this->ci->legal_case->get_matter_privacy_conditions($logged_user_id, $this->override_privacy, false);
                $query["select"] = ["opinions.*, ('" . $this->modelCode . "' + CAST( opinions.id AS nvarchar )) as opinionId, '" . $category . "' AS module", false];
                $query["join"] = [["legal_cases", "legal_cases.id = opinions.legal_case_id", "left"]];
                $query["where"][] = ["(opinions.private IS NULL OR opinions.private = 'no' OR(opinions.private = 'yes' AND (" . "opinions.modifiedBy = '" . $logged_user_id . "' OR opinions.assigned_to = '" . $logged_user_id . "' OR " . "opinions.id IN (SELECT opinion_id FROM opinion_users WHERE user_id = '" . $logged_user_id . "') OR '" . $this->override_privacy . "' = 'yes'" . ")" . ") OR" . "(" . "opinions.legal_case_id IS NOT NULL AND " . "((opinions.private IS NULL OR opinions.private = 'no') AND " . $where_condition . ")" . ")" . ")"];
                $query["where"][] = ["opinions.id IN (" . $recent_opinions . ")", NULL, false];
                $query["order_by"] = [$order_by];
                $response = $this->load_all($query);
            }
        }
        return $response;
    }
}

