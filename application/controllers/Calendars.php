<?php

if (!defined("BASEPATH")) {
    exit("No direct script access allowed");
}
require APPPATH . "controllers/Top_controller.php";
require_once substr(COREPATH, 0, -12) . "/application/libraries/scheduler/scheduler_connector.php";
require_once substr(COREPATH, 0, -12) . "/application/libraries/scheduler/db_phpci.php";
class Calendars extends Top_controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model("event", "eventfactory");
        $this->event = $this->eventfactory->get_instance();
        $this->load->model("event_attendee");
        $this->currentTopNavItem = "calendars";
        DataProcessor::$action_param = "dhx_editor_status";
    }
    public function view($date = "")
    {
        if ($this->input->is_ajax_request()) {
            if ($this->input->post("calendarSettings")) {
                $response["result"] = $this->user_preference->set_value("calendarSettings", $this->input->post("calendarSettings"), true);
                $_POST["calendar_settings"] = $this->user_preference->get_value("calendarSettings");
                $response["mode"] = $this->return_calendar_settings($this->input->post("calendar_settings"));
                $this->output->set_content_type("application/json")->set_output(json_encode($response));
            } else {
                $this->load_events();
            }
        } else {
            $this->pageTitle = sprintf($this->lang->line("sheria360_browser_title"), $this->lang->line("calendar"));
            $this->load->model("user", "userfactory");
            $this->user = $this->userfactory->get_instance();
            $_POST["logged_user_id"] = $this->is_auth->get_user_id();
            $_POST["users"] = $this->user->load_users_list();
            $_POST["calendar_settings"] = $this->user_preference->get_value("calendarSettings");
            $_POST["user_calendar_settings"] = $this->return_calendar_settings($this->input->post("calendar_settings"));
            $_POST["selected_date"] = $date;
            $integration = $this->user_preference->get_value("integration");
            $integration_settings = unserialize($integration);
            $_POST["show_calendar_integration_popup"] = false;
            $_POST["calendar_integration_enabled"] = !empty($integration_settings["calendar"]["enabled"]) ? true : false;
            if ($this->cloud_installation_type == "cloud" && empty($integration_settings["calendar"]["enabled"]) && empty($integration_settings["calendar"]["integration_popup_displayed"])) {
                $integration_settings["calendar"]["integration_popup_displayed"] = true;
                $this->user_preference->set_value("integration", serialize($integration_settings), true);
                $_POST["show_calendar_integration_popup"] = true;
            }
            $this->sync_integration_provider_events();
            $this->includes("scheduler/css/dhtmlxscheduler", "css");
            $this->includes("scheduler/js/dhtmlxscheduler", "js");
            $this->includes("scheduler/js/dhtmlxcore", "js");
            $this->includes("scheduler/js/dhtmlxscheduler_limit", "js");
            $this->includes("scheduler/js/dhtmlxscheduler_year_view", "js");
            $this->includes("scheduler/js/locale/locale_" . strtolower(substr($this->session->userdata("AUTH_language"), 0, 2)), "js");
            $this->includes("jquery/spectrum", "js");
            $this->includes("scripts/calendar", "js");
            $this->includes("bootstrap/toggle/css/bootstrap-toggle.min", "css");
            $this->includes("bootstrap/toggle/js/bootstrap-toggle.min", "js");
            $this->load->view("partial/header");
            $this->load->view("calendars/index", $this->input->post(NULL));
            $this->load->view("partial/footer");
        }
    }
    private function return_calendar_settings($settings)
    {
        $user_calendar_settings = [];
        if ($settings !== "") {
            $calendar_settings = explode("&", $settings);
            if (is_array($calendar_settings)) {
                foreach ($calendar_settings as $val) {
                    $key_val = explode("=", $val);
                    $user_calendar_settings[$key_val[0]] = $key_val[1];
                }
            }
        }
        return $user_calendar_settings;
    }
    private function load_events()
    {
        if ($this->input->get("min_date") && $this->input->get("max_date")) {
            $dates = ["min_date" => $this->input->get("min_date"), "max_date" => $this->input->get("max_date")];
            $this->sync_integration_provider_events($dates);
        }
        if ($this->input->get("users")) {
            $users = $this->input->get("users");
            $users_id = $users[0];
        } else {
            $calendar_settings = $this->return_calendar_settings($this->user_preference->get_value("calendarSettings"));
            $users_id = isset($calendar_settings["usersIds"]) ? $calendar_settings["usersIds"] : [];
        }
        $this->load->database();
        $calendar = new SchedulerConnector($this->db, "PHPCI");
        if (!empty($users_id)) {
            $calendar->render_complex_sql("select *,events.id as ev_id from events left join events_attendees on events.id= events_attendees.event_id where events_attendees.user_id IN (" . $users_id . ")", "events.id", "start_date,end_date,title, start_time, end_time,events.id(ev_id),user_id,private");
        } else {
            $calendar->render_complex_sql("select * from events where 1 = 2", "events.id", "start_date,end_date,title, start_time, end_time,events.id(ev_id),user_id,private");
        }
    }
    public function add()
    {
        if (!$this->input->post(NULL)) {
            $data = $response = [];
            $data["priorities"] = array_combine($this->event->get("priority_values"), [$this->lang->line("critical"), $this->lang->line("high"), $this->lang->line("medium"), $this->lang->line("low")]);
            $data["title"] = $this->lang->line("add_meeting");
            $default_date = date("Y-m-d", time());
            $time = ceil(ceil(time() / 900 + 1) * 900 / 1800) * 1800;
            $start_time = date("H:i", $time);
            $end_time = date("H:i", $time + 3600);
            $this->load->model("event_type", "event_typefactory");
            $this->event_type = $this->event_typefactory->get_instance();
            $meeting_types = $this->event_type->load_list_per_language();
            $data["meeting_data"] = ["id" => "", "priority" => "medium", "title" => "", "start_date" => $default_date, "end_date" => $default_date, "start_time" => $start_time, "end_time" => $end_time, "description" => "", "task_location_id" => "", "location" => "", "private" => "", "case_subject" => "", "event_type_id" => "", "meeting_types" => $meeting_types];
            $this->load->model("email_notification_scheme");
            $data["hide_show_notification"] = $this->email_notification_scheme->get_hide_show_send_email_notification_by_trigger_action("add_meeting") == "1" ? "yes" : "";
            $response["html"] = $this->load->view("calendars/form", $data, true);
        } else {
            $response = $this->save("add");
        }
        $this->output->set_content_type("application/json")->set_output(json_encode($response));
    }
    public function edit($id)
    {
        $response = [];
        $this->load->model("legal_case", "legal_casefactory");
        $this->legal_case = $this->legal_casefactory->get_instance();
        if (!$this->input->post(NULL)) {
            $data = [];
            $this->load->model("event_type", "event_typefactory");
            $this->event_type = $this->event_typefactory->get_instance();
            $meeting_types = $this->event_type->load_list_per_language();
            $data["meeting_data"] = $this->event->load_event($id);
            if ($data["meeting_data"]) {
                $data["meeting_data"]["meeting_types"] = $meeting_types;
                $this->load->model("email_notification_scheme");
                $data["hide_show_notification"] = $this->email_notification_scheme->get_hide_show_send_email_notification_by_trigger_action("edit_meeting") == "1" ? "yes" : "";
                $data["priorities"] = array_combine($this->event->get("priority_values"), [$this->lang->line("critical"), $this->lang->line("high"), $this->lang->line("medium"), $this->lang->line("low")]);
                $data["title"] = $this->lang->line("edit_meeting");
                $data["attendees"] = $this->event->load_attendees($id, true);
                $data["case_model_code"] = $this->legal_case->get("modelCode");
                $response["html"] = $this->load->view("calendars/form", $data, true);
            }
        } else {
            if ($this->event->fetch(["id" => $id])) {
                $response = $this->save("edit");
            }
        }
        $this->output->set_content_type("application/json")->set_output(json_encode($response));
    }
    private function send_notifications($object_type, $attendees = [])
    {
        $this->load->library("system_notification");
        $this->load->library("email_notifications");
        $this->load->model("email_notification_scheme");
        $event_id = $this->event->get_field("id");
        $this->load->model("user", "userfactory");
        $this->user = $this->userfactory->get_instance();
        $this->user->fetch($this->event->get_field("createdBy"));
        $meeting_creater_id = str_pad($this->event->get_field("createdBy"), 10, "0", STR_PAD_LEFT);
        foreach ($attendees as $key => $user_id) {
            $this->user->fetch($user_id);
            $to_emails[] = $this->user->get_field("email");
        }
        $notifications_data = ["to" => $to_emails, "toIds" => $attendees, "object" => $object_type, "object_id" => $event_id, "object_title" => $this->event->get_field("title"), "targetUser" => $meeting_creater_id, "secondTargetUser" => "", "invitees" => $this->email_notification_scheme->get_user_full_name($attendees)];
        $this->system_notification->notification_add($notifications_data);
        $attachments = [];
        $send_email_flag = $this->input->post("send_notifications_email");
        if ($send_email_flag) {
            $system_preference = $this->session->userdata("systemPreferences");
            $time_zone = isset($system_preference["systemTimezone"]) && $system_preference["systemTimezone"] ? $system_preference["systemTimezone"] : $this->config->item("default_timezone");
            $this->load->model("language");
            $languages = $this->language->load_all();
            $ical_languages = [];
            foreach ($languages as $value) {
                $ical_languages[$value["fullName"]] = strtoupper($value["name"]);
            }
            $this->load->helper("ical");
            $meeting_data = ["startDateTime" => $this->event->get_field("start_date") . " " . $this->event->get_field("start_time"), "endDateTime" => $this->event->get_field("end_date") . " " . $this->event->get_field("end_time"), "userEmail" => $this->is_auth->get_email_address(), "timezone" => $time_zone, "meetingLocation" => $this->input->post("location"), "summary" => $this->event->get_field("description"), "subject" => $this->event->get_field("title"), "language" => $this->session->userdata("AUTH_language"), "languages" => $ical_languages];
            $attachments[0]["path"] = create_ical_event($this->config->item("files_path"), $meeting_data);
            $attachments[0]["name"] = "event";
            $model = $this->event->get("_table");
            $model_data = ["id" => $event_id, "watchers_ids" => $attendees];
            $notifications_emails = $this->email_notification_scheme->get_emails($object_type, $model, $model_data);
            extract($notifications_emails);
            $notifications_data["to"] = $to_emails;
            $notifications_data["cc"] = $cc_emails;
            $notifications_data["meeting_data"] = ["event_id" => $event_id, "priority" => $this->event->get_field("priority"), "start_date" => $this->event->get_field("start_date") . " - " . $this->event->get_field("start_time"), "title" => $this->event->get_field("title"), "to" => $this->event->get_field("end_date") . " - " . $this->event->get_field("end_time"), "modified_by" => $this->email_notification_scheme->get_user_full_name($this->event->get_field("modifiedBy")), "description" => strip_tags($this->event->get_field("description"), "<h1><h2><h3><h4><h5><h6><strong><em><u><a><p>")];
            $notifications_data["attachments"] = $attachments;
            $notifications_data["fromLoggedUser"] = $this->is_auth->get_fullname();
            $this->email_notifications->notify($notifications_data);
        }
    }
    public function delete()
    {
        $response = ["result" => false, "hearing_related" => false];
        $this->load->model("legal_case_event_related_data", "legal_case_event_related_datafactory");
        $this->legal_case_event_related_data = $this->legal_case_event_related_datafactory->get_instance();
        $id = $this->input->post("id");
        $related_object = "event";
        if ($id && $this->event->fetch(["id" => $id])) {
            $integration_id = $this->event->get_field("integration_id");
            $this->load->model("legal_case_hearing", "legal_case_hearingfactory");
            $this->legal_case_hearing = $this->legal_case_hearingfactory->get_instance();
            $hearings_events_rows_num_related = $this->legal_case_hearing->count_related_hearings($id);
            if ($hearings_events_rows_num_related <= 0) {
                $this->event_attendee->delete(["where_in" => ["event_id", $id]]);
                if ($result = $this->event->delete(["where" => ["id", $id]])) {
                    if ($integration_settings = $this->event->check_integration()) {
                        extract($integration_settings);
                        if ($integration_id && $integration_settings["calendar"]["selected_calendar"] === $this->event->get_field("calendar_id")) {
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
                    if ($this->legal_case_event_related_data->fetch(["related_id" => $id, "related_object" => $related_object])) {
                        $this->legal_case_event_related_data->delete(["where" => [["related_id", $id], ["related_object", $related_object]]]);
                    }
                    $response["result"] = $result;
                }
            } else {
                $response["hearing_related"] = true;
            }
        }
        $this->output->set_content_type("application/json")->set_output(json_encode($response));
    }
    private function save($mode)
    {
        $result = false;
        $post_data = $this->input->post(NULL);
        $_POST["start_time"] = date("H:i", strtotime($post_data["start_time"]));
        $_POST["end_time"] = date("H:i", strtotime($post_data["end_time"]));
        $post_data["start_time"] = $this->input->post("start_time");
        $post_data["end_time"] = $this->input->post("end_time");
        $this->load->helper("format_comment_patterns");
        $description = $this->input->post("description", true, "<h1><h2><h3><h4><h5><h6><strong><em><u><a><p><img><br>");
        $post_data["description"] = format_comment_patterns($this->regenerate_note($description));
        $this->event->set_fields($post_data);
        $this->event->set_field("description", $this->input->post("description", true, "<h1><h2><h3><h4><h5><h6><strong><em><u><a><p><img><br>"));
        $this->event->set_field("private", $this->input->post("private"));
        if ($mode == "add") {
            $this->event->set_field("priority", $this->input->post("priority") ? $this->input->post("priority") : "medium");
            $this->event->set_field("created_from", "a4l");
        }
        $events_lookup_validate_errors = $this->event->get_lookup_validation_errors($this->event->get("events_lookup_inputs_validation"), $post_data);
        if ($this->event->validate() && !$events_lookup_validate_errors) {
            $result = $mode == "add" ? $this->event->insert() : $this->event->update();
            if ($result) {
                if ($mode === "add") {
                    $getting_started_settings = unserialize($this->user_preference->get_value("getting_started"));
                    $getting_started_settings["add_calendar_meeting_step_done"] = true;
                    $this->user_preference->set_value("getting_started", serialize($getting_started_settings), true);
                }
                if (!empty($post_data["legal_case_id"]) && $post_data["legal_case_id"]) {
                    $this->load->model("legal_case", "legal_casefactory");
                    $this->legal_case = $this->legal_casefactory->get_instance();
                    $this->legal_case->set_field("id", $post_data["legal_case_id"]);
                    $this->legal_case->touch_logs();
                }
                $this->load->model("notification", "notificationfactory");
                $this->notification = $this->notificationfactory->get_instance();
                $event_id = $this->event->get_field("id");
                $attendees = $this->input->post("type") ? array_keys($this->event->load_attendees($event_id)) : $this->input->post("attendees");
                $mandatory = $this->input->post("mandatory");
                $participant = $this->input->post("participant");
                $attendees_lookup_validate_errors = $this->event->get_lookup_validation_errors($this->event->get("attendees_lookup_inputs_validation"), $post_data);
                if (!$attendees_lookup_validate_errors) {
                    if ($mode == "add" && (!is_array($attendees) || is_array($attendees) && !in_array($this->is_auth->get_user_id(), $attendees))) {
                        $attendees[] = $this->is_auth->get_user_id();
                        if (!$mandatory) {
                            $mandatory[] = 1;
                        }
                    } else {
                        if ($mode == "edit" && (!is_array($attendees) || is_array($attendees) && !in_array($this->event->get_field("createdBy"), $attendees))) {
                            $attendees[] = $this->event->get_field("createdBy");
                        }
                    }
                    $event_data = ["event_id" => $event_id, "attendees" => $attendees, "mandatory" => $mandatory];
                    $this->event_attendee->insert_attendees($event_data, $mandatory, $participant);
                    $response["total_notifications"] = $this->notification->update_pending_notifications($attendees);
                    $this->send_notifications($mode . "_meeting", $attendees);
                    $this->event->update_integration_provider_calendar($event_id, $mode);
                    $response["event_id"] = $event_id;
                } else {
                    $result = false;
                    $response["validation_errors"] = $this->event->get_validation_errors($attendees_lookup_validate_errors);
                }
                $response["clone"] = $this->input->post("clone");
            }
        } else {
            $response["validation_errors"] = $this->event->get_validation_errors($events_lookup_validate_errors);
        }
        $response["result"] = $result;
        return $response;
    }
    public function calendars_list($integration_type)
    {
        $provider_oauth = $this->event->load_integration_provider_library($integration_type);
        if ($integration_type == "google") {
            $token = $this->session->userdata("google_access_token");
            $provider_oauth->setAccessToken($token);
        } else {
            if ($integration_type == "ms_cloud") {
                $token = $this->session->userdata("ms_cloud_access_token");
                $provider_oauth->set_access_token($token);
            }
        }
        if ($integration_type == "google" && $provider_oauth->getAccessToken() || $integration_type == "ms_cloud" && !empty($token)) {
            if (!$this->input->post(NULL)) {
                if ($integration_type == "google") {
                    $service = new Google_Service_Calendar($provider_oauth);
                    $calendar_list = $service->calendarList->listCalendarList();
                    foreach ($calendar_list->getItems() as $calendar_list_entry) {
                        if ($calendar_list_entry->getAccessRole() == "owner") {
                            $data["my_calendars"][$calendar_list_entry->getId()] = $calendar_list_entry->getSummary();
                        }
                    }
                } else {
                    if ($integration_type == "ms_cloud") {
                        $outlook_calendar = new Outlook_Calendar($provider_oauth);
                        $calendar_list = $outlook_calendar->list_calendars();
                        foreach ($calendar_list->value as $calendar) {
                            $data["my_calendars"][$calendar->id] = $calendar->name;
                        }
                    }
                }
                $this->session->set_userdata("my_calendars", $data["my_calendars"]);
                $this->load->view("partial/header");
                $this->load->view("calendars/calendars_list", $data);
                $this->load->view("partial/footer");
            } else {
                if ($this->input->post("selected_calendar") == "sheria360") {
                    $calendar_name = "Sheria360";
                    if ($integration_type == "google") {
                        $service = new Google_Service_Calendar($provider_oauth);
                        $calendar = new Google_Service_Calendar_Calendar();
                        $calendar->setSummary($calendar_name);
                        $created_calendar = $service->calendars->insert($calendar);
                        $selected_calendar = $created_calendar->getId();
                    } else {
                        $outlook_calendar = new Outlook_Calendar($provider_oauth);
                        $created_calendar = $outlook_calendar->create_calendar(["name" => $calendar_name]);
                        $selected_calendar = $created_calendar->id;
                    }
                } else {
                    $selected_calendar = $this->input->post("selected_calendar");
                    $my_calendars = $this->session->userdata("my_calendars");
                    $calendar_name = $my_calendars[$selected_calendar];
                }
                $data["calendar"]["enabled"] = true;
                $data["calendar"]["provider"] = $integration_type;
                $data["calendar"]["selected_calendar"] = $selected_calendar;
                $data["calendar"]["calendar_name"] = $calendar_name;
                $data["calendar"]["token"] = $this->session->userdata($integration_type . "_access_token");
                $old_settings = unserialize($this->user_preference->get_value("integration"));
                $data["calendar"]["integration_popup_displayed"] = isset($old_settings["calendar"]["integration_popup_displayed"]);
                if (isset($old_settings["calendar"]["token"]["refresh_token"]) && !isset($data["calendar"]["token"]["refresh_token"])) {
                    $data["calendar"]["token"]["refresh_token"] = $old_settings["calendar"]["token"]["refresh_token"];
                }
                $this->user_preference->set_value("integration", serialize($data), true);
                $this->set_flashmessage("success", $this->lang->line($integration_type . "_calendar_sync_done"));
                redirect("calendars/view");
            }
        } else {
            $this->set_flashmessage("error", $this->lang->line("calendar_sync_not_done"));
            redirect("users/profile");
        }
    }
    private function sync_integration_provider_events($range_date = [])
    {
        if ($integration_settings = $this->event->check_integration()) {
            extract($integration_settings);
            $opt_params = [];
            if ($integration_settings["calendar"]["provider"] == "google") {
                $service = new Google_Service_Calendar($provider_oauth);
                $opt_params = ["orderBy" => "startTime", "singleEvents" => true, "showDeleted" => true];
            } else {
                if ($integration_settings["calendar"]["provider"] == "ms_cloud") {
                    $outlook_calendar = new Outlook_Calendar($provider_oauth);
                    $system_preferences = $this->system_preference->get_values();
                    $timezone = isset($system_preferences["systemTimezone"]) && $system_preferences["systemTimezone"] ? $system_preferences["systemTimezone"] : $this->config->item("default_timezone");
                }
            }
            $user_calendar_settings = $this->return_calendar_settings($this->user_preference->get_value("calendarSettings"));
            if (empty($range_date)) {
                $user_calendar_settings["view"] = !isset($user_calendar_settings["view"]) ? "week" : $user_calendar_settings["view"];
                switch ($user_calendar_settings["view"]) {
                    case "day":
                        $opt_params["timeMin"] = date(DATE_ATOM, mktime(0, 0, 0, date("m"), date("d"), date("Y")));
                        $opt_params["timeMax"] = date(DATE_ATOM, mktime(0, 0, 0, date("m"), date("d") + 1, date("Y")));
                        break;
                    case "week":
                        $day = date("w");
                        $week_start = date("Y-m-d", strtotime("-" . $day . " days"));
                        $week_end = date("Y-m-d", strtotime("+" . (7 - $day) . " days"));
                        $opt_params["timeMin"] = date(DATE_ATOM, mktime(0, 0, 0, date("m", strtotime($week_start)), date("d", strtotime($week_start)) + 1, date("Y")));
                        $opt_params["timeMax"] = date(DATE_ATOM, mktime(23, 59, 59, date("m", strtotime($week_end)), date("d", strtotime($week_end)), date("Y")));
                        break;
                    case "month":
                        $opt_params["timeMin"] = date(DATE_ATOM, mktime(0, 0, 0, date("m"), 1, date("Y")));
                        $opt_params["timeMax"] = date(DATE_ATOM, mktime(0, 0, 0, date("m") + 1, 1, date("Y")));
                        break;
                    default:
                        $opt_params["timeMin"] = date(DATE_ATOM, mktime(0, 0, 0, date("m"), 1, date("Y")));
                        $opt_params["timeMax"] = date(DATE_ATOM, mktime(0, 0, 0, date("m") + 1, 1, date("Y")));
                }
            } else {
                $opt_params["timeMin"] = date(DATE_ATOM, mktime(0, 0, 0, date("m", strtotime($range_date["min_date"])), date("d", strtotime($range_date["min_date"])), date("Y", strtotime($range_date["min_date"]))));
                $opt_params["timeMax"] = date(DATE_ATOM, mktime(0, 0, 0, date("m", strtotime($range_date["max_date"])), date("d", strtotime($range_date["max_date"])), date("Y", strtotime($range_date["max_date"]))));
            }
            if ($integration_settings["calendar"]["provider"] == "google") {
                $events = $service->events->listEvents($integration_settings["calendar"]["selected_calendar"], $opt_params);
            } else {
                if ($integration_settings["calendar"]["provider"] == "ms_cloud") {
                    $events = $outlook_calendar->list_calendar_view($integration_settings["calendar"]["selected_calendar"], $opt_params["timeMin"], $opt_params["timeMax"], $timezone);
                }
            }
            $this->load->model("legal_case_hearing", "legal_case_hearingfactory");
            $this->legal_case_hearing = $this->legal_case_hearingfactory->get_instance();
            if ($integration_settings["calendar"]["provider"] == "google") {
                while (true) {
                    foreach ($events->getItems() as $events_list_entry) {
                        $parsed_data = $this->parse_fetched_event_data($integration_settings["calendar"]["provider"], $integration_settings["calendar"]["selected_calendar"], $events_list_entry);
                        if ($events_list_entry->getStatus() !== "cancelled") {
                            $this->integrate_fetched_event($parsed_data);
                        } else {
                            if ($this->event->fetch(["integration_id" => $parsed_data["event"]["integration_id"]])) {
                                $event_id = $this->event->get_field("id");
                                $this->legal_case_hearing->update_event_id($event_id);
                                $this->event_attendee->delete(["where_in" => ["event_id", $event_id]]);
                                $this->event->delete(["where" => ["id", $event_id]]);
                            }
                        }
                    }
                    $page_token = $events->getNextPageToken();
                    if ($page_token) {
                        $opt_params = ["pageToken" => $page_token];
                        $events = $service->events->listEvents($integration_settings["calendar"]["selected_calendar"], $opt_params);
                    }
                }
            } else {
                if ($integration_settings["calendar"]["provider"] == "ms_cloud") {
                    $synced_events = [];
                    foreach ($events->value as $event_data) {
                        $synced_events[] = $event_data->id;
                        $parsed_data = $this->parse_fetched_event_data($integration_settings["calendar"]["provider"], $integration_settings["calendar"]["selected_calendar"], $event_data);
                        $this->integrate_fetched_event($parsed_data);
                    }
                    $this->event->delete_ms_cloud_calendar_removed_events($synced_events);
                }
            }
            return true;
        }
        return false;
    }
    private function parse_fetched_event_data($integration_type, $calendar_id, $event_data)
    {
        $event = $event_meta_data = $data = [];
        $event["integration_type"] = $integration_type;
        $event["created_from"] = $integration_type;
        $event["calendar_id"] = $calendar_id;
        $event["priority"] = "medium";
        if ($integration_type == "google") {
            $event["integration_id"] = $event_data->getId();
            $event["title"] = $event_data->getSummary() ? $event_data->getSummary() : $this->lang->line("no_title");
            $event["private"] = $event_data->getVisibility() == "private" ? "yes" : "no";
            $event["createdOn"] = date("Y-m-d H:i:s", strtotime($event_data->getCreated()));
            $event["modifiedOn"] = date("Y-m-d H:i:s", strtotime($event_data->getUpdated()));
            $start_date_time = $event_data->getStart();
            if (isset($start_date_time["dateTime"]) && !empty($start_date_time["dateTime"])) {
                $event["start_date"] = date("Y-m-d", strtotime($start_date_time["dateTime"]));
                $event["start_time"] = date("H:i:s", strtotime($start_date_time["dateTime"]));
            } else {
                $event["start_date"] = date("Y-m-d", strtotime($start_date_time["date"]));
                $event["start_time"] = date("H:i:s", strtotime($start_date_time["date"]));
            }
            $end_date_time = $event_data->getEnd();
            if (isset($end_date_time["dateTime"]) && !empty($end_date_time["dateTime"])) {
                $event["end_date"] = date("Y-m-d", strtotime($end_date_time["dateTime"]));
                $event["end_time"] = date("H:i:s", strtotime($end_date_time["dateTime"]));
            } else {
                $event["end_date"] = date("Y-m-d", strtotime($end_date_time["date"]));
                $event["end_time"] = date("H:i:s", strtotime($end_date_time["date"]));
            }
            $event_meta_data["location_name"] = $event_data->getLocation();
            $creator_data = $event_data->getCreator();
            $event_meta_data["creator_email"] = $creator_data["email"];
            $data["event_attendees"] = $event_data->getAttendees();
        } else {
            if ($integration_type == "ms_cloud") {
                $event["integration_id"] = $event_data->id;
                $event["title"] = $event_data->subject ? $event_data->subject : $this->lang->line("no_title");
                $event["private"] = $event_data->sensitivity != "normal" ? "yes" : "no";
                $event["createdOn"] = date("Y-m-d H:i:s", strtotime($event_data->createdDateTime));
                $event["modifiedOn"] = date("Y-m-d H:i:s", strtotime($event_data->lastModifiedDateTime));
                $event["start_date"] = date("Y-m-d", strtotime($event_data->start->dateTime));
                $event["start_time"] = date("H:i:s", strtotime($event_data->start->dateTime));
                $event["end_date"] = date("Y-m-d", strtotime($event_data->end->dateTime));
                $event["end_time"] = date("H:i:s", strtotime($event_data->end->dateTime));
                $event_meta_data["location_name"] = $event_data->location->displayName;
                $event_meta_data["creator_email"] = $event_data->organizer->emailAddress->address;
                $data["event_attendees"] = [];
                foreach ($event_data->attendees as $k => $attendees_details) {
                    $data["event_attendees"][$k]["email"] = $attendees_details->emailAddress->address;
                    $data["event_attendees"][$k]["optional"] = $attendees_details->type == "required" ? 0 : 1;
                }
            }
        }
        if ($this->task_location->fetch(["name" => $event_meta_data["location_name"]])) {
            $event["task_location_id"] = $this->task_location->get_field("id");
        }
        $this->user->reset_fields();
        if ($this->user->fetch(["email" => $event_meta_data["creator_email"]])) {
            $creator_id = $this->user->get_field("id");
        } else {
            $creator_id = $this->is_auth->get_user_id();
        }
        $event["createdBy"] = $creator_id;
        $event["modifiedBy"] = $creator_id;
        $data["event"] = $event;
        return $data;
    }
    private function integrate_fetched_event($data)
    {
        $this->event->reset_fields();
        $this->event->disable_builtin_logs();
        if ($this->event->fetch(["integration_id" => $data["event"]["integration_id"]])) {
            $this->event->set_fields($data["event"]);
            $this->event->update();
        } else {
            $this->event->set_fields($data["event"]);
            $this->event->insert();
        }
        $attendees = [];
        $mandatory = [];
        if (empty($data["event_attendees"])) {
            $attendees[] = $data["event"]["createdBy"];
        } else {
            foreach ($data["event_attendees"] as $key => $attendees_details) {
                if ($this->user->fetch(["email" => $attendees_details["email"]])) {
                    $attendees[$key] = $this->user->get_field("id");
                    $mandatory[$key] = isset($attendees_details["optional"]) && $attendees_details["optional"] == 1 ? 0 : 1;
                }
            }
            if (empty($attendees)) {
                $attendees[] = $data["event"]["createdBy"];
            }
        }
        if (!in_array($this->is_auth->get_user_id(), $attendees)) {
            $attendees[] = $this->is_auth->get_user_id();
        }
        $event_id = $this->event->get_field("id");
        $event_data = ["event_id" => $event_id, "attendees" => $attendees];
        $this->event_attendee->insert_attendees($event_data, $mandatory);
    }
    public function integrations_list()
    {
        if ($this->input->is_ajax_request()) {
            $response = [];
            $integration = $this->user_preference->get_value("integration");
            $data["integration_settings"] = unserialize($integration);
            $response["html"] = $this->load->view("calendars/integrations_list", $data, true);
            $this->output->set_content_type("application/json");
            $this->output->set_output(json_encode($response));
        } else {
            show_404();
        }
    }
}

