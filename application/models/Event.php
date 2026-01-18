<?php


if (!defined("BASEPATH")) {
    exit("No direct script access allowed");
}
class Event extends My_Model_Factory
{
}
class mysql_Event extends My_Model
{
    protected $modelName = "event";
    protected $_table = "events";
    protected $_listFieldName = "legal_case_id";
    protected $_fieldsNames = ["id", "legal_case_id", "start_date", "start_time", "end_date", "end_time", "private", "priority", "task_location_id", "title", "description", "calendar_id", "integration_id", "integration_type", "created_from", "createdBy", "createdOn", "modifiedBy", "modifiedOn", "event_type_id"];
    protected $allowedNulls = ["legal_case_id", "priority", "private", "task_location_id", "calendar_id", "integration_id", "integration_type", "created_from"];
    protected $priority_values = ["critical", "high", "medium", "low"];
    protected $events_lookup_inputs_validation = [["input_name" => "related_case", "error_field" => "legal_case_id", "message" => ["main_var" => "not_exists2", "lookup_for" => "case"]], ["input_name" => "location", "error_field" => "task_location_id", "message" => ["main_var" => "not_exists", "lookup_for" => "location"]]];
    protected $attendees_lookup_inputs_validation = [["input_name" => "look_up_attendees", "error_field" => "attendees[]", "message" => ["main_var" => "not_exists", "lookup_for" => "user"]]];
    protected $builtInLogs = true;
    public function __construct()
    {
        parent::__construct();
        $this->validate = ["legal_case_id" => ["required" => false, "allowEmpty" => true, "rule" => "numeric", "message" => sprintf($this->ci->lang->line("is_numeric_rule"), $this->ci->lang->line("case"))], "start_date" => ["isRequired" => ["required" => true, "allowEmpty" => false, "rule" => ["minLength", 1], "message" => $this->ci->lang->line("cannot_be_blank_rule")], "dateCombinationRules" => ["rule" => ["validate_dates_combination", ["start_date" => ["min_for" => ["end_date"]]]], "message" => sprintf($this->ci->lang->line("related_to1"), $this->ci->lang->line("start_date"), $this->ci->lang->line("end_date"))], "date" => ["required" => false, "allowEmpty" => true, "rule" => $this->custom_validation_rules["date"], "message" => sprintf($this->ci->lang->line("required_date_rule"), $this->ci->lang->line("start_date"))]], "start_time" => ["isRequired" => ["required" => true, "allowEmpty" => false, "rule" => ["minLength", 1], "message" => $this->ci->lang->line("cannot_be_blank_rule")], "time_rules" => ["required" => false, "allowEmpty" => true, "rule" => "time", "message" => sprintf($this->ci->lang->line("time_rule"), $this->ci->lang->line("start_time"))]], "end_date" => ["isRequired" => ["required" => true, "allowEmpty" => false, "rule" => ["minLength", 1], "message" => $this->ci->lang->line("cannot_be_blank_rule")], "date" => ["required" => false, "allowEmpty" => true, "rule" => $this->custom_validation_rules["date"], "message" => sprintf($this->ci->lang->line("required_date_rule"), $this->ci->lang->line("end_date"))]], "end_time" => ["isRequired" => ["required" => true, "allowEmpty" => false, "rule" => ["minLength", 1], "message" => $this->ci->lang->line("cannot_be_blank_rule")], "time_rules" => ["required" => false, "allowEmpty" => true, "rule" => "time", "message" => sprintf($this->ci->lang->line("time_rule"), $this->ci->lang->line("end_time"))]], "priority" => ["required" => false, "allowEmpty" => true, "rule" => ["inList", $this->priority_values], "message" => sprintf($this->ci->lang->line("allowed_list_values"), implode(", ", $this->priority_values))], "task_location_id" => ["required" => false, "allowEmpty" => true, "rule" => ["maxLength", 255], "message" => sprintf($this->ci->lang->line("is_numeric_rule"), $this->ci->lang->line("location"))], "title" => ["isRequired" => ["required" => true, "allowEmpty" => false, "rule" => ["minLength", 1], "message" => $this->ci->lang->line("cannot_be_blank_rule")], "maxLength" => ["rule" => ["maxLength", 255], "message" => sprintf($this->ci->lang->line("max_characters"), 255)]], "description" => ["required" => false, "allowEmpty" => true, "rule" => ["minLength", 1], "message" => sprintf($this->ci->lang->line("min_length_rule"), $this->ci->lang->line("description"), 1)]];
    }
    public function load_integration_settings()
    {
        $return = ["data" => [], "error" => ""];
        $this->ci->load->model("instance_data");
        $installation_type = $this->ci->instance_data->get_value_by_key("installationType");
        if ($installation_type["keyValue"] == "on-cloud") {
            $return["data"] = json_decode("{
                \"Google-Calendar\":{\"client_id\":\"113513093041-s2bnppm89gajlfhp6tpmv4u2sgh6sbn1.apps.googleusercontent.com\",\"project_id\":\"strategic-cargo-320008\",\"auth_uri\":\"https://accounts.google.com/o/oauth2/auth\",\"token_uri\":\"https://accounts.google.com/o/oauth2/token\",\"auth_provider_x509_cert_url\":\"https://www.googleapis.com/oauth2/v1/certs\",\"client_secret\":\"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\",\"redirect_uris\":[\"https://app.sheria360.com/calendar_integrations/oauth\"]},
            \"MS-Office-Calendar-365\":{\"clientId\":\"909bede0-5f38-4efa-a230-5f474b973143\",\"clientSecret\":\"PMt7Q~J2S4X_YEhcWk19uIsKW_xRnZL9dWNdY\",\"redirectUri\": \"https://site.app4legal.com/callback_oauth.php\",\"urlAuthorize\": \"https://login.microsoftonline.com/common/oauth2/v2.0/authorize\",\"urlAccessToken\": \"https://login.microsoftonline.com/common/oauth2/v2.0/token\",\"urlResourceOwnerDetails\": \"\",\"scopes\": \"openid profile offline_access Calendars.ReadWrite\"}}", true);
        } else {
            $integrations_config_file = substr(COREPATH, 0, -12) . "application/config/integrations.json";
            $config = [];
            if (is_string($integrations_config_file) && file_exists($integrations_config_file)) {
                $json_config_data = file_get_contents($integrations_config_file);
                if (!($config = json_decode($json_config_data, true))) {
                    $return["error"] = $this->ci->lang->line("integration_config_file_not_valid");
                }
                if (!is_array($config)) {
                    $return["error"] = $this->ci->lang->line("integration_config_file_not_valid");
                }
            } else {
                $return["error"] = $this->ci->lang->line("missing_integration_config_file");
            }
            $config["Google-Calendar"]["auth_uri"] = "https://accounts.google.com/o/oauth2/auth";
            $config["Google-Calendar"]["token_uri"] = "https://accounts.google.com/o/oauth2/token";
            $config["Google-Calendar"]["auth_provider_x509_cert_url"] = "https://www.googleapis.com/oauth2/v1/certs";
            $config["Google-Calendar"]["redirect_uris"] = ["https://app.sheria360.com/calendar_integrations/oauth"];
            $config["MS-Office-Calendar-365"]["urlAuthorize"] = "https://login.microsoftonline.com/common/oauth2/v2.0/authorize";
            $config["MS-Office-Calendar-365"]["urlAccessToken"] = "https://login.microsoftonline.com/common/oauth2/v2.0/token";
            $config["MS-Office-Calendar-365"]["urlResourceOwnerDetails"] = "";
            $config["MS-Office-Calendar-365"]["scopes"] = "openid profile offline_access Calendars.ReadWrite";
            $return["data"] = $config;
        }
        return $return;
    }
    public function load_event($id, $user_id = 0)
    {
        $logged_user_id = $user_id ? $user_id : $this->ci->is_auth->get_user_id();
        $this->ci->load->model("user_profile");
        $this->ci->user_profile->fetch(["user_id" => $logged_user_id]);
        $override_privacy = $this->ci->user_profile->get_field("overridePrivacy");
        if (!isset($this->ci->legal_case)) {
            $this->ci->load->model("legal_case", "legal_casefactory");
            $this->ci->legal_case = $this->ci->legal_casefactory->get_instance();
        }
        $query = [];
        $query["select"] = ["events.*,CASE WHEN created.status='Inactive' THEN CONCAT( created.firstName, ' ', created.lastName , ' (Inactive)') ELSE CONCAT( created.firstName, ' ', created.lastName) END as created_by_name, legal_cases.subject as case_subject, legal_cases.category as case_category, task_locations.name AS location", false];
        $query["where"][] = ["events.id", $id];
        $where_condition = $this->ci->legal_case->get_matter_privacy_conditions($logged_user_id, $override_privacy, false);
        $query["where"][] = ["(events.legal_case_id IS NULL OR " . $where_condition . ")", NULL, false];
        $query["where"][] = ["(events.private IS NULL OR events.private = 'no' OR (events.private = 'yes' AND (events.id IN (SELECT event_id FROM events_attendees WHERE user_id = '" . $logged_user_id . "') OR '" . $override_privacy . "' = 'yes')))", NULL, false];
        $query["join"] = [["legal_cases", "legal_cases.id = events.legal_case_id", "left"], ["user_profiles created", "created.user_id = events.createdBy", "left"], ["task_locations", "task_locations.id = events.task_location_id", "left"]];
        return $this->load($query);
    }
    public function load_attendees($event_id, $custom_structure_for_meeting = false)
    {
        $users = [];
        $events_attendees = $this->ci->db->select("events_attendees.user_id as id, events_attendees.mandatory as mandatory , events_attendees.participant as participant , CASE WHEN UP.status='Inactive' THEN CONCAT( UP.firstName, ' ', UP.lastName , ' (Inactive)') ELSE CONCAT( UP.firstName, ' ', UP.lastName) END as name", false)->join("user_profiles UP", "UP.user_id = events_attendees.user_id", "inner")->where("events_attendees.event_id", $event_id)->get("events_attendees");
        if (!$events_attendees->num_rows()) {
            return $users;
        }
        if ($custom_structure_for_meeting) {
            foreach ($events_attendees->result() as $key => $user) {
                $users[$key]["id"] = (string) $user->id;
                $users[$key]["name"] = $user->name;
                $users[$key]["mandatory"] = $user->mandatory;
                $users[$key]["participant"] = $user->participant;
            }
        } else {
            foreach ($events_attendees->result() as $user) {
                $users[(string) $user->id] = $user->name;
            }
        }
        return $users;
    }
    public function load_all_events($page_size, $skip, $term = "")
    {
        if (!isset($this->ci->legal_case)) {
            $this->ci->load->model("legal_case", "legal_casefactory");
            $this->ci->legal_case = $this->ci->legal_casefactory->get_instance();
        }
        $query = $response = [];
        $query["select"] = ["events.*,CASE WHEN created.status='Inactive' THEN CONCAT( created.firstName, ' ', created.lastName , ' (Inactive)') ELSE CONCAT( created.firstName, ' ', created.lastName) END as created_by_name, legal_cases.subject as case_subject, legal_cases.category as case_category, task_locations.name AS location", false];
        if ($term != "") {
            $term = $this->ci->db->escape_like_str($term);
            $query["where"][] = [" ( events.title  LIKE '%" . $term . "%' )", NULL, false];
        }
        $query["join"] = [["legal_cases", "legal_cases.id = events.legal_case_id", "left"], ["user_profiles created", "created.user_id = events.createdBy", "left"], ["task_locations", "task_locations.id = events.task_location_id", "left"]];
        $this->prep_query($query);
        $response["totalRows"] = $this->ci->db->count_all_results($this->_table);
        $query["order_by"] = ["events.start_date desc"];
        $query["limit"] = [$page_size, $skip];
        $response["data"] = $this->load_all($query);
        return $response;
    }
    public function load_events($user_id, $page_size, $skip, $term = "")
    {
        $logged_user_id = $user_id ? $user_id : $this->ci->is_auth->get_user_id();
        $this->ci->load->model("user_profile");
        $this->ci->user_profile->fetch(["user_id" => $logged_user_id]);
        $override_privacy = $this->ci->user_profile->get_field("overridePrivacy");
        if (!isset($this->ci->legal_case)) {
            $this->ci->load->model("legal_case", "legal_casefactory");
            $this->ci->legal_case = $this->ci->legal_casefactory->get_instance();
        }
        $query = $response = [];
        $query["select"] = ["events.*,CASE WHEN created.status='Inactive' THEN CONCAT( created.firstName, ' ', created.lastName , ' (Inactive)') ELSE CONCAT( created.firstName, ' ', created.lastName) END as created_by_name, legal_cases.subject as case_subject, legal_cases.category as case_category, task_locations.name AS location", false];
        if ($term != "") {
            $term = $this->ci->db->escape_like_str($term);
            $query["where"][] = [" ( events.title  LIKE '%" . $term . "%' )", NULL, false];
        }
        $where_condition = $this->ci->legal_case->get_matter_privacy_conditions($logged_user_id, $override_privacy, false);
        $query["where"][] = ["(events.legal_case_id IS NULL OR " . $where_condition . ")", NULL, false];
        $query["where"][] = ["(events.private IS NULL OR events.private = 'no' OR (events.private = 'yes' AND (events.id IN (SELECT event_id FROM events_attendees WHERE user_id = '" . $logged_user_id . "') OR '" . $override_privacy . "' = 'yes')))", NULL, false];
        $query["where"][] = ["events_attendees.user_id", $user_id];
        $query["join"] = [["legal_cases", "legal_cases.id = events.legal_case_id", "left"], ["user_profiles created", "created.user_id = events.createdBy", "left"], ["events_attendees", "events_attendees.event_id = events.id ", "left"], ["task_locations", "task_locations.id = events.task_location_id", "left"]];
        $this->prep_query($query);
        $response["totalRows"] = $this->ci->db->count_all_results($this->_table);
        $query["order_by"] = ["events.start_date desc"];
        $query["limit"] = [$page_size, $skip];
        $response["data"] = $this->load_all($query);
        return $response;
    }
    public function delete_ms_cloud_calendar_removed_events($synced_event_ids)
    {
        $sql = "select id from events where integration_type = 'ms_cloud' and integration_id " . (!empty($synced_event_ids) ? "not in ('" . implode("', '", $synced_event_ids) . "')" : "is not null");
        $query_execution = $this->ci->db->query($sql);
        $result = $query_execution->result_array();
        $events = array_column($result, "id");
        if (!empty($events)) {
            $sql = "update legal_case_hearings set task_id = NULL where task_id in ('" . implode("', '", $events) . "')";
            $this->ci->db->query($sql);
            $this->ci->load->model("event_attendee");
            $this->ci->event_attendee->delete(["where_in" => ["event_id", $events]]);
            $this->ci->event->delete(["where_in" => ["id", $events]]);
        }
    }
    public function update_integration_provider_calendar($event_id, $mode = "add", $is_api = false)
    {
        if ($integration_settings = $this->check_integration($is_api)) {
            extract($integration_settings);
            if ($this->ci->event->fetch(["id" => $event_id])) {
                if ($integration_settings["calendar"]["provider"] == "google") {
                    $service = new Google_Service_Calendar($provider_oauth);
                } else {
                    if ($integration_settings["calendar"]["provider"] == "ms_cloud") {
                        $outlook_calendar = new Outlook_Calendar($provider_oauth);
                    }
                }
                $this->ci->load->model("system_preference");
                $system_preferences = $this->ci->system_preference->get_values();
                $timezone = isset($system_preferences["systemTimezone"]) && $system_preferences["systemTimezone"] ? $system_preferences["systemTimezone"] : $this->ci->config->item("default_timezone");
                $attendees = $this->ci->event->load_attendees($event_id, true);
                unset($attendees[$this->ci->is_auth->get_user_id()]);
                $location = "";
                if ($this->ci->event->get_field("task_location_id")) {
                    $this->ci->task_location->fetch($this->ci->event->get_field("task_location_id"));
                    $location = $this->ci->task_location->get_field("name");
                }
                $attendees_emails = [];
                foreach ($attendees as $key => $client) {
                    if ($this->ci->user->fetch($client["id"])) {
                        if ($integration_settings["calendar"]["provider"] == "google") {
                            $attendees_emails[$key]["email"] = $this->ci->user->get_field("email");
                            $attendees_emails[$key]["optional"] = !$client["mandatory"];
                        } else {
                            if ($integration_settings["calendar"]["provider"] == "ms_cloud") {
                                $attendees_emails[$key]["emailAddress"]["address"] = $this->ci->user->get_field("email");
                                if ($client["mandatory"]) {
                                    $attendees_emails[$key]["type"] = "required";
                                } else {
                                    $attendees_emails[$key]["type"] = "optional";
                                }
                            }
                        }
                    }
                }
                $start_date = date(DATE_ATOM, strtotime($this->ci->event->get_field("start_date") . $this->ci->event->get_field("start_time")));
                $end_date = date(DATE_ATOM, strtotime($this->ci->event->get_field("end_date") . $this->ci->event->get_field("end_time")));
                $visibility = $this->ci->event->get_field("private") == "yes" ? "private" : ($integration_settings["calendar"]["provider"] == "google" ? "default" : "normal");
                if ($integration_settings["calendar"]["provider"] == "ms_cloud") {
                    $event = ["subject" => $this->ci->event->get_field("title"), "location" => ["displayName" => $location], "body" => ["contentType" => "Text", "content" => $this->ci->event->get_field("description")], "start" => ["dateTime" => $start_date, "timeZone" => $timezone], "end" => ["dateTime" => $end_date, "timeZone" => $timezone], "attendees" => $attendees_emails, "sensitivity" => $visibility];
                }
                if ($mode == "add") {
                    if ($integration_settings["calendar"]["provider"] == "google") {
                        $event = new Google_Service_Calendar_Event(["summary" => $this->ci->event->get_field("title"), "location" => $location, "description" => $this->ci->event->get_field("description"), "start" => ["dateTime" => $start_date, "timeZone" => $timezone], "end" => ["dateTime" => $end_date, "timeZone" => $timezone], "attendees" => $attendees_emails, "visibility" => $visibility]);
                        $inserted_event = $service->events->insert($integration_settings["calendar"]["selected_calendar"], $event);
                    } else {
                        $inserted_event = $outlook_calendar->create_event($integration_settings["calendar"]["selected_calendar"], $event);
                    }
                    if ($inserted_event) {
                        $this->ci->event->set_field("integration_id", $inserted_event->id);
                        $this->ci->event->set_field("integration_type", $integration_settings["calendar"]["provider"]);
                        $this->ci->event->set_field("created_from", "a4l");
                        $this->ci->event->set_field("calendar_id", $integration_settings["calendar"]["selected_calendar"]);
                        $this->ci->event->update();
                        return true;
                    }
                } else {
                    if ($integration_settings["calendar"]["selected_calendar"] === $this->ci->event->get_field("calendar_id")) {
                        if ($integration_settings["calendar"]["provider"] == "google") {
                            $event = $service->events->get($integration_settings["calendar"]["selected_calendar"], $this->ci->event->get_field("integration_id"));
                            $event->setSummary($this->ci->event->get_field("title"));
                            $event->setDescription($this->ci->event->get_field("description"));
                            $event->setLocation($location);
                            $start = new Google_Service_Calendar_EventDateTime();
                            $start->setDateTime($start_date);
                            $event->setStart($start);
                            $end = new Google_Service_Calendar_EventDateTime();
                            $end->setDateTime($end_date);
                            $event->setEnd($end);
                            $event->setAttendees($attendees_emails);
                            $event->setVisibility($visibility);
                            $service->events->update($integration_settings["calendar"]["selected_calendar"], $event->getId(), $event);
                        } else {
                            if ($integration_settings["calendar"]["provider"] == "ms_cloud") {
                                $outlook_calendar->update_event($integration_settings["calendar"]["selected_calendar"], $this->ci->event->get_field("integration_id"), $event);
                            }
                        }
                        return true;
                    }
                }
            }
        }
        return false;
    }
    public function check_integration($is_api = false)
    {
        $user_id = $is_api ? $this->ci->user_logged_in_data["user_id"] : $this->ci->session->userdata("AUTH_user_id");
        $integration = $this->ci->user_preference->get_value_by_user("integration", $user_id);
        $integration_settings = unserialize($integration);
        if (!empty($integration_settings["calendar"]["enabled"]) && $integration_settings["calendar"]["token"] && ($provider_oauth = $this->load_integration_provider_library($integration_settings["calendar"]["provider"], $integration_settings["calendar"]["token"]))) {
            $this->ci->load->model("user", "userfactory");
            $this->ci->user = $this->ci->userfactory->get_instance();
            $this->ci->load->model("task_location");
            $data["integration_settings"] = $integration_settings;
            $data["provider_oauth"] = $provider_oauth;
            return $data;
        }
        return false;
    }
    public function load_integration_provider_library($integration_type, $token = NULL)
    {
        $provider_oauth = NULL;
        $integration_settings = $this->load_integration_settings();
        if ($integration_settings["error"]) {
            return false;
        }
        if ($integration_type == "google") {
            require_once substr(COREPATH, 0, -12) . "/application/libraries/google-api/vendor/autoload.php";
            $provider_oauth = new Google_Client();
            $provider_oauth->setAccessType("offline");
            $provider_oauth->setAuthConfig($integration_settings["data"]);
            $provider_oauth->setScopes(Google_Service_Calendar::CALENDAR);
            $guzzle_client = new GuzzleHttp\Client(["curl" => [CURLOPT_SSL_VERIFYPEER => false]]);
            $provider_oauth->setHttpClient($guzzle_client);
            if (!empty($token)) {
                $provider_oauth->setAccessToken($token);
                if ($provider_oauth->isAccessTokenExpired()) {
                    if (isset($token["refresh_token"])) {
                        $provider_oauth->refreshToken($token["refresh_token"]);
                    } else {
                        return false;
                    }
                }
            }
        } else {
            if ($integration_type == "ms_cloud") {
                require_once substr(COREPATH, 0, -12) . "/application/libraries/microsoft_graph_api/oauth.php";
                require_once substr(COREPATH, 0, -12) . "/application/libraries/microsoft_graph_api/outlook_calendar.php";
                $provider_oauth = new Oauth();
                $provider_oauth->set_oauth_configuration($integration_settings["data"]);
                if (!empty($token)) {
                    $provider_oauth->set_access_token($token);
                }
            }
        }
        return $provider_oauth;
    }
    public function delete_meetings($data, $data_is_query, $is_api = false)
    {
        $return = false;
        if (!$data_is_query && !empty($data)) {
            $this->ci->db->select("id");
            $this->ci->db->from("events");
            $this->ci->db->where("events.id IN (" . $data . ")", NULL, false);
            $meetings_where_clause = $this->ci->db->get_compiled_select();
            $this->ci->db->reset_query();
        } else {
            $meetings_where_clause = $data;
        }
        if (!empty($meetings_where_clause)) {
            $this->ci->db->select("id");
            $this->ci->db->where("events_attendees.event_id IN (" . $meetings_where_clause . ")", NULL, false);
            $meetings_attendees_query = $this->ci->db->get("events_attendees");
            $meetings_attendees_result = [];
            foreach ($meetings_attendees_query->result() as $row) {
                $meetings_attendees_result[] = $row->id;
            }
            $this->ci->db->reset_query();
            if (!empty($meetings_attendees_result)) {
                $this->ci->db->where("id IN (" . implode(", ", $meetings_attendees_result) . ")", NULL, false);
                $this->ci->db->delete("events_attendees");
                $this->ci->db->reset_query();
            }
            $event_records = $this->ci->db->query($meetings_where_clause);
            if ($event_records->result()) {
                foreach ($event_records->result() as $row) {
                    $meeting_id = $row->related_id;
                    $this->fetch(["id" => $meeting_id]);
                    $integration_id = $this->get_field("integration_id");
                    if (($result = $this->delete(["where" => ["id", $meeting_id]])) && ($integration_settings = $this->check_integration($is_api))) {
                        extract($integration_settings);
                        if ($integration_id && $integration_settings["calendar"]["selected_calendar"] === $this->get_field("calendar_id")) {
                            if ($integration_settings["calendar"]["provider"] == "google") {
                                $service = new Google_Service_Calendar($provider_oauth);
                                $service->events->delete($integration_settings["calendar"]["selected_calendar"], $integration_id);
                            } else {
                                if ($integration_settings["calendar"]["provider"] == "ms_cloud") {
                                    $outlook_calendar = new Outlook_Calendar($provider_oauth);
                                    $outlook_calendar->delete_event($integration_settings["calendar"]["selected_calendar"], $integration_id);
                                }
                            }
                        }
                    }
                }
            }
        }
        return $return;
    }
    public function load_todays_meetings($is_api = false)
    {
        $user_id = $is_api ? $this->ci->user_logged_in_data["user_id"] : $this->ci->session->userdata("AUTH_user_id");
        $query = [];
        $query["select"] = ["events.id, events.legal_case_id, events.start_date, events.start_time, events.end_date, events.end_time, events.title, events.description", false];
        $query["join"] = ["events_attendees", "events_attendees.event_id = events.id", "left"];
        $query["where"][] = ["CURDATE() = events.start_date", NULL, false];
        $query["where"][] = ["events_attendees.user_id", $user_id];
        $query["order_by"] = ["start_time asc"];
        $response = $this->load_all($query);
        foreach ($response as $key => $event) {
            $start = new DateTime($event["start_date"] . " " . $event["start_time"]);
            $end = new DateTime($event["end_date"] . " " . $event["end_time"]);
            $response[$key]["startTime"] = date("h:iA", strtotime($event["start_date"] . " " . $event["start_time"]));
            $response[$key]["start_date"] = $start->format(DateTime::ATOM);
            $response[$key]["end_date"] = $end->format(DateTime::ATOM);
        }
        return $response;
    }
}
class mysqli_Event extends mysql_Event
{
}
class sqlsrv_Event extends mysql_Event
{
    public function load_event($id, $user_id = 0)
    {
        $logged_user_id = $user_id ? $user_id : $this->ci->is_auth->get_user_id();
        $this->ci->load->model("user_profile");
        $this->ci->user_profile->fetch(["user_id" => $logged_user_id]);
        $override_privacy = $this->ci->user_profile->get_field("overridePrivacy");
        if (!isset($this->ci->legal_case)) {
            $this->ci->load->model("legal_case", "legal_casefactory");
            $this->ci->legal_case = $this->ci->legal_casefactory->get_instance();
        }
        $query = [];
        $query["select"] = ["events.*,CASE WHEN created.status='Inactive' THEN ( created.firstName+' '+created.lastName +' (Inactive)') ELSE ( created.firstName+ ' '+ created.lastName) END as created_by_name, legal_cases.subject as case_subject, legal_cases.category as case_category, task_locations.name AS location", false];
        $query["where"][] = ["events.id", $id];
        $where_condition = $this->ci->legal_case->get_matter_privacy_conditions($logged_user_id, $override_privacy, false);
        $query["where"][] = ["(events.legal_case_id IS NULL OR " . $where_condition . ")", NULL, false];
        $query["where"][] = ["(events.private IS NULL OR events.private = 'no' OR (events.private = 'yes' AND (events.id IN (SELECT event_id FROM events_attendees WHERE user_id = '" . $logged_user_id . "') OR '" . $override_privacy . "' = 'yes')))", NULL, false];
        $query["join"] = [["legal_cases", "legal_cases.id = events.legal_case_id", "left"], ["user_profiles created", "created.user_id = events.createdBy", "left"], ["task_locations", "task_locations.id = events.task_location_id", "left"]];
        return $this->load($query);
    }
    public function load_attendees($event_id, $custom_structure_for_meeting = false)
    {
        $users = [];
        $events_attendees = $this->ci->db->select("events_attendees.user_id as id, events_attendees.mandatory as mandatory , events_attendees.participant as participant , CASE WHEN UP.status='Inactive' THEN ( UP.firstName+ ' '+UP.lastName + ' (Inactive)') ELSE ( UP.firstName+ ' '+ UP.lastName) END as name", false)->join("user_profiles UP", "UP.user_id = events_attendees.user_id", "inner")->where("events_attendees.event_id", $event_id)->get("events_attendees");
        if (!$events_attendees->num_rows()) {
            return $users;
        }
        if ($custom_structure_for_meeting) {
            foreach ($events_attendees->result() as $key => $user) {
                $users[$key]["id"] = (string) $user->id;
                $users[$key]["name"] = $user->name;
                $users[$key]["mandatory"] = $user->mandatory;
                $users[$key]["participant"] = $user->participant;
            }
        } else {
            foreach ($events_attendees->result() as $user) {
                $users[(string) $user->id] = $user->name;
            }
        }
        return $users;
    }
    public function load_all_events($page_size, $skip, $term = "")
    {
        if (!isset($this->ci->legal_case)) {
            $this->ci->load->model("legal_case", "legal_casefactory");
            $this->ci->legal_case = $this->ci->legal_casefactory->get_instance();
        }
        $query = $response = [];
        $query["select"] = ["events.*,CASE WHEN created.status='Inactive' THEN (created.firstName + ' ' + created.lastName + ' (Inactive)') ELSE ( created.firstName + ' ' + created.lastName) END as created_by_name, legal_cases.subject as case_subject, legal_cases.category as case_category, task_locations.name AS location", false];
        if ($term != "") {
            $term = $this->ci->db->escape_like_str($term);
            $query["where"][] = [" ( events.title  LIKE '%" . $term . "%' )", NULL, false];
        }
        $query["join"] = [["legal_cases", "legal_cases.id = events.legal_case_id", "left"], ["user_profiles created", "created.user_id = events.createdBy", "left"], ["task_locations", "task_locations.id = events.task_location_id", "left"]];
        $this->prep_query($query);
        $response["totalRows"] = $this->ci->db->count_all_results($this->_table);
        $query["order_by"] = ["events.start_time asc"];
        $query["limit"] = [$page_size, $skip];
        $response["data"] = $this->load_all($query);
        return $response;
    }
    public function load_events($user_id, $page_size, $skip, $term = "")
    {
        $logged_user_id = $user_id ? $user_id : $this->ci->is_auth->get_user_id();
        $this->ci->load->model("user_profile");
        $this->ci->user_profile->fetch(["user_id" => $logged_user_id]);
        $override_privacy = $this->ci->user_profile->get_field("overridePrivacy");
        if (!isset($this->ci->legal_case)) {
            $this->ci->load->model("legal_case", "legal_casefactory");
            $this->ci->legal_case = $this->ci->legal_casefactory->get_instance();
        }
        $query = $response = [];
        $query["select"] = ["events.*,CASE WHEN created.status='Inactive' THEN (created.firstName + ' ' + created.lastName + ' (Inactive)') ELSE ( created.firstName + ' ' + created.lastName) END as created_by_name, legal_cases.subject as case_subject, legal_cases.category as case_category, task_locations.name AS location", false];
        if ($term != "") {
            $term = $this->ci->db->escape_like_str($term);
            $query["where"][] = [" ( events.title  LIKE '%" . $term . "%' )", NULL, false];
        }
        $query["where"][] = ["events_attendees.user_id", $user_id];
        $where_condition = $this->ci->legal_case->get_matter_privacy_conditions($user_id, $override_privacy, false);
        $query["where"][] = ["(events.legal_case_id IS NULL OR " . $where_condition . ")", NULL, false];
        $query["where"][] = ["(events.private IS NULL OR events.private = 'no' OR (events.private = 'yes' AND (events.id IN (SELECT event_id FROM events_attendees WHERE user_id = '" . $logged_user_id . "') OR '" . $override_privacy . "' = 'yes')))", NULL, false];
        $query["join"] = [["legal_cases", "legal_cases.id = events.legal_case_id", "left"], ["user_profiles created", "created.user_id = events.createdBy", "left"], ["events_attendees", "events_attendees.event_id = events.id", "left"], ["task_locations", "task_locations.id = events.task_location_id", "left"]];
        $this->prep_query($query);
        $response["totalRows"] = $this->ci->db->count_all_results($this->_table);
        $query["order_by"] = ["events.start_time asc"];
        $query["limit"] = [$page_size, $skip];
        $response["data"] = $this->load_all($query);
        return $response;
    }
    public function load_todays_meetings($is_api = false)
    {
        $user_id = $is_api ? $this->ci->user_logged_in_data["user_id"] : $this->ci->session->userdata("AUTH_user_id");
        $query = [];
        $query["select"] = ["events.id, events.legal_case_id, events.start_date, events.start_time, events.end_date, events.end_time, events.title, events.description", false];
        $query["join"] = ["events_attendees", "events_attendees.event_id = events.id", "left"];
        $query["where"][] = ["CAST(GETDATE() as date) = events.start_date", NULL, false];
        $query["where"][] = ["events_attendees.user_id", $user_id];
        $query["order_by"] = ["start_time asc"];
        $response = $this->load_all($query);
        foreach ($response as $key => $event) {
            $start = new DateTime($event["start_date"] . " " . $event["start_time"]);
            $end = new DateTime($event["end_date"] . " " . $event["end_time"]);
            $response[$key]["startTime"] = date("h:iA", strtotime($event["start_date"] . " " . $event["start_time"]));
            $response[$key]["start_date"] = $start->format(DateTime::ATOM);
            $response[$key]["end_date"] = $end->format(DateTime::ATOM);
        }
        return $response;
    }
}

?>