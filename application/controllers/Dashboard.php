<?php


if (!defined("BASEPATH")) {
    exit("No direct script access allowed");
}
require "Top_controller.php";
class Dashboard extends Top_controller
{
    public function __construct()
    {
        parent::__construct();
        $this->currentTopNavItem = "dashboard";
        $this->load->model("user", "userfactory");
        $this->load->model("user_preference");
        $this->user = $this->userfactory->get_instance();
        $this->load->model("contact", "contactfactory");
        $this->contact = $this->contactfactory->get_instance();
        $this->load->model("company", "companyfactory");
        $this->company = $this->companyfactory->get_instance();
        $this->load->model("legal_case", "legal_casefactory");
        $this->legal_case = $this->legal_casefactory->get_instance();
        $this->load->model("workflow_status", "workflow_statusfactory");
        $this->workflow_status = $this->workflow_statusfactory->get_instance();
        $this->load->model("legal_case_container", "legal_case_containerfactory");
        $this->legal_case_container = $this->legal_case_containerfactory->get_instance();
        $this->load->model("task", "taskfactory");
        $this->task = $this->taskfactory->get_instance();
    }
    public function index()
    {
        if (!$this->is_auth->is_logged_in()) {
            redirect("users/login");
        }
        $this->pageTitle = sprintf($this->lang->line("sheria360_browser_title"), $this->lang->line("my_dashboard"));
        if ($this->input->is_ajax_request()) {
            $response = [];
            $widgets = $this->input->get("widgets");
            if ($widgets == "all" || $widgets == "tasks") {
                $response["todays_tasks"] = $this->task->user_todays_tasks();
                $response["tasks_reported_by_me"] = $this->task->tasks_per_reporter();
                $response["tasks_assigned_to_me"] = $this->task->dashboard_tasks_assigned_to_auth_user();
            }
            if ($widgets == "all" || $widgets == "meetings") {
                $this->load->model("event", "eventfactory");
                $this->event = $this->eventfactory->get_instance();
                $response["todays_meetings"] = $this->event->load_todays_meetings();
            }
            if ($widgets == "all" || $widgets == "reminders") {
                $this->load->model("reminder", "reminderfactory");
                $this->reminder = $this->reminderfactory->get_instance();
                $response["todays_reminders"] = $this->reminder->load_todays_reminders();
            }
            if ($this->license_package == "core" || $this->license_package == "core_contract") {
                if ($widgets == "all" || $widgets == "hearings") {
                    $this->load->model("legal_case_hearing", "legal_case_hearingfactory");
                    $this->legal_case_hearing = $this->legal_case_hearingfactory->get_instance();
                    if ($this->input->get("hearings_date") == "pa") {
                        $response["upcoming_hearings"] = $this->legal_case_hearing->dashboard_pending_updates();
                    } else {
                        $response["upcoming_hearings"] = $this->legal_case_hearing->dashboard_my_hearings(false, $this->input->get("hearings_date"));
                    }
                    foreach ($response["upcoming_hearings"] as $key => $hearing) {
                        if (isset($this->session->userdata("systemPreferences")["hijriCalendarFeature"]) && $this->session->userdata("systemPreferences")["hijriCalendarFeature"]) {
                            $response["upcoming_hearings"][$key]["startDate"] = mb_substr(gregorianToHijri($hearing["startDate"]), 0, 10);
                        }
                        $response["upcoming_hearings"][$key]["hearing_day"] = $this->lang->line(date("l", strtotime($hearing["startDate"])));
                    }
                }
                if ($widgets == "all" || $widgets == "matters") {
                    $response["recent_corporate_matters"] = $this->legal_case->dashboard_recent_cases("corporate_matters");
                    $response["recent_litigation_cases"] = $this->legal_case->dashboard_recent_cases("litigation_cases");
                }
                if ($widgets == "all" || $widgets == "containers") {
                    $response["recent_matter_containers"] = $this->legal_case_container->dashboard_recent_matter_containers();
                }
                if ($widgets == "all" || $widgets == "IP") {
                    $this->load->model("ip_detail", "ip_detailfactory");
                    $this->ip_detail = $this->ip_detailfactory->get_instance();
                    $response["recent_intellectual_properties"] = $this->ip_detail->dashboard_recent_intellectual_properties();
                }
            }
            if ($widgets == "all" || $widgets == "time_logs") {
                $this->load->model("user_activity_log", "user_activity_logfactory");
                $this->user_activity_log = $this->user_activity_logfactory->get_instance();
                $response["time_logs"] = $this->user_activity_log->get_time_logs_by_time($this->session->userdata("AUTH_user_id"));
            }
            $this->output->set_content_type("application/json")->set_output(json_encode($response));
        } else {
            $this->load->model("case_type");
            $this->load->model("task_type", "task_typefactory");
            $this->task_type = $this->task_typefactory->get_instance();
            $this->load->model("voucher_header", "voucher_headerfactory");
            $this->voucher_header = $this->voucher_headerfactory->get_instance();
            $this->load->model("account", "accountfactory");
            $this->account = $this->accountfactory->get_instance();
            $getting_started = unserialize($this->user_preference->get_value("getting_started"));
            $data["selectedLanguage"] = $this->session->userdata("AUTH_language");
            if (isset($getting_started["show"]) && $getting_started["show"] && $this->cloud_installation_type) {
                $this->session->set_userdata("show_getting_started", true);
                if (!isset($getting_started["auto_open_avatar_form"])) {
                    $getting_started["auto_open_avatar_form"] = true;
                    $this->user_preference->set_value("getting_started", serialize($getting_started), true);
                }
                $this->getting_started();
            } else {
                if (isset($getting_started["show"]) && $this->cloud_installation_type) {
                    $this->session->set_userdata("show_getting_started", true);
                }
                $data["user_dashboard_order"] = unserialize($this->user_preference->get_value("user_dashboard_order"));
                $case_types = $this->case_type->load_all(["where" => ["isDeleted", 0], "order_by" => ["name", "asc"]]);
                $data["corporate_matter_types"][0] = $this->lang->line("all");
                $data["litigation_case_types"][0] = $this->lang->line("all");
                $data["matter_container_types"][0] = $this->lang->line("all");
                foreach ($case_types as $type) {
                    if ($type["litigation"] == "yes") {
                        $data["litigation_case_types"][$type["id"]] = $type["name"];
                    }
                    if ($type["corporate"] == "yes") {
                        $data["corporate_matter_types"][$type["id"]] = $type["name"];
                    }
                    $data["matter_container_types"][$type["id"]] = $type["name"];
                }
                $task_types = $this->task_type->load_all_per_language();
                $data["task_types"][0] = $this->lang->line("all");
                foreach ($task_types as $type) {
                    $data["task_types"][$type["id"]] = $type["name"];
                }
                $data["expenses"] = $this->voucher_header->load_dashboard_expenses();
                $data["accounts"] = $this->account->load_user_cash_accounts();
                $this->includes("scripts/my_dashboard", "js");
                $this->includes("customerPortal/clientPortal/css/datatables.min", "css");
                $this->includes("customerPortal/clientPortal/js/datatables.min", "js");
                $this->includes("customerPortal/clientPortal/js/jquery.dataTables.min", "js");
                $this->includes("customerPortal/clientPortal/js/dataTables.bootstrap.min", "js");
                $this->includes("jquery/apexcharts/apexcharts.min", "js");
                $this->includes("jquery/apexcharts/polyfill.min", "js");
                $this->includes("jquery/apexcharts/classlist", "js");
                $this->load->view("partial/header");
                $this->load->view("dashboards/my_dashboard", $data);
                $this->load->view("partial/footer");
            }
        }
    }
    public function pie_charts_widgets()
    {
        if (!$this->input->is_ajax_request()) {
            show_404();
        }
        $filters = $this->input->get("filters");
        if (isset($filters["litigation_cases"])) {
            $litigation_cases_per_status = $this->legal_case->dashboard_cases_per_status($filters["litigation_cases"], ["litigation"]);
            $response["pie_charts"]["litigation"] = ["statuses" => !empty($litigation_cases_per_status["statuses"]) ? $litigation_cases_per_status["statuses"] : [$this->lang->line("status")], "values" => !empty($litigation_cases_per_status["values"]) ? $litigation_cases_per_status["values"] : [0], "names" => !empty($litigation_cases_per_status["names"]) ? $litigation_cases_per_status["names"] : [0]];
        }
        if (isset($filters["corporate_matters"])) {
            $corporate_matters_per_status = $this->legal_case->dashboard_cases_per_status($filters["corporate_matters"], ["matter"]);
            $response["pie_charts"]["matter"] = ["statuses" => !empty($corporate_matters_per_status["statuses"]) ? $corporate_matters_per_status["statuses"] : [$this->lang->line("status")], "values" => !empty($corporate_matters_per_status["values"]) ? $corporate_matters_per_status["values"] : [0], "names" => !empty($corporate_matters_per_status["names"]) ? $corporate_matters_per_status["names"] : [0]];
        }
        if (isset($filters["matter_containers"])) {
            $containers_per_status = $this->legal_case_container->dashboard_containers_per_status($filters["matter_containers"]);
            $response["pie_charts"]["matter_container"] = ["statuses" => !empty($containers_per_status["statuses"]) ? $containers_per_status["statuses"] : [$this->lang->line("status")], "values" => !empty($containers_per_status["values"]) ? $containers_per_status["values"] : [0], "names" => !empty($containers_per_status["names"]) ? $containers_per_status["names"] : [0]];
        }
        if (isset($filters["tasks_assigned_to_me"])) {
            $tasks_assigned_to_me_per_status = $this->task->dashboard_tasks_per_status($filters["tasks_assigned_to_me"], "tasks_assigned_to_me");
            $response["pie_charts"]["tasks_assigned_to_me"] = ["statuses" => !empty($tasks_assigned_to_me_per_status["statuses"]) ? $tasks_assigned_to_me_per_status["statuses"] : [$this->lang->line("status")], "values" => !empty($tasks_assigned_to_me_per_status["values"]) ? $tasks_assigned_to_me_per_status["values"] : [0], "names" => !empty($tasks_assigned_to_me_per_status["names"]) ? $tasks_assigned_to_me_per_status["names"] : [0]];
        }
        if (isset($filters["tasks_reported_by_me"])) {
            $tasks_reported_by_me_per_status = $this->task->dashboard_tasks_per_status($filters["tasks_reported_by_me"], "tasks_reported_by_me");
            $response["pie_charts"]["tasks_reported_by_me"] = ["statuses" => !empty($tasks_reported_by_me_per_status["statuses"]) ? $tasks_reported_by_me_per_status["statuses"] : [$this->lang->line("status")], "values" => !empty($tasks_reported_by_me_per_status["values"]) ? $tasks_reported_by_me_per_status["values"] : [0], "names" => !empty($tasks_reported_by_me_per_status["names"]) ? $tasks_reported_by_me_per_status["names"] : [0]];
        }
        $this->output->set_content_type("application/json")->set_output(json_encode($response));
    }
    public function set_widgets_order()
    {
        if ($this->input->is_ajax_request()) {
            $response = [];
            $post_data = $this->input->post("widgets_order_data");
            foreach ($post_data as $data) {
                $order[] = $data["id"];
            }
            $response["result"] = $this->user_preference->set_value("user_dashboard_order", serialize($order), true);
            $this->output->set_content_type("application/json");
            $this->output->set_output(json_encode($response));
        } else {
            show_404();
        }
    }
    public function management()
    {
        if (!$this->is_auth->is_logged_in()) {
            redirect("users/login");
        }
        $this->pageTitle = sprintf($this->lang->line("sheria360_browser_title"), $this->lang->line("dashboard"));
        $year = $this->input->get("year") ? $this->input->get("year") : DATE("Y");
        if ($this->input->is_ajax_request()) {
            $response = [];
            $cases_by_filing = $this->legal_case->count_arrival_cases_per_month($year);
            for ($i = 1; $i <= 12; $i++) {
                $response["cases_by_filing"][] = isset($cases_by_filing[$i]) ? $cases_by_filing[$i] : 0;
            }
            $cases_by_due_date = $this->legal_case->count_dueDate_cases_per_month($year);
            for ($i = 1; $i <= 12; $i++) {
                $response["cases_by_due_date"][] = isset($cases_by_due_date[$i]) ? $cases_by_due_date[$i] : 0;
            }
            $cases_per_assignee = $this->legal_case->count_cases_per_assignee($year);
            if (!empty($cases_per_assignee)) {
                foreach ($cases_per_assignee as $assignee) {
                    $response["cases_per_assignee"]["names"][] = $assignee["status"] == "Inactive" && $assignee["userName"] != "Unassigned" ? $assignee["userName"] . "(" . $this->lang->line("Inactive") . ")" : $assignee["userName"];
                    $response["cases_per_assignee"]["case_count"][] = (int) $assignee["count"];
                }
            } else {
                $response["cases_per_assignee"]["names"] = ["Unassigned"];
                $response["cases_per_assignee"]["case_count"] = [0];
            }
            $status_filter = ["from" => $year . "-01-01", "to" => $year . "-12-31", "type" => "all"];
            $response["cases_per_status"] = $this->legal_case->get_cases_per_status($status_filter);
            if (empty($response["cases_per_status"]["values"])) {
                $response["cases_per_status"]["names"] = [""];
                $response["cases_per_status"]["values"] = [0];
            }
            foreach ($response["cases_per_status"]["names"] as &$case_status_name) {
                $case_status_name = 50 < mb_strlen($case_status_name) ? mb_substr($case_status_name, 0, 47) . "..." : $case_status_name;
            }
            $this->output->set_content_type("application/json")->set_output(json_encode($response));
        } else {
            $data["top_cases_by_dueDate"] = $this->legal_case->top_cases_by_dueDate();
            $data["companies_per_month"] = $this->company->companies_per_month();
            $data["assigned_to_me"] = $this->task->tasks_per_assignee();
            $data["selected_year"] = $year;
            $this->includes("scripts/management_dashboard", "js");
            $this->includes("customerPortal/clientPortal/css/datatables.min", "css");
            $this->includes("customerPortal/clientPortal/js/datatables.min", "js");
            $this->includes("customerPortal/clientPortal/js/jquery.dataTables.min", "js");
            $this->includes("customerPortal/clientPortal/js/dataTables.bootstrap.min", "js");
            $this->includes("jquery/apexcharts/apexcharts.min", "js");
            $this->includes("jquery/apexcharts/polyfill.min", "js");
            $this->includes("jquery/apexcharts/classlist", "js");
            $this->load->view("partial/header");
            $this->load->view("dashboards/management_dashboard", $data);
            $this->load->view("partial/footer");
        }
    }
    public function litigation_dashboard($dashboard_number = 1)
    {
        $this->authenticate_actions_per_license();
        if (!$this->is_auth->is_logged_in()) {
            redirect("users/login");
        }
        $this->pageTitle = sprintf($this->lang->line("sheria360_browser_title"), $this->lang->line("litigation_dashboard"));
        $this->load->model("system_configuration");
        $litigation_dashboard_order = $this->system_configuration->get_value_by_key("litigation_dashboard_order_" . $dashboard_number);
        $order = $litigation_dashboard_order ? unserialize($litigation_dashboard_order) : ($dashboard_number == 1 ? ["hidden_widgets" => [], "left_widgets" => [1, 6, 3, 8], "right_widgets" => [5, 7, 2]] : ["hidden_widgets" => [], "left_widgets" => [4, 11, 9], "right_widgets" => [10, 12, 13]]);
        if ($this->input->is_ajax_request()) {
            $this->load->model("legal_case_hearing", "legal_case_hearingfactory");
            $this->legal_case_hearing = $this->legal_case_hearingfactory->get_instance();
            $date_filter = ["from" => $this->input->get("from_date"), "to" => $this->input->get("to_date")];
            $response = [];
            $widgets = $this->input->get("widgets");
            if ($dashboard_number == 1) {
                if (!in_array(1, $order["hidden_widgets"]) && ($widgets == "all" || $widgets == "cases")) {
                    $response["cases_per_assignee"] = array_merge($this->legal_case->get_cases_per_assignee($date_filter), ["settings" => ["id" => "cases-per-assignee", "orientation" => "horizontal", "type" => "barChart", "chart_number" => "1"]]);
                }
                if (!in_array(2, $order["hidden_widgets"]) && ($widgets == "all" || $widgets == "cases")) {
                    $response["cases_per_status"] = array_merge($this->legal_case->get_cases_per_status($date_filter), ["settings" => ["id" => "cases-per-status", "orientation" => "horizontal", "type" => "pieChart", "chart_number" => "2"]]);
                }
                if (!in_array(3, $order["hidden_widgets"]) && ($widgets == "all" || $widgets == "cases")) {
                    $response["cases_per_practice_area"] = array_merge($this->legal_case->get_cases_per_practice_area($date_filter), ["settings" => ["id" => "cases-per-practice-area", "type" => "barChart", "chart_number" => "3"]]);
                }
                if (!in_array(5, $order["hidden_widgets"]) && ($widgets == "all" || $widgets == "cases")) {
                    $response["cases_per_arrival_date"] = array_merge($this->legal_case->count_litigation_cases_per_arrival_date($date_filter), ["settings" => ["id" => "cases-per-arrival-date", "type" => "lineChart", "chart_number" => "5"]]);
                }
                if (!in_array(6, $order["hidden_widgets"]) && ($widgets == "all" || $widgets == "cases")) {
                    $response["cases_per_client"] = array_merge($this->legal_case->get_cases_per_client($date_filter), ["settings" => ["id" => "cases-per-client", "type" => "barChart", "chart_number" => "6"]]);
                }
                if (!in_array(7, $order["hidden_widgets"]) && ($widgets == "all" || $widgets == "cases")) {
                    $response["cases_per_client_position"] = array_merge($this->legal_case->get_cases_per_client_position($date_filter), ["settings" => ["id" => "cases-per-client-position", "type" => "pieChart", "chart_number" => "7"]]);
                }
                if (!in_array(8, $order["hidden_widgets"]) && ($widgets == "all" || $widgets == "cases")) {
                    $response["cases_per_sucess_probability"] = array_merge($this->legal_case->get_cases_per_success_probability($date_filter), ["settings" => ["id" => "cases-per-success-probability", "type" => "pieChart", "chart_number" => "8"]]);
                }
            } else {
                if (!in_array(4, $order["hidden_widgets"]) && ($widgets == "all" || $widgets == "cases")) {
                    $response["cases_per_stage"] = array_merge($this->legal_case->get_cases_per_stage($date_filter), ["settings" => ["id" => "cases-per-stage", "type" => "barChart", "chart_number" => "4"]]);
                }
                if (!in_array(9, $order["hidden_widgets"]) && ($widgets == "all" || $widgets == "hearings")) {
                    $response["hearings_per_month"] = array_merge($this->legal_case_hearing->get_hearings_per_month($date_filter), ["settings" => ["id" => "hearings-per-month", "type" => "lineChart", "chart_number" => "9"]]);
                }
                if (!in_array(10, $order["hidden_widgets"]) && $widgets == "all") {
                    $response["case_stages_per_court"] = array_merge($this->legal_case->get_case_stages_per_court($date_filter), ["settings" => ["id" => "case-stages-per-court", "type" => "barChart", "chart_number" => "10"]]);
                }
                if (!in_array(11, $order["hidden_widgets"]) && $widgets == "all") {
                    $response["case_stages_per_court_type"] = array_merge($this->legal_case->get_case_stages_per_court_type($date_filter), ["settings" => ["id" => "case-stages-per-court-type", "type" => "barChart", "chart_number" => "11"]]);
                }
                if (!in_array(12, $order["hidden_widgets"]) && $widgets == "all") {
                    $response["case_stages_per_court_region"] = array_merge($this->legal_case->get_case_stages_per_court_region($date_filter), ["settings" => ["id" => "case-stages-per-court-region", "type" => "barChart", "chart_number" => "12"]]);
                }
                if (!in_array(13, $order["hidden_widgets"]) && ($widgets == "all" || $widgets == "hearings")) {
                    $response["hearings_per_assignee"] = array_merge($this->legal_case_hearing->get_hearings_per_assignee($date_filter), ["settings" => ["id" => "hearings-per-assignee", "type" => "barChart", "chart_number" => "13"]]);
                }
            }
            $this->output->set_content_type("application/json")->set_output(json_encode($response));
        } else {
            $data["order"] = $order;
            $data["rows"] = max(count($data["order"]["left_widgets"]), count($data["order"]["right_widgets"]));
            $data["from_date"] = date("Y-01-01");
            $data["to_date"] = date("Y-12-31");
            $data["dashboard_number"] = $dashboard_number;
            $this->includes("scripts/litigation_dashboard", "js");
            $this->includes("customerPortal/clientPortal/css/datatables.min", "css");
            $this->includes("customerPortal/clientPortal/js/datatables.min", "js");
            $this->includes("customerPortal/clientPortal/js/jquery.dataTables.min", "js");
            $this->includes("customerPortal/clientPortal/js/dataTables.bootstrap.min", "js");
            $this->includes("jquery/apexcharts/apexcharts.min", "js");
            $this->includes("jquery/apexcharts/polyfill.min", "js");
            $this->includes("jquery/apexcharts/classlist", "js");
            $this->load->view("partial/header");
            $this->load->view("dashboards/litigation_dashboard", $data);
            $this->load->view("partial/footer");
        }
    }
    public function litigation_dashboard_config($dashboard_number = 1)
    {
        $this->authenticate_actions_per_license();
        $this->pageTitle = sprintf($this->lang->line("sheria360_browser_title"), $this->lang->line("litigation_dashboard"));
        $this->load->model("system_configuration");
        if ($this->input->is_ajax_request()) {
            $response = [];
            $post_data = $this->input->post();
            $order = ["hidden_widgets" => isset($post_data["hidden_widgets"]) ? $post_data["hidden_widgets"] : [], "left_widgets" => isset($post_data["left_widgets"]) ? $post_data["left_widgets"] : [], "right_widgets" => isset($post_data["right_widgets"]) ? $post_data["right_widgets"] : []];
            $response["result"] = $this->system_configuration->set_value_by_key("litigation_dashboard_order_" . $dashboard_number, serialize($order), true);
            $this->output->set_content_type("application/json");
            $this->output->set_output(json_encode($response));
        } else {
            $litigation_dashboard_order = $this->system_configuration->get_value_by_key("litigation_dashboard_order_" . $dashboard_number);
            $data["order"] = $litigation_dashboard_order ? unserialize($litigation_dashboard_order) : ($dashboard_number == 1 ? ["hidden_widgets" => [], "left_widgets" => [1, 6, 3, 8], "right_widgets" => [5, 7, 2]] : ["hidden_widgets" => [], "left_widgets" => [4, 11, 9], "right_widgets" => [10, 12, 13]]);
            $data["lists"] = array_keys($data["order"]);
            $data["dashboard_number"] = $dashboard_number;
            $this->includes("scripts/litigation_dashboard_config", "js");
            $this->load->view("partial/header");
            $this->load->view("dashboards/litigation_dashboard_config", $data);
            $this->load->view("partial/footer");
        }
    }
    public function export_litigation_dashboard_pdf()
    {
        if ($this->input->is_ajax_request()) {
            $response = [];
            $data["data"] = $this->input->post(NULL, false);
            $html = $this->load->view("dashboards/litigation_dashboard_pdf", $data, true);
            $file_name = "litigation_dashboard_" . date("YmdHi");
            require_once substr(COREPATH, 0, -12) . "/application/libraries/mpdf/vendor/autoload.php";
            $mpdf = new Mpdf\Mpdf(["mode" => "utf-8", "format" => "Legal", "default_font_size" => 10, "default_font" => "dejavusans", "pagenumPrefix" => $this->lang->line("page") . " ", "nbpgPrefix" => " " . $this->lang->line("of") . " "]);
            if ($this->is_auth->is_layout_rtl()) {
                $mpdf->SetDirectionality("rtl");
                $mpdf->autoScriptToLang = true;
                $mpdf->autoLangToFont = true;
            }
            $mpdf->shrink_tables_to_fit = 0;
            ini_set("pcre.backtrack_limit", "150000000");
            $mpdf->WriteHTML($html);
            $mpdf->Output($this->config->item("files_path") . DIRECTORY_SEPARATOR . "tmp" . DIRECTORY_SEPARATOR . $file_name . ".pdf", "F");
            $response["file_name"] = $file_name;
            $this->output->set_content_type("application/json");
            $this->output->set_output(json_encode($response));
        }
    }

    public  function criminal_litigation_dashboard()
    {
        $data=[];
        $this->includes("jquery/apexcharts/polyfill.min", "js");
        $this->includes("jquery/apexcharts/classlist", "js");
        $this->includes("jquery/apexcharts/canvg", "js");
        $this->includes("jquery/apexcharts/apexcharts.min", "js");
        $this->load->view("partial/header");
        $this->load->view("prosecution/dashboard", $data);
        $this->load->view("partial/footer");

    }
    public function time_tracking_dashboard()
    {
        if (!$this->is_auth->is_logged_in()) {
            redirect("users/login");
        }
        $this->pageTitle = sprintf($this->lang->line("sheria360_browser_title"), $this->lang->line("time_tracking_dashboard"));
        $year = $this->input->get("year_filter") ? $this->input->get("year_filter") : DATE("Y");
        if ($this->input->is_ajax_request()) {
            $this->load->model("user_rate_per_hour", "user_rate_per_hourfactory");
            $this->user_rate_per_hour = $this->user_rate_per_hourfactory->get_instance();
            $filters = $this->input->get();
            $response = $this->user_rate_per_hour->time_tracking_dashboard_data($filters);
            $response["time_logs_per_month"] = $this->user_activity_log->time_logs_per_month($filters, $response["charts"]["target"]["billable"], $response["users"]);
            $response["billable_utilization_rate_per_user"] = array_merge(["names" => array_keys($response["billable_utilization_rate_per_user"]), "values" => array_values($response["billable_utilization_rate_per_user"])]);
            $response["non_billable_utilization_rate_per_user"] = array_merge(["names" => array_keys($response["non_billable_utilization_rate_per_user"]), "values" => array_values($response["non_billable_utilization_rate_per_user"])]);
            $this->output->set_content_type("application/json")->set_output(json_encode($response));
        } else {
            $this->load->model("user_preference");
            $this->user_preference->fetch(["user_id" => $this->session->userdata("AUTH_user_id"), "keyName" => "organization"]);
            $this->load->model("organization", "organizationfactory");
            $this->organization = $this->organizationfactory->get_instance();
            $this->load->model("provider_group");
            $this->load->model("seniority_level");
            $data["organization_list"] = $this->organization->load_list(["where" => ["status", "Active"]], ["key" => "id", "value" => "name"]);
            $organization_id = $this->user_preference->get_field("keyValue");
            if (!$organization_id && !empty($data["organization_list"])) {
                $organization_id = reset($data["organization_list"]);
            }
            $this->load->model("user", "userfactory");
            $this->user = $this->userfactory->get_instance();
            $users = $this->user->load_users_list();
            $data["organization_id"] = $organization_id;
            $data["user_list"] = $users;
            $data["selected_year"] = $year;
            $data["assigned_teams"] = $this->provider_group->load_list(["where" => ["allUsers", 0]]);
            $data["seniority_levels"] = $this->seniority_level->load_list([]);
            $this->includes("jquery/apexcharts/polyfill.min", "js");
            $this->includes("jquery/apexcharts/classlist", "js");
            $this->includes("jquery/apexcharts/canvg", "js");
            $this->includes("jquery/apexcharts/apexcharts.min", "js");
            $this->includes("scripts/time_tracking_dashboard", "js");
            $this->load->view("partial/header");
            $this->load->view("dashboards/time_tracking_dashboard", $data);
            $this->load->view("partial/footer");
        }
    }
    public function get_filter_users()
    {
        if (!$this->input->is_ajax_request()) {
            show_404();
        } else {
            $this->load->model("user", "userfactory");
            $this->user = $this->userfactory->get_instance();
            $response["users"] = $this->user->load_users_list($this->input->get("assigned_teams"), ["key" => "id", "value" => "name"]);
            $this->output->set_content_type("application/json")->set_output(json_encode($response));
        }
    }
    public function export_time_tracking_dashboard_pdf()
    {
        if ($this->input->is_ajax_request()) {
            $response = [];
            $data = $this->input->post(NULL, false);
            $html = $this->load->view("dashboards/time_tracking_dashboard_pdf", $data, true);
            $file_name = "time_tracking_dashboard" . date("YmdHi");
            require_once substr(COREPATH, 0, -12) . "/application/libraries/mpdf/vendor/autoload.php";
            $mpdf = new Mpdf\Mpdf(["mode" => "utf-8", "format" => "Legal", "default_font_size" => 10, "default_font" => "dejavusans", "pagenumPrefix" => $this->lang->line("page") . " ", "nbpgPrefix" => " " . $this->lang->line("of") . " "]);
            if ($this->is_auth->is_layout_rtl()) {
                $mpdf->SetDirectionality("rtl");
                $mpdf->autoScriptToLang = true;
                $mpdf->autoLangToFont = true;
            }
            $mpdf->shrink_tables_to_fit = 0;
            ini_set("pcre.backtrack_limit", "150000000");
            $mpdf->WriteHTML($html);
            $mpdf->Output($this->config->item("files_path") . DIRECTORY_SEPARATOR . "tmp" . DIRECTORY_SEPARATOR . $file_name . ".pdf", "F");
            $response["file_name"] = $file_name;
            $this->output->set_content_type("application/json");
            $this->output->set_output(json_encode($response));
        }
    }
    public function download_dashboard_pdf($file_name)
    {
        $this->load->helper("download");
        $file_path = $this->config->item("files_path") . DIRECTORY_SEPARATOR . "tmp" . DIRECTORY_SEPARATOR . $file_name . ".pdf";
        register_shutdown_function("unlink", $file_path);
        force_download($file_path, NULL);
    }
    public function getting_started()
    {
        $this->pageTitle = sprintf($this->lang->line("sheria360_browser_title"), $this->lang->line("getting_started"));
        if ($this->cloud_installation_type && $this->session->userdata("show_getting_started")) {
            if ($this->input->is_ajax_request() && $this->input->post("hide")) {
                $getting_started_settings = unserialize($this->user_preference->get_value("getting_started"));
                $getting_started_settings["show"] = false;
                $response["result"] = $this->user_preference->set_value("getting_started", serialize($getting_started_settings), true);
                $this->output->set_content_type("application/json")->set_output(json_encode($response));
            } else {
                $data = [];
                $data["getting_started_settings"] = unserialize($this->user_preference->get_value("getting_started"));
                $steps_done = 0;
                if (!empty($data["getting_started_settings"])) {
                    foreach ($data["getting_started_settings"] as $key => $val) {
                        if (mb_substr($key, -10) === "_step_done" && $val) {
                            $steps_done++;
                        }
                    }
                }
                $data["steps_done"] = $steps_done;
                $data["getting_started_steps"] = ["avatar" => "avatarUploaderForm()", "company" => "companyAddForm()", "contact" => "contactAddForm()", "legal_matter" => "legalMatterAddForm()", "task" => "taskAddForm()", "calendar_meeting" => "meetingForm()"];
                $this->includes("jquery/tipsy/js/jquery.tipsy.min", "js");
                $this->includes("jquery/tipsy/css/jquery.tipsy", "css");
                $this->includes("jquery/kuma_gauge/raphael-min", "js");
                $this->includes("jquery/kuma_gauge/kuma-gauge", "js");
                $this->includes("scripts/getting_started", "js");
                $this->load->view("partial/header");
                $this->load->view("home/getting_started", $data);
                $this->load->view("partial/footer");
            }
        } else {
            redirect("dashboard");
        }
    }
    public function admin()
    {
        $this->pageTitle = sprintf($this->lang->line("sheria360_browser_title"), $this->lang->line("administration_and_setup"));
        $this->currentTopNavItem = "dashboard-admin";
        $licenseExpiry = (int) $this->licensor->get("expiry") < 1 ? 0 : strtotime($this->licensor->get("expiry"));
        $licenseExpiryWithin = $licenseExpiry - time();
        $licenseExpiryMessage = "";
        $systemPreferences = $this->session->userdata("systemPreferences");
        if ($licenseExpiry) {
            if (-86400 < $licenseExpiryWithin && $licenseExpiryWithin < 86400) {
                $licenseExpiryMessage = $this->lang->line("support_agreement_expires_today");
            } else {
                if ($licenseExpiryWithin < 0) {
                    $licenseExpiryMessage = sprintf($this->lang->line("support_agreement_expired_on_date"), date("d M, Y", $licenseExpiry));
                } else {
                    if ($licenseExpiryWithin < 1209600) {
                        $licenseExpiryMessage = sprintf($this->lang->line("support_agreement_expires_on_date"), date("d M, Y", $licenseExpiry));
                    }
                }
            }
        }
        $AllowFeatureSLAManagement = isset($systemPreferences["AllowFeatureSLAManagement"]) ? $systemPreferences["AllowFeatureSLAManagement"] == "yes" : false;
        $AllowContractSLAManagement = isset($systemPreferences["AllowContractSLAManagement"]) ? $systemPreferences["AllowContractSLAManagement"] == "yes" : false;
        $installation_type = $this->cloud_installation_type;
        $this->load->model("saml_configuration");
        $saml_configuration = $this->saml_configuration->get_values();
        $enabled_idp = $saml_configuration["idp"] == "none" ? false : $saml_configuration["idp"];
        $this->load->view("partial/header");
        $this->load->view("dashboards/admin", array_merge(compact("licenseExpiryMessage"), ["adEnabled" => $systemPreferences["adEnabled"]], ["makerCheckerFeatureEnabled" => $systemPreferences["makerCheckerFeatureStatus"] === "yes", "AllowFeatureSLAManagement" => $AllowFeatureSLAManagement, "enabled_idp" => $enabled_idp, "is_cloud" => $installation_type, "AllowContractSLAManagement" => $AllowContractSLAManagement]));
        $this->load->view("partial/footer");
    }
    public function cases_result($planningBoardId = "")
    {
        if ($this->input->post(NULL)) {
            $case_id = $this->input->post("caseId");
            $caseStatusId = $this->input->post("newStatus");
            $old_status = $this->input->post("oldStatus");
            if (!empty($case_id) && !empty($caseStatusId)) {
                $this->load->model("workflow_status", "workflow_statusfactory");
                $this->workflow_status = $this->workflow_statusfactory->get_instance();
                $this->load->model("matter_fields", "matter_fieldsfactory");
                $this->matter_fields = $this->matter_fieldsfactory->get_instance();
                $this->legal_case->fetch($case_id);
                $this->matter_fields->load_all_fields($this->legal_case->get_field("category"), $this->legal_case->get_field("case_type_id"));
                $this->load->model("workflow_status_transition", "workflow_status_transitionfactory");
                $this->workflow_status_transition = $this->workflow_status_transitionfactory->get_instance();
                $response = ["result" => true, "display_message" => ""];
                $workflow_applicable = 0 < $this->legal_case->get_field("workflow") ? $this->legal_case->get_field("workflow") : 1;
                $this->workflow_status_transition->fetch(["workflow_id" => $workflow_applicable, "fromStep" => $old_status, "toStep" => $caseStatusId]);
                $system_preferences = $this->session->userdata("systemPreferences");
                $this->load->model("user", "userfactory");
                $this->user = $this->userfactory->get_instance();
                $transition = $this->workflow_status_transition->get_field("id");
                if ($this->input->post("action") == "return_screen") {
                    if (!$this->workflow_status->check_transition_allowed($case_id, $caseStatusId, $this->is_auth->get_user_id())) {
                        $response["result"] = false;
                        $response["display_message"] = $this->lang->line("transition_not_allowed");
                    } else {
                        $data = $this->matter_fields->return_screen_fields($case_id, $transition);
                        if ($data) {
                            $data["title"] = $this->workflow_status_transition->get_field("name");
                            $response["transition_id"] = $transition;
                            $response["screen_html"] = $this->load->view("templates/screen_fields", $data, true);
                        } else {
                            if (!$this->update_case_status($case_id, $caseStatusId, $old_status, $transition)) {
                                $response["result"] = false;
                                $response["display_message"] = $this->lang->line("workflowActionInvalid");
                            }
                        }
                    }
                } else {
                    $validation = $this->matter_fields->validate_fields($this->input->post("transition"));
                    $response["result"] = $validation["result"];
                    if (!$validation["result"]) {
                        $response["validation_errors"] = $validation["errors"];
                    } else {
                        if ($this->update_case_status($case_id, $caseStatusId, $old_status, $transition)) {
                            if (!$this->matter_fields->save_fields($case_id)) {
                                $response["result"] = false;
                                $response["display_message"] = $this->lang->line("records_not_saved");
                            }
                        } else {
                            $response["result"] = false;
                            $response["display_message"] = $this->lang->line("workflowActionInvalid");
                        }
                    }
                }
                $this->load->model("planning_board_column", "planning_board_columnfactory");
                $this->planning_board_column = $this->planning_board_columnfactory->get_instance();
                $is_board_filter = $this->input->post("isBoardFilter");
                $quick_filter = $this->input->post("quickFilter");
                $filter_id = $this->input->post("savedFilterValue");
                $filter_id = $filter_id ? $filter_id : "";
                empty($is_board_filter);
                empty($is_board_filter) ? "false" : $is_board_filter;
                $data["planning_board_id"] = $planningBoardId;
                $saved_filters = [];
                if ($this->input->post(NULL) && !empty($filter_id)) {
                    $this->load->model("grid_saved_filter", "grid_saved_filterfactory");
                    if ($is_board_filter == "false") {
                        $this->grid_saved_filter = $this->grid_saved_filterfactory->get_instance();
                        if ($this->grid_saved_filter->fetch($filter_id)) {
                            $gridSavedData = $this->grid_saved_filter->load_data($filter_id);
                            $saved_filters = unserialize($gridSavedData["formData"]);
                        }
                    } else {
                        $this->load->model("planning_board");
                        $this->load->model("planning_board_saved_filter", "planning_board_filter");
                        if ($this->planning_board_filter->fetch($filter_id)) {
                            $grid_board_filter = $this->planning_board_filter->get_fields();
                            $saved_grid_board_filter = unserialize($grid_board_filter["keyValue"]);
                            $saved_filters = $this->planning_board_filter->convert_matter_filter($saved_grid_board_filter);
                        }
                    }
                }
                $response["caseBoardColumnOptions"] = $this->planning_board_column->get_planning_board_column_options_data($planningBoardId, [], $saved_filters, $quick_filter);
                $result = [];
                if (!empty($response["caseBoardColumnOptions"]["cases"])) {
                    foreach ($response["caseBoardColumnOptions"]["cases"] as $key => $value) {
                        $result[$key] = sizeof($value);
                    }
                }
                $this->workflow_status->fetch($caseStatusId);
                $response["newStatusName"] = $this->workflow_status->get_field("name");
                $response["caseId"] = $case_id;
                $response["data"] = $result;
                $this->output->set_content_type("application/json")->set_output(json_encode($response));
            }
        }
    }
    private function update_case_status($case_id = 0, $status_id = 0, $old_status = 0, $transition = 0)
    {
        if (!$this->workflow_status->moveStatus($case_id, $status_id, $this->is_auth->get_user_id(), NULL)) {
            return false;
        }
        $this->notify_users($case_id, $status_id, $old_status, $transition);
        return true;
    }
    private function notify_users($case_id, $statusId, $old_status, $transition = 0)
    {
        $this->legal_case->fetch($case_id);
        if (!strcmp($this->legal_case->get_field("channel"), $this->legal_case->get("portalChannel"))) {
            $this->load->model("customer_portal_users", "customer_portal_usersfactory");
            $this->customer_portal_users = $this->customer_portal_usersfactory->get_instance();
            $this->customer_portal_users->fetch($this->legal_case->get_field("createdBy"));
            $toEmail = $this->customer_portal_users->get_field("email");
            $this->legal_case->notifyTicketUser($case_id, $statusId, $this->is_auth->get_user_id(), $this->session->userdata("AUTH_userProfileName"), $this->legal_case->get_field("createdBy"), $toEmail, false, $this->legal_case->get("webChannel"), $old_status);
        } else {
            $this->legal_case->notify_related_users($case_id, $old_status, $transition);
        }
        return true;
    }
    public function cases($planning_board_id = "")
    {
        $this->authenticate_actions_per_license();
        $this->load->model("grid_saved_board_filters_users");
        $planning_board_id = !empty($planning_board_id) ? $planning_board_id : $this->input->post("planning_board_id");
        $is_board_filter = $this->input->post("isBoardFilter");
        $filter_id = $this->input->post("filter_id");
        if (!empty($planning_board_id)) {
            $this->grid_saved_board_filters_users->set_default_filter($planning_board_id, $this->is_auth->get_user_id(), $filter_id, $is_board_filter);
        }
        $planning_board_id = $this->get_board_id($planning_board_id, $filter_id, $is_board_filter);
        if ($this->input->is_ajax_request()) {
            $response["status"] = false;
            $action = $this->input->post("action");
            $quick_filter = $this->input->post("quickFilter");
            switch ($action) {
                case "load_columns":
                    $data = $this->get_case_columns_data($planning_board_id, $filter_id, $quick_filter, empty($is_board_filter) ? "false" : $is_board_filter);
                    $response["board_options_columns"] = $data["board_options"]["columns"];
                    $response["board_options_cases"] = $data["board_options"]["cases"];
                    $response["is_board_filter"] = $is_board_filter;
                    $response["status"] = true;
                    $response["filter_id"] = $filter_id;
                    $response["html"] = $this->load->view("dashboards/cases_board", $data, true);
                    break;
                case "filter":
                    $data = $this->get_filter_data($planning_board_id);
                    $response["status"] = true;
                    $response["filter_id"] = $filter_id;
                    $response["is_board_filter"] = $is_board_filter;
                    $response["html"] = $this->load->view("dashboards/board_filter_form", $data, true);
                    break;
                default:
            }
                    $this->output->set_content_type("application/json")->set_output(json_encode($response));

        } else {
            $this->load->model("planning_board_column", "planning_board_columnfactory");
            $this->planning_board_column = $this->planning_board_columnfactory->get_instance();
            $data = $this->get_filter_data($planning_board_id);
            $data["filter_id"] = $filter_id;
            $data["planning_board_id"] = $planning_board_id;
            $data["is_board_filter"] = $is_board_filter;
            $this->includes("jquery/css/chosen", "css");
            $this->includes("jquery/chosen.min", "js");
            $this->includes("scripts/case_boards", "js");
            $this->includes("scripts/boards", "js");
            $this->includes("dragula/dragula.min", "css");
            $this->includes("jquery/form2js", "js");
            $this->includes("styles/ltr/fixes", "css");
            $this->includes_footer("dragula/dragula.min", "js");
            $this->includes_footer("dragula/dom-autoscroller.min", "js");
            $this->load->view("partial/header");
            $this->load->view("dashboards/cases", $data);
            $this->load->view("partial/footer");
        }
    }
    private function get_board_id($planning_board_id, &$filter_id, &$is_board_filter)
    {
        $this->load->model(["planning_board", "provider_group"]);
        $planning_boards_list = $this->planning_board->load_list();
        $default_filter = $this->grid_saved_board_filters_users->get_default_filter($this->is_auth->get_user_id());
        if ($default_filter) {
            if ($this->planning_board->fetch($default_filter["board_id"])) {
                $planning_board_id = $default_filter["board_id"];
                $filter_id = $default_filter["filter_id"];
                $is_board_filter = $default_filter["is_board"];
            }
        } else {
            if (sizeof($planning_boards_list) == 1) {
                $only_case_board = array_keys($planning_boards_list);
                $planning_board_id = $only_case_board[0];
            }
        }
        return $planning_board_id;
    }
    private function get_task_board_id($planning_board_id, &$filter_id)
    {
        $this->load->model(["task_board", "provider_group"]);
        $planning_task_boards_list = $this->task_board->load_list();
        $default_filter = $this->grid_saved_board_task_filters_users->get_default_filter($this->is_auth->get_user_id());
        if ($default_filter) {
            if ($this->task_board->fetch($default_filter["board_id"])) {
                $planning_board_id = $default_filter["board_id"];
                $filter_id = $default_filter["filter_id"];
                $is_board_filter = $default_filter["is_board"];
            }
        } else {
            if (sizeof($planning_task_boards_list) == 1) {
                $only_task_board = array_keys($planning_task_boards_list);
                $planning_board_id = $only_task_board[0];
            }
        }
        return $planning_board_id;
    }
    private function get_workflow_transition()
    {
        $this->load->model("workflow_status", "workflow_statusfactory");
        $this->workflow_status = $this->workflow_statusfactory->get_instance();
        $possible_transitions = $this->workflow_status->load_all_possible_transitions();
        $global_transitions = $this->workflow_status->get_all_global_statuses();
        if (!empty($possible_transitions) && !empty($global_transitions)) {
            foreach ($possible_transitions as $transition_key => $transition) {
                foreach ($global_transitions as $global) {
                    if ($transition["workflow_id"] === $global["workflow"]) {
                        $possible_transitions[$transition_key]["allowed_transitions"] .= "," . $global["id"];
                    }
                }
            }
        }
        foreach ($global_transitions as $t_key => $t_global) {
            $possible_transitions[] = ["id" => $t_global["id"], "isGlobal" => 1, "allowed_transitions" => "", "workflow_id" => ""];
        }
        foreach ($possible_transitions as $trans => $trans_value) {
            if ($trans_value["isGlobal"] == 1) {
                foreach ($global_transitions as $global) {
                    if ($trans_value["id"] === $global["id"]) {
                        $possible_transitions[$trans]["workflow_id"] = empty($possible_transitions[$trans]["workflow_id"]) ? $global["workflow"] : $possible_transitions[$trans]["workflow_id"] . "," . $global["workflow"];
                    }
                }
            }
        }
        foreach ($possible_transitions as $key => $possible_transition) {
            $transitions[$possible_transition["id"]] = $possible_transition;
        }
        return $transitions;
    }
    private function get_task_filter_data($task_board_id)
    {
        $data = [];
        $this->load->model("user", "userfactory");
        $this->user = $this->userfactory->get_instance();
        $this->load->model("board_task_post_filters", "board_task_post_filtersfactory");
        $this->board_task_post_filters = $this->board_task_post_filtersfactory->get_instance();
        $this->load->model("provider_group", "task_board");
        $this->load->model("task_board_saved_filter", "task_board_filter");
        $this->load->model("task", "taskfactory");
        $this->task = $this->taskfactory->get_instance();
        $data["filterId"] = "";
        $systemPreferences = $this->session->userdata("systemPreferences");
        $filters = ["provider_groups_list" => [], "users_list" => [], "caseId" => "", "due_date" => NULL, "created_on" => ""];
        $data["provider_groups_list"] = $this->provider_group->load_list(["where" => ["allUsers !=", 1]]);
        $provider_group_id = !empty($filters["provider_groups_list"]) ? $filters["provider_groups_list"] : NULL;
        $data["users_list"] = $this->user->load_users_list($provider_group_id);
        $data["filters"] = $filters;
        $data["task_board_id"] = $task_board_id;
        $this->load->model("legal_case", "legal_casefactory");
        $this->legal_case = $this->legal_casefactory->get_instance();
        $data["is_litigation"] = $this->legal_case->get_field("category") === "Litigation";
        $data["task_model_code"] = $this->task->get("modelCode");
        $data["portal_channel"] = $this->legal_case->get("portalChannel");
        $this->load->model("case_type");
        $case_category = NULL;
        if (!empty($filters["type_filter"])) {
            $case_category = $filters["type_filter"] == 2 ? "litigation" : ($filters["type_filter"] == 1 ? "corporate" : NULL);
        }
        $case_types = $this->case_type->api_load_all_types_per_case_category($case_category);
        $data["case_types"] = array_combine(array_column($case_types, "id"), array_column($case_types, "name"));
        if ($systemPreferences["archiveCaseStatus"]) {
            $this->load->model("workflow_status", "workflow_statusfactory");
            $this->workflow_status = $this->workflow_statusfactory->get_instance();
            $defaultWorkflow = "";
            $archiveCaseStatus = $this->workflow_status->loadListWorkflowStatuses($defaultWorkflow, ["where" => [["id IN ( " . $systemPreferences["archiveCaseStatus"] . ")", NULL, false]]]);
            $archiveCaseStatusStr = implode(", ", array_values($archiveCaseStatus));
            $data["archive_case_status"] = $archiveCaseStatusStr;
        } else {
            $data["archive_case_status"] = "";
        }
        $tasks_boards_filters = $this->task_board_filter->get_saved_filters($task_board_id);
        $this->load->model(["planning_board", "provider_group"]);
        $data["task_boards_list"] = $this->task_board->load_list();
        $data["grid_saved_filters"] = $tasks_boards_filters;
        $data["tasks_boards_filters"] = $tasks_boards_filters;
        $data["post_filters"] = $this->board_task_post_filters->load_all_post_filters($task_board_id);
        return $data;
    }
    private function get_filter_data($planning_board_id)
    {
        $data = [];
        $this->load->model("user", "userfactory");
        $this->load->model("client");
        $this->user = $this->userfactory->get_instance();
        $this->load->model("board_post_filters", "board_post_filtersfactory");
        $this->board_post_filters = $this->board_post_filtersfactory->get_instance();
        $this->load->model("provider_group");
        $this->load->model("planning_board_saved_filter", "planning_board_filter");
        $data["filterId"] = "";
        $systemPreferences = $this->session->userdata("systemPreferences");
        $filters = ["provider_groups_list" => [], "users_list" => [], "show_list" => [], "due_date" => NULL, "case_type_id" => [], "case_arrival_date" => NULL, "clients_list" => [], "type_filter" => NULL, "priority" => NULL];
        $data["provider_groups_list"] = $this->provider_group->load_list(["where" => ["allUsers !=", 1]]);
        $provider_group_id = !empty($filters["provider_groups_list"]) ? $filters["provider_groups_list"] : NULL;
        $data["users_list"] = $this->user->load_users_list($provider_group_id);
        $data["show_list"] = ["" => $this->lang->line("all") . ": " . $this->lang->line("assigned"), $this->lang->line("only_assigned"), $this->lang->line("only_unassigned")];
        $data["clients_list"] = $this->client->load_clients_list();
        $data["type_filter"] = ["" => $this->lang->line("category") . ": " . $this->lang->line("all"), "Matter" => $this->lang->line("corporate_matter"), "Litigation" => $this->lang->line("the_litigation_case")];
        $data["priority"] = ["" => $this->lang->line("priority") . ": " . $this->lang->line("all")] + array_combine($this->legal_case->get("priorityValues"), [$this->lang->line("critical"), $this->lang->line("high"), $this->lang->line("medium"), $this->lang->line("low")]);
        $data["filters"] = $filters;
        $data["planning_board_id"] = $planning_board_id;
        $this->load->model("legal_case", "legal_casefactory");
        $this->legal_case = $this->legal_casefactory->get_instance();
        $data["is_litigation"] = $this->legal_case->get_field("category") === "Litigation";
        $data["case_model_code"] = $this->legal_case->get("modelCode");
        $data["portal_channel"] = $this->legal_case->get("portalChannel");
        $this->load->model("case_type");
        $case_category = NULL;
        if (!empty($filters["type_filter"])) {
            $case_category = $filters["type_filter"] == 2 ? "litigation" : ($filters["type_filter"] == 1 ? "corporate" : NULL);
        }
        $case_types = $this->case_type->api_load_all_types_per_case_category($case_category);
        $data["case_types"] = array_combine(array_column($case_types, "id"), array_column($case_types, "name"));
        if ($systemPreferences["archiveCaseStatus"]) {
            $this->load->model("workflow_status", "workflow_statusfactory");
            $this->workflow_status = $this->workflow_statusfactory->get_instance();
            $defaultWorkflow = "";
            $archiveCaseStatus = $this->workflow_status->loadListWorkflowStatuses($defaultWorkflow, ["where" => [["id IN ( " . $systemPreferences["archiveCaseStatus"] . ")", NULL, false]]]);
            $archiveCaseStatusStr = implode(", ", array_values($archiveCaseStatus));
            $data["archive_case_status"] = $archiveCaseStatusStr;
        } else {
            $data["archive_case_status"] = "";
        }
        $planning_boards = $this->planning_board_filter->get_saved_filters($planning_board_id);
        $this->load->model(["planning_board", "provider_group"]);
        $matter_filter = $this->get_filter_by_model("Matter");
        $Litigation_filter = $this->get_filter_by_model("Litigation");
        $data["planning_boards_list"] = $this->planning_board->load_list();
        $data["grid_saved_filters"] = array_merge($matter_filter["gridSavedFilters"], $Litigation_filter["gridSavedFilters"]);
        $data["planning_board_filters"] = $planning_boards;
        $data["post_filters"] = $this->board_post_filters->load_all_post_filters($planning_board_id);
        return $data;
    }
    private function get_case_columns_data($planning_board_id, $filter_id, $quick_filter, $is_board_filter)
    {
        $this->pageTitle = sprintf($this->lang->line("sheria360_browser_title"), $this->lang->line("case_board"));
        $this->load->model("planning_board_column", "planning_board_columnfactory");
        $this->planning_board_column = $this->planning_board_columnfactory->get_instance();
        $data["planning_board_id"] = $planning_board_id;
        $saved_filters = [];
        if ($this->input->post(NULL) && !empty($filter_id)) {
            $this->load->model("grid_saved_filter", "grid_saved_filterfactory");
            if ($is_board_filter == "false") {
                $this->grid_saved_filter = $this->grid_saved_filterfactory->get_instance();
                if ($this->grid_saved_filter->fetch($filter_id)) {
                    $gridSavedData = $this->grid_saved_filter->load_data($filter_id);
                    $saved_filters = unserialize($gridSavedData["formData"]);
                }
            } else {
                $this->load->model("planning_board");
                $this->load->model("planning_board_saved_filter", "planning_board_filter");
                if ($this->planning_board_filter->fetch($filter_id)) {
                    $grid_board_filter = $this->planning_board_filter->get_fields();
                    $saved_grid_board_filter = unserialize($grid_board_filter["keyValue"]);
                    $saved_filters = $this->planning_board_filter->convert_matter_filter($saved_grid_board_filter);
                }
            }
        }
        $data["board_options"] = $this->planning_board_column->get_planning_board_column_options_data($planning_board_id, [], $saved_filters, $quick_filter);
        $data["columns_counts"] = count($data["board_options"]["columns"]);
        $data["direction"] = $this->is_auth->is_layout_rtl() ? "rtl" : "ltr";
        $data["possible_transitions"] = $this->get_workflow_transition();
        return $data;
    }
    private function get_task_columns_data($task_board_id, $filter_id, $quick_filter)
    {
        $this->pageTitle = sprintf($this->lang->line("sheria360_browser_title"), $this->lang->line("task_board"));
        $this->load->model("task_board_column", "task_board_columnfactory");
        $this->task_board_column = $this->task_board_columnfactory->get_instance();
        $data["task_board_id"] = $task_board_id;
        if ($this->input->post(NULL) && !empty($task_board_id)) {
            $this->load->model("task_board_saved_filter", "task_board_filter");
            $data["saved_filters"] = $this->task_board_filter->get_saved_filters($task_board_id);
        }
        $saved_filters = [];
        if ($this->input->post(NULL) && !empty($filter_id) && $this->task_board_filter->fetch((int) $filter_id)) {
            $grid_board_filter = $this->task_board_filter->get_fields();
            $saved_grid_board_filter = unserialize($grid_board_filter["keyValue"]);
            $data["saved_grid_board_filter"] = unserialize($grid_board_filter["keyValue"]);
            $saved_filters = $this->task_board_filter->convert_matter_filter($saved_grid_board_filter);
            $data["saved_grid_details"] = $saved_filters["gridFilters"];
        }
        $data["board_options"] = $this->task_board_column->get_task_board_column_options_data($task_board_id, [], $saved_filters, $this->input->post("quickFilter"));
        $data["columns_counts"] = count($data["board_options"]["columns"]);
        $data["direction"] = $this->is_auth->is_layout_rtl() ? "rtl" : "ltr";
        $this->load->model("task_workflow_status_transition", "task_workflow_status_transitionfactory");
        $this->task_workflow_status_transition = $this->task_workflow_status_transitionfactory->get_instance();
        $data["possible_transitions"] = $this->task_workflow_status_transition->load_all_transitions_per_workflow();
        return $data;
    }
    public function board_post_filters($board_id = "", $post_board_id = "")
    {
        $this->pageTitle = sprintf($this->lang->line("sheria360_browser_title"), $this->lang->line("post_filters"));
        $this->load->model(["planning_board", "planning_board_column_option"]);
        $this->load->model("planning_board_column", "planning_board_columnfactory");
        $this->planning_board_column = $this->planning_board_columnfactory->get_instance();
        $this->load->model("board_post_filters", "board_post_filtersfactory");
        $this->board_post_filters = $this->board_post_filtersfactory->get_instance();
        $data = [];
        $data["fields_details"] = $this->board_post_filters->load_fields();
        $data["fields_data"] = $this->board_post_filters->fields_details;
        if ($this->input->is_ajax_request()) {
            if ($this->input->post(NULL)) {
                $response = $this->board_post_filters->load_all_post_filters($this->input->post("boardId"));
            } else {
                $data["board_id"] = $board_id;
                $data["title"] = $this->lang->line("add_new_post_filter");
                $data["fields_filter"] = $this->board_post_filters->fields_details;
                $data["operators"]["list"] = $this->get_filter_operators("list");
                $data["operators"]["contain"] = $this->get_filter_operators("text");
                $data["operator_options"] = [];
                if (!empty($post_board_id) && $this->board_post_filters->fetch($post_board_id)) {
                    $data["board_post_filters_data"] = $this->board_post_filters->get_fields();
                    $data["title"] = $this->lang->line("edit_new_post_filter");
                    foreach ($data["fields_filter"] as $fields_filter) {
                        if ($fields_filter["db_value"] == $data["board_post_filters_data"]["field"] && isset($data["operators"][$fields_filter["operator_type"]])) {
                            $data["operator_options"] = $data["operators"][$fields_filter["operator_type"]];
                        }
                    }
                }
                $response["status"] = true;
                $data["post_board_id"] = $post_board_id;
                $response["html"] = $this->load->view("dashboards/post_filter_board", $data, true);
            }
            $this->output->set_content_type("application/json")->set_output(json_encode($response));
        } else {
            $this->includes("scripts/case_boards", "js");
            $data["caseStatuses"] = $this->workflow_status->loadStatusesUniqueList();
            $data["board_id"] = $board_id;
            $data["operators"]["list"] = $this->get_filter_operators("list");
            $data["operators"]["contain"] = $this->get_filter_operators("text");
            $this->load->view("partial/header");
            $this->load->view("dashboards/case_board_post_filter", $data);
            $this->load->view("partial/footer");
        }
    }
    public function board_task_post_filters($board_id = "", $post_board_id = "")
    {
        $this->pageTitle = sprintf($this->lang->line("sheria360_browser_title"), $this->lang->line("post_filters"));
        $this->load->model("task_board_column", "task_board_columnfactory");
        $this->task_board_column = $this->task_board_columnfactory->get_instance();
        $this->load->model("board_task_post_filters", "board_task_post_filtersfactory");
        $this->board_task_post_filters = $this->board_task_post_filtersfactory->get_instance();
        $data = [];
        $data["fields_details"] = $this->board_task_post_filters->load_fields();
        $data["fields_data"] = $this->board_task_post_filters->fields_details;
        if ($this->input->is_ajax_request()) {
            if ($this->input->post(NULL)) {
                $response = $this->board_task_post_filters->load_all_post_filters($this->input->post("boardId"));
            } else {
                $data["board_id"] = $board_id;
                $data["title"] = $this->lang->line("add_new_post_filter");
                $data["fields_filter"] = $this->board_task_post_filters->fields_details;
                $data["operators"]["list"] = $this->get_filter_operators("list");
                $data["operators"]["contain"] = $this->get_filter_operators("text");
                $data["operator_options"] = [];
                if (!empty($post_board_id) && $this->board_task_post_filters->fetch($post_board_id)) {
                    $data["board_post_filters_data"] = $this->board_task_post_filters->get_fields();
                    $data["title"] = $this->lang->line("edit_new_post_filter");
                    foreach ($data["fields_filter"] as $fields_filter) {
                        if ($fields_filter["db_value"] == $data["board_post_filters_data"]["field"] && isset($data["operators"][$fields_filter["operator_type"]])) {
                            $data["operator_options"] = $data["operators"][$fields_filter["operator_type"]];
                        }
                    }
                }
                $response["status"] = true;
                $data["post_board_id"] = $post_board_id;
                $response["html"] = $this->load->view("dashboards/post_task_filter_board", $data, true);
            }
            $this->output->set_content_type("application/json")->set_output(json_encode($response));
        } else {
            $this->includes("scripts/case_boards", "js");
            $data["caseStatuses"] = $this->workflow_status->loadStatusesUniqueList();
            $data["board_id"] = $board_id;
            $data["operators"]["list"] = $this->get_filter_operators("list");
            $data["operators"]["contain"] = $this->get_filter_operators("text");
            $this->load->view("partial/header");
            $this->load->view("dashboards/task_board_post_filter", $data);
            $this->load->view("partial/footer");
        }
    }
    private function get_filter_by_model($category)
    {
        $data = [];
        $this->load->model("grid_saved_filter", "grid_saved_filterfactory");
        $this->grid_saved_filter = $this->grid_saved_filterfactory->get_instance();
        $this->load->model("grid_saved_column");
        $data["model"] = $category;
        $data["gridSavedFilters"] = $this->grid_saved_filter->loadFiltersList($category, $this->session->userdata("AUTH_user_id"));
        $data["gridSavedFiltersData"] = false;
        $data["gridDefaultFilter"] = $this->grid_saved_filter->getDefaultFilter($category, $this->session->userdata("AUTH_user_id"));
        if ($data["gridDefaultFilter"]) {
            $gridSavedData = $this->grid_saved_filter->load_data($data["gridDefaultFilter"]["id"]);
            $data["gridSavedFiltersData"] = unserialize($gridSavedData["formData"]);
            $data = array_merge($data, $this->grid_saved_column->get_user_grid_details($category, $data["gridDefaultFilter"]["id"]));
        } else {
            $data = array_merge($data, $this->grid_saved_column->get_user_grid_details($category));
        }
        return $data;
    }
    public function case_boards()
    {
        $this->authenticate_actions_per_license();
        $this->pageTitle = sprintf($this->lang->line("sheria360_browser_title"), $this->lang->line("case_board"));
        $this->load->model("planning_board");
        $this->load->model("planning_board_column", "planning_board_columnfactory");
        $this->load->model("planning_board_saved_filter", "planning_board_filter");
        $this->planning_board_column = $this->planning_board_columnfactory->get_instance();
        if ($this->input->is_ajax_request()) {
            $response = [];
            $planningBoardId = $this->input->post("id");
            $this->load->model("planning_board_column_option");
            $userId = $this->session->userdata("AUTH_user_id");
            if ($this->planning_board_column_option->delete(["where" => ["planning_board_id", $planningBoardId]])) {
                $this->planning_board_column->delete(["where" => ["planning_board_id", $planningBoardId]]);
                $this->planning_board_filter->delete(["where" => ["boardId", $planningBoardId]]);
                $this->planning_board->delete(["where" => ["id", $planningBoardId]]);
                delete_cookie($this->is_auth->get_user_id() . "planning_board_id");
                $response["status"] = 500;
            } else {
                if ($this->planning_board_filter->delete(["where" => ["boardId", $planningBoardId]])) {
                    $this->planning_board->delete(["where" => ["id", $planningBoardId]]);
                    delete_cookie($this->is_auth->get_user_id() . "planning_board_id");
                    $response["status"] = 500;
                } else {
                    if ($this->planning_board->delete(["where" => ["id", $planningBoardId]])) {
                        delete_cookie($this->is_auth->get_user_id() . "planning_board_id");
                        $response["status"] = 500;
                    } else {
                        $response["status"] = 102;
                    }
                }
            }
            $this->output->set_content_type("application/json");
            $this->output->set_output(json_encode($response));
        } else {
            $data = [];
            $data["adminPlanningBoard"] = $this->planning_board_column->get_planning_Board_columns();
            $this->includes("scripts/case_boards", "js");
            $this->includes("scripts/boards", "js");
            $this->load->view("partial/header");
            $this->load->view("dashboards/case_boards", $data);
            $this->load->view("partial/footer");
        }
    }
    public function case_board_config($id = "0")
    {
        $this->authenticate_actions_per_license();
        $this->pageTitle = sprintf($this->lang->line("sheria360_browser_title"), $this->lang->line("case_board"));
        $this->load->model(["planning_board", "planning_board_column_option"]);
        $this->load->model("planning_board_column", "planning_board_columnfactory");
        $this->planning_board_column = $this->planning_board_columnfactory->get_instance();
        $this->load->model("board_post_filters", "board_post_filtersfactory");
        $this->board_post_filters = $this->board_post_filtersfactory->get_instance();
        $planningBoardId = "";
        $planningBoardName = "";
        $planningBoardColumns = [];
        $planningBoardColumnOptions = [];
        if ($this->input->post(NULL)) {
            $dataPlanningBoard = $this->input->post("Planning_Board");
            $dataPlanningBoardColumns = $this->input->post("Planning_Board_Column");
            $dataPlanningBoardColumnOptions = $this->input->post("Planning_Board_Column_Option");
            $this->planning_board->fetch($id);
            $this->planning_board->set_fields($dataPlanningBoard);
            if ($id) {
                $_boardSaved = $this->planning_board->update();
            } else {
                $_boardSaved = $this->planning_board->insert();
            }
            if ($_boardSaved) {
                $planningBoardId = $this->planning_board->get_field("id");
                $this->planning_board_column_option->delete(["where" => ["planning_board_id", $planningBoardId]]);
                $this->planning_board_column->delete(["where" => ["planning_board_id", $planningBoardId]]);
                foreach ($dataPlanningBoardColumns as $columnIndex => $columnData) {
                    $this->planning_board_column->reset_fields();
                    $this->planning_board_column->set_field("planning_board_id", $planningBoardId);
                    $this->planning_board_column->set_field("columnOrder", $columnIndex);
                    $this->planning_board_column->set_field("name", $columnData["name"]);
                    $this->planning_board_column->set_field("color", $columnData["color"]);
                    if ($this->planning_board_column->insert()) {
                        $planningBoardColumnId = $this->planning_board_column->get_field("id");
                        foreach ($dataPlanningBoardColumnOptions[$columnIndex]["case_status_id"] as $case_status_id) {
                            $this->planning_board_column_option->reset_fields();
                            $this->planning_board_column_option->set_field("planning_board_id", $planningBoardId);
                            $this->planning_board_column_option->set_field("planning_board_column_id", $planningBoardColumnId);
                            $this->planning_board_column_option->set_field("case_status_id", $case_status_id);
                            $this->planning_board_column_option->insert();
                        }
                    } else {
                        $this->db->where("id", $this->planning_board->get_field("id"))->delete("planning_boards");
                        $result = false;
                    }
                }
                redirect("dashboard/cases/");
            } else {
                $result = false;
            }
            if ($result) {
                $id = $id ? $id : $this->planning_board->get_field("id");
                $this->set_flashmessage("success", sprintf($this->lang->line("save_record_successfull"), $this->lang->line("planning_board_data")));
                redirect("dashboard/cases/" . $id);
            } else {
                if ($this->planning_board->is_valid()) {
                    $this->set_flashmessage("error", sprintf($this->lang->line("save_record_failed"), $this->lang->line("case_board")));
                    redirect("dashboard");
                }
            }
        } else {
            $this->planning_board->fetch($id);
            $planningBoard = $this->planning_board->get_fields();
            $planningBoardId = $planningBoard["id"];
            $planningBoardName = $planningBoard["name"];
            $planningBoardColumns = $this->planning_board_column->load_all(["where" => ["planning_board_id", $id], "order_by" => ["columnOrder", "asc"]]);
            $caseStatuses = $this->planning_board_column->load_all_options($id);
            $planningBoardColumnOptions = [];
            foreach ($caseStatuses as $caseStatuse) {
                $planningBoardColumnOptions[$caseStatuse["planning_board_column_id"]] = explode("|", $caseStatuse["case_status_id"]);
            }
            unset($caseStatuses);
            unset($caseStatuse);
        }
        $this->load->model("workflow_status", "workflow_statusfactory");
        $this->workflow_status = $this->workflow_statusfactory->get_instance();
        $_POST = compact("planningBoardId", "planningBoardName", "planningBoardColumns", "planningBoardColumnOptions");
        $_POST["minNbOfColumns"] = $this->planning_board_column->get("minNbOfColumns");
        $_POST["maxNbOfColumns"] = $this->planning_board_column->get("maxNbOfColumns");
        $_POST["caseStatuses"] = $this->workflow_status->loadStatusesUniqueList();
        $data = $_POST;
        $data["board_id"] = $id;
        $data["operators"]["list"] = $this->get_filter_operators("list");
        $data["operators"]["contain"] = $this->get_filter_operators("text");
        $data["fields_data"] = $this->board_post_filters->fields_details;
        $data["fields_details"] = $this->board_post_filters->load_fields();
        $this->load->model("workflow_status", "workflow_statusfactory");
        $this->workflow_status = $this->workflow_statusfactory->get_instance();
        $this->includes("jquery/css/chosen", "css");
        $this->includes("jquery/chosen.min", "js");
        $this->includes("jquery/spectrum", "js");
        $this->includes("jquery/form2js", "js");
        $this->includes("scripts/case_boards", "js");
        $this->includes("kendoui/js/kendo.web.min", "js");
        $this->includes("kendoui/styles/kendo.common-bootstrap.min", "css");
        $this->includes("kendoui/styles/kendo.bootstrap.min", "css");
        $this->includes("scripts/case_board_config", "js");
        $this->load->view("partial/header");
        $this->load->view("dashboards/case_board_config", $data);
        $this->load->view("partial/footer");
    }
    public function task_board_config($id = "0")
    {
        $this->pageTitle = sprintf($this->lang->line("sheria360_browser_title"), $this->lang->line("task_board"));
        $this->load->model(["task_board", "task_board_column_option"]);
        $this->load->model("task_board_column", "task_board_columnfactory");
        $this->task_board_column = $this->task_board_columnfactory->get_instance();
        $this->load->model("board_task_post_filters", "board_task_post_filtersfactory");
        $this->board_task_post_filters = $this->board_task_post_filtersfactory->get_instance();
        $taskBoardId = "";
        $taskBoardName = "";
        $taskBoardColumns = [];
        $taskBoardColumnOptions = [];
        if ($this->input->post(NULL)) {
            $dataTaskBoard = $this->input->post("Task_Board");
            $dataTaskBoardColumns = $this->input->post("Task_Board_Column");
            $dataTaskBoardColumnOptions = $this->input->post("Task_Board_Column_Option");
            $this->task_board->fetch($id);
            $this->task_board->set_fields($dataTaskBoard);
            if ($id) {
                $_boardSaved = $this->task_board->update();
            } else {
                $_boardSaved = $this->task_board->insert();
            }
            if ($_boardSaved) {
                $taskBoardId = $this->task_board->get_field("id");
                $this->task_board_column_option->delete(["where" => ["task_board_id", $taskBoardId]]);
                $this->task_board_column->delete(["where" => ["task_board_id", $taskBoardId]]);
                foreach ($dataTaskBoardColumns as $columnIndex => $columnData) {
                    $this->task_board_column->reset_fields();
                    $this->task_board_column->set_field("task_board_id", $taskBoardId);
                    $this->task_board_column->set_field("columnOrder", $columnIndex);
                    $this->task_board_column->set_field("name", $columnData["name"]);
                    $this->task_board_column->set_field("color", $columnData["color"]);
                    if ($this->task_board_column->insert()) {
                        $taskBoardColumnId = $this->task_board_column->get_field("id");
                        foreach ($dataTaskBoardColumnOptions[$columnIndex]["task_status_id"] as $task_status_id) {
                            $this->task_board_column_option->reset_fields();
                            $this->task_board_column_option->set_field("task_board_id", $taskBoardId);
                            $this->task_board_column_option->set_field("task_board_column_id", $taskBoardColumnId);
                            $this->task_board_column_option->set_field("task_status_id", $task_status_id);
                            $this->task_board_column_option->insert();
                        }
                    } else {
                        $this->db->where("id", $this->task_board->get_field("id"))->delete("task_boards");
                        $result = false;
                    }
                }
                redirect("dashboard/tasks/" . $taskBoardId);
            } else {
                $result = false;
            }
            if ($result) {
                $id = $id ? $id : $this->task_board->get_field("id");
                $this->set_flashmessage("success", sprintf($this->lang->line("save_record_successfull"), $this->lang->line("task_board_data")));
                redirect("dashboard/tasks/" . $id);
            } else {
                if ($this->task_board->is_valid()) {
                    $this->set_flashmessage("error", sprintf($this->lang->line("save_record_failed"), $this->lang->line("task_board")));
                    redirect("dashboard");
                }
            }
        } else {
            $this->task_board->fetch($id);
            $taskBoard = $this->task_board->get_fields();
            $taskBoardId = $taskBoard["id"];
            $taskBoardName = $taskBoard["name"];
            $taskBoardColumns = $this->task_board_column->load_all(["where" => ["task_board_id", $id], "order_by" => ["columnOrder", "asc"]]);
            $taskStatuses = $this->task_board_column->load_all_options($id);
            $taskBoardColumnOptions = [];
            foreach ($taskStatuses as $taskStatuse) {
                $taskBoardColumnOptions[$taskStatuse["task_board_column_id"]] = explode("|", $taskStatuse["task_status_id"]);
            }
            unset($taskStatuses);
            unset($taskStatuse);
        }
        $this->load->model("task_status");
        $_POST = compact("taskBoardId", "taskBoardName", "taskBoardColumns", "taskBoardColumnOptions");
        $_POST["minNbOfColumns"] = $this->task_board_column->get("minNbOfColumns");
        $_POST["maxNbOfColumns"] = $this->task_board_column->get("maxNbOfColumns");
        $_POST["taskStatuses"] = $this->task_status->load_list([], ["firstLine" => ["" => ""]]);
        $data = $_POST;
        $data["board_id"] = $id;
        $data["operators"]["list"] = $this->get_filter_operators("list");
        $data["operators"]["contain"] = $this->get_filter_operators("text");
        $data["fields_data"] = $this->board_task_post_filters->fields_details;
        $data["fields_details"] = $this->board_task_post_filters->load_fields();
        $this->includes("jquery/css/chosen", "css");
        $this->includes("jquery/chosen.min", "js");
        $this->includes("jquery/spectrum", "js");
        $this->includes("jquery/form2js", "js");
        $this->includes("scripts/task_boards", "js");
        $this->includes("kendoui/js/kendo.web.min", "js");
        $this->includes("kendoui/styles/kendo.common-bootstrap.min", "css");
        $this->includes("kendoui/styles/kendo.bootstrap.min", "css");
        $this->includes("scripts/task_board_config", "js");
        $this->load->view("partial/header");
        $this->load->view("dashboards/task_board_config", $data);
        $this->load->view("partial/footer");
    }
    public function case_board_save_search_filters()
    {
        if ($this->input->is_ajax_request()) {
            $this->load->model("planning_board_saved_filter", "planning_board_filter");
            $response = $this->planning_board_filter->save_filters();
            $this->output->set_content_type("application/json");
            $this->output->set_output(json_encode($response));
        }
    }
    public function case_board_delete_saved_reports($boardId, $deletiontype = "all", $filterId = "")
    {
        $this->load->model("planning_board_saved_filter", "planning_board_filter");
        $response = $deletiontype == "all" ? $this->planning_board_filter->delete_all_user_filters($boardId) : $this->planning_board_filter->delete_single_filter($filterId);
        $success_message = $deletiontype == "all" ? "delete_all_filters_success" : "delete_selected_filter_success";
        if ($response) {
            $this->set_flashmessage("success", $this->lang->line($success_message));
        } else {
            $this->set_flashmessage("error", $this->lang->line("failed"));
        }
        redirect("dashboard/cases/" . $boardId);
    }
    private function widget_main_dashboard_data()
    {
        $this->load->model("user_preference");
        if ($this->input->is_ajax_request()) {
            $response = [];
            $response["dashboard_data"] = $this->user_preference->get_value("dashboard");
            return $response;
        }
        show_404();
    }
    private function widget_edit_top_cases_settings()
    {
        $this->load->model("workflow_status", "workflow_statusfactory");
        $this->workflow_status = $this->workflow_statusfactory->get_instance();
        $data = [];
        $data["column"] = $this->uri->rsegment(3);
        $data["row"] = $this->uri->rsegment(4);
        $dashboard_data = $this->user_preference->get_value("dashboard");
        $data["caseStatuses"] = $dashboard_data[$data["column"]][$data["row"]]["settings"]["status"];
        $response["html"] = $this->load->view("dashboards/top_cases_by_dueDate_edit", $data, true);
        return $response;
    }
    private function widget_caseStatuses_autocomplete()
    {
        if (!$this->is_auth->is_logged_in()) {
            redirect("users/login");
        }
        $this->load->model("workflow_status", "workflow_statusfactory");
        $this->workflow_status = $this->workflow_statusfactory->get_instance();
        $term = $this->uri->rsegment(3);
        $configList = ["key" => "id", "value" => "name"];
        $configQury = ["where" => [["(name LIKE " . $this->db->escape("%" . $term . "%") . ")"]]];
        $response = $this->Case_Status->load_all($configQury, $configList);
        return $response;
    }
    public function widget_save_dashboard()
    {
        $response = [];
        $old_param_1 = $this->input->post("old_param_1");
        $new_param_1 = $this->input->post("new_param_1");
        $old_param_2 = $this->input->post("old_param_2");
        $new_param_2 = $this->input->post("new_param_2");
        if (empty($new_param_1) || empty($new_param_2)) {
            $response["success"] = false;
        } else {
            $dashboard_data = $this->user_preference->get_value("dashboard");
            $i = 0;
            $newDashboard = [];
            foreach ($new_param_1 as $v) {
                $newDashboard[$v["column"]][$v["row"]] = ["widget" => $dashboard_data[$old_param_1[$i]["column"]][$old_param_1[$i]["row"]]["widget"], "settings" => $dashboard_data[$old_param_1[$i]["column"]][$old_param_1[$i]["row"]]["settings"]];
                $newDashboard[$v["column"]][$v["row"]]["settings"]["collapse"] = $v["collapse"];
                $i++;
            }
            unset($v);
            $i = 0;
            foreach ($new_param_2 as $v) {
                $newDashboard[$v["column"]][$v["row"]] = ["widget" => $dashboard_data[$old_param_2[$i]["column"]][$old_param_2[$i]["row"]]["widget"], "settings" => $dashboard_data[$old_param_2[$i]["column"]][$old_param_2[$i]["row"]]["settings"]];
                $newDashboard[$v["column"]][$v["row"]]["settings"]["collapse"] = $v["collapse"];
                $i++;
            }
            $response["success"] = $this->user_preference->set_value("dashboard", $newDashboard, true);
        }
        return $response;
    }
    public function tasks_result($taskBoardId = "")
    {
        if ($this->input->post(NULL)) {
            $task_id = $this->input->post("taskId");
            $taskStatusId = $this->input->post("newStatus");
            $response = ["result" => false, "display_message" => $this->lang->line("updates_failed")];
            if (!empty($task_id) && !empty($taskStatusId)) {
                $response = ["result" => true, "display_message" => ""];
                $this->load->model("task", "taskfactory");
                $this->task = $this->taskfactory->get_instance();
                $this->load->model("task_fields", "task_fieldsfactory");
                $this->task_fields = $this->task_fieldsfactory->get_instance();
                $this->load->model("task_workflow_status_transition", "task_workflow_status_transitionfactory");
                $this->task_workflow_status_transition = $this->task_workflow_status_transitionfactory->get_instance();
                $this->task->fetch($task_id);
                $old_status = $this->task->get_field("task_status_id");
                if ($this->input->post("action") == "return_screen") {
                    if ($this->task_workflow_status_transition->check_transition_allowed($task_id, $taskStatusId, $this->is_auth->get_user_id())) {
                        $workflow_applicable = 0 < $this->task->get_field("workflow") ? $this->task->get_field("workflow") : 1;
                        $this->task_workflow_status_transition->fetch(["workflow_id" => $workflow_applicable, "from_step" => $old_status, "to_step" => $taskStatusId]);
                        $transition = $this->task_workflow_status_transition->get_field("id");
                        $data = $this->task_fields->return_screen_fields($task_id, $transition);
                        if ($data) {
                            $data["title"] = $this->task_workflow_status_transition->get_field("name");
                            $response["transition_id"] = $transition;
                            $response["screen_html"] = $this->load->view("templates/screen_fields", $data, true);
                        } else {
                            if (!$this->update_task_status($task_id, $taskStatusId, $old_status)) {
                                $response["result"] = false;
                                $response["display_message"] = $this->lang->line("move_status_invalid");
                            }
                        }
                    } else {
                        $response["result"] = false;
                        $response["display_message"] = $this->lang->line("transition_not_allowed");
                    }
                } else {
                    $validation = $this->task_fields->validate_fields($this->input->post("transition"));
                    $response["result"] = $validation["result"];
                    if (!$validation["result"]) {
                        $response["validation_errors"] = $validation["errors"];
                    } else {
                        if ($this->update_task_status($task_id, $taskStatusId, $old_status)) {
                            if (!$this->task_fields->save_fields($task_id)) {
                                $response["result"] = false;
                                $response["display_message"] = $this->lang->line("records_not_saved");
                            }
                        } else {
                            $response["result"] = false;
                            $response["display_message"] = $this->lang->line("move_status_invalid");
                        }
                    }
                }
                $this->load->model("task_board_column", "task_board_columnfactory");
                $this->task_board_column = $this->task_board_columnfactory->get_instance();
                $data["task_board_id"] = $taskBoardId;
                if ($this->input->post(NULL) && !empty($task_board_id)) {
                    $this->load->model("task_board_saved_filter", "task_board_filter");
                    $data["saved_filters"] = $this->task_board_filter->get_saved_filters($task_board_id);
                }
                $saved_filters = [];
                if ($this->input->post(NULL) && !empty($filter_id) && $this->task_board_filter->fetch((int) $filter_id)) {
                    $grid_board_filter = $this->task_board_filter->get_fields();
                    $saved_grid_board_filter = unserialize($grid_board_filter["keyValue"]);
                    $data["saved_grid_board_filter"] = unserialize($grid_board_filter["keyValue"]);
                    $saved_filters = $this->task_board_filter->convert_matter_filter($saved_grid_board_filter);
                    $data["saved_grid_details"] = $saved_filters["gridFilters"];
                }
                $response["taskBoardColumnOptions"] = $this->task_board_column->get_task_board_column_options_data($taskBoardId, [], $saved_filters, $this->input->post("quickFilter"));
                $result = [];
                if (!empty($response["taskBoardColumnOptions"]["tasks"])) {
                    foreach ($response["taskBoardColumnOptions"]["tasks"] as $key => $value) {
                        $result[$key] = sizeof($value);
                    }
                }
                $this->load->model("task_status");
                $this->task_status->fetch($taskStatusId);
                $response["newStatusName"] = $this->task_status->get_field("name");
                $response["taskId"] = $task_id;
                $response["data"] = $result;
            }
            $this->output->set_content_type("application/json")->set_output(json_encode($response));
        }
    }
    private function update_task_status($task_id = 0, $status_id = 0, $old_status = 0)
    {
        $this->task->fetch($task_id);
        $this->task->set_field("task_status_id", $status_id);
        $this->task->set_field("estimated_effort", $this->task->get_field("estimated_effort") * 1);
        if (!$this->task->update()) {
            return false;
        }
        if ($this->task->get_field("stage")) {
            $this->load->model("legal_case_litigation_detail", "legal_case_litigation_detailfactory");
            $this->legal_case_litigation_detail = $this->legal_case_litigation_detailfactory->get_instance();
            $this->legal_case_litigation_detail->update_stage_order($this->task->get_field("stage"));
        }
        $this->load->model("task_workflow_status_transition_history");
        $this->task_workflow_status_transition_history->log_transition_history($task_id, $old_status, $status_id, $this->is_auth->get_user_id());
        $this->load->model("task_status");
        $this->load->model("task_contributor");
        $contributors = $this->task_contributor->load_all(["where" => ["task_id", $task_id]]);
        $data["contributors"] = array_column($contributors, "user_id");
        $this->task_status->fetch($old_status);
        $old_status_name = $this->task_status->get_field("name");
        $this->task_status->reset_fields();
        $this->task_status->fetch($status_id);
        $new_status_name = $this->task_status->get_field("name");
        $data["task_data"] = ["old_status" => $old_status_name, "new_status" => $new_status_name, "on" => $this->task->get_field("modifiedOn")];
        $this->send_notification($task_id, "edit_task_status", $data);
        return true;
    }
    private function send_notification($id, $object, $data)
    {
        $this->load->model("email_notification_scheme");
        $this->load->library("email_notifications");
        $model = $this->task->get("_table");
        $model_data["id"] = $id;
        $model_data["contributors_ids"] = $data["contributors"] ? $data["contributors"] : [];
        $objectType = $object;
        $notifications_emails = $this->email_notification_scheme->get_emails($objectType, $model, $model_data);
        extract($notifications_emails);
        $notificationsData["to"] = $to_emails;
        $notificationsData["cc"] = $cc_emails;
        $notificationsData["object_id"] = (int) $id;
        $notificationsData["fromLoggedUser"] = $this->is_auth->get_fullname();
        $notificationsData["object"] = $objectType;
        $this->task->fetch($id);
        $data["task_data"]["task_id"] = $id;
        $data["task_data"]["assignee"] = $this->email_notification_scheme->get_user_full_name($this->task->get_field("assigned_to"));
        $data["task_data"]["created_by"] = $notificationsData["fromLoggedUser"];
        $this->load->model("task_types_language");
        $this->load->model("language");
        $langId = $this->language->get_id_by_session_lang();
        $this->task_types_language->fetch(["task_type_id" => $this->task->get_field("task_type_id"), "language_id" => $langId]);
        $taskType = $this->task_types_language->get_field("name");
        $data["task_data"]["priority"] = $this->task->get_field("priority");
        $data["task_data"]["dueDate"] = $this->task->get_field("due_date");
        $data["task_data"]["taskDescription"] = nl2br($this->task->get_field("description"));
        $data["task_data"]["taskType"] = $taskType;
        $notificationsData["taskData"] = $data["task_data"];
        $this->email_notifications->notify($notificationsData);
    }
    public function tasks($task_board_id = "")
    {
        $this->load->model("grid_saved_board_task_filters_users");
        $task_board_id = !empty($task_board_id) ? $task_board_id : $this->input->post("task_board_id");
        $filter_id = $this->input->post("filter_id");
        if (!empty($task_board_id)) {
            $this->grid_saved_board_task_filters_users->set_default_filter($task_board_id, $this->is_auth->get_user_id(), $filter_id);
        }
        $task_board_id = $this->get_task_board_id($task_board_id, $filter_id);
        if ($this->input->is_ajax_request()) {
            $response["status"] = false;
            $action = $this->input->post("action");
            $quick_filter = $this->input->post("quickFilter");
            switch ($action) {
                case "load_columns": 
                    $data = $this->get_task_columns_data($task_board_id, $filter_id, $quick_filter, "false"); ;
                    $response["board_options_columns"] = $data["board_options"]["columns"];
                    $response["board_options_cases"] = $data["board_options"]["tasks"];
                    $response["status"] = true;
                    $response["filter_id"] = $filter_id;
                    if (isset($data["saved_grid_board_filter"])) {
                        $response["saved_filters"] = $data["saved_grid_board_filter"];
                    }
                    if (isset($data["saved_grid_details"])) {
                        $response["saved_grid_details"] = $data["saved_grid_details"];
                    }
                    $response["html"] = $this->load->view("dashboards/tasks_board", $data, true);
                    break;
                case "filter":
                    $data = $this->get_task_filter_data($task_board_id);
                    $response["status"] = true;
                    $response["filter_id"] = $filter_id;
                    $response["html"] = $this->load->view("dashboards/board_task_filter_form", $data, true);
                    break;
                default:
            }
                    $this->output->set_content_type("application/json")->set_output(json_encode($response));

        } else {
            $this->load->model("task_board_column", "task_board_columnfactory");
            $this->task_board_column = $this->task_board_columnfactory->get_instance();
            $data = $this->get_task_filter_data($task_board_id);
            $data["filter_id"] = $filter_id;
            $data["task_board_id"] = $task_board_id;
            $this->includes("jquery/css/chosen", "css");
            $this->includes("jquery/chosen.min", "js");
            $this->includes("scripts/task_boards", "js");
            $this->includes("scripts/boards", "js");
            $this->includes("dragula/dragula.min", "css");
            $this->includes("jquery/form2js", "js");
            $this->includes("styles/ltr/fixes", "css");
            $this->includes_footer("dragula/dragula.min", "js");
            $this->includes_footer("dragula/dom-autoscroller.min", "js");
            $this->load->view("partial/header");
            $this->load->view("dashboards/tasks", $data);
            $this->load->view("partial/footer");
        }
    }
    public function task_boards()
    {
        $this->pageTitle = sprintf($this->lang->line("sheria360_browser_title"), $this->lang->line("task_board"));
        $this->load->model("task_board");
        $this->load->model("task_board_column", "task_board_columnfactory");
        $this->load->model("task_board_saved_filter", "task_board_filter");
        $this->task_board_column = $this->task_board_columnfactory->get_instance();
        if ($this->input->is_ajax_request()) {
            $response = [];
            $taskBoardId = $this->input->post("id");
            $this->load->model("task_board_column_option");
            $userId = $this->session->userdata("AUTH_user_id");
            if ($this->task_board_column_option->delete(["where" => ["task_board_id", $taskBoardId]])) {
                $this->task_board_column->delete(["where" => ["task_board_id", $taskBoardId]]);
                $this->task_board_filter->delete(["where" => ["boardId", $taskBoardId]]);
                $this->task_board->delete(["where" => ["id", $taskBoardId]]);
                delete_cookie($this->is_auth->get_user_id() . "task_board_id");
                $response["status"] = 500;
            } else {
                if ($this->task_board_filter->delete(["where" => ["boardId", $taskBoardId]])) {
                    $this->task_board->delete(["where" => ["id", $taskBoardId]]);
                    delete_cookie($this->is_auth->get_user_id() . "task_board_id");
                    $response["status"] = 500;
                } else {
                    if ($this->task_board->delete(["where" => ["id", $taskBoardId]])) {
                        delete_cookie($this->is_auth->get_user_id() . "task_board_id");
                        $response["status"] = 500;
                    } else {
                        $response["status"] = 102;
                    }
                }
            }
            $this->output->set_content_type("application/json");
            $this->output->set_output(json_encode($response));
        } else {
            $data = [];
            $data["adminTaskBoard"] = $this->task_board_column->get_task_Board_columns();
            $this->includes("scripts/task_boards", "js");
            $this->includes("scripts/boards", "js");
            $this->load->view("partial/header");
            $this->load->view("dashboards/task_boards", $data);
            $this->load->view("partial/footer");
        }
    }
    public function task_board_save_search_filters()
    {
        if ($this->input->is_ajax_request()) {
            $this->load->model("task_board_saved_filter", "task_board_filter");
            $response = $this->task_board_filter->save_filters();
            $this->output->set_content_type("application/json");
            $this->output->set_output(json_encode($response));
        }
    }
    public function task_board_delete_saved_reports()
    {
        $post_data = $this->input->post(NULL);
        $this->load->model("task_board_saved_filter", "task_board_filter");
        $result = $post_data["single"] == "false" ? $this->task_board_filter->delete_all_user_filters($post_data["board_id"]) : $this->task_board_filter->delete_single_filter($post_data["filter_id"]);
        $success_message = !$post_data["single"] ? $this->lang->line("delete_all_filters_success") : $this->lang->line("delete_selected_filter_success");
        $response["message"] = $result ? $success_message : $this->lang->line("failed");
        $response["status"] = $result;
        $this->output->set_output(json_encode($response));
    }
    public function board_task_delete_post_filter($filter_post_id)
    {
        $this->load->model("board_task_post_filters", "board_task_post_filtersfactory");
        $this->board_task_post_filters = $this->board_task_post_filtersfactory->get_instance();
        $response = [];
        if (!empty($filter_post_id)) {
            $response["status"] = false;
            if ($this->board_task_post_filters->board_delete_post_filter($filter_post_id)) {
                $response["status"] = true;
            }
            $this->output->set_content_type("application/json")->set_output(json_encode($response));
        }
    }
    public function board_save_post_filter()
    {
        $this->load->model("board_post_filters", "board_post_filtersfactory");
        $this->board_post_filters = $this->board_post_filtersfactory->get_instance();
        $this->load->model("board_post_filters_user", "board_post_filters_userfactory");
        $this->board_post_filters_user = $this->board_post_filters_userfactory->get_instance();
        $post_data = $this->input->post(NULL);
        if (empty($post_data["post-filter-board-id"])) {
            $this->board_post_filters->set_fields($post_data);
            if ($this->board_post_filters->insert()) {
                $response["status"] = true;
                $response["message"] = sprintf($this->lang->line("record_added_successfull"), $this->lang->line("post_filter"));
            } else {
                $response["status"] = false;
                $response["validationErrors"] = $this->board_post_filters->get("validationErrors");
            }
        } else {
            if ($this->input->post("toggleFilter")) {
                $response["status"] = false;
                if ($post_data["active"] == 0) {
                    $board_post_filters_user_data = [];
                    $board_post_filters_user_data["user_id"] = $this->is_auth->get_user_id();
                    $board_post_filters_user_data["board_post_filters_id"] = $post_data["post-filter-board-id"];
                    $condition_board = ["board_post_filters_id" => $post_data["post-filter-board-id"], "user_id" => $this->is_auth->get_user_id()];
                    if (!$this->board_post_filters_user->fetch($condition_board)) {
                        $this->board_post_filters_user->set_fields($board_post_filters_user_data);
                        if ($this->board_post_filters_user->insert($board_post_filters_user_data)) {
                            $response["status"] = true;
                        }
                    } else {
                        $response["message"] = $this->lang->line("already_exists");
                    }
                } else {
                    if ($this->board_post_filters_user->delete_filter_per_user($post_data["post-filter-board-id"])) {
                        $response["status"] = true;
                    }
                }
            } else {
                if ($this->board_post_filters->fetch($post_data["post-filter-board-id"])) {
                    $this->board_post_filters->set_fields($post_data);
                    if ($this->board_post_filters->update()) {
                        $response["status"] = true;
                        $response["message"] = sprintf($this->lang->line("record_save_successfull"), $this->lang->line("post_filter"));
                    } else {
                        $response["status"] = false;
                        $response["validationErrors"] = $this->board_post_filters->get("validationErrors");
                    }
                } else {
                    $response["status"] = false;
                    $response["message"] = $this->lang->line("invalid_record");
                }
            }
        }
        $this->output->set_content_type("application/json")->set_output(json_encode($response));
    }
    public function board_save_task_post_filter()
    {
        $this->load->model("board_task_post_filters", "board_task_post_filtersfactory");
        $this->board_task_post_filters = $this->board_task_post_filtersfactory->get_instance();
        $this->load->model("board_task_post_filters_user", "board_task_post_filters_userfactory");
        $this->board_task_post_filters_user = $this->board_task_post_filters_userfactory->get_instance();
        $post_data = $this->input->post(NULL);
        if (empty($post_data["post-filter-board-id"])) {
            $this->board_task_post_filters->set_fields($post_data);
            if ($this->board_task_post_filters->insert()) {
                $response["status"] = true;
                $response["message"] = sprintf($this->lang->line("record_added_successfull"), $this->lang->line("post_filter"));
            } else {
                $response["status"] = false;
                $response["validationErrors"] = $this->board_task_post_filters->get("validationErrors");
            }
        } else {
            if ($this->input->post("toggleFilter")) {
                $response["status"] = false;
                if ($post_data["active"] == 0) {
                    $board_task_post_filters_user_data = [];
                    $board_task_post_filters_user_data["user_id"] = $this->is_auth->get_user_id();
                    $board_task_post_filters_user_data["board_post_filters_id"] = $post_data["post-filter-board-id"];
                    $condition_board = ["board_post_filters_id" => $post_data["post-filter-board-id"], "user_id" => $this->is_auth->get_user_id()];
                    if (!$this->board_task_post_filters_user->fetch($condition_board)) {
                        $this->board_task_post_filters_user->set_fields($board_task_post_filters_user_data);
                        if ($this->board_task_post_filters_user->insert($board_task_post_filters_user_data)) {
                            $response["status"] = true;
                        }
                    } else {
                        $response["message"] = $this->lang->line("already_exists");
                    }
                } else {
                    if ($this->board_task_post_filters_user->delete_filter_per_user($post_data["post-filter-board-id"])) {
                        $response["status"] = true;
                    }
                }
            } else {
                if ($this->board_task_post_filters->fetch($post_data["post-filter-board-id"])) {
                    $this->board_task_post_filters->set_fields($post_data);
                    if ($this->board_task_post_filters->update()) {
                        $response["status"] = true;
                        $response["message"] = sprintf($this->lang->line("record_save_successfull"), $this->lang->line("post_filter"));
                    } else {
                        $response["status"] = false;
                        $response["validationErrors"] = $this->board_task_post_filters->get("validationErrors");
                    }
                } else {
                    $response["status"] = false;
                    $response["message"] = $this->lang->line("invalid_record");
                }
            }
        }
        $this->output->set_content_type("application/json")->set_output(json_encode($response));
    }
    public function board_delete_post_filter($filter_post_id = "")
    {
        $this->load->model("board_post_filters", "board_post_filtersfactory");
        $this->board_post_filters = $this->board_post_filtersfactory->get_instance();
        $response = [];
        if (!empty($filter_post_id)) {
            $response["status"] = false;
            if ($this->board_post_filters->board_delete_post_filter($filter_post_id)) {
                $response["status"] = true;
            }
            $this->output->set_content_type("application/json")->set_output(json_encode($response));
        }
    }
    public function archiving($board_id = false)
    {
        $data["board_id"] = $board_id;
        $data["title"] = $this->lang->line("archiveHideMatter");
        if (!empty($board_id)) {
            $this->load->model("workflow_status", "workflow_statusfactory");
            $this->workflow_status = $this->workflow_statusfactory->get_instance();
            $data["statuses_list"] = $this->workflow_status->loadStatusesUniqueList();
            $data["tooltip_hide_borad"] = $this->lang->line("tooltip_hide_borad");
            $data["tooltip_archive_board"] = $this->lang->line("tooltip_archive_board");
            $data["operators"]["date"] = $this->get_filter_operators("date");
            $this->load->model("system_preference");
            $system_preferences = $this->system_preference->get_key_groups();
            $data["archive_case_status"] = isset($system_preferences["DefaultValues"]["archiveCaseStatus"]) && !empty($system_preferences["DefaultValues"]["archiveCaseStatus"]) ? explode(",", $system_preferences["DefaultValues"]["archiveCaseStatus"]) : [];
            $response["status"] = true;
        } else {
            $post_data = $this->input->post();
            $this->load->model("workflow_status", "workflow_statusfactory");
            $this->workflow_status = $this->workflow_statusfactory->get_instance();
            if (isset($post_data["archiveAction"]) && !empty($post_data["archiveAction"])) {
                $archive_case_status_ids = implode(", ", array_values($post_data["formData"]["archiving_status"]));
                $response["result"] = $this->legal_case->archieved_cases_total_number($archive_case_status_ids, $post_data["formData"], true, $post_data["formData"]["archiving_type"] === "hide");
                $response["message"] = $response["result"] ? sprintf($this->lang->line("cases_archived_hide_successfully"), $post_data["formData"]["archiving_type"] === "hide" ? $this->lang->line("hide") : $this->lang->line("archived")) : sprintf($this->lang->line("cases_archived_hide_failed"), $post_data["formData"]["archiving_type"] === "hide" ? $this->lang->line("hide") : $this->lang->line("archived"));
            } else {
                if (empty($post_data["archiving_type"])) {
                    $response["validationErrors"]["archiving_type"] = sprintf($this->lang->line("required_rule"), $this->lang->line("type"));
                }
                if (empty($post_data["archiving_status"])) {
                    $response["validationErrors"]["archiving_status"] = sprintf($this->lang->line("required_rule"), $this->lang->line("status"));
                }
                if (empty($response["validationErrors"])) {
                    $response["status"] = true;
                    $archive_case_status_ids = implode(", ", array_values($post_data["archiving_status"]));
                    $archive_case_status = $this->workflow_status->loadListWorkflowStatuses("", ["where" => [["id IN ( " . $archive_case_status_ids . ")", NULL, false]]]);
                    $archive_case_status_str = implode(", ", array_values($archive_case_status));
                    $affected_Rows = $this->legal_case->archieved_cases_total_number($archive_case_status_ids, $post_data);
                    $response["message"] = sprintf($this->lang->line("feedback_message_archived_object"), $affected_Rows, $this->lang->line("cases"), $archive_case_status_str);
                    $response["affected_Rows"] = $affected_Rows;
                    $response["archive_case_status_messgae"] = sprintf($this->lang->line("confirmation_message_to_archive_cases_tasks_contracts_affected_rows"), $post_data["archiving_type"] === "hide" ? $this->lang->line("hide") : $this->lang->line("archive"), $this->lang->line("cases"), $archive_case_status_str, $affected_Rows);
                } else {
                    $response["status"] = false;
                }
            }
        }
        $response["html"] = $this->load->view("dashboards/archiving", $data, true);
        $this->output->set_content_type("application/json")->set_output(json_encode($response));
    }
    public function archiving_task($board_id = false)
    {
        $data["board_id"] = $board_id;
        $data["title"] = $this->lang->line("archiveHideTask");
        $this->load->model("task_status");
        if (!empty($board_id)) {
            $data["statuses_list"] = $this->task_status->load_list();
            $data["tooltip_hide_borad"] = $this->lang->line("tooltip_hide_borad_task");
            $data["tooltip_archive_board"] = $this->lang->line("tooltip_archive_board_task");
            $data["operators"]["date"] = $this->get_filter_operators("date");
            $this->load->model("system_preference");
            $system_preferences = $this->system_preference->get_key_groups();
            $data["archive_task_status"] = isset($system_preferences["DefaultValues"]["archiveTaskStatus"]) && !empty($system_preferences["DefaultValues"]["archiveTaskStatus"]) ? explode(",", $system_preferences["DefaultValues"]["archiveTaskStatus"]) : [];
            $response["status"] = true;
        } else {
            $post_data = $this->input->post();
            if (isset($post_data["archiveAction"]) && !empty($post_data["archiveAction"])) {
                $archive_task_status_ids = implode(", ", array_values($post_data["formData"]["archiving_status"]));
                $response["result"] = $this->task->archieved_tasks_total_number($archive_task_status_ids, $post_data["formData"], true, $post_data["formData"]["archiving_type"] === "hide");
                $response["message"] = $response["result"] ? sprintf($this->lang->line("tasks_archived_hide_successfully"), $post_data["formData"]["archiving_type"] === "hide" ? $this->lang->line("hide") : $this->lang->line("archived")) : sprintf($this->lang->line("tasks_archived_hide_failed"), $post_data["formData"]["archiving_type"] === "hide" ? $this->lang->line("hide") : $this->lang->line("archived"));
            } else {
                if (empty($post_data["archiving_type"])) {
                    $response["validationErrors"]["archiving_type"] = sprintf($this->lang->line("required_rule"), $this->lang->line("type"));
                }
                if (empty($post_data["archiving_status"])) {
                    $response["validationErrors"]["archiving_status"] = sprintf($this->lang->line("required_rule"), $this->lang->line("status"));
                }
                if (empty($response["validationErrors"])) {
                    $response["status"] = true;
                    $archive_task_status_ids = implode(", ", array_values($post_data["archiving_status"]));
                    $archive_task_status = $this->task_status->load_list(["where" => [["id IN ( " . $archive_task_status_ids . ")", NULL, false]]]);
                    $archive_task_status_str = implode(", ", array_values($archive_task_status));
                    $affected_Rows = $this->task->archieved_tasks_total_number($archive_task_status_ids, $post_data);
                    $response["message"] = sprintf($this->lang->line("feedback_message_archived_object"), $affected_Rows, $this->lang->line("tasks"), $archive_task_status_str);
                    $response["affected_Rows"] = $affected_Rows;
                    $response["archive_task_status_message"] = sprintf($this->lang->line("confirmation_message_to_archive_cases_tasks_contracts_affected_rows"), $post_data["archiving_type"] === "hide" ? $this->lang->line("hide") : $this->lang->line("archive"), $this->lang->line("tasks"), $archive_task_status_str, $affected_Rows);
                } else {
                    $response["status"] = false;
                }
            }
        }
        $response["html"] = $this->load->view("dashboards/archiving_tasks", $data, true);
        $this->output->set_content_type("application/json")->set_output(json_encode($response));
    }
}

?>