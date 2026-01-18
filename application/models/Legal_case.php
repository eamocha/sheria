<?php

if (!defined("BASEPATH")) {
    exit("No direct script access allowed");
}
class Legal_case extends My_Model_Factory
{
}
class mysql_Legal_case extends My_Model
{
    protected $modelName = "legal_case";
    protected $modelCode = "M";
    protected $_table = "legal_cases";
    protected $_listFieldName = "subject";
    protected $_fieldsNames = ["id", "case_status_id", "case_type_id", "provider_group_id", "user_id", "contact_id", "subject", "description", "latest_development", "priority", "arrivalDate", "caseArrivalDate", "dueDate", "statusComments", "category", "caseValue", "recoveredValue", "judgmentValue", "externalizeLawyers", "estimatedEffort", "createdOn", "createdBy", "modifiedOn", "modifiedBy", "archived", "hideFromBoard", "private", "internalReference", "client_id", "timeTrackingBillable", "expensesBillable", "referredBy", "requestedBy", "closedOn", "legal_case_stage_id", "legal_case_client_position_id", "legal_case_success_probability_id", "channel", "assignedOn", "isDeleted", "modifiedByChannel", "stage", "workflow", "visibleToCP", "cap_amount_enable", "time_logs_cap_ratio", "expenses_cap_ratio", "cap_amount", "cap_amount_disallow", "closure_requested_by", "closure_comments", "closed_by","first_litigation_case_court_activity_purpose", "approval_step"];
    protected $allowedNulls = ["case_status_id", "user_id", "contact_id", "arrivalDate", "caseArrivalDate", "dueDate", "statusComments", "estimatedEffort", "private", "internalReference", "client_id", "timeTrackingBillable", "expensesBillable", "referredBy", "requestedBy", "closedOn", "legal_case_stage_id", "legal_case_client_position_id", "legal_case_success_probability_id", "assignedOn", "stage", "visibleToCP", "cap_amount_enable", "time_logs_cap_ratio", "expenses_cap_ratio", "cap_amount", "cap_amount_disallow", "closure_requested_by", "closure_comments", "closed_by", "first_litigation_case_court_activity_purpose","approval_step"];

    protected $priorityValues = ["critical", "high", "medium", "low"];
    protected $priorityValuesKeys = ["'critical'", "'high'", "'medium'", "'low'"];
    protected $categoryValues = ["", "Litigation", "Matter", "IP","Criminal"];
    protected $externalizeLawyersValues = ["", "yes", "no"];
    protected $archivedValues = ["", "yes", "no"];
    protected $channelValues = ["", "yes", "no"];

    protected $opponentMemberType = ["", "companies", "contacts"];
    protected $builtInLogs = true;
    protected $webChannel = "A4L";
    protected $apiMobileChannel = "MOB";
    protected $apiOutlookChannel = "MSO";
    protected $portalChannel = "CP";
    protected $apiGmailChannel = "A4G";
    protected $advisor_portal_channel = "AP";
    protected $apiMicrosoftTeamsChannel = "MST";
    protected $defaultSuccessProbabilityId = 1;
    protected $lookupInputsToValidate = [["input_name" => "clientLookup", "error_field" => "contact_company_id", "message" => ["main_var" => "not_exists3"]], ["input_name" => "requestedByName", "error_field" => "requestedBy", "message" => ["main_var" => "not_exists2", "lookup_for" => "contact"]], ["input_name" => "lookupCaseUsers", "error_field" => "case_watchers", "message" => ["main_var" => "not_exists", "lookup_for" => "user"]], ["input_name" => "opponentLookup[]", "error_field" => "opponent_member_id[]", "message" => ["main_var" => "not_exists3"]]];
    protected $notArchived = "no";
    public function __construct()
    {
        parent::__construct();
        $this->validate = ["case_status_id" => ["required" => false, "allowEmpty" => true, "rule" => "numeric", "message" => $this->ci->lang->line("cannot_be_blank_rule")], "case_type_id" => ["required" => true, "allowEmpty" => false, "rule" => "numeric", "message" => $this->ci->lang->line("cannot_be_blank_rule")], "provider_group_id" => ["required" => true, "allowEmpty" => false, "rule" => "numeric", "message" => $this->ci->lang->line("cannot_be_blank_rule")], "user_id" => ["required" => false, "allowEmpty" => true, "rule" => "numeric", "message" => sprintf($this->ci->lang->line("is_numeric_rule"), $this->ci->lang->line("user"))], "contact_id" => ["required" => false, "allowEmpty" => true, "rule" => "numeric", "message" => sprintf($this->ci->lang->line("is_numeric_rule"), $this->ci->lang->line("contact"))], "referredBy" => ["required" => false, "allowEmpty" => true, "rule" => "numeric", "message" => sprintf($this->ci->lang->line("is_numeric_rule"), $this->ci->lang->line("referred_by"))], "requestedBy" => ["required" => false, "allowEmpty" => true, "rule" => "numeric", "message" => sprintf($this->ci->lang->line("is_numeric_rule"), $this->ci->lang->line("requested_by"))],
            "subject" => ["isRequired" => ["required" => true, "allowEmpty" => false, "rule" => ["minLength", 1], "message" => $this->ci->lang->line("cannot_be_blank_rule")], "maxLength" => ["rule" => ["maxLength", 255], "message" => sprintf($this->ci->lang->line("max_characters"), 255)], "trim" => ["rule" => "trim_white_spaces_validation", "message" => sprintf($this->ci->lang->line("cannot_be_blank_rule"), 255)]], "description" => ["required" => false, "allowEmpty" => true, "rule" => ["minLength", 3], "message" => sprintf($this->ci->lang->line("min_length_rule"), $this->ci->lang->line("description"), 3)], "latest_development" => ["required" => false, "allowEmpty" => true, "rule" => ["minLength", 3], "message" => sprintf($this->ci->lang->line("min_length_rule"), $this->ci->lang->line("latest_development"), 3)], "priority" => ["required" => true, "allowEmpty" => false, "rule" => ["inList", $this->priorityValues], "message" => sprintf($this->ci->lang->line("required_list_values"), implode(", ", $this->priorityValues))], "arrivalDate" => ["dateFormat" => ["required" => false, "allowEmpty" => true, "rule" => $this->custom_validation_rules["date"], "message" => sprintf($this->ci->lang->line("required_date_rule"), $this->ci->lang->line("filed_on"))]], "caseArrivalDate" => ["isRequired" => ["required" => true, "allowEmpty" => false, "rule" => ["minLength", 1], "message" => $this->ci->lang->line("cannot_be_blank_rule")], "date" => ["required" => false, "allowEmpty" => true, "rule" => $this->custom_validation_rules["date"], "message" => sprintf($this->ci->lang->line("required_date_rule"), $this->ci->lang->line("arrival_date"))]], "dueDate" => ["dateFormat" => ["required" => false, "allowEmpty" => true, "rule" => $this->custom_validation_rules["date"], "message" => sprintf($this->ci->lang->line("required_date_rule"), $this->ci->lang->line("due_date"))]], "closedOn" => ["required" => false, "allowEmpty" => true, "rule" => $this->custom_validation_rules["date"], "message" => sprintf($this->ci->lang->line("required_date_rule"), $this->ci->lang->line("closed_on"))], "statusComments" => ["required" => false, "allowEmpty" => true, "rule" => ["minLength", 3], "message" => sprintf($this->ci->lang->line("min_length_rule"), $this->ci->lang->line("status_comments"), 3)], "category" => ["required" => true, "allowEmpty" => false, "rule" => ["inList", $this->categoryValues], "message" => sprintf($this->ci->lang->line("required_list_values"), implode(", ", $this->categoryValues))], "caseValue" => ["maxLength" => ["required" => false, "allowEmpty" => true, "rule" => ["maxLengthDecimal", 13, 2], "message" => sprintf($this->ci->lang->line("max_characters"), 13)], "numeric" => ["required" => false, "allowEmpty" => true, "rule" => $this->custom_validation_rules["decimal"], "message" => sprintf($this->ci->lang->line("decimal_allowed"), $this->ci->lang->line("caseValue"))]], "recoveredValue" => ["numeric" => ["required" => false, "allowEmpty" => true, "rule" => "numeric", "message" => sprintf($this->ci->lang->line("is_numeric_rule"), $this->ci->lang->line("recovered_value"))], "maxLength" => ["required" => false, "allowEmpty" => true, "rule" => ["maxLengthDecimal", 13, 2], "message" => sprintf($this->ci->lang->line("min_length_rule"), $this->ci->lang->line("recovered_value"), 13)]], "judgmentValue" => ["numeric" => ["required" => false, "allowEmpty" => true, "rule" => "numeric", "message" => sprintf($this->ci->lang->line("is_numeric_rule"), $this->ci->lang->line("judgment_value"))], "maxLength" => ["required" => false, "allowEmpty" => true, "rule" => ["maxLengthDecimal", 13, 2], "message" => sprintf($this->ci->lang->line("min_length_rule"), $this->ci->lang->line("judgment_value"), 13)]], "externalizeLawyers" => ["required" => true, "allowEmpty" => false, "rule" => ["inList", $this->externalizeLawyersValues], "message" => sprintf($this->ci->lang->line("required_list_values"), implode(", ", $this->externalizeLawyersValues))], "estimatedEffort" => ["maxLength" => ["required" => false, "allowEmpty" => true, "rule" => ["maxLengthDecimal", 8, 2], "message" => sprintf($this->ci->lang->line("max_characters"), 8)], "numeric" => ["required" => false, "allowEmpty" => true, "rule" => $this->custom_validation_rules["decimal"], "message" => $this->ci->lang->line("decimal_allowed")], "timeFormat" => ["rule" => "time_format_validation", "message" => sprintf($this->ci->lang->line("form_validation_time_entry_invalid_format"), $this->ci->lang->line("estimatedEffort"))]], "archived" => ["required" => true, "allowEmpty" => false, "rule" => ["inList", $this->archivedValues], "message" => sprintf($this->ci->lang->line("required_list_values"), implode(", ", $this->archivedValues))], "hideFromBoard" => ["required" => false, "allowEmpty" => true, "rule" => ["maxLength", 3], "message" => sprintf($this->ci->lang->line("max_characters"), 3)], "internalReference" => ["required" => false, "allowEmpty" => true, "rule" => ["maxLength", 255], "message" => sprintf($this->ci->lang->line("max_characters"), 255)], "legal_case_client_position_id" => ["required" => false, "allowEmpty" => true, "rule" => "numeric", "message" => sprintf($this->ci->lang->line("is_numeric_rule"), $this->ci->lang->line("client_position"))], "legal_case_success_probability_id" => ["required" => false, "allowEmpty" => true, "rule" => "numeric", "message" => sprintf($this->ci->lang->line("is_numeric_rule"), $this->ci->lang->line("success_probability"))], "cap_amount_enable" => ["required" => false, "allowEmpty" => true, "rule" => "numeric", "message" => sprintf($this->ci->lang->line("is_numeric_rule"), $this->ci->lang->line("enable_cap_amount"))], "cap_amount_disallow" => ["required" => false, "allowEmpty" => true, "rule" => "numeric", "message" => sprintf($this->ci->lang->line("is_numeric_rule"), $this->ci->lang->line("cap_amount_disallow"))], "cap_amount" => ["isRequired" => ["required" => false, "allowEmpty" => true, "rule" => ["minLength", 1], "message" => $this->ci->lang->line("cannot_be_blank_rule")], "maxLength" => ["required" => false, "allowEmpty" => true, "rule" => ["maxLengthDecimal", 8, 2], "message" => sprintf($this->ci->lang->line("max_characters"), 8)], "numeric" => ["required" => false, "allowEmpty" => true, "rule" => $this->custom_validation_rules["decimal"], "message" => $this->ci->lang->line("decimal_allowed")]], "expenses_cap_ratio" => ["isRequired" => ["required" => false, "allowEmpty" => true, "rule" => ["minLength", 1], "message" => $this->ci->lang->line("cannot_be_blank_rule")], "numeric" => ["required" => false, "allowEmpty" => true, "rule" => $this->custom_validation_rules["decimal"], "message" => $this->ci->lang->line("decimal_allowed")]], "time_logs_cap_ratio" => ["isRequired" => ["required" => false, "allowEmpty" => true, "rule" => ["minLength", 1], "message" => $this->ci->lang->line("cannot_be_blank_rule")], "numeric" => ["required" => false, "allowEmpty" => true, "rule" => $this->custom_validation_rules["decimal"], "message" => $this->ci->lang->line("decimal_allowed")]]];
        $this->logged_user_id = $this->ci->is_auth->get_user_id();
        $this->override_privacy = $this->ci->is_auth->get_override_privacy();
    }
    public function check_case_by_stage($stage_id, $category)
    {
        $load_query = ["select" => ["legal_cases.*"], "where" => [["legal_cases.legal_case_stage_id = ", $stage_id], ["legal_cases.category = ", $category]]];
        if ($this->load($load_query)) {
            return true;
        }
        return false;
    }
    public function load_case($case_id)
    {
        $loadQuery = ["select" => ["legal_cases.*, lcee.effectiveEffort,case_types.name as practice_area, workflow_status.name as Status, workflow_status.category as workflow_status_category, CONCAT(user_profiles.firstName, ' ', user_profiles.lastName ) as Assignee, CASE WHEN referredByContact.father!='' THEN CONCAT(referredByContact.firstName, ' ', referredByContact.father, ' ', referredByContact.lastName) ELSE CONCAT(referredByContact.firstName, ' ', referredByContact.lastName) END AS referredByName, CASE WHEN requestedByContact.father!='' THEN CONCAT(requestedByContact.firstName, ' ', requestedByContact.father, ' ', requestedByContact.lastName) ELSE CONCAT(requestedByContact.firstName, ' ', requestedByContact.lastName) END AS requestedByName, user_profiles.user_id as user_id, clients.name as client_name", false], "join" => [["user_profiles", "user_profiles.user_id = legal_cases.user_id", "left"], ["workflow_status", "workflow_status.id = legal_cases.case_status_id", "left"], ["legal_case_effective_effort AS lcee", "lcee.legal_case_id = legal_cases.id", "left"], ["contacts AS referredByContact", "referredByContact.id = legal_cases.referredBy", "left"], ["contacts AS requestedByContact", "requestedByContact.id = legal_cases.requestedBy", "left"], ["clients_view clients", "clients.id = legal_cases.client_id and clients.model = \"clients\"", "left"], ["case_types", "case_types.id = legal_cases.case_type_id", "left"]], "where" => [["legal_cases.id = ", $case_id], ["legal_cases.isDeleted = ", "0"]]];
        return $this->load($loadQuery);
    }
    public function load_case_details($case_id, $is_api = false)
    {
        $lang_id = $this->get_lang_id($is_api);
        $loadQuery = ["select" => ["legal_cases.*, workflow_status.name as status, case_types.name as practice_area, legal_case_stage_languages.name as stage_name, provider_groups.name as assigned_team, workflows.name as workflow_name, CONCAT(user_profiles.firstName , ' ' , user_profiles.lastName ) as assignee, CONCAT( referredByContact.firstName , ' ' , referredByContact.lastName ) AS referredByName, CONCAT( requestedByContact.firstName , ' ' , requestedByContact.lastName ) AS requestedByName, user_profiles.user_id as user_id, clients_view.name as client_name, lcee.effectiveEffort", false], "join" => [["user_profiles", "user_profiles.user_id = legal_cases.user_id", "left"], ["workflow_status", "workflow_status.id = legal_cases.case_status_id", "left"], ["legal_case_effective_effort AS lcee", "lcee.legal_case_id = legal_cases.id", "left"], ["contacts AS referredByContact", "referredByContact.id = legal_cases.referredBy", "left"], ["contacts AS requestedByContact", "requestedByContact.id = legal_cases.requestedBy", "left"], ["clients_view", "clients_view.id = legal_cases.client_id and clients_view.model = 'clients'", "left"], ["case_types", "case_types.id = legal_cases.case_type_id", "left"], ["legal_case_stage_languages", "legal_case_stage_languages.legal_case_stage_id = legal_cases.legal_case_stage_id and legal_case_stage_languages.language_id = '" . $lang_id . "'", "left"], ["provider_groups", "provider_groups.id=legal_cases.provider_group_id", "left"], ["workflows", "workflows.id = legal_cases.workflow", "left"]], "where" => [["legal_cases.id = ", $case_id], ["legal_cases.isDeleted = ", 0]]];
        return $this->load($loadQuery);
    }
    public function load_intellectual_property($ip_id)
    {
        $loadQuery = ["select" => ["legal_cases.id, legal_cases.user_id, legal_cases.subject, legal_cases.description, legal_cases.category, legal_cases.arrivalDate as filed_on, legal_cases.caseArrivalDate as arrival_date, CONCAT(user_profiles.firstName, ' ', user_profiles.lastName ) as assignee, ip_details.filingNumber, ip_rights.name as ip_right, ip_classes.name as ip_class, ip_subcategories.name as ip_subcategory, ip_statuses.name as ip_status, ip_names.name as ip_name, clients.name as client_name, agents.name as agent_name", false], "join" => [["user_profiles", "user_profiles.user_id = legal_cases.user_id", "left"], ["ip_details", "ip_details.legal_case_id = legal_cases.id", "left"], ["intellectual_property_rights ip_rights", "ip_rights.id = ip_details.intellectual_property_right_id", "left"], ["ip_classes", "ip_classes.id = ip_details.ip_class_id", "left"], ["ip_subcategories", "ip_subcategories.id = ip_details.ip_subcategory_id", "left"], ["ip_statuses", "ip_statuses.id = ip_details.ip_status_id", "left"], ["ip_names", "ip_names.id = ip_details.ip_name_id", "left"], ["clients_view agents", "agents.member_id = ip_details.agentId and agents.model = \"clients\" and agents.type = (CASE WHEN ip_details.agentType = \"contact\" THEN \"Person\" ELSE \"Company\" END)", "left"], ["clients_view clients", "clients.id = legal_cases.client_id and clients.model = \"clients\"", "left"]], "where" => [["legal_cases.id = ", $ip_id], ["legal_cases.isDeleted = ", "0"]]];
        return $this->load($loadQuery);
    }
    public function get_workflow_caseId($case_id)
    {
        $loadQuery = ["select" => ["workflows.id, workflows.name", false], "join" => [["workflow_status", "workflow_status.id = legal_cases.case_status_id", "left"], ["workflow_status_relation", "workflow_status.id = workflow_status_relation.status_id", "left"], ["workflows", "workflows.id = workflow_status_relation.workflow_id", "left"]], "where" => [["legal_cases.id = ", $case_id], ["legal_cases.isDeleted = ", "0"]]];
        return $this->load($loadQuery);
    }
    public function get_all_matters_with_status($status_id, $category = NULL, $removed_case_types = NULL, $workflow_id = NULL)
    {
        $query = [];
        $query["select"] = ["legal_cases.id", false];
        $query["where"][] = ["legal_cases.case_status_id = ", $status_id];
        $query["where"][] = ["legal_cases.workflow = ", $workflow_id];
        $query["where"][] = ["legal_cases.isDeleted = ", "0"];
        if ($category) {
            $query["where"][] = ["legal_cases.category", $category];
        }
        if ($removed_case_types) {
            $query["where"][] = ["legal_cases.case_type_id IN (" . $removed_case_types . ")"];
        }
        return $this->load_all($query);
    }
    public function load_companies($case_id)
    {
        $companies = [];
        if ($case_id < 1) {
            return $companies;
        }
        $case_companies = $this->ci->db->select("companies.id as id, companies.name, legal_case_company_roles.name as role, legal_cases_companies.comments as comments")->join("companies", "companies.id = legal_cases_companies.company_id", "inner")->join("legal_case_company_roles", "legal_case_company_roles.id = legal_cases_companies.legal_case_company_role_id", "left")->where("legal_cases_companies.case_id", $case_id)->where("legal_cases_companies.companyType", "company")->where("companies.status", "Active")->get("legal_cases_companies");
        if (!$case_companies->num_rows()) {
            return $companies;
        }
        foreach ($case_companies->result() as $company) {
            $companies[(string) $company->id] = ["name" => $company->name, "role" => $company->role, "comments" => $company->comments];
        }
        return $companies;
    }
    public function load_contacts($case_id)
    {
        $contacts = ["contact" => [], "external lawyer" => [], "judge" => [], "opponentLawyer" => []];
        if ($case_id < 1) {
            return $contacts;
        }
        $case_contacts = $this->ci->db->select(["contacts.id as id, CASE WHEN contacts.father!='' THEN CONCAT(contacts.firstName, ' ', contacts.father, ' ', contacts.lastName) ELSE CONCAT(contacts.firstName, ' ', contacts.lastName) END AS name, contactType, contacts.isLawyer, legal_case_contact_roles.name as role, legal_cases_contacts.comments as comments", false])->join("contacts", "contacts.id = legal_cases_contacts.contact_id", "inner")->join("legal_case_contact_roles", "legal_case_contact_roles.id = legal_cases_contacts.legal_case_contact_role_id", "left")->where("legal_cases_contacts.case_id", $case_id)->where("contacts.status", "Active")->order_by("contacts.firstName", "asc")->order_by("contacts.lastName", "asc")->get("legal_cases_contacts");
        if (!$case_contacts->num_rows()) {
            return $contacts;
        }
        foreach ($case_contacts->result() as $contact) {
            $contacts[$contact->contactType][(string) $contact->id] = ["name" => $contact->name, "isLawyer" => $contact->isLawyer, "role" => $contact->role, "comments" => $contact->comments];
        }
        return $contacts;
    }
    public function load_case_effective_effort($case_id)
    {
        $loadQuery = [];
        $loadQuery = ["select" => "lcee.effectiveEffort", "join" => [["legal_case_effective_effort AS lcee", "lcee.legal_case_id = legal_cases.id", "left"]], "where" => [["legal_cases.id = ", $case_id], ["legal_cases.isDeleted = ", "0"]]];
        return $this->load($loadQuery);
    }
    public function load_watchers_users($case_id)
    {
        $users = [];
        $data = [];
        $status = [];
        if ($case_id < 1) {
            return $users;
        }
        $case_users = $this->ci->db->select(["UP.user_id as id, CONCAT( UP.firstName, ' ', UP.lastName ) as name,UP.status as status", false])->join("user_profiles UP", "UP.user_id = legal_case_users.user_id", "inner")->where("legal_case_users.legal_case_id", $case_id)->get("legal_case_users");
        if (!$case_users->num_rows()) {
            return $users;
        }
        foreach ($case_users->result() as $user) {
            $users[(string) $user->id] = $user->name;
        }
        foreach ($case_users->result() as $user) {
            $status[(string) $user->id] = $user->status;
        }
        $data[0] = $users;
        $data[1] = $status;
        return $data;
    }
    public function archieved_cases_total_number($case_status_ids = false, $filter = false, $update = false, $hide = false)
    {
        $systemPreferences = $this->ci->session->userdata("systemPreferences");
        $ids = $case_status_ids ? $case_status_ids : $systemPreferences["archiveCaseStatus"];
        $query = [];
        $query["select"] = ["legal_cases.id"];
        $where_condition = $hide ? "legal_cases.hideFromBoard IS NULL" : "legal_cases.archived = 'no'";
        $query["where"] = [["legal_cases.case_status_id IN ( " . $ids . ")"], [$where_condition], ["legal_cases.isDeleted = ", 0]];
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
    public function get_matter_privacy_conditions($logged_user_id, $override_privacy, $return_array = true)
    {
        $this->ci->load->model("system_preference");
        $privacy_per_assigned_team = $this->ci->system_preference->get_value_by_key("privacyPerAssignedTeam");
        $privacy_per_assigned_team = $privacy_per_assigned_team["keyValue"] ?? 0;
        $condition = "((" . "'" . $override_privacy . "' = 'yes'" . " OR (" . " '" . $privacy_per_assigned_team . "' = '1'" . " AND (" . "legal_cases.provider_group_id IN (SELECT provider_groups.id FROM provider_groups WHERE provider_groups.allUsers = '1')" . " OR legal_cases.provider_group_id IN (SELECT provider_groups_users.provider_group_id FROM provider_groups_users WHERE provider_groups_users.user_id = '" . $logged_user_id . "')" . " )" . " )" . " OR (" . " '" . $privacy_per_assigned_team . "' = '0'" . " AND (" . " legal_cases.private IS NULL" . " OR legal_cases.private = 'no'" . " OR (" . " legal_cases.private = 'yes'" . " AND  (" . " legal_cases.createdBy = '" . $logged_user_id . "'" . " OR legal_cases.user_id = '" . $logged_user_id . "'" . " OR legal_cases.id IN (SELECT legal_case_id FROM legal_case_users WHERE user_id = '" . $logged_user_id . "')" . " )" . " )" . " )" . " )" . ") AND legal_cases.isDeleted = '0')";
        return $return_array ? [$condition, NULL, false] : $condition;
    }
    public function lookup($term)
    {
        $fullSelectFlag = $this->ci->input->get("fullSelectFlag", true);
        $configList = ["key" => "legal_cases.id", "value" => "name"];
        $configQury["select"] = ["legal_cases.*,CONCAT('" . $this->modelCode . "',legal_cases.id) as caseID,IF(LENGTH(legal_cases.subject) > 45, CONCAT(SUBSTR(legal_cases.subject, 1, 45), ' ', '...'), legal_cases.subject) as subject, subject AS fullSubject,clients_view.name as clientName, workflow_status.name as status, concat(`modified`.`firstName`, ' ', `modified`.`lastName`) AS `modifiedByName`, concat(`user_profiles`.`firstName`, ' ', `user_profiles`.`lastName`) AS `createdByName`", false];
        $configQury["join"] = [["clients_view", "clients_view.id=legal_cases.client_id and clients_view.model = \"clients\"", "left"], ["workflow_status", "workflow_status.id = legal_cases.case_status_id", "left"], ["user_profiles", "user_profiles.user_id = legal_cases.createdBy", "left"], ["user_profiles modified", "modified.user_id = legal_cases.modifiedBy", "left"]];
        if ($fullSelectFlag == "true") {
            $configQury["select"] = ["legal_cases.*,clients_view.name as clientName", false];
        }
        $configQury["where"] = [];
        $configQury["where"][] = $this->get_matter_privacy_conditions($this->logged_user_id, $this->override_privacy);
        if (!empty($term)) {
            $configQury["where"] = [];
            $modelCode = substr($term, 0, 1);
            $ID = substr($term, 1);
            if (strcasecmp($modelCode, $this->get("modelCode")) === 0 && is_numeric($ID)) {
                $qId = substr($term, 1);
                if (is_numeric($qId)) {
                    $configQury["where"][] = ["legal_cases.id = " . $qId, NULL, false];
                }
                $configQury["where"][] = $this->get_matter_privacy_conditions($this->logged_user_id, $this->override_privacy);
                $configQury["where"][] = ["legal_cases.archived = '" . $this->notArchived . "'"];
            } else {
                $term = $this->ci->db->escape_like_str($term);
                $configQury["where"][] = ["(legal_cases.subject LIKE '%" . $term . "%' or clients_view.name LIKE '%" . $term . "%' or legal_cases.internalReference LIKE '%" . $term . "%')", NULL, false];
                $configQury["where"][] = ["legal_cases.archived = '" . $this->notArchived . "'"];
                $configQury["where"][] = $this->get_matter_privacy_conditions($this->logged_user_id, $this->override_privacy);
            }
        }
        if ($moreFilters = $this->ci->input->get("more_filters")) {
            foreach ($moreFilters as $_field => $_term) {
                if ($_field == "caseType" && $_term == "ExtractIP") {
                    $configQury["where"][] = ["legal_cases.category IN ('Matter','Litigation','Criminal')", NULL, false];
                } else {
                    $configQury["where"][] = ["legal_cases." . $_field, $_term];
                }
            }
            unset($_field);
            unset($_term);
        }
        return $this->load_all($configQury, $configList);
    }
    public function insert_companies($caseCompanies)
    {
        $sqlInsert = "";
        $sqlDelete = [];
        $rows = [];
        foreach ($caseCompanies as $companyType => $caseCompany) {
            extract($caseCompany);
            if (is_array($companies)) {
                $subDelete = "(case_id = '" . $case_id . "' and company_id NOT IN (0";
                foreach ($companies as $company_id) {
                    $rows[] = compact("case_id", "company_id");
                    $subDelete .= ", '" . $company_id . "'";
                }
                $subDelete .= "))";
            } else {
                $subDelete = "(case_id = '" . $case_id . "')";
            }
            $sqlDelete[] = [$subDelete];
        }
        $this->prep_query(["or_where" => $sqlDelete]);
        $this->ci->db->delete("legal_cases_companies");
        $this->reset_write();
        if (count($rows)) {
            $table = $this->_table;
            $this->_table = "legal_cases_companies";
            $this->insert_on_duplicate_update_batch($rows, ["case_id", "company_id"]);
            $this->_table = $table;
        }
        return true;
    }
    public function insert_contacts($caseContacts)
    {
        $sqlInsert = "";
        $sqlDelete = [];
        $rows = [];
        foreach ($caseContacts as $contactType => $caseContact) {
            extract($caseContact);
            if (is_array($contacts)) {
                $subDelete = "(case_id = '" . $case_id . "' and contactType = '" . $contactType . "' and contact_id NOT IN (0";
                foreach ($contacts as $contact_id) {
                    $rows[] = compact("case_id", "contact_id", "contactType");
                    $subDelete .= ", '" . $contact_id . "'";
                }
                $subDelete .= "))";
            } else {
                $subDelete = "(case_id = '" . $case_id . "' and contactType = '" . $contactType . "')";
            }
            $sqlDelete[] = [$subDelete];
        }
        $this->prep_query(["or_where" => $sqlDelete]);
        $this->ci->db->delete("legal_cases_contacts");
        $this->reset_write();
        if (count($rows)) {
            $table = $this->_table;
            $this->_table = "legal_cases_contacts";
            $this->insert_on_duplicate_update_batch($rows, ["case_id", "contact_id", "contactType"]);
            $this->_table = $table;
        }
        return true;
    }
    public function insert_watchers_users($caseUsers)
    {
        $sqlInsert = "";
        $sqlDelete = [];
        $rows = [];
        foreach ($caseUsers as $key => $caseUser) {
            extract($caseUser);
            if (is_array($users)) {
                $subDelete = "(legal_case_id = '" . $legal_case_id . "' and user_id NOT IN (0";
                foreach ($users as $user_id) {
                    $rows[] = compact("legal_case_id", "user_id");
                    $subDelete .= ", '" . $user_id . "'";
                }
                $subDelete .= "))";
            } else {
                $subDelete = "(legal_case_id = '" . $legal_case_id . "')";
            }
            $sqlDelete[] = [$subDelete];
        }
        $this->prep_query(["or_where" => $sqlDelete]);
        $this->ci->db->delete("legal_case_users");
        $this->reset_write();
        if (count($rows)) {
            $table = $this->_table;
            $this->_table = "legal_case_users";
            $this->insert_on_duplicate_update_batch($rows, ["legal_case_id", "user_id"]);
            $this->_table = $table;
        }
        return true;
    }
    public function get_legal_cases_by_Position_and_status($data, $columns_selected, $filter, $sortable, $totalRows = false)
    {
        $query = $this->get_legal_cases_grid_query_web($filter, $sortable, "", false, true, true);
        $select = "";
        $limits = $data["limits"];
        $skip = $data["skip"];
        $take = $data["take"];
        if (!empty($limits)) {
            $take1 = $limits;
            $skip1 = 0;
            if (!$totalRows) {
                if (!empty($limits)) {
                    if ($limits < $take) {
                        $take1 = $limits;
                        $skip1 = 0;
                    } else {
                        if ($take < $limits) {
                            if ($limits < $skip + $take) {
                                $take1 = $limits - $skip;
                                $skip1 = $skip;
                            } else {
                                $take1 = $take;
                                $skip1 = $skip;
                            }
                        }
                    }
                } else {
                    $take1 = $take;
                    $skip1 = $skip;
                }
            }
        }
        if (isset($take1)) {
            $take = $take1;
            $skip = $skip1;
        }
        foreach ($columns_selected["Field"] as $key => $value) {
            if ($key != count($columns_selected["Field"]) - 1) {
                $select .= $value . " as '" . $columns_selected["Lang_Field"][$key] . "' ,";
            } else {
                $select .= $value . " as '" . $columns_selected["Lang_Field"][$key] . "' ";
            }
        }
        $query["select"] = [$select, false];
        if (!empty($data["position"])) {
            $query["where"][] = ["legal_cases.legal_case_client_position_id", $data["position"]];
        }
        if (!empty($data["cases_category"]) && $data["cases_category"] != "All") {
            $query["where"][] = ["legal_cases.category", $data["cases_category"]];
        }
        if (!empty($data["sort"])) {
            $sortable = $data["sort"];
        }
        if (empty($limits)) {
            $this->prep_query($query);
            $response["totalRows"] = $this->ci->db->get($this->get("_table"))->num_rows();
        }
        if (is_array($sortable) && !empty($sortable)) {
            foreach ($sortable as $_sort) {
                if (!empty($columns_selected["Lang_Field"]) && in_array($_sort["field"], $columns_selected["Lang_Field"])) {
                    $query["order_by"][] = [$_sort["field"], $_sort["dir"]];
                }
            }
        } else {
            $query["order_by"] = ["legal_cases.id desc"];
        }
        if (empty($limits) && !$totalRows) {
            $query["limit"] = [$this->ci->input->post("take", true), $this->ci->input->post("skip", true)];
        } else {
            if (!empty($limits)) {
                $query["limit"] = [$take, $skip];
            }
        }
        $response["data"] = parent::load_all($query);
        if (!empty($limits) && isset($response["data"])) {
            if (sizeof($response["data"]) != 0 && $data["take"] <= sizeof($response["data"]) && sizeof($response["data"]) < $limits && $data["skip"] == 0) {
                $response["totalRows"] = $limits;
            } else {
                $response["totalRows"] = sizeof($response["data"]);
            }
        }
        return $response;
    }
    public function ms_escape_string($data)
    {
        if (!isset($data) || empty($data)) {
            return "";
        }
        if (is_numeric($data)) {
            return $data;
        }
        $non_displayables = ["/%0[0-8bcef]/", "/%1[0-9a-f]/", "/[\\x00-\\x08]/", "/\\x0b/", "/\\x0c/", "/[\\x0e-\\x1f]/"];
        foreach ($non_displayables as $regex) {
            $data = preg_replace($regex, "", $data);
        }
        $data = str_replace("'", "''", $data);
        return "  '" . $data . "'";
    }
    public function jasper_load_all_cases($columns, $customs)
    {
        if (empty($this->custom_field)) {
            $this->ci->load->model("custom_field", "custom_fieldfactory");
            $this->ci->custom_field = $this->ci->custom_fieldfactory->get_instance();
        }
        $parameters = [];
        $this->ci->load->model("language");
        $langId = $this->ci->language->get_id_by_session_lang();
        for ($i = 0; $i < count($columns); $i++) {
            if ($columns[$i] == "case_id") {
                $parameters["Field"][] = "concat('M', `legal_cases`.`id`)";
            } else {
                if ($columns[$i] == "case_stage") {
                    $parameters["Field"][] = "legal_case_stage_languages.name";
                } else {
                    if ($columns[$i] == "client_position") {
                        $parameters["Field"][] = "legal_case_client_position_languages.name";
                    } else {
                        if ($columns[$i] == "success_probability") {
                            $parameters["Field"][] = "legal_case_success_probability_languages.name";
                        } else {
                            if ($columns[$i] == "case_status") {
                                $parameters["Field"][] = "workflow_status.name";
                            } else {
                                if ($columns[$i] == "court_type") {
                                    $parameters["Field"][] = "court_types.name";
                                } else {
                                    if ($columns[$i] == "court_region") {
                                        $parameters["Field"][] = "court_regions.name";
                                    } else {
                                        if ($columns[$i] == "first_stage") {
                                            $parameters["Field"][] = "(Select legal_case_stage_languages.name\r\n                                                FROM legal_case_litigation_details lcld\r\n                                                         LEFT JOIN `legal_case_stage_languages`\r\n                                                                   ON `legal_case_stage_languages`.`legal_case_stage_id` = `lcld`.legal_case_stage and\r\n                                                                      `legal_case_stage_languages`.`language_id` = " . $this->get_lang_id() . "\r\n                                                where lcld.legal_case_id = legal_cases.id\r\n                                                order by lcld.createdOn asc\r\n                                                LIMIT 1)";
                                        } else {
                                            if ($columns[$i] == "first_stage_judgment") {
                                                $parameters["Field"][] = "(SELECT legal_case_hearings.judgment\r\n                                                    FROM legal_case_hearings\r\n                                                    WHERE legal_case_hearings.stage = (Select lcld.id\r\n                                                                                       FROM legal_case_litigation_details lcld\r\n                                                                                       where lcld.legal_case_id = legal_cases.id\r\n                                                                                       order by lcld.createdOn asc\r\n                                                                                       LIMIT 1)\r\n                                                          and startTime = (SELECT MAX(legal_case_hearings.startTime)\r\n                                                                            FROM legal_case_hearings\r\n                                                                           WHERE legal_case_hearings.legal_case_id = legal_cases.id\r\n                                                                             AND is_deleted = 0\r\n                                                                             and legal_case_hearings.judged = 'yes'\r\n                                                                             AND startDate = (\r\n                                                                                              SELECT MAX(startDate)\r\n                                                                                                FROM legal_case_hearings\r\n                                                                                              where legal_case_hearings.legal_case_id = legal_cases.id AND is_deleted = 0\r\n                                                                                                    AND legal_case_hearings.stage = (Select lcld.id\r\n                                                                                                                                         FROM legal_case_litigation_details lcld\r\n                                                                                                                                         where lcld.legal_case_id = legal_cases.id\r\n                                                                                                                                         order by lcld.createdOn asc\r\n                                                                                                                                         LIMIT 1) and legal_case_hearings.judged = 'yes')\r\n                                                                                                )\r\n                                                                            AND legal_case_hearings.stage = (Select lcld.id\r\n                                                                                                           FROM legal_case_litigation_details lcld\r\n                                                                                                           where lcld.legal_case_id = legal_cases.id\r\n                                                                                                           order by lcld.createdOn asc\r\n                                                                                                           LIMIT 1)\r\n                                                                              and legal_case_hearings.legal_case_id = legal_cases.id\r\n                                                                              and legal_case_hearings.judged = 'yes'\r\n                                                                                LIMIT 1)";
                                            } else {
                                                if ($columns[$i] == "first_judgment_date") {
                                                    $parameters["Field"][] = "(Select lcld_2.sentenceDate\r\n                                                FROM legal_case_litigation_details lcld_2\r\n                                                where lcld_2.legal_case_id = legal_cases.id\r\n                                                order by lcld_2.createdOn asc\r\n                                                LIMIT 1)";
                                                } else {
                                                    if ($columns[$i] == "last_hearing") {
                                                        $parameters["Field"][] = "(SELECT DISTINCT CONCAT(startDate, ' - ', startTime)\r\n                                       FROM legal_case_hearings\r\n                                       WHERE startTime = (SELECT MAX(legal_case_hearings.startTime)\r\n                                                          FROM legal_case_hearings\r\n                                                          WHERE legal_case_hearings.legal_case_id=legal_cases.id AND is_deleted = 0 \r\n                                                          AND startDate = (SELECT MAX(startDate) FROM legal_case_hearings where legal_case_hearings.legal_case_id=legal_cases.id AND is_deleted = 0)) and legal_case_hearings.legal_case_id=legal_cases.id\r\n                                                           and startDate = (SELECT MAX(startDate) FROM legal_case_hearings where legal_case_hearings.legal_case_id=legal_cases.id AND is_deleted = 0)\r\n                                       )";
                                                    } else {
                                                        if ($columns[$i] == "reasons_of_postponement_of_last_hearing") {
                                                            $parameters["Field"][] = "(SELECT DISTINCT reasons_of_postponement\r\n                                    FROM legal_case_hearings\r\n                                    WHERE startTime = (SELECT MAX(legal_case_hearings.startTime)\r\n                                                        FROM legal_case_hearings\r\n                                                        WHERE legal_case_hearings.legal_case_id=legal_cases.id AND is_deleted = 0\r\n                                                        AND startDate = (SELECT MAX(startDate) FROM legal_case_hearings where legal_case_hearings.legal_case_id=legal_cases.id AND is_deleted = 0)) and legal_case_hearings.legal_case_id=legal_cases.id\r\n                                                        and startDate = (SELECT MAX(startDate) FROM legal_case_hearings where legal_case_hearings.legal_case_id=legal_cases.id AND is_deleted = 0)\r\n                                    )";
                                                        } else {
                                                            if ($columns[$i] == "judgment") {
                                                                $parameters["Field"][] = "(SELECT  legal_case_hearings.judgment\r\n                                        FROM legal_case_hearings\r\n                                        WHERE startTime = (SELECT MAX(legal_case_hearings.startTime)\r\n                                                            FROM legal_case_hearings\r\n                                                            WHERE legal_case_hearings.legal_case_id=legal_cases.id AND is_deleted = 0 and legal_case_hearings.judged = 'yes'\r\n                                                            AND startDate = (SELECT MAX(startDate) FROM legal_case_hearings where legal_case_hearings.legal_case_id=legal_cases.id AND is_deleted = 0 and legal_case_hearings.judged = 'yes')) and legal_case_hearings.legal_case_id=legal_cases.id and legal_case_hearings.judged = 'yes'\r\n                                                            and startDate = (SELECT MAX(startDate) FROM legal_case_hearings where legal_case_hearings.legal_case_id=legal_cases.id AND is_deleted = 0 and legal_case_hearings.judged = 'yes')\r\n                                      GROUP BY legal_case_hearings.legal_case_id)";
                                                            } else {
                                                                if ($columns[$i] == "sentenceDate") {
                                                                    $parameters["Field"][] = "legal_case_litigation_details.sentenceDate";
                                                                } else {
                                                                    if ($columns[$i] == "opponentNationalities") {
                                                                        $parameters["Field"][] = "(select group_concat((CASE                                 WHEN isnull(`legal_case_opponents`.`opponent_member_type`) THEN NULL                                 ELSE (CASE                                           WHEN (`legal_case_opponents`.`opponent_member_type` = 'contact')                                               THEN (CASE                                                         WHEN (`opponentContactNationalitiesCountry`.`name` is not null)                                                             THEN (CASE                                                                       WHEN `opponent_positions`.`name` != ''                                                                           THEN concat(`opponentcontact`.`firstName`,                                                                                       ' ',                                                                                       `opponentcontact`.`lastName`,                                                                                       ' - ',                                                                                       `opponent_positions`.`name`,                                                                                       ' (',                                                                                       `opponentContactNationalitiesCountry`.`name`,                                                                                       ')')                                                                       ELSE concat(`opponentcontact`.`firstName`, ' ',                                                                                   `opponentcontact`.`lastName`, '(',                                                                                   `opponentContactNationalitiesCountry`.`name`,                                                                                   ')')                                                             END)                                                         ELSE (CASE                                                                   WHEN `opponent_positions`.`name` != ''                                                                       THEN (CASE                                                                                 WHEN (`opponentcontact`.`father` <> '')                                                                                     THEN concat(                                                                                         `opponentcontact`.`firstName`,                                                                                         ' ',                                                                                         `opponentcontact`.`father`,                                                                                         ' ',                                                                                         `opponentcontact`.`lastName`,                                                                                         ' - ',                                                                                         `opponent_positions`.`name`)                                                                                 ELSE concat(                                                                                         `opponentcontact`.`firstName`,                                                                                         ' ',                                                                                         `opponentcontact`.`lastName`,                                                                                         ' - ',                                                                                         `opponent_positions`.`name`)                                                                       END)                                                                   ELSE (CASE                                                                             WHEN (`opponentcontact`.`father` <> '')                                                                                 THEN concat(                                                                                     `opponentcontact`.`firstName`, ' ',                                                                                     `opponentcontact`.`father`, ' ',                                                                                     `opponentcontact`.`lastName`)                                                                             ELSE concat(`opponentcontact`.`firstName`,                                                                                         ' ',                                                                                         `opponentcontact`.`lastName`)                                                                       END)                                                             END)                                               END)                                           ELSE                                               (CASE                                                    WHEN `opponent_positions`.`name` != ''                                                        THEN                                                        (CASE                                                             WHEN (`opponentcompanynationalities`.`name` is not null)                                                                 THEN concat(`opponentcompany`.`name`, ' - ',                                                                             `opponent_positions`.`name`, ' (',                                                                             `opponentcompanynationalities`.`name`, ')')                                                             ELSE concat(`opponentcompany`.`name`, ' - ', `opponent_positions`.`name`)                                                            END)                                                    ELSE                                                        (CASE                                                             WHEN (`opponentcompanynationalities`.`name` is not null)                                                                 THEN concat(`opponentcompany`.`name`, '(',                                                                             `opponentcompanynationalities`.`name`, ')')                                                             ELSE `opponentcompany`.`name`                                                             END)                                                   END)                                     END)           END) order by `legal_case_opponents`.`case_id` ASC separator ', ')                                             from (((((`legal_case_opponents` join `opponents` on ((`opponents`.`id` =                                                                                                       `legal_case_opponents`.`opponent_id`))) left join `companies` `opponentcompany` on ((                                                        (`opponentcompany`.`id` = `opponents`.`company_id`) and                                                        (`legal_case_opponents`.`opponent_member_type` = 'company')))) left join `contacts` `opponentcontact` on ((                                                        (`opponentcontact`.`id` = `opponents`.`contact_id`) and                                                        (`legal_case_opponents`.`opponent_member_type` = 'contact')))) left join `countries_languages` `opponentcompanynationalities` on ((                                                        (`opponentcompanynationalities`.`country_id` = `opponentcompany`.`nationality_id`) and `opponentcompanynationalities`.`language_id` = " . $this->get_lang_id() . " AND                                                         (`legal_case_opponents`.`opponent_member_type` = 'company'))) LEFT JOIN `legal_case_opponent_position_languages` AS `opponent_positions`                                                          ON (((`opponent_positions`.`legal_case_opponent_position_id` = `legal_case_opponents`.`opponent_position`) and                                                                (`opponent_positions`.`language_id` = " . $this->get_lang_id() . "))))                                                         left join `contact_nationalities_details` `opponentcontactnationalities`                                                                   on (((`opponentcontactnationalities`.`contact_id` = `opponentcontact`.`id`) and                                                                        (`legal_case_opponents`.`opponent_member_type` = 'contact'))))                                                        LEFT JOIN countries_languages AS opponentContactNationalitiesCountry ON opponentContactNationalitiesCountry.country_id = opponentcontactnationalities.nationality_id AND opponentContactNationalitiesCountry.language_id = " . $this->get_lang_id() . "                                              where (`legal_case_opponents`.`case_id` = `legal_cases`.`id`))";
                                                                    } else {
                                                                        if ($columns[$i] == "opponent_foreign_name") {
                                                                            $parameters["Field"][] = "(SELECT GROUP_CONCAT(CASE                    WHEN `legal_case_opponents`.`opponent_member_type` IS NULL                        THEN NULL                    ELSE (CASE                              WHEN `opponent_positions`.`name` != '' THEN (CASE                                WHEN `legal_case_opponents`.`opponent_member_type` = 'company'                                    THEN IFNULL(CONCAT(opponentCompany.foreignName, ' - ', `opponent_positions`.`name`), CONCAT(`opponentCompany`.`name`, ' - ', `opponent_positions`.`name`))                                ELSE (                                CONCAT(IFNULL(opponentContact.foreignFirstName, `opponentContact`.`firstName`), ' ', IFNULL(opponentContact.foreignLastName, `opponentContact`.`lastName`), ' - ', `opponent_positions`.`name`)                                )                                END)                              ELSE (CASE                                        WHEN `legal_case_opponents`.`opponent_member_type` = 'company'                                            THEN IFNULL(opponentCompany.foreignName, `opponentCompany`.`name`)                                        ELSE (                                        CONCAT(IFNULL(opponentContact.foreignFirstName, `opponentContact`.`firstName`), ' ', IFNULL(opponentContact.foreignLastName, `opponentContact`.`lastName`))                                        )                                 END) END) END                order by `legal_case_opponents`.`case_id` ASC SEPARATOR ', ')                FROM `legal_case_opponents`                         LEFT JOIN `opponents` ON `opponents`.`id` = `legal_case_opponents`.`opponent_id`                         LEFT JOIN `companies` AS `opponentCompany`                                   ON `opponentCompany`.`id` = `opponents`.`company_id` AND                                      `legal_case_opponents`.`opponent_member_type` = 'company'                         LEFT JOIN `contacts` AS `opponentContact`                                   ON `opponentContact`.`id` = `opponents`.`contact_id` AND                                      `legal_case_opponents`.`opponent_member_type` = 'contact'                         LEFT JOIN `legal_case_opponent_position_languages` AS `opponent_positions`                                   ON `opponent_positions`.`legal_case_opponent_position_id` =                                      `legal_case_opponents`.`opponent_position` and                                      `opponent_positions`.`language_id` = '" . $langId . "'\r\n                WHERE `legal_case_opponents`.`case_id` = `legal_cases`.`id`)";
                                                                        } else {
                                                                            if ($columns[$i] == "latest_development") {
                                                                                $parameters["Field"][] = "legal_cases.latest_development";
                                                                            } else {
                                                                                if ($columns[$i] == "case_type") {
                                                                                    $parameters["Field"][] = "case_types.name";
                                                                                } else {
                                                                                    if ($columns[$i] == "description") {
                                                                                        $parameters["Field"][] = "legal_cases.description";
                                                                                    } else {
                                                                                        if ($columns[$i] == "court") {
                                                                                            $parameters["Field"][] = "courts.name";
                                                                                        } else {
                                                                                            if ($columns[$i] == "court_degree") {
                                                                                                $parameters["Field"][] = "court_degrees.name";
                                                                                            } else {
                                                                                                if ($columns[$i] == "outsource_to") {
                                                                                                    $parameters["Field"][] = "                CONCAT_WS                (                    ', ',                    GROUP_CONCAT(                        DISTINCT case                             when (`conextlaw`.`father` <> '')                             then concat(`conextlaw`.`firstName`, ' ', `conextlaw`.`father`, ' ', `conextlaw`.`lastName`)                            else concat(`conextlaw`.`firstName`, ' ', `conextlaw`.`lastName`)                         end                        SEPARATOR ', '                    ),                    GROUP_CONCAT(                        DISTINCT companiesExtLaw.name                        SEPARATOR ', '                    )                )";
                                                                                                } else {
                                                                                                    if ($columns[$i] == "litigationExternalRef") {
                                                                                                        $parameters["Field"][] = "GROUP_CONCAT( DISTINCT `legal_case_litigation_external_references`.`number`)";
                                                                                                    } else {
                                                                                                        if ($columns[$i] == "statusComments") {
                                                                                                            $parameters["Field"][] = "legal_cases.statusComments";
                                                                                                        } else {
                                                                                                            if ($columns[$i] == "assignee") {
                                                                                                                $parameters["Field"][] = "concat(`up`.`firstName`, ' ', `up`.`lastName`)";
                                                                                                            } else {
                                                                                                                if ($columns[$i] == "requestedByName") {
                                                                                                                    $parameters["Field"][] = "(case when (`requestedbycontact`.`father` <> '') then concat(`requestedbycontact`.`firstName`, ' ',                                                                   `requestedbycontact`.`father`, ' ',                                                                   `requestedbycontact`.`lastName`)                                           else concat(`requestedbycontact`.`firstName`, ' ',`requestedbycontact`.`lastName`) end)";
                                                                                                                } else {
                                                                                                                    if ($columns[$i] == "providerGroup") {
                                                                                                                        $parameters["Field"][] = "provider_groups.name";
                                                                                                                    } else {
                                                                                                                        if ($columns[$i] == "clientName") {
                                                                                                                            $parameters["Field"][] = "if(isnull(`clients`.`company_id`), (case                                               when (`cont`.`father` <> ' ') then concat_ws(' ', `cont`.`firstName`,                                                                                            `cont`.`father`,                                                                                            `cont`.`lastName`)                                               else concat_ws(' ', `cont`.`firstName`, `cont`.`lastName`) end),                                        `comp`.`name`)";
                                                                                                                        } else {
                                                                                                                            if ($columns[$i] == "client_foreign_name") {
                                                                                                                                $parameters["Field"][] = "(if(isnull(`clients`.`company_id`), concat_ws(' ', `cont`.`foreignFirstName`, `cont`.`foreignLastName`) ,                                    `comp`.`foreignName`))";
                                                                                                                            } else {
                                                                                                                                if ($columns[$i] == "effectiveEffort") {
                                                                                                                                    $parameters["Field"][] = "lcee.effectiveEffort";
                                                                                                                                } else {
                                                                                                                                    if (is_numeric($columns[$i])) {
                                                                                                                                        $field_data = $this->ci->custom_field->load(["select" => ["type, type_data"], "where" => [["id", $columns[$i]]]]);
                                                                                                                                        switch ($field_data["type"]) {
                                                                                                                                            case "date":
                                                                                                                                                $parameters["Field"][] = "(SELECT cfv.date_value FROM custom_field_values AS cfv WHERE legal_cases.id = cfv.recordId AND cfv.custom_field_id = " . $columns[$i] . ")";
                                                                                                                                                break;
                                                                                                                                            case "date_time":
                                                                                                                                                $parameters["Field"][] = "(SELECT CONCAT(cfv.date_value, ' ', TIME_FORMAT(cfv.time_value, '%h:%i')) FROM custom_field_values AS cfv WHERE legal_cases.id = cfv.recordId AND cfv.custom_field_id = " . $columns[$i] . ")";
                                                                                                                                                break;
                                                                                                                                            case "lookup":
                                                                                                                                                $lookup_type_properties = $this->ci->custom_field->get_lookup_type_properties($field_data["type_data"]);
                                                                                                                                                $lookup_displayed_columns_table = $lookup_type_properties["external_data"] ? "ltedt" : "ltt";
                                                                                                                                                $lookup_external_data_join = $lookup_type_properties["external_data"] ? "LEFT JOIN " . $lookup_type_properties["external_data_properties"]["table"] . " ltedt ON ltedt." . $lookup_type_properties["external_data_properties"]["foreign_key"] . " = ltt.id" : "";
                                                                                                                                                $last_segment = isset($lookup_type_properties["display_properties"]["third_segment"]["column_name"]) ? $lookup_displayed_columns_table . "." . $lookup_type_properties["display_properties"]["second_segment"]["column_name"] . ",' '," . $lookup_displayed_columns_table . "." . $lookup_type_properties["display_properties"]["third_segment"]["column_name"] : $lookup_displayed_columns_table . "." . $lookup_type_properties["display_properties"]["second_segment"]["column_name"];
                                                                                                                                                $parameters["Field"][] = "\r\n                        (SELECT GROUP_CONCAT(" . $lookup_displayed_columns_table . "." . $lookup_type_properties["display_properties"]["first_segment"]["column_name"] . ",' ' ," . $last_segment . " SEPARATOR ', ')\r\n                           FROM custom_field_values cfv\r\n                        left join " . $lookup_type_properties["table"] . " ltt on ltt.id = cfv.text_value " . $lookup_external_data_join . "\r\n                        where cfv.recordId = " . $this->_table . ".id  and custom_field_id = " . $columns[$i] . "\r\n                   \r\n                        )";
                                                                                                                                                break;
                                                                                                                                            case "list":
                                                                                                                                                $parameters["Field"][] = "(SELECT GROUP_CONCAT(cfv.text_value) FROM custom_field_values AS cfv WHERE cfv.recordId = " . $this->_table . ".id AND cfv.custom_field_id = " . $columns[$i] . ")";
                                                                                                                                                break;
                                                                                                                                            default:
                                                                                                                                                $parameters["Field"][] = "(SELECT cfv.text_value FROM custom_field_values as cfv WHERE legal_cases.id = cfv.recordId AND cfv.custom_field_id = " . $columns[$i] . ")";
                                                                                                                                        }
                                                                                                                                    } else {
                                                                                                                                        $parameters["Field"][] = "legal_cases." . $columns[$i];
                                                                                                                                    }
                                                                                                                                }
                                                                                                                            }
                                                                                                                        }
                                                                                                                    }
                                                                                                                }
                                                                                                            }
                                                                                                        }
                                                                                                    }
                                                                                                }
                                                                                            }
                                                                                        }
                                                                                    }
                                                                                }
                                                                            }
                                                                        }
                                                                    }
                                                                }
                                                            }
                                                        }
                                                    }
                                                }
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            }
            $parameters["Lang_Field"][] = $columns[$i];
        }
        return $parameters;
    }
    public function k_load_all_cases($filter, $sortable, $pagingOn = false, &$query = [], &$stringQuery = "", $page_number = "")
    {
        $response = $this->get_legal_cases_grid_query_web($filter, $sortable, $page_number, $pagingOn);
        $stringQuery = $response["stringQuery"];
        $query = $response["query"];
        unset($response["stringQuery"]);
        unset($response["query"]);
        return $response;
    }
    public function load_daily_agenda_cases()
    {
        $query = [];
        $query["select"] = ["legal_cases.id, legal_cases.subject, users.email, legal_cases.category", false];
        $query["join"] = [["users", "users.id = legal_cases.user_id"]];
        $query["where"][] = ["legal_cases.archived = 'no' AND CURRENT_DATE = legal_cases.dueDate AND legal_cases.isDeleted = 0", NULL, false];
        return $this->load_all($query);
    }
    public function load_all_cases_per_type_and_ranges($ranges, $pagingOn = false)
    {
        $this->ci->load->model("case_type");
        $_table = $this->_table;
        $this->_table = "legal_cases";
        $query = [];
        $dataResult = [];
        $string = "";
        for ($i = 0; $i < 6; $i++) {
            if (empty($ranges["Min"][$i]) && $i == 0 && empty($ranges["Max"][$i])) {
                $string .= "SUM(CASE WHEN (legal_cases.caseValue > '" . $ranges["Min"][$i] . "' ) THEN 1 ELSE 0 END) as '>" . $ranges["Min"][$i] . "'";
                if ($string != "") {
                    $string .= ",";
                }
            }
            if (empty($ranges["Min"][$i]) && $i == 0 && !empty($ranges["Max"][$i])) {
                $string .= "SUM(CASE WHEN (legal_cases.caseValue between  '0' and  '" . $ranges["Max"][0] . "' ) THEN 1 ELSE 0 END) as '" . $ranges["Min"][0] . "-" . $ranges["Max"][0] . "' ";
                if ($string != "") {
                    $string .= ",";
                }
            }
            if (!empty($ranges["Min"][$i])) {
                if (empty($ranges["Max"][$i])) {
                    $string .= "SUM(CASE WHEN (legal_cases.caseValue > '" . $ranges["Min"][$i] . "' ) THEN 1 ELSE 0 END) as '>" . $ranges["Min"][$i] . "'";
                    if ($string != "") {
                        $string .= ",";
                    }
                } else {
                    $string .= "SUM(CASE WHEN (legal_cases.caseValue between  '" . $ranges["Min"][$i] . "' and  '" . $ranges["Max"][$i] . "' ) THEN 1 ELSE 0 END) as '" . $ranges["Min"][$i] . "-" . $ranges["Max"][$i] . "' ";
                    if ($string != "") {
                        $string .= ",";
                    }
                }
            }
        }
        $total_of_cases_qry = ["select" => [$string]];
        $total_of_cases_qry["where"][] = $this->get_matter_privacy_conditions($this->logged_user_id, $this->override_privacy);
        $string .= ",case_type_id";
        $query["select"] = [$string];
        $query["where"][] = $this->get_matter_privacy_conditions($this->logged_user_id, $this->override_privacy);
        $query["group_by"] = ["case_type_id"];
        $query["join"] = ["case_types", "case_types.id = legal_cases.case_type_id", "left"];
        $query["where"][] = ["case_types.name != '" . $this->ci->case_type->get("caseTypeOfIP") . "' and case_types.isDeleted=0", NULL, false];
        if ($limit = $this->ci->input->post("take", true)) {
            $query["limit"] = [$this->ci->input->post("take", true), $this->ci->input->post("skip", true)];
            $paginationConf = ["urlPrefix" => ""];
            $paginationConf["inPage"] = $this->ci->input->post("take", true);
        }
        $dataResult["total_of_cases"] = $this->load($total_of_cases_qry);
        $totalRowsQuery = ["select" => ["count(*) as numRows"]];
        $totalRowsQuery["where"] = $this->get_matter_privacy_conditions($this->logged_user_id, $this->override_privacy);
        $totalRowsQueryResults = $this->load($totalRowsQuery);
        $dataResult["totalRows"] = $totalRowsQueryResults["numRows"];
        $dataResult["result"] = $pagingOn ? parent::paginate($query, $paginationConf) : parent::load_all($query);
        $this->ci->load->model(["case_type"]);
        $Case_Types = $this->ci->case_type->load_list(["where" => ["isDeleted", 0]]);
        foreach ($dataResult["result"] as $key => $value) {
            $dataResult["result"][$key]["name"] = $Case_Types[$value["case_type_id"]];
        }
        foreach ($dataResult["total_of_cases"] as $k => $val) {
            if ($k[0] === ">") {
                $tmpKey = ">" . number_format((double) substr($k, 1), 2);
            } else {
                $tmpKey = explode("-", $k);
                $tmpKey = number_format((double) $tmpKey[0], 2) . "-" . number_format((double) $tmpKey[1], 2);
            }
            unset($dataResult["total_of_cases"][$k]);
            $dataResult["total_of_cases"][$tmpKey] = $val;
        }
        foreach ($dataResult["result"] as $k1 => $v1) {
            foreach ($v1 as $k2 => $v2) {
                if ($k2[0] === ">") {
                    $tmpKey = ">" . number_format((double) substr($k2, 1), 2);
                    unset($dataResult["result"][$k1][$k2]);
                    $dataResult["result"][$k1][$tmpKey] = $v2;
                } else {
                    if (strpos($k2, "-") !== false) {
                        $tmpKey = explode("-", $k2);
                        if (!empty($tmpKey)) {
                            $tmpKey = number_format((double) $tmpKey[0], 2) . "-" . number_format((double) $tmpKey[1], 2);
                        }
                        unset($dataResult["result"][$k1][$k2]);
                        $dataResult["result"][$k1][$tmpKey] = $v2;
                    }
                }
            }
        }
        return $dataResult;
    }
    public function k_load_all_cases_per_company($filter, $sortable, $pagingOn = false, &$query = [])
    {
        $_table = $this->_table;
        $this->_table = "legal_cases_per_company AS legal_cases";
        $query = [];
        $response = [];
        $query["select"] = ["legal_cases.id, \r\n            legal_cases.case_status_id, \r\n            legal_cases.case_type_id, \r\n            legal_cases.provider_group_id, \r\n            legal_cases.user_id, \r\n            legal_cases.contact_id, \r\n            legal_cases.client_id, \r\n            legal_cases.subject, \r\n            legal_cases.description, \r\n            legal_cases.priority, \r\n            legal_cases.arrivalDate, \r\n            legal_cases.dueDate, \r\n            legal_cases.statusComments, \r\n            legal_cases.category, \r\n            legal_cases.caseValue, \r\n            legal_cases.internalReference, \r\n            legal_cases.externalizeLawyers, \r\n            legal_cases.estimatedEffort, \r\n            legal_cases.createdOn, \r\n            legal_cases.createdBy, \r\n            legal_cases.modifiedOn, \r\n            legal_cases.modifiedBy, \r\n            legal_cases.archived, \r\n            legal_cases.private, \r\n            legal_cases.effectiveEffort, \r\n            legal_cases.caseID, \r\n            legal_cases.status, \r\n            legal_cases.type, \r\n            legal_cases.providerGroup, \r\n            legal_cases.assignee, \r\n            legal_cases.archivedCases, \r\n            legal_cases.company, \r\n            legal_cases.role, \r\n            legal_cases.role_id, \r\n            legal_cases.contactOutsourceTo, \r\n            legal_cases.contactContributor", false];
        if (is_array($filter)) {
            if (isset($filter["filters"])) {
                foreach ($filter["filters"] as $_filter) {
                    if (!empty($_filter["filters"][0]["field"]) && in_array($_filter["filters"][0]["field"], ["effectiveEffort", "estimatedEffort"])) {
                        $system_preferences = $this->ci->session->userdata("systemPreferences");
                        $this->ci->load->library("TimeMask");
                        $_filter["filters"][0]["value"] = $this->ci->timemask->humanReadableToHours($_filter["filters"][0]["value"]);
                    }
                    $this->prep_k_filter($_filter, $query, $filter["logic"]);
                }
                unset($_filter);
            }
            if (isset($filter["customFields"])) {
                $this->ci->load->model("custom_field", "custom_fieldfactory");
                $this->ci->custom_field = $this->ci->custom_fieldfactory->get_instance("custom_fieldfactory");
                $this->ci->custom_field->prep_custom_field_filters($this->modelName, $filter["customFields"], $query);
            }
        }
        $query["where"][] = $this->get_matter_privacy_conditions($this->logged_user_id, $this->override_privacy);
        $query["where"][] = ["legal_cases.category !='IP'", NULL, false];
        $this->prep_query($query);
        $response["totalRows"] = $this->ci->db->get($this->get("_table"))->num_rows();
        if (is_array($sortable) && !empty($sortable)) {
            foreach ($sortable as $_sort) {
                $query["order_by"][] = [$_sort["field"], $_sort["dir"]];
            }
        } else {
            $query["order_by"] = ["legal_cases.id desc"];
        }
        if ($limit = $this->ci->input->post("take", true)) {
            $query["limit"] = [$this->ci->input->post("take", true), $this->ci->input->post("skip", true)];
            $paginationConf = ["urlPrefix" => ""];
            $paginationConf["inPage"] = $this->ci->input->post("take", true);
        }
        $response["data"] = $pagingOn ? parent::paginate($query, $paginationConf) : parent::load_all($query);
        $this->_table = $_table;
        return $response;
    }
    public function k_load_all_cases_per_contact($filter, $sortable, $pagingOn = false, &$query = [], $table = "legal_cases_per_contact AS legal_cases")
    {
        $_table = $this->_table;
        $this->_table = $table;
        $query = [];
        $response = [];
        $query["select"] = ["legal_cases.id, legal_cases.case_status_id, \r\n            legal_cases.case_type_id, \r\n            legal_cases.provider_group_id, \r\n            legal_cases.user_id, \r\n            legal_cases.contact_id, \r\n            legal_cases.client_id, \r\n            legal_cases.subject, \r\n            legal_cases.description, \r\n            legal_cases.priority, \r\n            legal_cases.arrivalDate, \r\n            legal_cases.dueDate, \r\n            legal_cases.statusComments, \r\n            legal_cases.category, \r\n            legal_cases.caseValue, \r\n            legal_cases.internalReference, \r\n            legal_cases.externalizeLawyers, \r\n            legal_cases.estimatedEffort, \r\n            legal_cases.createdOn, \r\n            legal_cases.createdBy, \r\n            legal_cases.modifiedOn, \r\n            legal_cases.modifiedBy, \r\n            legal_cases.archived, \r\n            legal_cases.private, \r\n            legal_cases.effectiveEffort, \r\n            legal_cases.caseID, \r\n            legal_cases.status, \r\n            legal_cases.type, \r\n            legal_cases.providerGroup, \r\n            legal_cases.assignee, \r\n            legal_cases.archivedCases, \r\n            legal_cases.contact, \r\n            legal_cases.role, \r\n            legal_cases.role_id, \r\n            legal_cases.contactOutsourceTo, \r\n            legal_cases.companyOutsourceTo,\r\n            legal_cases.contactContributor", false];
        if (is_array($filter)) {
            if (isset($filter["filters"])) {
                foreach ($filter["filters"] as $_filter) {
                    if (!empty($_filter["filters"][0]["field"]) && in_array($_filter["filters"][0]["field"], ["effectiveEffort", "estimatedEffort"])) {
                        $system_preferences = $this->ci->session->userdata("systemPreferences");
                        $this->ci->load->library("TimeMask");
                        $_filter["filters"][0]["value"] = $this->ci->timemask->humanReadableToHours($_filter["filters"][0]["value"]);
                    }
                    $this->prep_k_filter($_filter, $query, $filter["logic"]);
                }
                unset($_filter);
            }
            if (isset($filter["customFields"])) {
                $this->ci->load->model("custom_field", "custom_fieldfactory");
                $this->ci->custom_field = $this->ci->custom_fieldfactory->get_instance();
                $this->ci->custom_field->prep_custom_field_filters($this->modelName, $filter["customFields"], $query);
            }
        }
        $query["where"][] = $this->get_matter_privacy_conditions($this->logged_user_id, $this->override_privacy);
        $query["where"][] = ["legal_cases.category !='IP'", NULL, false];
        $this->prep_query($query);
        $response["totalRows"] = $this->ci->db->get($this->get("_table"))->num_rows();
        if (is_array($sortable) && !empty($sortable)) {
            foreach ($sortable as $_sort) {
                $query["order_by"][] = [$_sort["field"], $_sort["dir"]];
            }
        } else {
            $query["order_by"] = ["legal_cases.id desc"];
        }
        if ($limit = $this->ci->input->post("take", true)) {
            $query["limit"] = [$this->ci->input->post("take", true), $this->ci->input->post("skip", true)];
            $paginationConf = ["urlPrefix" => ""];
            $paginationConf["inPage"] = $this->ci->input->post("take", true);
        }
        $response["data"] = $pagingOn ? parent::paginate($query, $paginationConf) : parent::load_all($query);
        $this->_table = $_table;
        return $response;
    }
    public function universal_search_litigations($q, $pagingOn = true, $type = "Litigation")
    {
        return $this->universal_search($q, $pagingOn, $type);
    }
    public function universal_search_matters($q, $pagingOn = true, $type = "Matter")
    {
        return $this->universal_search($q, $pagingOn, $type);
    }
    public function universal_search($q, $pagingOn = true, $type = "Litigation")
    {
        $query = [];
        $q = addslashes(trim((string) $q));
        $category = $type;
        $select = "concat( '" . $this->modelCode . "', legal_cases.id ) as caseId,legal_cases.id, \r\n                    CONCAT(user_profiles.firstName, ' ',user_profiles.lastName ) as assignee, priority, dueDate,\r\n                    workflow_status.name as status, legal_cases.subject AS fullSubject, internalReference as reference";
        $select = $this->ci->session->userdata("AUTH_language") == "arabic" ? $select . ", CASE WHEN CHAR_LENGTH(legal_cases.subject) > 60 THEN CONCAT('...' , ' ',SUBSTR(legal_cases.subject, 1, 60)) ELSE legal_cases.subject END AS subject" : $select . ", CASE WHEN CHAR_LENGTH(legal_cases.subject) > 60 THEN CONCAT(SUBSTR(legal_cases.subject, 1, 60), ' ', '...') ELSE legal_cases.subject END AS subject";
        $query["select"] = [$select, false];
        $query["where"][] = ["(legal_cases.subject LIKE '%" . $q . "%' OR legal_cases.description LIKE '%" . $q . "%'OR legal_cases.internalReference LIKE '%" . $q . "%' OR legal_cases.id = if(SUBSTRING('" . $q . "', 1, 1) = '" . $this->modelCode . "', SUBSTRING('" . $q . "', 2), '" . $q . "')) AND legal_cases.category LIKE '%" . $category . "%' AND legal_cases.archived='" . $this->notArchived . "'", NULL, false];
        $query["where"][] = $this->get_matter_privacy_conditions($this->logged_user_id, $this->override_privacy);
        $query["join"] = [["user_profiles", "user_profiles.user_id = legal_cases.user_id", "left"], ["workflow_status", "workflow_status.id = legal_cases.case_status_id", "inner"]];
        return $pagingOn ? parent::paginate($query, ["urlPrefix" => ""]) : parent::load_all($query);
    }
    public function count_all_cases_by_category()
    {
        $user_id = $this->ci->user_logged_in_data["user_id"];
        $this->ci->user_profile->fetch(["user_id" => $user_id]);
        $override_privacy = $this->ci->user_profile->get_field("overridePrivacy");
        $query["select"] = ["COUNT(0) count, legal_cases.category"];
        $query["where"][] = $this->get_matter_privacy_conditions($user_id, $override_privacy);
        $query["where"][] = ["legal_cases.archived", "no"];
        $query["group_by"] = ["legal_cases.category"];
        $data = $this->load_list($query, ["key" => "category", "value" => "count"]);
        return ["litigation_cases" => isset($data["Litigation"]) ? $data["Litigation"] : "0", "Criminal_cases" => isset($data["Criminal"]) ? $data["Criminal"] : "0", "corporate_matters" => isset($data["Matter"]) ? $data["Matter"] : "0", "intellectual_properties" => isset($data["IP"]) ? $data["IP"] : "0"];
    }
    public function external_reference_universal_search($q, $pagingOn = true)
    {
        $query = [];
        $q = addslashes(trim((string) $q));
        $select = "concat( '" . $this->modelCode . "', legal_cases.id ) as caseId,legal_cases.id, CONCAT(user_profiles.firstName, ' ',user_profiles.lastName ) as assignee, priority, dueDate, workflow_status.name as status, case_types.name as type,lcler.number,lcler.refDate,lcler.comments, legal_cases.subject AS fullSubject";
        $select = $this->ci->session->userdata("AUTH_language") == "arabic" ? $select . ", CASE WHEN CHAR_LENGTH(legal_cases.subject) > 60 THEN CONCAT('...' , ' ',SUBSTR(legal_cases.subject, 1, 60)) ELSE legal_cases.subject END AS subject" : $select . ", CASE WHEN CHAR_LENGTH(legal_cases.subject) > 60 THEN CONCAT(SUBSTR(legal_cases.subject, 1, 60), ' ', '...') ELSE legal_cases.subject END AS subject";
        $query["select"] = [$select, false];
        $query["where"][] = ["(lcler.number LIKE '%" . $q . "%')", NULL, false];
        $query["where"][] = $this->get_matter_privacy_conditions($this->logged_user_id, $this->override_privacy);
        $query["join"] = [["legal_case_litigation_details ld", "ld.legal_case_id = legal_cases.id", "inner"], ["legal_case_litigation_external_references lcler", "lcler.stage = ld.id", "inner"], ["user_profiles", "user_profiles.user_id = legal_cases.user_id", "left"], ["workflow_status", "workflow_status.id = legal_cases.case_status_id", "inner"], ["case_types", "case_types.id = legal_cases.case_type_id", "inner"]];
        return $pagingOn ? parent::paginate($query, ["urlPrefix" => ""]) : parent::load_all($query);
    }
    public function count_arrival_cases_per_month($year)
    {
        $query["select"] = ["COUNT(0) count, MONTH(arrivalDate) month"];
        $query["where"][] = ["YEAR(arrivalDate)", $year];
        $query["where"][] = $this->get_matter_privacy_conditions($this->logged_user_id, $this->override_privacy);
        $query["group_by"] = ["MONTH(arrivalDate)"];
        return $this->load_list($query, ["key" => "month", "value" => "count"]);
    }
    public function count_dueDate_cases_per_month($year)
    {
        $query["select"] = ["COUNT(0) count, MONTH(dueDate) month"];
        $query["where"][] = ["YEAR(dueDate)", $year];
        $query["where"][] = $this->get_matter_privacy_conditions($this->logged_user_id, $this->override_privacy);
        $query["group_by"] = ["MONTH(dueDate)"];
        return $this->load_list($query, ["key" => "month", "value" => "count"]);
    }
    public function count_cases_per_assignee($year)
    {
        $query["select"] = ["Count(0) AS count, CASE WHEN legal_cases.user_id IS NOT NULL THEN MAX(CONCAT(user_profiles.firstName, ' ', user_profiles.lastName)) ELSE 'Unassigned' END AS userName,CASE WHEN legal_cases.user_id IS NOT NULL THEN MAX(user_profiles.status) ELSE '' END AS status", false];
        $query["join"] = ["user_profiles", "user_profiles.user_id = legal_cases.user_id", "left"];
        $query["where"][] = ["YEAR(caseArrivalDate)", $year];
        $query["where"][] = $this->get_matter_privacy_conditions($this->logged_user_id, $this->override_privacy);
        $query["group_by"] = ["legal_cases.user_id"];
        return $this->load_all($query);
    }
    public function get_case_status($id)
    {
        $this->prep_query(["select" => "name", "where" => ["id = '" . $id . "'"], "limit" => "1"]);
        $result = $this->ci->db->get("workflow_status");
        if ($result->num_rows() == 1) {
            $row = $result->row();
            return $row->name;
        }
        return "";
    }
    public function top_cases_by_dueDate()
    {
        $query = [];
        $query["select"] = ["legal_cases.id, legal_cases.subject, legal_cases.priority, workflow_status.name as caseStatus, legal_cases.dueDate"];
        $query["where"][] = ["legal_cases.user_id = " . $this->ci->is_auth->get_user_id()];
        $query["where"][] = $this->get_matter_privacy_conditions($this->logged_user_id, $this->override_privacy);
        $query["join"] = [["workflow_status", "workflow_status.id = legal_cases.case_status_id", "inner"]];
        $query["order_by"] = "legal_cases.dueDate asc";
        return parent::load_all($query);
    }
    public function k_load_all_cases_lawyers_contributors($filter, $sortable)
    {
        $query = [];
        $response = [];
        $query["select"] = ["legal_cases_contacts.id, legal_cases_contacts.comments as comments, CASE WHEN contacts.father!='' THEN CONCAT(contacts.firstName, ' ', contacts.father, ' ', contacts.lastName) ELSE CONCAT(contacts.firstName, ' ', contacts.lastName) END as contactName, CONCAT( user_profiles.firstName, ' ', user_profiles.lastName ) as createdBy, legal_cases_contacts.createdOn, CASE WHEN legal_case_contact_role_id IS NULL THEN 0 ELSE legal_case_contact_role_id END AS legal_case_contact_role_id, contacts.id AS contactId", false];
        if (is_array($filter) && isset($filter["filters"])) {
            foreach ($filter["filters"] as $_filter) {
                if (!empty($_filter["filters"][0]["field"]) && in_array($_filter["filters"][0]["field"], ["effectiveEffort", "estimatedEffort"])) {
                    $system_preferences = $this->ci->session->userdata("systemPreferences");
                    $this->ci->load->library("TimeMask");
                    $_filter["filters"][0]["value"] = $this->ci->timemask->humanReadableToHours($_filter["filters"][0]["value"]);
                }
                $this->prep_k_filter($_filter, $query, $filter["logic"]);
            }
            unset($_filter);
        }
        $query["join"] = [["contacts", "contacts.id = legal_cases_contacts.contact_id", "left"], ["legal_case_contact_roles", "legal_case_contact_roles.id = legal_cases_contacts.legal_case_contact_role_id", "left"], ["user_profiles", "user_profiles.user_id = legal_cases_contacts.createdBy", "left"]];
        $this->prep_query($query);
        $response["totalRows"] = $this->ci->db->count_all_results("legal_cases_contacts");
        if (is_array($sortable) && !empty($sortable)) {
            foreach ($sortable as $_sort) {
                $query["order_by"][] = [$_sort["field"], $_sort["dir"]];
            }
        } else {
            $query["order_by"] = ["legal_cases_contacts.id desc"];
        }
        if ($limit = $this->ci->input->post("take", true)) {
            $query["limit"] = [$this->ci->input->post("take", true), $this->ci->input->post("skip", true)];
        }
        $this->prep_query($query);
        $result = $this->ci->db->get("legal_cases_contacts");
        $response["data"] = $result->result_array();
        return $response;
    }
    public function get_old_values($id)
    {
        return $this->load(["select" => ["id, channel, case_status_id, case_type_id, priority, user_id, provider_group_id, client_id, caseValue, arrivalDate, dueDate, closedOn, category, legal_case_stage_id,stage"], "where" => ["id", $id], "limit" => "1"]);
    }
    public function touch_logs($action = "update", $oldValues = [], $userMaker = false, $modifiedByChannel = NULL)
    {
        if (!$userMaker) {
            $userMaker = $this->ci->is_auth->get_user_id();
        }
        if (!empty($oldValues)) {
            $changes = [];
            foreach ($oldValues as $field => $value) {
                if ($value != $this->_fields[$field] && $field != "legal_case_stage_id" && $field != "channel") {
                   // 
                 //  if($field=="user_id"){ $changes[$field] = ["before" => $value, "after" => $this->_fields[$field],"remarks"=>"off"];}
                   //else $changes[$field] = ["before" => $value, "after" => $this->_fields[$field]];
                   $changes[$field] = ["before" => $value, "after" => $this->_fields[$field]];
                   
                }
            }
            if (!empty($changes)) {
                $data = ["legal_case_id" => $oldValues["id"], "changes" => serialize($changes), "user_id" => $userMaker, "changedOn" => date("Y-m-d H:i:s")];
                $data["modifiedByChannel"] = isset($modifiedByChannel) ? $modifiedByChannel : $this->webChannel;
                $this->ci->db->set($data)->insert("legal_case_changes");
            }
        }
        $this->log_built_in_last_action($this->_fields["id"], $userMaker, isset($modifiedByChannel) ? $modifiedByChannel : $this->webChannel);
        $this->log_action($action, $this->_fields["id"]);
    }
    public function load_case_logs($id, $visible_cases_ids)
    {
        $sql = "\r\n          select legal_case_changes.*, CONCAT( UP.firstName, ' ', UP.lastName ) as modifiedBy\r\n          from user_profiles as UP left join legal_case_changes on UP.user_id = legal_case_changes.user_id\r\n          where legal_case_changes.legal_case_id = " . $id . " and legal_case_changes.legal_case_id in (" . implode(",", $visible_cases_ids) . ") and (legal_case_changes.modifiedByChannel != '" . $this->portalChannel . "' or legal_case_changes.modifiedByChannel is null)\r\n          union\r\n          select legal_case_changes.*, CONCAT( CPU.firstName, ' ', CPU.lastName, ' (Portal User)' ) as modifiedBy\r\n          from customer_portal_users as CPU left join legal_case_changes on CPU.id = legal_case_changes.user_id\r\n          where legal_case_changes.legal_case_id = " . $id . " and legal_case_changes.legal_case_id in (" . implode(",", $visible_cases_ids) . ") and legal_case_changes.modifiedByChannel = '" . $this->portalChannel . "'\r\n          union\r\n          select legal_case_changes.*, '---' as modifiedBy\r\n          from legal_case_changes\r\n          where legal_case_id = " . $id . " and legal_case_changes.legal_case_id in (" . implode(",", $visible_cases_ids) . ") and user_id is null";
        $query_execution = $this->ci->db->query($sql);
        $result = $query_execution->result_array();
        function cmp($a, $b)
        {
            return strcmp($b["changedOn"], $a["changedOn"]);
        }
        usort($result, "cmp");
        return $result;
    }
    public function load_all_case_data($id)
    {
        $this->ci->load->model("language");
        $langId = $this->ci->language->get_id_by_session_lang();
        $query = [];
        $query["select"] = ["legal_cases.*,user_profiles.status as userStatus,case_types.name as caseTypeName,workflow_status.name as caseStatusName, provider_groups.name as  providerGroupsName,CONCAT( user_profiles.firstName, ' ', user_profiles.lastName ) as assignedToName, legal_case_stage_languages.name as caseStage, legal_case_client_position_languages.name AS clientPosition,legal_case_success_probability_languages.name AS successProbability,CONCAT( `referredByContact`.`firstName`, ' ', `referredByContact`.`lastName` ) AS `referredByName`, CONCAT( `requestedByContact`.`firstName`, ' ', `requestedByContact`.`lastName` ) AS `requestedByName`", false];
        $query["join"] = [["contacts referredByContact", "referredByContact.id = legal_cases.referredBy", "left"], ["contacts requestedByContact", "requestedByContact.id = legal_cases.requestedBy", "left"], ["case_types", "case_types.id=legal_cases.case_type_id", "inner"], ["workflow_status", "workflow_status.id=legal_cases.case_status_id", "inner"], ["provider_groups", "provider_groups.id=legal_cases.provider_group_id", "inner"], ["user_profiles", "user_profiles.user_id=legal_cases.user_id", "left"], ["legal_case_stage_languages", "legal_case_stage_languages.legal_case_stage_id = legal_cases.legal_case_stage_id and legal_case_stage_languages.language_id = '" . $langId . "'", "left"], ["legal_case_client_position_languages", "legal_case_client_position_languages.legal_case_client_position_id = legal_cases.legal_case_client_position_id and legal_case_client_position_languages.language_id = '" . $langId . "'", "left"], ["legal_case_success_probability_languages", "legal_case_success_probability_languages.legal_case_success_probability_id = legal_cases.legal_case_success_probability_id and legal_case_success_probability_languages.language_id = '" . $langId . "'", "left"]];
        $query["where"] = ["legal_cases.id", $id];
        return $this->load_all($query);
    }
    public function load_all_cases_lawyers_contributors($id)
    {
        $query = [];
        $query["select"] = ["legal_cases_contacts.id, legal_cases_contacts.comments as comments, CASE WHEN contacts.father!='' THEN CONCAT(contacts.firstName, ' ', contacts.father, ' ', contacts.lastName) ELSE CONCAT(contacts.firstName, ' ', contacts.lastName) END as contactName, CONCAT( user_profiles.firstName, ' ', user_profiles.lastName ) as createdBy, legal_cases_contacts.createdOn, legal_case_contact_roles.name as role, contacts.isLawyer", false];
        $query["join"] = [["contacts", "contacts.id = legal_cases_contacts.contact_id", "left"], ["legal_case_contact_roles", "legal_case_contact_roles.id = legal_cases_contacts.legal_case_contact_role_id", "left"], ["user_profiles", "user_profiles.user_id = legal_cases_contacts.createdBy", "left"]];
        $query["where"] = [["legal_cases_contacts.case_id", $id], ["legal_cases_contacts.contactType", "contributor"]];
        $query["order_by"] = ["legal_cases_contacts.id desc"];
        $this->prep_query($query);
        $result = $this->ci->db->get("legal_cases_contacts");
        return $result->result_array();
    }
    public function load_visible_case($id)
    {
        if ($id < 1) {
            return false;
        }
        $fetched = false;
        $where_condition = $this->get_matter_privacy_conditions($this->logged_user_id, $this->override_privacy, false);
        $this->ci->db->where("id", $id);
        $this->ci->db->where($where_condition);
        $dbQuery = $this->ci->db->get($this->_table);
        if ($dbQuery && $dbQuery->num_rows()) {
            $this->set_fields($dbQuery->row_array());
            $fetched = true;
        }
        return $fetched;
    }
    public function load_visible_cases_ids($return = "array", $where = [])
    {
        $where_condition = $this->get_matter_privacy_conditions($this->logged_user_id, $this->override_privacy, false);
        $_table = $this->_table;
        $this->_table = "legal_cases";
        $this->ci->db->select("legal_cases.id");
        $this->ci->db->where($where_condition);
        if ($where) {
            $this->ci->db->where($where["column"] . " = " . $where["value"]);
        }
        $dbQuery = $this->ci->db->get($this->_table);
        $visibleIDs = [0];
        if ($dbQuery && $dbQuery->num_rows()) {
            foreach ($dbQuery->result() as $row) {
                $visibleIDs[] = $row->id;
            }
        }
        $this->_table = $_table;
        return $return == "array" ? $visibleIDs : "'" . implode("','", $visibleIDs) . "'";
    }
    public function k_load_all_cases_contacts($filter, $sortable)
    {
        $query = [];
        $response = [];
        $this->ci->load->model("language");
        $language_id = $this->ci->language->get_id_by_session_lang();
        $query["select"] = ["legal_cases_contacts.id, legal_cases_contacts.comments as comments,contacts.comments as contact_comments,\r\n         CASE WHEN contacts.father!='' THEN CONCAT(contacts.firstName, ' ', contacts.father, ' ', contacts.lastName) ELSE CONCAT(contacts.firstName, ' ', contacts.lastName) END as contactName,\r\n          legal_cases_contacts.contactType, CASE WHEN legal_case_contact_role_id IS NULL THEN 0 ELSE legal_case_contact_role_id END AS legal_case_contact_role_id, \r\n          contacts.id AS contactId,\r\n          contacts.phone, contacts.mobile, (SELECT GROUP_CONCAT( contact_emails.email SEPARATOR '; ' ) from contact_emails where contact_id = legal_cases_contacts.contact_id) AS email, contacts.address1, contacts.city, \r\n          contacts.country_id, contacts.internalReference, contacts.jobTitle,cl.name as country,cpu2.id as client_portal_id", false];
        if (is_array($filter) && isset($filter["filters"])) {
            foreach ($filter["filters"] as $_filter) {
                if (!empty($_filter["filters"][0]["field"]) && in_array($_filter["filters"][0]["field"], ["effectiveEffort", "estimatedEffort"])) {
                    $system_preferences = $this->ci->session->userdata("systemPreferences");
                    $this->ci->load->library("TimeMask");
                    $_filter["filters"][0]["value"] = $this->ci->timemask->humanReadableToHours($_filter["filters"][0]["value"]);
                }
                $this->prep_k_filter($_filter, $query, $filter["logic"]);
            }
            unset($_filter);
        }
        $query["join"] = [["contacts", "contacts.id = legal_cases_contacts.contact_id", "left"], ["legal_case_contact_roles", "legal_case_contact_roles.id = legal_cases_contacts.legal_case_contact_role_id", "left"], ["countries", "contacts.country_id = countries.id", "left"], ["countries_languages cl", "countries.id = cl.country_id and cl.language_id = " . $language_id, "left"], ["customer_portal_users cpu2", "contacts.id = cpu2.contact_id", "left"]];
        $query["where"][] = ["legal_cases_contacts.contactType", "contact"];
        $query["where"][] = ["contacts.status = 'Active'", NULL, false];
        $this->prep_query($query);
        $response["totalRows"] = $this->ci->db->count_all_results("legal_cases_contacts");
        if (is_array($sortable) && !empty($sortable)) {
            foreach ($sortable as $_sort) {
                $query["order_by"][] = [$_sort["field"], $_sort["dir"]];
            }
        } else {
            $query["order_by"] = ["legal_cases_contacts.id desc"];
        }
        if ($limit = $this->ci->input->post("take", true)) {
            $query["limit"] = [$this->ci->input->post("take", true), $this->ci->input->post("skip", true)];
        }
        $this->prep_query($query);
        $result = $this->ci->db->get("legal_cases_contacts");
        $response["data"] = $result->result_array();
        return $response;
    }
    public function k_load_all_cases_companies($company_type, $filter, $sortable)
    {
        $query = [];
        $response = [];
        $query["select"] = ["legal_cases_companies.id,   legal_cases_companies.comments as comments, \r\n            companies.name as companyName, \r\n            CASE WHEN legal_case_company_role_id IS NULL THEN 0 ELSE legal_case_company_role_id END AS legal_case_company_role_id, \r\n            companies.category AS companyCategory, \r\n            companies.id AS companyId,\r\n            companies.nationality_id,companies.registrationNb,companies.internalReference", false];
        if (is_array($filter) && isset($filter["filters"])) {
            foreach ($filter["filters"] as $_filter) {
                if (!empty($_filter["filters"][0]["field"]) && in_array($_filter["filters"][0]["field"], ["effectiveEffort", "estimatedEffort"])) {
                    $system_preferences = $this->ci->session->userdata("systemPreferences");
                    $this->ci->load->library("TimeMask");
                    $_filter["filters"][0]["value"] = $this->ci->timemask->humanReadableToHours($_filter["filters"][0]["value"]);
                }
                $this->prep_k_filter($_filter, $query, $filter["logic"]);
            }
            unset($_filter);
        }
        $query["join"] = [["companies", "companies.id = legal_cases_companies.company_id", "left"], ["legal_case_company_roles", "legal_case_company_roles.id = legal_cases_companies.legal_case_company_role_id", "left"]];
        $query["where"][] = ["companies.status = 'Active'", NULL, false];
        $query["where"][] = ["legal_cases_companies.companyType = '" . $company_type . "'", NULL, false];
        $this->prep_query($query);
        $response["totalRows"] = $this->ci->db->count_all_results("legal_cases_companies");
        if (is_array($sortable) && !empty($sortable)) {
            foreach ($sortable as $_sort) {
                $query["order_by"][] = [$_sort["field"], $_sort["dir"]];
            }
        } else {
            $query["order_by"] = ["legal_cases_companies.id desc"];
        }
        if ($limit = $this->ci->input->post("take", true)) {
            $query["limit"] = [$this->ci->input->post("take", true), $this->ci->input->post("skip", true)];
        }
        $this->prep_query($query);
        $result = $this->ci->db->get("legal_cases_companies");
        $response["data"] = $result->result_array();
        return $response;
    }
    public function k_load_all_cases_outsourcing_lawyers($filter, $sortable)
    {
        $query = [];
        $response = [];
        $query["select"] = ["legal_cases_contacts.id, legal_cases_contacts.comments as comments, CASE WHEN contacts.father!='' THEN CONCAT(contacts.firstName, ' ', contacts.father, ' ', contacts.lastName) ELSE CONCAT(contacts.firstName, ' ', contacts.lastName) END as contactName, CASE WHEN legal_case_contact_role_id IS NULL THEN 0 ELSE legal_case_contact_role_id END AS legal_case_contact_role_id, contacts.id AS contactId", false];
        if (is_array($filter) && isset($filter["filters"])) {
            foreach ($filter["filters"] as $_filter) {
                if (!empty($_filter["filters"][0]["field"]) && in_array($_filter["filters"][0]["field"], ["effectiveEffort", "estimatedEffort"])) {
                    $system_preferences = $this->ci->session->userdata("systemPreferences");
                    $this->ci->load->library("TimeMask");
                    $_filter["filters"][0]["value"] = $this->ci->timemask->humanReadableToHours($_filter["filters"][0]["value"]);
                }
                $this->prep_k_filter($_filter, $query, $filter["logic"]);
            }
            unset($_filter);
        }
        $query["join"] = [["contacts", "contacts.id = legal_cases_contacts.contact_id", "left"], ["legal_case_contact_roles", "legal_case_contact_roles.id = legal_cases_contacts.legal_case_contact_role_id", "left"]];
        $this->prep_query($query);
        $response["totalRows"] = $this->ci->db->count_all_results("legal_cases_contacts");
        if (is_array($sortable) && !empty($sortable)) {
            foreach ($sortable as $_sort) {
                $query["order_by"][] = [$_sort["field"], $_sort["dir"]];
            }
        } else {
            $query["order_by"] = ["legal_cases_contacts.id desc"];
        }
        if ($limit = $this->ci->input->post("take", true)) {
            $query["limit"] = [$this->ci->input->post("take", true), $this->ci->input->post("skip", true)];
        }
        $this->prep_query($query);
        $result = $this->ci->db->get("legal_cases_contacts");
        $response["data"] = $result->result_array();
        return $response;
    }
    public function count_case_outsourcing_lawyers($case_id)
    {
        $query = [];
        $query["select"] = ["legal_cases_contacts.id", false];
        $query["where"] = ["legal_cases_contacts.case_id = '" . $case_id . "' AND contactType = 'external lawyer'"];
        $this->prep_query($query);
        return $this->ci->db->count_all_results("legal_cases_contacts");
    }
    public function count_case_outsources($case_id)
    {
        $query = [];
        $query["select"] = ["legal_case_outsources.id", false];
        $query["where"] = ["legal_case_outsources.legal_case_id = '" . $case_id . "'"];
        $this->prep_query($query);
        return $this->ci->db->count_all_results("legal_case_outsources");
    }
    public function load_contacts_list($case_id)
    {
        $contacts = ["contact" => [], "external lawyer" => [], "judge" => [], "opponentLawyer" => []];
        if ($case_id < 1) {
            return $contacts;
        }
        $case_contacts = $this->ci->db->select(["contacts.id as id, CASE WHEN contacts.father!='' THEN CONCAT(contacts.firstName, ' ', contacts.father, ' ', contacts.lastName) ELSE CONCAT(contacts.firstName, ' ', contacts.lastName) END AS name, contactType", false])->join("contacts", "contacts.id = legal_cases_contacts.contact_id", "inner")->where("legal_cases_contacts.case_id", $case_id)->order_by("contacts.firstName", "asc")->order_by("contacts.lastName", "asc")->get("legal_cases_contacts");
        if (!$case_contacts->num_rows()) {
            return $contacts;
        }
        foreach ($case_contacts->result() as $contact) {
            $contacts[$contact->contactType][(string) $contact->id] = $contact->name;
        }
        return $contacts;
    }
    public function get_legal_case_related_container($id)
    {
        $table = $this->_table;
        $this->_table = "legal_case_containers";
        $query = [];
        $response = [];
        $this->ci->load->model("legal_case_container", "legal_case_containerfactory");
        $this->ci->legal_case_container = $this->ci->legal_case_containerfactory->get_instance();
        $query["select"] = ["legal_case_containers.id, legal_case_containers.subject", false];
        $query["join"] = [["legal_case_related_containers", "legal_case_related_containers.legal_case_container_id = legal_case_containers.id", "left"]];
        $query["where"] = ["legal_case_related_containers.legal_case_id", $id];
        $query["order_by"] = ["legal_case_related_containers.id", SORT_ASC];
        $query["limit"] = "1";
        $response["data"] = $this->load($query);
        $this->_table = $table;
        return $response;
    }
    public function count_legal_case_related_containers($id)
    {
        $table = $this->_table;
        $this->_table = "legal_case_related_containers";
        $query = [];
        $response = [];
        $query["select"] = ["count(id) AS totalRows", false];
        $query["where"] = ["legal_case_id", $id];
        $response["data"] = $this->load($query);
        $this->_table = $table;
        return $response;
    }
    public function get_count_by_a_b_numbers($stringQuery, $query, $grouping, $table)
    {
        return $this->_get_count_by_a_b_numbers($stringQuery, $query, $grouping, $table);
    }
    public function get_count_by_a_b_numbers_roll($stringQuery, $query, $grouping, $table)
    {
        return $this->_get_count_by_a_b_numbers($stringQuery, $query, $grouping, $table);
    }
    public function load_visible_related_containers($id)
    {
        $table = $this->_table;
        $this->_table = "legal_case_containers";
        $query = [];
        $response = [];
        $query["select"] = ["legal_case_containers.id", false];
        $query["join"] = [["legal_case_related_containers", "legal_case_related_containers.legal_case_container_id = legal_case_containers.id", "left"]];
        $query["where"][] = ["legal_case_related_containers.legal_case_id", $id];
        $query["where"][] = ["legal_case_containers.visible_in_cp", 1];
        $response = $this->load_all($query);
        $this->_table = $table;
        return $response;
    }
    private function _get_count_by_a_b_numbers($stringQuery, $query, $grouping, $table)
    {
        if (!(isset($grouping[0]) && isset($grouping[1])) && isset($grouping[0]["field"]) && isset($grouping[1]["field"])) {
            return [];
        }
        if (isset($query["limit"])) {
            unset($query["limit"]);
        }
        $_table = $this->_table;
        $this->_table = $table;
        $a = explode(".", $grouping[0]["field"]);
        $a = $a[1];
        $b = explode(".", $grouping[1]["field"]);
        $b = $b[1];
        $qur = $this->ci->db->query("select lc." . $a . " as a, lc." . $b . " as b, COUNT(0) AS totalCount from (" . $stringQuery . ") as lc group by lc." . $a . ", lc." . $b . " order by lc." . $a . ", lc." . $b);
        $queryResult = $qur->result_array();
        $results = [];
        foreach ($queryResult as $row) {
            $results[1 * $row["a"]][$row["b"]] = $row["totalCount"];
        }
        foreach ($results as $k => $v) {
            $results[1 * $k]["total"] = array_sum($v);
        }
        $this->_table = $_table;
        return $results;
    }
    public function get_count_by_a_b_text($query, $grouping, $table)
    {
        if (!(isset($grouping[0]) && isset($grouping[1])) && isset($grouping[0]["field"]) && isset($grouping[1]["field"])) {
            return [];
        }
        if (isset($query["limit"])) {
            unset($query["limit"]);
        }
        $_table = $this->_table;
        $this->_table = $table;
        $a = $grouping[0]["field"];
        $b = $grouping[1]["field"];
        $query["select"] = $a . " as a, " . $b . " as b, COUNT(0) AS totalCount";
        $query["group_by"] = $a . ", " . $b;
        $query["order_by"] = $a . ", " . $b;
        $queryResult = $this->load_all($query);
        $results = [];
        foreach ($queryResult as $row) {
            $results[$row["a"]][$row["b"]] = $row["totalCount"];
        }
        foreach ($results as $k => $v) {
            $results[$k]["total"] = array_sum($v);
        }
        $this->_table = $_table;
        return $results;
    }
    public function k_load_all_legal_case_expenses($id, $sortable, $my_expenses = false, $user_accounts = "")
    {
        $query = [];
        $response = [];
        $moneyLanguage = $this->ci->user_preference->get_value("money_language");
        $table = $this->_table;
        $this->_table = "expenses_full_details";
        $query["select"] = ["expenses_full_details.id, expenses_full_details.organization_id,\r\n\t\t\texpenses_full_details.case_id, expenses_full_details.dated, expenses_full_details.voucherType,\r\n\t\t\texpenses_full_details.refNum, expenses_full_details.referenceNum, expenses_full_details.attachment,\r\n\t\t\texpenses_full_details.description, expenses_full_details.caseID, expenses_full_details.expenseID,\r\n\t\t\texpenses_full_details.amount, expenses_full_details.billingStatus,  expenses_full_details.paidThroughID,\r\n\t\t\texpenses_full_details.paidThroughAccount, expenses_full_details.currency, expenses_full_details.currency_id,\r\n\t\t\texpenses_full_details.expenseCategory" . $moneyLanguage . " as expenseCategory, expenses_full_details.expenseCategoryId,\r\n\t\t\torganizations.name AS organizationName, expenses_full_details.createdOn, expenses_full_details.createdBy,\r\n\t\t\texpenses_full_details.modifiedOn, expenses_full_details.modifiedBy, expenses_full_details.createdByName,\r\n\t\t\texpenses_full_details.clientID,expenses_full_details.clientName AS clientName,expenses_full_details.task,expenses_full_details.task_id,expenses_full_details.hearing,expenses_full_details.event,\r\n\t\t\texpenses_full_details.modifiedByName,expenses_full_details.caseCategory, expenses_full_details.status", false];
        $query["join"] = [["organizations", "organizations.id = expenses_full_details.organization_id", "left"], ["legal_cases", "legal_cases.id = expenses_full_details.case_id", "inner"]];
        $query["where"][] = ["expenses_full_details.case_id = '" . $id . "' AND expenses_full_details.voucherType = 'EXP' AND (expenses_full_details.clientID = legal_cases.client_id OR billingStatus='internal' OR legal_cases.client_id IS NULL)"];
        if ($my_expenses) {
            $query["where_in"][] = ["expenses_full_details.paidThroughID", 0 < count($user_accounts) ? $user_accounts : ""];
        }
        $this->prep_query($query);
        $response["totalRows"] = $this->ci->db->count_all_results($this->_table);
        if (is_array($sortable) && !empty($sortable)) {
            foreach ($sortable as $_sort) {
                $query["order_by"][] = [$_sort["field"], $_sort["dir"]];
            }
        } else {
            $query["order_by"] = ["expenses_full_details.dated desc"];
        }
        $response["data"] = parent::load_all($query);
        $this->_table = $table;
        return $response;
    }
    public function k_load_all_legal_case_time_tracking($id, $sortable, $filter = [], $organization_id = 0, $only_log_rate = 0, $my_time_logs = false)
    {
        $query = [];
        $response = [];
        $this->ci->load->model("language");
        $lang_id = $this->ci->language->get_id_by_session_lang();
        $system_preferences = $this->ci->session->userdata("systemPreferences");
        $user_rate_per_entities = unserialize($system_preferences["userRatePerHour"]);
        $this->ci->load->model("organization", "organizationfactory");
        $this->ci->organization = $this->ci->organizationfactory->get_instance();
        $organizations = $this->ci->organization->load_list();
        ksort($organizations);
        $organization_id = 0 < $organization_id ? $organization_id : ($this->ci->user_preference->get_value("organization") ? $this->ci->user_preference->get_value("organization") : current(array_keys($organizations)));
        $user_rate_per_hour = isset($user_rate_per_entities[$organization_id]) && $user_rate_per_entities[$organization_id] ? $user_rate_per_entities[$organization_id] : 0;
        $user_rate_per_hour = number_format((double) $user_rate_per_hour, 2, ".", "");
        if (empty($organization_id)) {
            $this->ci->load->model("organization", "organizationfactory");
            $this->ci->organization = $this->ci->organizationfactory->get_instance();
            $organizations = $this->ci->organization->load_list();
            ksort($organizations);
            $organization_id = $this->ci->user_preference->get_value("organization") ? $this->ci->user_preference->get_value("organization") : current(array_keys($organizations));
        }
        $table = $this->_table;
        $this->_table = "user_activity_logs_full_details as ual";
        $query["select"][] = ["ual.id, ual.user_id, task_id, legal_case_id, effectiveEffort, ual.comments, createdBy, createdOn, taskId, \r\n        taskSummary, task_title, legalCaseId, legalCaseSummary, worker, inserter, billingStatus, \r\n        logDate,time_types.name as timeTypeName, time_internal_statuses_languages.name as timeInternalStatusName,timeStatus, ual.clientId, ual.clientName, ual.allRecordsClientName, ual.timeTypeId, ual.timeInternalStatusId, ual.rate_system", false];
        if (!$only_log_rate) {
            $query["select"][] = ["IF(ual.rate is NULL, IF(urphpc.ratePerHour IS NULL, IF(cs.rate_per_hour IS NULL, IF(urph.ratePerHour IS NULL, " . $user_rate_per_hour . ", urph.ratePerHour), cs.rate_per_hour),\r\n         urphpc.ratePerHour), ual.rate) as ratePerHour", false];
        } else {
            $query["select"][] = ["IF(ual.rate is NULL, '', ual.rate) as ratePerHour", false];
            $query["select"][] = ["IF(urphpc.ratePerHour IS NULL, IF(cs.rate_per_hour IS NULL, IF(urph.ratePerHour IS NULL, " . $user_rate_per_hour . ", urph.ratePerHour), cs.rate_per_hour),\r\n         urphpc.ratePerHour) as entityRatePerHour", false];
        }
        $query["join"] = [["time_types_languages as time_types", "time_types.type = ual.timeTypeId AND time_types.language_id = " . $lang_id, "left"], ["time_internal_statuses_languages", "time_internal_statuses_languages.internal_status = ual.timeInternalStatusId AND time_internal_statuses_languages.language_id = " . $lang_id, "left"], ["user_rate_per_hour urph", "urph.user_id = ual.user_id AND urph.organization_id = " . $organization_id, "left"], ["case_rate cs", "ual.legal_case_id = cs.case_id AND cs.organization_id = " . $organization_id, "left"], ["user_rate_per_hour_per_case urphpc", "urphpc.user_id = ual.user_id and urphpc.case_id = ual.legal_case_id AND urphpc.organization_id = " . $organization_id, "left"]];
        $query["where"][] = ["(ual.legal_case_id = " . $id . " OR ual.task_id IN (Select tasks.id from tasks where tasks.legal_case_id =" . $id . "))"];
        if ($my_time_logs) {
            $query["where"][] = ["ual.user_id", $this->ci->session->userdata("AUTH_user_id")];
        }
        if (!empty($filter) && is_array($filter) && isset($filter["filters"])) {
            foreach ($filter["filters"] as $_filter) {
                $this->prep_k_filter($_filter, $query, $filter["logic"]);
            }
            unset($_filter);
        }
        $this->prep_query($query);
        $response["totalRows"] = $this->ci->db->count_all_results($this->_table);
        if (is_array($sortable) && !empty($sortable)) {
            foreach ($sortable as $_sort) {
                $query["order_by"][] = [$_sort["field"], $_sort["dir"]];
            }
        } else {
            $query["order_by"] = ["ual.logDate desc"];
        }
        if ($limit = $this->ci->input->post("take", true)) {
            $query["limit"] = [$this->ci->input->post("take", true), $this->ci->input->post("skip", true)];
        }
        $response["data"] = $this->load_all($query);
        $this->_table = $table;
        return $response;
    }
    public function k_load_all_legal_case_expenses_per_client($cases, $client_id, $client_account_id)
    {
        $cases = "'" . implode("','", $cases) . "'";
        $moneyLanguage = $this->ci->user_preference->get_value("money_language");
        $table = $this->_table;
        $this->_table = "expenses_full_details";
        $query["select"] = ["expenses.expense_account as expense_account,expenses_full_details.*,expenses_full_details.expenseCategory" . $moneyLanguage . " as expensesCategoryName", false];
        $query["join"] = ["expenses", "expenses.voucher_header_id = expenses_full_details.id", "inner"];
        $query["where"] = ["expenses_full_details.case_id IN (" . $cases . ") AND expenses_full_details.organization_id = '" . $this->ci->session->userdata("organizationID") . "' AND ((expenses_full_details.billingStatus = 'to-invoice' AND expenses_full_details.clientAccountID = '" . $client_account_id . "') OR (expenses_full_details.billingStatus = 'not-set' AND expenses_full_details.clientID = '" . $client_id . "')) AND expenses_full_details.status = 'approved' "];
        $query["order_by"] = ["expenses_full_details.dated ASC"];
        $response = $this->load_all($query);
        $this->_table = $table;
        return $response;
    }
    public function case_values_per_client_name($pagingOn = false, $filter = [])
    {
        $_table = $this->_table;
        $this->_table = "legal_cases";
        $query = [];
        $response = [];
        $query["select"] = ["sum(legal_cases.caseValue) as caseValues,clients_view.name as clientName,legal_cases.client_id", false];
        $query["join"] = [["clients_view", "clients_view.id = legal_cases.client_id AND clients_view.model = 'clients'", "inner"]];
        if (is_array($filter) && isset($filter["filters"])) {
            foreach ($filter["filters"] as $_filter) {
                $this->prep_k_filter($_filter, $query, $filter["logic"]);
            }
            unset($_filter);
        }
        $query["where"][] = ["legal_cases.client_id != 'null'"];
        $query["group_by"] = ["legal_cases.client_id"];
        $query["where"][] = $this->get_matter_privacy_conditions($this->logged_user_id, $this->override_privacy);
        $query["order_by"] = ["caseValues desc"];
        if ($limit = $this->ci->input->post("take", true)) {
            $query["limit"] = [$this->ci->input->post("take", true), $this->ci->input->post("skip", true)];
            $paginationConf = ["urlPrefix" => ""];
            $paginationConf["inPage"] = $this->ci->input->post("take", true);
        }
        $totalRowsQuery = ["select" => ["count(*) as numRows"]];
        $totalRowsQuery["where"][] = $this->get_matter_privacy_conditions($this->logged_user_id, $this->override_privacy);
        $totalRowsQueryResults = $this->load($totalRowsQuery);
        $response["totalRows"] = $totalRowsQueryResults["numRows"];
        $response["result"] = $pagingOn ? parent::paginate($query, $paginationConf) : parent::load_all($query);
        return $response;
    }
    public function case_values_per_client_name_micro($pagingOn = false, $filter = [])
    {
        $query = $this->get_legal_cases_grid_query_web(NULL, NULL, "", false, true);
        if (isset($query["select"])) {
            unset($query["select"]);
        }
        $response = [];
        $this->ci->load->model("language");
        $langId = $this->ci->language->get_id_by_session_lang();
        $query["select"] = ["if(isnull(`clients`.`company_id`), (case                                                                           when (`cont`.`father` <> ' ') then concat_ws(' ', `cont`.`firstName`,                                                                                                                        `cont`.`father`,                                                                                                                        `cont`.`lastName`)                                                                           else concat_ws(' ', `cont`.`firstName`, `cont`.`lastName`) end),                                      `comp`.`name`)                                                           AS `clientName`,                                    caseValuesSummation(`legal_cases`.`client_id`) as totalCaseValues,                                    legal_cases.client_id,                                    legal_cases.id,                                    `case_types`.`name`  AS `type`,                                    legal_cases.subject,                                    legal_cases.internalReference,                                    legal_cases.arrivalDate,                                    legal_cases.caseArrivalDate,                                    legal_cases.closedOn,                                    `workflow_status`.`name`  AS `status`,                                    legal_cases.statusComments,                                    legal_case_stage_languages.name,                                    legal_cases.caseValue,                                    (SELECT GROUP_CONCAT(CASE                                                             WHEN `legal_case_opponents`.`opponent_member_type` IS NULL                                                                 THEN NULL                                                             ELSE (CASE                                                                       WHEN `opponent_positions`.`name` != '' THEN (CASE                                                                                                                        WHEN `legal_case_opponents`.`opponent_member_type` = 'company'                                                                                                                            THEN CONCAT(`opponentCompany`.`name`, ' - ', `opponent_positions`.`name`)                                                                                                                        ELSE (CASE                                                                                                                                  WHEN `opponentContact`.`father` != ''                                                                                                                                      THEN CONCAT(                                                                                                                                          `opponentContact`.`firstName`,                                                                                                                                          ' ',                                                                                                                                          `opponentContact`.`father`,                                                                                                                                          ' ',                                                                                                                                          `opponentContact`.`lastName`,                                                                                                                                          ' - ',                                                                                                                                          `opponent_positions`.`name`)                                                                                                                                  ELSE CONCAT(                                                                                                                                          `opponentContact`.`firstName`,                                                                                                                                          ' ',                                                                                                                                          `opponentContact`.`lastName`,                                                                                                                                          ' - ',                                                                                                                                          `opponent_positions`.`name`) END) END)                                                                       ELSE (CASE                                                                                 WHEN `legal_case_opponents`.`opponent_member_type` = 'company'                                                                                     THEN `opponentCompany`.`name`                                                                                 ELSE (CASE                                                                                           WHEN `opponentContact`.`father` != ''                                                                                               THEN CONCAT(                                                                                                   `opponentContact`.`firstName`,                                                                                                   ' ',                                                                                                   `opponentContact`.`father`,                                                                                                   ' ',                                                                                                   `opponentContact`.`lastName`)                                                                                           ELSE CONCAT(                                                                                                   `opponentContact`.`firstName`,                                                                                                   ' ',                                                                                                   `opponentContact`.`lastName`) END) END) END) END                                                         order by `legal_case_opponents`.`case_id` ASC SEPARATOR ', ')                                                       FROM `legal_case_opponents`                                                                  LEFT JOIN `opponents` ON `opponents`.`id` = `legal_case_opponents`.`opponent_id`                                                                  LEFT JOIN `companies` AS `opponentCompany`                                                                            ON `opponentCompany`.`id` = `opponents`.`company_id` AND                                                                               `legal_case_opponents`.`opponent_member_type` = 'company'                                                                  LEFT JOIN `contacts` AS `opponentContact`                                                                            ON `opponentContact`.`id` = `opponents`.`contact_id` AND                                                                               `legal_case_opponents`.`opponent_member_type` = 'contact'                                                                  LEFT JOIN `legal_case_opponent_position_languages` AS `opponent_positions`                                                                            ON `opponent_positions`.`legal_case_opponent_position_id` =                                                                               `legal_case_opponents`.`opponent_position` and                                                                               `opponent_positions`.`language_id` = '" . $langId . "'\r\n                                                       WHERE `legal_case_opponents`.`case_id` = `legal_cases`.`id`)  AS `opponentNames`", false];
        if (is_array($filter) && isset($filter["filters"])) {
            foreach ($filter["filters"] as $_filter) {
                $this->prep_k_filter($_filter, $query, $filter["logic"]);
            }
            unset($_filter);
        }
        $query["where"][] = ["legal_cases.client_id >", 0];
        $query["where"][] = $this->get_matter_privacy_conditions($this->logged_user_id, $this->override_privacy);
        $query["order_by"] = ["totalCaseValues desc"];
        $query["group_by"] = ["legal_cases.id"];
        if ($limit = $this->ci->input->post("take", true)) {
            $query["limit"] = [$this->ci->input->post("take", true), $this->ci->input->post("skip", true)];
            $paginationConf = ["urlPrefix" => ""];
            $paginationConf["inPage"] = $this->ci->input->post("take", true);
        }
        $totalRowsQuery = ["select" => ["count(*) as numRows"]];
        $totalRowsQuery["where"][] = $this->get_matter_privacy_conditions($this->logged_user_id, $this->override_privacy);
        $totalRowsQueryResults = $this->load($totalRowsQuery);
        $response["totalRows"] = $totalRowsQueryResults["numRows"];
        $response["result"] = $pagingOn ? parent::paginate($query, $paginationConf) : parent::load_all($query);
        return $response;
    }
    public function load_cases_by_client_id($client_id)
    {
        $query = [];
        $query["select"] = ["legal_cases.id, concat( '" . $this->modelCode . "', legal_cases.id) as case_id, concat( '" . $this->modelCode . "', legal_cases.id, ' - ', SUBSTR(legal_cases.subject, 1, 45) ) as caseId, legal_cases.subject, legal_cases.category as case_category, users.username as assignee, case_types.name as practice_area, firstName, lastName", false];
        $query["join"] = [["users", "users.id = legal_cases.user_id", "left"], ["user_profiles", "user_profiles.user_id = users.id", "left"], ["case_types", "case_types.id = legal_cases.case_type_id", "left"]];
        $query["where"][] = ["legal_cases.client_id", $client_id];
        $query["where"][] = $this->get_matter_privacy_conditions($this->logged_user_id, $this->override_privacy);
        $query["order_by"] = ["legal_cases.id desc"];
        return $this->load_all($query);
    }
    public function cases_by_tiers($filter, $sortable, $pagingOn)
    {
        $query = $this->get_legal_cases_grid_query_web(NULL, NULL, "", false, true);
        $this->ci->load->model("language");
        $langId = $this->ci->language->get_id_by_session_lang();
        $query["select"] = ["legal_cases.*,concat('M', `legal_cases`.`id`) AS `caseID`,         legal_case_stage_languages.name as caseStage,         legal_case_client_position_languages.name as caseClientPosition,         legal_case_litigation_details.id AS stage_id,         (SELECT GROUP_CONCAT(CASE                                 WHEN `legal_case_opponents`.`opponent_member_type` IS NULL                                     THEN NULL                                 ELSE (CASE                                           WHEN `opponent_positions`.`name` != '' THEN (CASE                                                                                            WHEN `legal_case_opponents`.`opponent_member_type` = 'company'                                                                                                THEN CONCAT(`opponentCompany`.`name`, ' - ', `opponent_positions`.`name`)                                                                                            ELSE (CASE                                                                                                      WHEN `opponentContact`.`father` != ''                                                                                                          THEN CONCAT(                                                                                                              `opponentContact`.`firstName`,                                                                                                              ' ',                                                                                                              `opponentContact`.`father`,                                                                                                              ' ',                                                                                                              `opponentContact`.`lastName`,                                                                                                              ' - ',                                                                                                              `opponent_positions`.`name`)                                                                                                      ELSE CONCAT(                                                                                                              `opponentContact`.`firstName`,                                                                                                              ' ',                                                                                                              `opponentContact`.`lastName`,                                                                                                              ' - ',                                                                                                              `opponent_positions`.`name`) END) END)                                           ELSE (CASE                                                     WHEN `legal_case_opponents`.`opponent_member_type` = 'company'                                                         THEN `opponentCompany`.`name`                                                     ELSE (CASE                                                               WHEN `opponentContact`.`father` != ''                                                                   THEN CONCAT(                                                                       `opponentContact`.`firstName`,                                                                       ' ',                                                                       `opponentContact`.`father`,                                                                       ' ',                                                                       `opponentContact`.`lastName`)                                                               ELSE CONCAT(                                                                       `opponentContact`.`firstName`,                                                                       ' ',                                                                       `opponentContact`.`lastName`) END) END) END) END                             order by `legal_case_opponents`.`case_id` ASC SEPARATOR ', ')                           FROM `legal_case_opponents`                                      LEFT JOIN `opponents` ON `opponents`.`id` = `legal_case_opponents`.`opponent_id`                                      LEFT JOIN `companies` AS `opponentCompany`                                                ON `opponentCompany`.`id` = `opponents`.`company_id` AND                                                   `legal_case_opponents`.`opponent_member_type` = 'company'                                      LEFT JOIN `contacts` AS `opponentContact`                                                ON `opponentContact`.`id` = `opponents`.`contact_id` AND                                                   `legal_case_opponents`.`opponent_member_type` = 'contact'                                      LEFT JOIN `legal_case_opponent_position_languages` AS `opponent_positions`                                                ON `opponent_positions`.`legal_case_opponent_position_id` =                                                   `legal_case_opponents`.`opponent_position` and                                                   `opponent_positions`.`language_id` = '" . $langId . "'\r\n                           WHERE `legal_case_opponents`.`case_id` = `legal_cases`.`id`)  AS `opponentNames`", false];
        $subjectValue = "";
        if (!empty($filter["caseSubject"])) {
            $subjectValue = " and legal_cases.subject LIKE '%" . $filter["caseSubject"] . "%'";
        }
        if ((!empty($filter["range2"]) || $filter["range2"] == 0) && $filter["range2"] != "NULL") {
            $query["where"][] = ["legal_cases.caseValue between  '" . $filter["range2"] . "' and  '" . $filter["range1"] . "' and  legal_cases.case_type_id=" . $filter["case_type_id"] . $subjectValue];
        } else {
            if (empty($filter["range2"]) || $filter["range2"] == "NULL") {
                $query["where"][] = ["legal_cases.caseValue >  '" . $filter["range1"] . "'  and  legal_cases.case_type_id=" . $filter["case_type_id"] . $subjectValue];
            }
        }
        $query["group_by"] = ["legal_cases.id"];
        if (is_array($sortable) && !empty($sortable)) {
            foreach ($sortable as $_sort) {
                $query["order_by"][] = [$_sort["field"], $_sort["dir"]];
            }
        } else {
            $query["order_by"] = ["legal_cases.id desc"];
        }
        $this->prep_query($query);
        $result["totalRows"] = $this->ci->db->get($this->get("_table"))->num_rows();
        if ($limit = $this->ci->input->post("take", true)) {
            $query["limit"] = [$this->ci->input->post("take", true), $this->ci->input->post("skip", true)];
            $paginationConf = ["urlPrefix" => ""];
            $paginationConf["inPage"] = $this->ci->input->post("take", true);
        }
        $result["data"] = $pagingOn ? parent::paginate($query, $paginationConf) : parent::load_all($query);
        return $result;
    }
    public function api_lookup($term, $category, $loggedUser, $overridePrivacy, $cpFeatureEnabled = false)
    {
        $configList = ["key" => "legal_cases.id", "value" => "name"];
        if ($cpFeatureEnabled) {
            $configQury["select"] = ["legal_cases.id, CONCAT('" . $this->modelCode . "',legal_cases.id) as caseID,legal_cases.subject as subject, CASE WHEN legal_cases.channel = 'CP' THEN 'true' ELSE 'false' END as comingFromCP,legal_cases.client_id,clients_view.name as clientName,legal_cases.timeTrackingBillable as is_time_tracking_billable, legal_cases.category, workflow_status.name as status, concat(`modified`.`firstName`, ' ', `modified`.`lastName`) AS `modifiedByName`, concat(`user_profiles`.`firstName`, ' ', `user_profiles`.`lastName`) AS `createdByName`", false];
        } else {
            $configQury["select"] = ["legal_cases.id, CONCAT('" . $this->modelCode . "',legal_cases.id) as caseID,legal_cases.subject as subject, 'false' as comingFromCP,legal_cases.client_id,clients_view.name as clientName,legal_cases.timeTrackingBillable as is_time_tracking_billable, legal_cases.category, workflow_status.name as status, concat(`modified`.`firstName`, ' ', `modified`.`lastName`) AS `modifiedByName`, concat(`user_profiles`.`firstName`, ' ', `user_profiles`.`lastName`) AS `createdByName`", false];
        }
        $configQury["join"] = [["clients_view", "clients_view.id=legal_cases.client_id AND clients_view.model = 'clients'", "left"], ["workflow_status", "workflow_status.id = legal_cases.case_status_id", "left"], ["user_profiles", "user_profiles.user_id = legal_cases.createdBy", "left"], ["user_profiles modified", "modified.user_id = legal_cases.modifiedBy", "left"]];
        $configQury["where"] = [];
        if (!empty($term)) {
            $configQury["where"] = [];
            if ($category != "") {
                if ($category == "Litigation_Matter") {
                    $configQury["where"][] = ["(legal_cases.category  = 'Litigation' OR legal_cases.category  = 'Matter') ", NULL, false];
                } else {
                    $configQury["where"][] = ["legal_cases.category  = '" . $category . "'", NULL, false];
                }
            }
            $modelCode = substr($term, 0, 1);
            $ID = substr($term, 1);
            if (strcasecmp($modelCode, $this->get("modelCode")) === 0 && is_numeric($ID)) {
                $qId = substr($term, 1);
                if (is_numeric($qId)) {
                    $configQury["where"][] = ["legal_cases.id = " . $qId, NULL, false];
                }
                $configQury["where"][] = $this->get_matter_privacy_conditions($loggedUser, $overridePrivacy);
                $configQury["where"][] = ["legal_cases.archived = '" . $this->notArchived . "'"];
            } else {
                $term = $this->ci->db->escape_like_str($term);
                $configQury["where"][] = ["(legal_cases.subject LIKE '%" . $term . "%' or legal_cases.internalReference LIKE '%" . $term . "%' or clients_view.name LIKE '%" . $term . "%')", NULL, false];
                $configQury["where"][] = $this->get_matter_privacy_conditions($loggedUser, $overridePrivacy);
                $configQury["where"][] = ["legal_cases.archived = '" . $this->notArchived . "'"];
            }
        }
        return $this->load_all($configQury, $configList);
    }
    public function api_load_visible_cases_ids($return, $loggedUser, $overridePrivacy)
    {
        $where_condition = $this->get_matter_privacy_conditions($loggedUser, $overridePrivacy, false);
        $_table = $this->_table;
        $this->_table = "legal_cases";
        $this->ci->db->select("legal_cases.id");
        $this->ci->db->where($where_condition);
        $dbQuery = $this->ci->db->get($this->_table);
        $visibleIDs = [0];
        if ($dbQuery && $dbQuery->num_rows()) {
            foreach ($dbQuery->result() as $row) {
                $visibleIDs[] = $row->id;
            }
        }
        $this->_table = $_table;
        return $return == "array" ? $visibleIDs : "'" . implode("','", $visibleIDs) . "'";
    }
    public function api_load_all_data($category, $loggedUserId, $overridePrivacy, $take = 20, $skip = 0, $term = "", $search_filters, $lang)
    {
        $query = $this->get_legal_cases_grid_query_api(NULL, NULL, "", false, $loggedUserId, $overridePrivacy, $lang, true, false);
        $query["select"] = ["legal_cases.id,                                   legal_cases.channel,                                    legal_cases.case_status_id,                                   legal_cases.case_type_id,                                   legal_cases.provider_group_id,                                   legal_cases.user_id,                                   legal_cases.contact_id,                                   legal_cases.client_id,                                   legal_cases.subject,                                   legal_cases.description,                                   legal_cases.latest_development,                                   legal_cases.priority,                                   legal_cases.caseArrivalDate,                                   legal_cases.arrivalDate,                                   legal_cases.dueDate,                                   legal_cases.closedOn,                                   legal_cases.statusComments,                                   legal_cases.category,                                   legal_cases.caseValue,                                   legal_cases.internalReference,                                   legal_cases.externalizeLawyers,                                    legal_cases.createdOn,                                    legal_cases.createdBy,                                    legal_cases.modifiedOn,                                    legal_cases.modifiedBy,                                    legal_cases.archived,                                    legal_cases.private,                                    legal_cases.timeTrackingBillable,                                    legal_cases.expensesBillable,                                    legal_cases.recoveredValue,                                    legal_cases.judgmentValue,                                    legal_cases.legal_case_client_position_id,                                    legal_cases.estimatedEffort,                                    `lcee`.`effectiveEffort`  AS `effectiveEffort`,                                    concat('M', `legal_cases`.`id`) AS `caseID`,                                    `workflow_status`.`name` AS status,                                    `case_types`.`name` as type,                                    `provider_groups`.`name` AS `providerGroup`,                                   concat(`up`.`firstName`, ' ', `up`.`lastName`) AS `assignee`,,                                    `legal_cases`.`archived` AS `archivedCases`,                                   `legal_case_litigation_details`.`court_type_id` AS `court_type_id`,                                    `legal_case_litigation_details`.`court_degree_id` AS `court_degree_id`,                                   `legal_case_litigation_details`.`court_region_id` AS `court_region_id`,                                   `legal_case_litigation_details`.`court_id` AS `court_id`,                                    `legal_case_litigation_details`.`sentenceDate` AS `sentenceDate`,,                                    if(isnull(`clients`.`company_id`), 'Person', 'Company')                                  AS `clientType`,                                   if(isnull(`clients`.`company_id`), (case                                                                           when (`cont`.`father` <> ' ') then concat_ws(' ', `cont`.`firstName`,                                                                                                                        `cont`.`father`,                                                                                                                        `cont`.`lastName`)                                                                           else concat_ws(' ', `cont`.`firstName`, `cont`.`lastName`) end),                                      `comp`.`name`)                                                                        AS `clientName`,                                    (case                                        when (`referredbycontact`.`father` <> '') then concat(`referredbycontact`.`firstName`, ' ',                                                                                              `referredbycontact`.`father`, ' ',                                                                                              `referredbycontact`.`lastName`)                                        else concat(`referredbycontact`.`firstName`, ' ',                                                    `referredbycontact`.`lastName`) end)                                    AS `referredByName`,                                   (case                                        when (`requestedbycontact`.`father` <> '') then concat(`requestedbycontact`.`firstName`, ' ',                                                                                               `requestedbycontact`.`father`, ' ',                                                                                               `requestedbycontact`.`lastName`)                                        else concat(`requestedbycontact`.`firstName`, ' ',                                                    `requestedbycontact`.`lastName`) end)                                   AS `requestedByName`,                                   legal_cases.legal_case_stage_id,                                    legal_cases.legal_case_success_probability_id,                                    up.status as assigneStatus,                                    legal_case_stage_languages.name as caseStage,                                   legal_case_client_position_languages.name as caseClientPosition,                                   (if(isnull(`clients`.`company_id`), concat_ws(' ', `cont`.`foreignFirstName`, `cont`.`foreignLastName`) ,                                    `comp`.`foreignName`)) as clientForeignName", false];
        $query["where"][] = ["legal_cases.category", $category];
        $query["where"][] = ["legal_cases.archived", "no"];
        if ($term != "") {
            $term = $this->ci->db->escape_like_str($term);
            $query["where"][] = [" ( legal_cases.subject LIKE '%" . $term . "%' or legal_cases.description LIKE '%" . $term . "%' )", NULL, false];
        }
        $query = $this->filter_builder($query, $search_filters);
        $query["group_by"] = ["legal_cases.id"];
        $query["order_by"] = ["legal_cases.id desc"];
        $query["limit"] = [$take, $skip];
        $response["data"] = parent::load_all($query);
        $response["totalRows"] = $this->get_legal_cases_count($query);
        return $response;
    }
    protected function filter_builder($query, $search_filters)
    {
        foreach ($search_filters as $key => $value) {
            if ($value != "") {
                if ($key === "dueDate") {
                    $value["operator"] = $value["operator"] == "" ? "=" : $value["operator"];
                    if ($value["operator"] === "between") {
                        $query["where"][] = [" ( legal_cases.dueDate >= '" . $value["date"]["from"] . "' AND legal_cases.dueDate <= '" . $value["date"]["to"] . "' )", NULL, false];
                    } else {
                        $query["where"][] = [" ( legal_cases.dueDate " . $value["operator"] . " '" . $value["date"] . "' )", NULL, false];
                    }
                } else {
                    if (gettype($value) === "array") {
                        if ($key === "priority") {
                            $value = implode("','", $value);
                            $query["where"][] = [" ( legal_cases." . $key . " in ('" . $value . "') )", NULL, false];
                        } else {
                            $value = implode(",", $value);
                            $query["where"][] = [" ( legal_cases." . $key . " in (" . $value . ") )", NULL, false];
                        }
                    } else {
                        if ($key === "company_id") {
                            $query["where"][] = [" ( com.id = '" . $value . "' )", NULL, false];
                        } else {
                            if ($key === "contact_id") {
                                $query["where"][] = [" ( conre.id = '" . $value . "' )", NULL, false];
                            } else {
                                if ($key === "opponent") {
                                    $query["where"][] = [" ( legal_case_opponents.opponent_id = '" . $value . "' )", NULL, false];
                                } else {
                                    $query["where"][] = [" ( legal_cases." . $key . " = '" . $value . "' )", NULL, false];
                                }
                            }
                        }
                    }
                }
            }
        }
        return $query;
    }
    public function api_load_all_related_companies($caseId)
    {
        $query = [];
        $response = [];
        $query["select"] = ["legal_cases_companies.id, legal_cases_companies.comments as comments, companies.name as companyName, CASE WHEN legal_case_company_role_id IS NULL THEN 0 ELSE legal_case_company_role_id END AS legal_case_company_role_id, companies.category AS companyCategory, companies.id AS companyId, legal_case_company_roles.name as roleName", false];
        $query["join"] = [["companies", "companies.id = legal_cases_companies.company_id", "left"], ["legal_case_company_roles", "legal_case_company_roles.id = legal_cases_companies.legal_case_company_role_id", "left"]];
        $query["where"][] = ["legal_cases_companies.case_id", $caseId];
        $query["where"][] = ["companies.status = 'Active'", NULL, false];
        $this->prep_query($query);
        $response["totalRows"] = $this->ci->db->count_all_results("legal_cases_companies");
        $query["order_by"] = ["legal_cases_companies.id desc"];
        $this->prep_query($query);
        $result = $this->ci->db->get("legal_cases_companies");
        $response["data"] = $result->result_array();
        return $response;
    }
    public function api_load_all_related_contacts($caseId)
    {
        $query = [];
        $response = [];
        $query["select"] = ["legal_cases_contacts.id, legal_cases_contacts.comments as comments, CASE WHEN contacts.father!='' THEN CONCAT(contacts.firstName, ' ', contacts.father, ' ', contacts.lastName) ELSE CONCAT(contacts.firstName, ' ', contacts.lastName) END as contactName, legal_cases_contacts.contactType, CASE WHEN legal_case_contact_role_id IS NULL THEN 0 ELSE legal_case_contact_role_id END AS legal_case_contact_role_id, contacts.id AS contactId, legal_case_contact_roles.name as roleName", false];
        $query["join"] = [["contacts", "contacts.id = legal_cases_contacts.contact_id", "left"], ["legal_case_contact_roles", "legal_case_contact_roles.id = legal_cases_contacts.legal_case_contact_role_id", "left"]];
        $query["where"][] = ["legal_cases_contacts.contactType", "contact"];
        $query["where"][] = ["legal_cases_contacts.case_id", $caseId];
        $query["where"][] = ["contacts.status = 'Active'", NULL, false];
        $this->prep_query($query);
        $response["totalRows"] = $this->ci->db->count_all_results("legal_cases_contacts");
        $query["order_by"] = ["legal_cases_contacts.id desc"];
        $this->prep_query($query);
        $result = $this->ci->db->get("legal_cases_contacts");
        $response["data"] = $result->result_array();
        return $response;
    }
    public function user_rates_per_cases_per_assignees($organizationID, $filter = [], $post_user_filter = false)
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
            $query["select"] = ["ual.id,\r\n\t\t\t\tCASE\r\n                   WHEN ual.rate IS NULL THEN (CASE\r\n                                                   WHEN urphpc.ratePerHour IS NULL THEN (CASE\r\n                                                                                             WHEN cs.rate_per_hour IS NULL\r\n                                                                                                 THEN (CASE\r\n                                                                                                           WHEN urph.ratePerHour IS NULL\r\n                                                                                                               THEN (CASE WHEN u.user_group_id IN (" . $userGroupsRate . ") THEN " . $userRatePerHour . " ELSE 0 END)\r\n                                                                                                           ELSE urph.ratePerHour END)\r\n                                                                                             ELSE cs.rate_per_hour END)\r\n                                                   ELSE urphpc.ratePerHour END)\r\n                   ELSE ual.rate END AS ratePerHour,\r\n\t\t\t\tual.user_id AS userId, ual.effectiveEffort, ual.comments, createdBy, createdOn, taskId, taskSummary, task_title, legalCaseId, legalCaseSummary, legalCaseDescription, worker, inserter, timeStatus, billingStatus, ual.clientId, ual.clientName, ual.matterInternalReference", false];
        } else {
            $query["select"] = ["ual.id,ual.legal_case_id, CASE WHEN ual.rate IS NULL THEN 0 ELSE ual.rate END AS ratePerHour,\r\n\t\t\t\tual.user_id AS userId, ual.effectiveEffort, ual.comments, createdBy, createdOn, taskId, taskSummary, task_title, legalCaseId, legalCaseSummary, legalCaseDescription, worker, inserter, timeStatus, billingStatus, ual.clientId, ual.clientName, ual.matterInternalReference", false];
        }
        if (is_array($filter) && isset($filter["filters"])) {
            foreach ($filter["filters"] as $_filter) {
                $this->prep_k_filter($_filter, $query, $filter["logic"]);
            }
            unset($_filter);
        }
        $query["join"] = [["users u", "u.id = ual.user_id", "left"], ["user_rate_per_hour urph", "urph.user_id = ual.user_id AND urph.organization_id = " . $organizationID, "left"], ["case_rate cs", "ual.legal_case_id = cs.case_id AND cs.organization_id = " . $organizationID, "left"], ["user_rate_per_hour_per_case urphpc", "urphpc.user_id = ual.user_id AND urphpc.organization_id = " . $organizationID . " AND urphpc.case_id = ual.legal_case_id", "left"]];
        $query["where"][] = ["ual.legal_case_id IS NOT NULL"];
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
    public function notifyTicketUser($ticketId, $ticketStatus, $userLoggedIn, $userProfile, $notifyUser, $notifyEmail, $systemNotification, $channel, $old_status)
    {
        $currentTime = date("Y-m-d H:i:s", time());
        $this->ci->legal_case->fetch($ticketId);
        $this->ci->workflow_status->fetch($ticketStatus);
        $new_status_name = $this->ci->workflow_status->get_field("name");
        $this->ci->workflow_status->fetch($old_status);
        $old_status_name = $this->ci->workflow_status->get_field("name");
        $case_subject = $this->get_field("subject");
        $comment = sprintf($this->ci->lang->line("userChangedCaseStatus"), $userProfile, $ticketId, $new_status_name, $currentTime, $case_subject);
        $this->ci->load->model("case_comment", "case_commentfactory");
        $this->ci->case_comment = $this->ci->case_commentfactory->get_instance();
        $this->ci->case_comment->set_field("case_id", $ticketId);
        $this->ci->case_comment->set_field("comment", nl2br($comment));
        $this->ci->case_comment->set_field("user_id", $userLoggedIn);
        $this->ci->case_comment->set_field("createdOn", $currentTime);
        $this->ci->case_comment->set_field("modifiedBy", $userLoggedIn);
        $this->ci->case_comment->set_field("createdByChannel", $channel);
        $this->ci->case_comment->set_field("modifiedByChannel", $channel);
        $this->ci->load->model("system_preference");
        $system_preferences = $this->ci->system_preference->get_key_groups();
        $cp_prefix = $system_preferences["CustomerPortalConfig"]["cpAppTitle"];
        $this->ci->load->model("email_notification_scheme");
        $model = $this->get("_table");
        $model_data["id"] = $ticketId;
        $this->ci->load->model("client");
        $client_info = $this->ci->client->fetch_client($this->get_field("client_id"));
        if ($this->ci->case_comment->insert()) {
            $send_email = true;
            if ($notifyUser && $channel != $this->get("portalChannel")) {
                $object = "legal_edit_ticket";
                $fromLoggedUser = $userProfile;
                $toProfileUser = $this->ci->email_notification_scheme->get_user_full_name($this->get_field("createdBy"), "customer_portal_users");
                $send_email = $this->ci->email_notification_scheme->get_hide_show_send_email_notification_by_trigger_action("legal_edit_ticket");
            }
            if ($channel == $this->get("portalChannel")) {
                $object = "cp_edit_ticket";
                $fromLoggedUser = $userProfile . "-" . $cp_prefix;
                $toProfileUser = $this->ci->email_notification_scheme->get_user_full_name($notifyUser);
            }
            if ($send_email) {
                $notifications_emails = $this->ci->email_notification_scheme->get_emails($object, $model, $model_data);
                extract($notifications_emails);
                $notificationsData = ["to" => $to_emails, "object" => $object, "object_id" => (int) $ticketId, "cc" => $cc_emails, "content" => ["userProfile" => $userProfile, "old_status_name" => $old_status_name, "new_status_name" => $new_status_name, "time" => $currentTime, "subject" => $case_subject], "fromLoggedUser" => $fromLoggedUser, "toProfileUser" => $toProfileUser, "priority" => $this->get_field("priority"), "assignee" => $this->ci->email_notification_scheme->get_user_full_name($this->get_field("user_id")), "file_reference" => $this->get_field("internalReference"), "client_name" => $client_info["name"], "objectModelCode" => $this->get("modelCode")];
                $systemNotification = isset($notifyUser) && $this->get_field("channel") != "CP" ? true : false;
                $this->notifyTicketUserByEmail($systemNotification, $notifyUser, $notifyEmail, $comment, true, $notificationsData);
            }
        }
    }
    public function notifyTicketUserByEmail($systemNotification, $userId, $userEmail, $comment, $sendEmail = true, $notificationsData = [])
    {
        if ($systemNotification) {
            $this->ci->load->model("notification", "notificationfactory");
            $this->ci->notification = $this->ci->notificationfactory->get_instance();
            $this->ci->notification->reset_fields();
            $this->ci->notification->set_field("status", "unseen");
            $this->ci->notification->set_field("message", $comment);
            $this->ci->notification->set_field("user_id", $userId);
            $this->ci->notification->insert();
            $this->ci->notification->reset_fields();
        }
        if ($sendEmail) {
            $this->ci->load->library("email_notifications");
            $this->ci->email_notifications->notify($notificationsData);
        }
    }
    public function systemHasUnassignedCustomerTickets()
    {
        $query = [];
        $query["select"] = ["legal_cases.id"];
        $query["where"][] = ["legal_cases.channel", $this->get("portalChannel")];
        $query["where"][] = ["user_id IS NULL"];
        $return = $this->load_all($query);
        return !empty($return) ? count($return) : 0;
    }
    public function check_if_case_deleted($case_id)
    {
        if ($case_id) {
            $this->fetch($case_id);
            $is_deleted = $this->get_field("isDeleted");
            return !empty($is_deleted) && $is_deleted == 1 ? true : false;
        }
        return false;
    }
    public function check_case_related_to_money($case_id)
    {
        $_table = $this->_table;
        $this->_table = "voucher_headers AS vh";
        $query = [];
        $query["select"] = ["vrc.legal_case_id"];
        $query["join"] = ["voucher_related_cases vrc", "vrc.voucher_header_id = vh.id", "inner"];
        $query["where"][] = ["vrc.legal_case_id", $case_id];
        $return = $this->load($query);
        $this->_table = $_table;
        return !empty($return) ? true : false;
    }
    public function get_time_trakcing_data($case_id)
    {
        $loadQuery = ["select" => "legal_cases.subject, lcee.effectiveEffort,legal_cases.estimatedEffort,legal_cases.category", "join" => [["legal_case_effective_effort AS lcee", "lcee.legal_case_id = legal_cases.id", "left"]], "where" => [["legal_cases.id = ", $case_id], ["legal_cases.isDeleted = ", 0]]];
        return $this->load($loadQuery);
    }
    public function log_delete_action($action, $userMaker)
    {
        $this->log_built_in_last_action($this->_fields["id"], $userMaker);
        $this->log_action($action, $this->_fields["id"]);
    }
    public function get_case_client($case_id)
    {
        $query["select"] = ["legal_cases.client_id, if(isnull(`clients`.`company_id`), (case when (`cont`.`father` <> ' ') then concat_ws(' ', `cont`.`firstName`,`cont`.`father`,`cont`.`lastName`) else concat_ws(' ', `cont`.`firstName`, `cont`.`lastName`) end),`comp`.`name`) AS `clientName`, (if(isnull(`clients`.`company_id`), concat_ws(' ', `cont`.`foreignFirstName`, `cont`.`foreignLastName`) ,`comp`.`foreignName`)) as clientForeignName", false];
        $query["join"] = [["`clients`", "`clients`.`id` = `legal_cases`.`client_id`", "left"], ["`companies` `comp`", "`comp`.`id` = `clients`.`company_id`", "left"], ["`contacts` `cont`", "`cont`.`id` = `clients`.`contact_id`", "left"]];
        $query["where"] = ["legal_cases.id", $case_id];
        $result = $this->load($query);
        return $result;
    }
    public function add_client_to_case($data)
    {
        if (array_key_exists("case_id", $data)) {
            $this->fetch($data["case_id"]);
        } else {
            if (array_key_exists("caseId", $data)) {
                $this->fetch($data["caseId"]);
            }
        }
        $case_client = $this->get_field("client_id");
        if (!$case_client && isset($data["client_id"]) && $data["client_id"]) {
            $this->set_field("client_id", $data["client_id"]);
            if ($this->update()) {
                $this->reset_fields();
                return true;
            }
        }
        return false;
    }
    public function update_validation_rules()
    {
        $this->validate["case_status_id"] = ["required" => ["required" => true, "allowEmpty" => false, "rule" => "numeric", "message" => $this->ci->lang->line("cannot_be_blank_rule")]];
    }
    public function load_client_trust_accounts($client_id)
    {
        $query = "SELECT DISTINCT accounts.trust_asset_account, accounts.trust_liability_account,VD.total_credit,VD.total_debit, accounts.organization_id, organizations.currency_id FROM deposits \r\n                INNER JOIN client_trust_accounts_relation as accounts ON accounts.id = deposits.client_trust_accounts_id\r\n                LEFT JOIN organizations ON organizations.id = accounts.organization_id\r\n                INNER JOIN(\r\n                SELECT voucher_details.account_id,SUM(CASE WHEN voucher_details.drCr = 'D' THEN (CASE WHEN voucher_details.local_amount IS NOT NULL THEN voucher_details.local_amount ELSE 0 END)ELSE 0 END ) AS total_debit,\r\n                SUM(CASE WHEN voucher_details.drCr = 'C' THEN (CASE WHEN voucher_details.local_amount IS NOT NULL THEN (voucher_details.local_amount) ELSE 0 END) ELSE 0 END ) AS total_credit from voucher_details group by voucher_details.account_id)\r\n                as VD on VD.account_id = accounts.trust_liability_account \r\n                WHERE accounts.client = '" . $client_id . "'";
        $records = $this->ci->db->query($query)->result_array();
        $system_preferences = $this->ci->session->userdata("systemPreferences");
        $this->ci->load->model("exchange_rate");
        $exchange_rates = $this->ci->exchange_rate->get_all_exchange_rates();
        $case_currency_id = $this->load_system_currency_id();
        $balance = 0;
        foreach ($records as $data) {
            $balance += ($data["total_credit"] * 1 - $data["total_debit"] * 1) * $exchange_rates[$data["organization_id"]][$data["currency_id"]] / $exchange_rates[$data["organization_id"]][$case_currency_id];
        }
        return number_format($balance, 2) . " " . $system_preferences["caseValueCurrency"];
    }
    public function load_system_currency_id()
    {
        $system_preferences = $this->ci->session->userdata("systemPreferences");
        $this->ci->load->model("country", "countryfactory");
        $this->ci->country = $this->ci->countryfactory->get_instance();
        $currencies = $this->ci->country->load_list(["where" => ["id in (" . $system_preferences["currencies"] . ")"]], ["key" => "currencyCode", "value" => "id"]);
        return $currencies[$system_preferences["caseValueCurrency"]];
    }
    public function load_client_amount_transactions($client_id = 0, $case_id = 0)
    {
        $_table = $this->_table;
        $this->_table = "voucher_headers";
        $query = ["select" => ["voucher_headers.organization_id,accounts.currency_id,SUM(payments.amount) AS payments_made,total-IFNULL(SUM(payments.amount), 0.0) AS balance_due,invoices.paidStatus as invoice_status", false], "where" => [["invoices.paidStatus!= 'draft' AND invoices.paidStatus!= 'cancelled'", NULL, false], ["model_id", $client_id], ["voucher_related_cases.legal_case_id", $case_id]], "join" => [["voucher_related_cases", "voucher_related_cases.voucher_header_id = voucher_headers.id", "left"], ["invoice_headers invoices", "invoices.voucher_header_id = voucher_headers.id", "inner"], ["invoice_payment_invoices payments", "payments.invoice_header_id = invoices.id", "left"], ["accounts_details_lookup accounts", "accounts.id = invoices.account_id", "inner"]], "group_by" => ["voucher_headers.id"]];
        $results = $this->load_all($query);
        $system_preferences = $this->ci->session->userdata("systemPreferences");
        $this->ci->load->model("exchange_rate");
        $exchange_rates = $this->ci->exchange_rate->get_all_exchange_rates();
        $case_currency_id = $this->load_system_currency_id();
        $due_balance = $paid_balance = 0;
        foreach ($results as $record) {
            if ($record["invoice_status"] == "open" || $record["invoice_status"] == "partially paid") {
                $due_balance += $record["balance_due"] * 1 * $exchange_rates[$record["organization_id"]][$record["currency_id"]] / $exchange_rates[$record["organization_id"]][$case_currency_id];
            }
            if ($record["invoice_status"] == "paid" || $record["invoice_status"] == "partially paid") {
                $paid_balance += $record["payments_made"] * 1 * $exchange_rates[$record["organization_id"]][$record["currency_id"]] / $exchange_rates[$record["organization_id"]][$case_currency_id];
            }
        }
        $this->_table = $_table;
        return ["due_balance" => number_format($due_balance, 2) . " " . $system_preferences["caseValueCurrency"], "paid_balance" => number_format($paid_balance, 2) . " " . $system_preferences["caseValueCurrency"]];
    }
    public function load_client_billable_logs($client_id = 0, $case_id = 0)
    {
        $system_preferences = $this->ci->session->userdata("systemPreferences");
        $user_rate_per_entities = unserialize($system_preferences["userRatePerHour"]);
        $this->ci->load->model("organization", "organizationfactory");
        $this->ci->organization = $this->ci->organizationfactory->get_instance();
        $organizations = $this->ci->organization->load_list();
        ksort($organizations);
        $organization_id = $this->ci->user_preference->get_value("organization") ? $this->ci->user_preference->get_value("organization") : current(array_keys($organizations));
        $user_rate_per_hour = isset($user_rate_per_entities[$organization_id]) && $user_rate_per_entities[$organization_id] ? $user_rate_per_entities[$organization_id] : 0;
        $_table = $this->_table;
        $this->_table = "user_activity_logs as logs";
        $query = ["select" => ["logs.effectiveEffort, (SELECT currency_id from organizations where id = '" . $organization_id . "' ) as currency_id,\r\n                (CASE WHEN logs.rate is NULL THEN (CASE WHEN urphpc.ratePerHour IS NULL \r\n                THEN \r\n                (CASE WHEN cs.rate_per_hour IS NULL THEN (CASE WHEN urph.ratePerHour IS NULL THEN " . $user_rate_per_hour . " ELSE urph.ratePerHour END) ELSE cs.rate_per_hour END) \r\n                ELSE \r\n                urphpc.ratePerHour END) ELSE logs.rate END) as rate_per_hour", false], "where" => [["(status.log_invoicing_statuses IS NULL OR status.log_invoicing_statuses = 'non-billable' OR status.log_invoicing_statuses = 'to-invoice')"], ["logs.legal_case_id ", $case_id], ["logs.client_id ", $client_id], ["logs.timeStatus <>", "internal"]], "join" => [["user_activity_log_invoicing_statuses status", "status.id = logs.id", "left"], ["user_rate_per_hour urph", "urph.user_id = logs.user_id AND urph.organization_id = '" . $organization_id . "'", "left"], ["case_rate cs", "ual.legal_case_id = cs.case_id AND cs.organization_id = " . $organization_id, "left"], ["user_rate_per_hour_per_case as urphpc", "urphpc.user_id = logs.user_id and urphpc.case_id = logs.legal_case_id AND urphpc.organization_id = '" . $organization_id . "'", "left"]]];
        $results = $this->load_all($query);
        $this->ci->load->model("exchange_rate");
        $exchange_rates = $this->ci->exchange_rate->get_all_exchange_rates();
        $case_currency_id = $this->load_system_currency_id();
        $balance = 0;
        foreach ($results as $record) {
            $balance += $record["effectiveEffort"] * 1 * $record["rate_per_hour"] * 1 * $exchange_rates[$organization_id][$record["currency_id"]] / $exchange_rates[$organization_id][$case_currency_id];
        }
        $this->_table = $_table;
        return $balance;
    }
    public function load_client_billable_expenses($client_id = 0, $case_id = 0)
    {
        $query = "SELECT voucher_headers.organization_id, accounts.currency_id, expenses.amount\r\n                    FROM voucher_headers\r\n                    INNER JOIN expenses ON expenses.voucher_header_id = voucher_headers.id\r\n                    INNER JOIN accounts_details_lookup accounts ON accounts.id = expenses.paid_through\r\n                    INNER JOIN voucher_related_cases vrc ON vrc.voucher_header_id = voucher_headers.id\r\n                    WHERE expenses.client_id =  " . $client_id . "\r\n                    AND vrc.legal_case_id =  " . $case_id . " AND (expenses.billingStatus= 'not-set'\r\n                    OR (expenses.billingStatus= 'to-invoice' AND expenses.id NOT IN \r\n                    (SELECT expense_id from invoice_details INNER JOIN invoice_headers ON invoice_headers.id = invoice_details.invoice_header_id \r\n                    AND (invoice_headers.paidStatus = 'open' OR invoice_headers.paidStatus = 'paid' OR invoice_headers.paidStatus = 'partially paid') \r\n                    WHERE expense_id IS NOT NULL)))";
        $results = $this->ci->db->query($query)->result_array();
        $this->ci->load->model("exchange_rate");
        $exchange_rates = $this->ci->exchange_rate->get_all_exchange_rates();
        $case_currency_id = $this->load_system_currency_id();
        $balanace_due = 0;
        foreach ($results as $record) {
            $balanace_due += $record["amount"] * 1 * $exchange_rates[$record["organization_id"]][$record["currency_id"]] / $exchange_rates[$record["organization_id"]][$case_currency_id];
        }
        return $balanace_due;
    }
    public function inject_folder_templates($matter_id, $matter_category, $matter_type)
    {
        $matter_category = strtolower($matter_category);
        $this->ci->load->model("legal_case_folder_template", "legal_case_folder_templatefactory");
        $this->ci->legal_case_folder_template = $this->ci->legal_case_folder_templatefactory->get_instance();
        $folders = $this->ci->legal_case_folder_template->get_matter_folders($matter_category, $matter_type);
        $this->ci->load->library("dms");
        $this->ci->load->model("document_management_system", "document_management_systemfactory");
        $this->ci->document_management_system = $this->ci->document_management_systemfactory->get_instance();
        foreach ($folders as $folder) {
            $this->create_folders_recursively($matter_id, $folder);
        }
    }
    public function create_folders_recursively($matter_id, $folder, $lineage = "")
    {
        if (!empty($folder)) {
            $response = $this->ci->dms->create_folder(["module" => "case", "module_record_id" => $matter_id, "lineage" => $lineage, "name" => $folder["text"]]);
            if ($response["status"] && isset($response["id"]) && isset($folder["children"]) && !empty($folder["children"])) {
                foreach ($folder["children"] as $node) {
                    $this->ci->document_management_system->fetch($response["id"]);
                    $this->create_folders_recursively($matter_id, $node, $this->ci->document_management_system->get_field("lineage"));
                }
            }
        }
    }
    public function notify_related_users($case_id, $old_status, $transition, $logged_user = false)
    {
        $modified_on = $this->ci->legal_case->get_field("modifiedOn");
        $status_id = $this->ci->legal_case->get_field("case_status_id");
        $logged_user = $logged_user ?: $this->ci->session->userdata("AUTH_userProfileName");
        $this->ci->load->model("email_notification_scheme");
        if ($transition && $this->ci->email_notification_scheme->fetch(["trigger_action" => "transition_" . $transition])) {
            $object = "transition_" . $transition;
        } else {
            $object = "edit_case_status";
        }
        $this->ci->workflow_status->fetch($status_id);
        $new_status_name = $this->ci->workflow_status->get_field("name");
        $this->ci->workflow_status->fetch($old_status);
        $old_status_name = $this->ci->workflow_status->get_field("name");
        $model = $this->get("_table");
        $model_data["id"] = $case_id;
        $notifications_emails = $this->ci->email_notification_scheme->get_emails($object, $model, $model_data);
        extract($notifications_emails);
        $this->ci->load->model("client");
        $client_info = $this->ci->client->fetch_client($this->get_field("client_id"));
        $notifications_data = ["to" => array_filter($to_emails), "object" => "edit_case_status", "object_id" => (int) $case_id, "cc" => $cc_emails, "content" => ["modifier" => $logged_user, "from" => $old_status_name, "to" => $new_status_name, "on" => $modified_on, "assignee" => $this->ci->email_notification_scheme->get_user_full_name($this->get_field("user_id")), "file_reference" => $this->get_field("internalReference"), "client_name" => $client_info["name"], "caseSubject" => $this->get_field("subject")], "fromLoggedUser" => $logged_user, "objectModelCode" => $this->get("modelCode")];
        $this->ci->load->library("email_notifications");
        $this->ci->email_notifications->notify($notifications_data);
    }
    public function get_legal_cases_grid_query_web($filter, $sortable, $page_number, $pagingOn, $return_query = false, $is_litigation = false, $get_query_count = true)
    {
        $this->ci->db->_protect_identifiers = false;
        $this->ci->db->_force_escape_string_values = true;
        $this->ci->load->model("language");
        $lang_id = $this->ci->language->get_id_by_session_lang();
        $response = $this->get_legal_cases_grid_query_details($lang_id, $this->logged_user_id, $this->override_privacy, $filter, $sortable, $page_number, $pagingOn, true, $return_query, $is_litigation, $get_query_count);
        $this->ci->db->_protect_identifiers = true;
        $this->ci->db->_force_escape_string_values = false;
        return $response;
    }
    public function get_legal_cases_grid_query_api($filter, $sortable, $page_number, $pagingOn, $loggedUserId, $overridePrivacy, $language, $return_query = false, $is_litigation = false)
    {
        $this->ci->db->_protect_identifiers = false;
        $this->ci->db->_force_escape_string_values = true;
        $response = $this->get_legal_cases_grid_query_details($language, $loggedUserId, $overridePrivacy, $filter, $sortable, $page_number, $pagingOn, true, $return_query, $is_litigation);
        $this->ci->db->_protect_identifiers = true;
        $this->ci->db->_force_escape_string_values = false;
        return $response;
    }
    private function get_legal_cases_grid_query_details($lang_id, $logged_user_id, $override_privacy, $filter, $sortable, $page_number, $pagingOn, $is_filtered, $return_query = false, $is_litigation = false, $get_query_count = true)
    {
        $_table = $this->_table;
        $this->_table = "legal_cases";
        $query = [];
        $this->ci->load->model("language");
        $langId = $this->ci->language->get_id_by_session_lang();
        $this->ci->db->query("set optimizer_switch = 'block_nested_loop=off'");
        $query["select"] = ["(case when (`legal_cases`.`channel` = 'CP') then 'yes' else 'no' end)        AS `isCP`,                                   `legal_cases`.`id`                                                          AS `id`,                                   `legal_cases`.`channel`                                                     AS `channel`,                                   `legal_cases`.`visibleToCP`                                                 AS `visibleToCP`,                                   `legal_cases`.`case_status_id`                                              AS `case_status_id`,                                   `provider_groups`.`name`                                                    AS `providerGroup`,                                   `legal_cases`.`user_id`                                                     AS `user_id`,                                   `legal_cases`.`contact_id`                                                  AS `contact_id`,                                   `legal_cases`.`subject`                                                     AS `subject`,                                   `legal_cases`.`description`                                                 AS `description`,                                   `legal_cases`.`latest_development`                                          AS `latest_development`,                                   `legal_cases`.`priority`                                                    AS `priority`,                                   `legal_cases`.`arrivalDate`                                                 AS `arrivalDate`,                                   `legal_cases`.`caseArrivalDate`                                             AS `caseArrivalDate`,                                   `legal_cases`.`dueDate`                                                     AS `dueDate`,                                   `legal_cases`.`closedOn`                                                    AS `closedOn`,                                   `legal_cases`.`statusComments`                                              AS `statusComments`,                                   `legal_cases`.`category`                                                    AS `category`,                                   `legal_cases`.`caseValue`                                                   AS `caseValue`,                                   `legal_cases`.`internalReference`                                           AS `internalReference`,                                   `legal_cases`.`externalizeLawyers`                                          AS `externalizeLawyers`,                                   `legal_cases`.`estimatedEffort`                                             AS `estimatedEffort`,                                   `legal_cases`.`createdOn`                                                   AS `createdOn`,                                   `legal_cases`.`createdBy`                                                   AS `createdBy`,                                   `legal_cases`.`modifiedOn`                                                  AS `modifiedOn`,                                   `legal_cases`.`modifiedBy`                                                  AS `modifiedBy`,                                   `legal_cases`.`archived`                                                    AS `archived`,                                   `legal_cases`.`private`                                                     AS `private`,                                   `legal_cases`.`timeTrackingBillable`                                        AS `timeTrackingBillable`,                                   `legal_cases`.`expensesBillable`                                            AS `expensesBillable`,                                   `legal_cases`.`archived`                                                    AS `archivedCases`,                                   `legal_cases`.`client_id`                                                   AS `client_id`,                                   `legal_cases`.`legal_case_stage_id`                                         AS `legal_case_stage_id`,                                   `legal_cases`.`legal_case_client_position_id`                               AS `legal_case_client_position_id`,                                   `legal_cases`.`legal_case_success_probability_id`                           AS `legal_case_success_probability_id`,                                   `legal_cases`.`recoveredValue`                                              AS `recoveredValue`,                                   `legal_cases`.`judgmentValue`                                               AS `judgmentValue`,                                   concat('M', `legal_cases`.`id`)                                             AS `caseID`,                                   `legal_cases`.`referredBy`                                                  AS `referredBy`,                                   `legal_cases`.`requestedBy`                                                 AS `requestedBy`,                                   `workflow_status`.`name`                                                    AS `status`,                                   concat(`up`.`firstName`, ' ', `up`.`lastName`)                              AS `assignee`,                                   `up`.`user_id`                                                              AS `assignee_user_id`,                                   `legal_case_litigation_external_references`.`number`                        AS `litigationExternalRef`,                                   `case_types`.`name`                                                         AS `type`,                                   `legal_case_litigation_details`.`id`                                        AS `stage_id`,                                   `legal_case_litigation_details`.`court_type_id`                             AS `court_type_id`,                                   `legal_case_litigation_details`.`court_degree_id`                           AS `court_degree_id`,                                   `legal_case_litigation_details`.`court_region_id`                           AS `court_region_id`,                                   `legal_case_litigation_details`.`court_id`                                  AS `court_id`,                                   `legal_case_litigation_details`.`comments`                                  AS `comments`,                                   `legal_case_litigation_details`.`sentenceDate`                              AS `sentenceDate`,                                   `com`.`name`                                                                AS `company`,                                   `com`.`id`                                                                  AS `company_id`,                                   `user_profiles`.`status`                                                    AS `userStatus`,                                    concat(`modified_users`.`firstName`, ' ', `modified_users`.`lastName`)     AS `modifiedByName`,                                    concat(`created_users`.`firstName`, ' ', `created_users`.`lastName`)       AS `createdByName`,                                   `lcee`.`effectiveEffort`                                                    AS `effectiveEffort`,                                   (CASE WHEN legal_case_litigation_details.status IS NULL THEN legal_case_stage_languages.name ELSE CONCAT(legal_case_stage_languages.name, ' (', stage_statuses.name, ')') END) AS caseStage,                                   `legal_case_success_probability_languages`.`name`                           AS `caseSuccessProbability`,                                   `legal_case_containers`.`subject`                                           AS `legalCaseContainerSubject`,                                   `legal_case_client_position_languages`.`name`                               AS `caseClientPosition`,                                    if(isnull(`clients`.`company_id`), (case                                                                           when (`cont`.`father` <> ' ') then concat_ws(' ', `cont`.`firstName`,                                                                                                                        `cont`.`father`,                                                                                                                        `cont`.`lastName`)                                                                           else concat_ws(' ', `cont`.`firstName`, `cont`.`lastName`) end),                                      `comp`.`name`)                                                           AS `clientName`,                                    if(isnull(`clients`.`company_id`), concat_ws(' ', `cont`.`foreignFirstName`, `cont`.`foreignLastName`) ,                                    `comp`.`foreignName`)                                                       AS `client_foreign_name`,                                    if(isnull(`clients`.`company_id`), 'Person', 'Company')                     AS `clientType`,                                    (case                                        when (`conre`.`father` <> '') then concat(`conre`.`firstName`, ' ', `conre`.`father`, ' ',                                                                                  `conre`.`lastName`)                                        else concat(`conre`.`firstName`, ' ', `conre`.`lastName`) end)         AS `contact`,                                    (case                                        when (`conhe`.`father` <> '') then concat(`conhe`.`firstName`, ' ', `conhe`.`father`, ' ',                                                                                  `conhe`.`lastName`)                                        else concat(`conhe`.`firstName`, ' ', `conhe`.`lastName`) end)         AS `contactContributor`,                                    (case                                        when (`conextlaw`.`father` <> '') then concat(`conextlaw`.`firstName`, ' ', `conextlaw`.`father`, ' ',                                                                                      `conextlaw`.`lastName`)                                        else concat(`conextlaw`.`firstName`, ' ', `conextlaw`.`lastName`) end) AS `contactOutsourceTo`,                                    `companiesExtLaw`.`name` AS `companyOutsourceTo`,                                    (case                                        when (`referredbycontact`.`father` <> '') then concat(`referredbycontact`.`firstName`, ' ',                                                                                              `referredbycontact`.`father`, ' ',                                                                                              `referredbycontact`.`lastName`)                                        else concat(`referredbycontact`.`firstName`, ' ',                                                    `referredbycontact`.`lastName`) end)                       AS `referredByName`,                                    (case                                        when (`requestedbycontact`.`father` <> '') then concat(`requestedbycontact`.`firstName`, ' ',                                                                                               `requestedbycontact`.`father`, ' ',                                                                                               `requestedbycontact`.`lastName`)                                        else concat(`requestedbycontact`.`firstName`, ' ',                                                    `requestedbycontact`.`lastName`) end)                      AS `requestedByName`", false];
        if ($is_filtered && isset($filter) && is_array($filter)) {
            if (isset($filter["filters"])) {
                foreach ($filter["filters"] as $_filter) {
                    if (!empty($_filter["filters"][0]["field"]) && in_array($_filter["filters"][0]["field"], ["legal_cases.effectiveEffort", "legal_cases.estimatedEffort"])) {
                        $system_preferences = $this->ci->session->userdata("systemPreferences");
                        $this->ci->load->library("TimeMask");
                        $_filter["filters"][0]["value"] = $this->ci->timemask->humanReadableToHours($_filter["filters"][0]["value"]);
                    }
                    $this->prep_k_filter($_filter, $query, $filter["logic"]);
                }
                unset($_filter);
            }
            if (isset($filter["customFields"])) {
                $this->ci->load->model("custom_field", "custom_fieldfactory");
                $this->ci->custom_field = $this->ci->custom_fieldfactory->get_instance("custom_fieldfactory");
                $this->ci->custom_field->prep_custom_field_filters($this->modelName, $filter["customFields"], $query);
            }
        }
        $query["join"] = [["`legal_cases_companies` `lcccom`", "`lcccom`.`case_id` = `legal_cases`.`id`", "left"], ["`companies` `com`", "`lcccom`.`company_id` = `com`.`id`", "left"], ["`legal_cases_contacts` `lccre`", "`lccre`.`case_id` = `legal_cases`.`id` and `lccre`.`contactType` = 'contact'", "left"], ["`contacts` `conre`", "`conre`.`id` = `lccre`.`contact_id`", "left"], ["`legal_cases_contacts` `lccch`", "`lccch`.`case_id` = `legal_cases`.`id` and `lccch`.`contactType` = 'contributor'", "left"], ["`contacts` `conhe`", "`conhe`.`id` = `lccch`.`contact_id`", "left"], ["`workflow_status`", "`workflow_status`.`id` = `legal_cases`.`case_status_id`", "left"], ["`user_profiles` `up`", "`up`.`user_id` = `legal_cases`.`user_id`", "left"], ["`legal_case_effective_effort` `lcee`", "`lcee`.`legal_case_id` = `legal_cases`.`id`", "left"], ["`legal_case_litigation_details`", "`legal_case_litigation_details`.`id` = `legal_cases`.`stage`", "left"], ["`legal_case_litigation_external_references`", "`legal_case_litigation_external_references`.`stage` = `legal_case_litigation_details`.`id`", "left"], ["`contacts` `referredbycontact`", "`referredbycontact`.`id` = `legal_cases`.`referredBy`", "left"], ["`contacts` `requestedbycontact`", "`requestedbycontact`.`id` = `legal_cases`.`requestedBy`", "left"], ["`case_types`", "`case_types`.`id` = `legal_cases`.`case_type_id`", "left"], ["`provider_groups`", "`provider_groups`.`id` = `legal_cases`.`provider_group_id`", "left"], ["`legal_case_related_containers`", "`legal_case_related_containers`.`legal_case_id` = `legal_cases`.`id`", "left"], ["`legal_case_containers`", "`legal_case_containers`.`id` = `legal_case_related_containers`.`legal_case_container_id`", "left"], ["`clients`", "`clients`.`id` = `legal_cases`.`client_id`", "left"], ["`companies` `comp`", "`comp`.`id` = `clients`.`company_id`", "left"], ["`contacts` `cont`", "`cont`.`id` = `clients`.`contact_id`", "left"], ["`user_profiles` `created`", "`created`.`user_id` = `clients`.`createdBy`", "left"], ["`user_profiles` `created_users`", "`created_users`.`user_id` = `legal_cases`.`createdBy`", "left"], ["`user_profiles` `modified`", "`modified`.`user_id` = `clients`.`modifiedBy`", "left"], ["`user_profiles` `modified_users`", "`modified_users`.`user_id` = `legal_cases`.`modifiedBy`", "left"], ["`legal_case_stage_languages`", "`legal_case_stage_languages`.`legal_case_stage_id` = `legal_cases`.`legal_case_stage_id` and `legal_case_stage_languages`.`language_id` = '" . $lang_id . "'", "left"], ["`legal_case_client_positions`", "`legal_cases`.`legal_case_client_position_id` = `legal_case_client_positions`.`id`", "left"], ["`legal_case_client_position_languages`", "`legal_case_client_position_languages`.`language_id` = '" . $lang_id . "' and `legal_case_client_positions`.`id` = `legal_case_client_position_languages`.`legal_case_client_position_id`", "left"], ["`legal_case_success_probability_languages`", "`legal_case_success_probability_languages`.`legal_case_success_probability_id` =\r\n                      `legal_cases`.`legal_case_success_probability_id` and\r\n                      `legal_case_success_probability_languages`.`language_id` = '" . $lang_id . "'", "left"], ["`user_profiles`", "`user_profiles`.`user_id` = `legal_cases`.`user_id`", "left"], ["`legal_case_outsources` `lccompaniesExtLaw`", "`lccompaniesExtLaw`.`legal_case_id` = `legal_cases`.`id`", "left"], ["`companies` `companiesExtLaw`", "`companiesExtLaw`.`id` = `lccompaniesExtLaw`.`company_id`", "left"], ["`legal_case_outsource_contacts` `lccextlaw`", "`lccextlaw`.`legal_case_outsource_id` = `lccompaniesExtLaw`.`id`", "left"], ["`contacts` `conextlaw`", "`conextlaw`.`id` = `lccextlaw`.`contact_id`", "left"]];
        $query["join"][] = ["court_types", "court_types.id = legal_case_litigation_details.court_type_id", "left"];
        $query["join"][] = ["court_degrees", "court_degrees.id = legal_case_litigation_details.court_degree_id", "left"];
        $query["join"][] = ["court_regions", "court_regions.id = legal_case_litigation_details.court_region_id", "left"];
        $query["join"][] = ["courts", "courts.id = legal_case_litigation_details.court_id", "left"];
        $query["join"][] = ["stage_statuses_languages as stage_statuses", "stage_statuses.status = legal_case_litigation_details.status and stage_statuses.language_id = '" . $lang_id . "'", "left"];
        if ($is_litigation || isset($query["select"][0]) && isset($query["where"][0][1]) && $query["where"][0][1] === "%Litigation%" || isset($query["where"][1][1]) && $query["where"][1][1] === "%Litigation%") {
            $query["select"][0] .= ",court_types.name as court_type,court_degrees.name as court_degree,court_regions.name as court_region,courts.name as court,";
            $query["select"][0] .= ",(select group_concat((case                                             when isnull(`legal_case_opponents`.`opponent_member_type`) then NULL                                             else (case                                                       when (`legal_case_opponents`.`opponent_member_type` = 'contact')                                                           then (case                                                                     when (`opponentContactNationalitiesCountry`.`name` is not null)                                                                         then concat(`opponentcontact`.`firstName`, ' ',                                                                                     `opponentcontact`.`lastName`, '(',                                                                                     `opponentContactNationalitiesCountry`.`name`,                                                                                     ')')                                                                     else (case                                                                               when (`opponentcontact`.`father` <> '') then concat(                                                                                       `opponentcontact`.`firstName`, ' ',                                                                                       `opponentcontact`.`father`, ' ',                                                                                       `opponentcontact`.`lastName`)                                                                               else concat(`opponentcontact`.`firstName`, ' ',                                                                                           `opponentcontact`.`lastName`) end) end)                                                       else (case                                                                 when (`opponentcompanynationalities`.`name` is not null)                                                                     then concat(`opponentcompany`.`name`, '(',                                                                                 `opponentcompanynationalities`.`name`, ')')                                                                 else `opponentcompany`.`name` end) end) end) order by                                        `legal_case_opponents`.`case_id` ASC separator ',')                                    from (((((`legal_case_opponents` join `opponents` on ((`opponents`.`id` =                                                                                           `legal_case_opponents`.`opponent_id`))) left join `companies` `opponentcompany` on ((                                            (`opponentcompany`.`id` = `opponents`.`company_id`) and                                            (`legal_case_opponents`.`opponent_member_type` = 'company')))) left join `contacts` `opponentcontact` on ((                                            (`opponentcontact`.`id` = `opponents`.`contact_id`) and                                            (`legal_case_opponents`.`opponent_member_type` = 'contact')))) left join `countries_languages` `opponentcompanynationalities` on ((                                            (`opponentcompanynationalities`.`country_id` = `opponentcompany`.`nationality_id`) and `opponentcompanynationalities`.`language_id` = " . $this->get_lang_id() . " AND                                             (`legal_case_opponents`.`opponent_member_type` = 'company'))))                                             left join `contact_nationalities_details` `opponentcontactnationalities`                                                       on (((`opponentcontactnationalities`.`contact_id` = `opponentcontact`.`id`) and                                                            (`legal_case_opponents`.`opponent_member_type` = 'contact'))))                                            LEFT JOIN countries_languages AS opponentContactNationalitiesCountry ON opponentContactNationalitiesCountry.country_id = opponentcontactnationalities.nationality_id AND opponentContactNationalitiesCountry.language_id = " . $this->get_lang_id() . "                                    where (`legal_case_opponents`.`case_id` =                                           `legal_cases`.`id`))                                                                         AS `opponentNationalities`,                                   (SELECT GROUP_CONCAT(CASE                                         WHEN `legal_case_opponents`.`opponent_member_type` IS NULL                                             THEN NULL                                         ELSE (CASE                                                   WHEN `opponent_positions`.`name` != '' THEN (CASE                                                                                                    WHEN `legal_case_opponents`.`opponent_member_type` = 'company'                                                                                                        THEN CONCAT(`opponentCompany`.`name`, ' - ', `opponent_positions`.`name`)                                                                                                    ELSE (CASE                                                                                                              WHEN `opponentContact`.`father` != ''                                                                                                                  THEN CONCAT(                                                                                                                      `opponentContact`.`firstName`,                                                                                                                      ' ',                                                                                                                      `opponentContact`.`father`,                                                                                                                      ' ',                                                                                                                      `opponentContact`.`lastName`,                                                                                                                      ' - ',                                                                                                                      `opponent_positions`.`name`)                                                                                                              ELSE CONCAT(                                                                                                                      `opponentContact`.`firstName`,                                                                                                                      ' ',                                                                                                                      `opponentContact`.`lastName`,                                                                                                                      ' - ',                                                                                                                      `opponent_positions`.`name`) END) END)                                                   ELSE (CASE                                                             WHEN `legal_case_opponents`.`opponent_member_type` = 'company'                                                                 THEN `opponentCompany`.`name`                                                             ELSE (CASE                                                                       WHEN `opponentContact`.`father` != ''                                                                           THEN CONCAT(                                                                               `opponentContact`.`firstName`,                                                                               ' ',                                                                               `opponentContact`.`father`,                                                                               ' ',                                                                               `opponentContact`.`lastName`)                                                                       ELSE CONCAT(                                                                               `opponentContact`.`firstName`,                                                                               ' ',                                                                               `opponentContact`.`lastName`) END) END) END) END                                     order by `legal_case_opponents`.`case_id` ASC SEPARATOR ', ')                                   FROM `legal_case_opponents`                                              LEFT JOIN `opponents` ON `opponents`.`id` = `legal_case_opponents`.`opponent_id`                                              LEFT JOIN `companies` AS `opponentCompany`                                                        ON `opponentCompany`.`id` = `opponents`.`company_id` AND                                                           `legal_case_opponents`.`opponent_member_type` = 'company'                                              LEFT JOIN `contacts` AS `opponentContact`                                                        ON `opponentContact`.`id` = `opponents`.`contact_id` AND                                                           `legal_case_opponents`.`opponent_member_type` = 'contact'                                              LEFT JOIN `legal_case_opponent_position_languages` AS `opponent_positions`                                                        ON `opponent_positions`.`legal_case_opponent_position_id` =                                                           `legal_case_opponents`.`opponent_position` and                                                           `opponent_positions`.`language_id` = '" . $langId . "'\r\n                                   WHERE `legal_case_opponents`.`case_id` = `legal_cases`.`id`)                                                  AS `opponentNames`";
            $query["select"][0] .= ",(SELECT GROUP_CONCAT(CASE                    WHEN `legal_case_opponents`.`opponent_member_type` IS NULL                        THEN NULL                    ELSE (CASE                              WHEN `opponent_positions`.`name` != '' THEN (CASE                                WHEN `legal_case_opponents`.`opponent_member_type` = 'company'                                    THEN IFNULL(CONCAT(opponentCompany.foreignName, ' - ', `opponent_positions`.`name`), CONCAT(`opponentCompany`.`name`, ' - ', `opponent_positions`.`name`))                                ELSE (                                CONCAT(IFNULL(opponentContact.foreignFirstName, `opponentContact`.`firstName`), ' ', IFNULL(opponentContact.foreignLastName, `opponentContact`.`lastName`), ' - ', `opponent_positions`.`name`)                                )                                END)                              ELSE (CASE                                        WHEN `legal_case_opponents`.`opponent_member_type` = 'company'                                            THEN IFNULL(opponentCompany.foreignName, `opponentCompany`.`name`)                                        ELSE (                                        CONCAT(IFNULL(opponentContact.foreignFirstName, `opponentContact`.`firstName`), ' ', IFNULL(opponentContact.foreignLastName, `opponentContact`.`lastName`))                                        )                                 END) END) END                order by `legal_case_opponents`.`case_id` ASC SEPARATOR ', ')                FROM `legal_case_opponents`                         LEFT JOIN `opponents` ON `opponents`.`id` = `legal_case_opponents`.`opponent_id`                         LEFT JOIN `companies` AS `opponentCompany`                                   ON `opponentCompany`.`id` = `opponents`.`company_id` AND                                      `legal_case_opponents`.`opponent_member_type` = 'company'                         LEFT JOIN `contacts` AS `opponentContact`                                   ON `opponentContact`.`id` = `opponents`.`contact_id` AND                                      `legal_case_opponents`.`opponent_member_type` = 'contact'                         LEFT JOIN `legal_case_opponent_position_languages` AS `opponent_positions`                                   ON `opponent_positions`.`legal_case_opponent_position_id` =                                      `legal_case_opponents`.`opponent_position` and                                      `opponent_positions`.`language_id` = '" . $langId . "'\r\n                WHERE `legal_case_opponents`.`case_id` = `legal_cases`.`id`) AS opponent_foreign_name";
            $query["select"][0] .= ",(SELECT DISTINCT CONCAT(startDate, ' - ', startTime)\r\n                                       FROM legal_case_hearings\r\n                                       WHERE startTime = (SELECT MAX(legal_case_hearings.startTime)\r\n                                                          FROM legal_case_hearings\r\n                                                          WHERE legal_case_hearings.legal_case_id=legal_cases.id AND is_deleted = 0 \r\n                                                          AND startDate = (SELECT MAX(startDate) FROM legal_case_hearings where legal_case_hearings.legal_case_id=legal_cases.id AND is_deleted = 0)) and legal_case_hearings.legal_case_id=legal_cases.id\r\n                                                           and startDate = (SELECT MAX(startDate) FROM legal_case_hearings where legal_case_hearings.legal_case_id=legal_cases.id AND is_deleted = 0)\r\n                                       ) as last_hearing, \r\n                                       (SELECT  legal_case_hearings.judgment\r\n                                        FROM legal_case_hearings\r\n                                        WHERE startTime = (SELECT MAX(legal_case_hearings.startTime)\r\n                                                            FROM legal_case_hearings\r\n                                                            WHERE legal_case_hearings.legal_case_id=legal_cases.id AND is_deleted = 0 and legal_case_hearings.judged = 'yes'\r\n                                                            AND startDate = (SELECT MAX(startDate) FROM legal_case_hearings where legal_case_hearings.legal_case_id=legal_cases.id AND is_deleted = 0 and legal_case_hearings.judged = 'yes')) and legal_case_hearings.legal_case_id=legal_cases.id and legal_case_hearings.judged = 'yes'\r\n                                                            and startDate = (SELECT MAX(startDate) FROM legal_case_hearings where legal_case_hearings.legal_case_id=legal_cases.id AND is_deleted = 0 and legal_case_hearings.judged = 'yes')\r\n                                      GROUP BY legal_case_hearings.legal_case_id) as judgment";
        }
        $query["where"][] = ["legal_cases.isDeleted = '0'", NULL, false];
        $query["where"][] = $this->get_matter_privacy_conditions($logged_user_id, $override_privacy);
        $query["group_by"] = ["legal_cases.id"];
        if (is_array($sortable) && !empty($sortable)) {
            foreach ($sortable as $_sort) {
                $query["order_by"][] = [$_sort["field"], $_sort["dir"]];
            }
        } else {
            $query["order_by"] = ["legal_cases.id desc"];
        }
        $paginationConf = [];
        if ($page_number != "") {
            $query["limit"] = [10000, ($page_number - 1) * 10000];
        }
        if ($limit = $this->ci->input->post("take", true)) {
            $query["limit"] = [$this->ci->input->post("take", true), $this->ci->input->post("skip", true)];
            $paginationConf = ["urlPrefix" => ""];
            $paginationConf["inPage"] = $this->ci->input->post("take", true);
        }
        $response["query"] = $query;
        if ($return_query) {
            if (isset($query["limit"])) {
                unset($query["limit"]);
            }
            return $query;
        }
        $response["data"] = $pagingOn && !empty($paginationConf) ? parent::paginate($query, $paginationConf) : parent::load_all($query);
        $response["stringQuery"] = $this->ci->db->last_query();
        if ($get_query_count) {
            $response["totalRows"] = $this->get_legal_cases_count($query);
        }
        $this->_table = $_table;
        return $response;
    }
    private function get_legal_cases_count($query)
    {
        $total_count = "";
        if (is_array($query) && is_array($query["select"]) && !empty($query) && !empty($query["select"])) {
            unset($query["group_by"]);
            $query["limit"] = 1;
            $query["select"][0] = "count(distinct `legal_cases`.`id` ), " . $query["select"][0];
            $total_count = $this->load($query);
            if (is_array($total_count) && !empty($total_count)) {
                list($total_count) = array_values($total_count);
            }
        }
        return $total_count;
    }
    public function case_id_field_value()
    {
        return "`legal_cases`.`id`";
    }
    public function client_type_field_value()
    {
        return "if(isnull(`clients`.`company_id`), 'Person', 'Company')";
    }
    public function contactoutsourceto_field_value()
    {
        return "(case        when (`conextlaw`.`father` <> '') then concat(`conextlaw`.`firstName`, ' ', `conextlaw`.`father`, ' ',                                                      `conextlaw`.`lastName`)        else concat(`conextlaw`.`firstName`, ' ', `conextlaw`.`lastName`) end)";
    }
    public function contactoutsourceto_field_value_from_view()
    {
        return "legal_cases.contactOutsourceTo";
    }
    public function companyoutsourceto_field_value()
    {
        return "companiesExtLaw.name";
    }
    public function companyoutsourceto_field_value_from_view()
    {
        return "legal_cases.companyOutsourceTo";
    }
    public function client_name_field_value()
    {
        return "if(isnull(`clients`.`company_id`), (case                                               when (`cont`.`father` <> ' ') then concat_ws(' ', `cont`.`firstName`,                                                                                            `cont`.`father`,                                                                                            `cont`.`lastName`)                                               else concat_ws(' ', `cont`.`firstName`, `cont`.`lastName`) end),          `comp`.`name`)";
    }
    public function client_id_field_value()
    {
        return "if(isnull(`clients`.`company_id`), `cont`.`id`, `comp`.`id`)";
    }
    public function client_type_con_comp_field_value()
    {
        return "if(isnull(`clients`.`company_id`), 'contact', 'company')";
    }
    public function assignee_user_id_field_value()
    {
        return "`up`.`user_id`";
    }
    public function client_foreign_name_field_value()
    {
        return "if(isnull(`clients`.`company_id`), (concat_ws(' ', `cont`.`foreignFirstName`, `cont`.`foreignLastName`)),`comp`.`foreignName`)";
    }
    public function contact_field_value()
    {
        return "CASE when (conre.father <> ' ')\r\n                              then concat_ws(' ', conre.firstName, conre.father, conre.lastName)\r\n                          else concat_ws(' ', conre.firstName, conre.lastName)\r\n        END";
    }
    public function contact_contributor_field_value()
    {
        return "CASE when (conhe.father <> ' ')\r\n                              then concat_ws(' ', conhe.firstName, conhe.father, conhe.lastName)\r\n                          else concat_ws(' ', conhe.firstName, conhe.lastName)\r\n        END";
    }
    public function contact_outsource_to_field_value()
    {
        return "CASE when (conextlaw.father <> ' ')\r\n                              then concat_ws(' ', conextlaw.firstName, conextlaw.father, conextlaw.lastName)\r\n                          else concat_ws(' ', conextlaw.firstName, conextlaw.lastName)\r\n        END";
    }
    public function referred_by_name_field_value()
    {
        return "CASE when (referredbycontact.father <> ' ')\r\n                              then concat_ws(' ', referredbycontact.firstName, referredbycontact.father, referredbycontact.lastName)\r\n                          else concat_ws(' ', referredbycontact.firstName, referredbycontact.lastName)\r\n        END";
    }
    public function requested_by_name_field_value()
    {
        return "CASE when (requestedbycontact.father <> ' ')\r\n                              then concat_ws(' ', requestedbycontact.firstName, requestedbycontact.father, requestedbycontact.lastName)\r\n                          else concat_ws(' ', requestedbycontact.firstName, requestedbycontact.lastName)\r\n        END";
    }
    public function assignee_field_value()
    {
        return "concat(`up`.firstName, ' ', `up`.`lastName`)";
    }
    public function is_cp_field_value()
    {
        return "(case when (`legal_cases`.`channel` = 'CP') then 'yes' else 'no' end)";
    }
    public function opponent_nationalities_field_value()
    {
        return "(select group_concat((case                                             when isnull(`legal_case_opponents`.`opponent_member_type`) then NULL                                             else (case                                                       when (`legal_case_opponents`.`opponent_member_type` = 'contact')                                                           then (case                                                                     when (`opponentContactNationalitiesCountry`.`name` is not null)                                                                         then concat(`opponentcontact`.`firstName`, ' ',                                                                                     `opponentcontact`.`lastName`, '(',                                                                                     `opponentContactNationalitiesCountry`.`name`,                                                                                     ')')                                                                     else (case                                                                               when (`opponentcontact`.`father` <> '') then concat(                                                                                       `opponentcontact`.`firstName`, ' ',                                                                                       `opponentcontact`.`father`, ' ',                                                                                       `opponentcontact`.`lastName`)                                                                               else concat(`opponentcontact`.`firstName`, ' ',                                                                                           `opponentcontact`.`lastName`) end) end)                                                       else (case                                                                 when (`opponentcompanynationalities`.`name` is not null)                                                                     then concat(`opponentcompany`.`name`, '(',                                                                                 `opponentcompanynationalities`.`name`, ')')                                                                 else `opponentcompany`.`name` end) end) end) order by                                        `legal_case_opponents`.`case_id` ASC separator ',')                    from (((((`legal_case_opponents` join `opponents` on ((`opponents`.`id` =                                                                           `legal_case_opponents`.`opponent_id`))) left join `companies` `opponentcompany` on ((                            (`opponentcompany`.`id` = `opponents`.`company_id`) and                            (`legal_case_opponents`.`opponent_member_type` = 'company')))) left join `contacts` `opponentcontact` on ((                            (`opponentcontact`.`id` = `opponents`.`contact_id`) and                            (`legal_case_opponents`.`opponent_member_type` = 'contact')))) left join `countries_languages` `opponentcompanynationalities` on ((                            (`opponentcompanynationalities`.`country_id` = `opponentcompany`.`nationality_id`) and `opponentcompanynationalities`.`language_id` = " . $this->get_lang_id() . " AND                             (`legal_case_opponents`.`opponent_member_type` = 'company'))))                             left join `contact_nationalities_details` `opponentcontactnationalities`                                       on (((`opponentcontactnationalities`.`contact_id` = `opponentcontact`.`id`) and                                            (`legal_case_opponents`.`opponent_member_type` = 'contact'))))                            LEFT JOIN countries_languages AS opponentContactNationalitiesCountry ON opponentContactNationalitiesCountry.country_id = opponentcontactnationalities.nationality_id AND opponentContactNationalitiesCountry.language_id = " . $this->get_lang_id() . "                    where (`legal_case_opponents`.`case_id` = `legal_cases`.`id`))";
    }
    public function opponent_names_field_value()
    {
        return "(SELECT GROUP_CONCAT(CASE                                         WHEN `legal_case_opponents`.`opponent_member_type` IS NULL                                             THEN NULL                                         ELSE (CASE                                                   WHEN `opponent_positions`.`name` != '' THEN (CASE                                                                                                    WHEN `legal_case_opponents`.`opponent_member_type` = 'company'                                                                                                        THEN CONCAT(`opponentCompany`.`name`, ' - ', `opponent_positions`.`name`)                                                                                                    ELSE (CASE                                                                                                              WHEN `opponentContact`.`father` != ''                                                                                                                  THEN CONCAT(                                                                                                                      `opponentContact`.`firstName`,                                                                                                                      ' ',                                                                                                                      `opponentContact`.`father`,                                                                                                                      ' ',                                                                                                                      `opponentContact`.`lastName`,                                                                                                                      ' - ',                                                                                                                      `opponent_positions`.`name`)                                                                                                              ELSE CONCAT(                                                                                                                      `opponentContact`.`firstName`,                                                                                                                      ' ',                                                                                                                      `opponentContact`.`lastName`,                                                                                                                      ' - ',                                                                                                                      `opponent_positions`.`name`) END) END)                                                   ELSE (CASE                                                             WHEN `legal_case_opponents`.`opponent_member_type` = 'company'                                                                 THEN `opponentCompany`.`name`                                                             ELSE (CASE                                                                       WHEN `opponentContact`.`father` != ''                                                                           THEN CONCAT(                                                                               `opponentContact`.`firstName`,                                                                               ' ',                                                                               `opponentContact`.`father`,                                                                               ' ',                                                                               `opponentContact`.`lastName`)                                                                       ELSE CONCAT(                                                                               `opponentContact`.`firstName`,                                                                               ' ',                                                                               `opponentContact`.`lastName`) END) END) END) END                                     order by `legal_case_opponents`.`case_id` ASC SEPARATOR ', ')                 FROM `legal_case_opponents`                          LEFT JOIN `opponents` ON `opponents`.`id` = `legal_case_opponents`.`opponent_id`                          LEFT JOIN `companies` AS `opponentCompany`                                    ON `opponentCompany`.`id` = `opponents`.`company_id` AND                                       `legal_case_opponents`.`opponent_member_type` = 'company'                          LEFT JOIN `contacts` AS `opponentContact`                                    ON `opponentContact`.`id` = `opponents`.`contact_id` AND                                       `legal_case_opponents`.`opponent_member_type` = 'contact'                          LEFT JOIN `legal_case_opponent_position_languages` AS `opponent_positions`                                    ON `opponent_positions`.`legal_case_opponent_position_id` =                                       `legal_case_opponents`.`opponent_position` and                                       `opponent_positions`.`language_id` = '1'                 WHERE `legal_case_opponents`.`case_id` = `legal_cases`.`id`)";
    }
    public function opponent_foreign_name_field_value()
    {
        return "(\r\n                SELECT\r\n                    GROUP_CONCAT(\r\n                      CASE WHEN `legal_case_opponents`.`opponent_member_type` IS NULL THEN NULL ELSE (\r\n                        CASE WHEN `opponent_positions`.`name` != '' THEN (\r\n                          CASE WHEN `legal_case_opponents`.`opponent_member_type` = 'company' THEN (IFNULL(\r\n                            opponentCompany.foreignName,\r\n                            CONCAT(\r\n                              `opponentCompany`.`name`,\r\n                              ' - ',\r\n                              `opponent_positions`.`name`\r\n                            )\r\n                          )) ELSE (\r\n                            CONCAT(\r\n                              IFNULL(\r\n                                opponentContact.foreignFirstName,\r\n                                `opponentContact`.`firstName`\r\n                              ),\r\n                              ' ',\r\n                              IFNULL(\r\n                                opponentContact.foreignLastName,\r\n                                `opponentContact`.`lastName`\r\n                              ),\r\n                              ' - ',\r\n                              `opponent_positions`.`name`\r\n                            )\r\n                          ) END\r\n                        ) ELSE (\r\n                          CASE WHEN `legal_case_opponents`.`opponent_member_type` = 'company' THEN (IFNULL(\r\n                            opponentCompany.foreignName,\r\n                            `opponentCompany`.`name`\r\n                          )) ELSE (\r\n                            CONCAT(\r\n                              IFNULL(\r\n                                opponentContact.foreignFirstName,\r\n                                `opponentContact`.`firstName`\r\n                              ),\r\n                              ' ',\r\n                              IFNULL(\r\n                                opponentContact.foreignLastName,\r\n                                `opponentContact`.`lastName`\r\n                              )\r\n                            )\r\n                          ) END\r\n                        ) END\r\n                      ) END\r\n                       order by\r\n                        `legal_case_opponents`.`case_id` ASC SEPARATOR ', '\r\n                    )\r\n                FROM `legal_case_opponents`\r\n                         LEFT JOIN `opponents` ON `opponents`.`id` = `legal_case_opponents`.`opponent_id`\r\n                         LEFT JOIN `companies` AS `opponentCompany`\r\n                                   ON `opponentCompany`.`id` = `opponents`.`company_id` AND\r\n                                      `legal_case_opponents`.`opponent_member_type` = 'company'\r\n                         LEFT JOIN `contacts` AS `opponentContact`\r\n                                   ON `opponentContact`.`id` = `opponents`.`contact_id` AND\r\n                                      `legal_case_opponents`.`opponent_member_type` = 'contact'\r\n                         LEFT JOIN `legal_case_opponent_position_languages` AS `opponent_positions`\r\n                                   ON `opponent_positions`.`legal_case_opponent_position_id` =\r\n                                      `legal_case_opponents`.`opponent_position` and\r\n                                      `opponent_positions`.`language_id` = '1'\r\n                WHERE `legal_case_opponents`.`case_id` = `legal_cases`.`id`)";
    }
    public function reasons_of_postponement_of_last_hearing_field_value()
    {
        return "(SELECT reasons_of_postponement\r\n               FROM legal_case_hearings\r\n               WHERE startTime = (SELECT MAX(legal_case_hearings.startTime)\r\n                                  FROM legal_case_hearings\r\n                                  WHERE legal_case_hearings.legal_case_id=legal_cases.id AND is_deleted = 0\r\n                                  AND startDate = (SELECT MAX(startDate) FROM legal_case_hearings where legal_case_hearings.legal_case_id=legal_cases.id AND is_deleted = 0)) and legal_case_hearings.legal_case_id=legal_cases.id\r\n                                  and startDate = (SELECT MAX(startDate) FROM legal_case_hearings where legal_case_hearings.legal_case_id=legal_cases.id AND is_deleted = 0))";
    }
    public function last_hearing_field_value()
    {
        return "(SELECT DISTINCT CONCAT(startDate, ' - ', startTime)\r\n               FROM legal_case_hearings\r\n               WHERE startTime = (SELECT MAX(legal_case_hearings.startTime)\r\n                                  FROM legal_case_hearings\r\n                                  WHERE legal_case_hearings.legal_case_id=legal_cases.id AND is_deleted = 0 \r\n                                  AND startDate = (SELECT MAX(startDate) FROM legal_case_hearings where legal_case_hearings.legal_case_id=legal_cases.id AND is_deleted = 0)) and legal_case_hearings.legal_case_id=legal_cases.id\r\n                                  and startDate = (SELECT MAX(startDate) FROM legal_case_hearings where legal_case_hearings.legal_case_id=legal_cases.id AND is_deleted = 0))";
    }
    public function judgment_field_value()
    {
        return "(SELECT  legal_case_hearings.judgment\r\n                    FROM legal_case_hearings\r\n                    WHERE startTime = (SELECT MAX(legal_case_hearings.startTime)\r\n                                        FROM legal_case_hearings\r\n                                        WHERE legal_case_hearings.legal_case_id=legal_cases.id AND is_deleted = 0 and legal_case_hearings.judged = 'yes' \r\n                                        AND startDate = (SELECT MAX(startDate) FROM legal_case_hearings where legal_case_hearings.legal_case_id=legal_cases.id AND is_deleted = 0 and legal_case_hearings.judged = 'yes')) and legal_case_hearings.legal_case_id=legal_cases.id and legal_case_hearings.judged = 'yes' \r\n                                        and startDate = (SELECT MAX(startDate) FROM legal_case_hearings where legal_case_hearings.legal_case_id=legal_cases.id AND is_deleted = 0 and legal_case_hearings.judged = 'yes')\r\n                  GROUP BY legal_case_hearings.legal_case_id)";
    }
    public function modified_by_name_field_value()
    {
        return "concat(`modified_users`.`firstName`, ' ', `modified_users`.`lastName`)";
    }
    public function created_by_name_field_value()
    {
        return "concat(`created_users`.`firstName`, ' ', `created_users`.`lastName`)";
    }
    public function litigation_external_references_field_value()
    {
        return "(SELECT GROUP_CONCAT(`legal_case_litigation_external_references`.`number` SEPARATOR ', ')                    FROM `legal_case_litigation_external_references`                             LEFT JOIN legal_case_litigation_details                                       ON legal_case_litigation_external_references.stage = legal_case_litigation_details.id                    WHERE `legal_case_litigation_details`.`legal_case_id` = `legal_cases`.`id`)";
    }
    public function get_legal_cases_order_by_fields($fields, $is_group_by)
    {
        return $fields;
    }
    public function get_lang_id($is_api = false)
    {
        $this->ci->load->model("language");
        $lang = NULL;
        if ($is_api) {
            $logged_user_id = $this->user_logged_in_data["user_id"];
            $this->ci->load->model("user_preference");
            $this->ci->user_preference->fetch(["user_id" => $logged_user_id, "keyName" => "language"]);
            $lang = $this->ci->user_preference->get_field("keyValue");
        }
        return $this->ci->language->get_id_by_session_lang($lang);
    }
    public function k_load_all_outsource($legal_case_id)
    {
        $query = "\r\n            (\r\n                SELECT \r\n                    legal_cases_companies.id AS id, \r\n                    legal_cases_companies.comments as comments, \r\n                    companies.name as outsource_name, \r\n                    CASE \r\n                        WHEN legal_case_company_role_id IS NULL \r\n                        THEN 0 \r\n                        ELSE legal_case_company_role_id \r\n                    END AS role_id, \r\n                    legal_case_company_roles.name AS role_name, \r\n                    companies.id AS outsource_id, \r\n                    'company' AS outsource_type,\r\n                    legal_cases_companies.case_id AS case_id\r\n                FROM\r\n                    legal_cases_companies\r\n                LEFT JOIN companies ON\r\n                    companies.id = legal_cases_companies.company_id\r\n                LEFT JOIN legal_case_company_roles ON\r\n                    legal_case_company_roles.id = legal_cases_companies.legal_case_company_role_id\r\n                WHERE\r\n                    companies.status = 'Active' AND\r\n                    legal_cases_companies.companyType = 'external lawyer' AND\r\n                    legal_cases_companies.case_id = ?\r\n            )\r\n            \r\n            UNION\r\n\r\n            (\r\n                SELECT\r\n                    legal_cases_contacts.id AS id, \r\n                    legal_cases_contacts.comments as comments, \r\n                    CASE \r\n                        WHEN contacts.father!='' \r\n                        THEN CONCAT(contacts.firstName, ' ', contacts.father, ' ', contacts.lastName) \r\n                        ELSE CONCAT(contacts.firstName, ' ', contacts.lastName) \r\n                    END as outsource_name, \r\n                    CASE \r\n                        WHEN legal_case_contact_role_id IS NULL \r\n                        THEN 0 \r\n                        ELSE legal_case_contact_role_id \r\n                    END AS role_id, \r\n                    legal_case_contact_roles.name AS role_name, \r\n                    contacts.id AS outsource_id,\r\n                    'contact' AS outsource_type,\r\n                    legal_cases_contacts.case_id AS case_id\r\n                FROM\r\n                    legal_cases_contacts\r\n                LEFT JOIN contacts ON\r\n                    contacts.id = legal_cases_contacts.contact_id\r\n                LEFT JOIN legal_case_contact_roles ON\r\n                    legal_case_contact_roles.id = legal_cases_contacts.legal_case_contact_role_id\r\n                WHERE\r\n                    legal_cases_contacts.contactType = 'external lawyer' AND\r\n                    legal_cases_contacts.case_id = ?\r\n            )\r\n            \r\n            ORDER BY\r\n                id DESC\r\n        ";
        $query_execution = $this->ci->db->query($query, [$legal_case_id, $legal_case_id]);
        $result["data"] = $query_execution->result_array();
        $result["totalRows"] = $query_execution->num_rows();
        return $result;
    }
    public function get_total_billable_new_record_amount($billable_logs_balance, $billable_expenses_balance, $effective_effort = false, $expenses_amount = false)
    {
        $allowed_decimal_format = $this->ci->config->item("allowed_decimal_format");
        $total_amount_balance = bcadd($billable_logs_balance, $billable_expenses_balance, $allowed_decimal_format);
        $total_amount_billable = 0;
        if ($effective_effort) {
            $total_amount_billable = $effective_effort;
        } else {
            if ($expenses_amount) {
                $total_amount_billable = $expenses_amount;
            }
        }
        return bcadd($total_amount_balance, $total_amount_billable, $allowed_decimal_format);
    }
    public function get_allowed_expenses_amount($expenses_amount, $expenses_cap_ratio)
    {
        if (!$expenses_amount) {
            return false;
        }
        $allowed_decimal_format = $this->ci->config->item("allowed_decimal_format");
        $allowed_expenses_amount = number_format(bcdiv(bcmul($expenses_amount, $expenses_cap_ratio, $allowed_decimal_format + 2), 100, $allowed_decimal_format + 2), $allowed_decimal_format);
        return str_replace(",", "", $allowed_expenses_amount);
    }
    public function get_allowed_time_logs_amount($effective_effort, $time_logs_cap_ratio, $api = false, $user_rate)
    {
        if (!$effective_effort) {
            return false;
        }
        $allowed_decimal_format = $this->ci->config->item("allowed_decimal_format");
        $total_new_amount = $this->ci->timemask->humanReadableToHours($effective_effort);
        $this->ci->load->model(["money_preference"]);
        $time_logs_amount = bcmul($total_new_amount, $user_rate, $allowed_decimal_format);
        $allowed_time_logs_amount = number_format(bcdiv(bcmul($time_logs_amount, $time_logs_cap_ratio, $allowed_decimal_format + 2), 100, $allowed_decimal_format + 2), $allowed_decimal_format);
        return str_replace(",", "", $allowed_time_logs_amount);
    }
    public function validate_capping_amount($client_id, $legal_case_id, $case_currency_id, $effective_effort = false, $expenses_amount = false, $post_data = NULL, $api = false, $user_id = "")
    {
        $this->ci->load->library("TimeMask");
        $allowed_decimal_format = $this->ci->config->item("allowed_decimal_format");
        $this->ci->load->model("common", "commonfactory");
        $this->ci->common = $this->ci->commonfactory->get_instance();
        if ($this->ci->input->post("rate_system") == "fixed_rate" && $this->ci->input->post("rate")) {
            $get_user_rate_hour = $this->ci->common->get_user_rate_hour($legal_case_id, $user_id, $this->ci->input->post("rate"));
        } else {
            $get_user_rate_hour = $this->ci->common->get_user_rate_hour($legal_case_id, $user_id);
        }
        if (empty($get_user_rate_hour) && $get_user_rate_hour != 0) {
            return "allow";
        }
        if ($this->fetch($legal_case_id)) {
            $cap_amount_enable = $this->get_field("cap_amount_enable");
            $cap_amount = $this->get_field("cap_amount");
            $expenses_cap_ratio = $this->get_field("expenses_cap_ratio");
            $time_logs_cap_ratio = $this->get_field("time_logs_cap_ratio");
            $cap_amount_disallow = $this->get_field("cap_amount_disallow");
            if (isset($post_data)) {
                $cap_amount_enable = $post_data["cap_amount_enable"];
                $cap_amount = $post_data["cap_amount"];
                $expenses_cap_ratio = $post_data["expenses_cap_ratio"];
                $time_logs_cap_ratio = $post_data["time_logs_cap_ratio"];
                $cap_amount_disallow = $post_data["cap_amount_disallow"];
            }
            if ($cap_amount_enable == "0") {
                return "allow";
            }
            $this->ci->load->model("system_preference");
            $matter_cap_after_invoicing = $this->ci->system_preference->get_value_by_key("matterCapAfterInvoicing")["keyValue"] == "1";
            $old_time_log_amount = $this->ci->common->load_client_billable_logs($client_id, $legal_case_id, $case_currency_id, $this->ci->input->get("organization"), $api, $matter_cap_after_invoicing);
            $old_expenses_amount = $this->ci->common->load_client_billable_expenses($client_id, $legal_case_id, $case_currency_id, $api, $matter_cap_after_invoicing);
            $allowed_capping_amount = $cap_amount;
            $get_allowed_expenses_amount = $this->get_allowed_expenses_amount($expenses_amount, $expenses_cap_ratio);
            $get_allowed_time_logs_amount = $this->get_allowed_time_logs_amount($effective_effort, $time_logs_cap_ratio, $api, $get_user_rate_hour);
            $billable_logs_balance = $this->get_allowed_expenses_amount($old_time_log_amount["amount"], $time_logs_cap_ratio);
            $billable_expenses_balance = $this->get_allowed_expenses_amount($old_expenses_amount, $expenses_cap_ratio);
            if ($this->ci->input->post("id") && $effective_effort) {
                $this->ci->load->model("user_activity_log", "user_activity_logfactory");
                $this->ci->user_activity_log = $this->ci->user_activity_logfactory->get_instance();
                $activity_data = $this->ci->user_activity_log->get_activity_details($this->ci->input->post("id"));
                if (!($activity_data["timeStatus"] == "internal" && $this->ci->input->post("timeStatus") == "billable")) {
                    $old_effective_record = $this->get_allowed_time_logs_amount($this->ci->timemask->timeToHumanReadable($activity_data["effectiveEffort"], "h"), $time_logs_cap_ratio, $api, $get_user_rate_hour);
                    $billable_logs_balance = bcsub($billable_logs_balance, $old_effective_record, $allowed_decimal_format);
                }
            }
            $total_amount_billable_new_record = $this->get_total_billable_new_record_amount($billable_logs_balance, $billable_expenses_balance, $get_allowed_time_logs_amount, $get_allowed_expenses_amount);
            return $total_amount_billable_new_record <= $allowed_capping_amount ? "allow" : ($cap_amount_disallow == "1" ? "warning" : "disallow");
        }
        return "disallow";
    }
    public function get_total_remaining_cap_amount($cap_expenses_amount, $get_cap_time_logs_amount, $cap_amount)
    {
        $allowed_decimal_format = $this->ci->config->item("allowed_decimal_format");
        return bcsub($cap_amount, bcadd($cap_expenses_amount, $get_cap_time_logs_amount, $allowed_decimal_format), $allowed_decimal_format);
    }
    public function get_cap_expenses_amount($client_id, $legal_case_id, $case_currency_id, $expenses_cap_ratio)
    {
        $this->ci->load->model("system_preference");
        $matter_cap_after_invoicing = $this->ci->system_preference->get_value_by_key("matterCapAfterInvoicing")["keyValue"] == "1";
        $old_expenses_amount = $this->ci->common->load_client_billable_expenses($client_id, $legal_case_id, $case_currency_id, false, $matter_cap_after_invoicing);
        $billable_expenses_balance = $this->get_allowed_expenses_amount($old_expenses_amount, $expenses_cap_ratio);
        return $billable_expenses_balance;
    }
    public function get_cap_time_logs_amount($client_id, $legal_case_id, $case_currency_id, $time_logs_cap_ratio)
    {
        $this->ci->load->model("system_preference");
        $matter_cap_after_invoicing = $this->ci->system_preference->get_value_by_key("matterCapAfterInvoicing")["keyValue"] == "1";
        $old_time_log_amount = $this->ci->common->load_client_billable_logs($client_id, $legal_case_id, $case_currency_id, $this->ci->input->get("organization"), false, $matter_cap_after_invoicing);
        $billable_logs_balance = $this->get_allowed_expenses_amount($old_time_log_amount["amount"], $time_logs_cap_ratio);
        return $billable_logs_balance;
    }
    public function get_money_currency($api = false)
    {
        $this->ci->load->model("common", "commonfactory");
        $money_system_preference = $this->ci->system_preference->get_key_groups();
        $case_value_currency = $money_system_preference["DefaultValues"]["caseValueCurrency"];
        $this->ci->common = $this->ci->commonfactory->get_instance();
        $case_currency_id = !empty($case_value_currency) ? !$api ? $this->ci->common->load_system_currency_id() : $this->ci->common->load_api_system_currency_id() : "";
        return $case_currency_id;
    }
    public function dashboard_recent_cases($category, $api_params = [])
    {
        $logged_user_id = $api_params["user_id"] ?? $this->logged_user_id;
        $this->ci->load->model("user_preference");
        $this->ci->load->model("language");
        $lang_id = $this->ci->language->get_id_by_session_lang();
        $recent_cases = unserialize($this->ci->user_preference->get_value_by_user("recent_cases", $logged_user_id));
        $response = [];
        if (isset($recent_cases[$category])) {
            $recent_cases = $recent_cases[$category];
            foreach ($recent_cases as $key => $val) {
                if ($val == 0) {
                    unset($recent_cases[$key]);
                }
            }
            $recent_cases = implode(",", array_map("intval", $recent_cases));
            $query["select"] = ["legal_cases.id, CONCAT('" . $this->modelCode . "', legal_cases.id) as case_id, legal_cases.subject, legal_cases.internalReference, legal_cases.description, workflow_status.name as status, CONCAT(user_profiles.firstName, ' ', user_profiles.lastName ) as Assignee, workflow_status.category as status_category, legal_case_stage_languages.name as stage_name, case_types.name as practice_area, clients.name as client_name, legal_case_client_position_languages.name AS client_position, legal_cases.createdOn, legal_cases.modifiedOn, '" . $category . "' AS module", false];
            $query["join"] = [["user_profiles", "user_profiles.user_id = legal_cases.user_id", "left"], ["workflow_status", "workflow_status.id = legal_cases.case_status_id", "left"], ["legal_case_stage_languages", "legal_case_stage_languages.legal_case_stage_id = legal_cases.legal_case_stage_id and legal_case_stage_languages.language_id = '" . $lang_id . "'", "left"], ["case_types", "case_types.id = legal_cases.case_type_id", "left"], ["clients_view clients", "clients.id = legal_cases.client_id and clients.model = \"clients\"", "left"], ["legal_case_client_position_languages", "legal_case_client_position_languages.legal_case_client_position_id = legal_cases.legal_case_client_position_id and legal_case_client_position_languages.language_id = '" . $lang_id . "'", "left"]];
            $query["where"][] = ["legal_cases.id IN (" . $recent_cases . ") AND legal_cases.isDeleted = 0", NULL, false];
            $query["where"][] = ["legal_cases.category", $category == "corporate_matters" ? "Matter" : "Litigation"];
            $query["where"][] = ["legal_cases.archived", $this->notArchived];
            $query["order_by"] = ["FIELD(legal_cases.id, " . $recent_cases . ")"];
            $response = $this->load_all($query);
        }
        return $response;
    }
    public function dashboard_cases_per_status($filters, $case_category)
    {
        $this->ci->load->model("workflow_status", "workflow_statusfactory");
        $this->ci->workflow_status = $this->ci->workflow_statusfactory->get_instance();
        if (0 < $filters["type"]) {
            $workflow_id = $this->ci->workflow_status->get_case_types_workflows($filters["type"], $case_category)["workflow_id"];
            $workflow_statuses = $this->ci->workflow_status->loadAllWorkflowStatuses($workflow_id != NULL ? $workflow_id : 1);
        } else {
            $workflow_statuses = $this->ci->workflow_status->load_all();
        }
        $response["statuses"] = [];
        $response["values"] = [];
        foreach ($workflow_statuses as $status) {
            $query = [];
            $query["select"] = ["legal_cases.id, concat('" . $this->modelCode . "', legal_cases.id, ': ', SUBSTRING(legal_cases.subject, 1, 100)) as name", false];
            $query["where"][] = ["legal_cases.isDeleted", 0];
            $query["where"][] = ["legal_cases.case_status_id", $status["id"]];
            $query["where"][] = ["legal_cases.user_id", $this->logged_user_id];
            if (0 < $filters["type"]) {
                $query["where"][] = ["legal_cases.case_type_id", $filters["type"]];
                $query["where"][] = ["legal_cases.workflow", $workflow_id != NULL ? $workflow_id : 1];
            }
            if (0 < $filters["year"]) {
                $query["where"][] = ["YEAR(legal_cases.caseArrivalDate)", $filters["year"]];
            }
            if (0 < $filters["month"]) {
                $query["where"][] = ["MONTH(legal_cases.caseArrivalDate)", $filters["month"]];
            }
            $query["where"][] = ["legal_cases.category", $case_category[0]];
            $data = $this->load_all($query);
            if (0 < count($data)) {
                $response["statuses"][] = $status["name"];
                $response["values"][] = count($data);
                $response["names"][] = implode(",", array_column($data, "name"));
            }
        }
        return $response;
    }
    public function get_cases_per_success_probability($date_filter)
    {
        $this->ci->load->model("legal_case_success_probability", "legal_case_success_probabilityfactory");
        $this->ci->legal_case_success_probability = $this->ci->legal_case_success_probabilityfactory->get_instance();
        $probabilities = $this->ci->legal_case_success_probability->load_list_per_language();
        unset($probabilities[""]);
        return $this->get_cases_per_field("legal_case_success_probability_id", $probabilities, $date_filter);
    }
    public function get_cases_per_practice_area($date_filter)
    {
        $this->ci->load->model("case_type");
        $types = $this->ci->case_type->load_list(["where" => [["litigation", "yes"], ["isDeleted", 0]], "order_by" => ["name", "asc"]]);
        return $this->get_cases_per_field("case_type_id", $types, $date_filter);
    }
    public function get_cases_per_stage($date_filter)
    {
        $this->ci->load->model("legal_case_stage", "legal_case_stagefactory");
        $this->ci->legal_case_stage = $this->ci->legal_case_stagefactory->get_instance();
        $stages = $this->ci->legal_case_stage->load_list_per_case_category_per_language("litigation");
        return $this->get_cases_per_field("legal_case_stage_id", $stages, $date_filter);
    }
    public function get_cases_per_client($date_filter)
    {
        $this->ci->load->model("client");
        $clients = $this->ci->client->load_clients_list();
        return $this->get_cases_per_field("client_id", $clients, $date_filter);
    }
    public function get_cases_per_client_position($date_filter)
    {
        $this->ci->load->model("legal_case_client_position", "legal_case_client_positionfactory");
        $this->ci->legal_case_client_position = $this->ci->legal_case_client_positionfactory->get_instance();
        $client_positions = $this->ci->legal_case_client_position->load_list_per_language();
        return $this->get_cases_per_field("legal_case_client_position_id", $client_positions, $date_filter);
    }
    public function get_cases_per_assignee($date_filter)
    {
        $this->ci->load->model("user", "userfactory");
        $this->ci->user = $this->ci->userfactory->get_instance();
        $users = $this->ci->user->load_all_users_list();
        return $this->get_cases_per_field("user_id", $users, $date_filter);
    }
    public function get_cases_per_status($filter)
    {
        $this->ci->load->model("workflow_status", "workflow_statusfactory");
        $this->ci->workflow_status = $this->ci->workflow_statusfactory->get_instance();
        $workflow_statuses = $this->ci->workflow_status->loadListWorkflowStatuses();
        return $this->get_cases_per_field("case_status_id", $workflow_statuses, $filter);
    }
    public function get_cases_per_field($field, $data, $filter)
    {
        $response["names"] = [];
        $response["values"] = [];
        if (!empty($data)) {
            $query = [];
            $query["select"] = ["legal_cases." . $field . ",  COUNT(" . $field . ") as count", false];
            if (empty($filter["type"]) || $filter["type"] != "all") {
                $query["where"][] = ["legal_cases.category", empty($filter["type"]) || $filter["type"] != "matter" ? "litigation" : "matter"];
            }
            $query["where"][] = ["legal_cases.isDeleted", 0];
            $query["where"][] = ["legal_cases.caseArrivalDate >= '" . $filter["from"] . "'", NULL, false];
            $query["where"][] = ["legal_cases.caseArrivalDate <= '" . $filter["to"] . "'", NULL, false];
            $query["group_by"] = ["legal_cases." . $field];
            if ($query_data = $this->load_list($query, ["key" => $field, "value" => "count"])) {
                arsort($query_data);
                foreach ($query_data as $id => $value) {
                    if (0 < $value) {
                        $response["names"][] = isset($data[$id]) ? $data[$id] : (isset($data[str_pad((string) $id, 10, "0", STR_PAD_LEFT)]) ? $data[str_pad((string) $id, 10, "0", STR_PAD_LEFT)] : $this->ci->lang->line("none"));
                        $response["values"][] = (int) $value;
                    }
                }
            }
        }
        return $response;
    }
    public function get_case_stages_per_court($date_filter)
    {
        $this->ci->load->model("court");
        $courts = $this->ci->court->load_courts_list();
        return $this->get_case_stages_per_field("court_id", $courts, $date_filter);
    }
    public function get_case_stages_per_court_type($date_filter)
    {
        $this->ci->load->model("court_type");
        $court_types = $this->ci->court_type->load_list([]);
        return $this->get_case_stages_per_field("court_type_id", $court_types, $date_filter);
    }
    public function get_case_stages_per_court_region($date_filter)
    {
        $this->ci->load->model("court");
        $court_regions = $this->ci->court->load_regions_list();
        return $this->get_case_stages_per_field("court_region_id", $court_regions, $date_filter);
    }
    public function get_case_stages_per_field($field, $data, $date_filter)
    {
        $_table = $this->_table;
        $this->_table = "legal_case_litigation_details AS litigation_details";
        $response["names"] = [];
        $response["values"] = [];
        if (!empty($data)) {
            $query = [];
            $query["select"] = ["litigation_details." . $field . ", COUNT(" . $field . ") as count", false];
            $query["join"][] = ["legal_cases", "legal_cases.id = litigation_details.legal_case_id", "left"];
            $query["where"][] = ["legal_cases.isDeleted", 0];
            $query["where"][] = ["legal_cases.caseArrivalDate >= '" . $date_filter["from"] . "'", NULL, false];
            $query["where"][] = ["legal_cases.caseArrivalDate <= '" . $date_filter["to"] . "'", NULL, false];
            $query["group_by"] = ["litigation_details." . $field];
            if ($query_data = $this->load_list($query, ["key" => $field, "value" => "count"])) {
                arsort($query_data);
                foreach ($query_data as $id => $value) {
                    if (0 < $value) {
                        $response["names"][] = isset($data[$id]) ? $data[$id] : $this->ci->lang->line("none");
                        $response["values"][] = (int) $value;
                    }
                }
            }
        }
        $this->_table = $_table;
        return $response;
    }
    public function count_litigation_cases_per_arrival_date($date_filter)
    {
        $query["select"] = ["COUNT(0) count, MONTH(caseArrivalDate) month"];
        $query["where"] = [["legal_cases.caseArrivalDate >= '" . $date_filter["from"] . "'", NULL, false], ["legal_cases.caseArrivalDate <= '" . $date_filter["to"] . "'", NULL, false], ["legal_cases.isDeleted", 0], ["legal_cases.category", "litigation"]];
        $query["group_by"] = ["MONTH(caseArrivalDate)"];
        $data = $this->load_list($query, ["key" => "month", "value" => "count"]);
        for ($i = 1; $i <= 12; $i++) {
            $return["values"][] = isset($data[$i]) ? $data[$i] : 0;
        }
        $return["names"] = $this->ci->lang->line("months_array");
        return $return;
    }
    public function get_cases_per_assignee_per_status($date_filter)
    {
        $this->ci->load->model("user", "userfactory");
        $this->ci->user = $this->ci->userfactory->get_instance();
        $this->ci->load->model("workflow_status", "workflow_statusfactory");
        $this->ci->workflow_status = $this->ci->workflow_statusfactory->get_instance();
        $workflow_statuses = $this->ci->workflow_status->loadListWorkflowStatuses();
        $users = $this->ci->user->load_users_list();
        $return["categories"] = array_values($users);
        $return["names"] = array_values($workflow_statuses);
        foreach ($workflow_statuses as $status_id => $value) {
            $count = 0;
            $select = "";
            foreach ($users as $user_id => $user) {
                $count++;
                $select .= "COUNT(CASE WHEN legal_cases.user_id = " . $user_id . " THEN legal_cases.id END) as '" . $user . "'" . ($count < sizeof($users) ? ", " : "");
            }
            $query = [];
            $query["select"] = [$select, false];
            $query["where"] = [["legal_cases.caseArrivalDate >= '" . $date_filter["from"] . "'", NULL, false], ["legal_cases.caseArrivalDate <= '" . $date_filter["to"] . "'", NULL, false], ["legal_cases.case_status_id", $status_id], ["legal_cases.category", "litigation"]];
            $data = $this->load($query);
            $return["values"][] = $data ? array_map("intval", array_values($data)) : 0;
        }
        return $return;
    }
    public function get_cases_per_assignee_per_stage($date_filter)
    {
        $this->ci->load->model("user", "userfactory");
        $this->ci->user = $this->ci->userfactory->get_instance();
        $this->ci->load->model("legal_case_stage", "legal_case_stagefactory");
        $this->ci->legal_case_stage = $this->ci->legal_case_stagefactory->get_instance();
        $stages = $this->ci->legal_case_stage->load_list_per_case_category_per_language("litigation");
        $users = $this->ci->user->load_users_list();
        $return["categories"] = array_values($users);
        $return["names"] = array_values($stages);
        foreach ($stages as $stage_id => $value) {
            $count = 0;
            $select = "";
            foreach ($users as $user_id => $user) {
                $count++;
                $select .= "COUNT(CASE WHEN legal_cases.user_id = " . $user_id . " THEN legal_cases.id END) as '" . $user . "'" . ($count < sizeof($users) ? ", " : "");
            }
            $query = [];
            $query["select"] = [$select, false];
            $query["where"] = [["legal_cases.caseArrivalDate >= '" . $date_filter["from"] . "'", NULL, false], ["legal_cases.caseArrivalDate <= '" . $date_filter["to"] . "'", NULL, false], ["legal_cases.legal_case_stage_id" . ($stage_id == "" ? " IS NULL" : "= " . $stage_id), NULL, false], ["legal_cases.category", "litigation"]];
            $data = $this->load($query);
            $return["values"][] = $data ? array_map("intval", array_values($data)) : 0;
        }
        return $return;
    }
    public function get_Total_effective_effort_cost($id, $organization_id, $filter = [], $get_all_rows = false, $my_time_logs = false)
    {
        $query = [];
        $response = [];
        $system_preferences = $this->ci->session->userdata("systemPreferences");
        $user_rate_per_entities = unserialize($system_preferences["userRatePerHour"]);
        $user_rate_per_hour = isset($user_rate_per_entities[$organization_id]) && $user_rate_per_entities[$organization_id] ? $user_rate_per_entities[$organization_id] : 0;
        $user_rate_per_hour = number_format((double) $user_rate_per_hour, 2, ".", "");
        $table = $this->_table;
        $this->_table = "user_activity_logs_full_details as ual";
        if (!$get_all_rows) {
            $query["select"][] = ["SUM(effectiveEffort) as totalEffectiveEffort", false];
            $query["select"][] = ["SUM(IF(timeStatus = 'internal', effectiveEffort , 0)) as totalNoneBillableEffort", false];
            $query["select"][] = ["SUM(IF(timeStatus = 'internal', 0 , effectiveEffort )) as totalBillableEffort", false];
            $query["select"][] = ["SUM(IF(ual.timeStatus = 'billable', IF(ual.rate IS NULL, IF(urphpc.ratePerHour IS NULL, IF(cs.rate_per_hour IS NULL, IF(urph.ratePerHour IS NULL, " . $user_rate_per_hour . ", urph.ratePerHour),\r\n                               cs.rate_per_hour), urphpc.ratePerHour), ual.rate) * effectiveEffort, 0)) as totalCost", false];
        } else {
            $query["select"][] = ["ual.*, (CASE\r\n                                                WHEN ual.rate IS NULL THEN (CASE\r\n                                                                                WHEN urphpc.ratePerHour IS NULL THEN (CASE\r\n                                                                                                                          WHEN cs.rate_per_hour IS NULL\r\n                                                                                                                              THEN (CASE WHEN urph.ratePerHour IS NULL THEN " . $user_rate_per_hour . " ELSE urph.ratePerHour END)\r\n                                                                                                                          ELSE cs.rate_per_hour END)\r\n                                                                                ELSE urphpc.ratePerHour END)\r\n                                                ELSE\r\n                                                    ual.rate\r\n                                               END) as ratePerHour", false];
        }
        $query["join"] = [["user_rate_per_hour urph", "urph.user_id = ual.user_id AND urph.organization_id = " . $organization_id, "left"], ["case_rate cs", "ual.legal_case_id = cs.case_id AND cs.organization_id = " . $organization_id, "left"], ["user_rate_per_hour_per_case urphpc", "urphpc.user_id = ual.user_id and urphpc.case_id = ual.legal_case_id AND urphpc.organization_id = " . $organization_id, "left"]];
        $query["where"][] = ["(ual.legal_case_id = " . $id . " OR ual.task_id IN (Select tasks.id from tasks where tasks.legal_case_id =" . $id . "))"];
        if (!empty($filter) && is_array($filter) && isset($filter["filters"])) {
            foreach ($filter["filters"] as $_filter) {
                $this->prep_k_filter($_filter, $query, $filter["logic"]);
            }
            unset($_filter);
        }
        if ($my_time_logs) {
            $query["where"][] = ["ual.user_id", $this->ci->session->userdata("AUTH_user_id")];
        }
        $response["data"] = $this->load_all($query);
        $this->_table = $table;
        return $response;
    }
    public function case_outsourced($case_id)
    {
        return $this->case_has_outsourcing_record($case_id);
    }
    public function case_has_outsourcing_record($case_id)
    {
        $query = ["select" => "count(legal_case_outsources.legal_case_id) AS counts", "join" => [["legal_case_outsources", "legal_case_outsources.legal_case_id = legal_cases.id"]], "where" => [["legal_cases.id", $case_id]]];
        $result = $this->ci->legal_case->load($query);
        return $result["counts"] ? 0 < $result["counts"] : false;
    }
    public function get_legal_case_contact_by_case_contact($case_id, $contact_id)
    {
    }
    public function get_document_generator_data($id, $category = "")
    {
        return $category == "IP" ? $this->load_intellectual_property($id) : $this->load_case_details($id);
    }
    public function load_task_legal_case_users($case_id)
    {
        $table = $this->_table;
        $this->_table = "legal_case_users";
        $query["select"] = ["UP.user_id as id, CONCAT( UP.firstName, ' ', UP.lastName ) as name,UP.status as status", false];
        $query["join"] = ["user_profiles UP", "UP.user_id = legal_case_users.user_id", "inner"];
        $query["where"] = ["legal_case_users.legal_case_id", $case_id];
        $response = $this->load_all($query);
        $this->_table = $table;
        return $response;
    }
    public function get_cases_names_by_ids($ids)
    {
        $query = [];
        $query["select"] = ["concat('M', `legal_cases`.`id`, ':' , subject) AS name", false];
        $query["where_in"] = ["legal_cases.id", $ids];
        return $this->load_all($query);
    }
    public function shared_documents_with_advisors($legal_case_id)
    {
        $legal_cases = $this->get_shared_documents_with_legal_cases();
        return is_array($legal_cases) && in_array($legal_case_id, $legal_cases);
    }
    public function get_shared_documents_with_legal_cases()
    {
        $this->ci->load->model("system_preference");
        $system_preferences = $this->ci->system_preference->get_key_groups();
        $sharedDocumentsLegalCases = $system_preferences["AdvisorConfig"]["SharedDocumentsLegalCases"] ?? [];
        if (!empty($sharedDocumentsLegalCases)) {
            return unserialize($sharedDocumentsLegalCases);
        }
        return NULL;
    }
    public function share_documents_with_advisors($caseId)
    {
        $this->ci->load->model("document_management_system", "document_management_systemfactory");
        $this->ci->document_management_system = $this->ci->document_management_systemfactory->get_instance();
        $query["where"] = [["module", "case"], ["module_record_id", $caseId]];
        $result = $this->ci->document_management_system->load_all($query);
        foreach ($result as $documet) {
            $this->ci->document_management_system->fetch($documet["id"]);
            $this->ci->document_management_system->set_field("visible_in_ap", 1);
            $this->ci->document_management_system->update();
        }
        return true;
    }
    public function load_all_cases_lawyers_contributors_emails($id)
    {
        $query = [];
        $query["select"] = ["contact_emails.email", false];
        $query["join"] = [["contact_emails", "contact_emails.contact_id = legal_cases_contacts.contact_id", "inner"]];
        $query["where"] = [["legal_cases_contacts.case_id", $id], ["legal_cases_contacts.contactType", "contributor"]];
        $this->prep_query($query);
        $result = $this->ci->db->get("legal_cases_contacts");
        $result_array = $result->result_array();
        $return = [];
        foreach ($result_array as $result_item) {
            $return[] = $result_item["email"];
        }
        return $return;
    }

}
class mysqli_Legal_case extends mysql_Legal_case
{
}
class sqlsrv_Legal_case extends mysql_Legal_case
{
    public function k_load_all_cases($filter, $sortable, $pagingOn = false, &$query = [], &$stringQuery = "", $page_number = "")
    {
        $response = $this->get_legal_cases_grid_query_web($filter, $sortable, $page_number, $pagingOn);
        $stringQuery = $response["stringQuery"];
        $query = $response["query"];
        unset($response["stringQuery"]);
        unset($response["query"]);
        return $response;
    }
    public function load_daily_agenda_cases()
    {
        $query = [];
        $query["select"] = ["legal_cases.id, legal_cases.subject, users.email, legal_cases.category", false];
        $query["join"] = [["users", "users.id = legal_cases.user_id"]];
        $query["where"][] = ["legal_cases.archived = 'no' AND CONVERT(varchar, getdate(), 23) = legal_cases.dueDate  AND legal_cases.isDeleted = 0", NULL, false];
        return $this->load_all($query);
    }
    public function k_load_all_cases_per_company($filter, $sortable, $pagingOn = false, &$query = [])
    {
        $_table = $this->_table;
        $this->_table = "legal_cases_per_company AS legal_cases";
        $query = [];
        $response = [];
        $query["select"] = ["legal_cases.id, \r\n            legal_cases.case_status_id, \r\n            legal_cases.case_type_id, \r\n            legal_cases.provider_group_id, \r\n            legal_cases.user_id, \r\n            legal_cases.contact_id, \r\n            legal_cases.client_id, \r\n            legal_cases.subject, \r\n            legal_cases.description, \r\n            legal_cases.priority, \r\n            legal_cases.arrivalDate, \r\n            legal_cases.dueDate, \r\n            legal_cases.statusComments, \r\n            legal_cases.category, \r\n            legal_cases.caseValue, \r\n            legal_cases.internalReference, \r\n            legal_cases.externalizeLawyers, \r\n            CAST( legal_cases.estimatedEffort AS nvarchar ) as estimatedEffort, \r\n            legal_cases.createdOn, \r\n            legal_cases.createdBy, \r\n            legal_cases.modifiedOn, \r\n            legal_cases.modifiedBy, \r\n            legal_cases.archived, \r\n            legal_cases.private, \r\n            CAST( legal_cases.effectiveEffort AS nvarchar ) as effectiveEffort, \r\n            legal_cases.caseID, \r\n            legal_cases.status, \r\n            legal_cases.type, \r\n            legal_cases.providerGroup, \r\n            legal_cases.assignee, \r\n            legal_cases.archivedCases, \r\n            legal_cases.company, \r\n            legal_cases.role, \r\n            legal_cases.contactOutsourceTo, \r\n            legal_cases.companyOutsourceTo, \r\n            legal_cases.contactContributor", false];
        if (is_array($filter)) {
            if (isset($filter["filters"])) {
                foreach ($filter["filters"] as $_filter) {
                    if (!empty($_filter["filters"][0]["field"]) && in_array($_filter["filters"][0]["field"], ["effectiveEffort", "estimatedEffort"])) {
                        $system_preferences = $this->ci->session->userdata("systemPreferences");
                        $this->ci->load->library("TimeMask");
                        $_filter["filters"][0]["value"] = $this->ci->timemask->humanReadableToHours($_filter["filters"][0]["value"]);
                    }
                    $this->prep_k_filter($_filter, $query, $filter["logic"]);
                }
                unset($_filter);
            }
            if (isset($filter["customFields"])) {
                $this->ci->load->model("custom_field", "custom_fieldfactory");
                $this->ci->custom_field = $this->ci->custom_fieldfactory->get_instance();
                $this->ci->custom_field->prep_custom_field_filters($this->modelName, $filter["customFields"], $query);
            }
        }
        $query["where"][] = $this->get_matter_privacy_conditions($this->logged_user_id, $this->override_privacy);
        $query["where"][] = ["legal_cases.category !='IP'", NULL, false];
        $this->prep_query($query);
        $response["totalRows"] = $this->ci->db->get($this->get("_table"))->num_rows();
        if (is_array($sortable) && !empty($sortable)) {
            foreach ($sortable as $_sort) {
                $query["order_by"][] = [$_sort["field"], $_sort["dir"]];
            }
        } else {
            $query["order_by"] = ["legal_cases.id desc"];
        }
        if ($limit = $this->ci->input->post("take", true)) {
            $query["limit"] = [$this->ci->input->post("take", true), $this->ci->input->post("skip", true)];
            $paginationConf = ["urlPrefix" => ""];
            $paginationConf["inPage"] = $this->ci->input->post("take", true);
        }
        $response["data"] = $pagingOn ? parent::paginate($query, $paginationConf) : parent::load_all($query);
        $this->_table = $_table;
        return $response;
    }
    public function k_load_all_cases_per_contact($filter, $sortable, $pagingOn = false, &$query = [], $table = "legal_cases_per_contact AS legal_cases")
    {
        $_table = $this->_table;
        $this->_table = $table;
        $query = [];
        $response = [];
        $query["select"] = ["legal_cases.id, \r\n            legal_cases.case_status_id, \r\n            legal_cases.case_type_id, \r\n            legal_cases.provider_group_id, \r\n            legal_cases.user_id, \r\n            legal_cases.contact_id, \r\n            legal_cases.client_id, \r\n            legal_cases.subject, \r\n            legal_cases.description, \r\n            legal_cases.priority, \r\n            legal_cases.arrivalDate, \r\n            legal_cases.dueDate, \r\n            legal_cases.statusComments, \r\n            legal_cases.category, \r\n            legal_cases.caseValue, \r\n            legal_cases.internalReference, \r\n            legal_cases.externalizeLawyers, \r\n            CAST( legal_cases.estimatedEffort AS nvarchar ) as estimatedEffort, \r\n            legal_cases.createdOn, \r\n            legal_cases.createdBy, \r\n            legal_cases.modifiedOn, \r\n            legal_cases.modifiedBy, \r\n            legal_cases.archived, \r\n            legal_cases.private, \r\n            CAST( legal_cases.effectiveEffort AS nvarchar ) as effectiveEffort, \r\n            legal_cases.caseID, \r\n            legal_cases.status, \r\n            legal_cases.type, \r\n            legal_cases.providerGroup, \r\n            legal_cases.assignee, \r\n            legal_cases.archivedCases, \r\n            legal_cases.contact, \r\n            legal_cases.role, \r\n            legal_cases.contactOutsourceTo, \r\n            legal_cases.companyOutsourceTo, \r\n            legal_cases.contactContributor", false];
        if (is_array($filter)) {
            if (isset($filter["filters"])) {
                foreach ($filter["filters"] as $_filter) {
                    if (!empty($_filter["filters"][0]["field"]) && in_array($_filter["filters"][0]["field"], ["effectiveEffort", "estimatedEffort"])) {
                        $system_preferences = $this->ci->session->userdata("systemPreferences");
                        $this->ci->load->library("TimeMask");
                        $_filter["filters"][0]["value"] = $this->ci->timemask->humanReadableToHours($_filter["filters"][0]["value"]);
                    }
                    $this->prep_k_filter($_filter, $query, $filter["logic"]);
                }
                unset($_filter);
            }
            if (isset($filter["customFields"])) {
                $this->ci->load->model("custom_field", "custom_fieldfactory");
                $this->ci->custom_field = $this->ci->custom_fieldfactory->get_instance();
                $this->ci->custom_field->prep_custom_field_filters($this->modelName, $filter["customFields"], $query);
            }
        }
        $query["where"][] = $this->get_matter_privacy_conditions($this->logged_user_id, $this->override_privacy);
        $query["where"][] = ["legal_cases.category !='IP'", NULL, false];
        $this->prep_query($query);
        $response["totalRows"] = $this->ci->db->get($this->get("_table"))->num_rows();
        if (is_array($sortable) && !empty($sortable)) {
            foreach ($sortable as $_sort) {
                $query["order_by"][] = [$_sort["field"], $_sort["dir"]];
            }
        } else {
            $query["order_by"] = ["legal_cases.id desc"];
        }
        if ($limit = $this->ci->input->post("take", true)) {
            $query["limit"] = [$this->ci->input->post("take", true), $this->ci->input->post("skip", true)];
            $paginationConf = ["urlPrefix" => ""];
            $paginationConf["inPage"] = $this->ci->input->post("take", true);
        }
        $response["data"] = $pagingOn ? parent::paginate($query, $paginationConf) : parent::load_all($query);
        $this->_table = $_table;
        return $response;
    }
    public function load_case($case_id)
    {
        $loadQuery = ["select" => ["legal_cases.*,case_types.name as practice_area,
         CAST( legal_cases.estimatedEffort AS nvarchar ) as estimatedEffort,CAST( lcee.effectiveEffort AS nvarchar ) as effectiveEffort,
          workflow_status.name as Status,workflow_status.category as workflow_status_category,
           ( user_profiles.firstName + ' ' + user_profiles.lastName ) as Assignee, (user_profiles.firstName + ' ' + user_profiles.lastName ) as Assignee,
            ( referredByContact.firstName + ' ' + referredByContact.lastName ) AS referredByName,
            ( requestedByContact.firstName + ' ' + requestedByContact.lastName ) AS requestedByName,
             user_profiles.user_id as user_id, 
            clients_view.name as client_name", false],
            "join" => [["user_profiles", "user_profiles.user_id = legal_cases.user_id", "left"],
                ["workflow_status", "workflow_status.id = legal_cases.case_status_id", "left"],
                ["legal_case_effective_effort AS lcee", "lcee.legal_case_id = legal_cases.id", "left"],
                ["contacts AS referredByContact", "referredByContact.id = legal_cases.referredBy", "left"],
                ["contacts AS requestedByContact", "requestedByContact.id = legal_cases.requestedBy", "left"],
                ["clients_view", "clients_view.id = legal_cases.client_id and clients_view.model = 'clients'", "left"],
                ["case_types", "case_types.id = legal_cases.case_type_id", "left"]],
            "where" => [["legal_cases.id = ", $case_id], ["legal_cases.isDeleted = ", 0]]];
        return $this->load($loadQuery);
    }
    public function load_case_details($case_id, $is_api = false)
    {
        $lang_id = $this->get_lang_id($is_api);
        $loadQuery = ["select" => ["legal_cases.*, workflow_status.name as status, case_types.name as practice_area, legal_case_stage_languages.name as stage_name, provider_groups.name as assigned_team, workflows.name as workflow_name, ( user_profiles.firstName + ' ' + user_profiles.lastName ) as assignee, ( referredByContact.firstName + ' ' + referredByContact.lastName ) AS referredByName, ( requestedByContact.firstName + ' ' + requestedByContact.lastName ) AS requestedByName, user_profiles.user_id as user_id, clients_view.name as client_name, CAST( legal_cases.estimatedEffort AS nvarchar ) as estimatedEffort,CAST( lcee.effectiveEffort AS nvarchar ) as effectiveEffort", false], "join" => [["user_profiles", "user_profiles.user_id = legal_cases.user_id", "left"], ["legal_case_effective_effort AS lcee", "lcee.legal_case_id = legal_cases.id", "left"], ["workflow_status", "workflow_status.id = legal_cases.case_status_id", "left"], ["contacts AS referredByContact", "referredByContact.id = legal_cases.referredBy", "left"], ["contacts AS requestedByContact", "requestedByContact.id = legal_cases.requestedBy", "left"], ["clients_view", "clients_view.id = legal_cases.client_id and clients_view.model = 'clients'", "left"], ["case_types", "case_types.id = legal_cases.case_type_id", "left"], ["legal_case_stage_languages", "legal_case_stage_languages.legal_case_stage_id = legal_cases.legal_case_stage_id and legal_case_stage_languages.language_id = '" . $lang_id . "'", "left"], ["provider_groups", "provider_groups.id=legal_cases.provider_group_id", "left"], ["workflows", "workflows.id = legal_cases.workflow", "left"]], "where" => [["legal_cases.id = ", $case_id], ["legal_cases.isDeleted = ", 0]]];
        return $this->load($loadQuery);
    }
    public function load_intellectual_property($ip_id)
    {
        $loadQuery = ["select" => ["legal_cases.id, legal_cases.user_id, legal_cases.subject, legal_cases.description, legal_cases.category, legal_cases.arrivalDate as filed_on, legal_cases.caseArrivalDate as arrival_date, (user_profiles.firstName + ' '+ user_profiles.lastName ) as assignee, ip_details.filingNumber, ip_rights.name as ip_right, ip_classes.name as ip_class, ip_subcategories.name as ip_subcategory, ip_statuses.name as ip_status, ip_names.name as ip_name, clients_view.name as client_name, agents.name as agent_name", false], "join" => [["user_profiles", "user_profiles.user_id = legal_cases.user_id", "left"], ["ip_details", "ip_details.legal_case_id = legal_cases.id", "left"], ["intellectual_property_rights ip_rights", "ip_rights.id = ip_details.intellectual_property_right_id", "left"], ["ip_classes", "ip_classes.id = ip_details.ip_class_id", "left"], ["ip_subcategories", "ip_subcategories.id = ip_details.ip_subcategory_id", "left"], ["ip_statuses", "ip_statuses.id = ip_details.ip_status_id", "left"], ["ip_names", "ip_names.id = ip_details.ip_name_id", "left"], ["clients_view agents", "agents.member_id = ip_details.agentId and agents.model = 'clients' and agents.type = (CASE WHEN ip_details.agentType = 'contact' THEN 'Person' ELSE 'Company' END)", "left"], ["clients_view", "clients_view.id = legal_cases.client_id AND clients_view.model = 'clients'", "left"]], "where" => [["legal_cases.id = ", $ip_id], ["legal_cases.isDeleted = ", "0"]]];
        return $this->load($loadQuery);
    }
    public function load_contacts($case_id)
    {
        $contacts = ["contact" => [], "external lawyer" => [], "judge" => [], "opponentLawyer" => []];
        if ($case_id < 1) {
            return $contacts;
        }
        $case_contacts = $this->ci->db->select(["contacts.id as id, ( CASE WHEN contacts.father!='' THEN contacts.firstName + ' '+ contacts.father + ' ' + contacts.lastName ELSE contacts.firstName+' '+contacts.lastName END ) AS name, contactType, contacts.isLawyer, legal_case_contact_roles.name as role, legal_cases_contacts.comments as comments", false])->join("contacts", "contacts.id = legal_cases_contacts.contact_id", "inner")->join("legal_case_contact_roles", "legal_case_contact_roles.id = legal_cases_contacts.legal_case_contact_role_id", "left")->where("legal_cases_contacts.case_id", $case_id)->where("contacts.status", "Active")->order_by("contacts.firstName", "asc")->order_by("contacts.lastName", "asc")->get("legal_cases_contacts");
        if (!$case_contacts->num_rows()) {
            return $contacts;
        }
        foreach ($case_contacts->result() as $contact) {
            $contacts[$contact->contactType][(string) $contact->id] = ["name" => $contact->name, "isLawyer" => $contact->isLawyer, "role" => $contact->role, "comments" => $contact->comments];
        }
        return $contacts;
    }
    public function load_watchers_users($case_id)
    {
        $users = [];
        $data = [];
        $status = [];
        if ($case_id < 1) {
            return $users;
        }
        $case_users = $this->ci->db->select(["UP.user_id as id, ( UP.firstName + ' ' + UP.lastName ) as name,UP.status as status", false])->join("user_profiles UP", "UP.user_id = legal_case_users.user_id", "inner")->where("legal_case_users.legal_case_id", $case_id)->get("legal_case_users");
        if (!$case_users->num_rows()) {
            return $users;
        }
        foreach ($case_users->result() as $user) {
            $users[(string) $user->id] = $user->name;
        }
        foreach ($case_users->result() as $user) {
            $status[(string) $user->id] = $user->status;
        }
        $data[0] = $users;
        $data[1] = $status;
        return $data;
    }
    public function lookup($term)
    {
        $fullSelectFlag = $this->ci->input->get("fullSelectFlag", true);
        $configList = ["key" => "legal_cases.id", "value" => "name"];
        $configQury["select"] = ["legal_cases.*, ( '" . $this->modelCode . "' +  CAST( legal_cases.id AS nvarchar ) ) AS caseID , CASE WHEN Datalength(legal_cases.subject) > 45 THEN (SUBSTRING(legal_cases.subject, 1, 45) + ' ' + '...' ) ELSE legal_cases.subject END as subject, subject AS fullSubject,clients_view.name as clientName, workflow_status.name as status, ( user_profiles.firstName + ' ' + user_profiles.lastName ) as createdByName,( modified.firstName + ' ' + modified.lastName ) AS modifiedByName", false];
        $configQury["join"] = [["clients_view", "clients_view.id=legal_cases.client_id AND clients_view.model = 'clients'", "left"], ["workflow_status", "workflow_status.id = legal_cases.case_status_id", "left"], ["user_profiles", "user_profiles.user_id = legal_cases.createdBy", "left"], ["user_profiles modified", "modified.user_id = legal_cases.modifiedBy", "left"]];
        if ($fullSelectFlag == "true") {
            $configQury["select"] = ["legal_cases.*,clients_view.name as clientName", false];
        }
        $configQury["where"] = [];
        $configQury["where"][] = $this->get_matter_privacy_conditions($this->logged_user_id, $this->override_privacy);
        if (!empty($term)) {
            $configQury["where"] = [];
            $modelCode = substr($term, 0, 1);
            $ID = substr($term, 1);
            if (strcasecmp($modelCode, $this->get("modelCode")) === 0 && is_numeric($ID)) {
                $qId = substr($term, 1);
                if (is_numeric($qId)) {
                    $configQury["where"][] = ["legal_cases.id = " . $qId, NULL, false];
                }
                $configQury["where"][] = $this->get_matter_privacy_conditions($this->logged_user_id, $this->override_privacy);
                $configQury["where"][] = ["legal_cases.archived = '" . $this->notArchived . "'"];
            } else {
                $term = $this->ci->db->escape_like_str($term);
                $configQury["where"][] = ["(legal_cases.subject LIKE '%" . $term . "%' or clients_view.name LIKE '%" . $term . "%' or legal_cases.internalReference LIKE '%" . $term . "%')", NULL, false];
                $configQury["where"][] = ["legal_cases.archived = '" . $this->notArchived . "'"];
                $configQury["where"][] = $this->get_matter_privacy_conditions($this->logged_user_id, $this->override_privacy);
            }
        }
        if ($moreFilters = $this->ci->input->get("more_filters")) {
            foreach ($moreFilters as $_field => $_term) {
                if ($_field == "caseType" && $_term == "ExtractIP") {
                    $configQury["where"][] = ["legal_cases.category IN ('Matter','Litigation')", NULL, false];
                } else {
                    $configQury["where"][] = ["legal_cases." . $_field, $_term];
                }
            }
            unset($_field);
            unset($_term);
        }
        return $this->load_all($configQury, $configList);
    }
    public function universal_search($q, $pagingOn = true, $type = "Litigation")
    {
        $query = [];
        $q2 = $this->escape_universal_search_keyword($q);
        $category = $type;
        $select = "( '" . $this->modelCode . "' + CAST( legal_cases.id AS nvarchar ) ) as caseId,legal_cases.id, ( user_profiles.firstName + ' ' + user_profiles.lastName ) as assignee, \r\n        priority, dueDate, workflow_status.name as status, legal_cases.subject AS fullSubject, internalReference as reference";
        $select = $this->ci->session->userdata("AUTH_language") == "arabic" ? $select . ", CASE WHEN LEN(legal_cases.subject) > 60 THEN '...' + ' ' + ( SUBSTRING(legal_cases.subject, 1, 60)) ELSE legal_cases.subject END AS subject" : $select . ", CASE WHEN LEN(legal_cases.subject) > 60 THEN (SUBSTRING(legal_cases.subject, 1, 60)) + ' ' + '...' ELSE legal_cases.subject END AS subject";
        $query["select"] = [$select, false];
        $query["where"][] = ["(legal_cases.subject LIKE '%" . $q2 . "%' escape '\\' OR legal_cases.description LIKE '%" . $q2 . "%' escape '\\' OR legal_cases.internalReference LIKE '%" . $q2 . "%' escape '\\' OR legal_cases.id = CASE WHEN SUBSTRING('" . $q2 . "', 1, 1) = '" . $this->modelCode . "' THEN (CASE WHEN ISNUMERIC(SUBSTRING('" . $q2 . "', 2, 9)) = 1 THEN SUBSTRING('" . $q2 . "', 2, 9) ELSE 0 END) ELSE (CASE WHEN ISNUMERIC('" . $q2 . "') = 1 THEN CAST('" . $q2 . "' as bigint) ELSE 0 END) END) AND legal_cases.category LIKE '%" . $category . "%' AND legal_cases.archived='" . $this->notArchived . "'", NULL, false];
        $query["where"][] = $this->get_matter_privacy_conditions($this->logged_user_id, $this->override_privacy);
        $query["join"] = [["user_profiles", "user_profiles.user_id = legal_cases.user_id", "left"], ["workflow_status", "workflow_status.id = legal_cases.case_status_id", "inner"]];
        return $pagingOn ? parent::paginate($query, ["urlPrefix" => ""]) : parent::load_all($query);
    }
    public function external_reference_universal_search($q, $pagingOn = true)
    {
        $query = [];
        $q2 = $this->escape_universal_search_keyword($q);
        $select = "( '" . $this->modelCode . "' + CAST( legal_cases.id AS nvarchar ) ) as caseId,legal_cases.id, ( user_profiles.firstName + ' ' + user_profiles.lastName ) as assignee, priority, dueDate, workflow_status.name as status, case_types.name as type,lcler.number,lcler.refDate,lcler.comments, legal_cases.subject AS fullSubject";
        $select = $this->ci->session->userdata("AUTH_language") == "arabic" ? $select . ", CASE WHEN LEN(legal_cases.subject) > 60 THEN '...' + ' ' + ( SUBSTRING(legal_cases.subject, 1, 60)) ELSE legal_cases.subject END AS subject" : $select . ", CASE WHEN LEN(legal_cases.subject) > 60 THEN (SUBSTRING(legal_cases.subject, 1, 60)) + ' ' + '...' ELSE legal_cases.subject END AS subject";
        $query["select"] = [$select, false];
        $query["where"][] = ["(lcler.number LIKE '%" . $q2 . "%'  escape '\\')", NULL, false];
        $query["where"][] = $this->get_matter_privacy_conditions($this->logged_user_id, $this->override_privacy);
        $query["join"] = [["legal_case_litigation_details ld", "ld.legal_case_id = legal_cases.id", "inner"], ["legal_case_litigation_external_references lcler", "lcler.stage = ld.id", "inner"], ["user_profiles", "user_profiles.user_id = legal_cases.user_id", "left"], ["workflow_status", "workflow_status.id = legal_cases.case_status_id", "inner"], ["case_types", "case_types.id = legal_cases.case_type_id", "inner"]];
        return $pagingOn ? parent::paginate($query, ["urlPrefix" => ""]) : parent::load_all($query);
    }
    public function count_cases_per_assignee($year)
    {
        $query["select"] = ["Count(0) AS count, CASE WHEN legal_cases.user_id IS NOT NULL THEN MAX(( user_profiles.firstName + ' ' + user_profiles.lastName)) ELSE 'Unassigned' END AS userName,CASE WHEN legal_cases.user_id IS NOT NULL THEN MAX(user_profiles.status) ELSE '' END AS status", false];
        $query["join"] = ["user_profiles", "user_profiles.user_id = legal_cases.user_id", "left"];
        $query["where"][] = ["YEAR(caseArrivalDate)", $year];
        $query["where"][] = $this->get_matter_privacy_conditions($this->logged_user_id, $this->override_privacy);
        $query["group_by"] = ["legal_cases.user_id"];
        return $this->load_all($query);
    }
    public function k_load_all_cases_lawyers_contributors($filter, $sortable)
    {
        $query = [];
        $response = [];
        $query["select"] = ["legal_cases_contacts.id, legal_cases_contacts.comments as comments, (CASE WHEN contacts.father!='' THEN contacts.firstName + ' '+ contacts.father + ' ' + contacts.lastName ELSE contacts.firstName+' '+contacts.lastName END) as contactName, ( user_profiles.firstName + ' ' + user_profiles.lastName ) as createdBy, legal_cases_contacts.createdOn, CASE WHEN legal_case_contact_role_id IS NULL THEN 0 ELSE legal_case_contact_role_id END AS legal_case_contact_role_id, contacts.id AS contactId", false];
        if (is_array($filter) && isset($filter["filters"])) {
            foreach ($filter["filters"] as $_filter) {
                if (!empty($_filter["filters"][0]["field"]) && in_array($_filter["filters"][0]["field"], ["effectiveEffort", "estimatedEffort"])) {
                    $system_preferences = $this->ci->session->userdata("systemPreferences");
                    $this->ci->load->library("TimeMask");
                    $_filter["filters"][0]["value"] = $this->ci->timemask->humanReadableToHours($_filter["filters"][0]["value"]);
                }
                $this->prep_k_filter($_filter, $query, $filter["logic"]);
            }
            unset($_filter);
        }
        $query["join"] = [["contacts", "contacts.id = legal_cases_contacts.contact_id", "left"], ["legal_case_contact_roles", "legal_case_contact_roles.id = legal_cases_contacts.legal_case_contact_role_id", "left"], ["user_profiles", "user_profiles.user_id = legal_cases_contacts.createdBy", "left"]];
        $this->prep_query($query);
        $response["totalRows"] = $this->ci->db->count_all_results("legal_cases_contacts");
        if (is_array($sortable) && !empty($sortable)) {
            foreach ($sortable as $_sort) {
                $query["order_by"][] = [$_sort["field"], $_sort["dir"]];
            }
        } else {
            $query["order_by"] = ["legal_cases_contacts.id desc"];
        }
        if ($limit = $this->ci->input->post("take", true)) {
            $query["limit"] = [$this->ci->input->post("take", true), $this->ci->input->post("skip", true)];
        }
        $this->prep_query($query);
        $result = $this->ci->db->get("legal_cases_contacts");
        $response["data"] = $result->result_array();
        return $response;
    }
    public function load_all_cases_lawyers_contributors($id)
    {
        $query = [];
        $query["select"] = ["legal_cases_contacts.id, legal_cases_contacts.comments as comments, ( CASE WHEN contacts.father!='' THEN contacts.firstName + ' '+ contacts.father + ' ' + contacts.lastName ELSE contacts.firstName+' '+contacts.lastName END) as contactName, ( user_profiles.firstName + ' ' + user_profiles.lastName ) as createdBy, legal_cases_contacts.createdOn, legal_case_contact_roles.name as role, contacts.isLawyer", false];
        $query["join"] = [["contacts", "contacts.id = legal_cases_contacts.contact_id", "left"], ["legal_case_contact_roles", "legal_case_contact_roles.id = legal_cases_contacts.legal_case_contact_role_id", "left"], ["user_profiles", "user_profiles.user_id = legal_cases_contacts.createdBy", "left"]];
        $query["where"] = [["legal_cases_contacts.case_id", $id], ["legal_cases_contacts.contactType", "contributor"]];
        $query["order_by"] = ["legal_cases_contacts.id desc"];
        $this->prep_query($query);
        $result = $this->ci->db->get("legal_cases_contacts");
        return $result->result_array();
    }
    public function k_load_all_cases_contacts($filter, $sortable)
    {
        $query = [];
        $response = [];
        $this->ci->load->model("language");
        $language_id = $this->ci->language->get_id_by_session_lang();
        $query["select"] = ["legal_cases_contacts.id, legal_cases_contacts.comments as comments, contacts.comments as contact_comments,\r\n         ( CASE WHEN contacts.father!='' THEN contacts.firstName + ' '+ contacts.father + ' ' + contacts.lastName ELSE contacts.firstName+' '+contacts.lastName END) as contactName,\r\n          legal_cases_contacts.contactType, CASE WHEN legal_case_contact_role_id IS NULL THEN 0 ELSE legal_case_contact_role_id END AS legal_case_contact_role_id,\r\n          contacts.id AS contactId, contacts.phone, contacts.mobile,STUFF((SELECT ', ' + contact_emails.email FROM contact_emails WHERE contact_id =  legal_cases_contacts.contact_id  FOR XML PATH('')), 1, 1, '') AS email\r\n  , contacts.address1, contacts.city, contacts.country_id, \r\n          contacts.internalReference, contacts.jobTitle,cl.name as country,cpu2.id as client_portal_id", false];
        if (is_array($filter) && isset($filter["filters"])) {
            foreach ($filter["filters"] as $_filter) {
                if (!empty($_filter["filters"][0]["field"]) && in_array($_filter["filters"][0]["field"], ["effectiveEffort", "estimatedEffort"])) {
                    $system_preferences = $this->ci->session->userdata("systemPreferences");
                    $this->ci->load->library("TimeMask");
                    $_filter["filters"][0]["value"] = $this->ci->timemask->humanReadableToHours($_filter["filters"][0]["value"]);
                }
                $this->prep_k_filter($_filter, $query, $filter["logic"]);
            }
            unset($_filter);
        }
        $query["join"] = [["contacts", "contacts.id = legal_cases_contacts.contact_id", "left"], ["legal_case_contact_roles", "legal_case_contact_roles.id = legal_cases_contacts.legal_case_contact_role_id", "left"], ["countries", "contacts.country_id = countries.id", "left"], ["countries_languages cl", "countries.id = cl.country_id and cl.language_id = " . $language_id, "left"], ["customer_portal_users cpu2", "contacts.id = cpu2.contact_id", "left"]];
        $query["where"][] = ["legal_cases_contacts.contactType", "contact"];
        $query["where"][] = ["contacts.status = 'Active'", NULL, false];
        $this->prep_query($query);
        $response["totalRows"] = $this->ci->db->count_all_results("legal_cases_contacts");
        if (is_array($sortable) && !empty($sortable)) {
            foreach ($sortable as $_sort) {
                $query["order_by"][] = [$_sort["field"], $_sort["dir"]];
            }
        } else {
            $query["order_by"] = ["legal_cases_contacts.id desc"];
        }
        if ($limit = $this->ci->input->post("take", true)) {
            $query["limit"] = [$this->ci->input->post("take", true), $this->ci->input->post("skip", true)];
        }
        $this->prep_query($query);
        $result = $this->ci->db->get("legal_cases_contacts");
        $response["data"] = $result->result_array();
        return $response;
    }
    public function k_load_all_cases_outsourcing_lawyers($filter, $sortable)
    {
        $query = [];
        $response = [];
        $query["select"] = ["legal_cases_contacts.id, legal_cases_contacts.comments as comments, ( CASE WHEN contacts.father!='' THEN contacts.firstName + ' '+ contacts.father + ' ' + contacts.lastName ELSE contacts.firstName+' '+contacts.lastName END ) as contactName, CASE WHEN legal_case_contact_role_id IS NULL THEN 0 ELSE legal_case_contact_role_id END AS legal_case_contact_role_id, contacts.id AS contactId", false];
        if (is_array($filter) && isset($filter["filters"])) {
            foreach ($filter["filters"] as $_filter) {
                if (!empty($_filter["filters"][0]["field"]) && in_array($_filter["filters"][0]["field"], ["effectiveEffort", "estimatedEffort"])) {
                    $system_preferences = $this->ci->session->userdata("systemPreferences");
                    $this->ci->load->library("TimeMask");
                    $_filter["filters"][0]["value"] = $this->ci->timemask->humanReadableToHours($_filter["filters"][0]["value"]);
                }
                $this->prep_k_filter($_filter, $query, $filter["logic"]);
            }
            unset($_filter);
        }
        $query["join"] = [["contacts", "contacts.id = legal_cases_contacts.contact_id", "left"], ["legal_case_contact_roles", "legal_case_contact_roles.id = legal_cases_contacts.legal_case_contact_role_id", "left"]];
        $this->prep_query($query);
        $response["totalRows"] = $this->ci->db->count_all_results("legal_cases_contacts");
        if (is_array($sortable) && !empty($sortable)) {
            foreach ($sortable as $_sort) {
                $query["order_by"][] = [$_sort["field"], $_sort["dir"]];
            }
        } else {
            $query["order_by"] = ["legal_cases_contacts.id desc"];
        }
        if ($limit = $this->ci->input->post("take", true)) {
            $query["limit"] = [$this->ci->input->post("take", true), $this->ci->input->post("skip", true)];
        }
        $this->prep_query($query);
        $result = $this->ci->db->get("legal_cases_contacts");
        $response["data"] = $result->result_array();
        return $response;
    }
    public function load_contacts_list($case_id)
    {
        $contacts = ["contact" => [], "external lawyer" => [], "judge" => [], "opponentLawyer" => []];
        if ($case_id < 1) {
            return $contacts;
        }
        $case_contacts = $this->ci->db->select(["contacts.id as id, (CASE WHEN contacts.father!='' THEN contacts.firstName + ' '+ contacts.father + ' ' + contacts.lastName ELSE contacts.firstName+' '+contacts.lastName END ) AS name, contactType", false])->join("contacts", "contacts.id = legal_cases_contacts.contact_id", "inner")->where("legal_cases_contacts.case_id", $case_id)->order_by("contacts.firstName", "asc")->order_by("contacts.lastName", "asc")->get("legal_cases_contacts");
        if (!$case_contacts->num_rows()) {
            return $contacts;
        }
        foreach ($case_contacts->result() as $contact) {
            $contacts[$contact->contactType][(string) $contact->id] = $contact->name;
        }
        return $contacts;
    }
    public function load_case_logs($id, $visible_cases_ids)
    {
        $sql = "\r\n          select legal_case_changes.*, ( UP.firstName + ' ' + UP.lastName ) as modifiedBy\r\n          from user_profiles as UP left join legal_case_changes on UP.user_id = legal_case_changes.user_id\r\n          where legal_case_changes.legal_case_id = " . $id . " and legal_case_changes.legal_case_id in (" . implode(",", $visible_cases_ids) . ") and (legal_case_changes.modifiedByChannel != '" . $this->portalChannel . "' or legal_case_changes.modifiedByChannel is null)\r\n          union\r\n          select legal_case_changes.*, ( CPU.firstName + ' ' + CPU.lastName + ' (Portal User)' ) as modifiedBy\r\n          from customer_portal_users as CPU left join legal_case_changes on CPU.id = legal_case_changes.user_id\r\n          where legal_case_changes.legal_case_id = " . $id . " and legal_case_changes.legal_case_id in (" . implode(",", $visible_cases_ids) . ") and legal_case_changes.modifiedByChannel = '" . $this->portalChannel . "'\r\n          union\r\n          select legal_case_changes.*, '---' as modifiedBy\r\n          from legal_case_changes\r\n          where legal_case_id = " . $id . " and legal_case_id in (" . implode(",", $visible_cases_ids) . ") and user_id is null";
        $query_execution = $this->ci->db->query($sql);
        $result = $query_execution->result_array();
        function cmp($a, $b)
        {
            return strcmp($b["changedOn"], $a["changedOn"]);
        }
        usort($result, "cmp");
        return $result;
    }
    public function load_all_case_data($id)
    {
        $this->ci->load->model("language");
        $langId = $this->ci->language->get_id_by_session_lang();
        $query = [];
        $query["select"] = ["legal_cases.*,user_profiles.status as userStatus,case_types.name as caseTypeName,workflow_status.name as caseStatusName, provider_groups.name as  providerGroupsName, ( user_profiles.firstName + ' ' + user_profiles.lastName ) as assignedToName, legal_case_stage_languages.name as caseStage, legal_case_client_position_languages.name AS clientPosition, legal_case_success_probability_languages.name AS successProbability,( referredByContact.firstName + ' ' + referredByContact.lastName ) as referredByName, (requestedByContact.firstName + ' ' + requestedByContact.lastName ) as requestedByName", false];
        $query["join"] = [["contacts referredByContact", "referredByContact.id = legal_cases.referredBy", "left"], ["contacts requestedByContact", "requestedByContact.id = legal_cases.requestedBy", "left"], ["case_types", "case_types.id=legal_cases.case_type_id", "inner"], ["workflow_status", "workflow_status.id=legal_cases.case_status_id", "inner"], ["provider_groups", "provider_groups.id=legal_cases.provider_group_id", "inner"], ["user_profiles", "user_profiles.user_id=legal_cases.user_id", "left"], ["legal_case_stage_languages", "legal_case_stage_languages.legal_case_stage_id = legal_cases.legal_case_stage_id and legal_case_stage_languages.language_id = '" . $langId . "'", "left"], ["legal_case_client_position_languages", "legal_case_client_position_languages.legal_case_client_position_id = legal_cases.legal_case_client_position_id and legal_case_client_position_languages.language_id = '" . $langId . "'", "left"], ["legal_case_success_probability_languages", "legal_case_success_probability_languages.legal_case_success_probability_id = legal_cases.legal_case_success_probability_id and legal_case_success_probability_languages.language_id = '" . $langId . "'", "left"]];
        $query["where"][] = ["legal_cases.id", $id];
        $query["where"][] = ["legal_cases.isDeleted", "0"];
        return $this->load_all($query);
    }
    public function load_cases_by_client_id($client_id)
    {
        $query = [];
        $query["select"] = ["legal_cases.id, ('" . $this->modelCode . "' + CAST( legal_cases.id AS nvarchar )) as case_id, ( '" . $this->modelCode . "' + CAST( legal_cases.id AS nvarchar ) + ' - ' + SUBSTRING( legal_cases.subject, 0, 45) ) as caseId, legal_cases.subject, legal_cases.category as case_category, users.username as assignee, case_types.name as practice_area, firstName, lastName", false];
        $query["join"] = [["users", "users.id = legal_cases.user_id", "left"], ["user_profiles", "user_profiles.user_id = users.id", "left"], ["case_types", "case_types.id = legal_cases.case_type_id", "left"]];
        $query["where"][] = ["legal_cases.client_id", $client_id];
        $query["where"][] = $this->get_matter_privacy_conditions($this->logged_user_id, $this->override_privacy);
        $query["order_by"] = ["legal_cases.id desc"];
        return $this->load_all($query);
    }
    public function case_values_per_client_name($pagingOn = false, $filter = [])
    {
        $_table = $this->_table;
        $this->_table = "legal_cases";
        $query = [];
        $response = [];
        $query["select"] = ["sum(legal_cases.caseValue) as caseValues,clients_view.name as clientName,legal_cases.client_id", false];
        $query["join"] = [["clients_view", "clients_view.id = legal_cases.client_id AND clients_view.model = 'clients'", "INNER"]];
        if (is_array($filter) && isset($filter["filters"])) {
            foreach ($filter["filters"] as $_filter) {
                $this->prep_k_filter($_filter, $query, $filter["logic"]);
            }
            unset($_filter);
        }
        $query["where"][] = ["legal_cases.client_id is not null"];
        $query["group_by"] = ["clients_view.name,legal_cases.client_id"];
        $query["where"][] = $this->get_matter_privacy_conditions($this->logged_user_id, $this->override_privacy);
        $query["order_by"] = ["sum(legal_cases.caseValue) desc"];
        if ($limit = $this->ci->input->post("take", true)) {
            $query["limit"] = [$this->ci->input->post("take", true), $this->ci->input->post("skip", true)];
            $paginationConf = ["urlPrefix" => ""];
            $paginationConf["inPage"] = $this->ci->input->post("take", true);
        }
        $totalRowsQuery = ["select" => ["count(*) as numRows"], "where" => $this->get_matter_privacy_conditions($this->logged_user_id, $this->override_privacy)];
        $totalRowsQueryResults = $this->load($totalRowsQuery);
        $response["totalRows"] = $totalRowsQueryResults["numRows"];
        $response["result"] = $pagingOn ? parent::paginate($query, $paginationConf) : parent::load_all($query);
        return $response;
    }
    public function case_values_per_client_name_micro($pagingOn = false, $filter = [])
    {
        $_table = $this->_table;
        $this->_table = "legal_cases";
        $query = [];
        $response = [];
        $this->ci->load->model("language");
        $langId = $this->ci->language->get_id_by_session_lang();
        $query["select"] = ["clients_view.name as clientName,legal_cases.client_id, legal_cases.id,case_types.name AS type,legal_cases.subject,legal_cases.internalReference," . "opponentNames = STUFF(\r\n\t\t\t(SELECT ', ' +\r\n\t\t\t(\r\n                            CASE WHEN opponent_positions.name IS NOT NULL\r\n                                THEN\r\n                                    CASE WHEN legal_case_opponents.opponent_member_type = 'company'\r\n                                        THEN\r\n                                            (opponentCompany.name + ' - ' + opponent_positions.name)\r\n                                        ELSE\r\n                                            opponentContact.firstName + ' ' + opponentContact.lastName + ' - ' + opponent_positions.name\r\n                                    END\r\n                                ELSE\r\n                                    CASE WHEN legal_case_opponents.opponent_member_type = 'company'\r\n                                        THEN\r\n                                            opponentCompany.name\r\n                                        ELSE\r\n                                            opponentContact.firstName + ' ' + opponentContact.lastName\r\n                                    END\r\n                            END\r\n\t\t\t)\r\n\t\t\t FROM legal_case_opponents\r\n\t\t\t INNER JOIN opponents ON opponents.id = legal_case_opponents.opponent_id\r\n\t\t\t LEFT JOIN companies AS opponentCompany ON opponentCompany.id = opponents.company_id AND legal_case_opponents.opponent_member_type = 'company'\r\n\t\t\t LEFT JOIN contacts AS opponentContact ON opponentContact.id = opponents.contact_id AND legal_case_opponents.opponent_member_type = 'contact'\r\n                         LEFT JOIN legal_case_opponent_position_languages AS opponent_positions ON opponent_positions.legal_case_opponent_position_id = legal_case_opponents.opponent_position and opponent_positions.language_id = '" . $langId . "'\r\n\t\t\t WHERE legal_case_opponents.case_id = legal_cases.id\r\n\t\t\tFOR XML PATH('')), 1, 1, ''), " . "legal_cases.arrivalDate,legal_cases.caseArrivalDate,legal_cases.closedOn,workflow_status.name AS status,legal_cases.statusComments,legal_case_stage_languages.name,legal_cases.caseValue,(select * from TotalCaseValuesByClientId(legal_cases.client_id))as totalCaseValues", false];
        $query["join"] = [["workflow_status", "workflow_status.id = legal_cases.case_status_id", "left"], ["case_types", "case_types.id = legal_cases.case_type_id", "left"], ["clients_view", "clients_view.id = legal_cases.client_id AND clients_view.model = 'clients'", "inner"], ["legal_case_stage_languages", "legal_case_stage_languages.legal_case_stage_id=legal_cases.legal_case_stage_id  and legal_case_stage_languages.language_id = '" . $langId . "'", "left"]];
        if (is_array($filter) && isset($filter["filters"])) {
            foreach ($filter["filters"] as $_filter) {
                $this->prep_k_filter($_filter, $query, $filter["logic"]);
            }
            unset($_filter);
        }
        $query["where"][] = ["legal_cases.client_id >", 0];
        $query["where"][] = $this->get_matter_privacy_conditions($this->logged_user_id, $this->override_privacy);
        $query["order_by"] = ["(select * from TotalCaseValuesByClientId(legal_cases.client_id)) desc"];
        if ($limit = $this->ci->input->post("take", true)) {
            $query["limit"] = [$this->ci->input->post("take", true), $this->ci->input->post("skip", true)];
            $paginationConf = ["urlPrefix" => ""];
            $paginationConf["inPage"] = $this->ci->input->post("take", true);
        }
        $totalRowsQuery = ["select" => ["count(*) as numRows"]];
        $totalRowsQuery["where"][] = $this->get_matter_privacy_conditions($this->logged_user_id, $this->override_privacy);
        $totalRowsQueryResults = $this->load($totalRowsQuery);
        $response["totalRows"] = $totalRowsQueryResults["numRows"];
        $response["result"] = $pagingOn ? parent::paginate($query, $paginationConf) : parent::load_all($query);
        return $response;
    }
    public function jasper_load_all_cases($columns, $customs)
    {
        $parameters = [];
        $this->ci->load->model("language");
        $langId = $this->ci->language->get_id_by_session_lang();
        for ($i = 0; $i < count($columns); $i++) {
            if ($columns[$i] == "case_id") {
                $parameters["Field"][] = "( 'M' + CAST(legal_cases.id AS nvarchar))";
            } else {
                if ($columns[$i] == "MAX(legal_cases.provider_group_id)") {
                    $parameters["Field"][] = "provider_groups.name";
                } else {
                    if ($columns[$i] == "assignee") {
                        $parameters["Field"][] = "(MAX(up.firstName) + ' ' + MAX(up.lastName))";
                    } else {
                        if ($columns[$i] == "internalReference") {
                            $parameters["Field"][] = "MAX(legal_cases.internalReference)";
                        } else {
                            if ($columns[$i] == "description") {
                                $parameters["Field"][] = "MAX(cast(legal_cases.description as varchar(max)))";
                            } else {
                                if ($columns[$i] == "latest_development") {
                                    $parameters["Field"][] = "MAX(cast(legal_cases.latest_development as varchar(max)))";
                                } else {
                                    if ($columns[$i] == "description") {
                                        $parameters["Field"][] = "legal_cases.description";
                                    } else {
                                        if ($columns[$i] == "court") {
                                            $parameters["Field"][] = "Max(courts.name)";
                                        } else {
                                            if ($columns[$i] == "outsource_to") {
                                                $parameters["Field"][] = "\r\n                    STUFF(\r\n                        COALESCE(\r\n                            ' ,' + \r\n                            NULLIF(\r\n                                SUBSTRING\r\n                                (\r\n                                    STUFF(\r\n                                        (\r\n                                            SELECT \r\n                                                ', ' + conExtLaw.firstName + ' ' + conExtLaw.lastName\r\n                                            FROM legal_cases_contacts lccExtLaw \r\n                                            LEFT JOIN contacts conExtLaw ON \r\n                                                conExtLaw.id = lccExtLaw.contact_id \r\n                                            WHERE \r\n                                                lccExtLaw.case_id = legal_cases.id AND \r\n                                                lccExtLaw.contactType = 'external lawyer' FOR XML PATH(''), type\r\n                                        ).value(N'.[1]', N'nvarchar(max)'),\r\n                                        1, \r\n                                        2, \r\n                                        ''\r\n                                    ),\r\n                                    0, \r\n                                    2000\r\n                                ),\r\n                                ''\r\n                            ),\r\n                            ''\r\n                        ) + \r\n                        COALESCE(\r\n                            ', ' + \r\n                            NULLIF(\r\n                                SUBSTRING\r\n                                (\r\n                                    STUFF(\r\n                                        (\r\n                                            SELECT \r\n                                                ', ' + comExtLaw.name\r\n                                            FROM legal_cases_companies lccomExtLaw \r\n                                            LEFT JOIN companies comExtLaw ON \r\n                                                comExtLaw.id = lccomExtLaw.company_id \r\n                                            WHERE \r\n                                                lccomExtLaw.case_id = legal_cases.id AND \r\n                                                lccomExtLaw.companyType = 'external lawyer' FOR XML PATH(''), type\r\n                                        ).value(N'.[1]', N'nvarchar(max)'),\r\n                                        1,\r\n                                        2,\r\n                                        ''\r\n                                    ),\r\n                                    0, \r\n                                    2000\r\n                                ),\r\n                                ''\r\n                            ),\r\n                            ''\r\n                        ),\r\n                        1,\r\n                        1,\r\n                        ''\r\n                    )";
                                            } else {
                                                if ($columns[$i] == "litigationExternalRef") {
                                                    $parameters["Field"][] = "SUBSTRING((select DISTINCT ( MAX(legal_case_litigation_external_references.number ) + ', ') FOR XML PATH ('')), 0, 2000)";
                                                } else {
                                                    if ($columns[$i] == "statusComments") {
                                                        $parameters["Field"][] = "MAX(legal_cases.statusComments)";
                                                    } else {
                                                        if ($columns[$i] == "case_type") {
                                                            $parameters["Field"][] = "MAX(case_types.name)";
                                                        } else {
                                                            if ($columns[$i] == "priority") {
                                                                $parameters["Field"][] = "MAX(legal_cases.priority)";
                                                            } else {
                                                                if ($columns[$i] == "case_status") {
                                                                    $parameters["Field"][] = "MAX(workflow_status.name)";
                                                                } else {
                                                                    if ($columns[$i] == "case_stage") {
                                                                        $parameters["Field"][] = "MAX(legal_case_stage_languages.name)";
                                                                    } else {
                                                                        if ($columns[$i] == "caseArrivalDate") {
                                                                            $parameters["Field"][] = "MAX(legal_cases.caseArrivalDate)";
                                                                        } else {
                                                                            if ($columns[$i] == "arrivalDate") {
                                                                                $parameters["Field"][] = "MAX(legal_cases.arrivalDate)";
                                                                            } else {
                                                                                if ($columns[$i] == "dueDate") {
                                                                                    $parameters["Field"][] = "MAX(legal_cases.dueDate)";
                                                                                } else {
                                                                                    if ($columns[$i] == "closedOn") {
                                                                                        $parameters["Field"][] = "MAX(legal_cases.closedOn)";
                                                                                    } else {
                                                                                        if ($columns[$i] == "client_position") {
                                                                                            $parameters["Field"][] = "MAX(legal_case_client_position_languages.name)";
                                                                                        } else {
                                                                                            if ($columns[$i] == "success_probability") {
                                                                                                $parameters["Field"][] = "MAX(legal_case_success_probability_languages.name)";
                                                                                            } else {
                                                                                                if ($columns[$i] == "estimatedEffort") {
                                                                                                    $parameters["Field"][] = "MAX(legal_cases.estimatedEffort)";
                                                                                                } else {
                                                                                                    if ($columns[$i] == "effectiveEffort") {
                                                                                                        $parameters["Field"][] = "MAX(lcee.effectiveEffort)";
                                                                                                    } else {
                                                                                                        if ($columns[$i] == "caseValue") {
                                                                                                            $parameters["Field"][] = "MAX(legal_cases.caseValue)";
                                                                                                        } else {
                                                                                                            if ($columns[$i] == "judgmentValue") {
                                                                                                                $parameters["Field"][] = "MAX(legal_cases.judgmentValue)";
                                                                                                            } else {
                                                                                                                if ($columns[$i] == "recoveredValue") {
                                                                                                                    $parameters["Field"][] = "MAX(legal_cases.recoveredValue)";
                                                                                                                } else {
                                                                                                                    if ($columns[$i] == "archived") {
                                                                                                                        $parameters["Field"][] = "MAX(legal_cases.archived)";
                                                                                                                    } else {
                                                                                                                        if ($columns[$i] == "providerGroup") {
                                                                                                                            $parameters["Field"][] = "MAX(provider_groups.name)";
                                                                                                                        } else {
                                                                                                                            if ($columns[$i] == "court_type") {
                                                                                                                                $parameters["Field"][] = "MAX(court_types.name)";
                                                                                                                            } else {
                                                                                                                                if ($columns[$i] == "court_region") {
                                                                                                                                    $parameters["Field"][] = "MAX(court_regions.name)";
                                                                                                                                } else {
                                                                                                                                    if ($columns[$i] == "court_degree") {
                                                                                                                                        $parameters["Field"][] = "MAX(court_degrees.name)";
                                                                                                                                    } else {
                                                                                                                                        if ($columns[$i] == "first_stage") {
                                                                                                                                            $parameters["Field"][] = "(Select top 1 legal_case_stage_languages.name\r\n                                                FROM legal_case_litigation_details lcld\r\n                                                         LEFT JOIN legal_case_stage_languages\r\n                                                                   ON legal_case_stage_languages.legal_case_stage_id = lcld.legal_case_stage and\r\n                                                                      legal_case_stage_languages.language_id = " . $this->get_lang_id() . "\r\n                                                where lcld.legal_case_id = legal_cases.id\r\n                                                order by lcld.createdOn asc)";
                                                                                                                                        } else {
                                                                                                                                            if ($columns[$i] == "first_stage_judgment") {
                                                                                                                                                $parameters["Field"][] = "(SELECT legal_case_hearings.judgment\r\n                                                    FROM legal_case_hearings\r\n                                                    WHERE legal_case_hearings.stage = (Select top 1 lcld.id\r\n                                                                                       FROM legal_case_litigation_details lcld\r\n                                                                                       where lcld.legal_case_id = legal_cases.id\r\n                                                                                       order by lcld.createdOn asc)\r\n                                                          and startTime = (Select top 1 MAX(legal_case_hearings.startTime)\r\n                                                                            FROM legal_case_hearings\r\n                                                                           WHERE legal_case_hearings.legal_case_id = legal_cases.id\r\n                                                                             AND is_deleted = 0\r\n                                                                             and legal_case_hearings.judged = 'yes'\r\n                                                                             AND startDate = (SELECT MAX(startDate)\r\n                                                                                              FROM legal_case_hearings\r\n                                                                                              where legal_case_hearings.legal_case_id = legal_cases.id\r\n                                                                                                AND is_deleted = 0\r\n                                                                                                AND legal_case_hearings.stage = (Select top 1 lcld.id\r\n                                                                                                                                 FROM legal_case_litigation_details lcld\r\n                                                                                                                                 where lcld.legal_case_id = legal_cases.id\r\n                                                                                                                                 order by lcld.createdOn asc) and legal_case_hearings.judged = 'yes')\r\n                                                                                             )\r\n                                                                            AND legal_case_hearings.stage = (Select top 1 lcld.id\r\n                                                                                                                FROM legal_case_litigation_details lcld\r\n                                                                                                              where lcld.legal_case_id = legal_cases.id\r\n                                                                                                               order by lcld.createdOn asc)\r\n                                                                              and legal_case_hearings.legal_case_id = legal_cases.id\r\n                                                                              and legal_case_hearings.judged = 'yes')";
                                                                                                                                            } else {
                                                                                                                                                if ($columns[$i] == "first_judgment_date") {
                                                                                                                                                    $parameters["Field"][] = "(Select top 1 lcld_2.sentenceDate\r\n                                                FROM legal_case_litigation_details lcld_2\r\n                                                where lcld_2.legal_case_id = legal_cases.id\r\n                                                order by lcld_2.createdOn asc)";
                                                                                                                                                } else {
                                                                                                                                                    if ($columns[$i] == "category") {
                                                                                                                                                        $parameters["Field"][] = "MAX(legal_cases.category)";
                                                                                                                                                    } else {
                                                                                                                                                        if ($columns[$i] == "clientName") {
                                                                                                                                                            $parameters["Field"][] = "CASE WHEN MAX(clients.company_id) IS NULL THEN (CASE\r\n                                            when (MAX(cont.father) <> ' ') then (' ' + MAX(cont.firstName) + ' ' + MAX(cont.father) + ' ' +\r\n                                                                                 MAX(cont.lastName))\r\n                                            else (' ' + MAX(cont.firstName) + ' ' + MAX(cont.lastName)) END) \r\n                        ELSE MAX(comp.name) END";
                                                                                                                                                        } else {
                                                                                                                                                            if ($columns[$i] == "client_foreign_name") {
                                                                                                                                                                $parameters["Field"][] = "CASE WHEN MAX(clients.company_id) IS NULL THEN MAX(cont.foreignFirstName) + ' ' + MAX(cont.foreignLastName) ELSE MAX(comp.foreignName) END";
                                                                                                                                                            } else {
                                                                                                                                                                if ($columns[$i] == "requestedByName") {
                                                                                                                                                                    $parameters["Field"][] = "CASE when (MAX(requestedbycontact.father) <> ' ') then (' ' + MAX(requestedbycontact.firstName) + ' ' +\r\n                                                               MAX(requestedbycontact.father) + ' ' +\r\n                                                               MAX(requestedbycontact.lastName))\r\n                                            else (' ' + MAX(requestedbycontact.firstName) + ' ' + MAX(requestedbycontact.lastName)) END";
                                                                                                                                                                } else {
                                                                                                                                                                    if ($columns[$i] == "subject") {
                                                                                                                                                                        $parameters["Field"][] = "MAX(cast(legal_cases.subject as varchar(255)))";
                                                                                                                                                                    } else {
                                                                                                                                                                        if ($columns[$i] == "last_hearing") {
                                                                                                                                                                            $parameters["Field"][] = "(SELECT DISTINCT ((CAST(legal_case_hearings.startDate AS varchar) + ' ' +  CAST(legal_case_hearings.startTime AS varchar)))\r\n                                       FROM legal_case_hearings\r\n                                       WHERE startTime = (SELECT MAX(CAST(legal_case_hearings.startTime AS nvarchar))\r\n                                                          FROM legal_case_hearings\r\n                                                          WHERE legal_case_hearings.legal_case_id=legal_cases.id AND is_deleted = 0 \r\n                                                          AND startDate = (SELECT MAX(startDate) FROM legal_case_hearings where legal_case_hearings.legal_case_id=legal_cases.id AND is_deleted = 0)) and legal_case_hearings.legal_case_id=legal_cases.id\r\n                                                           and startDate = (SELECT MAX(startDate) FROM legal_case_hearings where legal_case_hearings.legal_case_id=legal_cases.id AND is_deleted = 0)\r\n                                       )";
                                                                                                                                                                        } else {
                                                                                                                                                                            if ($columns[$i] == "reasons_of_postponement_of_last_hearing") {
                                                                                                                                                                                $parameters["Field"][] = "(SELECT top 1 reasons_of_postponement\r\n                                       FROM legal_case_hearings\r\n                                       WHERE startTime = (SELECT MAX(CAST(legal_case_hearings.startTime AS nvarchar))\r\n                                                          FROM legal_case_hearings\r\n                                                          WHERE legal_case_hearings.legal_case_id=legal_cases.id AND is_deleted = 0 \r\n                                                          AND startDate = (SELECT MAX(startDate) FROM legal_case_hearings where legal_case_hearings.legal_case_id=legal_cases.id AND is_deleted = 0)) and legal_case_hearings.legal_case_id=legal_cases.id\r\n                                                           and startDate = (SELECT MAX(startDate) FROM legal_case_hearings where legal_case_hearings.legal_case_id=legal_cases.id AND is_deleted = 0)\r\n                                       )";
                                                                                                                                                                            } else {
                                                                                                                                                                                if ($columns[$i] == "judgment") {
                                                                                                                                                                                    $parameters["Field"][] = "(SELECT top 1 (CAST(legal_case_hearings.judgment AS varchar(max)))\r\n                                        FROM legal_case_hearings\r\n                                        WHERE startTime = (SELECT MAX(CAST(legal_case_hearings.startTime AS nvarchar))\r\n                                                            FROM legal_case_hearings\r\n                                                            WHERE legal_case_hearings.legal_case_id=legal_cases.id AND is_deleted = 0 and legal_case_hearings.judged = 'yes'\r\n                                                            AND startDate = (SELECT MAX(startDate) FROM legal_case_hearings where legal_case_hearings.legal_case_id=legal_cases.id AND is_deleted = 0 and legal_case_hearings.judged = 'yes')) and legal_case_hearings.legal_case_id=legal_cases.id and legal_case_hearings.judged = 'yes' \r\n                                                            and startDate = (SELECT MAX(startDate) FROM legal_case_hearings where legal_case_hearings.legal_case_id=legal_cases.id AND is_deleted = 0 and legal_case_hearings.judged = 'yes')\r\n                                      )";
                                                                                                                                                                                } else {
                                                                                                                                                                                    if ($columns[$i] == "sentenceDate") {
                                                                                                                                                                                        $parameters["Field"][] = "MAX(legal_case_litigation_details.sentenceDate)";
                                                                                                                                                                                    } else {
                                                                                                                                                                                        if ($columns[$i] == "opponentNames") {
                                                                                                                                                                                            $parameters["Field"][] = "STUFF((SELECT ', ' +\r\n\t\t\t\t(CASE WHEN legal_case_opponents.opponent_member_type = 'company'\r\n\t\t\t\tTHEN opponentCompany.name\r\n\t\t\t\tELSE opponentContact.firstName + ' ' + opponentContact.lastName END )\r\n\t\t\t\t FROM legal_case_opponents\r\n\t\t\t\t INNER JOIN opponents ON opponents.id = legal_case_opponents.opponent_id\r\n\t\t\t\t LEFT JOIN companies AS opponentCompany ON opponentCompany.id = opponents.company_id AND legal_case_opponents.opponent_member_type = 'company'\r\n\t\t\t\t LEFT JOIN contacts AS opponentContact ON opponentContact.id = opponents.contact_id AND legal_case_opponents.opponent_member_type = 'contact'\r\n\t\t\t\t WHERE legal_case_opponents.case_id = legal_cases.id\r\n\t\t\t\tFOR XML PATH('')), 1, 1, '') ";
                                                                                                                                                                                        } else {
                                                                                                                                                                                            if ($columns[$i] == "opponent_foreign_name") {
                                                                                                                                                                                                $parameters["Field"][] = "STUFF((SELECT ', ' +\r\n\t\t\t\t(\r\n                                    CASE WHEN opponent_positions.name != '' THEN\r\n                                    (\r\n                                        CASE WHEN legal_case_opponents.opponent_member_type = 'company'\r\n                                        THEN (CASE WHEN opponentCompany.foreignName IS NULL THEN opponentCompany.name ELSE opponentCompany.foreignName END) + ' - ' + opponent_positions.name\r\n                                        ELSE (CASE WHEN opponentContact.foreignFirstName IS NULL THEN opponentContact.firstName ELSE opponentContact.foreignFirstName END) + ' ' + (CASE WHEN opponentContact.foreignLastName IS NULL THEN opponentContact.lastName ELSE opponentContact.foreignLastName END) + ' - ' + opponent_positions.name END\r\n                                    )\r\n                                    ELSE\r\n                                    (\r\n                                        CASE WHEN legal_case_opponents.opponent_member_type = 'company'\r\n                                        THEN (CASE WHEN opponentCompany.foreignName IS NULL THEN opponentCompany.name ELSE opponentCompany.foreignName END)\r\n                                        ELSE (CASE WHEN opponentContact.foreignFirstName IS NULL THEN opponentContact.firstName ELSE opponentContact.foreignFirstName END) + ' ' + (CASE WHEN opponentContact.foreignLastName IS NULL THEN opponentContact.lastName ELSE opponentContact.foreignLastName END) END\r\n                                    )\r\n                                    END\r\n                                )\r\n\t\t\t\t FROM legal_case_opponents\r\n\t\t\t\t INNER JOIN opponents ON opponents.id = legal_case_opponents.opponent_id\r\n\t\t\t\t LEFT JOIN companies AS opponentCompany ON opponentCompany.id = opponents.company_id AND legal_case_opponents.opponent_member_type = 'company'\r\n\t\t\t\t LEFT JOIN contacts AS opponentContact ON opponentContact.id = opponents.contact_id AND legal_case_opponents.opponent_member_type = 'contact'\r\n                                 LEFT JOIN legal_case_opponent_position_languages AS opponent_positions ON opponent_positions.legal_case_opponent_position_id = legal_case_opponents.opponent_position and opponent_positions.language_id = '" . $langId . "'\r\n\t\t\t\t WHERE legal_case_opponents.case_id = legal_cases.id\r\n\t\t\t\tFOR XML PATH('')), 1, 1, '') ";
                                                                                                                                                                                            } else {
                                                                                                                                                                                                if ($columns[$i] == "opponentNationalities") {
                                                                                                                                                                                                    $parameters["Field"][] = "STUFF((SELECT ', ' + (CASE\r\n                                                                     WHEN legal_case_opponents.opponent_member_type = 'company'\r\n                                                                         THEN (CASE\r\n                                                                                   WHEN opponentcompanynationalities.name IS NOT NULL\r\n                                                                                       THEN (CASE WHEN opponent_positions.name != ''\r\n                                                                                           THEN\r\n                                                                                                (opponentCompany.name + ' - ' + opponent_positions.name + ' (' +\r\n                                                                                                 opponentcompanynationalities.name +\r\n                                                                                                 ')')\r\n                                                                                           ELSE\r\n                                                                                               (opponentCompany.name + ' (' +\r\n                                                                                                opponentcompanynationalities.name +\r\n                                                                                                ')')\r\n                                                                                           END)\r\n                                                                                   ELSE (CASE WHEN opponent_positions.name != '' THEN (opponentCompany.name) + ' - ' + opponent_positions.name ELSE  (opponentCompany.name) END) END)\r\n                                                                     ELSE (CASE\r\n                                                                               WHEN opponentContactNationalitiesCountry.name IS NOT NULL\r\n                                                                                   THEN\r\n                                                                                   (CASE WHEN (opponentContact.father <> ' ')\r\n                                                                                       THEN (CASE WHEN opponent_positions.name != ''\r\n                                                                                           THEN\r\n                                                                                              (opponentContact.firstName + ' ' + opponentContact.father + ' ' +\r\n                                                                                               opponentContact.lastName + ' -  ' + opponent_positions.name + ' (' +\r\n                                                                                               opponentContactNationalitiesCountry.name + ')')\r\n                                                                                           ELSE\r\n                                                                                               (opponentContact.firstName + ' ' + opponentContact.father + ' ' +\r\n                                                                                                opponentContact.lastName + ' (' +\r\n                                                                                                opponentContactNationalitiesCountry.name + ')')\r\n                                                                                           END)\r\n                                                                                       ELSE\r\n                                                                                           (CASE WHEN opponent_positions.name != '' THEN\r\n                                                                                                 (opponentContact.firstName + ' ' +\r\n                                                                                                  opponentContact.lastName + ' - ' + opponent_positions.name + ' (' +\r\n                                                                                                  opponentContactNationalitiesCountry.name + ')')\r\n                                                                                               ELSE\r\n                                                                                                   (opponentContact.firstName + ' ' +\r\n                                                                                                    opponentContact.lastName + '(' +\r\n                                                                                                    opponentContactNationalitiesCountry.name + ')')\r\n                                                                                           END)\r\n                                                                                       END)\r\n                                                                               ELSE\r\n                                                                                   (CASE WHEN (opponentContact.father <> ' ')\r\n                                                                                       THEN\r\n                                                                                            (CASE WHEN opponent_positions.name != ''\r\n                                                                                                THEN\r\n                                                                                                  (opponentContact.firstName + ' ' + opponentContact.father + ' ' + opponentContact.lastName + ' - ' + opponent_positions.name)\r\n                                                                                                ELSE\r\n                                                                                                    (opponentContact.firstName + ' ' + opponentContact.father + ' ' + opponentContact.lastName)\r\n                                                                                                END)\r\n                                                                                       ELSE\r\n                                                                                           (CASE WHEN opponent_positions.name != ''\r\n                                                                                               THEN\r\n                                                                                                 (opponentContact.firstName + ' ' + opponentContact.lastName + ' - ' + opponent_positions.name)\r\n                                                                                               ELSE\r\n                                                                                                   (opponentContact.firstName + ' ' + opponentContact.lastName)\r\n                                                                                               END)\r\n                                                                                       END)\r\n                                                                         END) END)\r\n                                          FROM legal_case_opponents\r\n                                                   INNER JOIN opponents ON opponents.id = legal_case_opponents.opponent_id\r\n                                                   LEFT JOIN companies AS opponentCompany\r\n                                                             ON opponentCompany.id = opponents.company_id AND\r\n                                                                legal_case_opponents.opponent_member_type = 'company'\r\n                                                   LEFT JOIN contacts AS opponentContact\r\n                                                             ON opponentContact.id = opponents.contact_id AND\r\n                                                                legal_case_opponents.opponent_member_type = 'contact'\r\n                                                   LEFT JOIN countries_languages AS opponentCompanyNationalities\r\n                                                             ON opponentCompanyNationalities.country_id = opponentCompany.nationality_id AND opponentcompanynationalities.language_id = " . $this->get_lang_id() . " AND \r\n                                                                legal_case_opponents.opponent_member_type = 'company'\r\n                                                   LEFT JOIN contact_nationalities_details AS opponentContactNationalities\r\n                                                             ON opponentContactNationalities.contact_id = opponentContact.id AND\r\n                                                                legal_case_opponents.opponent_member_type = 'contact'\r\n                                                    LEFT JOIN legal_case_opponent_position_languages AS opponent_positions ON opponent_positions.legal_case_opponent_position_id = legal_case_opponents.opponent_position and opponent_positions.language_id = " . $this->get_lang_id() . "\r\n                                                    LEFT JOIN countries_languages AS opponentContactNationalitiesCountry ON opponentContactNationalitiesCountry.country_id = opponentContactNationalities.nationality_id AND opponentContactNationalitiesCountry.language_id = " . $this->get_lang_id() . "\r\n                                          WHERE legal_case_opponents.case_id = legal_cases.id FOR XML PATH ('')), 1, 1, '')";
                                                                                                                                                                                                } else {
                                                                                                                                                                                                    if (is_numeric($columns[$i])) {
                                                                                                                                                                                                        $field_data = $this->ci->custom_field->load(["select" => ["type, type_data"], "where" => [["id", $columns[$i]]]]);
                                                                                                                                                                                                        switch ($field_data["type"]) {
                                                                                                                                                                                                            case "date":
                                                                                                                                                                                                                $parameters["Field"][] = "(SELECT cfv.date_value FROM custom_field_values AS cfv WHERE legal_cases.id = cfv.recordId AND cfv.custom_field_id = " . $columns[$i] . ")";
                                                                                                                                                                                                                break;
                                                                                                                                                                                                            case "date_time":
                                                                                                                                                                                                                $parameters["Field"][] = "(SELECT FORMAT(cfv.date_value, N'yyyy-MM-dd') + ' ' + FORMAT(cfv.time_value, N'hh\\:mm') FROM custom_field_values AS cfv where legal_cases.id = cfv.recordId AND cfv.custom_field_id = " . $columns[$i] . ")";
                                                                                                                                                                                                                break;
                                                                                                                                                                                                            case "lookup":
                                                                                                                                                                                                                $lookup_type_properties = $this->ci->custom_field->get_lookup_type_properties($field_data["type_data"]);
                                                                                                                                                                                                                $lookup_displayed_columns_table = $lookup_type_properties["external_data"] ? "ltedt" : "ltt";
                                                                                                                                                                                                                $lookup_external_data_join = $lookup_type_properties["external_data"] ? "LEFT JOIN " . $lookup_type_properties["external_data_properties"]["table"] . " ltedt ON ltedt." . $lookup_type_properties["external_data_properties"]["foreign_key"] . " = ltt.id" : "";
                                                                                                                                                                                                                $last_segment = isset($lookup_type_properties["display_properties"]["third_segment"]["column_name"]) ? $lookup_displayed_columns_table . "." . $lookup_type_properties["display_properties"]["second_segment"]["column_name"] . " + ' ' +" . $lookup_displayed_columns_table . "." . $lookup_type_properties["display_properties"]["third_segment"]["column_name"] : $lookup_displayed_columns_table . "." . $lookup_type_properties["display_properties"]["second_segment"]["column_name"];
                                                                                                                                                                                                                $parameters["Field"][] = "\r\n                        (\r\n                            STUFF((\r\n                                SELECT ',' + " . $lookup_displayed_columns_table . "." . $lookup_type_properties["display_properties"]["first_segment"]["column_name"] . "+' ' + " . $last_segment . "\r\n                                FROM custom_field_values cfv\r\n                                left join " . $lookup_type_properties["table"] . " ltt on CAST(ltt.id AS VARCHAR) = cfv.text_value " . $lookup_external_data_join . "\r\n                                 where cfv.recordId = " . $this->_table . ".id  and custom_field_id =  " . $columns[$i] . "\r\n                            FOR XML PATH('')), 1, 1, '')\r\n                        )";
                                                                                                                                                                                                                break;
                                                                                                                                                                                                            case "list":
                                                                                                                                                                                                                $parameters["Field"][] = "( \r\n                            STUFF((SELECT ',' + cfv.text_value FROM custom_field_values cfv WHERE cfv.recordId = " . $this->_table . ".id AND cfv.custom_field_id = " . $columns[$i] . " FOR XML PATH ('')), 1, 1, ''))";
                                                                                                                                                                                                                break;
                                                                                                                                                                                                            default:
                                                                                                                                                                                                                $parameters["Field"][] = "(select cfv.text_value from custom_field_values as cfv where legal_cases.id = cfv.recordId and cfv.custom_field_id = " . $columns[$i] . ")";
                                                                                                                                                                                                        }
                                                                                                                                                                                                    } else {
                                                                                                                                                                                                        $parameters["Field"][] = "legal_cases." . $columns[$i];
                                                                                                                                                                                                    }
                                                                                                                                                                                                }
                                                                                                                                                                                            }
                                                                                                                                                                                        }
                                                                                                                                                                                    }
                                                                                                                                                                                }
                                                                                                                                                                            }
                                                                                                                                                                        }
                                                                                                                                                                    }
                                                                                                                                                                }
                                                                                                                                                            }
                                                                                                                                                        }
                                                                                                                                                    }
                                                                                                                                                }
                                                                                                                                            }
                                                                                                                                        }
                                                                                                                                    }
                                                                                                                                }
                                                                                                                            }
                                                                                                                        }
                                                                                                                    }
                                                                                                                }
                                                                                                            }
                                                                                                        }
                                                                                                    }
                                                                                                }
                                                                                            }
                                                                                        }
                                                                                    }
                                                                                }
                                                                            }
                                                                        }
                                                                    }
                                                                }
                                                            }
                                                        }
                                                    }
                                                }
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            }
            $parameters["Lang_Field"][] = $columns[$i];
        }
        return $parameters;
    }
    public function get_legal_cases_by_Position_and_status($data, $columns_selected, $filter, $sortable, $totalRows = false)
    {
        $query = $this->get_legal_cases_grid_query_web($filter, NULL, "", false, true, true);
        $limits = $data["limits"];
        $skip = $data["skip"];
        $take = $data["take"];
        if (!empty($limits)) {
            $take1 = $limits;
            $skip1 = 0;
            if (!$totalRows) {
                if (!empty($limits)) {
                    if ($limits < $take) {
                        $take1 = $limits;
                        $skip1 = 0;
                    } else {
                        if ($take < $limits) {
                            if ($limits < $skip + $take) {
                                $take1 = $limits - $skip;
                                $skip1 = $skip;
                            } else {
                                $take1 = $take;
                                $skip1 = $skip;
                            }
                        }
                    }
                } else {
                    $take1 = $take;
                    $skip1 = $skip;
                }
            }
        }
        if (isset($take1)) {
            $take = $take1;
            $skip = $skip1;
        }
        $response = [];
        if (!empty($data["position"])) {
            $query["where"][] = ["legal_cases.legal_case_client_position_id", $data["position"]];
        }
        if (!empty($data["cases_category"]) && $data["cases_category"] != "All") {
            $query["where"][] = ["legal_cases.category", $data["cases_category"]];
        }
        if (!empty($data["sort"])) {
            $sortable = $data["sort"];
        }
        $query["group_by"] = ["legal_cases.id"];
        $select = "";
        foreach ($columns_selected["Field"] as $key => $value) {
            if ($key != count($columns_selected["Field"]) - 1) {
                $select .= $value . " as '" . $columns_selected["Lang_Field"][$key] . "' ,";
            } else {
                $select .= $value . " as '" . $columns_selected["Lang_Field"][$key] . "' ";
            }
        }
        $query["select"] = [$select, false];
        if (is_array($sortable) && !empty($sortable)) {
            unset($query["order_by"]);
            foreach ($sortable as $_sort) {
                $query["order_by"][] = [$_sort["field"], $_sort["dir"]];
            }
        } else {
            $query["order_by"] = ["legal_cases.id desc"];
        }
        if (empty($limits) && !$totalRows) {
            $query["limit"] = [$this->ci->input->post("take", true), $this->ci->input->post("skip", true)];
        } else {
            if (!empty($limits)) {
                $query["limit"] = [$take, $skip];
            }
        }
        $response["data"] = parent::load_all($query);
        if (!empty($limits)) {
            if (isset($response["data"])) {
                if (sizeof($response["data"]) != 0 && $data["take"] <= sizeof($response["data"]) && sizeof($response["data"]) < $limits && $data["skip"] == 0) {
                    $response["totalRows"] = $limits;
                } else {
                    $response["totalRows"] = sizeof($response["data"]);
                }
            }
        } else {
            $response["totalRows"] = $this->count_total_matching_rows($query);
        }
        return $response;
    }
    public function cases_by_tiers($filter, $sortable, $pagingOn)
    {
        $query = $this->get_legal_cases_grid_query_web(NULL, NULL, "", false, true, true);
        $subjectValue = "";
        if (!empty($filter["caseSubject"])) {
            $subjectValue = " and legal_cases.subject LIKE '%" . $filter["caseSubject"] . "%'";
        }
        if ((!empty($filter["range2"]) || $filter["range2"] == 0) && $filter["range2"] != "NULL") {
            $query["where"][] = ["legal_cases.caseValue between  '" . $filter["range2"] . "' and  '" . $filter["range1"] . "' and  legal_cases.case_type_id=" . $filter["case_type_id"] . $subjectValue];
        } else {
            if (empty($filter["range2"]) || $filter["range2"] == "NULL") {
                $query["where"][] = ["legal_cases.caseValue >  '" . $filter["range1"] . "'  and  legal_cases.case_type_id=" . $filter["case_type_id"] . $subjectValue];
            }
        }
        if (is_array($sortable) && !empty($sortable)) {
            foreach ($sortable as $_sort) {
                $query["order_by"][] = [$_sort["field"], $_sort["dir"]];
            }
        } else {
            $query["order_by"] = ["legal_cases.id desc"];
        }
        $paginationConf = [];
        if ($limit = $this->ci->input->post("take", true)) {
            $query["limit"] = [$this->ci->input->post("take", true), $this->ci->input->post("skip", true)];
            $paginationConf = ["urlPrefix" => ""];
            $paginationConf["inPage"] = $this->ci->input->post("take", true);
        }
        $result["data"] = $pagingOn ? parent::paginate($query, $paginationConf) : parent::load_all($query);
        $result["totalRows"] = $this->count_total_matching_rows($query);
        return $result;
    }
    public function api_load_all_data($category, $loggedUserId, $overridePrivacy, $take = 20, $skip = 0, $term = "", $search_filters, $lang)
    {
        $query = $this->get_legal_cases_grid_query_api(NULL, NULL, "", false, $loggedUserId, $overridePrivacy, false, true);
        $response = [];
        $query["select"] = ["legal_cases.id,\r\n                                    MAX(legal_cases.channel) AS channel,\r\n                                    MAX(legal_cases.case_status_id) AS case_status_id,\r\n                                    MAX(legal_cases.case_type_id) AS case_type_id,\r\n                                    MAX(legal_cases.provider_group_id) AS provider_group_id,\r\n                                    MAX(legal_cases.user_id) AS user_id,\r\n                                    MAX(legal_cases.contact_id) AS contact_id,\r\n                                    MAX(legal_cases.client_id) AS client_id,\r\n                                    MAX(legal_cases.referredBy) AS referredBy,\r\n                                    MAX(legal_cases.requestedBy) AS requestedBy,\r\n                                    MAX(cast(legal_cases.subject as varchar(255))) AS subject,\r\n                                    MAX(cast(legal_cases.description as varchar(255))) AS description,\r\n                                    MAX(cast(legal_cases.latest_development as varchar(255))) AS latest_development,\r\n                                    MAX(legal_cases.priority) AS priority,\r\n                                    MAX(legal_cases.caseArrivalDate) AS caseArrivalDate,\r\n                                    MAX(legal_cases.caseArrivalDate) AS caseArrivalDate,\r\n                                    MAX(legal_cases.dueDate) AS dueDate,\r\n                                    MAX(legal_cases.closedOn) AS closedOn,\r\n                                    MAX(legal_cases.statusComments) AS statusComments,\r\n                                    MAX(legal_cases.category) AS category,\r\n                                    MAX(legal_cases.caseValue) AS caseValue,\r\n                                    MAX(legal_cases.internalReference) AS internalReference,\r\n                                    MAX(legal_cases.externalizeLawyers) AS externalizeLawyers,\r\n                                    MAX(legal_cases.createdOn) AS createdOn,\r\n                                    MAX(legal_cases.createdBy) AS createdBy,\r\n                                    MAX(legal_cases.modifiedOn) AS modifiedOn,\r\n                                    MAX(legal_cases.modifiedBy) AS modifiedBy,\r\n                                    MAX(legal_cases.archived) AS archived,\r\n                                    MAX(legal_cases.private) AS private,\r\n                                    MAX(legal_cases.timeTrackingBillable) AS timeTrackingBillable,\r\n                                    MAX(legal_cases.expensesBillable) AS expensesBillable,\r\n                                    MAX(legal_cases.recoveredValue) AS recoveredValue,\r\n                                    MAX(legal_cases.judgmentValue) AS judgmentValue,\r\n                                    MAX(legal_cases.legal_case_client_position_id) AS legal_case_client_position_id,\r\n                                    MAX(legal_cases.estimatedEffort) AS estimatedEffort,\r\n                                    MAX(CAST( lcee.effectiveEffort AS nvarchar )) AS effectiveEffort,\r\n                                    ( '" . $this->modelCode . "' + cast(legal_cases.id as varchar(11))) as caseID,\r\n                                    MAX(workflow_status.name) AS status,\r\n                                    MAX(case_types.name) AS type,\r\n                                    MAX(provider_groups.name) AS providerGroup,\r\n                                    MAX(up.status) as assigneStatus,\r\n                                    (MAX(up.firstName) + ' ' + MAX(up.lastName)) AS assignee,\r\n                                    MAX(up.user_id) AS assignee_user_id,\r\n                                    MAX(legal_cases.archived) AS archivedCases,\r\n                                    MAX(legal_case_litigation_details.court_type_id) AS court_type_id,\r\n                                    MAX(legal_case_litigation_details.court_degree_id) AS court_degree_id,\r\n                                    MAX(legal_case_litigation_details.court_region_id) AS court_region_id,\r\n                                    MAX(legal_case_litigation_details.court_id) AS court_id,\r\n                                    MAX(legal_case_litigation_details.sentenceDate) AS sentenceDate,\r\n                                    CASE WHEN MAX(clients.company_id) IS NULL THEN 'Person' ELSE 'Company' END  AS clientType,\r\n                                    CASE \r\n                                         WHEN Max(clients.company_id) IS NULL THEN (CASE WHEN MAX(cont.father)!='' THEN MAX(cont.firstName) + ' '+ \r\n                                                MAX(cont.father) + ' ' + MAX(cont.lastName) \r\n                                         ELSE MAX(cont.firstName)+' '+MAX(cont.lastName) END) ELSE MAX(comp.name) END AS clientName,\r\n                                    CASE\r\n                                           when (MAX(referredbycontact.father) <> ' ') then (' ' + MAX(referredbycontact.firstName) + '\r\n                                        ' +\r\n                                                                                             MAX(referredbycontact.father) + ' ' +\r\n                                                                                             MAX(referredbycontact.lastName))\r\n                                           else (' ' + MAX(referredbycontact.firstName) + ' ' +\r\n                                                 MAX(referredbycontact.lastName)) END AS referredByName,\r\n                                    CASE\r\n                                         when (MAX(requestedbycontact.father) <> ' ') then (' ' +\r\n                                                                                            MAX(requestedbycontact.firstName) + ' ' +\r\n                                                                                            MAX(requestedbycontact.father) + ' ' +\r\n                                                                                            MAX(requestedbycontact.lastName))\r\n                                         else (' ' + MAX(requestedbycontact.firstName) + ' ' +\r\n                                               MAX(requestedbycontact.lastName)) END AS requestedByName,\r\n                                    MAX(legal_case_stage_languages.name) as caseStage,\r\n                                    MAX(legal_cases.legal_case_stage_id) AS legal_case_stage_id,\r\n                                    MAX(legal_cases.legal_case_success_probability_id) AS legal_case_success_probability_id,\r\n                                    MAX(legal_case_client_position_languages.name) AS caseClientPosition, CASE WHEN Max(clients.company_id) IS NULL THEN MAX(cont.foreignFirstName) + ' ' + MAX(cont.foreignLastName) ELSE MAX(comp.foreignName) END AS clientForeignName,", false];
        $query["where"][] = ["legal_cases.category", $category];
        $query["where"][] = ["legal_cases.archived", "no"];
        if ($term != "") {
            $term = $this->ci->db->escape_like_str($term);
            $query["where"][] = [" ( legal_cases.subject LIKE '%" . $term . "%' or legal_cases.description LIKE '%" . $term . "%' )", NULL, false];
        }
        $query = $this->filter_builder($query, $search_filters);
        $query["group_by"] = ["legal_cases.id"];
        $query["order_by"] = ["legal_cases.id desc"];
        $query["limit"] = [$take, $skip];
        $response["data"] = parent::load_all($query);
        $response["totalRows"] = $this->count_total_matching_rows($query);
        return $response;
    }
    public function api_load_all_related_contacts($caseId)
    {
        $query = [];
        $response = [];
        $query["select"] = ["legal_cases_contacts.id, legal_cases_contacts.comments as comments, ( CASE WHEN contacts.father!='' THEN contacts.firstName + ' '+ contacts.father + ' ' + contacts.lastName ELSE contacts.firstName+' '+contacts.lastName END ) as contactName, legal_cases_contacts.contactType, CASE WHEN legal_case_contact_role_id IS NULL THEN 0 ELSE legal_case_contact_role_id END AS legal_case_contact_role_id, contacts.id AS contactId, legal_case_contact_roles.name as roleName", false];
        $query["join"] = [["contacts", "contacts.id = legal_cases_contacts.contact_id", "left"], ["legal_case_contact_roles", "legal_case_contact_roles.id = legal_cases_contacts.legal_case_contact_role_id", "left"]];
        $query["where"][] = ["legal_cases_contacts.contactType", "contact"];
        $query["where"][] = ["legal_cases_contacts.case_id", $caseId];
        $query["where"][] = ["contacts.status = 'Active'", NULL, false];
        $this->prep_query($query);
        $response["totalRows"] = $this->ci->db->count_all_results("legal_cases_contacts");
        $query["order_by"] = ["legal_cases_contacts.id desc"];
        $this->prep_query($query);
        $result = $this->ci->db->get("legal_cases_contacts");
        $response["data"] = $result->result_array();
        return $response;
    }
    public function api_lookup($term, $category, $loggedUser, $overridePrivacy, $cpFeatureEnabled = false)
    {
        $configList = ["key" => "legal_cases.id", "value" => "name"];
        if ($cpFeatureEnabled) {
            $configQury["select"] = ["legal_cases.id, ( '" . $this->modelCode . "' +  CAST( legal_cases.id AS nvarchar ) ) AS caseID ,legal_cases.subject as subject, CASE WHEN legal_cases.channel = 'CP' THEN 'true' ELSE 'false' END as comingFromCP,legal_cases.client_id,clients_view.name as clientName,legal_cases.timeTrackingBillable as is_time_tracking_billable, legal_cases.category, workflow_status.name as status, ( user_profiles.firstName + ' ' + user_profiles.lastName ) as createdByName,( modified.firstName + ' ' + modified.lastName ) AS modifiedByName", false];
        } else {
            $configQury["select"] = ["legal_cases.id,  ( '" . $this->modelCode . "' +  CAST( legal_cases.id AS nvarchar ) ) AS caseID ,legal_cases.subject as subject, 'false' as comingFromCP,legal_cases.client_id,clients_view.name as clientName,legal_cases.timeTrackingBillable as is_time_tracking_billable, legal_cases.category, workflow_status.name as status, ( user_profiles.firstName + ' ' + user_profiles.lastName ) as createdByName,( modified.firstName + ' ' + modified.lastName ) AS modifiedByName", false];
        }
        $configQury["join"] = [["clients_view", "clients_view.id=legal_cases.client_id AND clients_view.model = 'clients'", "left"], ["workflow_status", "workflow_status.id = legal_cases.case_status_id", "left"], ["user_profiles", "user_profiles.user_id = legal_cases.createdBy", "left"], ["user_profiles modified", "modified.user_id = legal_cases.modifiedBy", "left"]];
        $configQury["where"] = [];
        if (!empty($term)) {
            $configQury["where"] = [];
            if ($category != "") {
                if ($category == "Litigation_Matter") {
                    $configQury["where"][] = ["(legal_cases.category  = 'Litigation' OR legal_cases.category  = 'Matter') ", NULL, false];
                } else {
                    $configQury["where"][] = ["legal_cases.category  = '" . $category . "'", NULL, false];
                }
            }
            $modelCode = substr($term, 0, 1);
            $ID = substr($term, 1);
            if (strcasecmp($modelCode, $this->get("modelCode")) === 0 && is_numeric($ID)) {
                $qId = substr($term, 1);
                if (is_numeric($qId)) {
                    $configQury["where"][] = ["legal_cases.id = " . $qId, NULL, false];
                }
                $configQury["where"][] = $this->get_matter_privacy_conditions($loggedUser, $overridePrivacy);
                $configQury["where"][] = ["legal_cases.archived = '" . $this->notArchived . "'"];
            } else {
                $term = $this->ci->db->escape_like_str($term);
                $configQury["where"][] = ["(legal_cases.subject LIKE '%" . $term . "%' or legal_cases.internalReference LIKE '%" . $term . "%' or clients_view.name LIKE '%" . $term . "%')", NULL, false];
                $configQury["where"][] = $this->get_matter_privacy_conditions($loggedUser, $overridePrivacy);
                $configQury["where"][] = ["legal_cases.archived = '" . $this->notArchived . "'"];
            }
        }
        return $this->load_all($configQury, $configList);
    }
    public function check_case_related_to_money($case_id)
    {
        $_table = $this->_table;
        $this->_table = "voucher_headers AS vh";
        $query = [];
        $query["select"] = ["vrc.legal_case_id"];
        $query["join"] = ["voucher_related_cases vrc", "vrc.voucher_header_id = vh.id", "inner"];
        $query["where"][] = ["vrc.legal_case_id", $case_id];
        $return = $this->load_all($query);
        $this->_table = $_table;
        return !empty($return) ? true : false;
    }
    public function load_client_amount_transactions($client_id = 0, $case_id = 0)
    {
        $query = "SELECT voucher_headers.organization_id, accounts.currency_id, payments.payments_made, \r\n                total-ISNULL(payments.payments_made, 0.0) as balance_due, invoices.paidStatus as invoice_status\r\n                FROM voucher_headers\r\n                INNER JOIN invoice_headers invoices ON invoices.voucher_header_id = voucher_headers.id\r\n                LEFT JOIN (\r\n                SELECT invoice_payment_invoices.invoice_header_id,\r\n                SUM(invoice_payment_invoices.amount) as payments_made\r\n                FROM invoice_payment_invoices\r\n                GROUP BY invoice_payment_invoices.invoice_header_id\r\n                ) payments ON payments.invoice_header_id = invoices.id\r\n                INNER JOIN accounts_details_lookup accounts ON accounts.id = invoices.account_id\r\n                WHERE invoices.paidStatus!= 'draft' AND invoices.paidStatus!= 'cancelled'\r\n                AND accounts.model_id =  " . $client_id . "\r\n                AND voucher_related_cases.legal_case_id = " . $case_id;
        $results = $this->ci->db->query($query)->result_array();
        $system_preferences = $this->ci->session->userdata("systemPreferences");
        $this->ci->load->model("exchange_rate");
        $exchange_rates = $this->ci->exchange_rate->get_all_exchange_rates();
        $case_currency_id = $this->load_system_currency_id();
        $due_balance = $paid_balance = 0;
        foreach ($results as $record) {
            if ($record["invoice_status"] == "open" || $record["invoice_status"] == "partially paid") {
                $due_balance += $record["balance_due"] * 1 * $exchange_rates[$record["organization_id"]][$record["currency_id"]] / $exchange_rates[$record["organization_id"]][$case_currency_id];
            }
            if ($record["invoice_status"] == "paid" || $record["invoice_status"] == "partially paid") {
                $paid_balance += $record["payments_made"] * 1 * $exchange_rates[$record["organization_id"]][$record["currency_id"]] / $exchange_rates[$record["organization_id"]][$case_currency_id];
            }
        }
        return ["due_balance" => number_format($due_balance, 2) . " " . $system_preferences["caseValueCurrency"], "paid_balance" => number_format($paid_balance, 2) . " " . $system_preferences["caseValueCurrency"]];
    }
    function load_matter_feeNotes($matter_id,$load_list=false){
      $response = [];
        $table = $this->_table;
        $this->_table = "bills_full_details";
        $query["where"]=["case_id", $matter_id];
        if (!$load_list){
            $query["group_by"] = ["case_id"];
            $query["select"] = ["sum(total) as total_fees, sum(payemntsMade) as total_settled,sum(balanceDue) as balance_due",false];
            $response = $this->load($query);
        }else {
            $response = $this->load_all($query);
        }
        $this->_table = $table;
        return $response;
    }
    public function get_legal_cases_grid_query_web($filter, $sortable, $page_number, $pagingOn, $return_query = false, $is_litigation = false, $get_query_count = true)
    {
        $this->ci->db->_protect_identifiers = false;
        $this->ci->db->_force_escape_string_values = true;
        $this->ci->load->model("language");
        $lang_id = $this->ci->language->get_id_by_session_lang();
        $response = $this->get_legal_cases_grid_query_details($lang_id, $this->logged_user_id, $this->override_privacy, $filter, $sortable, $page_number, $pagingOn, true, $return_query, $is_litigation, $get_query_count);
        $this->ci->db->_protect_identifiers = true;
        $this->ci->db->_force_escape_string_values = false;
        return $response;
    }
    public function get_legal_cases_grid_query_api($filter, $sortable, $page_number, $pagingOn, $loggedUserId, $overridePrivacy, $language, $return_query = false, $is_litigation = false)
    {
        $this->ci->db->_protect_identifiers = false;
        $this->ci->db->_force_escape_string_values = true;
        $this->ci->load->model("language");
        $lang_id = $this->ci->language->get_id_by_session_lang($loggedUserId);
        $response = $this->get_legal_cases_grid_query_details($lang_id, $loggedUserId, $overridePrivacy, $filter, $sortable, $page_number, $pagingOn, true, $return_query, $is_litigation);
        $this->ci->db->_protect_identifiers = true;
        $this->ci->db->_force_escape_string_values = false;
        return $response;
    }
    private function get_legal_cases_grid_query_details($lang_id, $logged_user_id, $override_privacy, $filter, $sortable, $page_number, $pagingOn, $is_filtered, $return_query = false, $is_litigation = false, $get_query_count = true)
    {
        $this->_table = "legal_cases";
        $query = [];
        $this->ci->load->model("language");
        $langId = $this->ci->language->get_id_by_session_lang();
        $query["select"] = ["(case when (MAX(legal_cases.channel) = 'CP') then 'yes' else 'no' end)  AS isCP,\r\n                           legal_cases.id                                                                                  AS id,\r\n                           MAX(legal_cases.channel)                                                                        AS channel,\r\n                           MAX(legal_cases.visibleToCP)                                                                    AS visibleToCP,\r\n                           MAX(legal_cases.case_status_id)                                                                 AS case_status_id,\r\n                           MAX(legal_cases.provider_group_id)                                                              AS provider_group_id,\r\n                           MAX(legal_cases.user_id)                                                                        AS user_id,\r\n                           MAX(legal_cases.contact_id)                                                                     AS contact_id,\r\n                           MAX(cast(legal_cases.subject as varchar(255)))                                                  AS subject,\r\n                           MAX(cast(legal_cases.description as varchar(255)))                                              AS description,\r\n                           MAX(cast(legal_cases.latest_development as varchar(max)))                                       AS latest_development,\r\n                           MAX(legal_cases.priority)                                                                       AS priority,\r\n                           MAX(legal_cases.arrivalDate)                                                                    AS arrivalDate,\r\n                           MAX(legal_cases.caseArrivalDate)                                                                AS caseArrivalDate,\r\n                           MAX(legal_cases.dueDate)                                                                        AS dueDate,\r\n                           MAX(legal_cases.closedOn)                                                                       AS closedOn,\r\n                           MAX(legal_cases.statusComments)                                                                 AS statusComments,\r\n                           MAX(legal_cases.category)                                                                       AS category,\r\n                           MAX(legal_cases.caseValue)                                                                      AS caseValue,\r\n                           MAX(legal_cases.internalReference)                                                              AS internalReference,\r\n                           MAX(legal_cases.externalizeLawyers)                                                             AS externalizeLawyers,\r\n                           MAX(legal_cases.estimatedEffort)                                                                AS estimatedEffort,\r\n                           MAX(legal_cases.createdOn)                                                                      AS createdOn,\r\n                           MAX(legal_cases.createdBy)                                                                      AS createdBy,\r\n                           MAX(legal_cases.modifiedOn)                                                                     AS modifiedOn,\r\n                           MAX(legal_cases.modifiedBy)                                                                     AS modifiedBy,\r\n                           MAX(legal_cases.archived)                                                                       AS archived,\r\n                           MAX(legal_cases.private)                                                                        AS private,\r\n                           MAX(legal_cases.timeTrackingBillable)                                                           AS timeTrackingBillable,\r\n                           MAX(legal_cases.expensesBillable)                                                               AS expensesBillable,\r\n                           MAX(legal_cases.archived)                                                                       AS archivedCases,\r\n                           MAX(legal_cases.client_id)                                                                      AS client_id,\r\n                           MAX(legal_cases.legal_case_stage_id)                                                            AS legal_case_stage_id,\r\n                           MAX(legal_cases.legal_case_client_position_id)                                                  AS legal_case_client_position_id,\r\n                           MAX(legal_cases.legal_case_success_probability_id)                                              AS legal_case_success_probability_id,\r\n                           MAX(legal_cases.recoveredValue)                                                                 AS recoveredValue,\r\n                           MAX(legal_cases.judgmentValue)                                                                  AS judgmentValue,\r\n                           MAX(legal_cases.referredBy)                                                                     AS referredBy,\r\n                           MAX(legal_cases.requestedBy)                                                                    AS requestedBy,\r\n                           MAX(case_types.name)                                                                            AS type,\r\n                           MAX(legal_cases.case_type_id)                                                                   AS case_type_id,\r\n                           MAX(provider_groups.name)                                                                       AS providerGroup,\r\n                           MAX(workflow_status.name)                                                                       AS status,\r\n                           MAX(legal_case_litigation_details.id)                                                           AS stage_id,\r\n                           MAX(legal_case_litigation_details.status)                                                       AS stage_status,\r\n                           MAX(legal_case_litigation_details.court_type_id)                                                AS court_type_id,\r\n                           MAX(legal_case_litigation_details.court_degree_id)                                              AS court_degree_id,\r\n                           MAX(legal_case_litigation_details.court_region_id)                                              AS court_region_id,\r\n                           MAX(legal_case_litigation_details.court_id)                                                     AS court_id,\r\n                           MAX(cast(legal_case_litigation_details.comments as varchar(255)))                               AS comments,\r\n                           MAX(legal_case_litigation_details.sentenceDate)                                                 AS sentenceDate,\r\n                           MAX(com.name)                                                                                   AS company,\r\n                           MAX(com.id)                                                                                     AS company_id,\r\n                           MAX(legal_case_litigation_external_references.number)                                           AS litigationExternalRef,\r\n                           MAX(user_profiles.status)                                                                       as userStatus,\r\n                           MAX(lcee.effectiveEffort)                                                                       AS effectiveEffort,\r\n                           (CASE WHEN MAX(legal_case_litigation_details.status) IS NULL THEN MAX(legal_case_stage_languages.name) ELSE (MAX(legal_case_stage_languages.name) + ' (' + MAX(stage_statuses_languages.name) + ')') END) AS caseStage,\r\n                           MAX(stage_statuses_languages.name)                                                              as stageStatus,\r\n                           MAX(legal_case_success_probability_languages.name)                                              as caseSuccessProbability,\r\n                           MAX(legal_case_client_position_languages.name)                                                  AS caseClientPosition,\r\n                           MAX(legal_case_containers.subject)                                                              AS legalCaseContainerSubject,\r\n                           (MAX(up.firstName) + ' ' + MAX(up.lastName))                                                    AS assignee,\r\n                           ('M' + cast(legal_cases.id as varchar(11)))                                                     AS caseID,\r\n                           (MAX(modified_users.firstName) + ' ' + MAX(modified_users.lastName))                              AS modifiedByName,\r\n                           (MAX(created_users.firstName) + ' ' + MAX(created_users.lastName))                                AS createdByName,\r\n                           CASE WHEN MAX(clients.company_id) IS NULL THEN 'Person' ELSE 'Company' END                      AS clientType,\r\n                           CASE \r\n                             WHEN Max(clients.company_id) IS NULL THEN (CASE WHEN MAX(cont.father)!='' THEN MAX(cont.firstName) + ' '+ \r\n                                    MAX(cont.father) + ' ' + MAX(cont.lastName) \r\n                             ELSE MAX(cont.firstName)+' '+MAX(cont.lastName) END) ELSE MAX(comp.name) END                  AS clientName,\r\n                           CASE\r\n                             WHEN Max(clients.company_id) IS NULL THEN MAX(cont.foreignFirstName)+' '+MAX(cont.foreignLastName)\r\n                                ELSE MAX(comp.foreignName) END                                                             AS client_foreign_name,\r\n                           CASE\r\n                               when (MAX(conre.father) <> ' ') then (' ' + MAX(conre.firstName) + ' ' + MAX(conre.father) + ' ' +\r\n                                                                     MAX(conre.lastName))\r\n                               else (' ' + MAX(conre.firstName) + ' ' + MAX(conre.lastName)) END                           AS contact,\r\n                           CASE\r\n                               when (MAX(conhe.father) <> ' ') then (' ' + MAX(conhe.firstName) + ' ' + MAX(conhe.father) + ' ' +\r\n                                                                     MAX(conhe.lastName))\r\n                               else (' ' + MAX(conhe.firstName) + ' ' + MAX(conhe.lastName)) END                           AS contactContributor,\r\n                           CASE\r\n                               when (MAX(conextlaw.father) <> ' ') then (MAX(conextlaw.firstName) + ' ' +\r\n                                                                         MAX(conextlaw.father) + ' ' + MAX(conextlaw.lastName))\r\n                               else (MAX(conextlaw.firstName) + ' ' + MAX(conextlaw.lastName)) END                         AS contactOutsourceTo,\r\n                           MAX(companiesExtLaw.name) AS companyOutsourceTo,\r\n                           CASE\r\n                               when (MAX(referredbycontact.father) <> ' ') then (' ' + MAX(referredbycontact.firstName) + ' ' +\r\n                                                                                 MAX(referredbycontact.father) + ' ' +\r\n                                                                                 MAX(referredbycontact.lastName))\r\n                               else (' ' + MAX(referredbycontact.firstName) + ' ' +\r\n                                     MAX(referredbycontact.lastName)) END                                                  AS referredByName,\r\n                           CASE\r\n                               when (MAX(requestedbycontact.father) <> ' ') then (' ' + MAX(requestedbycontact.firstName) + ' ' +\r\n                                                                                  MAX(requestedbycontact.father) + ' ' +\r\n                                                                                  MAX(requestedbycontact.lastName))\r\n                               else (' ' + MAX(requestedbycontact.firstName) + ' ' +\r\n                                     MAX(requestedbycontact.lastName)) END                                                 AS requestedByName,\r\n                           MAX(legal_case_containers.subject)                                                              AS legalCaseContainerSubject", false];
        if ($is_filtered && isset($filter) && is_array($filter)) {
            if (isset($filter["filters"])) {
                foreach ($filter["filters"] as $_filter) {
                    if (!empty($_filter["filters"][0]["field"]) && in_array($_filter["filters"][0]["field"], ["lcee.effectiveEffort", "legal_cases.estimatedEffort"])) {
                        $system_preferences = $this->ci->session->userdata("systemPreferences");
                        $this->ci->load->library("TimeMask");
                        $_filter["filters"][0]["value"] = $this->ci->timemask->humanReadableToHours($_filter["filters"][0]["value"]);
                    }
                    $this->prep_k_filter($_filter, $query, $filter["logic"]);
                }
                unset($_filter);
            }
            if (isset($filter["customFields"])) {
                $this->ci->load->model("custom_field", "custom_fieldfactory");
                $this->ci->custom_field = $this->ci->custom_fieldfactory->get_instance("custom_fieldfactory");
                $this->ci->custom_field->prep_custom_field_filters($this->modelName, $filter["customFields"], $query);
            }
        }
        if ($is_litigation || isset($query["select"][0]) && isset($query["where"][0][1]) && $query["where"][0][1] === "%Litigation%" || isset($query["where"][1][1]) && $query["where"][1][1] === "%Litigation%") {
            $query["select"][0] .= ",MAX(court_types.name) as court_type,MAX(court_degrees.name) as court_degree,MAX(court_regions.name) as court_region,MAX(courts.name) as court,";
            $query["select"][0] .= ",opponentNationalities = STUFF((SELECT ', ' + (CASE\r\n                                                                             WHEN legal_case_opponents.opponent_member_type = 'company'\r\n                                                                                 THEN (CASE\r\n                                                                                           WHEN opponentcompanynationalities.name IS NOT NULL\r\n                                                                                               THEN (opponentCompany.name + '(' +\r\n                                                                                                     opponentcompanynationalities.name +\r\n                                                                                                     ')')\r\n                                                                                           ELSE (opponentCompany.name + '( - )') END)\r\n                                                                             ELSE (CASE\r\n                                                                                       WHEN opponentContactNationalitiesCountry.name IS NOT NULL\r\n                                                                                           THEN (opponentContact.firstName + ' ' +\r\n                                                                                                 opponentContact.lastName + '(' +\r\n                                                                                                 opponentContactNationalitiesCountry.name +\r\n                                                                                                 ')')\r\n                                                                                       ELSE (opponentContact.firstName + ' ' + opponentContact.lastName + '( - )') END) END)\r\n                                                          FROM legal_case_opponents\r\n                                                                   INNER JOIN opponents ON opponents.id = legal_case_opponents.opponent_id\r\n                                                                   LEFT JOIN companies AS opponentCompany\r\n                                                                             ON opponentCompany.id = opponents.company_id AND\r\n                                                                                legal_case_opponents.opponent_member_type = 'company'\r\n                                                                   LEFT JOIN contacts AS opponentContact\r\n                                                                             ON opponentContact.id = opponents.contact_id AND\r\n                                                                                legal_case_opponents.opponent_member_type = 'contact'\r\n                                                                   LEFT JOIN countries_languages AS opponentCompanyNationalities\r\n                                                                             ON opponentCompanyNationalities.country_id = opponentCompany.nationality_id AND opponentCompanyNationalities.language_id = " . $this->get_lang_id() . " AND \r\n                                                                                legal_case_opponents.opponent_member_type = 'company'\r\n                                                                   LEFT JOIN contact_nationalities_details AS opponentContactNationalities\r\n                                                                             ON opponentContactNationalities.contact_id = opponentContact.id AND\r\n                                                                                legal_case_opponents.opponent_member_type = 'contact'\r\n                                                                    LEFT JOIN countries_languages AS opponentContactNationalitiesCountry ON opponentContactNationalitiesCountry.country_id = opponentContactNationalities.nationality_id AND opponentContactNationalitiesCountry.language_id = " . $this->get_lang_id() . "\r\n                                                          WHERE legal_case_opponents.case_id = legal_cases.id FOR XML PATH ('')), 1,\r\n                                                         1, ''),\r\n                                       opponentNames         = STUFF((SELECT ', ' + (CASE\r\n                                                                                         WHEN opponent_positions.name IS NOT NULL THEN CASE\r\n                                                                                                                                           WHEN legal_case_opponents.opponent_member_type = 'company'\r\n                                                                                                                                               THEN (opponentCompany.name + ' - ' + opponent_positions.name)\r\n                                                                                                                                           ELSE opponentContact.firstName +\r\n                                                                                                                                                ' ' +\r\n                                                                                                                                                opponentContact.lastName +\r\n                                                                                                                                                ' - ' +\r\n                                                                                                                                                opponent_positions.name END\r\n                                                                                         ELSE CASE\r\n                                                                                                  WHEN legal_case_opponents.opponent_member_type = 'company'\r\n                                                                                                      THEN opponentCompany.name\r\n                                                                                                  ELSE opponentContact.firstName + ' ' + opponentContact.lastName END END)\r\n                                                                      FROM legal_case_opponents\r\n                                                                               INNER JOIN opponents ON opponents.id = legal_case_opponents.opponent_id\r\n                                                                               LEFT JOIN companies AS opponentCompany\r\n                                                                                         ON opponentCompany.id = opponents.company_id AND\r\n                                                                                            legal_case_opponents.opponent_member_type = 'company'\r\n                                                                               LEFT JOIN contacts AS opponentContact\r\n                                                                                         ON opponentContact.id = opponents.contact_id AND\r\n                                                                                            legal_case_opponents.opponent_member_type = 'contact'\r\n                                                                               LEFT JOIN legal_case_opponent_position_languages AS opponent_positions\r\n                                                                                         ON opponent_positions.legal_case_opponent_position_id =\r\n                                                                                            legal_case_opponents.opponent_position and\r\n                                                                                            opponent_positions.language_id = '" . $langId . "'\r\n                                                                      WHERE legal_case_opponents.case_id = legal_cases.id FOR XML PATH ('')), 1,\r\n                                                                     1, ''), opponent_foreign_name = STUFF((SELECT ', ' +\r\n\t\t\t\t(\r\n                                    CASE WHEN opponent_positions.name != '' THEN\r\n                                    (\r\n                                        CASE WHEN legal_case_opponents.opponent_member_type = 'company'\r\n                                        THEN (CASE WHEN opponentCompany.foreignName IS NULL THEN opponentCompany.name ELSE opponentCompany.foreignName END) + ' - ' + opponent_positions.name\r\n                                        ELSE (CASE WHEN opponentContact.foreignFirstName IS NULL THEN opponentContact.firstName ELSE opponentContact.foreignFirstName END) + ' ' + (CASE WHEN opponentContact.foreignLastName IS NULL THEN opponentContact.lastName ELSE opponentContact.foreignLastName END) + ' - ' + opponent_positions.name END\r\n                                    )\r\n                                    ELSE\r\n                                    (\r\n                                        CASE WHEN legal_case_opponents.opponent_member_type = 'company'\r\n                                        THEN (CASE WHEN opponentCompany.foreignName IS NULL THEN opponentCompany.name ELSE opponentCompany.foreignName END)\r\n                                        ELSE (CASE WHEN opponentContact.foreignFirstName IS NULL THEN opponentContact.firstName ELSE opponentContact.foreignFirstName END) + ' ' + (CASE WHEN opponentContact.foreignLastName IS NULL THEN opponentContact.lastName ELSE opponentContact.foreignLastName END) END\r\n                                    )\r\n                                    END\r\n                                )\r\n\t\t\t\t FROM legal_case_opponents\r\n\t\t\t\t INNER JOIN opponents ON opponents.id = legal_case_opponents.opponent_id\r\n\t\t\t\t LEFT JOIN companies AS opponentCompany ON opponentCompany.id = opponents.company_id AND legal_case_opponents.opponent_member_type = 'company'\r\n\t\t\t\t LEFT JOIN contacts AS opponentContact ON opponentContact.id = opponents.contact_id AND legal_case_opponents.opponent_member_type = 'contact'\r\n                                 LEFT JOIN legal_case_opponent_position_languages AS opponent_positions ON opponent_positions.legal_case_opponent_position_id = legal_case_opponents.opponent_position and opponent_positions.language_id = '" . $langId . "'\r\n\t\t\t\t WHERE legal_case_opponents.case_id = legal_cases.id\r\n\t\t\t\tFOR XML PATH('')), 1, 1, '')";
            $query["select"][0] .= ", (SELECT DISTINCT (CAST(legal_case_hearings.startDate AS varchar) + ' - ' +  CAST(legal_case_hearings.startTime AS varchar))\r\n                                       FROM legal_case_hearings\r\n                                       WHERE startTime = (SELECT MAX(CAST(legal_case_hearings.startTime AS nvarchar))\r\n                                                          FROM legal_case_hearings\r\n                                                          WHERE legal_case_hearings.legal_case_id=legal_cases.id AND is_deleted = 0 \r\n                                                          AND startDate = (SELECT MAX(startDate) FROM legal_case_hearings where legal_case_hearings.legal_case_id=legal_cases.id AND is_deleted = 0)) and legal_case_hearings.legal_case_id=legal_cases.id\r\n                                                           and startDate = (SELECT MAX(startDate) FROM legal_case_hearings where legal_case_hearings.legal_case_id=legal_cases.id AND is_deleted = 0)\r\n                                       ) as last_hearing,\r\n                                      (SELECT top 1 (CAST(legal_case_hearings.judgment AS varchar(max)))\r\n                                        FROM legal_case_hearings\r\n                                        WHERE startTime = (SELECT MAX(CAST(legal_case_hearings.startTime AS nvarchar))\r\n                                                            FROM legal_case_hearings\r\n                                                            WHERE legal_case_hearings.legal_case_id=legal_cases.id AND is_deleted = 0 and legal_case_hearings.judged = 'yes' \r\n                                                            AND startDate = (SELECT MAX(startDate) FROM legal_case_hearings where legal_case_hearings.legal_case_id=legal_cases.id AND is_deleted = 0 and legal_case_hearings.judged = 'yes')) and legal_case_hearings.legal_case_id=legal_cases.id and legal_case_hearings.judged = 'yes'\r\n                                                            and startDate = (SELECT MAX(startDate) FROM legal_case_hearings where legal_case_hearings.legal_case_id=legal_cases.id AND is_deleted = 0 and legal_case_hearings.judged = 'yes')\r\n                                      ) as judgment";
        }
        $query["join"] = [["legal_cases_companies lcccom", "lcccom.case_id = legal_cases.id", "left"], ["companies com", "lcccom.company_id = com.id", "left"], ["legal_cases_contacts lccre", "lccre.case_id = legal_cases.id and lccre.contactType = 'contact'", "left"], ["contacts conre", "conre.id = lccre.contact_id", "left"], ["legal_cases_contacts lccch", "lccch.case_id = legal_cases.id and lccch.contactType = 'contributor'", "left"], ["contacts conhe", "conhe.id = lccch.contact_id", "left"], ["workflow_status", "workflow_status.id = legal_cases.case_status_id", "left"], ["user_profiles up", "up.user_id = legal_cases.user_id", "left"], ["legal_case_effective_effort lcee", "lcee.legal_case_id = legal_cases.id", "left"], ["legal_case_litigation_details", "legal_case_litigation_details.id = legal_cases.stage", "left"], ["legal_case_litigation_external_references", "legal_case_litigation_external_references.stage = legal_case_litigation_details.id", "left"], ["contacts referredbycontact", "referredbycontact.id = legal_cases.referredBy", "left"], ["contacts requestedbycontact", "requestedbycontact.id = legal_cases.requestedBy", "left"], ["case_types", "case_types.id = legal_cases.case_type_id", "inner"], ["provider_groups", "provider_groups.id = legal_cases.provider_group_id", "inner"], ["legal_case_related_containers", "legal_case_related_containers.legal_case_id = legal_cases.id", "left"], ["legal_case_containers", "legal_case_containers.id = legal_case_related_containers.legal_case_container_id", "left"], ["clients", "clients.id = legal_cases.client_id", "left"], ["companies comp", "comp.id = clients.company_id", "left"], ["contacts cont", "cont.id = clients.contact_id", "left"], ["user_profiles created", "created.user_id = clients.createdBy", "left"], ["user_profiles modified", "modified.user_id = clients.modifiedBy", "left"], ["user_profiles created_users", "created_users.user_id = legal_cases.createdBy", "left"], ["user_profiles modified_users", "modified_users.user_id = legal_cases.modifiedBy", "left"], ["legal_case_stage_languages", "legal_case_stage_languages.legal_case_stage_id = legal_cases.legal_case_stage_id and legal_case_stage_languages.language_id = '" . $lang_id . "'", "left"], ["legal_case_client_positions", "legal_cases.legal_case_client_position_id = legal_case_client_positions.id", "left"], ["legal_case_client_position_languages", "legal_case_client_position_languages.language_id = '" . $lang_id . "' and legal_case_client_positions.id = legal_case_client_position_languages.legal_case_client_position_id", "left"], ["legal_case_success_probability_languages", "legal_case_success_probability_languages.legal_case_success_probability_id =\r\n                      legal_cases.legal_case_success_probability_id and\r\n                      legal_case_success_probability_languages.language_id = '" . $lang_id . "'", "left"], ["user_profiles", "user_profiles.user_id = legal_cases.user_id", "left"], ["legal_case_outsources lccompaniesExtLaw", "lccompaniesExtLaw.legal_case_id = legal_cases.id", "left"], ["companies companiesExtLaw", "companiesExtLaw.id = lccompaniesExtLaw.company_id", "left"], ["legal_case_outsource_contacts lccextlaw", "lccextlaw.legal_case_outsource_id = lccompaniesExtLaw.id", "left"], ["contacts conextlaw", "conextlaw.id = lccextlaw.contact_id", "left"], ["stage_statuses_languages", "stage_statuses_languages.status = legal_case_litigation_details.status and stage_statuses_languages.language_id = '" . $lang_id . "'", "left"], ["court_types", "court_types.id = legal_case_litigation_details.court_type_id", "left"], ["court_degrees", "court_degrees.id = legal_case_litigation_details.court_degree_id", "left"], ["court_regions", "court_regions.id = legal_case_litigation_details.court_region_id", "left"], ["courts", "courts.id = legal_case_litigation_details.court_id", "left"]];
        $query["where"][] = $this->get_matter_privacy_conditions($logged_user_id, $override_privacy);
        $query["group_by"] = ["legal_cases.id"];
        if (is_array($sortable) && !empty($sortable)) {
            foreach ($sortable as $_sort) {
                $query["order_by"][] = [$_sort["field"], $_sort["dir"]];
            }
        } else {
            $query["order_by"] = ["legal_cases.id desc"];
        }
        $paginationConf = [];
        if ($page_number != "") {
            $query["limit"] = [10000, ($page_number - 1) * 10000];
        }
        if ($limit = $this->ci->input->post("take", true)) {
            $query["limit"] = [$this->ci->input->post("take", true), $this->ci->input->post("skip", true)];
            $paginationConf = ["urlPrefix" => ""];
            $paginationConf["inPage"] = $this->ci->input->post("take", true);
        }
        $response["query"] = $query;
        if ($return_query) {
            if (isset($query["limit"])) {
                unset($query["limit"]);
            }
            return $query;
        }
        $response["data"] = $pagingOn && !empty($paginationConf) ? parent::paginate($query, $paginationConf) : parent::load_all($query);
        $response["stringQuery"] = $this->ci->db->last_query();
        if ($get_query_count) {
            $response["totalRows"] = $this->count_total_matching_rows($query);
        }
        return $response;
    }
    public function case_id_field_value()
    {
        return "legal_cases.id";
    }
    public function contactoutsourceto_field_value()
    {
        return "CASE when ((conextlaw.father) <> ' ') then ((conextlaw.firstName) + ' ' + (conextlaw.father) + ' ' +\r\n                                                                     (conextlaw.lastName))\r\n                               else ((conextlaw.firstName) + ' ' + (conextlaw.lastName)) END";
    }
    public function client_type_field_value()
    {
        return "CASE WHEN clients.company_id IS NULL THEN 'Person' ELSE 'Company' END";
    }
    public function client_name_field_value()
    {
        return "CASE when (clients.company_id) IS NULL then\r\n                    (CASE when ((cont.father) <> ' ')\r\n                        then(' ' + (cont.firstName) + ' ' + (cont.father) + ' ' + (cont.lastName))\r\n                            else (' ' + (cont.firstName) + ' ' + (cont.lastName)) END)\r\n                else (comp.name) END";
    }
    public function client_foreign_name_field_value()
    {
        return "CASE when (clients.company_id) IS NULL then (isnull(cont.foreignFirstName, '')) + ' ' + (isnull(cont.foreignLastName, '')) else (comp.foreignName) END";
    }
    public function contact_field_value()
    {
        return "CASE when ((conre.father) <> ' ') then (' ' + (conre.firstName) + ' ' + (conre.father) + ' ' +\r\n                                                                     (conre.lastName))\r\n                               else (' ' + (conre.firstName) + ' ' + (conre.lastName)) END";
    }
    public function contact_contributor_field_value()
    {
        return "CASE when ((conhe.father) <> ' ') then (' ' + (conhe.firstName) + ' ' +\r\n                                                                 (conhe.father) + ' ' + (conhe.lastName))\r\n                       else (' ' + (conhe.firstName) + ' ' + (conhe.lastName)) END";
    }
    public function contact_outsource_to_field_value()
    {
        return "CASE when ((conextlaw.father) <> ' ') then (' ' + (conextlaw.firstName) + ' ' +\r\n                                                                         (conextlaw.father) + ' ' + (conextlaw.lastName))\r\n                               else (' ' + (conextlaw.firstName) + ' ' + (conextlaw.lastName)) END";
    }
    public function referred_by_name_field_value()
    {
        return "CASE when ((referredbycontact.father) <> ' ') then (' ' + (referredbycontact.firstName) + ' ' +\r\n                                                                         (referredbycontact.father) + ' ' + (referredbycontact.lastName))\r\n                               else (' ' + (referredbycontact.firstName) + ' ' + (referredbycontact.lastName)) END";
    }
    public function requested_by_name_field_value()
    {
        return "CASE when ((requestedbycontact.father) <> ' ') then (' ' + (requestedbycontact.firstName) + ' ' +\r\n                                                                 (requestedbycontact.father) + ' ' + (requestedbycontact.lastName))\r\n                       else (' ' + (requestedbycontact.firstName) + ' ' + (requestedbycontact.lastName)) END";
    }
    public function assignee_field_value()
    {
        return "((up.firstName) + ' ' + (up.lastName))";
    }
    public function client_id_field_value()
    {
        return "CASE WHEN clients.company_id IS NULL THEN cont.id ELSE comp.id END";
    }
    public function client_type_con_comp_field_value()
    {
        return "CASE WHEN clients.company_id IS NULL THEN 'contact' ELSE 'company' END";
    }
    public function assignee_user_id_field_value()
    {
        return "up.user_id";
    }
    public function is_cp_field_value()
    {
        return "(case when (legal_cases.channel = 'CP') then 'yes' else 'no' end)";
    }
    public function opponent_nationalities_field_value()
    {
        return "STUFF((SELECT ', ' + (CASE WHEN legal_case_opponents.opponent_member_type = 'company'\r\n                                                 THEN (CASE\r\n                                                           WHEN opponentcompanynationalities.name IS NOT NULL\r\n                                                               THEN (opponentCompany.name + '(' +\r\n                                                                     opponentcompanynationalities.name +\r\n                                                                     ')')\r\n                                                           ELSE (opponentCompany.name + '( - )') END)\r\n                                             ELSE (CASE\r\n                                                       WHEN opponentContactNationalitiesCountry.name IS NOT NULL\r\n                                                           THEN (opponentContact.firstName + ' ' +\r\n                                                                 opponentContact.lastName + '(' +\r\n                                                                 opponentContactNationalitiesCountry.name +\r\n                                                                 ')')\r\n                                                       ELSE (opponentContact.firstName + ' ' + opponentContact.lastName + '( - )') END) END)\r\n                          FROM legal_case_opponents\r\n                                   INNER JOIN opponents ON opponents.id = legal_case_opponents.opponent_id\r\n                                   LEFT JOIN companies AS opponentCompany\r\n                                             ON opponentCompany.id = opponents.company_id AND\r\n                                                legal_case_opponents.opponent_member_type = 'company'\r\n                                   LEFT JOIN contacts AS opponentContact\r\n                                             ON opponentContact.id = opponents.contact_id AND\r\n                                                legal_case_opponents.opponent_member_type = 'contact'\r\n                                   LEFT JOIN countries_languages AS opponentCompanyNationalities\r\n                                             ON opponentCompanyNationalities.country_id = opponentCompany.nationality_id AND opponentCompanyNationalities.language_id = " . $this->get_lang_id() . " AND \r\n                                                legal_case_opponents.opponent_member_type = 'company'\r\n                                   LEFT JOIN contact_nationalities_details AS opponentContactNationalities\r\n                                             ON opponentContactNationalities.contact_id = opponentContact.id AND\r\n                                                legal_case_opponents.opponent_member_type = 'contact'\r\n                                    LEFT JOIN countries_languages AS opponentContactNationalitiesCountry ON opponentContactNationalitiesCountry.country_id = opponentContactNationalities.nationality_id AND opponentContactNationalitiesCountry.language_id = " . $this->get_lang_id() . "\r\n                          WHERE legal_case_opponents.case_id = legal_cases.id FOR XML PATH ('')), 1,\r\n                         1, '')";
    }
    public function opponent_names_field_value()
    {
        return "STUFF((SELECT ', ' + (CASE WHEN opponent_positions.name IS NOT NULL THEN CASE\r\n                                                                                               WHEN legal_case_opponents.opponent_member_type = 'company'\r\n                                                                                                   THEN (opponentCompany.name + ' - ' + opponent_positions.name)\r\n                                                                                               ELSE opponentContact.firstName +\r\n                                                                                                    ' ' +\r\n                                                                                                    opponentContact.lastName +\r\n                                                                                                    ' - ' +\r\n                                                                                                    opponent_positions.name END\r\n                                             ELSE CASE\r\n                                                      WHEN legal_case_opponents.opponent_member_type = 'company'\r\n                                                          THEN opponentCompany.name\r\n                                                      ELSE opponentContact.firstName + ' ' + opponentContact.lastName END END)\r\n                          FROM legal_case_opponents\r\n                                   INNER JOIN opponents ON opponents.id = legal_case_opponents.opponent_id\r\n                                   LEFT JOIN companies AS opponentCompany\r\n                                             ON opponentCompany.id = opponents.company_id AND\r\n                                                legal_case_opponents.opponent_member_type = 'company'\r\n                                   LEFT JOIN contacts AS opponentContact\r\n                                             ON opponentContact.id = opponents.contact_id AND\r\n                                                legal_case_opponents.opponent_member_type = 'contact'\r\n                                   LEFT JOIN legal_case_opponent_position_languages AS opponent_positions\r\n                                             ON opponent_positions.legal_case_opponent_position_id =\r\n                                                legal_case_opponents.opponent_position and\r\n                                                opponent_positions.language_id = '1'\r\n                          WHERE legal_case_opponents.case_id = legal_cases.id FOR XML PATH ('')), 1,\r\n                         1, '')";
    }
    public function last_hearing_field_value()
    {
        return "(SELECT DISTINCT (CAST(legal_case_hearings.startDate AS varchar))\r\n                   FROM legal_case_hearings\r\n                   WHERE startTime = (SELECT MAX(CAST(legal_case_hearings.startTime AS nvarchar))\r\n                                      FROM legal_case_hearings\r\n                                      WHERE legal_case_hearings.legal_case_id=legal_cases.id AND is_deleted = 0 \r\n                                      AND startDate = (SELECT MAX(startDate) FROM legal_case_hearings where legal_case_hearings.legal_case_id=legal_cases.id AND is_deleted = 0)) and legal_case_hearings.legal_case_id=legal_cases.id\r\n                                      and startDate = (SELECT MAX(startDate) FROM legal_case_hearings where legal_case_hearings.legal_case_id=legal_cases.id AND is_deleted = 0)\r\n                 )";
    }
    public function reasons_of_postponement_of_last_hearing_field_value()
    {
        return "(SELECT top 1 reasons_of_postponement\r\n                   FROM legal_case_hearings\r\n                   WHERE startTime = (SELECT MAX(CAST(legal_case_hearings.startTime AS nvarchar))\r\n                                      FROM legal_case_hearings\r\n                                      WHERE legal_case_hearings.legal_case_id=legal_cases.id AND is_deleted = 0 \r\n                                      AND startDate = (SELECT MAX(startDate) FROM legal_case_hearings where legal_case_hearings.legal_case_id=legal_cases.id AND is_deleted = 0)) and legal_case_hearings.legal_case_id=legal_cases.id\r\n                                      and startDate = (SELECT MAX(startDate) FROM legal_case_hearings where legal_case_hearings.legal_case_id=legal_cases.id AND is_deleted = 0)\r\n                 )";
    }
    public function judgment_field_value()
    {
        return "(SELECT top 1 (CAST(legal_case_hearings.judgment AS varchar(max)))\r\n                    FROM legal_case_hearings\r\n                    WHERE startTime = (SELECT MAX(CAST(legal_case_hearings.startTime AS nvarchar))\r\n                                        FROM legal_case_hearings\r\n                                        WHERE legal_case_hearings.legal_case_id=legal_cases.id AND is_deleted = 0 \r\n                                        AND startDate = (SELECT MAX(startDate) FROM legal_case_hearings where legal_case_hearings.legal_case_id=legal_cases.id AND is_deleted = 0)) and legal_case_hearings.legal_case_id=legal_cases.id \r\n                                        and startDate = (SELECT MAX(startDate) FROM legal_case_hearings where legal_case_hearings.legal_case_id=legal_cases.id AND is_deleted = 0)\r\n                )";
    }
    public function modified_by_name_field_value()
    {
        return "((modified_users.firstName) + ' ' + (modified_users.lastName))";
    }
    public function created_by_name_field_value()
    {
        return "((created_users.firstName) + ' ' + (created_users.lastName))";
    }
    public function get_legal_cases_order_by_fields($fields, $is_group_by)
    {
        $result = [];
        foreach ($fields as $field) {
            array_push($result, ["field" => $is_group_by ? "Max(" . $field["field"] . ")" : $field["field"], "dir" => $field["dir"]]);
        }
        return $result;
    }
    public function litigation_external_references_field_value()
    {
        return "(STUFF((SELECT ',' + legal_case_litigation_external_references.number\r\n                                                               FROM legal_case_litigation_external_references\r\n                                                                   left join legal_case_litigation_details ON legal_case_litigation_external_references.stage = legal_case_litigation_details.id\r\n                                                               where legal_case_litigation_details.legal_case_id = legal_cases.id\r\n                                                                   FOR XML PATH('')), 1, 1, ''))";
    }
    public function get_count_by_a_b_numbers($stringQuery, $query, $grouping, $table)
    {
        if (!(isset($grouping[0]) && isset($grouping[1])) && isset($grouping[0]["field"]) && isset($grouping[1]["field"])) {
            return [];
        }
        if (isset($query["limit"])) {
            unset($query["limit"]);
        }
        $_table = $this->_table;
        $this->_table = $table;
        $a = mb_substr($grouping[0]["field"], 4);
        $b = mb_substr($grouping[1]["field"], 4);
        $a = rtrim($a, ")");
        $b = rtrim($b, ")");
        if (isset($query["select"])) {
            unset($query["select"]);
        }
        if (isset($query["group_by"])) {
            unset($query["group_by"]);
        }
        if (isset($query["order_by"])) {
            unset($query["order_by"]);
        }
        $query["select"] = [$a . " as a," . $b . " as b,COUNT(0) AS totalCount", false];
        $query["group_by"] = $a . " , " . $b;
        $query["order_by"] = $a . " , " . $b;
        $queryResult = $this->load_all($query);
        $results = [];
        foreach ($queryResult as $row) {
            $results[1 * $row["a"]][$row["b"]] = $row["totalCount"];
        }
        foreach ($results as $k => $v) {
            $results[1 * $k]["total"] = array_sum($v);
        }
        $this->_table = $_table;
        return $results;
    }
    public function k_load_all_outsource($legal_case_id)
    {
        $query = "\r\n            (\r\n                SELECT \r\n                    legal_cases_companies.id AS id, \r\n                    legal_cases_companies.comments as comments, \r\n                    companies.name as outsource_name, \r\n                    CASE \r\n                        WHEN legal_case_company_role_id IS NULL \r\n                        THEN 0 \r\n                        ELSE legal_case_company_role_id \r\n                    END AS role_id, \r\n                    legal_case_company_roles.name AS role_name, \r\n                    companies.id AS outsource_id, \r\n                    'company' AS outsource_type,\r\n                    legal_cases_companies.case_id AS case_id\r\n                FROM\r\n                    legal_cases_companies\r\n                LEFT JOIN companies ON\r\n                    companies.id = legal_cases_companies.company_id\r\n                LEFT JOIN legal_case_company_roles ON\r\n                    legal_case_company_roles.id = legal_cases_companies.legal_case_company_role_id\r\n                WHERE\r\n                    companies.status = 'Active' AND\r\n                    legal_cases_companies.companyType = 'external lawyer' AND\r\n                    legal_cases_companies.case_id = ?\r\n            )\r\n            \r\n            UNION\r\n\r\n            (\r\n                SELECT\r\n                    legal_cases_contacts.id AS id, \r\n                    legal_cases_contacts.comments as comments, \r\n                    CASE \r\n                        WHEN contacts.father!='' \r\n                        THEN contacts.firstName + ' '+ contacts.father + ' ' + contacts.lastName \r\n                        ELSE contacts.firstName+' '+contacts.lastName \r\n                    END AS outsource_name, \r\n                    CASE \r\n                        WHEN legal_case_contact_role_id IS NULL \r\n                        THEN 0 \r\n                        ELSE legal_case_contact_role_id \r\n                    END AS role_id, \r\n                    legal_case_contact_roles.name AS role_name, \r\n                    contacts.id AS outsource_id,\r\n                    'contact' AS outsource_type,\r\n                    legal_cases_contacts.case_id AS case_id\r\n                FROM\r\n                    legal_cases_contacts\r\n                LEFT JOIN contacts ON\r\n                    contacts.id = legal_cases_contacts.contact_id\r\n                LEFT JOIN legal_case_contact_roles ON\r\n                    legal_case_contact_roles.id = legal_cases_contacts.legal_case_contact_role_id\r\n                WHERE\r\n                    legal_cases_contacts.contactType = 'external lawyer' AND\r\n                    legal_cases_contacts.case_id = ?\r\n            )\r\n            \r\n            ORDER BY\r\n                id DESC\r\n        ";
        $query_execution = $this->ci->db->query($query, [$legal_case_id, $legal_case_id]);
        $result["data"] = $query_execution->result_array();
        $result["totalRows"] = $query_execution->num_rows();
        return $result;
    }
    public function get_case_client($case_id)
    {
        $query["select"] = ["legal_cases.client_id, CASE WHEN clients.company_id IS NULL THEN (CASE WHEN cont.father !='' THEN cont.firstName + ' ' + cont.father + ' ' + cont.lastName ELSE cont.firstName + ' ' + cont.lastName END) ELSE comp.name END AS clientName,CASE WHEN clients.company_id IS NULL THEN cont.foreignFirstName + ' ' + cont.foreignLastName ELSE comp.foreignName END AS clientForeignName", false];
        $query["join"] = [["clients", "clients.id = legal_cases.client_id", "left"], ["companies comp", "comp.id = clients.company_id", "left"], ["contacts cont", "cont.id = clients.contact_id", "left"]];
        $query["where"] = ["legal_cases.id", $case_id];
        $result = $this->load($query);
        return $result;
    }
    public function opponent_foreign_name_field_value()
    {
        return "STUFF((SELECT ', ' +\r\n                (\r\n                    CASE WHEN opponent_positions.name != '' THEN\r\n                    (\r\n                        CASE WHEN legal_case_opponents.opponent_member_type = 'company'\r\n                        THEN (CASE WHEN opponentCompany.foreignName IS NULL THEN opponentCompany.name ELSE opponentCompany.foreignName END) + ' - ' + opponent_positions.name\r\n                        ELSE (CASE WHEN opponentContact.foreignFirstName IS NULL THEN opponentContact.firstName ELSE opponentContact.foreignFirstName END) + ' ' + (CASE WHEN opponentContact.foreignLastName IS NULL THEN opponentContact.lastName ELSE opponentContact.foreignLastName END) + ' - ' + opponent_positions.name END\r\n                    )\r\n                    ELSE\r\n                    (\r\n                        CASE WHEN legal_case_opponents.opponent_member_type = 'company'\r\n                        THEN (CASE WHEN opponentCompany.foreignName IS NULL THEN opponentCompany.name ELSE opponentCompany.foreignName END)\r\n                        ELSE (CASE WHEN opponentContact.foreignFirstName IS NULL THEN opponentContact.firstName ELSE opponentContact.foreignFirstName END) + ' ' + (CASE WHEN opponentContact.foreignLastName IS NULL THEN opponentContact.lastName ELSE opponentContact.foreignLastName END) END\r\n                    )\r\n                    END\r\n                )\r\n                 FROM legal_case_opponents\r\n                 INNER JOIN opponents ON opponents.id = legal_case_opponents.opponent_id\r\n                 LEFT JOIN companies AS opponentCompany ON opponentCompany.id = opponents.company_id AND legal_case_opponents.opponent_member_type = 'company'\r\n                 LEFT JOIN contacts AS opponentContact ON opponentContact.id = opponents.contact_id AND legal_case_opponents.opponent_member_type = 'contact'\r\n                 LEFT JOIN legal_case_opponent_position_languages AS opponent_positions ON opponent_positions.legal_case_opponent_position_id = legal_case_opponents.opponent_position and opponent_positions.language_id = '1'\r\n                 WHERE legal_case_opponents.case_id = legal_cases.id FOR XML PATH ('')), 1, 1, '')";
    }
    public function dashboard_recent_cases($category, $api_params = [])
    {
        $logged_user_id = $api_params["user_id"] ?? $this->logged_user_id;
        $this->ci->load->model("user_preference");
        $this->ci->load->model("language");
        $lang_id = $this->ci->language->get_id_by_session_lang();
        $recent_cases = unserialize($this->ci->user_preference->get_value_by_user("recent_cases", $logged_user_id));
        $response = [];
        if (isset($recent_cases[$category])) {
            $recent_cases = $recent_cases[$category];
            $order_by = "CASE legal_cases.id";
            foreach ($recent_cases as $key => $val) {
                if ($val == 0) {
                    unset($recent_cases[$key]);
                } else {
                    $order_by .= " when '" . $val . "' then " . $key;
                }
            }
            $order_by .= " end";
            $str_recent_cases = implode(",", array_map("intval", $recent_cases));
            $query["select"] = ["legal_cases.id, ( '" . $this->modelCode . "' +  CAST( legal_cases.id AS nvarchar ) ) as case_id, legal_cases.subject, legal_cases.internalReference, legal_cases.description, workflow_status.name as status, ( user_profiles.firstName + ' ' + user_profiles.lastName ) as Assignee, workflow_status.category as status_category, legal_case_stage_languages.name as stage_name, case_types.name as practice_area, clients.name as client_name, legal_case_client_position_languages.name AS client_position, legal_cases.createdOn, legal_cases.modifiedOn, '" . $category . "' AS module", false];
            $query["join"] = [["user_profiles", "user_profiles.user_id = legal_cases.user_id", "left"], ["workflow_status", "workflow_status.id = legal_cases.case_status_id", "left"], ["legal_case_stage_languages", "legal_case_stage_languages.legal_case_stage_id = legal_cases.legal_case_stage_id and legal_case_stage_languages.language_id = '" . $lang_id . "'", "left"], ["case_types", "case_types.id = legal_cases.case_type_id", "left"], ["clients_view clients", "clients.id = legal_cases.client_id and clients.model = 'clients'", "left"], ["legal_case_client_position_languages", "legal_case_client_position_languages.legal_case_client_position_id = legal_cases.legal_case_client_position_id and legal_case_client_position_languages.language_id = '" . $lang_id . "'", "left"]];
            $query["where"][] = ["legal_cases.id IN (" . $str_recent_cases . ") AND legal_cases.isDeleted = '0'", NULL, false];
            $query["where"][] = ["legal_cases.category", $category == "corporate_matters" ? "Matter" : "Litigation"];
            $query["where"][] = ["legal_cases.archived", $this->notArchived];
            $query["order_by"] = [$order_by];
            $response = $this->load_all($query);
        }
        return $response;
    }
    public function k_load_all_legal_case_time_tracking($id, $sortable, $filter = [], $organization_id = 0, $only_log_rate = 0, $my_time_logs = false)
    {
        $query = [];
        $response = [];
        $this->ci->load->model("language");
        $lang_id = $this->ci->language->get_id_by_session_lang();
        $system_preferences = $this->ci->session->userdata("systemPreferences");
        $user_rate_per_entities = unserialize($system_preferences["userRatePerHour"]);
        $this->ci->load->model("organization", "organizationfactory");
        $this->ci->organization = $this->ci->organizationfactory->get_instance();
        $organizations = $this->ci->organization->load_list();
        ksort($organizations);
        $organization_id = 0 < $organization_id ? $organization_id : ($this->ci->user_preference->get_value("organization") ? $this->ci->user_preference->get_value("organization") : current(array_keys($organizations)));
        $user_rate_per_hour = isset($user_rate_per_entities[$organization_id]) && $user_rate_per_entities[$organization_id] ? $user_rate_per_entities[$organization_id] : 0;
        $table = $this->_table;
        $this->_table = "user_activity_logs_full_details as ual";
        $query["select"][] = ["ual.id, ual.user_id, task_id, legal_case_id, effectiveEffort, ual.comments, createdBy, createdOn, taskId, \r\n        taskSummary, task_title, legalCaseId, legalCaseSummary, worker, inserter, billingStatus, \r\n        logDate,time_types.name as timeTypeName,time_internal_statuses_languages.name as timeInternalStatusName,timeStatus, ual.clientId, ual.clientName,  ual.allRecordsClientName, ual.timeTypeId, ual.timeInternalStatusId, ual.rate_system", false];
        if (empty($organization_id)) {
            $this->ci->load->model("organization", "organizationfactory");
            $this->ci->organization = $this->ci->organizationfactory->get_instance();
            $organizations = $this->ci->organization->load_list();
            ksort($organizations);
            $organization_id = $this->ci->user_preference->get_value("organization") ? $this->ci->user_preference->get_value("organization") : current(array_keys($organizations));
        }
        if (!$only_log_rate) {
            $query["select"][] = ["CASE WHEN ual.rate is NULL THEN (CASE WHEN urphpc.ratePerHour IS NULL THEN (CASE WHEN cs.rate_per_hour IS NULL THEN (CASE WHEN urph.ratePerHour IS NULL THEN " . $user_rate_per_hour . " \r\n            ELSE  urph.ratePerHour END) ELSE cs.rate_per_hour END) ELSE urphpc.ratePerHour END) ELSE ual.rate END AS ratePerHour", false];
        } else {
            $query["select"][] = ["(CASE WHEN ual.rate is NULL THEN 0 ELSE ual.rate END) as ratePerHour", false];
            $query["select"][] = ["CASE WHEN urphpc.ratePerHour IS NULL THEN (CASE WHEN cs.rate_per_hour IS NULL THEN (CASE WHEN urph.ratePerHour IS NULL THEN " . $user_rate_per_hour . " \r\n            ELSE  urph.ratePerHour END) ELSE cs.rate_per_hour END) ELSE urphpc.ratePerHour END AS entityRatePerHour", false];
        }
        $query["join"] = [["time_types_languages as time_types", "time_types.type = ual.timeTypeId AND time_types.language_id = " . $lang_id, "left"], ["time_internal_statuses_languages", "time_internal_statuses_languages.internal_status = ual.timeInternalStatusId AND time_internal_statuses_languages.language_id = " . $lang_id, "left"], ["user_rate_per_hour urph", "urph.user_id = ual.user_id AND urph.organization_id = " . $organization_id, "left"], ["case_rate cs", "ual.legal_case_id = cs.case_id AND cs.organization_id = " . $organization_id, "left"], ["user_rate_per_hour_per_case urphpc", "urphpc.user_id = ual.user_id and urphpc.case_id = ual.legal_case_id AND urphpc.organization_id = " . $organization_id, "left"]];
        $query["where"][] = ["(ual.legal_case_id = " . $id . " OR ual.task_id IN (Select tasks.id from tasks where tasks.legal_case_id =" . $id . "))"];
        if ($my_time_logs) {
            $query["where"][] = ["ual.user_id", $this->ci->session->userdata("AUTH_user_id")];
        }
        if (!empty($filter) && is_array($filter) && isset($filter["filters"])) {
            foreach ($filter["filters"] as $_filter) {
                $this->prep_k_filter($_filter, $query, $filter["logic"]);
            }
            unset($_filter);
        }
        $this->prep_query($query);
        $response["totalRows"] = $this->ci->db->count_all_results($this->_table);
        if (is_array($sortable) && !empty($sortable)) {
            foreach ($sortable as $_sort) {
                $query["order_by"][] = [$_sort["field"], $_sort["dir"]];
            }
        } else {
            $query["order_by"] = ["ual.logDate desc"];
        }
        if ($limit = $this->ci->input->post("take", true)) {
            $query["limit"] = [$this->ci->input->post("take", true), $this->ci->input->post("skip", true)];
        }
        $response["data"] = $this->load_all($query);
        $this->_table = $table;
        return $response;
    }
    public function get_Total_effective_effort_cost($id, $organization_id, $filter = [], $get_all_rows = false, $my_time_logs = false)
    {
        $query = [];
        $response = [];
        $system_preferences = $this->ci->session->userdata("systemPreferences");
        $user_rate_per_entities = unserialize($system_preferences["userRatePerHour"]);
        $user_rate_per_hour = isset($user_rate_per_entities[$organization_id]) && $user_rate_per_entities[$organization_id] ? $user_rate_per_entities[$organization_id] : 0;
        $user_rate_per_hour = number_format((double) $user_rate_per_hour, 2, ".", "");
        $table = $this->_table;
        $this->_table = "user_activity_logs_full_details as ual";
        if (!$get_all_rows) {
            $query["select"][] = ["SUM(effectiveEffort) as totalEffectiveEffort", false];
            $query["select"][] = ["SUM(CASE WHEN timeStatus = 'internal' THEN (effectiveEffort) ELSE 0 END) as totalNoneBillableEffort", false];
            $query["select"][] = ["SUM(CASE WHEN timeStatus = 'internal' THEN 0 ELSE (effectiveEffort) END) as totalBillableEffort", false];
            $query["select"][] = ["SUM(CASE WHEN ual.timeStatus = 'billable' THEN\r\n                                        ((CASE\r\n                                            WHEN ual.rate is NULL THEN (CASE\r\n                                                                            WHEN urphpc.ratePerHour IS NULL THEN (CASE\r\n                                                                                                                      WHEN cs.rate_per_hour IS NULL\r\n                                                                                                                          THEN (CASE WHEN urph.ratePerHour IS NULL THEN " . $user_rate_per_hour . " ELSE urph.ratePerHour END)\r\n                                                                                                                      ELSE cs.rate_per_hour END)\r\n                                                                            ELSE urphpc.ratePerHour END)\r\n                                            ELSE ual.rate END) * effectiveEffort) ELSE 0 END) as totalCost", false];
        } else {
            $query["select"][] = ["ual.*, (CASE\r\n                                                WHEN ual.rate IS NULL THEN (CASE\r\n                                                                                WHEN urphpc.ratePerHour IS NULL THEN (CASE\r\n                                                                                                                          WHEN cs.rate_per_hour IS NULL\r\n                                                                                                                              THEN (CASE WHEN urph.ratePerHour IS NULL THEN " . $user_rate_per_hour . " ELSE urph.ratePerHour END)\r\n                                                                                                                          ELSE cs.rate_per_hour END)\r\n                                                                                ELSE urphpc.ratePerHour END)\r\n                                                ELSE\r\n                                                    ual.rate\r\n                                               END) as ratePerHour", false];
        }
        $query["join"] = [["user_rate_per_hour urph", "urph.user_id = ual.user_id AND urph.organization_id = " . $organization_id, "left"], ["case_rate cs", "ual.legal_case_id = cs.case_id AND cs.organization_id = " . $organization_id, "left"], ["user_rate_per_hour_per_case urphpc", "urphpc.user_id = ual.user_id and urphpc.case_id = ual.legal_case_id AND urphpc.organization_id = " . $organization_id, "left"]];
        $query["where"][] = ["(ual.legal_case_id = " . $id . " OR ual.task_id IN (Select tasks.id from tasks where tasks.legal_case_id =" . $id . "))"];
        if (!empty($filter) && is_array($filter) && isset($filter["filters"])) {
            foreach ($filter["filters"] as $_filter) {
                $this->prep_k_filter($_filter, $query, $filter["logic"]);
            }
            unset($_filter);
        }
        if ($my_time_logs) {
            $query["where"][] = ["ual.user_id", $this->ci->session->userdata("AUTH_user_id")];
        }
        $response["data"] = $this->load_all($query);
        $this->_table = $table;
        return $response;
    }
    public function load_task_users($case_id)
    {
        $table = $this->_table;
        $this->_table = "legal_case_users";
        $query["select"] = ["UP.user_id as id, CONCAT( UP.firstName, ' ', UP.lastName ) as name,UP.status as status", false];
        $query["join"] = ["user_profiles UP", "UP.user_id = legal_case_users.user_id", "inner"];
        $query["where"] = ["legal_case_users.legal_case_id", $case_id];
        $response = $this->load_all($query);
        $this->_table = $table;
        return $response;
    }
    public function get_cases_names_by_ids($ids)
    {
        $query = [];
        $query["select"] = ["('M' + cast(legal_cases.id as varchar(11))) + ':' + subject AS name", false];
        $query["where_in"] = ["legal_cases.id", $ids];
        return $this->load_all($query);
    }
}

