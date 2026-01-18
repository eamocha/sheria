<?php

if (!defined("BASEPATH")) {
    exit("No direct script access allowed");
}
class Legal_case_hearing extends My_Model_Factory
{
}
class mysql_Legal_case_hearing extends My_Model
{
    protected $modelName = "legal_case_hearing";
    protected $modelCode = "H";
    protected $_table = "legal_case_hearings";
    protected $_listFieldName = "legal_case_id";
    protected $_fieldsNames = ["id", "legal_case_id", "task_id", "startDate", "startTime", "postponedDate", "postponedTime", "summary", "summaryToClient", "is_deleted", "type", "judgment", "stage", "judged", "comments", "reasons_of_postponement", "createdOn", "createdBy", "modifiedOn", "modifiedBy", "verifiedSummary", "reason_of_win_or_lose", "hearing_outcome", "clientReportEmailSent"];
    protected $allowedNulls = ["task_id", "startDate", "summary", "summaryToClient", "verifiedSummary", "postponedDate", "postponedTime", "stage", "type", "judgment", "judged", "comments", "reasons_of_postponement", "reason_of_win_or_lose", "hearing_outcome", "clientReportEmailSent"];
    protected $case_lookup_inputs_validation = [["input_name" => "related_case", "error_field" => "legal_case_id", "message" => ["main_var" => "not_exists2", "lookup_for" => "case"]]];
    protected $assignees_lookup_inputs_validation = [["input_name" => "lookupHearingLawyers", "error_field" => "Hearing_Lawyers[]", "message" => ["main_var" => "not_exists", "lookup_for" => "user"]]];
    protected $judgedValues = ["", "yes", "no"];
    protected $hearingOutcomeValues = [NULL, "won", "lost"];
    protected $pendingUpdatesBusinessRules = ["summary" => "legal_case_hearings.summary", "reasons_of_postponement" => "legal_case_hearings.reasons_of_postponement", "postponed_until" => "legal_case_hearings.postponedDate"];
    protected $builtInLogs = true;
    public function __construct()
    {
        parent::__construct();
        $this->validate = ["legal_case_id" => ["required" => true, "allowEmpty" => false, "rule" => "numeric", "message" => $this->ci->lang->line("cannot_be_blank_rule")], "task_id" => ["required" => false, "allowEmpty" => true, "rule" => "numeric", "message" => sprintf($this->ci->lang->line("is_numeric_rule"), $this->ci->lang->line("task"))], "startDate" => ["required" => true, "allowEmpty" => false, "rule" => "date", "message" => sprintf($this->ci->lang->line("date_rule"), $this->ci->lang->line("start_date"))], "startTime" => ["required" => true, "allowEmpty" => false, "rule" => "time", "message" => sprintf($this->ci->lang->line("time_rule"), $this->ci->lang->line("start_time"))], "postponedDate" => ["required" => false, "allowEmpty" => true, "rule" => "date", "message" => sprintf($this->ci->lang->line("date_rule"), $this->ci->lang->line("postponed_until"))], "postponedTime" => ["required" => false, "allowEmpty" => true, "rule" => "time", "message" => sprintf($this->ci->lang->line("time_rule"), $this->ci->lang->line("postponed_time"))], "summary" => ["required" => false, "allowEmpty" => true, "rule" => ["minLength", 1], "message" => sprintf($this->ci->lang->line("min_length_rule"), $this->ci->lang->line("summary_by_lawyer"), 1)], "summaryToClient" => ["required" => false, "allowEmpty" => true, "rule" => ["minLength", 1], "message" => sprintf($this->ci->lang->line("min_length_rule"), $this->ci->lang->line("summary_to_client"), 1)], "judgment" => ["required" => false, "allowEmpty" => true, "rule" => ["minLength", 1], "message" => sprintf($this->ci->lang->line("min_length_rule"), $this->ci->lang->line("judgment"), 1)], "type" => ["required" => false, "allowEmpty" => true, "rule" => "numeric", "message" => sprintf($this->ci->lang->line("is_numeric_rule"), $this->ci->lang->line("type"))]];
        $this->logged_user_id = $this->ci->is_auth->get_user_id();
    }
    public function getBasicList($case_id = null)
    {
        $this->_table = "legal_case_hearings";

        // Select fields with join to hearing_types_languages
        $query["select"] = [
            "legal_case_hearings.id",
            "legal_case_hearings.hearingID",
            "legal_case_hearings.legal_case_id",
            "legal_case_hearings.createdOn",
            "legal_case_hearings.startDate",
            "legal_case_hearings.startTime",
            "legal_case_hearings.postponedDate",
            "legal_case_hearings.postponedTime",
            "hearing_types_languages.name as type_name" // From joined table
        ];

        // Add join condition
        $query["join"] = [
            [
                "hearing_types_languages",
                "hearing_types_languages.language_id = legal_case_hearings.type AND hearing_types_languages.type = 1",
                "left"
            ]
        ];

        // Filter by case ID if provided
        if ($case_id) {
            $query["where"] = [["legal_case_hearings.legal_case_id", $case_id]];
        }

        // Order by start date (newest first)
        $query["order_by"] = ["legal_case_hearings.startDate desc"];

        // Execute query

        $response["data"] = $this->load_all($query);

        return $response;

    }

    public function k_load_all_hearings($filter, $sortable, $legalCaseID = 0, $page_number = "", $hijri_calendar_enabled = false, $language = false)
    {
        $language = $this->ci->session->userdata("AUTH_language") ? strtolower(substr($this->ci->session->userdata("AUTH_language"), 0, 2)) : $language;
        $table = $this->_table;
        $response = [];
        $this->_table = "mv_hearings as legal_case_hearings";
        $query["select"] = ["legal_case_hearings.id, legal_case_hearings.hearingID, legal_case_hearings.summaryToClient, legal_case_hearings.legal_case_id, legal_case_hearings.caseReference, legal_case_hearings.task_id, legal_case_hearings.caseValue, legal_case_hearings.statusComments, legal_case_hearings.latest_development, legal_case_hearings.caseArrivalDate, legal_case_hearings.closedOn, legal_case_hearings.court_type_id, legal_case_hearings.court_degree_id, legal_case_hearings.court_region_id, legal_case_hearings.court_id, legal_case_hearings.startDate, legal_case_hearings.startTime, legal_case_hearings.postponedDate, legal_case_hearings.postponedTime, legal_case_hearings.type, legal_case_hearings.reasons_of_postponement, legal_case_hearings.summary, legal_case_hearings.comments, legal_case_hearings.caseID, legal_case_hearings.judged, legal_case_hearings.judgment, legal_case_hearings.stage_status, legal_case_hearings.clients, legal_case_hearings.client_foreign_name, legal_case_hearings.courtType, legal_case_hearings.courtDegree, legal_case_hearings.courtRegion, legal_case_hearings.court, legal_case_hearings.clientPosition_" . $language . " as clientPosition, legal_case_hearings.sentenceDate, legal_case_hearings.stage as stage_id, legal_case_hearings.legal_case_stage_name_" . $language . " as stage_name, legal_case_hearings.legal_case_stage_id, legal_case_hearings.areaOfPractice, legal_case_hearings.area_of_practice, legal_case_hearings.containerID, legal_case_hearings.createdOn, legal_case_hearings.modifiedOn, legal_case_hearings.createdBy, legal_case_hearings.modifiedBy, legal_case_hearings.previousHearingDate, legal_case_hearings.caseSubject, legal_case_hearings.fullCaseSubject, legal_case_hearings.type_name_" . $language . " as type_name, legal_case_hearings.reference, legal_case_hearings.opponentLawyers, legal_case_hearings.lawyers, legal_case_hearings.opponent_foreign_name_" . $language . " as opponent_foreign_name, legal_case_hearings.opponents_" . $language . " as opponents, legal_case_hearings.judges, legal_case_hearings.createdByName, legal_case_hearings.clientReportEmailSent, legal_case_hearings.verifiedSummary", false];
        $query["join"] = [["legal_case_hearings_users", "legal_case_hearings_users.legal_case_hearing_id = legal_case_hearings.id", "left"]];
        $query["group_by"] = ["legal_case_hearings.id"];
        if (is_array($filter) && isset($filter["filters"])) {
            foreach ($filter["filters"] as $_filter) {
                $this->prep_k_filter($_filter, $query, $filter["logic"], $hijri_calendar_enabled);
            }
            unset($_filter);
        }
        if ($legalCaseID) {
            $query["where"][] = ["legal_case_hearings.legal_case_id", $legalCaseID];
        }
        $this->prep_query($query);
        $response["totalRows"] = $this->ci->db->get($this->get("_table"))->num_rows();
        if (is_array($sortable) && !empty($sortable)) {
            foreach ($sortable as $_sort) {
                $query["order_by"][] = [$_sort["field"], $_sort["dir"]];
            }
        } else {
            $query["order_by"] = ["legal_case_hearings.startDate desc"];
        }
        if ($page_number != "") {
            $export_offset = ($page_number - 1) * 10000;
            $query["limit"] = ["10000", $export_offset];
        } else {
            $limit = $this->ci->input->post("take", true);
            $offset = $this->ci->input->post("skip", true);
            if ($limit) {
                $query["limit"] = [$limit, $offset];
            }
        }
        $response["data"] = $this->load_all($query);
        $this->_table = $table;
        return $response;
    }
    public function load_hearings_roll_session_report($filter, $sortable, $filter_type = "", $hijri_calendar_enabled = false, $page_number = "")
    {
        $this->ci->db->_protect_identifiers = false;
        $this->ci->db->_force_escape_string_values = true;
        $this->ci->load->model("custom_field", "custom_fieldfactory");
        $this->ci->custom_field = $this->ci->custom_fieldfactory->get_instance("custom_fieldfactory");
        $language = strtolower(substr($this->ci->session->userdata("AUTH_language"), 0, 2));
        $this->ci->load->model("legal_case", "legal_casefactory");
        $this->ci->legal_case = $this->ci->legal_casefactory->get_instance();
        $table = $this->_table;
        $this->ci->load->model("language");
        $langId = $this->ci->language->get_id_by_session_lang();
        $this->_table = "mv_hearings AS legal_case_hearings";
        $query = [];
        $response = [];
        $query = ["select" => ["legal_case_hearings.id"]];
        if (is_array($filter)) {
            if (is_array($filter) && isset($filter["filters"])) {
                foreach ($filter["filters"] as $_filter) {
                    $this->prep_k_filter($_filter, $query, $filter["logic"], $hijri_calendar_enabled);
                }
                unset($_filter);
            }
            if (isset($filter["customFields"])) {
                $this->ci->custom_field->prep_custom_field_filters($this->ci->legal_case->modelName, $filter["customFields"], $query, "legal_case_id", "legal_case_hearings");
            }
        }
        $query["join"] = [["legal_case_hearings_users", "legal_case_hearings_users.legal_case_hearing_id = legal_case_hearings.id", "left"]];
        $ids = $this->load_all($query, "query");
        $query["group_by"] = ["legal_case_hearings.id"];
        $query = [];
        $select = "legal_case_hearings.*,SUBSTR(legal_case_hearings.caseSubject, 1, 417) AS caseSubject,legal_case_hearings.caseReference as CaseInternalReference,legal_case_hearings.clientPosition_" . $language . " AS clientPosition,legal_case_hearings.legal_case_stage_name_" . $language . " as hearing_stage,legal_case_hearings.caseValue, (select GROUP_CONCAT( (CASE WHEN conExtLaw.father!='' THEN CONCAT(conExtLaw.firstName, ' ', conExtLaw.father, ' ', conExtLaw.lastName) ELSE CONCAT(conExtLaw.firstName, ' ', conExtLaw.lastName) END) SEPARATOR ',' ) from legal_cases_contacts lccExtLaw LEFT JOIN contacts conExtLaw ON conExtLaw.id = lccExtLaw.contact_id where lccExtLaw.case_id = legal_case_hearings.legal_case_id AND lccExtLaw.contactType = 'external lawyer')  as OutsourcingLawyers,  legal_case_hearings.statusComments, legal_case_hearings.latest_development,legal_case_hearings.type_name_" . $language . " as type_name,legal_case_hearings.client_foreign_name AS client_foreign_name,legal_case_hearings.opponent_foreign_name_" . $language . " AS opponent_foreign_name, legal_case_hearings.case_assignee";
        $parameters = $this->load_custom_fields();
        if (isset($parameters["Field"])) {
            $select .= ",";
            $count = 0;
            foreach ($parameters["Field"] as $id => $value) {
                $select .= $value . " as custom_" . $id . ($count !== count($parameters["Field"]) - 1 ? "," : "");
                $count++;
            }
        }
        $query["select"] = [$select, false];
        if ($filter_type == "weekly") {
            $systemPreferences = $this->ci->session->userdata("systemPreferences");
            $businessWeekEquals = $systemPreferences["businessWeekEquals"];
            $sysDaysOff = $systemPreferences["sysDaysOff"];
            $days = ["Mon", "Tue", "Wed", "Thu", "Fri", "Sat", "Sun"];
            if (isset($businessWeekEquals) && !empty($businessWeekEquals) && isset($sysDaysOff) && !empty($sysDaysOff)) {
                $offDays = explode(", ", $sysDaysOff);
                $workingDays = array_diff($days, $offDays);
            } else {
                $workingDays = $days;
            }
            $dates = [];
            foreach ($workingDays as $key => $value) {
                $dates[] = date("Y-m-d", strtotime($value . " this week"));
            }
            $ranges[] = $dates[0];
            $ranges[] = $dates[sizeof($dates) - 1];
            $query["where"][] = ["(legal_case_hearings.startDate between '" . $ranges[0] . "' and '" . $ranges[1] . "' or legal_case_hearings.postponedDate between '" . $ranges[0] . "' and '" . $ranges[1] . "')", NULL, false];
        } else {
            if ($filter_type == "monthly") {
                $query_date = date("Y-m-d");
                $ranges[] = date("Y-m-01", strtotime($query_date));
                $ranges[] = date("Y-m-t", strtotime($query_date));
                $query["where"][] = ["(legal_case_hearings.startDate between '" . $ranges[0] . "' and '" . $ranges[1] . "' or legal_case_hearings.postponedDate between '" . $ranges[0] . "' and '" . $ranges[1] . "')", NULL, false];
            }
        }
        $query["where"][] = ["legal_case_hearings.id IN (" . $ids . ")"];
        if (is_array($sortable) && !empty($sortable)) {
            foreach ($sortable as $_sort) {
                $query["order_by"][] = [$_sort["field"], $_sort["dir"]];
            }
        } else {
            $query["order_by"] = ["legal_case_hearings.startDate asc"];
        }
        if ($page_number != "") {
            $query["limit"] = [10000, ($page_number - 1) * 10000];
        } else {
            if ($limit = $this->ci->input->post("take", true)) {
                $query["limit"] = [$this->ci->input->post("take", true), $this->ci->input->post("skip", true)];
            }
        }
        $response["data"] = parent::load_all($query);
        $response["totalRows"] = $this->count_total_matching_rows($query);
        $this->_table = $table;
        $this->ci->db->_protect_identifiers = true;
        $this->ci->db->_force_escape_string_values = false;
        return $response;
    }
    private function load_custom_fields()
    {
        $custom_fields = $this->ci->custom_field->load_list_per_language($this->ci->legal_case->get("modelName"));
        $parameters = [];
        foreach ($custom_fields as $field_data) {
            switch ($field_data["type"]) {
                case "date":
                    $parameters["Field"][$field_data["id"]] = "(SELECT cfv.date_value FROM custom_field_values AS cfv WHERE legal_case_hearings.legal_case_id = cfv.recordId AND cfv.custom_field_id = " . $field_data["id"] . ")";
                    break;
                case "date_time":
                    $parameters["Field"][$field_data["id"]] = "(SELECT CONCAT(cfv.date_value, ' ', TIME_FORMAT(cfv.time_value, '%h:%i')) FROM custom_field_values AS cfv WHERE legal_case_hearings.legal_case_id = cfv.recordId AND cfv.custom_field_id = " . $field_data["id"] . ")";
                    break;
                case "lookup":
                    $lookup_type_properties = $this->ci->custom_field->get_lookup_type_properties($field_data["type_data"]);
                    $lookup_displayed_columns_table = $lookup_type_properties["external_data"] ? "ltedt" : "ltt";
                    $lookup_external_data_join = $lookup_type_properties["external_data"] ? "LEFT JOIN " . $lookup_type_properties["external_data_properties"]["table"] . " ltedt ON ltedt." . $lookup_type_properties["external_data_properties"]["foreign_key"] . " = ltt.id" : "";
                    $last_segment = isset($lookup_type_properties["display_properties"]["third_segment"]["column_name"]) ? $lookup_displayed_columns_table . "." . $lookup_type_properties["display_properties"]["second_segment"]["column_name"] . ",' '," . $lookup_displayed_columns_table . "." . $lookup_type_properties["display_properties"]["third_segment"]["column_name"] : $lookup_displayed_columns_table . "." . $lookup_type_properties["display_properties"]["second_segment"]["column_name"];
                    $parameters["Field"][$field_data["id"]] = "\r\n                    (\r\n                        SELECT GROUP_CONCAT(" . $lookup_displayed_columns_table . "." . $lookup_type_properties["display_properties"]["first_segment"]["column_name"] . ",' ' ," . $last_segment . " SEPARATOR ', ')\r\n                           FROM custom_field_values cfv\r\n                        left join " . $lookup_type_properties["table"] . " ltt on ltt.id = cfv.text_value " . $lookup_external_data_join . "\r\n                        where cfv.recordId = legal_case_hearings.legal_case_id  and custom_field_id = " . $field_data["id"] . "\r\n                    )";
                    break;
                case "list":
                    $parameters["Field"][$field_data["id"]] = "(SELECT GROUP_CONCAT(cfv.text_value) FROM custom_field_values AS cfv WHERE cfv.recordId = legal_case_hearings.legal_case_id AND cfv.custom_field_id = " . $field_data["id"] . ")";
                    break;
                default:
                    $parameters["Field"][$field_data["id"]] = "(SELECT cfv.text_value FROM custom_field_values as cfv WHERE legal_case_hearings.legal_case_id = cfv.recordId AND cfv.custom_field_id = " . $field_data["id"] . ")";
            }
        }
        return $parameters;
    }
    public function update_extra_users_fields($fieldsData)
    {
        $sqlInsert = "";
        $sqlDelete = [];
        $rows = [];
        foreach ($fieldsData as $key => $fieldData) {
            extract($fieldData);
            if (is_array($users)) {
                $subDelete = "(legal_case_hearing_id = '" . $legal_case_hearing_id . "' and user_id NOT IN (0";
                foreach ($users as $user_id) {
                    $rows[] = compact("legal_case_hearing_id", "user_id");
                    $subDelete .= ", '" . $user_id . "'";
                }
                $subDelete .= ") and user_type != 'AP')";
            } else {
                $subDelete = "(legal_case_hearing_id = '" . $legal_case_hearing_id . "' and user_type != 'AP')";
            }
            $sqlDelete[] = [$subDelete];
        }
        $this->prep_query(["or_where" => $sqlDelete]);
        $this->ci->db->delete("legal_case_hearings_users");
        $this->reset_write();
        if (count($rows)) {
            $table = $this->_table;
            $this->_table = "legal_case_hearings_users";
            $this->insert_on_duplicate_update_batch($rows, ["legal_case_hearing_id", "user_id"]);
            $this->_table = $table;
        }
        return true;
    }
    public function load_extra_users_data($hearingId)
    {
        $users = [];
        $ap_users = [];
        $data = [];
        $status = [];
        if ($hearingId < 1) {
            return $users;
        }
        $case_users = $this->ci->db->select(["user_profiles.user_id as id, CONCAT( user_profiles.firstName, ' ', user_profiles.lastName ) as name,user_profiles.status as status", false])->join("user_profiles", "user_profiles.user_id = legal_case_hearings_users.user_id", "inner")->where(["legal_case_hearings_users.legal_case_hearing_id" => $hearingId, "legal_case_hearings_users.user_type !=" => "AP"])->get("legal_case_hearings_users");
        $case_advisor_users = $this->ci->db->select(["advisor_users.id as id, CONCAT( advisor_users.firstName, ' ', advisor_users.lastName ) as name", false])->join("advisor_users", "advisor_users.id = legal_case_hearings_users.user_id", "inner")->where(["legal_case_hearings_users.legal_case_hearing_id" => $hearingId, "legal_case_hearings_users.user_type" => "AP"])->get("legal_case_hearings_users");
        if (!$case_users->num_rows() && !$case_advisor_users->num_rows()) {
            return $users;
        }
        foreach ($case_users->result() as $user) {
            $users[(string) $user->id] = $user->name;
        }
        foreach ($case_advisor_users->result() as $user) {
            $ap_users[(string) $user->id] = $user->name;
        }
        foreach ($case_users->result() as $user) {
            $status[(string) $user->id] = $user->status;
        }
        $data[0] = $users;
        $data[1] = $status;
        $data[2] = $ap_users;
        return $data;
    }
    public function api_load_all_hearings($userLang, $take = 20, $skip = 0, $term = "", $search_filters, $hijri_calendar_enabled = false, $order = "desc")
    {
        $table = $this->_table;
        $this->_table = "mv_hearings AS legal_case_hearings";
        $query = [];
        $response = [];
        $query["select"] = ["legal_case_hearings.*, CONCAT(SUBSTRING(caseSubject, 1, 60), '...') AS caseSubject, clientPosition_" . $userLang . " AS clientPosition, legal_case_hearings.judged", false];
        $query["join"] = [["legal_cases", "legal_case_hearings.legal_case_id = legal_cases.id", "left"]];
        if ($term != "") {
            $term = $this->ci->db->escape_like_str($term);
            $query["where"][] = [" ( legal_case_hearings.summary LIKE '%" . $term . "%' or legal_case_hearings.caseSubject LIKE '%" . $term . "%' or legal_cases.description LIKE '%" . $term . "%' )", NULL, false];
        }
        $query = $this->filter_builder($query, $search_filters);
        $this->prep_query($query);
        $response["totalRows"] = $this->ci->db->get($this->get("_table"))->num_rows();
        $query["order_by"] = ["legal_case_hearings.startDate " . $order];
        $query["limit"] = [$take, $skip];
        $response["data"] = $this->load_all($query);
        if ($hijri_calendar_enabled) {
            foreach ($response["data"] as $key => $hearing) {
                $response["data"][$key]["startDate"] = gregorianToHijri($hearing["startDate"], "Y-m-d");
                $response["data"][$key]["postponedDate"] = $hearing["postponedDate"] ? gregorianToHijri($hearing["postponedDate"], "Y-m-d") : NULL;
            }
        }
        $this->_table = $table;
        return $response;
    }
    protected function filter_builder($query, $search_filters)
    {
        foreach ($search_filters as $key => $value) {
            switch ($key) {
                case "user_id":
                    $query["join"][] = ["legal_case_hearings_users", "legal_case_hearings_users.legal_case_hearing_id = legal_case_hearings.id", "left"];
                    $query["where"][] = [" legal_case_hearings_users.user_id = '" . $value . "' ", NULL, false];
                    break;
                case "startDate":
                    $value["operator"] = $value["operator"] == "" ? "=" : $value["operator"];
                    if ($value["operator"] === "between") {
                        $query["where"][] = [" ( legal_case_hearings.startDate >= '" . $value["date"]["from"] . "' AND legal_case_hearings.startDate <= '" . $value["date"]["to"] . "' )", NULL, false];
                    } else {
                        $query["where"][] = [" ( legal_case_hearings.startDate " . $value["operator"] . " '" . $value["date"] . "' )", NULL, false];
                    }
                    break;
                default:
                    $query["where"][] = [" legal_case_hearings." . $key . " = '" . $value . "' ", NULL, false];
            }
        }
        return $query;
    }
    public function load_related_hearing_lawyers($hearingId)
    {
        $users = [];
        if ($hearingId < 1) {
            return $users;
        }
        $case_users = $this->ci->db->select(["user_profiles.user_id as id, users.email as email, CONCAT( user_profiles.firstName, ' ', user_profiles.lastName ) as name", false])->join("user_profiles", "user_profiles.user_id = legal_case_hearings_users.user_id", "inner")->join("users", "users.id = legal_case_hearings_users.user_id", "inner")->where("legal_case_hearings_users.legal_case_hearing_id", $hearingId)->where("user_profiles.status", "Active")->get("legal_case_hearings_users");
        if (!$case_users->num_rows()) {
            return $users;
        }
        foreach ($case_users->result() as $user) {
            $users[(string) $user->id] = ["name" => $user->name, "email" => $user->email];
        }
        return $users;
    }
    public function updateGoogleEventIDs($eventID)
    {
        return $this->ci->db->where("google_api_event_id", $eventID)->update($this->_table, ["google_api_event_id" => NULL]);
    }
    public function delete_google_data($id)
    {
        return $this->ci->db->where("id", $id)->update($this->_table, ["google_api_event_id" => NULL]);
    }
    public function update_event_id($event_id)
    {
        if ($event_id) {
            return $this->ci->db->where("task_id", $event_id)->update($this->_table, ["task_id" => NULL]);
        }
        return true;
    }
    public function remove_object_related_to_deleted_hearings($variable, $id)
    {
        $deleted = true;
        $hearings = $this->ci->db->select("id,is_deleted")->from($this->_table)->where($variable, $id)->get();
        foreach ($hearings->result() as $hearing) {
            if (!$hearing->is_deleted) {
                $deleted = false;
            }
        }
        if ($deleted) {
            foreach ($hearings->result() as $hearing) {
                $this->ci->db->where("id", $hearing->id);
                $this->ci->db->update($this->_table, [$variable => NULL]);
            }
        }
        return $deleted;
    }
    public function count_related_hearings($id)
    {
        $query = [];
        $query["select"] = ["COUNT(0) as numRows"];
        $query["where"] = ["legal_case_hearings.task_id", $id];
        $this->prep_query($query);
        $data = $this->load($query);
        return $data["numRows"];
    }
    public function load_hearing_data($id)
    {
        $language = strtolower(substr($this->ci->session->userdata("AUTH_language"), 0, 2));
        $this->ci->load->model("language");
        $lang_id = $this->ci->language->get_id_by_session_lang();
        $table = $this->_table;
        $this->_table = "mv_hearings AS legal_case_hearings";
        $query = $response = [];
        $query["select"] = ["legal_case_hearings.id,legal_case_hearings.legal_case_id,legal_case_hearings.caseReference as internalReference, legal_case_hearings.startDate, legal_case_hearings.startTime, CONCAT_WS(' ',legal_case_hearings.postponedDate, ' ', legal_case_hearings.postponedTime) as postponed_date,legal_case_hearings.summary,legal_case_hearings.reference,legal_case_hearings.caseID,legal_case_hearings.clients,legal_case_hearings.judges, legal_case_hearings.opponentLawyers,legal_case_hearings.lawyers,legal_case_hearings.courtType,legal_case_hearings.courtDegree,legal_case_hearings.courtRegion,legal_case_hearings.court, legal_case_hearings.caseSubject, clientPosition_" . $language . " AS clientPosition,types.name as type_name,stage_statuses.name as stage_status, legal_case_hearings.judgment, stages.name as stage_name, legal_case_hearings.sentenceDate, legal_case_hearings.case_description," . "(SELECT GROUP_CONCAT(CASE WHEN `legal_case_opponents`.`opponent_member_type` IS NULLTHEN  NULLELSE (                                CASE WHEN opponent_positions.name != ''                                    THEN                                         (CASE WHEN `legal_case_opponents`.`opponent_member_type` = 'company'                                            THEN CONCAT(`opponentCompany`.`name`, ' - ', opponent_positions.name)                                            ELSE (CASE WHEN opponentContact.father!='' THEN CONCAT(opponentContact.firstName, ' ', opponentContact.father, ' ', opponentContact.lastName, ' - ', opponent_positions.name) ELSE CONCAT(opponentContact.firstName, ' ', opponentContact.lastName, ' - ', opponent_positions.name) END)                                        END)                                    ELSE                                        (CASE WHEN `legal_case_opponents`.`opponent_member_type` = 'company'                                            THEN `opponentCompany`.`name`                                            ELSE (CASE WHEN opponentContact.father!='' THEN CONCAT(opponentContact.firstName, ' ', opponentContact.father, ' ', opponentContact.lastName) ELSE CONCAT(opponentContact.firstName, ' ', opponentContact.lastName) END)                                        END)END                        )END order by legal_case_hearings.legal_case_id ASC SEPARATOR ',' )from  `legal_case_opponents`LEFT JOIN `opponents` ON `opponents`.`id` = `legal_case_opponents`.`opponent_id`LEFT JOIN `companies` AS `opponentCompany` ON `opponentCompany`.`id` = `opponents`.`company_id` AND `legal_case_opponents`.`opponent_member_type` = 'company'LEFT JOIN `contacts` AS `opponentContact` ON `opponentContact`.`id` = `opponents`.`contact_id` AND `legal_case_opponents`.`opponent_member_type` = 'contact'LEFT JOIN legal_case_opponent_position_languages AS opponent_positions ON opponent_positions.legal_case_opponent_position_id = legal_case_opponents.opponent_position and opponent_positions.language_id = '" . $lang_id . "'\r\n                        WHERE `legal_case_opponents`.`case_id` = legal_case_hearings.legal_case_id ) AS opponents," . "(SELECT GROUP_CONCAT(CASE WHEN opponent_positions.name != ''                                    THEN                                         (CASE WHEN `opponents`.`contact_id` IS NULL                                            THEN CONCAT(`opponentCompany`.`name`, ' - ', opponent_positions.name)                                            ELSE (CASE WHEN opponentContact.father!='' THEN CONCAT(opponentContact.firstName, ' ', opponentContact.father, ' ', opponentContact.lastName, ' - ', opponent_positions.name) ELSE CONCAT(opponentContact.firstName, ' ', opponentContact.lastName, ' - ', opponent_positions.name) END)                                        END)                                    ELSE                                        (CASE WHEN `opponents`.`contact_id` IS NULL                                            THEN `opponentCompany`.`name`                                            ELSE (CASE WHEN opponentContact.father!='' THEN CONCAT(opponentContact.firstName, ' ', opponentContact.father, ' ', opponentContact.lastName) ELSE CONCAT(opponentContact.firstName, ' ', opponentContact.lastName) END)                                        END)END                        order by legal_case_hearings.legal_case_id ASC SEPARATOR ',' )from  `legal_case_litigation_stages_opponents`LEFT JOIN `opponents` ON `opponents`.`id` = `legal_case_litigation_stages_opponents`.`opponent_id`LEFT JOIN `companies` AS `opponentCompany` ON `opponentCompany`.`id` = `opponents`.`company_id` AND `opponents`.`contact_id` IS NULLLEFT JOIN `contacts` AS `opponentContact` ON `opponentContact`.`id` = `opponents`.`contact_id` AND `opponents`.`company_id` IS NULLLEFT JOIN legal_case_opponent_position_languages AS opponent_positions ON opponent_positions.legal_case_opponent_position_id = legal_case_litigation_stages_opponents.opponent_position and opponent_positions.language_id = '" . $lang_id . "'\r\n                        WHERE `legal_case_litigation_stages_opponents`.`stage` = legal_case_hearings.stage ) AS stage_opponents", false];
        $query["where"] = ["legal_case_hearings.id", $id];
        $query["join"][] = ["stage_statuses_languages as stage_statuses", "stage_statuses.status = legal_case_hearings.stage_status and stage_statuses.language_id = '" . $lang_id . "'", "left"];
        $query["join"][] = ["hearing_types_languages as types", "types.type = legal_case_hearings.type and types.language_id = '" . $lang_id . "'", "left"];
        $query["join"][] = ["legal_case_stage_languages as stages", "stages.legal_case_stage_id = legal_case_hearings.legal_case_stage_id and stages.language_id = '" . $lang_id . "'", "left"];
        $response = $this->load($query);
        $this->_table = $table;
        return $response;
    }
    public function bulk_update_summary_to_client($hearingIds)
    {
        return $this->ci->db->query("UPDATE legal_case_hearings set summaryToClient = summary, verifiedSummary = 1 where id in (" . $hearingIds . ") and summary is not null");
    }
    public function get_empty_summary($hearingIds)
    {
        return $this->ci->db->query("select id from legal_case_hearings where id in (" . $hearingIds . ") and summary is null")->result_array();
    }
    public function lookup($term, $more_filters = [], $hijri_calendar_enabled = false)
    {
        $config_query = [];
        $config_query["select"][] = ["CONCAT('" . $this->get("modelCode") . "', legal_case_hearings.id) as hearingID, legal_case_hearings.id, CASE WHEN case_details.court_id IS NULL THEN CONCAT(legal_case_hearings.startDate, ' ',IFNULL(legal_case_hearings.startTime, '')) ELSE CONCAT(legal_case_hearings.startDate, ' ',IFNULL(legal_case_hearings.startTime, ''),' - ',courts.name) END as subject, legal_case_hearings.legal_case_id", false];
        $config_query["where"][] = ["legal_case_hearings.is_deleted = '0'", NULL, false];
        $config_query["join"][] = ["legal_case_litigation_details as case_details", "case_details.id = legal_case_hearings.stage", "left"];
        $config_query["join"][] = ["courts", "courts.id = case_details.court_id", "left"];
        if (!empty($term)) {
            $modelCode = substr($term, 0, 1);
            $ID = substr($term, 1);
            if (strcasecmp($modelCode, $this->get("modelCode")) === 0 && is_numeric($ID)) {
                $qId = substr($term, 1);
                if (is_numeric($qId)) {
                    $config_query["where"][] = ["legal_case_hearings.id = " . $qId, NULL, false];
                }
            } else {
                $term = $this->ci->db->escape_like_str($term);
                if ($hijri_calendar_enabled) {
                    $original_term = $term;
                    $term = hijriToGregorian($term);
                    $term = $term ? $term : $original_term;
                }
                $config_query["where"][] = ["CASE WHEN case_details.court_id IS NULL THEN CONCAT(legal_case_hearings.startDate, ' ',IFNULL(legal_case_hearings.startTime, '')) ELSE CONCAT(legal_case_hearings.startDate, ' ',IFNULL(legal_case_hearings.startTime, ''),' - ',courts.name) END LIKE '%" . $term . "%'", NULL, false];
            }
        }
        if ($more_filters) {
            foreach ($more_filters as $_field => $_term) {
                $config_query["where"][] = ["legal_case_hearings." . $_field, $_term];
            }
        }
        return $this->load_all($config_query);
    }
    public function count_related_expenses($id)
    {
        $table = $this->_table;
        $this->_table = "expenses";
        $query = [];
        $query["select"] = ["COUNT(0) as numRows"];
        $query["where"] = ["expenses.hearing", $id];
        $data = $this->load($query);
        $this->_table = $table;
        return $data["numRows"];
    }
    public function load_daily_agenda_hearings()
    {
        $query = [];
        $query["select"] = ["legal_case_hearings.startDate, legal_case_hearings.startTime, legal_case_hearings.legal_case_id, users.email, legal_cases.subject", false];
        $query["join"] = [["legal_case_hearings_users", "legal_case_hearings.id = legal_case_hearings_users.legal_case_hearing_id"], ["legal_cases", "legal_case_hearings.legal_case_id = legal_cases.id"], ["users", "users.id = legal_case_hearings_users.user_id"]];
        $query["where"][] = ["CURRENT_DATE = legal_case_hearings.startDate AND legal_cases.isDeleted = 0", NULL, false];
        $query["order_by"] = ["startDate desc"];
        return $this->load_all($query);
    }
    public function roll_session_per_court($filter, $export = false, $hijri_calendar_enabled = false, &$query = [], &$stringQuery = "")
    {
        $_table = $this->_table;
        $this->_table = "mv_hearings AS legal_case_hearings";
        $query = [];
        $response = [];
        $this->ci->load->model("language");
        $langId = $this->ci->language->get_id_by_session_lang();
        $language = strtolower(substr($this->ci->session->userdata("AUTH_language"), 0, 2));
        $sortable = [["field" => "legal_case_hearings.legal_case_stage_name_" . $language, "dir" => "asc"], ["field" => "legal_case_hearings.court_id", "dir" => "asc"], ["field" => "legal_case_hearings.startDate", "dir" => "asc"]];
        $query["select"] = ["legal_case_hearings.id, legal_case_hearings.court_id as court_id, legal_case_hearings.court as court_name, legal_case_hearings.summary,legal_case_hearings.lawyers,legal_case_hearings.postponedDate,legal_case_hearings.legal_case_id as legal_case_id,legal_case_hearings.caseID,legal_case_hearings.legal_case_stage_name_" . $language . " as stage_name,legal_case_hearings.clients AS client,legal_case_hearings.reference as reference,legal_case_hearings.startDate,legal_case_hearings.startTime,legal_case_hearings.containerID as containerID,legal_case_hearings.reasons_of_postponement,legal_case_hearings.comments,legal_case_hearings.courtType as court_type,legal_case_hearings.courtDegree as court_degree,legal_case_hearings.courtRegion as court_region,legal_case_hearings.stage AS stage_id,legal_case_hearings.type_name_" . $language . " as type_name,legal_case_hearings.clientPosition_" . $language . " AS clientPosition, (CASE WHEN `user_profiles`.`father` = '' THEN CONCAT(user_profiles.firstName,' ', user_profiles.lastName) ELSE CONCAT(user_profiles.firstName, ' ', user_profiles.father, ' ', user_profiles.lastName) END) as matter_assignee, legal_case_hearings.opponents_" . $language . " AS opponents,legal_case_hearings.opponent_foreign_name_" . $language . " AS opponent_foreign_name,legal_case_hearings.client_foreign_name AS client_foreign_name", false];
        if (is_array($filter) && isset($filter["filters"])) {
            foreach ($filter["filters"] as $_filter) {
                $this->prep_k_filter($_filter, $query, $filter["logic"], $hijri_calendar_enabled);
            }
            unset($_filter);
        }
        $this->ci->load->model("system_configuration");
        $excluded_statuses = $this->ci->system_configuration->get_value_by_key("hearingReportExcludedStatuses");
        if (!empty($excluded_statuses)) {
            $query["where_not_in"][] = ["legal_cases.case_status_id", $excluded_statuses];
        }
        $query["join"] = [["legal_cases", "legal_cases.id = legal_case_hearings.legal_case_id", "left"], ["user_profiles", "user_profiles.user_id = legal_cases.user_id", "left"]];
        $query["group_by"] = ["legal_case_hearings.id"];
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
        $response["totalRows"] = $this->count_total_matching_rows($query);
        $this->_table = $_table;
        return $response;
    }
    public function pending_updates($filter, $export = false, $hijri_calendar_enabled = false)
    {
        $_table = $this->_table;
        $this->_table = "mv_hearings AS legal_case_hearings";
        $query = [];
        $response = [];
        $this->ci->load->model("language");
        $langId = $this->ci->language->get_id_by_session_lang();
        $language = strtolower(substr($this->ci->session->userdata("AUTH_language"), 0, 2));
        $query["select"] = ["legal_case_hearings.id, legal_case_hearings.startDate, legal_case_hearings.startTime, legal_case_hearings.legal_case_id as legal_case_id,legal_case_hearings.caseID,legal_case_hearings.legal_case_stage_name_" . $language . " as stage_name,legal_case_hearings.clients AS client,legal_case_hearings.reference as reference,legal_case_hearings.postponedDate,legal_case_hearings.containerID as containerID,legal_case_hearings.stage AS stage_id,legal_case_hearings.type_name_" . $language . " as type_name,legal_case_hearings.opponent_foreign_name_" . $language . " AS opponent_foreign_name,legal_case_hearings.client_foreign_name AS client_foreign_name,legal_case_hearings.previousHearingDate as previousHearingDate,legal_case_hearings.opponents_" . $language . " AS opponents", false];
        if (is_array($filter) && isset($filter["filters"])) {
            foreach ($filter["filters"] as $_filter) {
                $this->prep_k_filter($_filter, $query, $filter["logic"], $hijri_calendar_enabled);
            }
            unset($_filter);
        }
        $this->ci->load->model("system_configuration");
        $excluded_statuses = $this->ci->system_configuration->get_value_by_key("hearingUpdatesReportExcludedStatuses");
        if (!empty($excluded_statuses)) {
            $query["where_not_in"][] = ["legal_cases.case_status_id", $excluded_statuses];
        }
        $business_rules = $this->ci->system_configuration->get_value_by_key("hearingUpdatesReportBusinessRules");
        $business_rules_str = "";
        if (!empty($business_rules)) {
            foreach ($business_rules as $business_rule) {
                $business_rules_str .= $business_rule . " is null or ";
            }
            $business_rules_str = " and (" . substr($business_rules_str, 0, -4) . ")";
        }
        $query["where"][] = ["startDate <= CURDATE()", NULL, false];
        $query["where"][] = ["(" . "(legal_case_hearings.judged = 'no' and legal_case_hearings.judgment is null " . $business_rules_str . ")" . " OR (legal_case_hearings.judged = 'yes' and legal_case_hearings.judgment is null " . $business_rules_str . ")" . ")", NULL, false];
        $query["join"] = [["legal_cases", "legal_cases.id = legal_case_hearings.legal_case_id", "left"]];
        $query["group_by"] = ["legal_case_hearings.id"];
        $query["order_by"] = ["legal_case_hearings.startDate  desc"];
        $paginationConf = [];
        if (!$export) {
            $query["limit"] = [$this->ci->input->post("take", true), $this->ci->input->post("skip", true)];
            $paginationConf = ["urlPrefix" => ""];
            $paginationConf["inPage"] = $this->ci->input->post("take", true);
        }
        $response["data"] = $export ? parent::load_all($query) : parent::paginate($query, $paginationConf);
        $stringQuery = $this->ci->db->last_query();
        $response["totalRows"] = $this->count_total_matching_rows($query);
        $this->_table = $_table;
        return $response;
    }
    public function delete_legal_case_hearings($data)
    {
        if (!empty($data)) {
            $this->ci->db->set("is_deleted", 1);
            $this->ci->db->where("legal_case_hearings.id IN (" . $data . ")", NULL, false);
            if ($this->ci->db->dbdriver == "mysqli") {
                $this->ci->db->update("legal_case_hearings");
                $this->ci->db->where("id IN (" . $data . ")", NULL, false);
                return $this->ci->db->delete("mv_hearings");
            }
            return $this->ci->db->update("legal_case_hearings");
        }
        return false;
    }
    public function inject_reminders($hearingData)
    {
        if (!$hearingData["startDate"]) {
            return ["result" => false, "message" => ["type" => "warning", "text" => $this->ci->lang->line("reminders_date_not_set")]];
        }
        $this->ci->load->model("reminder", "reminderfactory");
        $this->ci->reminder = $this->ci->reminderfactory->get_instance();
        $hearingData["startTime"] = $hearingData["startTime"] ? $hearingData["startTime"] : $this->ci->reminder->get("reminderTimeQuickAddDefaultValue");
        $this->ci->load->model("system_preference");
        $systemPreferences = $this->ci->system_preference->get_values();
        $reminderType = $systemPreferences["hearingReminderType"];
        if (!isset($reminderType) || !$reminderType) {
            return ["result" => false, "message" => ["type" => "warning", "text" => $this->ci->lang->line("default_reminder_type_not_set")]];
        }
        foreach ($hearingData["assignees"] as $userId) {
            $this->ci->reminder->reset_fields();
            $this->ci->reminder->set_field("legal_case_hearing_id", $hearingData["id"]);
            $this->ci->reminder->set_field("legal_case_id", $hearingData["legal_case_id"]);
            $this->ci->reminder->set_field("user_id", $userId);
            $this->ci->reminder->set_field("reminder_type_id", $reminderType);
            $this->ci->reminder->set_field("remindDate", $hearingData["startDate"]);
            $this->ci->reminder->set_field("remindTime", $hearingData["startTime"]);
            $this->ci->reminder->set_field("summary", $hearingData["summaryText"]);
            $this->ci->reminder->set_field("status", "Open");
            $this->ci->reminder->set_field("notify_before_time", $systemPreferences["reminderIntervalDate"]);
            $this->ci->reminder->set_field("notify_before_time_type", $this->ci->reminder->get("default_notify_me_before_time_type"));
            $this->ci->reminder->set_field("notify_before_type", $this->ci->reminder->get("default_notify_me_before_type"));
            $result = $this->ci->reminder->insert();
        }
        return ["result" => $result];
    }
    public function inject_calendar_events($hearingData)
    {
        $this->ci->load->model("event", "eventfactory");
        $this->ci->event = $this->ci->eventfactory->get_instance();
        $result = false;
        $this->ci->event->set_field("legal_case_id", $hearingData["legal_case_id"]);
        $this->ci->event->set_field("start_date", $hearingData["startDate"]);
        $this->ci->event->set_field("end_date", $hearingData["startDate"]);
        $this->ci->event->set_field("start_time", $hearingData["startTime"]);
        $this->ci->event->set_field("end_time", date("H:i", strtotime($hearingData["startTime"]) + 1800));
        $this->ci->event->set_field("title", mb_substr($hearingData["summaryText"], 0, 255));
        $this->ci->event->set_field("priority", "medium");
        if ($this->ci->event->insert()) {
            $EventUsers = ["event_id" => $this->ci->event->get_field("id"), "attendees" => $hearingData["assignees"]];
            $this->ci->load->model("event_attendee");
            $this->ci->event_attendee->insert_attendees($EventUsers);
            if (0 < $this->ci->event->get_field("id")) {
                $this->fetch($hearingData["id"]);
                $this->set_field("task_id", $this->ci->event->get_field("id"));
                $this->update();
                $this->ci->event->update_integration_provider_calendar($this->ci->event->get_field("id"), "add");
            }
        }
        return $result;
    }
    public function load_hearing_details($id, $language = false)
    {
        $this->ci->load->model("language");
        $lang_id = $this->ci->language->get_id_by_session_lang($language);
        $query = [];
        $this->ci->load->model("language");
        $language_id = $this->ci->language->get_id_by_session_lang();
        $query["select"][] = ["legal_case_litigation_details.sentenceDate, mv_hearings.courtRegion AS court_region, mv_hearings.courtDegree AS court_degree, mv_hearings.courtType as court_type, legal_case_hearings.id,legal_cases.subject as case_subject,legal_case_hearings.startDate as hearing_date,stage_statuses.name as stage_status,stages.name as stage_name,case_types.name as type_name, legal_case_hearings.startTime as hearing_time, legal_case_hearings.postponedDate as postponedDate, legal_case_hearings.postponedTime as postponedTime, legal_case_hearings.comments as comments, legal_case_hearings.judged as judged,  legal_case_hearings.judgment as judgment, legal_case_hearings.reasons_of_postponement as reasons_of_postponement, legal_case_hearings.summary,legal_case_hearings.summaryToClient,legal_cases.id as legal_case_id, legal_cases.subject as case_subject, legal_cases.description as case_description, legal_cases.internalReference, legal_case_client_position_languages.name as client_position, mv_hearings.court as court, (case when isnull(clients.company_id) then (case when contacts.father <> ' ' then concat(contacts.firstName,' ',contacts.father, ' ', contacts.lastName)else concat(contacts.firstName,' ', contacts.lastName) end)else companies.name end) as client_name,GROUP_CONCAT( DISTINCT( CASE WHEN judges.father != '' THEN CONCAT(judges.firstName, ' ', judges.father, ' ', judges.lastName) ELSE CONCAT(judges.firstName, ' ', judges.lastName) END ) SEPARATOR ', ' ) AS judges, CONCAT( mv_hearings.caseID, ' - ', mv_hearings.fullCaseSubject ) as caseID, mv_hearings.opponentLawyers AS opponent_lawyers, mv_hearings.lawyers AS `assignee(s)`,GROUP_CONCAT( DISTINCT CONCAT( lawyers.firstName, ' ', lawyers.lastName ) SEPARATOR ', ' ) AS assignees,GROUP_CONCAT( DISTINCT CONCAT( lcler.number, ' (', lcler.refDate , ')' ) SEPARATOR ', ' ) AS reference_date,(select concat(next_hearing.startDate, ' ', next_hearing.startTime) from legal_case_hearings as next_hearing where next_hearing.id != legal_case_hearings.id and legal_case_hearings.stage = next_hearing.stage and next_hearing.startDate > legal_case_hearings.startDate and next_hearing.legal_case_id = legal_case_hearings.legal_case_id limit 1) as next_hearing," . "(SELECT GROUP_CONCAT(CASE WHEN opponent_positions.name != ''                                    THEN                                         (CASE WHEN `opponents`.`contact_id` IS NULL                                            THEN CONCAT(`opponentCompany`.`name`, ' - (', opponent_positions.name, ')')                                            ELSE (CASE WHEN opponentContact.father!='' THEN CONCAT(opponentContact.firstName, ' ', opponentContact.father, ' ', opponentContact.lastName, ' - (', opponent_positions.name , ')') ELSE CONCAT(opponentContact.firstName, ' ', opponentContact.lastName, ' - (', opponent_positions.name , ')') END)                                        END)                                    ELSE                                        (CASE WHEN `opponents`.`contact_id` IS NULL                                            THEN `opponentCompany`.`name`                                            ELSE (CASE WHEN opponentContact.father!='' THEN CONCAT(opponentContact.firstName, ' ', opponentContact.father, ' ', opponentContact.lastName) ELSE CONCAT(opponentContact.firstName, ' ', opponentContact.lastName) END)                                        END)END                        order by legal_case_hearings.legal_case_id ASC SEPARATOR ',' )from  `legal_case_litigation_stages_opponents`LEFT JOIN `opponents` ON `opponents`.`id` = `legal_case_litigation_stages_opponents`.`opponent_id`LEFT JOIN `companies` AS `opponentCompany` ON `opponentCompany`.`id` = `opponents`.`company_id` AND `opponents`.`contact_id` IS NULLLEFT JOIN `contacts` AS `opponentContact` ON `opponentContact`.`id` = `opponents`.`contact_id` AND `opponents`.`company_id` IS NULLLEFT JOIN legal_case_opponent_position_languages AS opponent_positions ON opponent_positions.legal_case_opponent_position_id = legal_case_litigation_stages_opponents.opponent_position and opponent_positions.language_id = '" . $lang_id . "'\r\n                        WHERE `legal_case_litigation_stages_opponents`.`stage` = legal_case_hearings.stage ) AS stage_opponents", false];
        $query["join"] = [["legal_cases", "legal_cases.id = legal_case_hearings.legal_case_id ", "left"], ["case_types", "case_types.id = legal_cases.case_type_id", "left"], ["clients", "clients.id = legal_cases.client_id ", "left"], ["companies", "companies.id = clients.company_id ", "left"], ["contacts", "contacts.id = clients.contact_id ", "left"], ["legal_case_client_positions", "legal_case_client_positions.id = legal_cases.legal_case_client_position_id", "left"], ["legal_case_client_position_languages", "legal_case_client_position_languages.legal_case_client_position_id = legal_case_client_positions.id and language_id = " . $language_id, "left"], ["legal_case_litigation_details", "legal_case_litigation_details.legal_case_id = legal_cases.id and legal_case_litigation_details.legal_case_stage = legal_cases.legal_case_stage_id and legal_case_litigation_details.legal_case_id = legal_case_hearings.legal_case_id", "left"], ["legal_case_litigation_external_references lcler", "lcler.stage = legal_case_litigation_details.id", "left"], ["legal_case_stage_contacts lcsc", "lcsc.stage = legal_case_hearings.stage AND lcsc.contact_type = 'judge'", "left"], ["contacts judges", "judges.id = lcsc.contact", "left"], ["legal_case_hearings_users lchu", "lchu.legal_case_hearing_id = legal_case_hearings.id", "left"], ["user_profiles lawyers", "lawyers.id = lchu.user_id", "left"], ["mv_hearings", "mv_hearings.id = legal_case_hearings.id", "left"], ["legal_case_stage_languages as stages", "stages.legal_case_stage_id = legal_case_hearings.stage and stages.language_id = '" . $lang_id . "'", "left"], ["stage_statuses_languages as stage_statuses", "stage_statuses.status = mv_hearings.stage_status and stage_statuses.language_id = '" . $lang_id . "'", "left"]];
        $query["where"][] = ["legal_case_hearings.id", $id];
        return $this->load($query);
    }
    public function filter_matter_clients()
    {
        return "IF( ISNULL(clients.company_id), CASE WHEN con.father != ' ' THEN CONCAT_WS( ' ', con.firstName, con.father, con.lastName) ELSE CONCAT_WS(' ', con.firstName, con.lastName) END, com.name)";
    }
    public function filter_matter_client_foreign_name()
    {
        return "if(isnull(clients.company_id), concat_ws(' ', con.foreignFirstName, con.foreignLastName), com.foreignName)";
    }
    public function filter_stage_opponent_lawyer()
    {
        return "(CONCAT( contoppLaw.firstName, ' ', contoppLaw.lastName ))";
    }
    public function filter_stage_judge()
    {
        return "(CONCAT(contJud.firstName, ' ', contJud.lastName))";
    }
    public function filter_stage_judges()
    {
        return "(GROUP_CONCAT( DISTINCT( CASE WHEN contJud.father != '' THEN CONCAT(contJud.firstName, ' ', contJud.father, ' ', contJud.lastName) ELSE CONCAT(contJud.firstName, ' ', contJud.lastName) END ) SEPARATOR ';' ))";
    }
    public function filter_matter_client_position_id()
    {
        return "legal_cases.legal_case_client_position_id";
    }
    public function filter_stage_court_type_id()
    {
        return "ld.court_type_id";
    }
    public function filter_stage_court_degree_id()
    {
        return "ld.court_degree_id";
    }
    public function filter_stage_court_region_id()
    {
        return "ld.court_region_id";
    }
    public function filter_stage_court_id()
    {
        return "ld.court_id";
    }
    public function filter_matter_practice_area()
    {
        return "legal_cases.case_type_id";
    }
    public function filter_matter_container_id()
    {
        return "CONCAT( 'MC', legal_case_containers.id )";
    }
    public function filter_hearing_lawyers()
    {
        return "(GROUP_CONCAT( DISTINCT CONCAT( userLawfirstName, ' ', userLawlastName , IF( userLawstatus = 'Inactive', ' (Inactive)', '') ) SEPARATOR ';' ))";
    }
    public function dashboard_my_hearings($is_api = false, $hearings_date = "cw")
    {
        $_table = $this->_table;
        $this->_table = "mv_hearings AS legal_case_hearings";
        if ($is_api) {
            $user_id = $this->ci->user_logged_in_data["user_id"];
            $this->ci->load->model("user_preference");
            $this->ci->user_preference->fetch(["user_id" => $user_id, "keyName" => "language"]);
            $lang_code = substr($this->ci->user_preference->get_field("keyValue"), 0, 2);
        } else {
            $user_id = $this->logged_user_id;
            $lang_code = substr($this->ci->session->userdata("AUTH_language"), 0, 2);
        }
        switch ($hearings_date) {
            case "lw":
                $hearings_date = "YEARWEEK(legal_case_hearings.startDate) = YEARWEEK(CURRENT_DATE - INTERVAL 7 DAY)";
                break;
            case "td":
                $hearings_date = "legal_case_hearings.startDate = CURRENT_DATE";
                break;
            case "cm":
                $hearings_date = "MONTH(legal_case_hearings.startDate) = MONTH(CURRENT_DATE) AND YEAR(legal_case_hearings.startDate) = YEAR(CURRENT_DATE)";
                break;
            default:
                $hearings_date = "YEARWEEK(legal_case_hearings.startDate) = YEARWEEK(CURRENT_DATE)";
                $query = [];
                $query["select"] = ["legal_case_hearings.id, legal_case_hearings.startDate, legal_case_hearings.startTime, legal_case_hearings.legal_case_id, legal_case_hearings.caseID as case_id, legal_case_hearings.caseSubject as case_subject, legal_case_hearings.type_name_" . $lang_code . " as hearing_type, legal_case_hearings.court, legal_case_hearings.courtRegion, legal_case_hearings.legal_case_stage_name_" . $lang_code . " as stage, legal_case_hearings.opponentLawyers as opponentLawyers, legal_case_hearings.filed_on as filed_on, legal_case_hearings.reference as reference, legal_case_hearings.comments as comments", false];
                $query["join"] = ["legal_case_hearings_users", "legal_case_hearings_users.legal_case_hearing_id = legal_case_hearings.id", "left"];
                $query["where"][] = [(string) $hearings_date, NULL, false];
                $query["where"][] = ["legal_case_hearings_users.user_id", $user_id];
                $query["order_by"] = ["startDate asc"];
                $response = $this->load_all($query);
                $this->_table = $_table;
                return $response;
        }
    }
    public function dashboard_pending_updates($is_api = false)
    {
        $_table = $this->_table;
        $this->_table = "mv_hearings AS legal_case_hearings";
        if ($is_api) {
            $user_id = $this->ci->user_logged_in_data["user_id"];
            $this->ci->load->model("user_preference");
            $this->ci->user_preference->fetch(["user_id" => $user_id, "keyName" => "language"]);
            $lang_code = substr($this->ci->user_preference->get_field("keyValue"), 0, 2);
        } else {
            $user_id = $this->logged_user_id;
            $lang_code = substr($this->ci->session->userdata("AUTH_language"), 0, 2);
        }
        $query = [];
        $response = [];
        $query["select"] = ["legal_case_hearings.id, legal_case_hearings.startDate, legal_case_hearings.startTime, legal_case_hearings.legal_case_id, legal_case_hearings.caseID as case_id, legal_case_hearings.caseSubject as case_subject, legal_case_hearings.type_name_" . $lang_code . " as hearing_type, legal_case_hearings.court, legal_case_hearings.courtRegion, legal_case_hearings.legal_case_stage_name_" . $lang_code . " as stage, legal_case_hearings.opponentLawyers as opponentLawyers, legal_case_hearings.filed_on as filed_on, legal_case_hearings.reference as reference, legal_case_hearings.comments as comments", false];
        $this->ci->load->model("system_configuration");
        $excluded_statuses = $this->ci->system_configuration->get_value_by_key("hearingUpdatesReportExcludedStatuses");
        if (!empty($excluded_statuses)) {
            $query["where_not_in"][] = ["legal_cases.case_status_id", $excluded_statuses];
        }
        $business_rules = $this->ci->system_configuration->get_value_by_key("hearingUpdatesReportBusinessRules");
        $business_rules_str = "";
        if (!empty($business_rules)) {
            foreach ($business_rules as $business_rule) {
                $business_rules_str .= $business_rule . " is null or ";
            }
            $business_rules_str = " and (" . substr($business_rules_str, 0, -4) . ")";
        }
        $query["where"][] = ["startDate <= CURDATE()", NULL, false];
        $query["where"][] = ["legal_case_hearings.lawyers LIKE '%" . $this->ci->session->userdata("AUTH_userProfileName") . "%'", NULL, false];
        $query["where"][] = ["(" . "(legal_case_hearings.judged = 'no' and legal_case_hearings.judgment is null " . $business_rules_str . ")" . " OR (legal_case_hearings.judged = 'yes' and legal_case_hearings.judgment is null " . $business_rules_str . ")" . ")", NULL, false];
        $query["join"] = [["legal_cases", "legal_cases.id = legal_case_hearings.legal_case_id", "left"]];
        $query["group_by"] = ["legal_case_hearings.id"];
        $query["order_by"] = ["legal_case_hearings.startDate desc"];
        $response = parent::load_all($query);
        $stringQuery = $this->ci->db->last_query();
        $this->_table = $_table;
        return $response;
    }
    public function count_all_hearings()
    {
        $_table = $this->_table;
        $this->_table = "mv_hearings AS legal_case_hearings";
        $query["select"] = ["COUNT(0) as hearings", false];
        $response = $this->load($query)["hearings"];
        $this->_table = $_table;
        return $response;
    }
    public function get_hearings_per_month($date_filter)
    {
        $query["select"] = ["COUNT(0) count, MONTH(startDate) month"];
        $query["where"] = [["legal_case_hearings.startDate >= '" . $date_filter["from"] . "'", NULL, false], ["legal_case_hearings.startDate <= '" . $date_filter["to"] . "'", NULL, false]];
        $query["group_by"] = ["MONTH(startDate)"];
        $data = $this->load_list($query, ["key" => "month", "value" => "count"]);
        for ($i = 1; $i <= 12; $i++) {
            $return["values"][] = isset($data[$i]) ? $data[$i] : 0;
        }
        $return["names"] = $this->ci->lang->line("months_array");
        return $return;
    }
    public function get_hearings_per_assignee($date_filter)
    {
        $this->ci->load->model("user", "userfactory");
        $this->ci->user = $this->ci->userfactory->get_instance();
        $data = $this->ci->user->load_users_list();
        $response["names"] = [];
        $response["values"] = [];
        $query = [];
        $query["select"] = ["legal_case_hearings_users.user_id,  COUNT(legal_case_hearings.id) as count", false];
        $query["join"] = ["legal_case_hearings_users", "legal_case_hearings_users.legal_case_hearing_id = legal_case_hearings.id", "left"];
        $query["where"] = [["legal_case_hearings.startDate >= '" . $date_filter["from"] . "'", NULL, false], ["legal_case_hearings.startDate <= '" . $date_filter["to"] . "'", NULL, false]];
        $query["group_by"] = ["legal_case_hearings_users.user_id"];
        if ($query_data = $this->load_list($query, ["key" => "user_id", "value" => "count"])) {
            arsort($query_data);
            foreach ($query_data as $id => $value) {
                if (0 < $value) {
                    $response["names"][] = isset($data[$id]) ? $data[$id] : (isset($data[str_pad((string) $id, 10, "0", STR_PAD_LEFT)]) ? $data[str_pad((string) $id, 10, "0", STR_PAD_LEFT)] : $this->ci->lang->line("unassigned"));
                    $response["values"][] = (int) $value;
                }
            }
        }
        return $response;
    }
    public function get_hearings_per_month_per_assignee($date_filter)
    {
        $this->ci->load->model("user", "userfactory");
        $this->ci->user = $this->ci->userfactory->get_instance();
        $users = $this->ci->user->load_users_list();
        $data["categories"] = $this->ci->lang->line("months_array");
        $months = $data["categories"];
        $data["names"] = array_values($users);
        foreach ($users as $id => $value) {
            $count = 0;
            $select = "";
            foreach ($months as $i => $month) {
                $count++;
                $select .= "COUNT(CASE WHEN MONTH(startDate) = " . $count . " THEN legal_case_hearings.id END) as '" . $month . "'" . ($count < sizeof($months) ? ", " : "");
            }
            $query = [];
            $query["select"] = [$select, false];
            $query["join"] = ["legal_case_hearings_users", "legal_case_hearings_users.legal_case_hearing_id = legal_case_hearings.id", "left"];
            $query["where"] = [["startDate >= '" . $date_filter["from"] . "'", NULL, false], ["startDate <= '" . $date_filter["to"] . "'", NULL, false], ["legal_case_hearings_users.user_id", $id]];
            $data["values"][] = array_map("intval", array_values($this->load($query)));
        }
        return $data;
    }
    public function get_document_generator_data($id, $category = "")
    {
        $this->fetch($id);
        return array_merge($this->get_fields(), $this->load_hearing_data($id));
    }
    public function dashboard_recent_hearings($category = "hearings", $api_params = [])
    {
        $logged_user_id = $api_params["user_id"] ?? $this->logged_user_id;
        $_table = $this->_table;
        $this->_table = "mv_hearings AS legal_case_hearings";
        $this->ci->load->model("user_preference");
        $recent_hearings = unserialize($this->ci->user_preference->get_value_by_user("recent_cases", $logged_user_id));
        $response = [];
        if (isset($recent_hearings[$category])) {
            $recent_hearings = $recent_hearings[$category];
            foreach ($recent_hearings as $key => $val) {
                if ($val == 0) {
                    unset($recent_hearings[$key]);
                }
            }
            if (!empty($recent_hearings)) {
                $recent_hearings = implode(",", array_map("intval", $recent_hearings));
                $query["select"] = ["legal_case_hearings.id, legal_case_hearings.legal_case_id, legal_case_hearings.caseID as case_id, legal_case_hearings.caseSubject as case_subject, legal_case_hearings.startDate, legal_case_hearings.startTime, legal_case_hearings.createdOn, legal_case_hearings.modifiedOn, \"" . $category . "\" AS module", false];
                $query["where"][] = ["legal_case_hearings.id IN (" . $recent_hearings . ")", NULL, false];
                $query["order_by"] = ["FIELD(legal_case_hearings.id, " . $recent_hearings . ")"];
                $response = $this->load_all($query);
            }
        }
        $this->_table = $_table;
        return $response;
    }
}
class mysqli_Legal_case_hearing extends mysql_Legal_case_hearing
{
}
class sqlsrv_Legal_case_hearing extends mysql_Legal_case_hearing
{
    public function getBasicList($case_id = null)
    {
        $this->_table = "legal_case_hearings_full_details AS legal_case_hearings";

        // Select fields with join to hearing_types_languages
        $query["select"] = [
            "legal_case_hearings.id",
            "legal_case_hearings.hearingID",
            "legal_case_hearings.legal_case_id",
            "legal_case_hearings.createdOn",
            "legal_case_hearings.startDate",
            "legal_case_hearings.startTime",
            "legal_case_hearings.postponedDate",
            "legal_case_hearings.postponedTime",
            "hearing_types_languages.name as type_name" // From joined table
        ];

//        // Add join condition
//        $query["join"] = [
//            [
//                "hearing_types_languages",
//                "hearing_types_languages.language_id = legal_case_hearings.type AND hearing_types_languages.type = 1",
//                "left"
//            ]
//        ];

        // Filter by case ID if provided
        if ($case_id) {
            $query["where"] = [["legal_case_hearings.legal_case_id", $case_id]];
        }

        // Order by start date (newest first)
        $query["order_by"] = ["legal_case_hearings.startDate desc"];

        // Execute query

        $response["data"] = $this->load_all($query);
        echo '<pre>'; print_r($this->db->last_query()); echo '</pre>'; exit();

        return $response;

    }

    public function load_extra_users_data($hearingId)
    {
        $users = [];
        $ap_users = [];
        $data = [];
        $status = [];
        if ($hearingId < 1) {
            return $users;
        }
        $case_users = $this->ci->db->select(["user_profiles.user_id as id, ( user_profiles.firstName + ' ' + user_profiles.lastName ) as name,user_profiles.status as status", false])->join("user_profiles", "user_profiles.user_id = legal_case_hearings_users.user_id", "inner")->where(["legal_case_hearings_users.legal_case_hearing_id" => $hearingId, "legal_case_hearings_users.user_type !=" => "AP"])->get("legal_case_hearings_users");
        $case_advisor_users = $this->ci->db->select(["advisor_users.id as id, ( advisor_users.firstName +' '+ advisor_users.lastName ) as name", false])->join("advisor_users", "advisor_users.id = legal_case_hearings_users.user_id", "inner")->where(["legal_case_hearings_users.legal_case_hearing_id" => $hearingId, "legal_case_hearings_users.user_type" => "AP"])->get("legal_case_hearings_users");
        if (!$case_users->num_rows() && !$case_advisor_users->num_rows()) {
            return $users;
        }
        foreach ($case_users->result() as $user) {
            $users[(string) $user->id] = $user->name;
        }
        foreach ($case_advisor_users->result() as $user) {
            $ap_users[(string) $user->id] = $user->name;
        }
        foreach ($case_users->result() as $user) {
            $status[(string) $user->id] = $user->status;
        }
        $data[0] = $users;
        $data[1] = $status;
        $data[2] = $ap_users;
        return $data;
    }
    public function k_load_all_hearings($filter, $sortable, $legalCaseID = 0, $page_number = "", $hijri_calendar_enabled = false, $language = false)
    {
        $this->ci->db->_protect_identifiers = false;
        $this->ci->db->_force_escape_string_values = true;
        $this->ci->load->model("language");
        $lang_id = $this->ci->language->get_id_by_session_lang($language);
        $response = [];
        $language = $this->ci->session->userdata("AUTH_language") ? strtolower(substr($this->ci->session->userdata("AUTH_language"), 0, 2)) : $language;
        $table = $this->_table;
        $this->_table = "legal_case_hearings_full_details AS legal_case_hearings";
        $query = ["select" => ["legal_case_hearings.id"]];
        $query["join"] = [["legal_case_hearings_users", "legal_case_hearings_users.legal_case_hearing_id = legal_case_hearings.id", "left"]];
        if (is_array($filter) && isset($filter["filters"])) {
            foreach ($filter["filters"] as $_filter) {
                $this->prep_k_filter($_filter, $query, $filter["logic"], $hijri_calendar_enabled);
            }
            unset($_filter);
        }
        if ($legalCaseID) {
            $query["where"][] = ["legal_case_hearings.legal_case_id", $legalCaseID];
        }
        if (empty($query["where"])) {
            unset($query["where"]);
        }
        $query["group_by"] = ["legal_case_hearings.id"];
        $ids = $this->load_all($query, "query");
        $this->_table = $table;
        $query = [];
        $query["select"] = ["legal_case_hearings.summaryToClient,legal_case_hearings.id, ('H'+CAST( legal_case_hearings.id AS nvarchar )) as hearingID, legal_case_hearings.stage AS stage_id, legal_case_hearings.clientReportEmailSent, legal_case_hearings.verifiedSummary,legal_cases.latest_development,legal_case_hearings.createdOn,CASE WHEN clients.company_id IS NULL THEN cont.foreignFirstName + ' ' + cont.foreignLastName ELSE comp.foreignName END as client_foreign_name,(select top 1 max(previous.startDate) from legal_case_hearings as previous where previous.id != legal_case_hearings.id and legal_case_hearings.stage = previous.stage and previous.startDate < legal_case_hearings.startDate and previous.legal_case_id = legal_case_hearings.legal_case_id) as previousHearingDate," . "legal_case_hearings.legal_case_id,legal_case_hearings.task_id, legal_case_hearings.startDate,legal_case_hearings.startTime,legal_case_hearings.postponedDate,legal_case_hearings.postponedTime,legal_case_hearings.summary, CASE WHEN LEN(legal_cases.subject) > 60 THEN (SUBSTRING(legal_cases.subject, 1, 60) + '...') ELSE legal_cases.subject END AS caseSubject, legal_cases.subject as fullCaseSubject,lccplen.name as clientPosition_en,lccplfr.name as clientPosition_fr,lccplar.name as clientPosition_ar,lccplsp.name as clientPosition_sp,lccpl" . $language . ".name AS clientPosition,types.name as type_name, stages.name as stage_name, legal_case_hearings.judgment, legal_case_hearings.judged, legal_case_hearings.comments, legal_case_hearings.reasons_of_postponement, case_types.name as areaOfPractice," . "ld.status as stage_status, legal_cases.internalReference AS caseReference,('M'+CAST( legal_case_hearings.legal_case_id AS nvarchar )) as caseID, (modified.firstName + ' ' + modified.lastName) AS modifiedByName, (created.firstName + ' ' + created.lastName) AS createdByName, legal_case_hearings.modifiedOn, legal_case_hearings.modifiedBy," . "opponents = STUFF(\r\n\t\t\t(SELECT ', ' +\r\n\t\t\t(\r\n                            CASE WHEN opponent_positions.name IS NOT NULL\r\n                                THEN\r\n                                    CASE WHEN opponents.company_id IS NOT NULL\r\n                                        THEN\r\n                                            (opponentCompany.name + ' - ' + opponent_positions.name)\r\n                                        ELSE\r\n                                            opponentContact.firstName + ' ' + opponentContact.lastName + ' - ' + opponent_positions.name\r\n                                    END\r\n                                ELSE\r\n                                    CASE WHEN opponents.company_id IS NOT NULL\r\n                                        THEN\r\n                                            opponentCompany.name\r\n                                        ELSE\r\n                                            opponentContact.firstName + ' ' + opponentContact.lastName\r\n                                    END\r\n                            END\r\n\t\t\t)\r\n\t\t\t FROM legal_case_litigation_stages_opponents\r\n\t\t\t INNER JOIN opponents ON opponents.id = legal_case_litigation_stages_opponents.opponent_id\r\n\t\t\t LEFT JOIN companies AS opponentCompany ON opponentCompany.id = opponents.company_id AND opponents.company_id IS NOT NULL\r\n\t\t\t LEFT JOIN contacts AS opponentContact ON opponentContact.id = opponents.contact_id AND opponents.contact_id IS NOT NULL\r\n                         LEFT JOIN legal_case_opponent_position_languages AS opponent_positions ON opponent_positions.legal_case_opponent_position_id = legal_case_litigation_stages_opponents.opponent_position and opponent_positions.language_id = '" . $lang_id . "'\r\n\t\t\t WHERE legal_case_litigation_stages_opponents.stage = legal_case_hearings.stage\r\n\t\t\tFOR XML PATH('')), 1, 1, '')," . "opponent_foreign_name = STUFF((SELECT ', ' +\r\n                (\r\n                    CASE WHEN opponent_positions.name != '' THEN\r\n                    (\r\n                        CASE WHEN opponents.company_id IS NOT NULL\r\n                        THEN (CASE WHEN opponentCompany.foreignName IS NULL THEN opponentCompany.name ELSE opponentCompany.foreignName END) + ' - ' + opponent_positions.name\r\n                        ELSE (CASE WHEN opponentContact.foreignFirstName IS NULL THEN opponentContact.firstName ELSE opponentContact.foreignFirstName END) + ' ' + (CASE WHEN opponentContact.foreignLastName IS NULL THEN opponentContact.lastName ELSE opponentContact.foreignLastName END) + ' - ' + opponent_positions.name END\r\n                    )\r\n                    ELSE\r\n                    (\r\n                        CASE WHEN opponents.company_id IS NOT NULL\r\n                        THEN (CASE WHEN opponentCompany.foreignName IS NULL THEN opponentCompany.name ELSE opponentCompany.foreignName END)\r\n                        ELSE (CASE WHEN opponentContact.foreignFirstName IS NULL THEN opponentContact.firstName ELSE opponentContact.foreignFirstName END) + ' ' + (CASE WHEN opponentContact.foreignLastName IS NULL THEN opponentContact.lastName ELSE opponentContact.foreignLastName END) END\r\n                    )\r\n                    END\r\n                )\r\n                 FROM legal_case_litigation_stages_opponents\r\n                 INNER JOIN opponents ON opponents.id = legal_case_litigation_stages_opponents.opponent_id\r\n                 LEFT JOIN companies AS opponentCompany ON opponentCompany.id = opponents.company_id AND opponents.company_id IS NOT NULL\r\n                 LEFT JOIN contacts AS opponentContact ON opponentContact.id = opponents.contact_id AND opponents.contact_id IS NOT NULL\r\n                 LEFT JOIN legal_case_opponent_position_languages AS opponent_positions ON opponent_positions.legal_case_opponent_position_id = legal_case_litigation_stages_opponents.opponent_position and opponent_positions.language_id = '" . $lang_id . "'\r\n                 WHERE legal_case_litigation_stages_opponents.stage = legal_case_hearings.stage\r\n                FOR XML PATH('')), 1, 1, '')," . "clients = STUFF((SELECT ' ; ' + clients_view.name FROM clients_view WHERE clients_view.id = legal_cases.client_id AND clients_view.model = 'clients' FOR XML PATH('')), 1, 3, '')," . "judges = STUFF((SELECT ' ; ' + ( CASE WHEN contJud.father!='' THEN contJud.firstName + ' '+ contJud.father + ' ' + contJud.lastName ELSE contJud.firstName+' '+contJud.lastName END ) FROM contacts AS contJud INNER JOIN legal_case_stage_contacts lchcj ON lchcj.stage = legal_case_hearings.stage AND lchcj.contact_type = 'judge' AND contJud.id = lchcj.contact FOR XML PATH('')), 1, 3, '')," . "opponentLawyers = STUFF((SELECT ' ; ' + ( CASE WHEN contOppLaw.father!='' THEN contOppLaw.firstName + ' '+ contOppLaw.father + ' ' + contOppLaw.lastName ELSE contOppLaw.firstName+' '+contOppLaw.lastName END ) FROM contacts AS contOppLaw INNER JOIN legal_case_stage_contacts lchcol ON lchcol.stage = legal_case_hearings.stage AND lchcol.contact_type = 'opponent-lawyer' AND contOppLaw.id = lchcol.contact FOR XML PATH('')), 1, 3, '')," . "lawyers = " . "COALESCE (STUFF((SELECT' ; ' + (userLaw.firstName + ' ' + userLaw.lastName + CASE WHEN userLaw.status = 'Inactive' THEN ' (Inactive)'ELSE '' END) FROM user_profiles AS userLaw INNER JOIN legal_case_hearings_users ON legal_case_hearings_users.legal_case_hearing_id = legal_case_hearings.id AND userLaw.user_id = legal_case_hearings_users.user_id AND legal_case_hearings_users.user_type != 'AP' FOR xml PATH ('')), 1, 3, '') ,'')" . " + CASE WHEN (COALESCE (STUFF((SELECT ' ; ' + (userLaw.firstName + ' ' + userLaw.lastName + CASE  WHEN userLaw.status = 'Inactive' THEN ' (Inactive)'  ELSE '' END) FROM user_profiles AS userLaw INNER JOIN legal_case_hearings_users   ON legal_case_hearings_users.legal_case_hearing_id = legal_case_hearings.id AND userLaw.user_id = legal_case_hearings_users.user_id  AND legal_case_hearings_users.user_type != 'AP'  FOR xml PATH ('')), 1, 3, '') ,'') = '' or " . " COALESCE(STUFF((SELECT  ' ; ' + (userAdv.firstName + ' ' + userAdv.lastName + CASE WHEN userAdv.status = 'Inactive' THEN ' (Inactive)'ELSE ''  END) FROM advisor_users AS userAdv INNER JOIN legal_case_hearings_users ON legal_case_hearings_users.legal_case_hearing_id = legal_case_hearings.id AND userAdv.id = legal_case_hearings_users.user_id AND legal_case_hearings_users.user_type = 'AP' FOR xml PATH ('')), 1, 3, '') ,'') = '' ) THEN" . "   '' ELSE ','  END +" . "COALESCE(STUFF((SELECT  ' ; ' + (userAdv.firstName + ' ' + userAdv.lastName + CASE WHEN userAdv.status = 'Inactive' THEN ' (Inactive)'ELSE ''  END) FROM advisor_users AS userAdv INNER JOIN legal_case_hearings_users ON legal_case_hearings_users.legal_case_hearing_id = legal_case_hearings.id AND userAdv.id = legal_case_hearings_users.user_id AND legal_case_hearings_users.user_type = 'AP' FOR xml PATH ('')), 1, 3, '') ,'')  ," . "court_types.name AS courtType, court_degrees.name AS courtDegree, court_regions.name AS courtRegion, courts.name AS court," . "reference = STUFF((SELECT ' ; ' + lcler.number FROM legal_case_litigation_external_references lcler WHERE lcler.stage=ld.id FOR XML PATH('')), 1, 3, '')," . "containerID = STUFF((SELECT ' ; ' + lccfd.containerId FROM legal_case_containers_full_details as lccfd where lccfd.legal_case_id = legal_case_hearings.legal_case_id FOR XML PATH('')), 1, 3, '')", false];
        $query["join"] = [["legal_case_litigation_details as ld", "ld.legal_case_id = legal_case_hearings.legal_case_id AND  ld.id = legal_case_hearings.stage", "left"], ["stage_statuses_languages as stage_statuses", "stage_statuses.status = ld.status and stage_statuses.language_id = '" . $lang_id . "'", "left"], ["hearing_types_languages as types", "types.type = legal_case_hearings.type and types.language_id = '" . $lang_id . "'", "left"], ["legal_case_stage_languages as stages", "stages.legal_case_stage_id = ld.legal_case_stage and stages.language_id = '" . $lang_id . "'", "left"], ["legal_cases", "legal_cases.id = legal_case_hearings.legal_case_id", "left"], ["courts", "courts.id = ld.court_id", "left"], ["court_types", "court_types.id = ld.court_type_id", "left"], ["court_degrees", "court_degrees.id = ld.court_degree_id", "left"], ["court_regions", "court_regions.id = ld.court_region_id", "left"], ["case_types", "case_types.id = legal_cases.case_type_id", "left"], ["user_profiles created", "created.user_id = legal_case_hearings.createdBy", "left"], ["user_profiles modified", "modified.user_id = legal_case_hearings.modifiedBy", "left"], ["legal_case_client_position_languages lccplen", "lccplen.legal_case_client_position_id = ld.client_position AND lccplen.language_id = '1'", "left"], ["legal_case_client_position_languages lccplar", "lccplar.legal_case_client_position_id = ld.client_position AND lccplar.language_id = '2'", "left"], ["legal_case_client_position_languages lccplfr", "lccplfr.legal_case_client_position_id = ld.client_position AND lccplfr.language_id = '3'", "left"], ["legal_case_client_position_languages lccplsp", "lccplsp.legal_case_client_position_id = ld.client_position AND lccplsp.language_id = '4'", "left"], ["clients", "clients.id = legal_cases.client_id", "left"], ["companies comp", "comp.id = clients.company_id", "left"], ["contacts cont", "cont.id = clients.contact_id", "left"]];
        $query["where"][] = ["legal_case_hearings.id IN (" . $ids . ")"];
        if (is_array($sortable) && !empty($sortable)) {
            foreach ($sortable as $_sort) {
                $query["order_by"][] = [$_sort["field"], $_sort["dir"]];
            }
        } else {
            $query["order_by"] = ["legal_case_hearings.startDate desc"];
        }
        if ($page_number != "") {
            $query["limit"] = [10000, ($page_number - 1) * 10000];
        }
        if ($limit = $this->ci->input->post("take", true)) {
            $query["limit"] = [$this->ci->input->post("take", true), $this->ci->input->post("skip", true)];
        }
        $response["data"] = $this->load_all($query);
        $response["totalRows"] = $this->count_total_matching_rows($query);
        $this->ci->db->_protect_identifiers = true;
        $this->ci->db->_force_escape_string_values = false;
        return $response;
    }
    public function load_hearings_roll_session_report($filter, $sortable, $filter_type = "", $hijri_calendar_enabled = false, $page_number = "")
    {
        $this->ci->db->_protect_identifiers = false;
        $this->ci->db->_force_escape_string_values = true;
        $this->ci->load->model("custom_field", "custom_fieldfactory");
        $this->ci->custom_field = $this->ci->custom_fieldfactory->get_instance("custom_fieldfactory");
        $language = strtolower(substr($this->ci->session->userdata("AUTH_language"), 0, 2));
        $this->ci->load->model("legal_case", "legal_casefactory");
        $this->ci->legal_case = $this->ci->legal_casefactory->get_instance();
        $table = $this->_table;
        $this->ci->load->model("language");
        $langId = $this->ci->language->get_id_by_session_lang();
        $this->_table = "legal_case_hearings_full_details AS legal_case_hearings";
        $query = [];
        $response = [];
        $query = ["select" => ["legal_case_hearings.id"]];
        $query["join"] = [["legal_case_hearings_users", "legal_case_hearings_users.legal_case_hearing_id = legal_case_hearings.id", "left"], ["legal_cases", "legal_cases.id = legal_case_hearings.legal_case_id", "left"], ["clients", "clients.id = legal_cases.client_id", "left"], ["companies comp", "comp.id = clients.company_id", "left"], ["contacts cont", "cont.id = clients.contact_id", "left"]];
        if (is_array($filter)) {
            if (isset($filter["filters"])) {
                foreach ($filter["filters"] as $_filter) {
                    $this->prep_k_filter($_filter, $query, $filter["logic"], $hijri_calendar_enabled);
                }
                unset($_filter);
            }
            if (isset($filter["customFields"])) {
                $this->ci->custom_field->prep_custom_field_filters($this->modelName, $filter["customFields"], $query, "legal_case_id", "legal_case_hearings");
            }
        }
        $ids = $this->load_all($query, "query");
        $query = [];
        $select = "SELECT legal_case_hearings.*, legal_case_hearings.legal_case_stage as legal_case_stage_id, CASE WHEN clients.company_id IS NULL THEN (CASE WHEN con.father!='' THEN con.firstName + ' ' + con.father + ' ' + con.lastName ELSE con.firstName+' '+con.lastName END) ELSE com.name END AS clients, ( SUBSTRING(legal_case_hearings.caseSubject, 1, 60) + '...' ) AS caseSubject,legal_case_hearings.caseReference as CaseInternalReference, clientPosition_" . $language . " AS clientPosition,legal_case_stage_languages.name as hearing_stage,legal_case_hearings.caseValue,SUBSTRING((select  ((CASE WHEN conExtLaw.father!='' THEN conExtLaw.firstName + ' '+ conExtLaw.father + ' ' + conExtLaw.lastName ELSE conExtLaw.firstName+' '+conExtLaw.lastName END)+',') from legal_cases_contacts lccExtLaw LEFT JOIN contacts conExtLaw ON conExtLaw.id = lccExtLaw.contact_id where lccExtLaw.case_id = legal_case_hearings.legal_case_id AND lccExtLaw.contactType = 'external lawyer' FOR XML PATH('')), 0, 2000)  as OutsourcingLawyers, legal_case_hearings.statusComments,legal_case_hearings.latest_development, types.name as type_name," . "opponents = STUFF(\r\n\t\t\t(SELECT ', ' +\r\n\t\t\t(\r\n                            CASE WHEN opponent_positions.name IS NOT NULL\r\n                                THEN\r\n                                    CASE WHEN opponents.company_id IS NOT NULL\r\n                                        THEN\r\n                                            (opponentCompany.name + ' - ' + opponent_positions.name)\r\n                                        ELSE\r\n                                            opponentContact.firstName + ' ' + opponentContact.lastName + ' - ' + opponent_positions.name\r\n                                    END\r\n                                ELSE\r\n                                    CASE WHEN opponents.company_id IS NOT NULL\r\n                                        THEN\r\n                                            opponentCompany.name\r\n                                        ELSE\r\n                                            opponentContact.firstName + ' ' + opponentContact.lastName\r\n                                    END\r\n                            END\r\n\t\t\t)\r\n\t\t\t FROM legal_case_litigation_stages_opponents\r\n\t\t\t INNER JOIN opponents ON opponents.id = legal_case_litigation_stages_opponents.opponent_id\r\n\t\t\t LEFT JOIN companies AS opponentCompany ON opponentCompany.id = opponents.company_id AND opponents.company_id IS NOT NULL\r\n\t\t\t LEFT JOIN contacts AS opponentContact ON opponentContact.id = opponents.contact_id AND opponents.contact_id IS NOT NULL\r\n                         LEFT JOIN legal_case_opponent_position_languages AS opponent_positions ON opponent_positions.legal_case_opponent_position_id = legal_case_litigation_stages_opponents.opponent_position and opponent_positions.language_id = '" . $langId . "'\r\n\t\t\t WHERE legal_case_litigation_stages_opponents.stage = legal_case_hearings.stage\r\n\t\t\tFOR XML PATH('')), 1, 1, ''),\r\n            CASE WHEN clients.company_id IS NULL THEN con.foreignFirstName + ' ' + con.foreignLastName ELSE com.foreignName END as client_foreign_name," . "opponent_foreign_name = STUFF((SELECT ', ' +\r\n                (\r\n                    CASE WHEN opponent_positions.name != '' THEN\r\n                    (\r\n                        CASE WHEN opponents.company_id IS NOT NULL\r\n                        THEN (CASE WHEN opponentCompany.foreignName IS NULL THEN opponentCompany.name ELSE opponentCompany.foreignName END) + ' - ' + opponent_positions.name\r\n                        ELSE (CASE WHEN opponentContact.foreignFirstName IS NULL THEN opponentContact.firstName ELSE opponentContact.foreignFirstName END) + ' ' + (CASE WHEN opponentContact.foreignLastName IS NULL THEN opponentContact.lastName ELSE opponentContact.foreignLastName END) + ' - ' + opponent_positions.name END\r\n                    )\r\n                    ELSE\r\n                    (\r\n                        CASE WHEN opponents.company_id IS NOT NULL\r\n                        THEN (CASE WHEN opponentCompany.foreignName IS NULL THEN opponentCompany.name ELSE opponentCompany.foreignName END)\r\n                        ELSE (CASE WHEN opponentContact.foreignFirstName IS NULL THEN opponentContact.firstName ELSE opponentContact.foreignFirstName END) + ' ' + (CASE WHEN opponentContact.foreignLastName IS NULL THEN opponentContact.lastName ELSE opponentContact.foreignLastName END) END\r\n                    )\r\n                    END\r\n                )\r\n                 FROM legal_case_litigation_stages_opponents\r\n                 INNER JOIN opponents ON opponents.id = legal_case_litigation_stages_opponents.opponent_id\r\n                 LEFT JOIN companies AS opponentCompany ON opponentCompany.id = opponents.company_id AND opponents.company_id IS NOT NULL\r\n                 LEFT JOIN contacts AS opponentContact ON opponentContact.id = opponents.contact_id AND opponents.contact_id IS NOT NULL\r\n                 LEFT JOIN legal_case_opponent_position_languages AS opponent_positions ON opponent_positions.legal_case_opponent_position_id = legal_case_litigation_stages_opponents.opponent_position and opponent_positions.language_id = '" . $langId . "'\r\n                 WHERE legal_case_litigation_stages_opponents.stage = legal_case_hearings.stage\r\n                FOR XML PATH('')), 1, 1, '')";
        if (is_array($filter)) {
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
                if (!isset($where)) {
                    $where = " WHERE ";
                } else {
                    $where .= " AND ";
                }
                foreach ($query["where_in"] as $key => $condition) {
                    $where .= count($condition) == 2 ? $condition[0] . " " . " IN (" . implode(",", $condition[1]) . ")" : $condition[0];
                    if (count($query["where_in"]) - 1 !== $key) {
                        $where .= " AND ";
                    }
                }
            }
        }
        if ($filter_type == "weekly") {
            $systemPreferences = $this->ci->session->userdata("systemPreferences");
            $businessWeekEquals = $systemPreferences["businessWeekEquals"];
            $sysDaysOff = $systemPreferences["sysDaysOff"];
            $days = ["Mon", "Tue", "Wed", "Thu", "Fri", "Sat", "Sun"];
            if (isset($businessWeekEquals) && !empty($businessWeekEquals) && isset($sysDaysOff) && !empty($sysDaysOff)) {
                $offDays = explode(", ", $sysDaysOff);
                $workingDays = array_diff($days, $offDays);
            } else {
                $workingDays = $days;
            }
            $dates = [];
            foreach ($workingDays as $key => $value) {
                $dates[] = date("Y-m-d", strtotime($value . " this week"));
            }
            $ranges[] = $dates[0];
            $ranges[] = $dates[sizeof($dates) - 1];
            if (isset($where)) {
                $where .= " AND";
            } else {
                $where = " WHERE ";
            }
            $query["where"][] = ["(legal_case_hearings.startDate between '" . $ranges[0] . "' and '" . $ranges[1] . "' or legal_case_hearings.postponedDate between '" . $ranges[0] . "' and '" . $ranges[1] . "')", NULL, false];
            $where .= " (legal_case_hearings.startDate between '" . $ranges[0] . "' and '" . $ranges[1] . "' or legal_case_hearings.postponedDate between '" . $ranges[0] . "' and '" . $ranges[1] . "')";
        } else {
            if ($filter_type == "monthly") {
                $query_date = date("Y-m-d");
                $ranges[] = date("Y-m-01", strtotime($query_date));
                $ranges[] = date("Y-m-t", strtotime($query_date));
                if (isset($where)) {
                    $where .= " AND";
                } else {
                    $where = " WHERE ";
                }
                $query["where"][] = ["(legal_case_hearings.startDate between '" . $ranges[0] . "' and '" . $ranges[1] . "' or legal_case_hearings.postponedDate between '" . $ranges[0] . "' and '" . $ranges[1] . "')", NULL, false];
                $where .= " (legal_case_hearings.startDate between '" . $ranges[0] . "' and '" . $ranges[1] . "' or legal_case_hearings.postponedDate between '" . $ranges[0] . "' and '" . $ranges[1] . "')";
            }
        }
        $query["where"][] = ["legal_case_hearings.id IN (" . $ids . ")", NULL, false];
        $this->prep_query($query);
        $response["totalRows"] = $this->ci->db->count_all_results($this->_table);
        $query = [];
        $this->_table = $table;
        $order_by = " ORDER BY ";
        if (is_array($sortable) && !empty($sortable)) {
            foreach ($sortable as $index => $_sort) {
            }
            $order_by .= $_sort["field"] . " " . $_sort["dir"] . (count($sortable) - 1 !== $index ? ", " : "");
        } else {
            $order_by .= "legal_case_hearings.startDate asc";
        }
        $parameters = $this->load_custom_fields();
        if (isset($parameters["Field"])) {
            $count = 0;
            $select .= ",";
            foreach ($parameters["Field"] as $id => $value) {
                $select .= $value . " as custom_" . $id . ($count !== count($parameters["Field"]) - 1 ? "," : "");
                $count++;
            }
        }
        $select .= " from legal_case_hearings_full_details AS legal_case_hearings";
        $select .= " LEFT JOIN legal_case_stage_languages ON legal_case_stage_languages.legal_case_stage_id = legal_case_hearings.legal_case_stage and legal_case_stage_languages.language_id = '" . $langId . "'";
        $select .= " LEFT JOIN hearing_types_languages as types ON types.type = legal_case_hearings.type and types.language_id = '" . $langId . "'";
        $select .= " LEFT JOIN legal_cases ON legal_cases.id = legal_case_hearings.legal_case_id";
        $select .= " LEFT JOIN clients ON clients.id = legal_cases.client_id";
        $select .= " LEFT JOIN companies com ON com.id = clients.company_id";
        $select .= " LEFT JOIN contacts con ON con.id = clients.contact_id";
        $where_ids = " (legal_case_hearings.id IN (" . $ids . "))";
        if (isset($where)) {
            $where .= " AND " . $where_ids;
            $select .= $where;
        } else {
            $select .= " WHERE " . $where_ids;
        }
        $select .= $order_by;
        if ($page_number != "") {
            $limit_skip = ($page_number - 1) * 10000;
            $select .= " OFFSET  " . $limit_skip . " ROWS FETCH NEXT 10000 ROWS ONLY";
        } else {
            if ($limit = $this->ci->input->post("take", true)) {
                $select .= " OFFSET  " . $this->ci->input->post("skip", true) . " ROWS FETCH NEXT " . $limit . " ROWS ONLY";
            }
        }
        $response["data"] = $this->ci->db->query($select)->result_array();
        $this->ci->db->_protect_identifiers = true;
        $this->ci->db->_force_escape_string_values = false;
        return $response;
    }
    private function load_custom_fields()
    {
        $custom_fields = $this->ci->custom_field->load_list_per_language($this->ci->legal_case->modelName);
        $parameters = [];
        foreach ($custom_fields as $field_data) {
            switch ($field_data["type"]) {
                case "date":
                    $parameters["Field"][$field_data["id"]] = "(SELECT cfv.date_value FROM custom_field_values AS cfv WHERE legal_case_hearings.legal_case_id = cfv.recordId AND cfv.custom_field_id = " . $field_data["id"] . ")";
                    break;
                case "date_time":
                    $parameters["Field"][$field_data["id"]] = "(SELECT FORMAT(cfv.date_value, N'yyyy-MM-dd') + ' ' + FORMAT(cfv.time_value, N'hh\\:mm') FROM custom_field_values AS cfv where legal_case_hearings.legal_case_id = cfv.recordId AND cfv.custom_field_id = " . $field_data["id"] . ")";
                    break;
                case "lookup":
                    $lookup_type_properties = $this->ci->custom_field->get_lookup_type_properties($field_data["type_data"]);
                    $lookup_displayed_columns_table = $lookup_type_properties["external_data"] ? "ltedt" : "ltt";
                    $lookup_external_data_join = $lookup_type_properties["external_data"] ? "LEFT JOIN " . $lookup_type_properties["external_data_properties"]["table"] . " ltedt ON ltedt." . $lookup_type_properties["external_data_properties"]["foreign_key"] . " = ltt.id" : "";
                    $last_segment = isset($lookup_type_properties["display_properties"]["third_segment"]["column_name"]) ? $lookup_displayed_columns_table . "." . $lookup_type_properties["display_properties"]["second_segment"]["column_name"] . ",' '," . $lookup_displayed_columns_table . "." . $lookup_type_properties["display_properties"]["third_segment"]["column_name"] : $lookup_displayed_columns_table . "." . $lookup_type_properties["display_properties"]["second_segment"]["column_name"];
                    $parameters["Field"][$field_data["id"]] = "\r\n                        (\r\n                              STUFF((\r\n                                  SELECT ',' + " . $lookup_displayed_columns_table . "." . $lookup_type_properties["display_properties"]["first_segment"]["column_name"] . ", ' '," . $last_segment . "\r\n                                  FROM custom_field_values cfv\r\n                                  left join " . $lookup_type_properties["table"] . " ltt on CAST(ltt.id AS VARCHAR) = cfv.text_value " . $lookup_external_data_join . "\r\n                                  where cfv.recordId = legal_case_hearings.legal_case_id  and custom_field_id = " . $field_data["id"] . "\r\n                              FOR XML PATH('')), 1, 1, '')\r\n                        )";
                    break;
                case "list":
                    $parameters["Field"][$field_data["id"]] = "( \r\n                         STUFF((SELECT ',' + cfv.text_value FROM custom_field_values cfv WHERE cfv.recordId = legal_case_hearings.legal_case_id AND cfv.custom_field_id = " . $field_data["id"] . " FOR XML PATH ('')), 1, 1, ''))";
                    break;
                default:
                    $parameters["Field"][$field_data["id"]] = "(SELECT cfv.text_value FROM custom_field_values as cfv WHERE legal_case_hearings.legal_case_id = cfv.recordId AND cfv.custom_field_id = " . $field_data["id"] . ")";
            }
        }
        return $parameters;
    }
    public function api_load_all_hearings($userLang, $take = 20, $skip = 0, $term = "", $search_filters, $hijri_calendar_enabled = false, $order = "desc")
    {
        $table = $this->_table;
        $this->_table = "legal_case_hearings_full_details AS legal_case_hearings";
        $query = [];
        $response = [];
        $query["select"] = ["legal_case_hearings.*, ( SUBSTRING(caseSubject, 1, 60) + '...' ) AS caseSubject, clientPosition_" . $userLang . " AS clientPosition, legal_case_hearings.judged", false];
        $query["join"] = [["legal_cases", "legal_case_hearings.legal_case_id = legal_cases.id", "left"]];
        if ($term != "") {
            $term = $this->ci->db->escape_like_str($term);
            $query["where"][] = [" ( legal_case_hearings.summary LIKE '%" . $term . "%' or legal_case_hearings.reference LIKE '%" . $term . "%'\r\n             or legal_case_hearings.caseSubject LIKE '%" . $term . "%' or legal_cases.description LIKE '%" . $term . "%' )", NULL, false];
        }
        $query = $this->filter_builder($query, $search_filters);
        $this->prep_query($query);
        $response["totalRows"] = $this->ci->db->get($this->get("_table"))->num_rows();
        $query["order_by"] = ["legal_case_hearings.startDate " . $order];
        $query["limit"] = [$take, $skip];
        $response["data"] = $this->load_all($query);
        if ($hijri_calendar_enabled) {
            foreach ($response["data"] as $key => $hearing) {
                $response["data"][$key]["startDate"] = gregorianToHijri($hearing["startDate"], "Y-m-d");
                $response["data"][$key]["postponedDate"] = $hearing["postponedDate"] ? gregorianToHijri($hearing["postponedDate"], "Y-m-d") : NULL;
            }
        }
        $this->_table = $table;
        return $response;
    }
    public function load_related_hearing_lawyers($hearingId)
    {
        $users = [];
        if ($hearingId < 1) {
            return $users;
        }
        $case_users = $this->ci->db->select(["user_profiles.user_id as id, users.email as email, ( user_profiles.firstName + ' ' + user_profiles.lastName ) as name", false])->join("user_profiles", "user_profiles.user_id = legal_case_hearings_users.user_id", "inner")->join("users", "users.id = legal_case_hearings_users.user_id", "inner")->where("legal_case_hearings_users.legal_case_hearing_id", $hearingId)->where("user_profiles.status", "Active")->get("legal_case_hearings_users");
        if (!$case_users->num_rows()) {
            return $users;
        }
        foreach ($case_users->result() as $user) {
            $users[(string) $user->id] = ["name" => $user->name, "email" => $user->email];
        }
        return $users;
    }
    public function load_daily_agenda_hearings()
    {
        $query = [];
        $query["select"] = ["legal_case_hearings.startDate, legal_case_hearings.startTime, legal_case_hearings.legal_case_id, users.email, legal_cases.subject", false];
        $query["join"] = [["legal_case_hearings_users", "legal_case_hearings.id = legal_case_hearings_users.legal_case_hearing_id"], ["legal_cases", "legal_case_hearings.legal_case_id = legal_cases.id"], ["users", "users.id = legal_case_hearings_users.user_id"]];
        $query["where"][] = ["CONVERT(varchar, getdate(), 23) = legal_case_hearings.startDate AND legal_cases.isDeleted = 0", NULL, false];
        $query["order_by"] = ["startDate desc"];
        return $this->load_all($query);
    }
    public function load_hearing_data($id)
    {
        $language = strtolower(substr($this->ci->session->userdata("AUTH_language"), 0, 2));
        $this->ci->load->model("language");
        $lang_id = $this->ci->language->get_id_by_session_lang();
        $table = $this->_table;
        $this->_table = "legal_case_hearings_full_details AS legal_case_hearings";
        $query = $response = [];
        $query["select"] = ["legal_case_hearings.id,legal_case_hearings.legal_case_id,legal_case_hearings.caseReference as internalReference,\r\n        CAST( legal_case_hearings.startDate AS varchar ) AS startDate, legal_case_hearings.startTime,\r\n        COALESCE(CAST( legal_case_hearings.postponedDate AS varchar ), '') + ' ' + COALESCE(legal_case_hearings.postponedTime, '') as postponed_date,\r\n        legal_case_hearings.summary,legal_case_hearings.reference,legal_case_hearings.caseID,legal_case_hearings.clients,legal_case_hearings.judges, legal_case_hearings.opponentLawyers,legal_case_hearings.lawyers,legal_case_hearings.courtType,legal_case_hearings.courtDegree,legal_case_hearings.courtRegion,legal_case_hearings.court, legal_case_hearings.caseSubject, clientPosition_" . $language . " AS clientPosition,types.name as type_name,stage_statuses.name as stage_status, legal_case_hearings.judgment, stages.name as stage_name, legal_case_hearings.sentenceDate, legal_case_hearings.case_description," . "opponents = STUFF(\r\n\t\t\t(SELECT ', ' +\r\n\t\t\t(\r\n                            CASE WHEN opponent_positions.name IS NOT NULL\r\n                                THEN\r\n                                    CASE WHEN legal_case_opponents.opponent_member_type = 'company'\r\n                                        THEN\r\n                                            (opponentCompany.name + ' - ' + opponent_positions.name)\r\n                                        ELSE\r\n                                            opponentContact.firstName + ' ' + opponentContact.lastName + ' - ' + opponent_positions.name\r\n                                    END\r\n                                ELSE\r\n                                    CASE WHEN legal_case_opponents.opponent_member_type = 'company'\r\n                                        THEN\r\n                                            opponentCompany.name\r\n                                        ELSE\r\n                                            opponentContact.firstName + ' ' + opponentContact.lastName\r\n                                    END\r\n                            END\r\n\t\t\t)\r\n\t\t\t FROM legal_case_opponents\r\n\t\t\t INNER JOIN opponents ON opponents.id = legal_case_opponents.opponent_id\r\n\t\t\t LEFT JOIN companies AS opponentCompany ON opponentCompany.id = opponents.company_id AND legal_case_opponents.opponent_member_type = 'company'\r\n\t\t\t LEFT JOIN contacts AS opponentContact ON opponentContact.id = opponents.contact_id AND legal_case_opponents.opponent_member_type = 'contact'\r\n                         LEFT JOIN legal_case_opponent_position_languages AS opponent_positions ON opponent_positions.legal_case_opponent_position_id = legal_case_opponents.opponent_position and opponent_positions.language_id = '" . $lang_id . "'\r\n\t\t\t WHERE legal_case_opponents.case_id = legal_case_hearings.legal_case_id\r\n\t\t\tFOR XML PATH('')), 1, 1, '')," . "stage_opponents = STUFF(\r\n\t\t\t(SELECT ', ' +\r\n\t\t\t(\r\n                            CASE WHEN opponent_positions.name IS NOT NULL\r\n                                THEN\r\n                                    CASE WHEN opponents.contact_id IS NULL\r\n                                        THEN\r\n                                            (opponentCompany.name + ' - ' + opponent_positions.name)\r\n                                        ELSE\r\n                                            opponentContact.firstName + ' ' + opponentContact.lastName + ' - ' + opponent_positions.name\r\n                                    END\r\n                                ELSE\r\n                                    CASE WHEN opponents.contact_id IS NULL\r\n                                        THEN\r\n                                            opponentCompany.name\r\n                                        ELSE\r\n                                            opponentContact.firstName + ' ' + opponentContact.lastName\r\n                                    END\r\n                            END\r\n\t\t\t)\r\n\t\t\t FROM legal_case_litigation_stages_opponents\r\n\t\t\t INNER JOIN opponents ON opponents.id = legal_case_litigation_stages_opponents.opponent_id\r\n\t\t\t LEFT JOIN companies AS opponentCompany ON opponentCompany.id = opponents.company_id AND opponents.contact_id IS NULL\r\n\t\t\t LEFT JOIN contacts AS opponentContact ON opponentContact.id = opponents.contact_id AND opponents.company_id IS NULL\r\n                         LEFT JOIN legal_case_opponent_position_languages AS opponent_positions ON opponent_positions.legal_case_opponent_position_id = legal_case_litigation_stages_opponents.opponent_position and opponent_positions.language_id = '" . $lang_id . "'\r\n\t\t\t WHERE legal_case_litigation_stages_opponents.stage = legal_case_hearings.stage\r\n\t\t\tFOR XML PATH('')), 1, 1, '')", false];
        $query["join"][] = ["stage_statuses_languages as stage_statuses", "stage_statuses.status = legal_case_hearings.stage_status and stage_statuses.language_id = '" . $lang_id . "'", "left"];
        $query["join"][] = ["hearing_types_languages as types", "types.type = legal_case_hearings.type and types.language_id = '" . $lang_id . "'", "left"];
        $query["join"][] = ["legal_case_stage_languages as stages", "stages.legal_case_stage_id = legal_case_hearings.legal_case_stage and stages.language_id = '" . $lang_id . "'", "left"];
        $query["where"] = ["legal_case_hearings.id", $id];
        $response = $this->load($query);
        $this->_table = $table;
        return $response;
    }
    public function lookup($term, $more_filters = [], $hijri_calendar_enabled = false)
    {
        $config_query = [];
        $config_query["select"][] = ["('" . $this->get("modelCode") . "'+CAST( legal_case_hearings.id AS nvarchar )) as hearingID, legal_case_hearings.id, " . "CASE WHEN case_details.court_id IS NULL THEN (CAST( legal_case_hearings.startDate AS varchar ) + ' ' + COALESCE(CAST(legal_case_hearings.startTime AS varchar ), '')) ELSE (CAST( legal_case_hearings.startDate AS varchar ) + ' ' + COALESCE(CAST(legal_case_hearings.startTime AS varchar ), '') + ' - ' + courts.name) END as subject,legal_case_hearings.legal_case_id", false];
        $config_query["where"][] = ["legal_case_hearings.is_deleted = '0'", NULL, false];
        $config_query["join"][] = ["legal_case_litigation_details as case_details", "case_details.id = legal_case_hearings.stage", "left"];
        $config_query["join"][] = ["courts", "courts.id = case_details.court_id", "left"];
        if (!empty($term)) {
            $modelCode = substr($term, 0, 1);
            $ID = substr($term, 1);
            if (strcasecmp($modelCode, $this->get("modelCode")) === 0 && is_numeric($ID)) {
                $qId = substr($term, 1);
                if (is_numeric($qId)) {
                    $config_query["where"][] = ["legal_case_hearings.id = " . $qId, NULL, false];
                }
            } else {
                $term = $this->ci->db->escape_like_str($term);
                if ($hijri_calendar_enabled) {
                    $original_term = $term;
                    $term = hijriToGregorian($term);
                    $term = $term ? $term : $original_term;
                }
                $config_query["where"][] = ["CASE WHEN case_details.court_id IS NULL THEN (CAST( legal_case_hearings.startDate AS varchar ) + ' ' + COALESCE(CAST(legal_case_hearings.startTime AS varchar ), '')) ELSE (CAST( legal_case_hearings.startDate AS varchar ) + ' ' + COALESCE(CAST(legal_case_hearings.startTime AS varchar ), '') + ' - ' + courts.name) END LIKE '%" . $term . "%'", NULL, false];
            }
        }
        if ($more_filters) {
            foreach ($more_filters as $_field => $_term) {
                $config_query["where"][] = ["legal_case_hearings." . $_field, $_term];
            }
        }
        return $this->load_all($config_query);
    }
    public function roll_session_per_court($filter, $export = false, $hijri_calendar_enabled = false, &$query = [], &$stringQuery = "")
    {
        $this->ci->db->_protect_identifiers = false;
        $this->ci->db->_force_escape_string_values = true;
        $_table = $this->_table;
        $this->_table = "legal_case_hearings_full_details AS legal_case_hearings";
        $sortable = [["field" => "stages.name", "dir" => "asc"], ["field" => "legal_case_litigation_details.court_id", "dir" => "asc"], ["field" => "legal_case_hearings.startDate", "dir" => "asc"]];
        $query = ["select" => "legal_case_hearings.id"];
        $query["join"] = [["legal_cases", "legal_cases.id = legal_case_hearings.legal_case_id", "left"]];
        if (is_array($filter) && isset($filter["filters"])) {
            foreach ($filter["filters"] as $_filter) {
                $this->prep_k_filter($_filter, $query, $filter["logic"], $hijri_calendar_enabled);
            }
            unset($_filter);
        }
        $this->ci->load->model("system_configuration");
        $excluded_statuses = $this->ci->system_configuration->get_value_by_key("hearingReportExcludedStatuses");
        if (!empty($excluded_statuses)) {
            $query["where_not_in"][] = ["legal_cases.case_status_id", $excluded_statuses];
        }
        $this->ci->load->model("language");
        $langId = $this->ci->language->get_id_by_session_lang();
        $query["group_by"] = ["legal_case_hearings.id, legal_case_hearings.legal_case_id"];
        $ids = $this->load_all($query, "query");
        $this->_table = "legal_case_hearings AS legal_case_hearings";
        $query = [];
        $response = [];
        $language = strtolower(substr($this->ci->session->userdata("AUTH_language"), 0, 2));
        $query["select"] = ["legal_case_hearings.id, legal_case_litigation_details.court_id as court_id, courts.name as court_name, legal_case_hearings.summary,court_types.name as court_type,court_degrees.name as court_degree,court_regions.name as court_region, legal_case_hearings.stage AS stage_id, (CASE WHEN user_profiles.father = '' THEN (user_profiles.firstName +' '+ user_profiles.lastName) ELSE (user_profiles.firstName + ' '+ user_profiles.father + ' '+ user_profiles.lastName) END) as matter_assignee,lccplen.name as clientPosition_en,lccplfr.name as clientPosition_fr,lccplar.name as clientPosition_ar,lccplsp.name as clientPosition_sp,lccpl" . $language . ".name AS clientPosition," . "lawyers = STUFF((SELECT ' ; ' + ( userLaw.firstName + ' ' + userLaw.lastName+ CASE WHEN userLaw.status = 'Inactive' THEN ' (Inactive)' ELSE '' END ) FROM user_profiles AS userLaw INNER JOIN legal_case_hearings_users ON legal_case_hearings_users.legal_case_hearing_id = legal_case_hearings.id AND userLaw.user_id = legal_case_hearings_users.user_id FOR XML PATH('')), 1, 3, '')," . "legal_case_hearings.postponedDate,legal_case_hearings.legal_case_id as legal_case_id,('M'+CAST( legal_case_hearings.legal_case_id AS nvarchar )) as caseID,stages.name as stage_name, CASE WHEN clients.company_id IS NULL THEN cont.foreignFirstName + ' ' + cont.foreignLastName ELSE comp.foreignName END as client_foreign_name,types.name as type_name,CASE WHEN clients.company_id IS NULL THEN cont.firstName + ' ' + cont.lastName ELSE comp.name END as client," . "opponents = STUFF(\r\n\t\t\t(SELECT '<br/>' +\r\n\t\t\t(\r\n                            CASE WHEN opponent_positions.name IS NOT NULL\r\n                                THEN\r\n                                    CASE WHEN opponents.company_id IS NOT NULL\r\n                                        THEN\r\n                                            (opponentCompany.name + ' - ' + opponent_positions.name)\r\n                                        ELSE\r\n                                            opponentContact.firstName + ' ' + opponentContact.lastName + ' - ' + opponent_positions.name\r\n                                    END\r\n                                ELSE\r\n                                    CASE WHEN opponents.company_id IS NOT NULL\r\n                                        THEN\r\n                                            opponentCompany.name\r\n                                        ELSE\r\n                                            opponentContact.firstName + ' ' + opponentContact.lastName\r\n                                    END\r\n                            END\r\n\t\t\t)\r\n\t\t\t FROM legal_case_litigation_stages_opponents\r\n\t\t\t INNER JOIN opponents ON opponents.id = legal_case_litigation_stages_opponents.opponent_id\r\n\t\t\t LEFT JOIN companies AS opponentCompany ON opponentCompany.id = opponents.company_id AND opponents.company_id IS NOT NULL\r\n\t\t\t LEFT JOIN contacts AS opponentContact ON opponentContact.id = opponents.contact_id AND opponents.contact_id IS NOT NULL\r\n                         LEFT JOIN legal_case_opponent_position_languages AS opponent_positions ON opponent_positions.legal_case_opponent_position_id = legal_case_litigation_stages_opponents.opponent_position and opponent_positions.language_id = '" . $langId . "'\r\n\t\t\t WHERE legal_case_litigation_stages_opponents.stage = legal_case_hearings.stage\r\n\t\t\t FOR XML PATH (''), TYPE).value('.','varchar(max)'), 1, 5, ''),\r\n            opponent_foreign_name = STUFF((SELECT '<br/>' +\r\n                (\r\n                    CASE WHEN opponent_positions.name != '' THEN\r\n                    (\r\n                        CASE WHEN opponents.company_id IS NOT NULL\r\n                        THEN (CASE WHEN opponentCompany.foreignName IS NULL THEN opponentCompany.name ELSE opponentCompany.foreignName END) + ' - ' + opponent_positions.name\r\n                        ELSE (CASE WHEN opponentContact.foreignFirstName IS NULL THEN opponentContact.firstName ELSE opponentContact.foreignFirstName END) + ' ' + (CASE WHEN opponentContact.foreignLastName IS NULL THEN opponentContact.lastName ELSE opponentContact.foreignLastName END) + ' - ' + opponent_positions.name END\r\n                    )\r\n                    ELSE\r\n                    (\r\n                        CASE WHEN opponents.company_id IS NOT NULL\r\n                        THEN (CASE WHEN opponentCompany.foreignName IS NULL THEN opponentCompany.name ELSE opponentCompany.foreignName END)\r\n                        ELSE (CASE WHEN opponentContact.foreignFirstName IS NULL THEN opponentContact.firstName ELSE opponentContact.foreignFirstName END) + ' ' + (CASE WHEN opponentContact.foreignLastName IS NULL THEN opponentContact.lastName ELSE opponentContact.foreignLastName END) END\r\n                    )\r\n                    END\r\n                )\r\n                 FROM legal_case_litigation_stages_opponents\r\n                 INNER JOIN opponents ON opponents.id = legal_case_litigation_stages_opponents.opponent_id\r\n                 LEFT JOIN companies AS opponentCompany ON opponentCompany.id = opponents.company_id AND opponents.company_id IS NOT NULL\r\n                 LEFT JOIN contacts AS opponentContact ON opponentContact.id = opponents.contact_id AND opponents.contact_id IS NOT NULL\r\n                 LEFT JOIN legal_case_opponent_position_languages AS opponent_positions ON opponent_positions.legal_case_opponent_position_id = legal_case_litigation_stages_opponents.opponent_position and opponent_positions.language_id = '" . $langId . "'\r\n                 WHERE legal_case_litigation_stages_opponents.stage = legal_case_hearings.stage\r\n                FOR XML PATH (''), TYPE).value('.','varchar(max)'), 1, 5, '')," . "reference = STUFF((SELECT ' ; ' + lcler.number FROM legal_case_litigation_external_references lcler WHERE lcler.stage=legal_case_litigation_details.id FOR XML PATH('')), 1, 3, ''),legal_case_hearings.startDate,legal_case_hearings.startTime," . "(SELECT MAX(lccfd.containerId) FROM legal_case_containers_full_details lccfd WHERE legal_cases.id = lccfd.legal_case_id) AS containerID," . "legal_case_hearings.reasons_of_postponement,legal_case_hearings.comments", false];
        $query["where"][] = ["legal_case_hearings.id IN (" . $ids . ")"];
        $query["join"] = [["legal_cases", "legal_cases.id = legal_case_hearings.legal_case_id", "left"], ["legal_case_litigation_details", "legal_case_litigation_details.id = legal_case_hearings.stage", "left"], ["legal_case_stages", "legal_case_stages.id = legal_case_litigation_details.legal_case_stage", "left"], ["legal_case_stage_languages as stages", "stages.legal_case_stage_id = legal_case_stages.id and stages.language_id = '" . $langId . "'", "left"], ["courts", "courts.id = legal_case_litigation_details.court_id", "left"], ["court_types", "court_types.id = legal_case_litigation_details.court_type_id", "left"], ["court_degrees", "court_degrees.id = legal_case_litigation_details.court_degree_id", "left"], ["court_regions", "court_regions.id = legal_case_litigation_details.court_region_id", "left"], ["hearing_types_languages as types", "types.type = legal_case_hearings.type and types.language_id = '" . $langId . "'", "left"], ["user_profiles", "user_profiles.user_id = legal_cases.user_id", "left"], ["clients", "clients.id = legal_cases.client_id", "left"], ["companies comp", "comp.id = clients.company_id", "left"], ["contacts cont", "cont.id = clients.contact_id", "left"], ["legal_case_client_position_languages lccplen", "lccplen.legal_case_client_position_id = legal_case_litigation_details.client_position AND lccplen.language_id = '1'", "left"], ["legal_case_client_position_languages lccplar", "lccplar.legal_case_client_position_id = legal_case_litigation_details.client_position AND lccplar.language_id = '2'", "left"], ["legal_case_client_position_languages lccplfr", "lccplfr.legal_case_client_position_id = legal_case_litigation_details.client_position AND lccplfr.language_id = '3'", "left"], ["legal_case_client_position_languages lccplsp", "lccplsp.legal_case_client_position_id = legal_case_litigation_details.client_position AND lccplsp.language_id = '4'", "left"]];
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
        $response["totalRows"] = $this->count_total_matching_rows($query);
        $this->_table = $_table;
        $this->ci->db->_protect_identifiers = true;
        $this->ci->db->_force_escape_string_values = false;
        return $response;
    }
    public function pending_updates($filter, $export = false, $hijri_calendar_enabled = false)
    {
        $this->ci->db->_protect_identifiers = false;
        $this->ci->db->_force_escape_string_values = true;
        $_table = $this->_table;
        $this->_table = "legal_case_hearings_full_details AS legal_case_hearings";
        $query = ["select" => "legal_case_hearings.id"];
        $query["join"] = [["legal_cases", "legal_cases.id = legal_case_hearings.legal_case_id", "left"]];
        if (is_array($filter) && isset($filter["filters"])) {
            foreach ($filter["filters"] as $_filter) {
                if (isset($_filter["filters"][0]["field"]) && $_filter["filters"][0]["field"] == "legal_case_hearings.court_region_id") {
                    $_filter["filters"][0]["field"] = "legal_case_litigation_details.court_region_id";
                }
                $this->prep_k_filter($_filter, $query, $filter["logic"], $hijri_calendar_enabled);
            }
            unset($_filter);
        }
        $this->ci->load->model("system_configuration");
        $excluded_statuses = $this->ci->system_configuration->get_value_by_key("hearingUpdatesReportExcludedStatuses");
        if (!empty($excluded_statuses)) {
            $query["where_not_in"][] = ["legal_cases.case_status_id", $excluded_statuses];
        }
        $business_rules = $this->ci->system_configuration->get_value_by_key("hearingUpdatesReportBusinessRules");
        $business_rules_str = "";
        if (!empty($business_rules)) {
            foreach ($business_rules as $business_rule) {
                $business_rules_str .= $business_rule . " is null or ";
            }
            $business_rules_str = " and (" . substr($business_rules_str, 0, -4) . ")";
        }
        $current_time = date("H:i:s", time());
        $query["where"][] = ["startDate <= CONVERT(varchar, getdate(), 23)", NULL, false];
        $query["where"][] = ["(" . "(legal_case_hearings.judged = 'no' and legal_case_hearings.judgment is null " . $business_rules_str . ")" . " OR (legal_case_hearings.judged = 'yes' and legal_case_hearings.judgment is null " . $business_rules_str . ")" . ")", NULL, false];
        $this->ci->load->model("language");
        $langId = $this->ci->language->get_id_by_session_lang();
        $query["group_by"] = ["legal_case_hearings.id, legal_case_hearings.legal_case_id"];
        $ids = $this->load_all($query, "query");
        $this->_table = "legal_case_hearings AS legal_case_hearings";
        $query = [];
        $response = [];
        $query["select"] = ["legal_case_hearings.id, legal_case_hearings.startDate, legal_case_hearings.startTime, legal_case_hearings.legal_case_id as legal_case_id,('M'+CAST( legal_case_hearings.legal_case_id AS nvarchar )) as caseID,stages.name as stage_name, CASE WHEN clients.company_id IS NULL THEN (CASE WHEN con.father!='' THEN con.firstName + ' ' + con.father + ' ' + con.lastName ELSE con.firstName+' '+con.lastName END) ELSE com.name END AS client,CASE WHEN clients.company_id IS NULL THEN con.foreignFirstName + ' ' + con.foreignLastName ELSE com.foreignName END as client_foreign_name,legal_case_hearings.postponedDate, (SELECT MAX(lccfd.containerId) FROM legal_case_containers_full_details lccfd WHERE legal_cases.id = lccfd.legal_case_id) AS containerID, legal_case_hearings.stage AS stage_id, types.name as type_name, lawyers = STUFF((SELECT ' ; ' + ( userLaw.firstName + ' ' + userLaw.lastName+ CASE WHEN userLaw.status = 'Inactive' THEN ' (Inactive)' ELSE '' END ) FROM user_profiles AS userLaw INNER JOIN legal_case_hearings_users ON legal_case_hearings_users.legal_case_hearing_id = legal_case_hearings.id AND userLaw.user_id = legal_case_hearings_users.user_id FOR XML PATH('')), 1, 3, '')," . "opponents = STUFF(\r\n\t\t\t(SELECT ', ' +\r\n\t\t\t(\r\n                            CASE WHEN opponent_positions.name IS NOT NULL\r\n                                THEN\r\n                                    CASE WHEN opponents.company_id IS NOT NULL\r\n                                        THEN\r\n                                            (opponentCompany.name + ' - ' + opponent_positions.name)\r\n                                        ELSE\r\n                                            opponentContact.firstName + ' ' + opponentContact.lastName + ' - ' + opponent_positions.name\r\n                                    END\r\n                                ELSE\r\n                                    CASE WHEN opponents.company_id IS NOT NULL\r\n                                        THEN\r\n                                            opponentCompany.name\r\n                                        ELSE\r\n                                            opponentContact.firstName + ' ' + opponentContact.lastName\r\n                                    END\r\n                            END\r\n\t\t\t)\r\n\t\t\t FROM legal_case_litigation_stages_opponents\r\n\t\t\t INNER JOIN opponents ON opponents.id = legal_case_litigation_stages_opponents.opponent_id\r\n\t\t\t LEFT JOIN companies AS opponentCompany ON opponentCompany.id = opponents.company_id AND opponents.company_id IS NOT NULL\r\n\t\t\t LEFT JOIN contacts AS opponentContact ON opponentContact.id = opponents.contact_id AND opponents.contact_id IS NOT NULL\r\n                         LEFT JOIN legal_case_opponent_position_languages AS opponent_positions ON opponent_positions.legal_case_opponent_position_id = legal_case_litigation_stages_opponents.opponent_position and opponent_positions.language_id = '" . $langId . "'\r\n\t\t\t WHERE legal_case_litigation_stages_opponents.stage = legal_case_hearings.stage\r\n\t\t\tFOR XML PATH('')), 1, 1, ''),\r\n            opponent_foreign_name = STUFF((SELECT ', ' +\r\n                (\r\n                    CASE WHEN opponent_positions.name != '' THEN\r\n                    (\r\n                        CASE WHEN opponents.company_id IS NOT NULL\r\n                        THEN (CASE WHEN opponentCompany.foreignName IS NULL THEN opponentCompany.name ELSE opponentCompany.foreignName END) + ' - ' + opponent_positions.name\r\n                        ELSE (CASE WHEN opponentContact.foreignFirstName IS NULL THEN opponentContact.firstName ELSE opponentContact.foreignFirstName END) + ' ' + (CASE WHEN opponentContact.foreignLastName IS NULL THEN opponentContact.lastName ELSE opponentContact.foreignLastName END) + ' - ' + opponent_positions.name END\r\n                    )\r\n                    ELSE\r\n                    (\r\n                        CASE WHEN opponents.company_id IS NOT NULL\r\n                        THEN (CASE WHEN opponentCompany.foreignName IS NULL THEN opponentCompany.name ELSE opponentCompany.foreignName END)\r\n                        ELSE (CASE WHEN opponentContact.foreignFirstName IS NULL THEN opponentContact.firstName ELSE opponentContact.foreignFirstName END) + ' ' + (CASE WHEN opponentContact.foreignLastName IS NULL THEN opponentContact.lastName ELSE opponentContact.foreignLastName END) END\r\n                    )\r\n                    END\r\n                )\r\n                 FROM legal_case_litigation_stages_opponents\r\n                 INNER JOIN opponents ON opponents.id = legal_case_litigation_stages_opponents.opponent_id\r\n                 LEFT JOIN companies AS opponentCompany ON opponentCompany.id = opponents.company_id AND opponents.company_id IS NOT NULL\r\n                 LEFT JOIN contacts AS opponentContact ON opponentContact.id = opponents.contact_id AND opponents.contact_id IS NOT NULL\r\n                 LEFT JOIN legal_case_opponent_position_languages AS opponent_positions ON opponent_positions.legal_case_opponent_position_id = legal_case_litigation_stages_opponents.opponent_position and opponent_positions.language_id = '" . $langId . "'\r\n                 WHERE legal_case_litigation_stages_opponents.stage = legal_case_hearings.stage\r\n                FOR XML PATH('')), 1, 1, '')," . "reference = STUFF((SELECT ' ; ' + lcler.number FROM legal_case_litigation_external_references lcler WHERE lcler.stage=legal_case_litigation_details.id FOR XML PATH('')), 1, 3, ''),legal_case_hearings.startDate," . "(select top 1 max(previous.startDate) from legal_case_hearings as previous" . " where previous.id != legal_case_hearings.id and legal_case_hearings.stage = previous.stage" . " and previous.startDate < legal_case_hearings.startDate and previous.legal_case_id = legal_case_hearings.legal_case_id) as previousHearingDate", false];
        $query["where"][] = ["legal_case_hearings.id IN (" . $ids . ")"];
        $query["join"] = [["legal_cases", "legal_cases.id = legal_case_hearings.legal_case_id", "left"], ["legal_case_litigation_details", "legal_case_litigation_details.id = legal_case_hearings.stage", "left"], ["legal_case_stages", "legal_case_stages.id = legal_case_litigation_details.legal_case_stage", "left"], ["legal_case_stage_languages as stages", "stages.legal_case_stage_id = legal_case_stages.id and stages.language_id = '" . $langId . "'", "left"], ["hearing_types_languages as types", "types.type = legal_case_hearings.type and types.language_id = '" . $langId . "'", "left"], ["clients", "clients.id = legal_cases.client_id", "left"], ["companies com", "com.id = clients.company_id", "left"], ["contacts con", "con.id = clients.contact_id", "left"]];
        $query["order_by"] = ["legal_case_hearings.startDate desc"];
        $paginationConf = [];
        if (!$export) {
            $query["limit"] = [$this->ci->input->post("take", true), $this->ci->input->post("skip", true)];
            $paginationConf = ["urlPrefix" => ""];
            $paginationConf["inPage"] = $this->ci->input->post("take", true);
        }
        $response["data"] = $export ? parent::load_all($query) : parent::paginate($query, $paginationConf);
        $response["totalRows"] = $this->count_total_matching_rows($query);
        $this->_table = $_table;
        $this->ci->db->_protect_identifiers = false;
        $this->ci->db->_force_escape_string_values = true;
        return $response;
    }
    public function dashboard_pending_updates($is_api = false)
    {
        $this->ci->db->_protect_identifiers = false;
        $this->ci->db->_force_escape_string_values = true;
        $_table = $this->_table;
        $this->_table = "legal_case_hearings_full_details AS legal_case_hearings";
        $query = ["select" => "legal_case_hearings.id"];
        $query["join"] = [["legal_cases", "legal_cases.id = legal_case_hearings.legal_case_id", "left"]];
        $this->ci->load->model("system_configuration");
        $excluded_statuses = $this->ci->system_configuration->get_value_by_key("hearingUpdatesReportExcludedStatuses");
        if (!empty($excluded_statuses)) {
            $query["where_not_in"][] = ["legal_cases.case_status_id", $excluded_statuses];
        }
        $business_rules = $this->ci->system_configuration->get_value_by_key("hearingUpdatesReportBusinessRules");
        $business_rules_str = "";
        if (!empty($business_rules)) {
            foreach ($business_rules as $business_rule) {
                $business_rules_str .= $business_rule . " is null or ";
            }
            $business_rules_str = " and (" . substr($business_rules_str, 0, -4) . ")";
        }
        $query["where"][] = ["startDate <= CONVERT(varchar, getdate(), 23)", NULL, false];
        $query["where"][] = ["legal_case_hearings.lawyers LIKE '%" . $this->ci->session->userdata("AUTH_userProfileName") . "%'", NULL, false];
        $query["where"][] = ["(" . "(legal_case_hearings.judged = 'no' and legal_case_hearings.judgment is null " . $business_rules_str . ")" . " OR (legal_case_hearings.judged = 'yes' and legal_case_hearings.judgment is null " . $business_rules_str . ")" . ")", NULL, false];
        $this->ci->load->model("language");
        $lang_code = strtolower(substr($this->ci->session->userdata("AUTH_language"), 0, 2));
        $lang_id = $this->ci->language->get_id_by_session_lang();
        $query["group_by"] = ["legal_case_hearings.id, legal_case_hearings.legal_case_id"];
        $ids = $this->load_all($query, "query");
        $query = [];
        $response = [];
        $query["select"] = ["legal_case_hearings.id, legal_case_hearings.startDate, legal_case_hearings.startTime, legal_case_hearings.legal_case_id, legal_case_hearings.caseID as case_id, legal_case_hearings.caseSubject as case_subject, legal_case_hearings.court, legal_case_hearings.courtRegion, hearing_types.name as hearing_type, legal_case_hearings.legal_case_stage_name_" . $lang_code . " as stage, legal_case_hearings.opponentLawyers as opponentLawyers, legal_case_hearings.filed_on as filed_on, legal_case_hearings.reference as reference, legal_case_hearings.comments as comments", false];
        $query["where"][] = ["legal_case_hearings.id IN (" . $ids . ")"];
        $query["join"] = [["hearing_types_languages as hearing_types", "hearing_types.type = legal_case_hearings.type and hearing_types.language_id = '" . $lang_id . "'", "left"]];
        $query["order_by"] = ["legal_case_hearings.startDate desc"];
        $response = parent::load_all($query);
        $this->_table = $_table;
        $this->ci->db->_protect_identifiers = false;
        $this->ci->db->_force_escape_string_values = true;
        return $response;
    }
    public function load_hearing_details($id, $language = false)
    {
        $query = [];
        $this->ci->load->model("language");
        $language_id = $this->ci->language->get_id_by_session_lang($language);
        $query["select"] = ["stage_statuses.name as stage_status, legal_case_litigation_details.sentenceDate, case_types.name as type_name, legal_case_hearings.id, legal_case_hearings.startDate as hearing_date,stages.name as stage_name, legal_case_hearings.startTime as hearing_time, legal_case_hearings.postponedDate as postponedDate, legal_case_hearings.postponedTime as postponedTime, legal_case_hearings.comments as comments, legal_case_hearings.judged as judged,  legal_case_hearings.judgment as judgment, legal_case_hearings.reasons_of_postponement as reasons_of_postponement, legal_case_hearings.summary,legal_case_hearings.summaryToClient,legal_cases.id as legal_case_id, legal_cases.subject as case_subject, legal_cases.description as case_description,legal_cases.internalReference, legal_case_client_position_languages.name as client_position, hearings_details.court AS court, hearings_details.courtRegion AS court_region, hearings_details.courtDegree AS court_degree, hearings_details.courtType as court_type, (case when clients.company_id IS NULL then (case when contacts.father <> ' ' then (contacts.firstName + ' ' + contacts.father + ' ' + contacts.lastName)else (contacts.firstName + ' ' + contacts.lastName) end)else companies.name end) as client_name, judges = STUFF((SELECT ', ' + ( CASE WHEN contJud.father!='' THEN contJud.firstName + ' '+ contJud.father + ' ' + contJud.lastName ELSE contJud.firstName+' '+contJud.lastName END ) FROM contacts AS contJud INNER JOIN legal_case_stage_contacts lchcj ON lchcj.stage = legal_case_hearings.stage AND lchcj.contact_type = 'judge' AND contJud.id = lchcj.contact FOR XML PATH('')), 1, 2, ''),opponent_lawyers = STUFF((SELECT ', ' + ( CASE WHEN contJud.father!='' THEN contJud.firstName + ' '+ contJud.father + ' ' + contJud.lastName ELSE contJud.firstName+' '+contJud.lastName END ) FROM contacts AS contJud INNER JOIN legal_case_stage_contacts lchcj ON lchcj.stage = legal_case_hearings.stage AND lchcj.contact_type = 'opponent-lawyer' AND contJud.id = lchcj.contact FOR XML PATH('')), 1, 2, ''),( 'M' + CAST(legal_cases.id as nvarchar) + ' - ' + legal_cases.subject ) as caseID,[assignee(s)] = STUFF((SELECT ', ' + ( lawyers.firstName + ' ' + lawyers.lastName+ CASE WHEN lawyers.status = 'Inactive' THEN ' (Inactive)' ELSE '' END ) FROM user_profiles AS lawyers INNER JOIN legal_case_hearings_users ON legal_case_hearings_users.legal_case_hearing_id = legal_case_hearings.id AND lawyers.user_id = legal_case_hearings_users.user_id FOR XML PATH('')), 1, 2, ''),assignees = STUFF((SELECT ', ' + ( lawyers.firstName + ' ' + lawyers.lastName+ CASE WHEN lawyers.status = 'Inactive' THEN ' (Inactive)' ELSE '' END ) FROM user_profiles AS lawyers INNER JOIN legal_case_hearings_users ON legal_case_hearings_users.legal_case_hearing_id = legal_case_hearings.id AND lawyers.user_id = legal_case_hearings_users.user_id FOR XML PATH('')), 1, 2, ''),reference_date = STUFF((SELECT ', ' + ( CAST(lcler.number as nvarchar) + ' (' + CAST(lcler.refDate as nvarchar) + ')') FROM legal_case_litigation_external_references AS lcler INNER JOIN legal_case_litigation_details ON legal_case_litigation_details.id = lcler.stage FOR XML PATH('')), 1, 2, ''),(select top 1 CAST(next_hearing.startDate as nvarchar) + ' ' + CAST(next_hearing.startTime as nvarchar) from legal_case_hearings as next_hearing where next_hearing.id != legal_case_hearings.id and next_hearing.startDate > legal_case_hearings.startDate and next_hearing.legal_case_id = legal_case_hearings.legal_case_id) as next_hearing," . "stage_opponents = STUFF(\r\n\t\t\t(SELECT ', ' +\r\n\t\t\t(\r\n                            CASE WHEN opponent_positions.name IS NOT NULL\r\n                                THEN\r\n                                    CASE WHEN opponents.contact_id IS NULL\r\n                                        THEN\r\n                                            (opponentCompany.name + ' - (' + opponent_positions.name + ')')\r\n                                        ELSE\r\n                                            opponentContact.firstName + ' ' + opponentContact.lastName + ' - (' + opponent_positions.name + ')'\r\n                                    END\r\n                                ELSE\r\n                                    CASE WHEN opponents.contact_id IS NULL\r\n                                        THEN\r\n                                            opponentCompany.name\r\n                                        ELSE\r\n                                            opponentContact.firstName + ' ' + opponentContact.lastName\r\n                                    END\r\n                            END\r\n\t\t\t)\r\n\t\t\t FROM legal_case_litigation_stages_opponents\r\n\t\t\t INNER JOIN opponents ON opponents.id = legal_case_litigation_stages_opponents.opponent_id\r\n\t\t\t LEFT JOIN companies AS opponentCompany ON opponentCompany.id = opponents.company_id AND opponents.contact_id IS NULL\r\n\t\t\t LEFT JOIN contacts AS opponentContact ON opponentContact.id = opponents.contact_id AND opponents.company_id IS NULL\r\n                         LEFT JOIN legal_case_opponent_position_languages AS opponent_positions ON opponent_positions.legal_case_opponent_position_id = legal_case_litigation_stages_opponents.opponent_position and opponent_positions.language_id = '" . $language_id . "'\r\n\t\t\t WHERE legal_case_litigation_stages_opponents.stage = legal_case_hearings.stage\r\n\t\t\tFOR XML PATH('')), 1, 1, '')", false];
        $query["join"] = [["legal_cases", "legal_cases.id = legal_case_hearings.legal_case_id ", "left"], ["case_types", "case_types.id = legal_cases.case_type_id", "left"], ["clients", "clients.id = legal_cases.client_id ", "left"], ["companies", "companies.id = clients.company_id ", "left"], ["contacts", "contacts.id = clients.contact_id ", "left"], ["legal_case_client_positions", "legal_case_client_positions.id = legal_cases.legal_case_client_position_id", "left"], ["legal_case_client_position_languages", "legal_case_client_position_languages.legal_case_client_position_id = legal_case_client_positions.id and language_id = " . $language_id, "left"], ["legal_case_litigation_details", "legal_case_litigation_details.legal_case_id = legal_cases.id and legal_case_litigation_details.legal_case_stage = legal_cases.legal_case_stage_id and legal_case_litigation_details.legal_case_id = legal_case_hearings.legal_case_id", "left"], ["legal_case_hearings_full_details as hearings_details", "hearings_details.id = legal_case_hearings.id", "left"], ["legal_case_stage_languages as stages", "stages.legal_case_stage_id = legal_case_hearings.stage and stages.language_id = '" . $language_id . "'", "left"], ["stage_statuses_languages as stage_statuses", "stage_statuses.status = hearings_details.legal_case_stage and stage_statuses.language_id = '" . $language_id . "'", "left"]];
        $query["where"][] = ["legal_case_hearings.id", $id];
        return $this->load($query);
    }
    public function filter_matter_opponents()
    {
        return "STUFF((SELECT ', ' +\r\n                    (\r\n                        CASE WHEN opponent_positions.name IS NOT NULL\r\n                            THEN\r\n                                CASE WHEN opponents.company_id IS NOT NULL\r\n                                    THEN\r\n                                        (opponentCompany.name + ' - ' + opponent_positions.name)\r\n                                    ELSE\r\n                                        opponentContact.firstName + ' ' + opponentContact.lastName + ' - ' + opponent_positions.name\r\n                                END\r\n                            ELSE\r\n                                CASE WHEN opponents.company_id IS NOT NULL\r\n                                    THEN\r\n                                        opponentCompany.name\r\n                                    ELSE\r\n                                        opponentContact.firstName + ' ' + opponentContact.lastName\r\n                                END\r\n                        END\r\n                    )\r\n                     FROM legal_case_litigation_stages_opponents\r\n                     INNER JOIN opponents ON opponents.id = legal_case_litigation_stages_opponents.opponent_id\r\n                     LEFT JOIN companies AS opponentCompany ON opponentCompany.id = opponents.company_id AND opponents.company_id IS NOT NULL\r\n                     LEFT JOIN contacts AS opponentContact ON opponentContact.id = opponents.contact_id AND opponents.contact_id IS NOT NULL\r\n                     LEFT JOIN legal_case_opponent_position_languages AS opponent_positions ON opponent_positions.legal_case_opponent_position_id = legal_case_litigation_stages_opponents.opponent_position and opponent_positions.language_id = '1'\r\n                     WHERE legal_case_litigation_stages_opponents.stage = legal_case_hearings.stage FOR XML PATH ('')), 1,\r\n                         1, '')";
    }
    public function filter_matter_opponent_foreign_name()
    {
        return "STUFF((SELECT ', ' +\r\n                (\r\n                    CASE WHEN opponent_positions.name != '' THEN\r\n                    (\r\n                        CASE WHEN opponents.company_id IS NOT NULL\r\n                        THEN (CASE WHEN opponentCompany.foreignName IS NULL THEN opponentCompany.name ELSE opponentCompany.foreignName END) + ' - ' + opponent_positions.name\r\n                        ELSE (CASE WHEN opponentContact.foreignFirstName IS NULL THEN opponentContact.firstName ELSE opponentContact.foreignFirstName END) + ' ' + (CASE WHEN opponentContact.foreignLastName IS NULL THEN opponentContact.lastName ELSE opponentContact.foreignLastName END) + ' - ' + opponent_positions.name END\r\n                    )\r\n                    ELSE\r\n                    (\r\n                        CASE WHEN opponents.company_id IS NOT NULL\r\n                        THEN (CASE WHEN opponentCompany.foreignName IS NULL THEN opponentCompany.name ELSE opponentCompany.foreignName END)\r\n                        ELSE (CASE WHEN opponentContact.foreignFirstName IS NULL THEN opponentContact.firstName ELSE opponentContact.foreignFirstName END) + ' ' + (CASE WHEN opponentContact.foreignLastName IS NULL THEN opponentContact.lastName ELSE opponentContact.foreignLastName END) END\r\n                    )\r\n                    END\r\n                )\r\n                 FROM legal_case_litigation_stages_opponents\r\n                 INNER JOIN opponents ON opponents.id = legal_case_litigation_stages_opponents.opponent_id\r\n                 LEFT JOIN companies AS opponentCompany ON opponentCompany.id = opponents.company_id AND opponents.company_id IS NOT NULL\r\n                 LEFT JOIN contacts AS opponentContact ON opponentContact.id = opponents.contact_id AND opponents.contact_id IS NOT NULL\r\n                 LEFT JOIN legal_case_opponent_position_languages AS opponent_positions ON opponent_positions.legal_case_opponent_position_id = legal_case_litigation_stages_opponents.opponent_position and opponent_positions.language_id = '1'\r\n                 WHERE legal_case_litigation_stages_opponents.stage = legal_case_hearings.stage FOR XML PATH ('')), 1,\r\n                         1, '')";
    }
    public function filter_matter_clients()
    {
        return "(CASE WHEN clients.company_id IS NULL THEN (CASE WHEN cont.father!='' THEN cont.firstName + ' ' + cont.father + ' ' + cont.lastName ELSE cont.firstName+' '+cont.lastName END) ELSE comp.name END)";
    }
    public function filter_matter_client_foreign_name()
    {
        return "(CASE WHEN clients.company_id IS NULL THEN cont.foreignFirstName + ' ' + cont.foreignLastName ELSE comp.foreignName END)";
    }
    public function filter_stage_opponent_lawyer()
    {
        return "(SELECT CASE WHEN contOppLaw.father!='' THEN contOppLaw.firstName + ' '+ contOppLaw.father + ' ' + contOppLaw.lastName ELSE contOppLaw.firstName+' '+contOppLaw.lastName END FROM contacts AS contOppLaw INNER JOIN legal_case_stage_contacts lchcol ON lchcol.stage = legal_case_hearings.stage AND lchcol.contact_type = 'opponent-lawyer' AND contOppLaw.id = lchcol.contact)";
    }
    public function filter_stage_judge()
    {
        return "(SELECT CASE WHEN contJud.father!='' THEN contJud.firstName + ' '+ contJud.father + ' ' + contJud.lastName ELSE contJud.firstName+' '+contJud.lastName END FROM contacts AS contJud INNER JOIN legal_case_stage_contacts lchcj ON lchcj.stage = legal_case_hearings.stage AND lchcj.contact_type = 'judge' AND contJud.id = lchcj.contact)";
    }
    public function filter_stage_judges()
    {
        return "STUFF((SELECT ' ; ' + ( CASE WHEN contJud.father!='' THEN contJud.firstName + ' '+ contJud.father + ' ' + contJud.lastName ELSE contJud.firstName+' '+contJud.lastName END ) FROM contacts AS contJud INNER JOIN legal_case_stage_contacts lchcj ON lchcj.stage = legal_case_hearings.stage AND lchcj.contact_type = 'judge' AND contJud.id = lchcj.contact FOR XML PATH('')), 1, 3, '')";
    }
    public function filter_matter_container_id()
    {
        return "( 'MC' + legal_case_containers.id )";
    }
    public function filter_hearing_lawyers()
    {
        return "(STUFF((SELECT ' ; ' + ( userLaw.firstName + ' ' + userLaw.lastName+ CASE WHEN userLaw.status = 'Inactive' THEN ' (Inactive)' ELSE '' END ) FROM user_profiles AS userLaw INNER JOIN legal_case_hearings_users ON legal_case_hearings_users.legal_case_hearing_id = legal_case_hearings.id AND userLaw.user_id = legal_case_hearings_users.user_id FOR XML PATH('')), 1, 3, ''))";
    }
    public function dashboard_my_hearings($is_api = false, $hearings_date = "cw")
    {
        $_table = $this->_table;
        $this->_table = "legal_case_hearings_full_details AS legal_case_hearings";
        $this->ci->load->model("language");
        if ($is_api) {
            $user_id = $this->ci->user_logged_in_data["user_id"];
            $this->ci->load->model("user_preference");
            $this->ci->user_preference->fetch(["user_id" => $user_id, "keyName" => "language"]);
            $language = $this->ci->user_preference->get_field("keyValue");
            $this->ci->language->fetch(["fullName" => $language]);
            $lang_id = $this->ci->language->get_field("id");
            $lang_code = substr($this->ci->user_preference->get_field("keyValue"), 0, 2);
        } else {
            $user_id = $this->logged_user_id;
            $lang_id = $this->ci->language->get_id_by_session_lang();
            $lang_code = substr($this->ci->session->userdata("AUTH_language"), 0, 2);
        }
        switch ($hearings_date) {
            case "lw":
                $hearings_date = "legal_case_hearings.startDate >= DATEADD(dd, -1, DATEADD(ww, DATEDIFF(ww, 0, GETDATE()) - 1, 0))\r\n                                    AND legal_case_hearings.startDate <= DATEADD(dd, 5, DATEADD(ww, DATEDIFF(ww, 0, GETDATE()) - 1, 0))";
                break;
            case "td":
                $hearings_date = "legal_case_hearings.startDate = CONVERT(varchar, GETDATE(), 23)";
                break;
            case "cm":
                $hearings_date = "MONTH(legal_case_hearings.startDate) = MONTH(GETDATE()) AND YEAR(legal_case_hearings.startDate) = YEAR(GETDATE())";
                break;
            default:
                $hearings_date = "legal_case_hearings.startDate >= DATEADD(wk, DATEDIFF(wk, 0, GETDATE()), -1)\r\n                                    AND legal_case_hearings.startDate <= DATEADD(wk, DATEDIFF(wk, 0, GETDATE()), 5)";
        }
        $query = [];
        $query["select"] = ["legal_case_hearings.id, legal_case_hearings.startDate, legal_case_hearings.startTime, legal_case_hearings.legal_case_id, legal_case_hearings.caseID as case_id, legal_case_hearings.caseSubject as case_subject, legal_case_hearings.court, legal_case_hearings.courtRegion, hearing_types.name as hearing_type, legal_case_hearings.legal_case_stage_name_" . $lang_code . " as stage, legal_case_hearings.opponentLawyers as opponentLawyers, legal_case_hearings.filed_on as filed_on, legal_case_hearings.reference as reference, legal_case_hearings.comments as comments", false];
        $query["join"] = [["hearing_types_languages as hearing_types", "hearing_types.type = legal_case_hearings.type and hearing_types.language_id = '" . $lang_id . "'", "left"], ["legal_case_hearings_users as hearing_users", "hearing_users.legal_case_hearing_id = legal_case_hearings.id", "left"]];
        $query["where"][] = [(string) $hearings_date, NULL, false];
        $query["where"][] = ["hearing_users.user_id", $user_id];
        $query["order_by"] = ["startDate asc"];
        $response = $this->load_all($query);
        $this->_table = $_table;
        return $response;

    }
    public function count_all_hearings()
    {
        $_table = $this->_table;
        $this->_table = "legal_case_hearings_full_details AS legal_case_hearings";
        $query["select"] = ["COUNT(0) as hearings", false];
        $response = $this->load($query)["hearings"];
        $this->_table = $_table;
        return $response;
    }
    public function dashboard_recent_hearings($category = "hearings", $api_params = [])
    {
        $logged_user_id = $api_params["user_id"] ?? $this->logged_user_id;
        $_table = $this->_table;
        $this->_table = "legal_case_hearings_full_details AS legal_case_hearings";
        $this->ci->load->model("user_preference");
        $recent_hearings = unserialize($this->ci->user_preference->get_value_by_user("recent_cases", $logged_user_id));
        $response = [];
        if (isset($recent_hearings[$category])) {
            $recent_hearings = $recent_hearings[$category];
            $order_by = "CASE legal_case_hearings.id";
            foreach ($recent_hearings as $key => $val) {
                if ($val == 0) {
                    unset($recent_hearings[$key]);
                } else {
                    $order_by .= " when '" . $val . "' then " . $key;
                }
            }
            $order_by .= " end";
            if (!empty($recent_hearings)) {
                $recent_hearings = implode(",", array_map("intval", $recent_hearings));
                $query["select"] = ["legal_case_hearings.id, legal_case_hearings.legal_case_id, legal_case_hearings.caseID as case_id, legal_case_hearings.caseSubject as case_subject, legal_case_hearings.startDate, legal_case_hearings.startTime, legal_case_hearings.createdOn, legal_case_hearings.modifiedOn, '" . $category . "' AS module", false];
                $query["where"][] = ["legal_case_hearings.id IN (" . $recent_hearings . ")", NULL, false];
                $query["order_by"] = [$order_by];
                $response = $this->load_all($query);
            }
        }
        $this->_table = $_table;
        return $response;
    }
}

?>