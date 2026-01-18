<?php

if (!defined("BASEPATH")) {
    exit("No direct script access allowed");
}
require "App_controller.php";
define("APPNAME", "App4Legal");
class Top_controller extends App_Controller
{
    public $js = ["base" => "assets/", "files" => []];
    public $css = ["base" => "assets/", "files" => []];
    public $js_footer = ["base" => "assets/", "files" => []];
    public $css_footer = ["base" => "assets/", "files" => []];
    public $currentTopNavItem = "nothing-yet";
    public $is_auth;
    public $top_nav_menu = "applinks";
    public $pageTitle = "";
    public $userActivityLogTimer = false;
    public $license_availability = false;
    public $contract_license_availability = false;
    public $notificationRefreshInterval;
    public $remindersRefreshInterval;
    public $totalRemindersNotifications;
    public $allowFeatureCustomerPortal;
    public $allowFeatureAdvisor;
    public $currentLanguages = [];
    public $app_main_logo_name = "";
    public $instance_data_array = [];
    public $cloud_installation_type = "";
    public $instance_client_type = "";
    public $instance_data_values = [];
    public $app_login_second_logo = "";
    public $sqlsrv_2008 = false;
    public $document_editor_download_url = "https://docs.sheria360.com/display/s360/sheria360+Document+Editor";
    public $default_allowed_tags = "<h1><h2><h3><h4><h5><h6><strong><em><u><a><p>";
    public $web_channel = "A4L";
    public $mobile_channel = "MOB";
    public $outlook_channel = "MSO";
    public $cp_channel = "CP";
    public $gmail_channel = "A4G";
    public $microsoft_teams_channel = "MST";
    public $selected_plan = false;
    public $plan_excluded_features = false;
    public $plan_feature_warning_msgs = false;
    public $instance_subscription = "";
    public $app_trial_period = "";
    public $user_guide = false;
    public $workthrough = false;
    public $first_sign_in = false;
    public $max_drop_down_length = 10;
    public function __construct()
    {
        parent::__construct();
        $this->exit_if_upgrading();
        $this->app_main_logo_name = $this->getInstanceConfig("login_second_logo");
        $this->load->model("instance_data");
        $this->instance_data_values = $this->instance_data->get_values();
        $this->app_login_second_logo = !empty($this->instance_data_values["app_login_second_logo"]) ? $this->instance_data_values["app_login_second_logo"] : "";
        $this->pageTitle = APPNAME;
        $this->load->database();
        $this->load->library(["is_auth"]);
        $this->load->helper(["url", "cookie"]);
        $cloud_config_params = $this->session->userdata("cloud_config_params");
        $this->app_trial_period = isset($cloud_config_params["trial_period"]) ? $cloud_config_params["trial_period"] : 10;
        $this->load->library(["licensor"]);
        $this->activeInstalledModules = isset($this->licensor) ? $this->licensor->get_installed_modules() : [];
        $this->load->model("instance_data");
        $this->instance_data_array = $this->instance_data->get_values();
        $this->cloud_installation_type = $this->instance_data_array["installationType"] == "on-cloud";
        if ($this->cloud_installation_type) {
            $this->instance_client_type = isset($this->instance_data_array["clientType"]) ? $this->instance_data_array["clientType"] : NULL;
            $this->instance_subscription = isset($this->instance_data_array["subscription"]) ? $this->instance_data_array["subscription"] : NULL;
        }
        $systemPreferences = $this->session->userdata("systemPreferences");
        $this->totalRemindersNotifications = $this->loadTotalRemindersNotifications();
        $remindersAccess = $this->is_auth->check_uri_permissions("/reminders/", "/reminders/reminders_list/", "core", true, true);
        $notificationAccess = $this->is_auth->check_uri_permissions("/notifications/", "/notifications/get_pending_list/", "core", true, true);
        $this->notificationRefreshInterval = $notificationAccess ? $this->getInstanceConfig("notification_refresh_interval") : "";
        $this->remindersRefreshInterval = $remindersAccess ? $this->getInstanceConfig("reminder_refresh_interval") : "";
        $this->allowFeatureCustomerPortal = isset($systemPreferences["AllowFeatureCustomerPortal"]) ? $systemPreferences["AllowFeatureCustomerPortal"] == "yes" : false;
        $this->allowFeatureAdvisor = isset($systemPreferences["AllowFeatureAdvisor"]) ? $systemPreferences["AllowFeatureAdvisor"] == "yes" : false;
        $this->currentLanguages = explode(",", $this->session->userdata("languages"));
        $this->session->set_userdata("is_cloud", $this->cloud_installation_type);
        $this->session->set_userdata("max_drop_down_length", $this->max_drop_down_length);
        $this->load->library("user_agent");
        $this->sqlsrv_2008 = $this->instance_data->validate_sqlsrv_version("2008");
        $this->user_guide = $this->session->userdata("user_guide");
        $this->workthrough = $this->session->userdata("workthrough");
        $this->first_sign_in = isset($this->instance_data_array["first_sign_in"]) ? $this->instance_data_array["first_sign_in"] : NULL;
        $this->core_controllers = ["cases"];
        $this->contract_controllers = ["contracts"];
        $license_data = $this->licensor->get_all_licenses();
        $this->license_package = $this->licensor->license_package;
        if ($this->license_package == "core_contract") {
            $this->license_availability = $this->licensor->check_license_date("core");
            $this->contract_license_availability = $this->licensor->check_license_date("contract");
            if ($this->session->userdata("AUTH_access_type") !== "both") {
                $this->license_package = $this->session->userdata("AUTH_access_type");
            }
        } else {
            if ($this->session->userdata("AUTH_access_type") && $this->session->userdata("AUTH_access_type") !== "both" && $this->session->userdata("AUTH_access_type") !== $this->license_package) {
                $this->license_validity = $this->licensor->check_license_validity($this->session->userdata("AUTH_access_type"));
            }
            if ($this->license_package == "core") {
                $this->license_availability = $this->licensor->check_license_date($this->license_package);
            } else {
                $this->license_availability = true;
                $this->contract_license_availability = $this->licensor->check_license_date($this->license_package);
            }
        }
        $this->selected_plan = $this->license_package == "contract" ? $license_data["contract"]["App4Legal"]["plan"] ?? false : $license_data["core"]["App4Legal"]["plan"] ?? false;
        $this->plan_excluded_features = $this->selected_plan ? $this->get_plan_excluded_features($this->selected_plan) : false;
        $this->plan_feature_warning_msgs = $this->selected_plan ? $this->get_plan_feature_warning_msgs() : false;
        $this->session->set_userdata("selected_plan", $this->selected_plan);
        $this->session->set_userdata("plan_excluded_features", $this->plan_excluded_features);
        $this->session->set_userdata("plan_feature_warning_msgs", $this->plan_feature_warning_msgs);
    }
    private function exit_if_upgrading()
    {
        if (CLOUD && file_exists(INSTANCE_PATH . "a4l.upgrading")) {
            header("HTTP/1.1 503 Service Unavailable.", true, 503);
            echo "<h2> Sorry, we are down for a scheduled maintenance right now, please check again in a couple of minutes, thank you. <h2>";
            exit(1);
        }
    }
    public function get_plan_excluded_features($plan)
    {
        $excluded_features = ["cloud-basic" => "In-line-Word-Editor,Document-Automation-&-Templates,Multi-entities-Accounting,Advanced-Permissions,In-Document-Search,Advanced-Workflows-&-Approvals,LDAP-User-Management-Integration,Azure-User-Management-Integration,Third-Party-Integration-(APIs),Service-Level-Agreement", "cloud-business" => "In-Document-Search,Advanced-Workflows-&-Approvals,LDAP-User-Management-Integration,Azure-User-Management-Integration,Third-Party-Integration-(APIs),Service-Level-Agreement", "cloud-enterprise" => "", "self-business" => "In-Document-Search,Advanced-Workflows-&-Approvals,LDAP-User-Management-Integration,Azure-User-Management-Integration,Third-Party-Integration-(APIs),Service-Level-Agreement", "self-enterprise" => ""];
        return $excluded_features[$plan] ?? false;
    }
    public function get_plan_feature_warning_msgs()
    {
        return ["In-line-Word-Editor" => $this->lang->line("plan_feature_warning_msg_business_enterprise"), "Document-Automation-&-Templates" => $this->lang->line("plan_feature_warning_msg_business_enterprise"), "Multi-entities-Accounting" => $this->lang->line("plan_feature_warning_msg_business_enterprise"), "Advanced-Permissions" => $this->lang->line("plan_feature_warning_msg_business_enterprise"), "In-Document-Search" => $this->lang->line("plan_feature_warning_enterprise"), "Advanced-Workflows-&-Approvals" => $this->lang->line("plan_feature_warning_enterprise"), "LDAP-User-Management-Integration" => $this->lang->line("plan_feature_warning_enterprise"), "Azure-User-Management-Integration" => $this->lang->line("plan_feature_warning_enterprise"), "Service-Level-Agreement" => $this->lang->line("plan_feature_warning_enterprise")];
    }
    public function getInstanceConfig($parameter)
    {
        $this->config->load("instance", true);
        $parameterValue = $this->config->item($parameter, "instance");
        if (in_array($parameter, ["notification_refresh_interval", "reminder_refresh_interval"])) {
            $minRefreshInterval = $this->config->item("min_refresh_interval");
            if ($parameterValue && (int) $parameterValue < (int) $minRefreshInterval) {
                $parameterValue = $minRefreshInterval;
            }
        }
        return $parameterValue ? $parameterValue : "";
    }
    public function LicenceAvailability()
    {
        if (!$this->input->is_ajax_request()) {
            show_404();
        }
        $module = $this->input->post("moduleName");
        if (isset($module) && strcmp($module, "")) {
            $this->license_availability = $this->licensor->check_license_date($module);
        }
        $response = [];
        $response["status"] = $this->license_availability === false ? "false" : "true";
        $response["data"] = $this->licensor->get_license_message(MODULE);
        $this->output->set_content_type("application/json");
        $this->output->set_output(json_encode($response));
    }
    public function set_flashmessage($ty = "information", $m, $d = "")
    {
        $m = rawurlencode($m);
        $this->session->set_userdata("pnotify_feedback", compact("ty", "m", "d"));
    }
    public function includes($src, $type, $remote = false, $extra = [])
    {
        $src .= empty($type) ? "" : "." . $type;
        $addFile = compact("src", "remote", "extra");
        $key = base64_encode($src);
        if (!array_key_exists($key, $this->{$type}["files"])) {
            $this->{$type}["files"][$key] = $addFile;
        }
    }
    public function includes_footer($src, $type, $remote = false, $extra = [])
    {
        $src .= empty($type) ? "" : "." . $type;
        $addFile = compact("src", "remote", "extra");
        $key = base64_encode($src);
        if (!array_key_exists($key, $this->{$type . "_footer"}["files"])) {
            $this->{$type . "_footer"}["files"][$key] = $addFile;
        }
    }
    protected function get_filter_operators($type)
    {
        $operators = [];
        switch ($type) {
            case "list":
                $operators = ["eq" => $this->lang->line("equal"), "neq" => $this->lang->line("neq")];
                break;
            case "groupList":
                $operators = ["in" => $this->lang->line("equal"), "notin" => $this->lang->line("neq")];
                break;
            case "number":
                $operators = ["eq" => $this->lang->line("equal"), "neq" => $this->lang->line("neq"), "lt" => $this->lang->line("lt"), "lte" => $this->lang->line("lte"), "gt" => $this->lang->line("gt"), "gte" => $this->lang->line("gte"), "contains" => $this->lang->line("contains"), "startswith" => $this->lang->line("starts_with"), "endswith" => $this->lang->line("ends_with")];
                break;
            case "text":
                $operators = ["eq" => $this->lang->line("equal"), "neq" => $this->lang->line("neq"), "contains" => $this->lang->line("contains"), "startswith" => $this->lang->line("starts_with"), "endswith" => $this->lang->line("ends_with")];
                break;
            case "text_empty":
                $operators = ["eq" => $this->lang->line("equal"), "neq" => $this->lang->line("neq"), "contains" => $this->lang->line("contains"), "startswith" => $this->lang->line("starts_with"), "endswith" => $this->lang->line("ends_with"), "empty" => $this->lang->line("empty"), "not_empty" => $this->lang->line("not_empty")];
                break;
            case "date":
                $operators = ["cast_eq" => $this->lang->line("equal"), "cast_neq" => $this->lang->line("neq"), "cast_lt" => $this->lang->line("lt"), "cast_lte" => $this->lang->line("lte"), "cast_gt" => $this->lang->line("gt"), "cast_gte" => $this->lang->line("gte"), "cast_between" => $this->lang->line("between"), "yd" => $this->lang->line("yesterday"), "tday" => $this->lang->line("today"), "tomorrow" => $this->lang->line("tomorrow"), "lw" => $this->lang->line("last_week"), "tw" => $this->lang->line("this_week"), "nw" => $this->lang->line("next_week"), "lm" => $this->lang->line("last_month"), "tm" => $this->lang->line("this_month"), "nm" => $this->lang->line("next_month"), "tq" => $this->lang->line("this_quarter"), "lq" => $this->lang->line("last_quarter"), "ty" => $this->lang->line("this_year"), "ly" => $this->lang->line("last_year")];
                break;
            case "lookUp":
                $operators = ["lookUp" => $this->lang->line("look_up")];
                break;
            case "bigText":
                $operators = ["contains" => $this->lang->line("contains"), "startswith" => $this->lang->line("starts_with"), "endswith" => $this->lang->line("ends_with")];
                break;
            case "operatorsTime":
                $operators = ["eq" => $this->lang->line("equal"), "neq" => $this->lang->line("neq"), "lt" => $this->lang->line("lt"), "lte" => $this->lang->line("lte"), "gt" => $this->lang->line("gt"), "gte" => $this->lang->line("gte")];
                break;
            case "number_only":
                $operators = ["eq" => $this->lang->line("equal"), "neq" => $this->lang->line("neq"), "lt" => $this->lang->line("lt"), "lte" => $this->lang->line("lte"), "gt" => $this->lang->line("gt"), "gte" => $this->lang->line("gte")];
                break;
            default:
        }
                return $operators;

    }
    public function _push_file($path, $download_name)
    {
        if (is_file($path)) {
            if (ini_get("zlib.output_compression")) {
                ini_set("zlib.output_compression", "Off");
            }
            $this->load->helper("file");
            $mime = get_mime_by_extension($path);
            header("Pragma: public");
            header("Expires: 0");
            header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
            header("Last-Modified: " . gmdate("D, d M Y H:i:s", filemtime($path)) . " GMT");
            header("Cache-Control: private", false);
            header("Content-Type: " . $mime);
            if (isset($_SERVER["HTTP_USER_AGENT"])) {
                $user_agent = $_SERVER["HTTP_USER_AGENT"];
                if (0 < strlen(strstr($user_agent, "Firefox")) || 0 < strlen(strstr($user_agent, "Safari"))) {
                    header("Content-Disposition: attachment; filename=\"" . basename($download_name) . "\"");
                } else {
                    header("Content-Disposition: attachment; filename*=UTF-8''" . basename($download_name));
                }
                if (0 < strlen(strstr($user_agent, "Chrome"))) {
                    header("Content-Disposition: attachment; filename*=UTF-8''" . basename($download_name));
                }
            } else {
                header("Content-Disposition: attachment; filename=\"" . basename($download_name) . "\"");
            }
            header("Content-Transfer-Encoding: binary");
            header("Content-Length: " . filesize($path));
            header("Connection: close");
            readfile($path);
        }
    }
    public function downloaded_file_name_by_browser($tempFilename)
    {
        $filename = $tempFilename;
        if (isset($_SERVER["HTTP_USER_AGENT"])) {
            $user_agent = $_SERVER["HTTP_USER_AGENT"];
            if (0 < strlen(strstr($user_agent, "Firefox")) || 0 < strlen(strstr($user_agent, "Safari"))) {
                $filename = $tempFilename;
            } else {
                $filename = str_replace("+", " ", urlencode($tempFilename));
            }
            if (0 < strlen(strstr($user_agent, "Chrome"))) {
                $filename = str_replace("+", " ", urlencode($tempFilename));
            }
        }
        return $filename;
    }
    public function loadTotalRemindersNotifications()
    {
        $this->load->model("notification", "notificationfactory");
        $this->notification = $this->notificationfactory->get_instance();
        $data["pendingNotifications"] = $this->notification->get_pending_notifications();
        $this->load->model("reminder", "reminderfactory");
        $this->reminder = $this->reminderfactory->get_instance();
        $data["pendingReminders"] = $this->reminder->load_reminders("counter");
        return $data;
    }
    public function authenticate_exempted_actions()
    {
        if (!$this->is_auth->is_logged_in()) {
            if ($this->input->is_ajax_request()) {
                exit("login_needed");
            }
            redirect("users/login");
        }
    }
    public function sanitize_post($item, $key)
    {
        if ($item != "" && is_string($item)) {
            $_POST[$key] = trim($item);
        }
    }
    public function force_cloud_login_screen()
    {
        return NULL;
    }
    public function write_log($file_path, $message, $type = "info")
    {
        $log_path = $this->config->item("files_path") . DIRECTORY_SEPARATOR . "logs" . DIRECTORY_SEPARATOR . $file_path . ".log";
        $pr = fopen($log_path, "a");
        fwrite($pr, date("Y-m-d H:i:s") . " [" . $type . "] - " . $message . " \n");
        fclose($pr);
        if ($type == "error") {
            echo $type . ": " . $message . ". Please check the log file '" . $log_path . "' for more details and to fix the error";
            exit;
        }
    }
    public function validate_id($id)
    {
        return 0 < $id && is_numeric($id);
    }
    public function regenerate_note($note)
    {
        $note = mb_convert_encoding($note, "UTF-8", "UTF-8");
        $note = str_replace("&lt !--[if !supportLists]--&gt;", "", $note);
        $note = str_replace("&lt;!--[endif]--&gt;", "", $note);
        return $note;
    }
    protected function push_to_array_if_not_exists($new_item, &$items, $unique_key, $value)
    {
        $total_items = count($items);
        $counter = 1;
        foreach ($items as $item) {
            if (isset($item[$unique_key])) {
                if ($item[$unique_key] == $value) {
                    return false;
                }
                if ($total_items == $counter) {
                    $items[] = $new_item;
                    return true;
                }
            }
            $counter++;
        }
        $items[] = $new_item;
        return true;
    }
    public function call_materialized_view_triggers($trigger, $record_id, $data = [])
    {
        if ($this->db->dbdriver != "mysqli") {
            return false;
        }
        $call_trigger = !empty($data) ? false : true;
        foreach ($data as $field => $value) {
            if ($value["old"] != $value["new"]) {
                $call_trigger = true;
            }
        }
        if ($call_trigger) {
            $cmd = $this->getInstanceConfig("PHP_executable_path") . " index.php Materialized_view_triggers " . $trigger . " " . $record_id;
            if (substr(php_uname(), 0, 7) == "Windows") {
                pclose(popen("start /B " . $cmd, "r"));
            } else {
                exec($cmd . " > /dev/null &");
            }
        }
    }
    public function get_plans()
    {
        return ["cloud-basic" => "Cloud / Basic", "cloud-business" => "Cloud / Business", "cloud-enterprise" => "Cloud / Enterprise", "self-business" => "Self-Hosted / Business", "self-enterprise" => "Self-Hosted / Enterprise"];
    }
    public function authenticate_actions_per_license($access = "core", $base_url = "")
    {
        if (!$this->session->userdata("AUTH_user_id")) {
            return false;
        }
        if ($this->license_package == "core_contract") {
            return true;
        }
        if ($this->license_package !== $access) {
            $this->set_flashmessage("warning", sprintf($this->lang->line("license_permission_denied"), $this->lang->line($access)));
            if ($this->session->userdata("AUTH_access_type") !== $access) {
                $this->set_flashmessage("warning", sprintf($this->lang->line("permission_not_allowed_for"), $this->lang->line($access)));
            }
            redirect($base_url ? $base_url . "dashboard" : "dashboard");
        }
    }
    public function _download_docs_zip_file($module = "doc", $module_controller = "docs", $selected_items = "")
    {
        $zip_docs = explode(",", $selected_items);
        $zip_file_name = "Documents";
        if (count($zip_docs) == 1) {
            $zip_file_name = $this->dms->get_document_details(["module" => $module, "id" => $zip_docs[0]])["full_name"];
        }
        $response = $this->dms->download_files_and_folders_as_zip($module, $module_controller, $zip_docs, $zip_file_name);
        if (!isset($response["status"])) {
            $this->set_flashmessage("warning", $response["message"]);
            redirect("dashboard");
        }
    }
    public function request_can_cause_insufficient_anti_automation($module)
    {
        $feature_enabled = $this->config->item("anti_automation_protection") ?? false;
        if ($feature_enabled && 0 < $this->config->item("anti_automation_max_requests_per_session") && 0 < $this->config->item("anti_automation_max_time_per_session") && ($count_records_per_session = $this->session->userdata("count_records_per_session"))) {
            $module_count = $count_records_per_session[$module]["count"] ?? 0;
            if (0 < $module_count) {
                $datetime1 = new DateTime(date("Y-m-d H:i:s", time()));
                $datetime2 = new DateTime(date("Y-m-d H:i:s", $count_records_per_session[$module]["initial_request"]));
                $interval = $datetime1->diff($datetime2);
                if ($this->config->item("anti_automation_max_time_per_session") <= $interval->format("%i")) {
                    $count_records_per_session[$module] = [];
                    $this->session->set_userdata("count_records_per_session", $count_records_per_session);
                } else {
                    if ($interval->format("%i") == 0 || $interval->format("%i") < $this->config->item("anti_automation_max_time_per_session")) {
                        $updated_count = $module_count + 1;
                        if ($this->config->item("anti_automation_max_requests_per_session") < $updated_count) {
                            return true;
                        }
                    }
                }
            }
        }
        return false;
    }
    public function increase_count_for_anti_automation_prevention($module)
    {
        $feature_enabled = $this->config->item("anti_automation_protection") ?? false;
        if ($feature_enabled && 0 < $this->config->item("anti_automation_max_requests_per_session") && 0 < $this->config->item("anti_automation_max_time_per_session")) {
            if (!$this->session->userdata("count_records_per_session")) {
                $this->session->set_userdata("count_records_per_session", [$module => ["count" => 1, "initial_request" => strtotime(date("Y-m-d H:i:s"), time())]]);
            } else {
                $current_count = $this->session->userdata("count_records_per_session");
                $module_current_count = $current_count[$module]["count"] ?? 0;
                if ($module_current_count == 0) {
                    $current_count[$module]["count"] = 1;
                    $current_count[$module]["initial_request"] = strtotime(date("Y-m-d H:i:s"), time());
                } else {
                    $current_count[$module]["count"]++;
                }
                $this->session->set_userdata("count_records_per_session", $current_count);
            }
        }
        return true;
    }
}
?>