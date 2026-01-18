<?php

if (!defined("BASEPATH")) {
    exit("No direct script access allowed");
}
require "Top_controller.php";
class Reports extends Top_controller
{
    public $Report;
    public $Legal_Case;
    public function __construct()
    {
        parent::__construct();
        $this->currentTopNavItem = "reports";
        $this->load->model("legal_case", "legal_casefactory");
    }
    public function index()
    {
        $this->pageTitle = sprintf($this->lang->line("sheria360_browser_title"), $this->lang->line("reports"));
        $systemPreferences = $this->session->userdata("systemPreferences");
        $data["AllowFeatureSLAManagement"] = isset($systemPreferences["AllowFeatureSLAManagement"]) ? $systemPreferences["AllowFeatureSLAManagement"] == "yes" : false;
        $data["AllowContractSLAManagement"] = isset($systemPreferences["AllowContractSLAManagement"]) ? $systemPreferences["AllowContractSLAManagement"] == "yes" : false;
        $this->load->model("user_report");
        $data["reports"] = $this->get_reports();
        $this->load->model("system_preference");
        $this->load->view("partial/header");
        $this->load->view("reports/index", $data);
        $this->load->view("partial/footer");
    }
    public function export_shares_by_date_to_word($id = 0, $memberId = 0)
    {
        $data = [];
        $this->load->model("company", "companyfactory");
        $this->company = $this->companyfactory->get_instance();
        $systemPreferences = $this->session->userdata("systemPreferences");
        $hijri_calendar_enabled = isset($systemPreferences["hijriCalendarFeature"]) && $systemPreferences["hijriCalendarFeature"] ? $systemPreferences["hijriCalendarFeature"] : 0;
        if (is_numeric($id) && 0 < $id) {
            $this->load->model("company", "companyfactory");
            $this->company = $this->companyfactory->get_instance();
            $companyDataFetched = $this->company->fetch_company_accessibility($id);
            if (!empty($companyDataFetched)) {
                $this->load->model("share_movement", "share_movementfactory");
                $this->share_movement = $this->share_movementfactory->get_instance();
                $data["report_data"] = $this->share_movement->shares_by_date($id, $memberId);
                $data["shareholders"] = $this->share_movement->load_current_shareholders($id, $memberId);
                $data["shareholder"] = $this->share_movement->load_current_shareholders($id);
                $data["companyInfo"] = $this->company->load(["select" => ["id,name,shareParValue"], "where" => ["id", $id]]);
                $data["direction"] = $this->is_auth->is_layout_rtl() ? "rtl" : "ltr";
                $data["hijri_calendar_enabled"] = $hijri_calendar_enabled;
                $corepath = substr(COREPATH, 0, -12);
                require_once $corepath . "/application/libraries/phpdocx-advanced/classes/CreateDocx.php";
                $docx = new createDocx();
                $html = $this->load->view("reports/shares_by_date_to_word", $data, true);
                $docx->embedHTML($html);
                $docx->modifyPageLayout("A4-landscape", []);
                $tempDirectory = $this->config->item("files_path") . DIRECTORY_SEPARATOR . "tmp" . DIRECTORY_SEPARATOR . "cases" . DIRECTORY_SEPARATOR . "usr_" . $this->is_auth->get_user_id();
                if (!is_dir($tempDirectory)) {
                    @mkdir($tempDirectory, 493);
                }
                $file_name = str_replace(" ", "_", sprintf($this->lang->line("company_shares_by_date_export"), $companyDataFetched["name"])) . "_" . date("Ymd");
                $docx->createDocx($tempDirectory . "/" . $file_name);
                $this->load->helper("download");
                $content = file_get_contents($tempDirectory . "/" . $file_name . ".docx");
                unlink($tempDirectory . "/" . $file_name . ".docx");
                $file_nameEncoded = $this->downloaded_file_name_by_browser($file_name . ".docx");
                force_download($file_nameEncoded, $content);
                exit;
            }
        }
    }
    public function shares_by_date($id = 0, $memberId = 0)
    {
        $this->pageTitle = sprintf($this->lang->line("sheria360_browser_title"), $this->lang->line("reports") . " | " . $this->lang->line("company_shares_by_date"));
        $data = [];
        $data["chooseCompany"] = true;
        $this->load->model("company", "companyfactory");
        $this->company = $this->companyfactory->get_instance();
        $data["companies"] = $this->company->load_list_internal_companies_with_shortName();
        $systemPreferences = $this->session->userdata("systemPreferences");
        $hijri_calendar_enabled = isset($systemPreferences["hijriCalendarFeature"]) && $systemPreferences["hijriCalendarFeature"] ? $systemPreferences["hijriCalendarFeature"] : 0;
        if (is_numeric($id) && 0 < $id) {
            $this->load->model("company", "companyfactory");
            $this->company = $this->companyfactory->get_instance();
            $this->fetch_company_accessibility($id);
            $this->load->model("share_movement", "share_movementfactory");
            $this->share_movement = $this->share_movementfactory->get_instance();
            $data["selected_member"] = $memberId;
            $data["report_data"] = $this->share_movement->shares_by_date($id, $memberId);
            $data["shareholders"] = $this->share_movement->load_current_shareholders($id, $memberId);
            $data["shareholder"] = $this->share_movement->load_current_shareholders($id);
            $membersof = [];
            $membersof[0] = $this->lang->line("chooseShareholder");
            foreach ($data["shareholder"]["data"] as $temp) {
                $membersof[$temp["member_id"]] = $temp["shareholderName"];
            }
            $data["membersof"] = $membersof;
            $data["companyInfo"] = $this->company->load(["select" => ["id,name,shareParValue"], "where" => ["id", $id]]);
            $data["chooseCompany"] = false;
            $data["hijri_calendar_enabled"] = $hijri_calendar_enabled;
            $this->includes("scripts/reports/shares_movement", "js");
        }
        $data["hijri_calendar_enabled"] = $hijri_calendar_enabled;
        $this->load->view("partial/header");
        $this->load->view("reports/shares_by_date", $data);
        $this->load->view("partial/footer");
    }
    private function shares_by_date_old($id = 0)
    {
        $data = [];
        $data["chooseCompany"] = true;
        if (is_numeric($id) && 0 < $id) {
            $this->load->model("company", "companyfactory");
            $this->company = $this->companyfactory->get_instance();
            $this->fetch_company_accessibility($id);
            $this->load->model("share_movement", "share_movementfactory");
            $this->share_movement = $this->share_movementfactory->get_instance();
            $data["report_data"] = $this->share_movement->shares_by_date_old($id);
            $data["shareholders"] = $this->share_movement->load_current_shareholders($id);
            $data["companyInfo"] = $this->company->load(["select" => ["name,shareParValue"], "where" => ["id", $id]]);
            $data["chooseCompany"] = false;
        } else {
            $this->load->model("company", "companyfactory");
            $this->company = $this->companyfactory->get_instance();
            $data["companies"] = $this->company->load_list_internal_companies_with_shortName();
        }
        $this->load->view("partial/header");
        $this->load->view("reports/shares_by_date_old", $data);
        $this->load->view("partial/footer");
    }
    public function shares_by_holder_to_word($id = 0, $memberId = 0)
    {
        $data = [];
        $this->load->model("company", "companyfactory");
        $this->company = $this->companyfactory->get_instance();
        $systemPreferences = $this->session->userdata("systemPreferences");
        $hijri_calendar_enabled = isset($systemPreferences["hijriCalendarFeature"]) && $systemPreferences["hijriCalendarFeature"] ? $systemPreferences["hijriCalendarFeature"] : 0;
        if (is_numeric($id) && 0 < $id) {
            $this->load->model("company", "companyfactory");
            $this->company = $this->companyfactory->get_instance();
            $companyDataFetched = $this->company->fetch_company_accessibility($id);
            if (!empty($companyDataFetched)) {
                $this->load->model("share_movement", "share_movementfactory");
                $this->share_movement = $this->share_movementfactory->get_instance();
                $data["report_data"] = $this->share_movement->shares_by_holder($id, $memberId);
                $data["shareholder"] = $this->share_movement->load_current_shareholders($id);
                $data["shareholders"] = $this->share_movement->load_current_shareholders($id, $memberId);
                $data["companyInfo"] = $this->company->load(["select" => ["id,name,shareParValue"], "where" => ["id", $id]]);
                $data["direction"] = $this->is_auth->is_layout_rtl() ? "rtl" : "ltr";
                $corepath = substr(COREPATH, 0, -12);
                require_once $corepath . "/application/libraries/phpdocx-advanced/classes/CreateDocx.php";
                $docx = new createDocx();
                $data["hijri_calendar_enabled"] = $hijri_calendar_enabled;
                $html = $this->load->view("reports/shares_by_holder_to_word", $data, true);
                $docx->embedHTML($html);
                $docx->modifyPageLayout("A4-landscape", []);
                $tempDirectory = $this->config->item("files_path") . DIRECTORY_SEPARATOR . "tmp" . DIRECTORY_SEPARATOR . "cases" . DIRECTORY_SEPARATOR . "usr_" . $this->is_auth->get_user_id();
                if (!is_dir($tempDirectory)) {
                    @mkdir($tempDirectory, 493);
                }
                $file_name = str_replace(" ", "_", sprintf($this->lang->line("company_shares_by_shareholder_export"), $companyDataFetched["name"])) . "_" . date("Ymd");
                $docx->createDocx($tempDirectory . "/" . $file_name);
                $this->load->helper("download");
                $content = file_get_contents($tempDirectory . "/" . $file_name . ".docx");
                unlink($tempDirectory . "/" . $file_name . ".docx");
                $file_nameEncoded = $this->downloaded_file_name_by_browser($file_name . ".docx");
                force_download($file_nameEncoded, $content);
                exit;
            }
        }
    }
    public function shares_by_holder($id = 0, $memberId = 0)
    {
        $this->pageTitle = sprintf($this->lang->line("sheria360_browser_title"), $this->lang->line("reports") . " | " . $this->lang->line("company_shares_by_shareholder"));
        $data = [];
        $data["chooseCompany"] = true;
        $this->load->model("company", "companyfactory");
        $this->company = $this->companyfactory->get_instance();
        $data["companies"] = $this->company->load_list_internal_companies_with_shortName();
        $systemPreferences = $this->session->userdata("systemPreferences");
        $hijri_calendar_enabled = isset($systemPreferences["hijriCalendarFeature"]) && $systemPreferences["hijriCalendarFeature"] ? $systemPreferences["hijriCalendarFeature"] : 0;
        $data["hijri_calendar_enabled"] = $hijri_calendar_enabled;
        if (is_numeric($id) && 0 < $id) {
            $this->load->model("company", "companyfactory");
            $this->company = $this->companyfactory->get_instance();
            $this->fetch_company_accessibility($id);
            $this->load->model("share_movement", "share_movementfactory");
            $this->share_movement = $this->share_movementfactory->get_instance();
            $data["selected_member"] = $memberId;
            $data["report_data"] = $this->share_movement->shares_by_holder($id, $memberId);
            $data["shareholder"] = $this->share_movement->load_current_shareholders($id);
            $data["shareholders"] = $this->share_movement->load_current_shareholders($id, $memberId);
            $membersof = [];
            $membersof[0] = $this->lang->line("chooseShareholder");
            foreach ($data["shareholder"]["data"] as $temp) {
                $membersof[$temp["member_id"]] = $temp["shareholderName"];
            }
            $data["membersof"] = $membersof;
            $data["companyInfo"] = $this->company->load(["select" => ["id,name,shareParValue"], "where" => ["id", $id]]);
            $data["chooseCompany"] = false;
        }
        $this->load->view("partial/header");
        $this->load->view("reports/shares_by_holder", $data);
        $this->load->view("partial/footer");
    }
    public function shareholder_votes_to_word($id = 0)
    {
        $data = [];
        $this->load->model("company", "companyfactory");
        $this->company = $this->companyfactory->get_instance();
        if (is_numeric($id) && 0 < $id) {
            $systemPreferences = $this->session->userdata("systemPreferences");
            $this->load->model("company", "companyfactory");
            $this->company = $this->companyfactory->get_instance();
            $companyDataFetched = $this->company->fetch_company_accessibility($id);
            if (!empty($companyDataFetched)) {
                $this->load->model("share_movement", "share_movementfactory");
                $this->share_movement = $this->share_movementfactory->get_instance();
                $data["sharesTotalPerHolder"] = $this->share_movement->shareholder_votes($id);
                $data["companyInfo"] = $this->company->load(["select" => ["id,name,shareParValue"], "where" => ["id", $id]]);
                $data["multiplyFactor"] = isset($systemPreferences["shareholderVoteFactor"]) && is_numeric($systemPreferences["shareholderVoteFactor"]) ? $systemPreferences["shareholderVoteFactor"] : 2;
                $data["yearInterval"] = isset($systemPreferences["shareholderVoteYear"]) && is_numeric($systemPreferences["shareholderVoteYear"]) ? $systemPreferences["shareholderVoteYear"] : 2;
                $data["shareholders"] = $this->share_movement->load_current_shareholders($id);
                $data["direction"] = $this->is_auth->is_layout_rtl() ? "rtl" : "ltr";
                $system_preferences = $this->session->userdata("systemPreferences");
                $data["hijri_calendar_enabled"] = isset($system_preferences["hijriCalendarFeature"]) && $system_preferences["hijriCalendarFeature"] ? $system_preferences["hijriCalendarFeature"] : 0;
                $corepath = substr(COREPATH, 0, -12);
                require_once $corepath . "/application/libraries/phpdocx-advanced/classes/CreateDocx.php";
                $docx = new createDocx();
                $html = $this->load->view("reports/shareholder_votes_to_word", $data, true);
                $docx->embedHTML($html);
                $docx->modifyPageLayout("A4-landscape", []);
                $tempDirectory = $this->config->item("files_path") . DIRECTORY_SEPARATOR . "tmp" . DIRECTORY_SEPARATOR . "cases" . DIRECTORY_SEPARATOR . "usr_" . $this->is_auth->get_user_id();
                if (!is_dir($tempDirectory)) {
                    @mkdir($tempDirectory, 493);
                }
                $file_name = str_replace(" ", "_", sprintf($this->lang->line("company_shareholder_votes_export"), $companyDataFetched["name"])) . "_" . date("Ymd");
                $docx->createDocx($tempDirectory . "/" . $file_name);
                $this->load->helper("download");
                $content = file_get_contents($tempDirectory . "/" . $file_name . ".docx");
                unlink($tempDirectory . "/" . $file_name . ".docx");
                $file_nameEncoded = $this->downloaded_file_name_by_browser($file_name . ".docx");
                force_download($file_nameEncoded, $content);
                exit;
            }
        }
    }
    public function shareholder_votes($id = 0)
    {
        $this->pageTitle = sprintf($this->lang->line("sheria360_browser_title"), $this->lang->line("reports") . " | " . $this->lang->line("shareholder_votes"));
        $data = [];
        $data["chooseCompany"] = true;
        $this->load->model("company", "companyfactory");
        $this->company = $this->companyfactory->get_instance();
        $data["companies"] = $this->company->load_list_internal_companies_with_shortName();
        if (is_numeric($id) && 0 < $id) {
            $systemPreferences = $this->session->userdata("systemPreferences");
            $this->load->model("company", "companyfactory");
            $this->company = $this->companyfactory->get_instance();
            $this->fetch_company_accessibility($id);
            $this->load->model("share_movement", "share_movementfactory");
            $this->share_movement = $this->share_movementfactory->get_instance();
            $data["sharesTotalPerHolder"] = $this->share_movement->shareholder_votes($id);
            $data["companyInfo"] = $this->company->load(["select" => ["id,name,shareParValue"], "where" => ["id", $id]]);
            $data["multiplyFactor"] = isset($systemPreferences["shareholderVoteFactor"]) && is_numeric($systemPreferences["shareholderVoteFactor"]) ? $systemPreferences["shareholderVoteFactor"] : 2;
            $data["yearInterval"] = isset($systemPreferences["shareholderVoteYear"]) && is_numeric($systemPreferences["shareholderVoteYear"]) ? $systemPreferences["shareholderVoteYear"] : 2;
            $data["chooseCompany"] = false;
            $system_preferences = $this->session->userdata("systemPreferences");
            $data["hijri_calendar_enabled"] = isset($system_preferences["hijriCalendarFeature"]) && $system_preferences["hijriCalendarFeature"] ? $system_preferences["hijriCalendarFeature"] : 0;
            $data["shareholders"] = $this->share_movement->load_current_shareholders($id);
        }
        $this->load->view("partial/header");
        $this->load->view("reports/shareholder_votes", $data);
        $this->load->view("partial/footer");
    }
    public function company_shareholders_tree_view($id = 0)
    {
        $this->pageTitle = sprintf($this->lang->line("sheria360_browser_title"), $this->lang->line("reports") . " | " . $this->lang->line("company_shareholders_tree_view"));
        $data = [];
        $data["memberId"] = "no-class";
        $this->load->model("company", "companyfactory");
        $this->company = $this->companyfactory->get_instance();
        $this->includes("jquery/css/chosen", "css");
        $this->includes("jquery/chosen.min", "js");
        $this->includes("jquery/cookie", "js");
        $this->includes("jquery/treeview", "js");
        if (is_numeric($id) && 0 < $id) {
            $this->fetch_company_accessibility($id);
            $this->company->fetch($id);
            $this->load->model("member");
            $parentCompany["percentage"] = "1";
            $parentCompany["name"] = $this->company->get_field("name");
            $parentCompany["shortName"] = $this->company->get_field("shortName");
            $parentCompany["category"] = $this->company->get_field("category");
            $parentCompany["member_id"] = $this->member->get_member_company($id);
            $parentCompany["id"] = $id;
            $this->company->get_shares_tree_view($tree, $parentCompany);
            $data["tree"] = $tree;
            $data["memberId"] = $parentCompany["member_id"];
        }
        $query["select"] = ["id, name", false];
        $query["where_in"] = [["category", ["Internal", "Group"]]];
        $query["where"][] = ["(companies.private IS NULL OR companies.private = 'no' OR (companies.private = 'yes' AND (companies.createdBy = '" . $this->company->logged_user_id . "' OR companies.id IN (SELECT company_id FROM company_users WHERE user_id = '" . $this->company->logged_user_id . "') OR '" . $this->company->override_privacy . "' = 'yes')))", NULL, false];
        $query["where"][] = ["status", "Active"];
        $query["order_by"] = [["category", "desc"], ["name", "asc"]];
        $config = ["value" => "name", "firstLine" => ["" => $this->lang->line("choose_company")]];
        $data["companies"] = $this->company->load_list($query, $config);
        $data["companyFilter"] = $id;
        $this->load->view("partial/header");
        $this->load->view("reports/company_shareholders_tree_view", $data);
        $this->load->view("partial/footer");
    }
    public function company_related_matters($id = 0)
    {
        $this->pageTitle = sprintf($this->lang->line("sheria360_browser_title"), $this->lang->line("reports") . " | " . $this->lang->line("company_related_matters"));
        $this->load->model("company", "companyfactory");
        $this->company = $this->companyfactory->get_instance();
        $data = [];
        if (is_numeric($id) && 0 < $id) {
            $this->fetch_company_accessibility($id);
            $this->company->fetch($id);
            $this->load->model("member");
            $parent_company["name"] = $this->company->get_field("name");
            $parent_company["shortName"] = $this->company->get_field("shortName");
            $parent_company["category"] = $this->company->get_field("category");
            $parent_company["member_id"] = $this->member->get_member_company($id);
            $parent_company["id"] = $id;
            $data["parent_company"] = $parent_company;
            if ($parent_company["category"] == "Group") {
                $data["companies"] = array_merge([["id" => $parent_company["id"], "name" => $parent_company["name"]]], $this->company->load_all(["select" => ["id, name"], "where" => ["company_id", $id]]));
            } else {
                $this->company->get_shares_tree_view($tree, $parent_company);
                $data["tree"] = $tree;
            }
        }
        $this->includes("scripts/reports/company_related_matters_report", "js");
        $this->includes("customerPortal/clientPortal/css/datatables.min", "css");
        $this->includes("customerPortal/clientPortal/js/datatables.min", "js");
        $this->includes("customerPortal/clientPortal/js/jquery.dataTables.min", "js");
        $this->includes("customerPortal/clientPortal/js/dataTables.bootstrap.min", "js");
        $this->load->view("partial/header");
        $this->load->view("reports/company_related_matters/main", $data);
        $this->load->view("partial/footer");
    }
    public function shareholders_finder($memberType = "", $id = 0)
    {
        $this->pageTitle = sprintf($this->lang->line("sheria360_browser_title"), $this->lang->line("reports") . " | " . $this->lang->line("shareholders_finder"));
        $data = [];
        $this->load->model("company", "companyfactory");
        $this->company = $this->companyfactory->get_instance();
        $this->load->model("contact", "contactfactory");
        $this->contact = $this->contactfactory->get_instance();
        if ($this->input->is_ajax_request()) {
            $response = [];
            $memberType = $this->input->post("memberType");
            if ($memberType == "contacts") {
                $response["records"] = $this->contact->load_list_contacts();
            } else {
                if ($memberType == "companies") {
                    $response["records"] = $this->company->load_list(["where" => [["status", "Active"], ["(companies.private IS NULL OR companies.private = 'no' OR (companies.private = 'yes' AND (companies.createdBy = '" . $this->company->logged_user_id . "' OR companies.id IN (SELECT company_id FROM company_users WHERE user_id = '" . $this->company->logged_user_id . "') OR '" . $this->company->override_privacy . "' = 'yes')))", NULL, false]], "where_in" => [["category", ["Internal", "Group"]]], "order_by" => [["category", "desc"], ["name", "asc"]]]);
                } else {
                    $response["records"] = [];
                }
            }
            $this->output->set_content_type("application/json")->set_output(json_encode($response));
        } else {
            $this->includes("jquery/css/chosen", "css");
            $this->includes("jquery/chosen.min", "js");
            $this->includes("scripts/reports/shareholder_finder", "js");
            if (is_numeric($id) && 0 < $id) {
                $data["shareholders"] = $this->company->find_member_shareholder($id, $memberType);
                if ($memberType == "companies") {
                    $this->company->fetch($id);
                    $data["filter"]["memberName"] = $this->company->get_field("name");
                    $data["filter"]["category"] = $this->company->get_field("category");
                } else {
                    $this->contact->fetch($id);
                    $data["filter"]["memberName"] = $this->contact->get_field("firstName") . " " . $this->contact->get_field("father") . " " . $this->contact->get_field("lastName");
                    $data["filter"]["category"] = "";
                }
                $this->includes("jquery/cookie", "js");
                $this->includes("jquery/treeview", "js");
            }
            $data["filter"]["member"] = $id;
            $data["filter"]["memberType"] = $memberType;
            $data["memberTypeList"] = [" " => $this->lang->line("choose_type"), "companies" => $this->lang->line("company"), "contacts" => $this->lang->line("contact")];
            $system_preferences = $this->session->userdata("systemPreferences");
            $data["hijri_calendar_enabled"] = isset($system_preferences["hijriCalendarFeature"]) && $system_preferences["hijriCalendarFeature"] ? $system_preferences["hijriCalendarFeature"] : 0;
            $this->load->view("partial/header");
            $this->load->view("reports/shareholders_finder", $data);
            $this->load->view("partial/footer");
        }
    }
    public function hearings_roll_session_report()
    {
        if ($this->input->is_ajax_request()) {
            $filters = $this->input->post("filter");
            $this->load->model("legal_case_hearing", "legal_case_hearingfactory");
            $this->legal_case_hearing = $this->legal_case_hearingfactory->get_instance();
            $filter_type = $this->input->post("filter_type");
            $_POST["take"] = $this->input->post("take") ? $this->input->post("take") : 20;
            $sort = $this->input->post("sort") ? $this->input->post("take") : [];
            $systemPreferences = $this->session->userdata("systemPreferences");
            $hijri_calendar_enabled = isset($systemPreferences["hijriCalendarFeature"]) && $systemPreferences["hijriCalendarFeature"] ? $systemPreferences["hijriCalendarFeature"] : 0;
            $data = $this->legal_case_hearing->load_hearings_roll_session_report($filters, $sort, $filter_type, $hijri_calendar_enabled);
            $filter_type = $this->input->post("filter_type");
            if ($filter_type == "last_case_hearing") {
                $cases = [];
                if (isset($data["data"]) && !empty($data["data"])) {
                    foreach ($data["data"] as $date) {
                        $mostRecent = 0;
                        $mostRecent1 = 0;
                        foreach ($data["data"] as $date1) {
                            if ($date1["legal_case_id"] == $date["legal_case_id"] && $date1["startDate"] != NULL) {
                                $startDate = $date1["startDate"] . " " . $date1["startTime"];
                                if ($mostRecent < $startDate) {
                                    $mostRecent = $startDate;
                                }
                            }
                            if ($date1["legal_case_id"] == $date["legal_case_id"] && $date1["postponedDate"] != NULL) {
                                $postponedDate = $date1["postponedDate"] . " " . $date1["postponedTime"];
                                if ($mostRecent1 < $postponedDate) {
                                    $mostRecent1 = $postponedDate;
                                }
                            }
                        }
                        $cases["maxDates"][$date["legal_case_id"]] = $mostRecent1 < $mostRecent ? $mostRecent : $mostRecent1;
                    }
                }
                $newData = [];
                if (!empty($cases) && !empty($data["data"])) {
                    foreach ($cases["maxDates"] as $key => $value) {
                        foreach ($data["data"] as $key1 => $value1) {
                            $startDate = $value1["startDate"] . " " . $value1["startTime"];
                            $postponedDate = $value1["postponedDate"] . " " . $value1["postponedTime"];
                            if ($key == $value1["legal_case_id"] && ($value == $startDate || $value == $postponedDate)) {
                                $exists_val = false;
                                if (!empty($newData["data"])) {
                                    foreach ($newData["data"] as $key3 => $value3) {
                                        if ($value3["legal_case_id"] == $value1["legal_case_id"]) {
                                            $newData["data"][$key3] = $value1;
                                            $exists_val = true;
                                        }
                                    }
                                }
                                if (!$exists_val) {
                                    $newData["data"][] = $value1;
                                }
                            }
                        }
                    }
                }
                if (isset($newData["data"]) && !empty($newData["data"])) {
                    $data["data"] = $newData["data"];
                    $data["totalRows"] = count($data["data"]);
                }
            }
            $this->output->set_content_type("application/json");
            $this->output->set_output(json_encode($data));
        } else {
            $this->pageTitle = sprintf($this->lang->line("sheria360_browser_title"), $this->lang->line("reports") . " | " . $this->lang->line("hearings_roll_session_report"));
            $filter_type = $this->input->post("filter_type");
            $data["take"] = $this->input->post("take");
            $data["skip"] = $this->input->post("skip");
            $data["filter_type"] = $filter_type;
            $data["direction"] = in_array($this->session->userdata("AUTH_language"), ["arabic", "persian", "hibrew"]) ? "rtl" : "ltr";
            if ($filter_type == "weekly") {
                $data["reportName"] = $this->lang->line("weekly_hearing_roll_session");
            } else {
                if ($filter_type == "monthly") {
                    $data["reportName"] = $this->lang->line("monthly_hearing_roll_session");
                } else {
                    if ($filter_type == "last_case_hearing") {
                        $data["reportName"] = $this->lang->line("last_case_hearing_report");
                    } else {
                        $data["reportName"] = $this->lang->line("hearings_roll_session_report");
                    }
                }
            }
            $data["operators"]["text"] = $this->get_filter_operators("text");
            $data["operators"]["big_text"] = $this->get_filter_operators("bigText");
            $data["operators"]["number"] = $this->get_filter_operators("number");
            $data["operators"]["date"] = $this->get_filter_operators("date");
            $data["operators"]["time"] = $this->get_filter_operators("time");
            $data["operators"]["list"] = $this->get_filter_operators("list");
            $data["operators"]["group_list"] = $this->get_filter_operators("groupList");
            $this->load->model(["court", "court_type", "court_region", "court_degree"]);
            $data["court_types"] = $this->court_type->load_list([]);
            $data["court_degrees"] = $this->court_degree->load_list([]);
            $data["court_regions"] = $this->court_region->load_list([]);
            $data["courts"] = $this->court->load_list([]);
            $this->load->model(["case_type", "provider_group"]);
            $data["case_types"] = $this->case_type->load_list(["where" => [["isDeleted", 0]], "order_by" => ["name", "asc"]]);
            $this->load->model("custom_field", "custom_fieldfactory");
            $this->custom_field = $this->custom_fieldfactory->get_instance();
            $this->load->model("legal_case", "legal_casefactory");
            $this->legal_case = $this->legal_casefactory->get_instance();
            $data["dataCustomFields"] = $this->custom_field->load_list_per_language($this->legal_case->get("modelName"));
            $this->load->model("legal_case_stage", "legal_case_stagefactory");
            $this->legal_case_stage = $this->legal_case_stagefactory->get_instance();
            $data["caseStages"] = $this->legal_case_stage->load_list_per_case_category_per_language("litigation");
            unset($data["caseStages"][""]);
            $systemPreferences = $this->session->userdata("systemPreferences");
            $data["hijri_calendar_enabled"] = isset($systemPreferences["hijriCalendarFeature"]) && $systemPreferences["hijriCalendarFeature"] ? $systemPreferences["hijriCalendarFeature"] : 0;
            $this->load->model("user", "userfactory");
            $this->user = $this->userfactory->get_instance();
            $data["users_list"] = $this->user->load_all_list();
            $data["users_list"][0] = "";
            $this->load->model("hearing_types_languages", "hearing_types_languagesfactory");
            $this->hearing_types_languages = $this->hearing_types_languagesfactory->get_instance();
            $data["types"] = $this->hearing_types_languages->load_list_per_language();
            unset($data["types"][""]);
            $data["types"] = [$this->lang->line("none")] + $data["types"];
            $this->includes("jquery/jquery.fixedheadertable", "js");
            $this->includes("jquery/form2js", "js");
            $this->includes("jquery/toObject", "js");
            $this->includes("kendoui/js/kendo.web.min", "js");
            $this->includes("kendoui/styles/kendo.common-bootstrap.min", "css");
            $this->includes("kendoui/styles/kendo.bootstrap.min", "css");
            if ($this->is_auth->is_layout_rtl()) {
                $this->includes("styles/rtl/fixes", "css");
            }
            $this->load->helper("text");
            $this->includes(app_url("compressed_asset/system_defaults.js?a=" . random_string("alpha")), "js", true);
            if ($this->is_auth->is_layout_rtl()) {
                $this->includes("styles/rtl/fixes", "css");
            }
            $this->includes("scripts/reports/hearings_roll_session_report", "js");
            $this->includes("scripts/advance_search_custom_field_template", "js");
            $this->load->view("partial/header");
            $this->load->view("reports/hearings_roll_session_" . $this->db->dbdriver, $data);
            $this->load->view("partial/footer");
        }
    }
    public function case_values_per_client_name($page = "", $operator = "", $fromDate = "", $toDate = "")
    {
        $this->pageTitle = sprintf($this->lang->line("sheria360_browser_title"), $this->lang->line("reports") . " | " . $this->lang->line("case_values_per_client_name"));
        $this->load->model("legal_case", "legal_casefactory");
        $this->legal_case = $this->legal_casefactory->get_instance();
        $systemPreferences = $this->session->userdata("systemPreferences");
        $data = [];
        $fixedFilters = ["caseArrivalDateField" => "", "caseArrivalDateOperator" => "cast_eq", "caseArrivalDateValue" => "", "caseArrivalDateEndField" => "", "caseArrivalDateEndOperator" => "", "caseArrivalDateEndValue" => "", "caseArrivalDateLogic" => ""];
        $filters["logic"] = "and";
        $filters["filters"][0]["filters"][0]["field"] = "caseArrivalDate";
        $filters["filters"][0]["filters"][0]["operator"] = $operator ? $operator : "cast_gte";
        $filters["filters"][0]["filters"][0]["value"] = $fromDate ? $fromDate : date("Y-01-01");
        if (!$operator || $operator && $operator === "cast_between") {
            $filters["filters"][0]["filters"][0]["operator"] = "cast_gte";
            $filters["filters"][0]["filters"][1]["field"] = "caseArrivalDate";
            $filters["filters"][0]["filters"][1]["operator"] = "cast_lte";
            $filters["filters"][0]["filters"][1]["value"] = $toDate ? $toDate : date("Y-m-d");
            $filters["filters"][0]["logic"] = "and";
        }
        if ($this->input->post(NULL)) {
            $filters = $this->input->post("filter");
            if (!empty($filters["filters"])) {
                if (isset($filters["filters"][0]["filters"][0]["value"]) && !empty($filters["filters"][0]["filters"][0]["value"])) {
                    $fixedFilters["caseArrivalDateField"] = $filters["filters"][0]["filters"][0]["field"];
                    $fixedFilters["caseArrivalDateOperator"] = $filters["filters"][0]["filters"][0]["operator"];
                    $fixedFilters["caseArrivalDateValue"] = $filters["filters"][0]["filters"][0]["value"];
                    if (isset($filters["filters"][0]["filters"][1]["value"]) && !empty($filters["filters"][0]["filters"][1]["value"]) && !empty($filters["filters"][0]["filters"][0]["value"]) && isset($filters["filters"][0]["filters"][0]["value"])) {
                        $fixedFilters["caseArrivalDateEndField"] = $filters["filters"][0]["filters"][1]["field"];
                        $fixedFilters["caseArrivalDateEndOperator"] = $filters["filters"][0]["filters"][1]["operator"];
                        $fixedFilters["caseArrivalDateEndValue"] = $filters["filters"][0]["filters"][1]["value"];
                        $fixedFilters["caseArrivalDateLogic"] = $filters["filters"][0]["logic"];
                    }
                } else {
                    $filters = [];
                }
            }
        } else {
            $_POST["take"] = 50;
            $_POST["skip"] = 0;
            $fixedFilters["caseArrivalDateField"] = $filters["filters"][0]["filters"][0]["field"];
            $fixedFilters["caseArrivalDateOperator"] = $filters["filters"][0]["filters"][0]["operator"];
            $fixedFilters["caseArrivalDateValue"] = $filters["filters"][0]["filters"][0]["value"];
            if (isset($filters["filters"][0]["filters"][1]["value"]) && !empty($filters["filters"][0]["filters"][1]["value"])) {
                $fixedFilters["caseArrivalDateEndField"] = $filters["filters"][0]["filters"][1]["field"];
                $fixedFilters["caseArrivalDateEndOperator"] = $filters["filters"][0]["filters"][1]["operator"];
                $fixedFilters["caseArrivalDateEndValue"] = $filters["filters"][0]["filters"][1]["value"];
                $fixedFilters["caseArrivalDateLogic"] = $filters["filters"][0]["logic"];
            }
        }
        $data = $this->legal_case->case_values_per_client_name(true, $filters);
        $data["caseValueCurrency"] = $systemPreferences["caseValueCurrency"];
        $data["perPageList"] = ["10" => 10, "20" => 20, "30" => 30, "40" => 40, "50" => 50, "100" => 100];
        $data["take"] = $this->input->post("take");
        $data["skip"] = $this->input->post("skip");
        $data["currPage"] = $this->uri->segment(3, "1");
        $data["fixedFilters"] = $fixedFilters;
        $data["selectedFilters"] = $filters;
        $data["operatorsDate"] = $this->get_filter_operators("date");
        $data["direction"] = in_array($this->session->userdata("AUTH_language"), ["arabic", "persian", "hibrew"]) ? "rtl" : "ltr";
        $this->includes("kendoui/js/kendo.web.min", "js");
        $this->includes("kendoui/styles/kendo.common-bootstrap.min", "css");
        $this->includes("kendoui/styles/kendo.bootstrap.min", "css");
        if ($this->is_auth->is_layout_rtl()) {
            $this->includes("styles/rtl/fixes", "css");
        }
        $this->load->view("partial/header");
        $this->load->view("reports/case_values_per_client_name", $data);
        $this->load->view("partial/footer");
    }
    public function case_values_per_client_name_micro($page = "", $operator = "", $fromDate = "", $toDate = "")
    {
        $this->pageTitle = sprintf($this->lang->line("sheria360_browser_title"), $this->lang->line("reports") . " | " . $this->lang->line("case_values_per_client_name"));
        $this->load->model("legal_case", "legal_casefactory");
        $this->legal_case = $this->legal_casefactory->get_instance();
        $systemPreferences = $this->session->userdata("systemPreferences");
        $data = [];
        $clients = [];
        $existsClients = [];
        $count = 0;
        $fixedFilters = ["caseArrivalDateField" => "", "caseArrivalDateOperator" => "cast_eq", "caseArrivalDateValue" => "", "caseArrivalDateEndField" => "", "caseArrivalDateEndOperator" => "", "caseArrivalDateEndValue" => "", "caseArrivalDateLogic" => ""];
        $filters["logic"] = "and";
        $filters["filters"][0]["filters"][0]["field"] = "legal_cases.caseArrivalDate";
        $filters["filters"][0]["filters"][0]["operator"] = $operator ? $operator : "cast_gte";
        $filters["filters"][0]["filters"][0]["value"] = $fromDate ? $fromDate : date("Y-01-01");
        if (!$operator || $operator && $operator === "cast_between") {
            $filters["filters"][0]["filters"][0]["operator"] = "cast_gte";
            $filters["filters"][0]["filters"][1]["field"] = "legal_cases.caseArrivalDate";
            $filters["filters"][0]["filters"][1]["operator"] = "cast_lte";
            $filters["filters"][0]["filters"][1]["value"] = $toDate ? $toDate : date("Y-m-d");
            $filters["filters"][0]["logic"] = "and";
        }
        if ($this->input->post(NULL)) {
            $filters = $this->input->post("filter");
            if (!empty($filters["filters"])) {
                if (isset($filters["filters"][0]["filters"][0]["value"]) && !empty($filters["filters"][0]["filters"][0]["value"])) {
                    $fixedFilters["caseArrivalDateField"] = $filters["filters"][0]["filters"][0]["field"];
                    $fixedFilters["caseArrivalDateOperator"] = $filters["filters"][0]["filters"][0]["operator"];
                    $fixedFilters["caseArrivalDateValue"] = $filters["filters"][0]["filters"][0]["value"];
                    if (isset($filters["filters"][0]["filters"][1]["value"]) && !empty($filters["filters"][0]["filters"][1]["value"]) && !empty($filters["filters"][0]["filters"][0]["value"]) && isset($filters["filters"][0]["filters"][0]["value"])) {
                        $fixedFilters["caseArrivalDateEndField"] = $filters["filters"][0]["filters"][1]["field"];
                        $fixedFilters["caseArrivalDateEndOperator"] = $filters["filters"][0]["filters"][1]["operator"];
                        $fixedFilters["caseArrivalDateEndValue"] = $filters["filters"][0]["filters"][1]["value"];
                        $fixedFilters["caseArrivalDateLogic"] = $filters["filters"][0]["logic"];
                    }
                } else {
                    $filters = [];
                }
            }
        } else {
            $_POST["take"] = 50;
            $_POST["skip"] = 0;
            $fixedFilters["caseArrivalDateField"] = $filters["filters"][0]["filters"][0]["field"];
            $fixedFilters["caseArrivalDateOperator"] = $filters["filters"][0]["filters"][0]["operator"];
            $fixedFilters["caseArrivalDateValue"] = $filters["filters"][0]["filters"][0]["value"];
            if (isset($filters["filters"][0]["filters"][1]["value"]) && !empty($filters["filters"][0]["filters"][1]["value"])) {
                $fixedFilters["caseArrivalDateEndField"] = $filters["filters"][0]["filters"][1]["field"];
                $fixedFilters["caseArrivalDateEndOperator"] = $filters["filters"][0]["filters"][1]["operator"];
                $fixedFilters["caseArrivalDateEndValue"] = $filters["filters"][0]["filters"][1]["value"];
                $fixedFilters["caseArrivalDateLogic"] = $filters["filters"][0]["logic"];
            }
        }
        $data = $this->legal_case->case_values_per_client_name_micro(true, $filters);
        foreach ($data["result"] as $value) {
            if (!in_array($value["client_id"], $existsClients)) {
                $clients[$count]["clientName"] = $value["clientName"];
                $clients[$count]["client_id"] = $value["client_id"];
                $existsClients[] = $value["client_id"];
                $count++;
            }
        }
        $data["caseValueCurrency"] = $systemPreferences["caseValueCurrency"];
        $data["perPageList"] = ["10" => 10, "20" => 20, "30" => 30, "40" => 40, "50" => 50, "100" => 100];
        $data["take"] = $this->input->post("take");
        $data["skip"] = $this->input->post("skip");
        $data["currPage"] = $this->uri->segment(3, "1");
        $data["clients"] = $clients;
        $data["fixedFilters"] = $fixedFilters;
        $data["selectedFilters"] = $filters;
        $data["operatorsDate"] = $this->get_filter_operators("date");
        $data["direction"] = in_array($this->session->userdata("AUTH_language"), ["arabic", "persian", "hibrew"]) ? "rtl" : "ltr";
        $this->includes("kendoui/js/kendo.web.min", "js");
        $this->includes("kendoui/styles/kendo.common-bootstrap.min", "css");
        $this->includes("kendoui/styles/kendo.bootstrap.min", "css");
        if ($this->is_auth->is_layout_rtl()) {
            $this->includes("styles/rtl/fixes", "css");
        }
        $this->load->view("partial/header");
        $this->load->view("reports/case_values_per_client_name_micro", $data);
        $this->load->view("partial/footer");
    }
    public function case_values_per_client_name_chart()
    {
        $this->pageTitle = sprintf($this->lang->line("sheria360_browser_title"), $this->lang->line("reports") . " | " . $this->lang->line("case_values_per_client_name"));
        $this->load->model("legal_case", "legal_casefactory");
        $this->legal_case = $this->legal_casefactory->get_instance();
        $data = [];
        $clientName = [];
        $caseValues = [];
        $dataResult = $this->legal_case->case_values_per_client_name(false);
        if (!empty($dataResult["result"])) {
            foreach ($dataResult["result"] as $value) {
                $clientName[] = $value["clientName"];
                $caseValues[] = floatval($value["caseValues"]);
            }
        }
        if (is_array($caseValues) && 10 < count($caseValues)) {
            for ($i = 0; $i < 10; $i++) {
                $clients[] = $clientName[$i];
                $cases[] = $caseValues[$i];
            }
            $others = 0;
            for ($i = 10; $i < count($caseValues); $i++) {
                $others += $caseValues[$i];
            }
            $clients[] = "Others";
            $cases[] = $others;
            $clientName = $clients;
            $caseValues = $cases;
        }
        $this->load->helper("text");
        $this->includes("jquery/jqplot/excanvas", "js");
        $this->includes("jquery/jqplot/jquery.jqplot.min", "css");
        $this->includes("jquery/jqplot/jquery.jqplot.min", "js");
        $this->includes("jquery/jqplot/plugins/jqplot.dateAxisRenderer", "js");
        $this->includes("jquery/jqplot/plugins/jqplot.canvasTextRenderer.min", "js");
        $this->includes("jquery/jqplot/plugins/jqplot.canvasAxisTickRenderer", "js");
        $this->includes("jquery/jqplot/plugins/jqplot.categoryAxisRenderer.min", "js");
        $this->includes("jquery/jqplot/plugins/jqplot.barRenderer.min", "js");
        $this->includes("jquery/jqplot/plugins/jqplot.categoryAxisRenderer.min", "js");
        $this->includes("jquery/jqplot/plugins/jqplot.pointLabels.min", "js");
        $data["clientName"] = json_encode($clientName);
        $data["caseValues"] = json_encode($caseValues);
        $this->load->view("partial/header");
        $this->load->view("reports/case_values_per_client_name_chart", $data);
        $this->load->view("partial/footer");
    }
    public function case_value_tiers_chart()
    {
        $this->pageTitle = sprintf($this->lang->line("sheria360_browser_title"), $this->lang->line("reports") . " | " . $this->lang->line("case_value_tiers"));
        $this->load->model("legal_case", "legal_casefactory");
        $this->load->model(["case_configuration", "case_type"]);
        $this->legal_case = $this->legal_casefactory->get_instance();
        $ranges = $this->case_configuration->get_value_by_key("caseValueTiers");
        $Case_Types = $this->case_type->load_list(["where" => ["isDeleted", 0]]);
        $dataFetched = $this->legal_case->load_all_cases_per_type_and_ranges($ranges);
        $rangesChart = [];
        foreach ($dataFetched["result"][0] as $key => $value) {
            if ($key != "name" && $key != "case_type_id") {
                $rangesChart[] = $key;
            }
        }
        $dataResult = [];
        $types = [];
        foreach ($dataFetched["result"] as $key => $value) {
            $tmp = [];
            foreach ($value as $key1 => $value2) {
                if ($key1 != "name" && $key1 != "case_type_id") {
                    $tmp[] = $value2;
                } else {
                    if ($key1 == "name") {
                        $types[] = $value2;
                    }
                }
            }
            array_push($dataResult, $tmp);
        }
        $this->load->helper("text");
        $this->includes("jquery/jqplot/excanvas", "js");
        $this->includes("jquery/jqplot/jquery.jqplot.min", "css");
        $this->includes("jquery/jqplot/jquery.jqplot.min", "js");
        $this->includes("jquery/jqplot/plugins/jqplot.barRenderer.min", "js");
        $this->includes("jquery/jqplot/plugins/jqplot.categoryAxisRenderer.min", "js");
        $this->includes("jquery/jqplot/plugins/jqplot.pointLabels.min", "js");
        $data["ranges"] = json_encode($rangesChart);
        $data["Case_Types"] = json_encode($dataResult);
        $data["types"] = json_encode($types);
        $this->load->view("partial/header");
        $this->load->view("reports/case_value_tiers_chart", $data);
        $this->load->view("partial/footer");
    }
    public function time_tracking_by_case($case_id = 0, $filter_by_date = "")
    {
        $data["chooseCase"] = false;
        $data["chart_hours"] = [];
        $data["chart_users"] = [];
        $data["caseId"] = "";
        $data["case_id"] = $case_id;
        $data["subject"] = "";
        $data["estimatedEffortString"] = "";
        $data["estimatedEffort"] = "";
        $data["effectiveEffort"] = "";
        $data["dataFetched"] = "";
        if (is_numeric($case_id) && $case_id != 0) {
            $this->load->library("TimeMask");
            $data["chooseCase"] = true;
            $this->load->model("legal_case", "legal_casefactory");
            $this->legal_case = $this->legal_casefactory->get_instance();
            $this->load->model("user_activity_log", "user_activity_logfactory");
            $this->user_activity_log = $this->user_activity_logfactory->get_instance();
            $is_case_deleted = $this->legal_case->check_if_case_deleted($case_id);
            if ($is_case_deleted) {
                $this->set_flashmessage("error", $this->lang->line("invalid_record"));
                redirect("reports");
            }
            $this->load->helper("text");
            $this->includes("jquery/jqplot/excanvas", "js");
            $this->includes("jquery/jqplot/jquery.jqplot.min", "css");
            $this->includes("jquery/jqplot/jquery.jqplot.min", "js");
            $this->includes("scripts/reports/time_tracking_by_case", "js");
            $this->includes("jquery/jqplot/plugins/jqplot.barRenderer.min", "js");
            $this->includes("jquery/jqplot/plugins/jqplot.categoryAxisRenderer.min", "js");
            $this->includes("jquery/jqplot/plugins/jqplot.pointLabels.min", "js");
            $time_tracking_data = $this->user_activity_log->time_tracking_by_case($case_id, $filter_by_date);
            foreach ($time_tracking_data as $val) {
                $data["chart_hours"][] = floatval($val["nbOfHours"]);
                if ($val["status"] == "Inactive") {
                    $data["chart_users"][] = $val["userName"] . "(" . $this->lang->line("Inactive") . ")";
                } else {
                    $data["chart_users"][] = $val["userName"];
                }
            }
            $data["effectiveEffort"] = array_sum($data["chart_hours"]);
            $data["chart_hours"] = json_encode($data["chart_hours"]);
            $data["chart_users"] = json_encode($data["chart_users"]);
            $legal_case_data = $this->legal_case->get_time_trakcing_data($case_id);
            $data["caseId"] = $this->legal_case->get("modelCode") . $case_id;
            $data["subject"] = $legal_case_data["subject"];
            $data["category"] = $legal_case_data["category"];
            $data["estimatedEffortString"] = $legal_case_data["estimatedEffort"];
            $data["estimated_effort_hours_minutes"] = $this->timemask->timeToHumanReadable($legal_case_data["estimatedEffort"]);
            $data["effective_effort_hours_minutes"] = $this->timemask->timeToHumanReadable($data["effectiveEffort"]);
            $data["filter_by_date_list"] = ["all" => $this->lang->line("all"), "month" => $this->lang->line("month"), "year" => $this->lang->line("year")];
            $data["filter_by_date"] = !empty($filter_by_date) ? $filter_by_date : "all";
            $data["dataFetched"] = empty($time_tracking_data) ? 0 : 1;
        }
        $this->pageTitle = sprintf($this->lang->line("sheria360_browser_title"), $this->lang->line("reports") . " | " . $this->lang->line("time_tracking_by_case"));
        $this->load->view("partial/header");
        $this->load->view("reports/time_tracking_by_case", $data);
        $this->load->view("partial/footer");
    }
    public function time_tracking_by_seniority($caseId = 0)
    {
        $this->load->library("TimeMask");
        $data["caseSearch"] = true;
        $data["caseId"] = "";
        $data["subject"] = "";
        $data["dataFetched"] = "";
        if (is_numeric($caseId) && $caseId != 0) {
            $data["caseId"] = $caseId;
            $data["caseSearch"] = false;
            $this->load->model("legal_case", "legal_casefactory");
            $this->legal_case = $this->legal_casefactory->get_instance();
            $is_case_deleted = $this->legal_case->check_if_case_deleted($caseId);
            if ($is_case_deleted) {
                $this->set_flashmessage("error", $this->lang->line("invalid_record"));
                redirect("reports");
            }
            $this->legal_case->fetch($caseId);
            $this->load->model("user_activity_log", "user_activity_logfactory");
            $this->user_activity_log = $this->user_activity_logfactory->get_instance();
            $this->load->helper("text");
            $timeTrackingSeniority = $this->user_activity_log->time_tracking_by_case_seniority($caseId);
            $data["ratesBySeniorityLevel"] = [];
            $totalHours = 0;
            foreach ($timeTrackingSeniority as $key => $val) {
                $data["ratesBySeniorityLevel"][$val["seniorityLevel"]][] = $val;
                $totalHours += $val["nbOfHours"];
                $data["category"] = $timeTrackingSeniority[$key]["category"];
            }
            $data["totalHours"] = $totalHours;
            $data["subject"] = $this->legal_case->get_field("subject");
            $this->includes("jquery/jqplot/jquery.jqplot.min", "css");
            $this->includes("jquery/jqplot/jquery.jqplot.min", "js");
            $this->includes("jquery/jqplot/plugins/jqplot.pieRenderer", "js");
            $this->includes("scripts/reports/time_tracking_by_seniority", "js");
        }
        $this->pageTitle = sprintf($this->lang->line("sheria360_browser_title"), $this->lang->line("reports") . " | " . $this->lang->line("time_tracking_by_seniority"));
        $this->load->view("partial/header");
        $this->load->view("reports/time_tracking_by_seniority", $data);
        $this->load->view("partial/footer");
    }
    private function create_graph($data)
    {
        require_once COREPATH . "libraries/libchart/libchart.php";
        $chart = new PieChart();
        $dataSet = new XYDataSet();
        foreach ($data as $key => $val) {
            $dataSet->addPoint(new Point($key, $val));
        }
        $chart->setDataSet($dataSet);
        $chart->setTitle("");
        $file_name = "seniority_chart.png";
        $corepath = substr(COREPATH, 0, -12);
        $chartDir = $this->config->item("files_path") . DIRECTORY_SEPARATOR . "tmp" . DIRECTORY_SEPARATOR . "charts";
        if (!is_dir($chartDir)) {
            @mkdir($chartDir, 493);
        }
        $filePath = $chartDir . DIRECTORY_SEPARATOR . $file_name;
        $chart->render($filePath);
        return $filePath;
    }
    public function export_time_tracking_seniority_pdf($caseId = 0)
    {
        $this->load->library("TimeMask");
        $data["caseId"] = $caseId;
        $data["caseSearch"] = false;
        $this->load->model("legal_case", "legal_casefactory");
        $this->legal_case = $this->legal_casefactory->get_instance();
        $this->legal_case->fetch($caseId);
        $this->load->model("user_activity_log", "user_activity_logfactory");
        $this->user_activity_log = $this->user_activity_logfactory->get_instance();
        $this->load->helper("text");
        $timeTrackingSeniority = $this->user_activity_log->time_tracking_by_case_seniority($caseId);
        $data["ratesBySeniorityLevel"] = [];
        $totalHours = 0;
        $chartData = [];
        foreach ($timeTrackingSeniority as $val) {
            $chartVal = isset($chartData[$val["seniorityLevel"]]) ? $chartData[$val["seniorityLevel"]] + $val["nbOfHours"] : $val["nbOfHours"];
            $chartData[$val["seniorityLevel"]] = $chartVal;
            $data["ratesBySeniorityLevel"][$val["seniorityLevel"]][] = $val;
            $totalHours += $val["nbOfHours"];
        }
        $chartImg = $this->create_graph($chartData);
        $content = base64_encode(@file_get_contents($chartImg));
        if (file_exists($chartImg)) {
            $image_size = getimagesize($chartImg);
            $ext = substr(substr($chartImg, strrpos($chartImg, ".")), 1);
            $image = ["content" => $content, "width" => $image_size[0], "height" => $image_size[1], "ext" => $ext];
        }
        $data["image"] = $image;
        $data["totalHours"] = $totalHours;
        $data["subject"] = $this->legal_case->get_field("subject");
        $data["direction"] = $this->is_auth->is_layout_rtl() ? "rtl" : "ltr";
        $html = $this->load->view("reports/time_tracking_seniority_pdf", $data, true);
        $file_name = $this->lang->line("time_tracking_by_seniority") . "_" . date("YmdHi");
        $this->load->helper(["dompdf", "file"]);
        pdf_create($html, $file_name);
    }
    public function time_tracking_kpi()
    {
        $this->pageTitle = sprintf($this->lang->line("sheria360_browser_title"), $this->lang->line("reports") . " | " . $this->lang->line("time_tracking_kpi_report"));
        $data = [];
        $this->load->model("user_rate_per_hour_per_case", "user_rate_per_hour_per_casefactory");
        $this->user_Rate = $this->user_rate_per_hour_per_casefactory->get_instance();
        $data["organizations"] = $this->user_Rate->get_entities();
        $data["displayTasks"] = !strcmp($this->input->post("displayTasks"), "yes") || !$this->input->post(NULL) ? true : false;
        $data["organizationId"] = $data["organizations"][0]["id"];
        $filter = [];
        $this->load->library("TimeMask");
        $savedFilters = ["userIdValue" => "", "userNameValue" => "", "caseValue" => "", "taskValue" => "", "dateField" => "", "dateOperator" => "cast_lte", "dateValue" => "", "dateEndValue" => ""];
        if ($this->input->post(NULL)) {
            $filter = $this->input->post("filter");
            $data["organizationId"] = $this->input->post("organizationId");
            if (isset($filter["filters"][1]["filters"][0]["value"]) && !empty($filter["filters"][1]["filters"][0]["value"])) {
                $savedFilters["userIdValue"] = $filter["filters"][1]["filters"][0]["value"];
                $this->load->model("user_profile");
                $this->user_profile->fetch(["user_id" => $savedFilters["userIdValue"]]);
                $savedFilters["userNameValue"] = $this->user_profile->get_field("firstName") . " " . $this->user_profile->get_field("lastName");
            }
            if (isset($filter["filters"][2]["filters"][0]["value"])) {
                if (!is_numeric($filter["filters"][2]["filters"][0]["value"])) {
                    $this->set_flashmessage("error", $this->lang->line("invalid_case_id"));
                    redirect("reports/time_tracking_kpi");
                }
                $savedFilters["caseValue"] = $filter["filters"][2]["filters"][0]["value"];
            }
            if (isset($filter["filters"][3]["filters"][0]["value"])) {
                if (!is_numeric($filter["filters"][3]["filters"][0]["value"])) {
                    $this->set_flashmessage("error", $this->lang->line("invalid_task_id"));
                    redirect("reports/time_tracking_kpi");
                }
                $savedFilters["taskValue"] = $filter["filters"][3]["filters"][0]["value"];
            }
            if (isset($filter["filters"][0]["filters"][0]["value"])) {
                $savedFilters["dateOperator"] = $filter["filters"][0]["filters"][0]["operator"];
                $savedFilters["dateValue"] = $filter["filters"][0]["filters"][0]["value"];
                if (isset($filter["filters"][0]["filters"][1]["value"]) && !empty($filter["filters"][0]["filters"][0]["value"])) {
                    $savedFilters["dateEndOperator"] = $filter["filters"][0]["filters"][1]["operator"];
                    $savedFilters["dateEndValue"] = $filter["filters"][0]["filters"][1]["value"];
                }
            } else {
                unset($filter["filters"][0]);
            }
        } else {
            $filter["logic"] = "and";
            $filter["filters"][] = ["filters" => [["field" => "ual.logDate", "operator" => "cast_lte", "value" => date("Y-m-d")]]];
        }
        $data["savedFilters"] = $savedFilters;
        $this->load->model("legal_case", "legal_casefactory");
        $this->legal_case = $this->legal_casefactory->get_instance();
        $casesTimeTracking = $this->legal_case->user_rates_per_cases_per_assignees($data["organizationId"], $filter);
        $data["operatorsDate"] = $this->get_filter_operators("date");
        $initialArr = ["userName" => "", "billableNbOfHours" => 0, "nonBillableNbOfHours" => 0, "billableAmount" => 0, "nonBillableAmount" => 0];
        $casesPerUser = [];
        if (!empty($casesTimeTracking["data"])) {
            foreach ($casesTimeTracking["data"] as $usrLog) {
                if (!isset($casesPerUser[$usrLog["userId"]])) {
                    $casesPerUser[$usrLog["userId"]] = $initialArr;
                    $casesPerUser[$usrLog["userId"]]["userName"] = $usrLog["worker"];
                }
                if (!strcmp($usrLog["timeStatus"], "billable")) {
                    $billableNbOfHours = $usrLog["effectiveEffort"];
                    $nonBillableNbOfHours = 0;
                    $billableAmount = isset($usrLog["ratePerHour"]) ? $billableNbOfHours * $usrLog["ratePerHour"] : 0;
                    $nonBillableAmount = 0;
                } else {
                    $billableNbOfHours = 0;
                    $nonBillableNbOfHours = $usrLog["effectiveEffort"];
                    $billableAmount = 0;
                    $nonBillableAmount = isset($usrLog["ratePerHour"]) ? $nonBillableNbOfHours * $usrLog["ratePerHour"] : 0;
                }
                $casesPerUser[$usrLog["userId"]]["billableNbOfHours"] += $billableNbOfHours;
                $casesPerUser[$usrLog["userId"]]["nonBillableNbOfHours"] += $nonBillableNbOfHours;
                $casesPerUser[$usrLog["userId"]]["billableAmount"] += $billableAmount;
                $casesPerUser[$usrLog["userId"]]["nonBillableAmount"] += $nonBillableAmount;
            }
        }
        $data["cases_relatedTasks"] = $casesPerUser;
        $tasksPerUser = [];
        if ($data["displayTasks"]) {
            $this->load->model("task", "taskfactory");
            $this->task = $this->taskfactory->get_instance();
            $tasksTimeTracking = $this->task->user_rates_per_tasks_per_assignees($data["organizationId"], $filter);
            if (!empty($tasksTimeTracking["data"])) {
                foreach ($tasksTimeTracking["data"] as $usrLog) {
                    if (!isset($tasksPerUser[$usrLog["userId"]])) {
                        $tasksPerUser[$usrLog["userId"]] = $initialArr;
                        $tasksPerUser[$usrLog["userId"]]["userName"] = $usrLog["worker"];
                    }
                    if (!strcmp($usrLog["timeStatus"], "billable")) {
                        $billableNbOfHours = $usrLog["effectiveEffort"];
                        $nonBillableNbOfHours = 0;
                        $billableAmount = isset($usrLog["ratePerHour"]) ? $billableNbOfHours * $usrLog["ratePerHour"] : 0;
                        $nonBillableAmount = 0;
                    } else {
                        $billableNbOfHours = 0;
                        $nonBillableNbOfHours = $usrLog["effectiveEffort"];
                        $billableAmount = 0;
                        $nonBillableAmount = isset($usrLog["ratePerHour"]) ? $nonBillableNbOfHours * $usrLog["ratePerHour"] : 0;
                    }
                    $tasksPerUser[$usrLog["userId"]]["billableNbOfHours"] += $billableNbOfHours;
                    $tasksPerUser[$usrLog["userId"]]["nonBillableNbOfHours"] += $nonBillableNbOfHours;
                    $tasksPerUser[$usrLog["userId"]]["billableAmount"] += $billableAmount;
                    $tasksPerUser[$usrLog["userId"]]["nonBillableAmount"] += $nonBillableAmount;
                }
            }
        }
        $data["tasks"] = $tasksPerUser;
        $this->load->view("partial/header");
        $this->load->view("reports/time_tracking_kpi", $data);
        $this->load->view("partial/footer");
    }
    public function master_register()
    {
        $this->pageTitle = sprintf($this->lang->line("sheria360_browser_title"), $this->lang->line("reports") . " | " . $this->lang->line("category_cases_statistics"));
        $data['title']=$this->lang->line("master_register");
        $this->load->view("partial/header");
        $this->load->view("reports/prosecution/master_register", $data);
        $this->load->view("partial/footer");

    }
    public function category_cases_statistics()
    {
        $this->pageTitle = sprintf($this->lang->line("sheria360_browser_title"), $this->lang->line("reports") . " | " . $this->lang->line("category_cases_statistics"));
$data['title']=$this->lang->line("category_cases_statistics");
        $this->load->view("partial/header");
        $this->load->view("reports/prosecution/category_cases_statistics", $data);
        $this->load->view("partial/footer");
    }
    public function exhibit_reports()
    {

        $this->pageTitle = sprintf($this->lang->line("sheria360_browser_title"), $this->lang->line("reports") . " | " . $this->lang->line("exhibit_reports"));
        $data['title']=$this->lang->line("exhibit_reports");
        $this->load->view("partial/header");
        $this->load->view("reports/prosecution/exhibit_reports", $data);
        $this->load->view("partial/footer");
    }

   public function cases_pending_before_court()
    {
        $this->pageTitle = sprintf($this->lang->line("sheria360_browser_title"), $this->lang->line("reports") . " | " . $this->lang->line("cases_pending_before_court"));
        $data['title']=$this->lang->line("cases_pending_before_court");
        $this->load->view("partial/header");
        $this->load->view("reports/prosecution/cases_pending_before_court", $data);
        $this->load->view("partial/footer");

    }
    public function case_log_summary($page="case_log_summary")
    {    $this->pageTitle = sprintf($this->lang->line("sheria360_browser_title"), $this->lang->line("reports") . " | " . $this->lang->line("case_log_summary"));
        $data['title']=$this->lang->line("case_log_summary");

      if ($page=="case_log_summary") $page="case_log_summary"; else $page="v2/".$page;
          $this->load->view("partial/header");
        $this->load->view("reports/prosecution/".$page, $data);
        $this->load->view("partial/footer");

    }
    public function prosecution($page="case_log_summary")
    { echo"etshs";
        $this->load->view("partial/header");
        $this->load->view("reports/prosecution/v2/".$page);
        $this->load->view("partial/footer");

    }
    public function time_tracking_kpi_micro()
    {
        $this->load->library("TimeMask");
        $this->pageTitle = sprintf($this->lang->line("sheria360_browser_title"), $this->lang->line("reports") . " | " . $this->lang->line("time_tracking_kpi_report"));
        $data = [];
        $this->load->model("user_rate_per_hour_per_case", "user_rate_per_hour_per_casefactory");
        $this->user_Rate = $this->user_rate_per_hour_per_casefactory->get_instance();
        $data["organizations"] = $this->user_Rate->get_entities();
        $data["displayAll"] = true;
        $data["displayTasks"] = !strcmp($this->input->post("displayTasks"), "yes") || !$this->input->post(NULL) ? true : false;
        $data["organizationId"] = $data["organizations"][0]["id"];
        $filter = [];
        $savedFilters = ["userIdValue" => "", "userNameValue" => "", "caseValue" => "", "taskValue" => "", "clientIdValue" => "", "clientNameValue" => "", "dateField" => "", "dateOperator" => "cast_lte", "dateValue" => "", "dateEndValue" => ""];
        if ($this->input->post(NULL)) {
            $filter = $this->input->post("filter");
            $data["organizationId"] = $this->input->post("organizationId");
            if (isset($filter["filters"][1]["filters"][0]["value"]) && !empty($filter["filters"][1]["filters"][0]["value"])) {
                $savedFilters["userIdValue"] = $filter["filters"][1]["filters"][0]["value"];
                $this->load->model("user_profile");
                $this->user_profile->fetch(["user_id" => $savedFilters["userIdValue"]]);
                $savedFilters["userNameValue"] = $this->user_profile->get_field("firstName") . " " . $this->user_profile->get_field("lastName");
            }
            if (isset($filter["filters"][2]["filters"][0]["value"])) {
                if (!is_numeric($filter["filters"][2]["filters"][0]["value"])) {
                    $this->set_flashmessage("error", $this->lang->line("invalid_case_id"));
                    redirect("reports/time_tracking_kpi_micro");
                }
                $savedFilters["caseValue"] = $filter["filters"][2]["filters"][0]["value"];
            }
            if (isset($filter["filters"][3]["filters"][0]["value"])) {
                if (!is_numeric($filter["filters"][3]["filters"][0]["value"])) {
                    $this->set_flashmessage("error", $this->lang->line("invalid_task_id"));
                    redirect("reports/time_tracking_kpi_micro");
                }
                $savedFilters["taskValue"] = $filter["filters"][3]["filters"][0]["value"];
            }
            if (isset($filter["filters"][4]["filters"][0]["value"]) && !empty($filter["filters"][4]["filters"][0]["value"])) {
                $savedFilters["clientIdValue"] = $filter["filters"][4]["filters"][0]["value"];
                $this->load->model("client");
                $client_data = $this->client->fetch_client($savedFilters["clientIdValue"]);
                $savedFilters["clientNameValue"] = $client_data["clientName"];
            }
            if (isset($filter["filters"][0]["filters"][0]["value"])) {
                $savedFilters["dateOperator"] = $filter["filters"][0]["filters"][0]["operator"];
                $savedFilters["dateValue"] = $filter["filters"][0]["filters"][0]["value"];
                if (isset($filter["filters"][0]["filters"][1]["value"]) && !empty($filter["filters"][0]["filters"][0]["value"])) {
                    $savedFilters["dateEndOperator"] = $filter["filters"][0]["filters"][1]["operator"];
                    $savedFilters["dateEndValue"] = $filter["filters"][0]["filters"][1]["value"];
                }
            } else {
                unset($filter["filters"][0]);
            }
        } else {
            $filter["logic"] = "and";
            $filter["filters"][] = ["filters" => [["field" => "ual.logDate", "operator" => "cast_lte", "value" => date("Y-m-d")]]];
        }
        $data["savedFilters"] = $savedFilters;
        $this->load->model("legal_case", "legal_casefactory");
        $this->legal_case = $this->legal_casefactory->get_instance();
        $casesTimeTracking = $this->legal_case->user_rates_per_cases_per_assignees($data["organizationId"], $filter);
        $userLogs = [];
        if (!empty($casesTimeTracking["data"])) {
            foreach ($casesTimeTracking["data"] as $usrLog) {
                $userLogs[$usrLog["userId"]][] = $usrLog;
            }
        }
        if ($data["displayTasks"]) {
            $this->load->model("task", "taskfactory");
            $this->task = $this->taskfactory->get_instance();
            $tasksTimeTracking = $this->task->user_rates_per_tasks_per_assignees($data["organizationId"], $filter);
            if (!empty($tasksTimeTracking["data"])) {
                foreach ($tasksTimeTracking["data"] as $usrTaskLog) {
                    $userLogs[$usrTaskLog["userId"]][] = $usrTaskLog;
                }
            }
        }
        $data["userLogs"] = $userLogs;
        $data["operatorsDate"] = $this->get_filter_operators("date");
        $this->load->view("partial/header");
        $this->load->view("reports/time_tracking_kpi_micro", $data);
        $this->load->view("partial/footer");
    }
    public function cases_by_tiers($case_type_id, $range1, $range2 = [])
    {
        $this->pageTitle = sprintf($this->lang->line("sheria360_browser_title"), $this->lang->line("reports") . " | " . $this->lang->line("case_value_tiers"));
        if ($this->input->is_ajax_request()) {
            $this->load->model("legal_case", "legal_casefactory");
            $this->legal_case = $this->legal_casefactory->get_instance();
            $filter["caseSubject"] = $this->input->post("caseSubject");
            $filter["case_type_id"] = $this->input->post("case_type_id");
            $filter["range1"] = $this->input->post("range1");
            $filter["range2"] = $this->input->post("range2");
            $sortable = $this->input->post("sort");
            $response = $this->legal_case->cases_by_tiers($filter, $sortable, true);
            $this->output->set_content_type("application/json");
            $this->output->set_output(json_encode($response));
        } else {
            $this->includes("kendoui/js/kendo.web.min", "js");
            $this->includes("kendoui/styles/kendo.common-bootstrap.min", "css");
            $this->includes("kendoui/styles/kendo.bootstrap.min", "css");
            $this->includes("jquery/form2js", "js");
            $this->includes("jquery/toObject", "js");
            $this->includes(app_url("compressed_asset/system_defaults.js?a=" . random_string("alpha")), "js", true);
            if ($this->is_auth->is_layout_rtl()) {
                $this->includes("styles/rtl/fixes", "css");
            }
            $this->load->model("case_type");
            $this->case_type->fetch($case_type_id);
            $data["case_type"] = $this->case_type->get_field("name");
            $data["case_type_id"] = $case_type_id;
            $data["range1"] = $range1;
            $data["range2"] = $range2;
            $data["perPageList"] = ["5" => 5, "10" => 10, "50" => 50, "100" => 100];
            $data["take"] = $this->input->post("take");
            $data["skip"] = $this->input->post("skip");
            $data["currPage"] = $this->uri->segment(3, "1");
            $this->load->library("TimeMask");
            $system_preferences = $this->session->userdata("systemPreferences");
            $data["businessWeekDays"] = $system_preferences["businessWeekEquals"];
            $data["businessDayHours"] = $system_preferences["businessDayEquals"];
            $this->includes("jquery/timemask", "js");
            $this->includes("scripts/case_value_tiers", "js");
            $this->load->view("partial/header");
            $this->load->view("cases/case_value_tiers", $data);
            $this->load->view("partial/footer");
        }
    }
    public function case_value_tiers()
    {
        $result = [];
        if (!$this->input->post(NULL)) {
            $_POST["take"] = 50;
            $_POST["skip"] = 0;
        }
        $this->pageTitle = sprintf($this->lang->line("sheria360_browser_title"), $this->lang->line("reports") . " | " . $this->lang->line("case_value_tiers"));
        $this->load->model("legal_case", "legal_casefactory");
        $systemPreferences = $this->session->userdata("systemPreferences");
        $this->load->model(["case_configuration", "case_type"]);
        $this->legal_case = $this->legal_casefactory->get_instance();
        $ranges = $this->case_configuration->get_value_by_key("caseValueTiers");
        if (empty($ranges)) {
            $result["result"] = [];
        } else {
            $result = $this->legal_case->load_all_cases_per_type_and_ranges($ranges, true);
        }
        $result["perPageList"] = ["5" => 5, "10" => 10, "50" => 50, "100" => 100];
        $result["take"] = $this->input->post("take");
        $result["skip"] = $this->input->post("skip");
        $result["currPage"] = $this->uri->segment(3, "1");
        $result["caseValueCurrency"] = $systemPreferences["caseValueCurrency"];
        $result["direction"] = in_array($this->session->userdata("AUTH_language"), ["arabic", "persian", "hibrew"]) ? "rtl" : "ltr";
        $this->includes("jquery/jquery.fixedheadertable", "js");
        $this->load->view("partial/header");
        $this->load->view("reports/case_value_tiers", $result);
        $this->load->view("partial/footer");
    }
    private function cases_related_models_data()
    {
        $this->load->model("legal_case", "legal_casefactory");
        $this->legal_case = $this->legal_casefactory->get_instance();
        $data = [];
        $this->load->model("workflow_status", "workflow_statusfactory");
        $this->workflow_status = $this->workflow_statusfactory->get_instance();
        $this->load->model(["case_type", "provider_group", "court", "court_type", "court_region", "court_degree", "legal_case_company_role", "legal_case_contact_role"]);
        $this->load->model("custom_field", "custom_fieldfactory");
        $this->custom_field = $this->custom_fieldfactory->get_instance();
        $this->load->model("company", "companyfactory");
        $this->company = $this->companyfactory->get_instance();
        $this->load->model("user", "userfactory");
        $this->user = $this->userfactory->get_instance();
        $this->load->model("contact", "contactfactory");
        $this->contact = $this->contactfactory->get_instance();
        $data["Companies"] = $this->company->load_list([], ["firstLine" => ["" => $this->lang->line("choose_company")]]);
        $data["Case_Statuses"] = $this->workflow_status->loadListWorkflowStatuses("", [], ["firstLine" => ["" => $this->lang->line("choose_case_status")]]);
        $data["case_statuses_values"] = $this->workflow_status->loadListWorkflowStatuses("", [], ["firstLine" => ["" => $this->lang->line("choose_case_status")]]);
        $data["Case_Types"] = $this->case_type->load_list(["where" => ["isDeleted", 0]], ["firstLine" => ["" => $this->lang->line("choose_case_type")]]);
        $data["Provider_Groups"] = $this->provider_group->load_list([], ["firstLine" => ["" => "--"]]);
        $data["Contacts"] = $this->contact->load_list([], ["firstLine" => ["" => $this->lang->line("choose_contact")]]);
        $data["priorities"] = array_combine($this->legal_case->get("priorityValues"), [$this->lang->line("critical"), $this->lang->line("high"), $this->lang->line("medium"), $this->lang->line("low")]);
        $data["categories"] = array_combine(["", "Litigation", "Matter"], ["", $this->lang->line("litigation"), $this->lang->line("legal_matter")]);
        $data["categories"][""] = "";
        $data["archivedValues"] = array_combine($this->legal_case->get("archivedValues"), [$this->lang->line("either"), $this->lang->line("yes"), $this->lang->line("no")]);
        $data["operators"]["text"] = $this->get_filter_operators("text");
        $data["operators"]["number"] = $this->get_filter_operators("number");
        $data["operators"]["date"] = $this->get_filter_operators("date");
        $data["operators"]["time"] = $this->get_filter_operators("time");
        $data["operators"]["list"] = $this->get_filter_operators("list");
        $data["operators"]["group_list"] = $this->get_filter_operators("groupList");
        $data["dataCustomFields"] = $this->custom_field->load_list_per_language($this->legal_case->get("modelName"));
        $data["courtTypes"] = $this->court_type->load_list([], ["firstLine" => [" " => " "]]);
        $data["courtDegrees"] = $this->court_degree->load_list([], ["firstLine" => [" " => " "]]);
        $data["courtRegions"] = $this->court_region->load_list([], ["firstLine" => [" " => " "]]);
        $data["courts"] = $this->court->load_list([], ["firstLine" => [" " => " "]]);
        $data["default_archived_value"] = "either";
        $data["contactRoles"] = $this->legal_case_contact_role->load_list([], ["firstLine" => [" " => " "]]);
        $data["companyRoles"] = $this->legal_case_company_role->load_list([], ["firstLine" => [" " => " "]]);
        return $data;
    }
    public function companies_per_ss_expiry_dates()
    {
        $data = [];
        $this->load->model("company_discharge_social_security", "company_discharge_social_securityfactory");
        $this->company_discharge_social_security = $this->company_discharge_social_securityfactory->get_instance();
        $response = [];
        $system_preferences = $this->session->userdata("systemPreferences");
        $data["hijri_calendar_enabled"] = isset($system_preferences["hijriCalendarFeature"]) && $system_preferences["hijriCalendarFeature"] ? $system_preferences["hijriCalendarFeature"] : 0;
        if ($this->input->post(NULL)) {
            $filter = $this->input->post("filter");
            $data["fixedFilters"] = isset($filter["filters"][0]["filters"]) ? $filter["filters"][0]["filters"] : "";
            $response = $this->company_discharge_social_security->k_load_all_companies_per_ss_expiry_dates($filter, [], $data["hijri_calendar_enabled"]);
            $this->output->set_content_type("application/json");
            $this->output->set_output(json_encode($response));
        } else {
            $response = $this->company_discharge_social_security->k_load_all_companies_per_ss_expiry_dates([], []);
            $this->pageTitle = sprintf($this->lang->line("sheria360_browser_title"), $this->lang->line("company_in_menu"));
            $data["operatorsText"] = $this->get_filter_operators("text");
            $data["operatorsNumbers"] = $this->get_filter_operators("number");
            $data["operatorsList"] = $this->get_filter_operators("list");
            $data["operatorsDate"] = $this->get_filter_operators("date");
            $data["results"] = json_encode($response["data"]);
            $data["totalRows"] = $response["totalRows"];
            $this->includes("kendoui/js/kendo.web.min", "js");
            $this->includes("kendoui/styles/kendo.common-bootstrap.min", "css");
            $this->includes("kendoui/styles/kendo.bootstrap.min", "css");
            $this->includes("jquery/form2js", "js");
            $this->includes("jquery/toObject", "js");
            $this->includes("scripts/reports/companies_per_ss_expiry_dates", "js");
            if ($this->is_auth->is_layout_rtl()) {
                $this->includes("styles/rtl/fixes", "css");
            }
            $this->load->view("partial/header");
            $this->load->view("reports/companies_per_ss_expiry_dates", $data);
            $this->load->view("partial/footer");
        }
    }
    private function cases_reports_data($reportName, $sortable, $perCompanyFirst = false, $perContactFirst = false, $perExternalLawyerFirst = false, $companyoutsourceto_field_function = "companyoutsourceto_field_value", $contactoutsourceto_field_function = "contactoutsourceto_field_value")
    {
        $this->load->helper("text");
        if ($this->input->post(NULL)) {
            $filters = $this->input->post("filter");
            $customFields = $this->input->post("customFields");
            $filters["customFields"] = $customFields ? $customFields : [];
        } else {
            $filters = [];
            $_POST["skip"] = 0;
        }
        if ($reportName == "advanced_case_report") {
            $this->includes("kendoui/js/kendo.web.min", "js");
            $this->includes("kendoui/styles/kendo.common-bootstrap.min", "css");
            $this->includes("kendoui/styles/kendo.bootstrap.min", "css");
            if ($this->is_auth->is_layout_rtl()) {
                $this->includes("styles/rtl/fixes", "css");
            }
            $this->pageTitle = sprintf($this->lang->line("sheria360_browser_title"), $this->lang->line("reports") . " | " . $this->lang->line("advanced_case_report"));
        } else {
            $this->pageTitle = sprintf($this->lang->line("sheria360_browser_title"), $this->lang->line("reports") . " | " . $this->lang->line($reportName));
        }
        $data = $this->cases_related_models_data();
        foreach ($data as $key => $val) {
            if ($key != "lawSuits" && $key != "archivedValues" && $key != "case_statuses_values" && $key != "categories" && $key != "companyRoles" && $key != "contactRoles") {
                if (isset($val[""])) {
                    unset($data[$key][""]);
                }
                if (isset($val[" "])) {
                    unset($data[$key][" "]);
                }
            }
        }
        $query = [];
        $data["reportName"] = $reportName;
        $stringQuery = "";
        if ($perCompanyFirst) {
            $table = "legal_cases_per_company AS legal_cases";
            $data["records"] = $this->legal_case->k_load_all_cases_per_company($filters, $sortable, true, $query, false);
            $data["countGrouping"] = $this->legal_case->get_count_by_a_b_text($query, $sortable, $table);
        } else {
            if ($perContactFirst) {
                $table = "legal_cases_per_contact AS legal_cases";
                $data["records"] = $this->legal_case->k_load_all_cases_per_contact($filters, $sortable, true, $query, $table, false);
                $data["countGrouping"] = $this->legal_case->get_count_by_a_b_text($query, $sortable, $table);
            } else {
                if ($perExternalLawyerFirst) {
                    $table = "legal_cases_per_external_lawyer AS legal_cases";
                    $data["records"] = $this->legal_case->k_load_all_cases_per_contact($filters, $sortable, true, $query, $table, false);
                    $data["countGrouping"] = $this->legal_case->get_count_by_a_b_text($query, $sortable, $table);
                } else {
                    $table = "legal_cases";
                    $data["records"] = $this->legal_case->k_load_all_cases($filters, $sortable, true, $query, $stringQuery, false);
                    if (is_array($sortable) && 0 < count($sortable)) {
                        $data["countGrouping"] = $this->legal_case->get_count_by_a_b_numbers($stringQuery, $query, $sortable, $table);
                    }
                }
            }
        }
        $data["take"] = $this->input->post("take");
        $data["skip"] = $this->input->post("skip");
        $data["currPage"] = $this->uri->segment(3, "1");
        $data["selectedFilters"] = $filters;
        $data["submitBtn2"] = $this->input->post("submitBtn2") == NULL ? "0" : "1";
        $data["perPageList"] = ["10" => 10, "20" => 20, "50" => 50, "100" => 100];
        $data["direction"] = in_array($this->session->userdata("AUTH_language"), ["arabic", "persian", "hibrew"]) ? "rtl" : "ltr";
        $system_preferences = $this->session->userdata("systemPreferences");
        $data["businessWeekDays"] = $system_preferences["businessWeekEquals"];
        $data["businessDayHours"] = $system_preferences["businessDayEquals"];
        $data["companyoutsourceto_field_function"] = $companyoutsourceto_field_function;
        $data["contactoutsourceto_field_function"] = $contactoutsourceto_field_function;
        $this->includes("jquery/jquery.fixedheadertable", "js");
        $this->includes("jquery/form2js", "js");
        $this->includes("jquery/toObject", "js");
        $this->includes(app_url("compressed_asset/system_defaults.js?a=" . random_string("alpha")), "js", true);
        $this->includes("scripts/advance_search_custom_field_template", "js");
        if ($this->is_auth->is_layout_rtl()) {
            $this->includes("styles/rtl/fixes", "css");
        }
        $this->includes("scripts/reports/cases_reports", "js");
        $this->includes("jquery/timemask", "js");
        $this->load->library("TimeMask");
        $this->load->view("partial/header");
        $this->load->view("reports/" . $reportName, $data);
        $this->load->view("partial/footer");
    }
    public function advanced_case_report()
    {
        $sortable = [];
        $this->cases_reports_data("advanced_case_report", $sortable);
    }
    public function matters_attachments_report()
    {
        if ($this->input->post(NULL)) {
            if (!$this->input->is_ajax_request()) {
                redirect("reports");
            }
            $response = [];
            $sortable = $this->input->post("sort");
            $this->load->model("document_management_system", "document_management_systemfactory");
            $this->document_management_system = $this->document_management_systemfactory->get_instance();
            $response = $this->document_management_system->get_matters_attachments_report_info($sortable);
            $this->output->set_content_type("application/json");
            $this->output->set_output(json_encode($response));
        } else {
            $this->pageTitle = sprintf($this->lang->line("sheria360_browser_title"), $this->lang->line("reports"));
            $this->includes("kendoui/styles/kendo.common-bootstrap.min", "css");
            $this->includes("kendoui/styles/kendo.bootstrap.min", "css");
            $this->includes("jquery/css/chosen", "css");
            $this->includes("jquery/chosen.min", "js");
            $this->includes("jquery/form2js", "js");
            $this->includes("jquery/toObject", "js");
            $this->includes("kendoui/js/kendo.web.min", "js");
            $this->includes("scripts/matters_attachments_report", "js");
            if ($this->is_auth->is_layout_rtl()) {
                $this->includes("styles/rtl/fixes", "css");
            }
            $this->load->view("partial/header");
            $this->load->view("reports/matters_attachments_report");
            $this->load->view("partial/footer");
        }
    }
    public function case_other_reports()
    {
        $this->pageTitle = sprintf($this->lang->line("sheria360_browser_title"), $this->lang->line("reports") . " | " . $this->lang->line("saved_reports"));
        $this->includes("jquery/css/chosen", "css");
        $this->includes("jquery/chosen.min", "js");
        $this->includes("kendoui/js/kendo.web.min", "js");
        $this->includes("kendoui/styles/kendo.common-bootstrap.min", "css");
        $this->includes("kendoui/styles/kendo.bootstrap.min", "css");
        $this->includes("jquery/form2js", "js");
        $this->includes("jquery/toObject", "js");

        $this->load->view("partial/header");
        $this->load->view("reports/case_other_reports");
        $this->load->view("partial/footer");
    }

    private function _load_related_models($litigationFlag = false)
    {
        $data = [];
        $this->load->model("workflow_status", "workflow_statusfactory");
        $this->workflow_status = $this->workflow_statusfactory->get_instance();
        $this->load->model(["case_type", "provider_group"]);
        $this->load->model("legal_case_stage", "legal_case_stagefactory");
        $this->legal_case_stage = $this->legal_case_stagefactory->get_instance();
        $this->load->model("company", "companyfactory");
        $this->company = $this->companyfactory->get_instance();
        $this->load->model("contact", "contactfactory");
        $this->contact = $this->contactfactory->get_instance();
        $data["Companies"] = $this->company->load_list([], ["firstLine" => ["" => $this->lang->line("choose_company")]]);
        $data["case_statuses"] = $this->workflow_status->loadListWorkflowStatuses("", [], ["firstLine" => ["" => $this->lang->line("choose_case_status")]]);
        if ($litigationFlag) {
            $data["Case_Types"] = $this->case_type->load_list(["where" => [["litigation", "yes"], ["isDeleted", 0]]], ["firstLine" => ["" => $this->lang->line("choose_case_type")]]);
            $data["caseStages"] = $this->legal_case_stage->load_list_per_case_category_per_language("litigation");
        } else {
            $data["Case_Types"] = $this->case_type->load_list(["where" => [["corporate", "yes"], ["isDeleted", 0]]], ["firstLine" => ["" => $this->lang->line("choose_case_type")]]);
            $data["caseStages"] = $this->legal_case_stage->load_list_per_case_category_per_language("corporate");
        }
        $this->load->model("legal_case_client_position", "legal_case_client_positionfactory");
        $this->legal_case_client_position = $this->legal_case_client_positionfactory->get_instance();
        $data["clientPositions"] = $this->legal_case_client_position->load_list_per_language();
        $data["Provider_Groups"] = $this->provider_group->load_list([], ["firstLine" => ["" => "--"]]);
        $data["Contacts"] = $this->contact->load_list([], ["firstLine" => ["" => $this->lang->line("choose_contact")]]);
        $data["priorities"] = array_combine($this->legal_case->get("priorityValues"), [$this->lang->line("critical"), $this->lang->line("high"), $this->lang->line("medium"), $this->lang->line("low")]);
        $data["categories"] = array_combine(["", "Litigation", "Matter"], ["", $this->lang->line("litigation"), $this->lang->line("legal_matter")]);
        $data["categories"][""] = "";
        $data["externalizeLawyers"] = array_combine($this->legal_case->get("externalizeLawyersValues"), ["", $this->lang->line("yes"), $this->lang->line("no")]);
        $data["externalizeLawyers"][""] = "";
        $data["usersProviderGroup"] = [];
        return $data;
    }
    private function get_report_saved_filters($reportId)
    {
        $response = $this->user_report->get_value($reportId);
        $data["data"]["title"] = $response["keyName"];
        $result = unserialize($response["keyValue"]);
        $data["data"]["report_type"] = $result["report_type"];
        $data["data"]["sort"] = $result["sort"];
        $data["data"]["take"] = $result["take"];
        $data["data"]["skip"] = $result["skip"];
        $default_columns = ["case_id", "subject"];
        $columns = $result["columns"];
        $columns = empty($columns) ? $default_columns : $columns;
        $data["data"]["columns"] = $columns;
        $data["data"]["selectedFilters"] = $result["advancedFilters"];
        $filters = ["logic" => $data["data"]["selectedFilters"]["logic"], "filters" => $data["data"]["selectedFilters"]["filters"]];
        $customFields = isset($data["data"]["selectedFilters"]["customFields"]) ? $data["data"]["selectedFilters"]["customFields"] : [];
        $data["data"]["position"] = $result["position"];
        $data["data"]["cases_category"] = $result["cases_category"];
        $data["data"]["limits"] = $result["limits"];
        $data["data"]["SelectedReportId"] = $reportId;
        $data["filters"] = $filters;
        $data["customFields"] = $customFields;
        $data["columns"] = $columns;
        return $data;
    }
    public function report_builder()
    {
        $this->load->model("user_report");
        if ($this->input->is_ajax_request()) {
            $routine = $this->input->post("routine") ? $this->input->post("routine") : "";
            if (method_exists($this, $routine)) {
                $response = $this->{$routine}();
                $this->output->set_content_type("application/json");
                $this->output->set_output(json_encode($response));
                return NULL;
            }
        }
        $this->legal_case = $this->legal_casefactory->get_instance();
        $this->pageTitle = sprintf($this->lang->line("sheria360_browser_title"), $this->lang->line("reports") . " | " . $this->lang->line("report_builder"));
        if (!$this->input->post(NULL)) {
            $data["take"] = 20;
            $data["skip"] = 0;
        }
        $formData = $this->_load_related_models(false);
        $formData["id"] = "";
        $formData["priorities"] = array_combine($this->legal_case->get("priorityValues"), [$this->lang->line("critical"), $this->lang->line("high"), $this->lang->line("medium"), $this->lang->line("low")]);
        $formData["categories"] = array_combine($this->legal_case->get("categoryValues"), ["", $this->lang->line("litigation"), $this->lang->line("legal_matter"), $this->lang->line("IP")]);
        $formData["externalizeLawyers"] = array_combine($this->legal_case->get("externalizeLawyersValues"), ["", $this->lang->line("yes"), $this->lang->line("no")]);
        $this->legal_case->reset_fields();
        $this->legal_case->set_field("arrivalDate", date("Y-m-d", time()));
        $this->legal_case->set_field("priority", "medium");
        $formData["externalizeLawyers"][""] = "";
        $data["formData"] = $formData;
        unset($data["formData"]["caseStages"][""]);
        $data["archivedValues"] = array_combine($this->legal_case->get("archivedValues"), ["", $this->lang->line("yes"), $this->lang->line("no")]);
        $data["operators"]["text"] = $this->get_filter_operators("text");
        $data["operators"]["big_text"] = $this->get_filter_operators("bigText");
        $data["operators"]["number"] = $this->get_filter_operators("number");
        $data["operators"]["date"] = $this->get_filter_operators("date");
        $data["operators"]["time"] = $this->get_filter_operators("time");
        $data["operators"]["list"] = $this->get_filter_operators("list");
        $data["operators"]["group_list"] = $this->get_filter_operators("groupList");
        $this->load->model(["court", "court_type", "court_region", "court_degree"]);
        $this->load->model("custom_field", "custom_fieldfactory");
        $this->custom_field = $this->custom_fieldfactory->get_instance();
        $data["dataCustomFields"] = $this->custom_field->load_list_per_language($this->legal_case->get("modelName"));
        $data["courtTypes"] = $this->court_type->load_list([], ["firstLine" => [" " => " "]]);
        $data["courtDegrees"] = $this->court_degree->load_list([], ["firstLine" => [" " => " "]]);
        $data["courtRegions"] = $this->court_region->load_list([], ["firstLine" => [" " => " "]]);
        $data["courts"] = $this->court->load_list([], ["firstLine" => [" " => " "]]);
        $data["defaultArchivedValue"] = "no";
        $parameters = [];
        $data["reports"] = $this->get_reports();
        $data["all_columns"] = ["case_id", "internalReference", "subject", "description", "latest_development", "providerGroup", "assignee", "requestedByName", "case_type", "priority", "case_status", "statusComments", "case_stage", "caseArrivalDate", "arrivalDate", "dueDate", "closedOn", "clientName", "client_position", "success_probability", "opponentNationalities", "court", "litigationExternalRef", "outsource_to", "estimatedEffort", "effectiveEffort", "caseValue", "judgmentValue", "recoveredValue", "archived", "last_hearing", "judgment", "sentenceDate", "reasons_of_postponement_of_last_hearing", "opponent_foreign_name", "client_foreign_name", "court_type", "court_region", "court_degree", "first_stage", "first_stage_judgment", "first_judgment_date"];
        if (!empty($data["columns"])) {
            $result = array_unique(array_merge((array) $data["columns"], (array) $data["all_columns"]));
            $data["all_columns"] = $result;
        }
        $this->load->model("legal_case_client_position", "legal_case_client_positionfactory");
        $this->legal_case_client_position = $this->legal_case_client_positionfactory->get_instance();
        $this->load->model("workflow_status", "workflow_statusfactory");
        $this->workflow_status = $this->workflow_statusfactory->get_instance();
        $data["case_status_options"] = $this->workflow_status->loadListWorkflowStatuses("", [], ["firstLine" => ["" => $this->lang->line("choose_case_status")]]);
        $systemPreferences = $this->session->userdata("systemPreferences");
        $data["case_status"] = !empty($data["case_status"]) ? $data["case_status"] : "";
        $data["Client_Position"] = $this->legal_case_client_position->load_list_per_language();
        $this->load->model("legal_case_success_probability", "legal_case_success_probabilityfactory");
        $this->legal_case_success_probability = $this->legal_case_success_probabilityfactory->get_instance();
        $data["success_probabilities"] = $this->legal_case_success_probability->load_list_per_language();
        $data["cases_category_options"] = ["Litigation" => $this->lang->line("only_litigation"), "Matter" => $this->lang->line("only_legal_matters"), "IP" => $this->lang->line("only_IP"), "All" => $this->lang->line("all")];
        $this->includes("jquery/css/ui.multiselect", "css");
        $this->includes("jquery/css/chosen", "css");
        $this->includes("jquery/chosen.min", "js");
        $this->includes("jquery/ui.multiselect", "js");
        $this->includes("kendoui/js/kendo.web.min", "js");
        $this->includes("kendoui/styles/kendo.common-bootstrap.min", "css");
        $this->includes("kendoui/styles/kendo.bootstrap.min", "css");
        $this->includes("jquery/form2js", "js");
        $this->includes("jquery/toObject", "js");
        $this->includes("scripts/reports/report_builder", "js");
        $this->includes("scripts/advance_search_custom_field_template", "js");
        $this->load->view("partial/header");
        $this->load->view("reports/report_builder/report_builder", $data);
        $this->load->view("partial/footer");
    }
    public function report_builder_view($report_id = 0)
    {
        $this->load->model("user_report");
        if ($this->input->is_ajax_request()) {
            $routine = $this->input->post("routine") ? $this->input->post("routine") : "";
            if (method_exists($this, $routine)) {
                $response = $this->{$routine}();
                $this->output->set_content_type("application/json");
                $this->output->set_output(json_encode($response));
                return NULL;
            }
        }
        $this->legal_case = $this->legal_casefactory->get_instance();
        $this->pageTitle = sprintf($this->lang->line("sheria360_browser_title"), $this->lang->line("reports") . " | " . $this->lang->line("report_builder"));
        if (0 < $report_id) {
            $report_permis = $this->get_allowed_reports();
            if (in_array($report_id, $report_permis["created_reports"]) || in_array($report_id, $report_permis["shared_reports"])) {
                $this->load->model(["user_profile", "shared_report"]);
                $shared_users = $this->shared_report->get_shared_file_users($report_id);
                for ($i = 0; $i < count($shared_users); $i++) {
                    if ($this->session->userdata["AUTH_user_id"] != $shared_users[$i]["user_id"]) {
                        $user_profile = $this->user_profile->get_profile_by_id($shared_users[$i]["user_id"]);
                        $data["sharedWithUsers"][] = $shared_users[$i]["user_id"];
                        $data["sharedWithUsersStatus"][$shared_users[$i]["user_id"]] = $user_profile["status"];
                    }
                }
                $result = $this->get_report_saved_filters($report_id);
                $data = $result["data"];
                $response = $this->user_report->get_value($report_id);
                $data["reportTitle"] = $response["keyName"];
            } else {
                $this->set_flashmessage("warning", $this->lang->line("you_do_not_have_enough_previlages_to_access_the_requested_page"));
                redirect("dashboard");
            }
        }
        $data["take"] = 20;
        $data["skip"] = 0;
        $formData = $this->_load_related_models(false);
        $formData["id"] = "";
        $formData["priorities"] = array_combine($this->legal_case->get("priorityValues"), [$this->lang->line("critical"), $this->lang->line("high"), $this->lang->line("medium"), $this->lang->line("low")]);
        $formData["categories"] = array_combine($this->legal_case->get("categoryValues"), ["", $this->lang->line("litigation"), $this->lang->line("legal_matter"), $this->lang->line("IP")]);
        $formData["externalizeLawyers"] = array_combine($this->legal_case->get("externalizeLawyersValues"), ["", $this->lang->line("yes"), $this->lang->line("no")]);
        $this->legal_case->reset_fields();
        $this->legal_case->set_field("arrivalDate", date("Y-m-d", time()));
        $this->legal_case->set_field("priority", "medium");
        $formData["externalizeLawyers"][""] = "";
        $data["formData"] = $formData;
        unset($data["formData"]["caseStages"][""]);
        $data["archivedValues"] = array_combine($this->legal_case->get("archivedValues"), ["", $this->lang->line("yes"), $this->lang->line("no")]);
        $data["operators"]["text"] = $this->get_filter_operators("text");
        $data["operators"]["big_text"] = $this->get_filter_operators("bigText");
        $data["operators"]["number"] = $this->get_filter_operators("number");
        $data["operators"]["date"] = $this->get_filter_operators("date");
        $data["operators"]["time"] = $this->get_filter_operators("time");
        $data["operators"]["list"] = $this->get_filter_operators("list");
        $data["operators"]["group_list"] = $this->get_filter_operators("groupList");
        $this->load->model(["court", "court_type", "court_region", "court_degree"]);
        $this->load->model("custom_field", "custom_fieldfactory");
        $this->custom_field = $this->custom_fieldfactory->get_instance();
        $data["dataCustomFields"] = $this->custom_field->load_list_per_language($this->legal_case->get("modelName"));
        $data["courtTypes"] = $this->court_type->load_list([], ["firstLine" => [" " => " "]]);
        $data["courtDegrees"] = $this->court_degree->load_list([], ["firstLine" => [" " => " "]]);
        $data["courtRegions"] = $this->court_region->load_list([], ["firstLine" => [" " => " "]]);
        $data["courts"] = $this->court->load_list([], ["firstLine" => [" " => " "]]);
        $data["defaultArchivedValue"] = "no";
        $parameters = [];
        $data["reports"] = $this->get_reports();
        $data["all_columns"] = ["case_id", "internalReference", "subject", "description", "latest_development", "providerGroup", "assignee", "requestedByName", "case_type", "priority", "case_status", "statusComments", "case_stage", "caseArrivalDate", "arrivalDate", "dueDate", "closedOn", "clientName", "client_position", "success_probability", "opponentNationalities", "court", "litigationExternalRef", "outsource_to", "estimatedEffort", "effectiveEffort", "caseValue", "judgmentValue", "recoveredValue", "archived", "last_hearing", "judgment", "sentenceDate", "reasons_of_postponement_of_last_hearing", "opponent_foreign_name", "client_foreign_name", "court_type", "court_region", "court_degree", "first_stage", "first_stage_judgment", "first_judgment_date"];
        if (!empty($data["columns"])) {
            $result = array_unique(array_merge((array) $data["columns"], (array) $data["all_columns"]));
            $data["all_columns"] = $result;
        }
        $this->load->model("legal_case_client_position", "legal_case_client_positionfactory");
        $this->legal_case_client_position = $this->legal_case_client_positionfactory->get_instance();
        $this->load->model("workflow_status", "workflow_statusfactory");
        $this->workflow_status = $this->workflow_statusfactory->get_instance();
        $data["case_status_options"] = $this->workflow_status->loadListWorkflowStatuses("", [], ["firstLine" => ["" => $this->lang->line("choose_case_status")]]);
        $systemPreferences = $this->session->userdata("systemPreferences");
        $data["case_status"] = !empty($data["case_status"]) ? $data["case_status"] : "";
        $data["Client_Position"] = $this->legal_case_client_position->load_list_per_language();
        $this->load->model("legal_case_success_probability", "legal_case_success_probabilityfactory");
        $this->legal_case_success_probability = $this->legal_case_success_probabilityfactory->get_instance();
        $data["success_probabilities"] = $this->legal_case_success_probability->load_list_per_language();
        $data["cases_category_options"] = ["Litigation" => $this->lang->line("only_litigation"), "Matter" => $this->lang->line("only_legal_matters"), "IP" => $this->lang->line("only_IP"), "All" => $this->lang->line("all")];
        $this->includes("jquery/css/ui.multiselect", "css");
        $this->includes("jquery/css/chosen", "css");
        $this->includes("jquery/chosen.min", "js");
        $this->includes("jquery/ui.multiselect", "js");
        $this->includes("kendoui/js/kendo.web.min", "js");
        $this->includes("kendoui/styles/kendo.common-bootstrap.min", "css");
        $this->includes("kendoui/styles/kendo.bootstrap.min", "css");
        $this->includes("jquery/form2js", "js");
        $this->includes("jquery/toObject", "js");
        $this->includes("scripts/reports/report_builder", "js");
        $this->includes("scripts/advance_search_custom_field_template", "js");
        $this->load->view("partial/header");
        $this->load->view("reports/report_builder/report_builder", $data);
        $this->load->view("partial/footer");
    }
    public function report_builder_list()
    {
        if (!$this->input->is_ajax_request()) {
            show_404();
        }
        $this->load->model("user_report");
        $this->legal_case = $this->legal_casefactory->get_instance();
        $this->pageTitle = sprintf($this->lang->line("sheria360_browser_title"), $this->lang->line("reports") . " | " . $this->lang->line("report_builder"));
        if ($this->input->post(NULL)) {
            $data["report_type"] = $this->input->post("report_type");
            $default_columns = ["case_id", "subject"];
            $filter = $this->input->post("filter");
            if ($data["report_type"] != "HTML") {
                $filter = json_decode($filter, true);
            }
            $reportTitle = $this->input->post("reportTitle");
            $customFields = isset($filter["customFields"]) ? $filter["customFields"] : [];
            $data["take"] = $this->input->post("take");
            $data["skip"] = $this->input->post("skip");
            $sortable = $this->input->post("sort");
            $this->load->model("custom_field", "custom_fieldfactory");
            $this->custom_field = $this->custom_fieldfactory->get_instance();
            $dataCustomFields = $this->custom_field->load_list_per_language($this->legal_case->get("modelName"));
            if (!empty($dataCustomFields)) {
                foreach ($dataCustomFields as $key1 => $value1) {
                    if (!empty($dataCustomFields[$key1])) {
                        $customs[$dataCustomFields[$key1]["id"]] = $dataCustomFields[$key1]["customName"];
                    }
                }
            }
            $data["customs"] = $customs;
            $filter["customFields"] = $customFields ? $customFields : [];
            $filter = $filter ? $filter : [];
            $data["selectedFilters"] = $filter;
            $data["position"] = $this->input->post("Client_Position");
            $data["cases_category"] = $this->input->post("cases_category");
            $data["limits"] = $this->input->post("limits");
            $submit = $this->input->post("submit");
            if ($submit == "yes") {
                $data["sort"] = $this->input->post("sort");
            } else {
                $data["sort"] = $this->input->post("sortData");
                $data["sort"] = json_decode($data["sort"], true);
            }
            if ($data["report_type"] != "HTML") {
                $_POST["columns"] = json_decode($this->input->post("columns"), true);
            }
            $columns = !$this->input->post("columns") ? $default_columns : $this->input->post("columns");
            array_push($columns, "category");
            $data["columns"] = $columns;
        }
        if (isset($data["columns"])) {
            $columns_selected = $this->legal_case->jasper_load_all_cases($columns, $data["customs"]);
            if ($data["report_type"] == "PDF" || $data["report_type"] == "EXCEL") {
                $response = $this->legal_case->get_legal_cases_by_Position_and_status($data, $columns_selected, $filter, $sortable, true);
            } else {
                $response = $this->legal_case->get_legal_cases_by_Position_and_status($data, $columns_selected, $filter, $sortable);
            }
            if (!empty($reportTitle)) {
                $title = $reportTitle;
            } else {
                $title = $this->lang->line("excel_dynamic_report");
            }
        }
        if ($data["report_type"] == "HTML") {
            try {
                $response["columns"] = $data["columns"];
                $advancedFilters = [];
                if (isset($data["selectedFilters"]["filters"])) {
                    $advancedFilters = $data["selectedFilters"];
                }
                $dataSerach = [];
                $dataSerach["columns"] = $data["columns"];
                $dataSerach["advancedFilters"] = $advancedFilters;
                $dataSerach["report_type"] = $data["report_type"];
                $dataSerach["sort"] = $data["sort"];
                $dataSerach["take"] = $data["take"];
                $dataSerach["skip"] = $data["skip"];
                $dataSerach["position"] = $data["position"];
                $dataSerach["cases_category"] = $data["cases_category"];
                $dataSerach["limits"] = $data["limits"];
                $response["dataSerach"] = $dataSerach;
                foreach ($response["data"] as $key => $value) {
                    if (!empty($value["opponentNationalities"])) {
                        $response["data"][$key]["opponentNationalities"] = str_replace("&nbsp;", " ", $value["opponentNationalities"]);
                    }
                    if (!empty($value["outsource_to"])) {
                        $last_char = strlen($value["outsource_to"]);
                        if ($value["outsource_to"][$last_char - 1] == ",") {
                            $response["data"][$key]["outsource_to"] = substr($value["outsource_to"], 0, -1);
                        }
                    }
                    foreach ($value as $key1 => $value1) {
                        if (is_numeric($key1)) {
                            $val = $response["data"][$key][$key1];
                            unset($response["data"][$key][$key1]);
                            $response["data"][$key]["custom_" . $key1] = $val;
                        }
                    }
                }
                $this->output->set_content_type("application/json");
                $this->output->set_output(json_encode($response));
            } catch (RESTRequestException $e) {
                $response["error"] = true;
                $this->output->set_content_type("application/json");
                $this->output->set_output(json_encode($response));
            }
        }
    }
    public function report_builder_pdf()
    {
        $data = $this->report_builder_export_common();
        $filter = $this->input->post("filter");
        if ($data["report_type"] != "HTML") {
            $filter = json_decode($filter, true);
        }
        $sortable = $this->input->post("sort");
        if (isset($data["columns"])) {
            $columns_selected = $this->legal_case->jasper_load_all_cases($data["columns"], $data["customs"]);
            if ($data["report_type"] == "PDF" || $data["report_type"] == "EXCEL") {
                $response = $this->legal_case->get_legal_cases_by_Position_and_status($data, $columns_selected, $filter, $sortable, true);
            } else {
                $response = $this->legal_case->get_legal_cases_by_Position_and_status($data, $columns_selected, $filter, $sortable);
            }
            if (!empty($reportTitle)) {
                $title = $reportTitle;
            } else {
                $title = $this->lang->line("excel_dynamic_report");
            }
            $pdf_width = sizeof($data["columns"]) * 60;
            $pdf_width < 500 ? $pdf_width = 500 : "";
            $data["title"] = $title;
            $data["report"] = $response["data"];
            $systemPreferences1 = $this->system_preference->get_key_groups();
            if (isset($systemPreferences1["SystemValues"]["exportFilters"]) && $systemPreferences1["SystemValues"]["exportFilters"] == "1") {
                $filter_info = json_decode($this->input->post("filter_info"), true);
                $data["filters"] = $this->load->view("reports/pdf_filter", ["filter_info" => $filter_info], true);
            }
            $html = $this->load->view("reports/dynamic_report_pdf", $data, true);
            $file_name = $title . "_" . date("Ymd");
            $this->load->helper(["dompdf", "file"]);
            pdf_create($html, $file_name, true, $pdf_width);
        }
    }
    public function report_builder_excel()
    {
        $data = $this->report_builder_export_common();
        $filter = $this->input->post("filter");
        $filter_info = json_decode($this->input->post("filter_info"), true);
        if ($data["report_type"] != "HTML") {
            $filter = json_decode($filter, true);
        }
        $sortable = $this->input->post("sort");
        if (isset($data["columns"])) {
            $columns_selected = $this->legal_case->jasper_load_all_cases($data["columns"], $data["customs"]);
            if ($data["report_type"] == "PDF" || $data["report_type"] == "EXCEL") {
                $response = $this->legal_case->get_legal_cases_by_Position_and_status($data, $columns_selected, $filter, $sortable, true);
            } else {
                $response = $this->legal_case->get_legal_cases_by_Position_and_status($data, $columns_selected, $filter, $sortable);
            }
            if (!empty($reportTitle)) {
                $title = $reportTitle;
            } else {
                $title = $this->lang->line("excel_dynamic_report");
            }
            $data["report"] = $response["data"];
            $data["headerBg"] = "#cccccc";
            $this->load->helper("export_xlsx_helper");
            $filename = urlencode(str_replace(" ", "_", trim($title)));
            $content_sheet = $this->load->view("excel/header_new", "", true);
            $content_sheet .= $this->load->view("excel/dynamic_report", $data, true);
            $content_sheet .= $this->load->view("excel/footer", "", true);
            $filters_sheet = $this->load->view("excel/filter_info", ["filter_info" => $filter_info], true);
            return create_xlsx_report($content_sheet, $filters_sheet, $filename);
        }
    }
    private function report_builder_export_common()
    {
        $this->load->model("user_report");
        $this->load->helper("download");
        $this->legal_case = $this->legal_casefactory->get_instance();
        $this->pageTitle = sprintf($this->lang->line("sheria360_browser_title"), $this->lang->line("reports") . " | " . $this->lang->line("report_builder"));
        $routine = $this->input->post("routine");
        if (!$this->input->post(NULL) && !$reportId) {
            $data["take"] = 20;
            $data["skip"] = 0;
        }
        if ($this->input->post(NULL) && !method_exists($this, $routine)) {
            $data["report_type"] = $this->input->post("report_type");
            $default_columns = ["case_id", "subject"];
            $reportTitle = $this->input->post("reportTitle");
            $customFields = isset($filter["customFields"]) ? $filter["customFields"] : [];
            $data["take"] = $this->input->post("take");
            $data["skip"] = $this->input->post("skip");
            $this->load->model("custom_field", "custom_fieldfactory");
            $this->custom_field = $this->custom_fieldfactory->get_instance();
            $dataCustomFields = $this->custom_field->load_list_per_language($this->legal_case->get("modelName"));
            if (!empty($dataCustomFields)) {
                foreach ($dataCustomFields as $key1 => $value1) {
                    if (!empty($dataCustomFields[$key1])) {
                        $customs[$dataCustomFields[$key1]["id"]] = $dataCustomFields[$key1]["customName"];
                    }
                }
            }
            $data["customs"] = $customs;
            $filter["customFields"] = $customFields ? $customFields : [];
            $filter = $filter ? $filter : [];
            $data["selectedFilters"] = $filter;
            $data["position"] = $this->input->post("Client_Position");
            $data["cases_category"] = $this->input->post("cases_category");
            $data["limits"] = $this->input->post("limits");
            $submit = $this->input->post("submit");
            if ($submit == "yes") {
                $data["sort"] = $this->input->post("sort");
            } else {
                $data["sort"] = $this->input->post("sortData");
                $data["sort"] = json_decode($data["sort"], true);
            }
            if ($data["report_type"] != "HTML") {
                $_POST["columns"] = json_decode($this->input->post("columns"), true);
            }
            $columns = !$this->input->post("columns") ? $default_columns : $this->input->post("columns");
            $data["columns"] = $columns;
            return $data;
        }
    }
    private function report_shared_with()
    {
        $data = [];
        $SelectedReportId = $this->input->post("SelectedReportId");
        if (!empty($SelectedReportId)) {
            $this->load->model(["user_profile", "shared_report"]);
            $shared_users = $this->shared_report->get_shared_file_users($SelectedReportId);
            $data["sharedWithUsers"] = [];
            for ($i = 0; $i < count($shared_users); $i++) {
                $user_profile = $this->user_profile->get_profile_by_id($shared_users[$i]["user_id"]);
                $data["sharedWithUsers"][$shared_users[$i]["user_id"]] = $user_profile["firstName"] . " " . $user_profile["lastName"];
                $data["sharedWithUsersStatus"][$shared_users[$i]["user_id"]] = $user_profile["status"];
            }
        }
        $response["html"] = $this->load->view("reports/report_shared_with_form", $data, true);
        return $response;
    }
    public function report_builder_save()
    {
        $routine = $this->input->post("routine");
        if ($this->input->is_ajax_request() && method_exists($this, $routine)) {
            $response = $this->{$routine}();
            $this->output->set_content_type("application/json");
            $this->output->set_output(json_encode($response));
        }
    }
    private function save_report()
    {
        $this->load->model("user_report");
        $response = $this->user_report->set_value();
        return $response;
    }
    private function delete_report()
    {
        if ($this->input->is_ajax_request()) {
            $returnValue = true;
            $report_id = $this->input->post("report_id");
            $this->load->model("shared_report");
            $data = $this->shared_report->get_shared_reports_by_id($report_id);
            if (!empty($data)) {
                $returnValue = $this->shared_report->delete_shared_report($report_id);
            }
            if ($returnValue) {
                $this->load->model("user_report");
                $returnValue = $this->user_report->delete_user_report($report_id);
            }
            return $returnValue;
        }
    }
    private function edit_report()
    {
        $this->load->model("user_report");
        $response = $this->user_report->edit_report();
        return $response;
    }
    private function get_reports()
    {
        $this->load->model("shared_report");
        $data["created_reports"] = $this->user_report->get_created_reports();
        $data["shared_reports"] = $this->shared_report->get_shared_reports();
        return $data;
    }
    private function get_allowed_reports()
    {
        $this->load->model("shared_report");
        $data["created_reports"] = $this->user_report->get_created_permis();
        $data["shared_reports"] = $this->shared_report->get_shared_permis();
        return $data;
    }
    public function cases_per_assignee_per_status()
    {
        $fields = [["field" => "legal_cases.user_id", "dir" => "asc"], ["field" => "legal_cases.case_status_id", "dir" => "asc"]];
        $sortable = $this->get_legal_cases_sortable_fields($fields, true);
        if (!$this->input->post(NULL)) {
            $_POST["take"] = 100;
        }
        $this->cases_reports_data("cases_per_assignee_per_status", $sortable);
    }
    public function cases_per_assignee_per_due_date()
    {
        $fields = [["field" => "legal_cases.user_id", "dir" => "asc"], ["field" => "legal_cases.dueDate", "dir" => "asc"]];
        $sortable = $this->get_legal_cases_sortable_fields($fields, true);
        if (!$this->input->post(NULL)) {
            $_POST["take"] = 100;
        }
        $this->cases_reports_data("cases_per_assignee_per_due_date", $sortable);
    }
    public function cases_per_company_per_assignee()
    {
        $sortable = [["field" => "legal_cases.company", "dir" => "asc"], ["field" => "legal_cases.user_id", "dir" => "asc"]];
        if (!$this->input->post(NULL)) {
            $_POST["take"] = 100;
        }
        $this->cases_reports_data("cases_per_company_per_assignee", $sortable, true, NULL, NULL, "companyoutsourceto_field_value_from_view", "contactoutsourceto_field_value_from_view");
    }
    public function cases_per_contact_per_assignee()
    {
        $sortable = [["field" => "contact", "dir" => "asc"], ["field" => "legal_cases.user_id", "dir" => "asc"]];
        if (!$this->input->post(NULL)) {
            $_POST["take"] = 100;
        }
        $this->cases_reports_data("cases_per_contact_per_assignee", $sortable, false, true, NULL, "companyoutsourceto_field_value_from_view", "contactoutsourceto_field_value_from_view");
    }
    public function cases_per_company_per_role()
    {
        $sortable = [["field" => "company", "dir" => "asc"], ["field" => "role", "dir" => "asc"]];
        if (!$this->input->post(NULL)) {
            $_POST["take"] = 100;
        }
        $this->cases_reports_data("cases_per_company_per_role", $sortable, true, NULL, NULL, "companyoutsourceto_field_value_from_view", "contactoutsourceto_field_value_from_view");
    }
    public function cases_per_contact_per_role()
    {
        $sortable = [["field" => "contact", "dir" => "asc"], ["field" => "role", "dir" => "asc"]];
        if (!$this->input->post(NULL)) {
            $_POST["take"] = 100;
        }
        $this->cases_reports_data("cases_per_contact_per_role", $sortable, false, true, NULL, "companyoutsourceto_field_value_from_view", "contactoutsourceto_field_value_from_view");
    }
    public function cases_per_external_lawyer_per_status()
    {
        $sortable = [["field" => "contact", "dir" => "asc"], ["field" => "legal_cases.case_status_id", "dir" => "asc"]];
        if (!$this->input->post(NULL)) {
            $_POST["take"] = 100;
        }
        $this->cases_reports_data("cases_per_external_lawyer_per_status", $sortable, false, false, true, "companyoutsourceto_field_value_from_view", "contactoutsourceto_field_value_from_view");
    }
    private function fetch_company_accessibility($id)
    {
        $companyDataFetched = $this->company->fetch_company_accessibility($id);
        if (empty($companyDataFetched)) {
            $this->set_flashmessage("error", $this->lang->line("invalid_record"));
            redirect("reports");
        }
    }
    public function contacts_per_group_of_companies($company_id = 0)
    {
        $this->pageTitle = sprintf($this->lang->line("sheria360_browser_title"), $this->lang->line("reports") . " | " . $this->lang->line("contacts_per_group_of_companies"));
        $this->load->model("company", "companyfactory");
        $this->company = $this->companyfactory->get_instance();
        if (0 < $company_id) {
            $data["grid"] = $this->company->contacts_per_group_of_companies($company_id);
            $data["conatcts"] = $this->company->contacts_of_group_company($company_id);
            $data["company_group"] = "";
            $data["company_group_id"] = "";
            $com = [];
            foreach ($data["grid"] as $k => $v) {
                $data["company_group"] = $v["GroupCompany"];
                $data["company_group_id"] = $v["GroupCompanyId"];
                if (array_key_exists($v["id"], $com)) {
                    array_push($com[$v["id"]], $data["grid"][$k]);
                } else {
                    $com[$v["id"]] = [];
                    array_push($com[$v["id"]], $data["grid"][$k]);
                }
            }
            $data["grid"] = $com;
            $config = [];
        } else {
            $config = ["value" => "name", "firstLine" => ["" => $this->lang->line("choose_company")]];
        }
        $data["companies"] = $this->company->load_list(["select" => ["id,name"], "where" => ["company_id is null and category = 'Group'"]], $config);
        $this->load->view("partial/header");
        $this->load->view("reports/contacts_per_group_of_companies", $data);
        $this->load->view("partial/footer");
    }
    private function get_conflict_of_interset_data($contact_company_type, $contact_company_id)
    {
        $data = [];
        if (!empty($contact_company_id) && is_numeric($contact_company_id) && !empty($contact_company_type) && in_array($contact_company_type, ["contacts", "companies"])) {
            $model = $contact_company_type == "contacts" ? "contact" : "company";
            $model_factory = $model . "factory";
            $related_cases_method = $contact_company_type == "contacts" ? "contact_related_cases" : "company_related_cases";
            $related_companies_method = $contact_company_type == "contacts" ? "contact_related_companies" : "company_related_companies";
            $this->load->model($model, $model_factory);
            $this->{$model} = $this->{$model_factory}->get_instance();
            $this->{$model}->fetch($contact_company_id);
            $data["contact_company_id"] = $contact_company_id;
            $data["contact_company_type"] = $contact_company_type;
            $data["contact_company_name"] = $model == "contact" ? $this->{$model}->get_field("father") ? $this->{$model}->get_field("firstName") . " " . $this->{$model}->get_field("father") . " " . $this->{$model}->get_field("lastName") : $this->{$model}->get_field("firstName") . " " . $this->{$model}->get_field("lastName") : $this->{$model}->get_field("name") . ($this->{$model}->get_field("shortName") ? " (" . $this->{$model}->get_field("shortName") . ")" : "");
            $this->load->model("legal_case", "legal_casefactory");
            $this->legal_case = $this->legal_casefactory->get_instance();
            $data["model_code"] = $this->legal_case->get("modelCode");
            $data["related_cases"] = $this->{$model}->{$related_cases_method}($contact_company_id);
            $data["related_companies"] = $this->{$model}->{$related_companies_method}($contact_company_id);
        }
        return $data;
    }
    public function conflict_of_interset($contact_company_type = NULL, $contact_company_id = NULL)
    {
        $this->pageTitle = sprintf($this->lang->line("sheria360_browser_title"), $this->lang->line("reports") . " | " . $this->lang->line("conflict_of_interest"));
        $data = [];
        $data["contact_company_type"] = "";
        $data["contact_company_name"] = "";
        $data["display_result"] = false;
        $report_data = $this->get_conflict_of_interset_data($contact_company_type, $contact_company_id);
        if (!empty($report_data)) {
            $data = array_merge($data, $report_data);
            $data["display_result"] = true;
        }
        $this->includes("scripts/reports/conflict_of_interset", "js");
        $this->load->view("partial/header");
        $this->load->view("reports/conflict_of_interset", $data);
        $this->load->view("partial/footer");
    }
    public function export_conflict_of_interest_word($contact_company_type = NULL, $contact_company_id = NULL)
    {
        $this->pageTitle = sprintf($this->lang->line("sheria360_browser_title"), $this->lang->line("reports") . " | " . $this->lang->line("conflict_of_interest"));
        $data = [];
        $data = $this->get_conflict_of_interset_data($contact_company_type, $contact_company_id);
        if (empty($data)) {
            $this->set_flashmessage("error", $this->lang->line("invalid_record"));
            redirect("reports/conflict_of_interset");
        }
        $data["direction"] = $this->is_auth->is_layout_rtl() ? "rtl" : "ltr";
        $corepath = substr(COREPATH, 0, -12);
        require_once $corepath . "/application/libraries/phpdocx-advanced/classes/CreateDocx.php";
        $docx = new createDocx();
        $html = $this->load->view("reports/conflict_of_interset_word", $data, true);
        $docx->embedHTML($html);
        $docx->modifyPageLayout("A4-landscape", []);
        $temp_directory = $this->config->item("files_path") . DIRECTORY_SEPARATOR . "tmp" . DIRECTORY_SEPARATOR . "cases" . DIRECTORY_SEPARATOR . "usr_" . $this->is_auth->get_user_id();
        if (!is_dir($temp_directory)) {
            @mkdir($temp_directory, 493);
        }
        $data["contact_company_name"] = strip_tags($data["contact_company_name"]);
        $file_name = str_replace(str_split("\\/:*?\"<>| "), "_", $this->lang->line("conflict_of_interest") . "_" . $data["contact_company_name"]);
        $docx->createDocx($temp_directory . "/" . $file_name);
        $file_name_encoded = $this->downloaded_file_name_by_browser($file_name);
        $this->_push_file($temp_directory . "/" . $file_name . ".docx", $file_name_encoded . ".docx");
        unlink($temp_directory . "/" . $file_name . ".docx");
        exit;
    }
    public function board_member_finder($memberType = "", $id = 0)
    {
        $this->pageTitle = sprintf($this->lang->line("sheria360_browser_title"), $this->lang->line("reports") . " | " . $this->lang->line("board_member_finder"));
        $this->load->helper("text");
        $data = [];
        $this->load->model("company", "companyfactory");
        $this->company = $this->companyfactory->get_instance();
        $this->load->model("contact", "contactfactory");
        $this->contact = $this->contactfactory->get_instance();
        $systemPreferences = $this->session->userdata("systemPreferences");
        $hijri_calendar_enabled = isset($systemPreferences["hijriCalendarFeature"]) && $systemPreferences["hijriCalendarFeature"] ? $systemPreferences["hijriCalendarFeature"] : 0;
        if ($this->input->is_ajax_request()) {
            $response = [];
            $memberType = $this->input->post("memberType");
            if ($memberType == "contacts") {
                $response["records"] = $this->contact->load_list_contacts();
            } else {
                if ($memberType == "companies") {
                    $response["records"] = $this->company->load_list(["where" => [["status", "Active"], ["(companies.private IS NULL OR companies.private = 'no' OR (companies.private = 'yes' AND (companies.createdBy = '" . $this->company->logged_user_id . "' OR companies.id IN (SELECT company_id FROM company_users WHERE user_id = '" . $this->company->logged_user_id . "') OR '" . $this->company->override_privacy . "' = 'yes')))", NULL, false]], "where_in" => [["category", ["Internal", "Group"]]], "order_by" => [["category", "desc"], ["name", "asc"]]]);
                } else {
                    $response["records"] = [];
                }
            }
            $this->output->set_content_type("application/json")->set_output(json_encode($response));
        } else {
            $this->includes("jquery/css/chosen", "css");
            $this->includes("jquery/chosen.min", "js");
            $this->includes("scripts/reports/board_member_finder", "js");
            if (is_numeric($id) && 0 < $id) {
                $data["boardMembers"] = $this->company->find_member_as_board_member($id, $memberType);
                if ($memberType == "companies") {
                    $this->company->fetch($id);
                    $data["filter"]["memberName"] = $this->company->get_field("name");
                    $data["filter"]["category"] = $this->company->get_field("category");
                } else {
                    $this->contact->fetch($id);
                    $data["filter"]["memberName"] = $this->contact->get_field("firstName") . " " . $this->contact->get_field("father") . " " . $this->contact->get_field("lastName");
                    $data["filter"]["category"] = "";
                }
                $this->includes("jquery/cookie", "js");
                $this->includes("jquery/treeview", "js");
            }
            $data["filter"]["member"] = $id;
            $data["filter"]["memberType"] = $memberType;
            $data["memberTypeList"] = [" " => $this->lang->line("choose_type"), "companies" => $this->lang->line("company"), "contacts" => $this->lang->line("contact")];
            $systemPreferences = $this->session->userdata("systemPreferences");
            $data["hijri_calendar_enabled"] = $hijri_calendar_enabled;
            $this->load->view("partial/header");
            $this->load->view("reports/board_member_finder", $data);
            $this->load->view("partial/footer");
        }
    }
    public function case_value_tiers_configure()
    {
        $this->authenticate_actions_per_license();
        $this->pageTitle = sprintf($this->lang->line("sheria360_browser_title"), $this->lang->line("reports") . " | " . $this->lang->line("case_value_tiers"));
        $this->load->model("case_configuration");
        if ($this->input->post(NULL)) {
            $flag = $this->check_if_valid($this->input->post(NULL));
            if ($flag != 1) {
                if ($this->case_configuration->set_value_by_key("caseValueTiers", $this->input->post(NULL))) {
                    $this->set_flashmessage("success", $this->lang->line("record_saved"));
                } else {
                    $this->set_flashmessage("error", $this->lang->line("record_could_not_be_saved"));
                }
            } else {
                $this->set_flashmessage("error", $this->lang->line("record_could_not_be_saved"));
            }
        }
        if ($this->is_auth->is_layout_rtl()) {
            $this->includes("styles/rtl/fixes", "css");
        }
        $data = $this->case_configuration->get_value_by_key("caseValueTiers");
        $this->load->view("partial/header");
        $this->load->view("case_value_tiers/index", $data);
        $this->load->view("partial/footer");
    }
    private function check_if_valid($ranges)
    {
        $flag = 0;
        $i = 0;
        while ($i < 6) {
            if (!empty($ranges["Max"][$i]) && !empty($ranges["Min"][$i]) && $ranges["Max"][$i] < $ranges["Min"][$i]) {
                $flag = 1;
            } else {
                $j = $i + 1;
                while ($j < 6) {
                    if (empty($ranges["Max"][$i]) && !empty($ranges["Max"][$j])) {
                        $flag = 1;
                    } else {
                        if (empty($ranges["Max"][$i]) && !empty($ranges["Min"][$j])) {
                            $flag = 1;
                        } else {
                            if (!empty($ranges["Max"][$i]) && !empty($ranges["Min"][$j]) && $ranges["Min"][$j] < $ranges["Max"][$i]) {
                                $flag = 1;
                            } else {
                                $j++;
                            }
                        }
                    }
                }
                if ($flag != 1) {
                    $i++;
                }
            }
        }
        return $flag;
    }
    public function sla_met_vs_breached_bar_chart($workflow_id = "", $hideNoLogs = false)
    {
        $this->pageTitle = sprintf($this->lang->line("sheria360_browser_title"), $this->lang->line("reports") . " | " . $this->lang->line("sla_met_vs_breached_cases"));
        $this->load->model("sla_management_mod", "sla_management_modfactory");
        $this->sla_management_mod = $this->sla_management_modfactory->get_instance();
        $this->load->helper("text");
        $data = [];
        $slaList = [];
        $values = [];
        $data["workflow_id"] = $workflow_id;
        if ($this->input->post(NULL)) {
            $data["workflow_id"] = $this->input->post("workflow");
        }
        $dataResult = $this->sla_management_mod->met_vs_breached($data["workflow_id"]);
        $showNoLogs = false;
        $totalCases = 0;
        if (!empty($dataResult)) {
            foreach ($dataResult as $value) {
                $totalCases = $value["cases"];
                $slaList[] = $value["slaName"];
                $tab1[] = floatval($value["met"]);
                $tab2[] = floatval($value["breached"]);
                $tab3[] = floatval($value["inProgressPaused"]);
                if ($value["noLogs"] != 0) {
                    $showNoLogs = true;
                }
                if ($hideNoLogs != "hideNoLogs") {
                    $tab4[] = floatval($value["noLogs"]);
                }
            }
            if ($hideNoLogs != "hideNoLogs") {
                $values = [$tab1, $tab2, $tab3, $tab4];
            } else {
                $values = [$tab1, $tab2, $tab3];
            }
        }
        if ($hideNoLogs != "hideNoLogs") {
            $valuesNames = [$this->lang->line("met"), $this->lang->line("breached"), $this->lang->line("in_progress_paused"), $this->lang->line("no_sla_logs")];
        } else {
            $valuesNames = [$this->lang->line("met"), $this->lang->line("breached"), $this->lang->line("in_progress_paused")];
        }
        $this->includes("jquery/jqplot/jquery.jqplot.min", "css");
        $this->includes("jquery/jqplot/jquery.jqplot.min", "js");
        $this->includes("jquery/jqplot/plugins/jqplot.dateAxisRenderer.min", "js");
        $this->includes("jquery/jqplot/plugins/jqplot.canvasTextRenderer.min", "js");
        $this->includes("jquery/jqplot/plugins/jqplot.canvasAxisTickRenderer.min", "js");
        $this->includes("jquery/jqplot/plugins/jqplot.categoryAxisRenderer.min", "js");
        $this->includes("jquery/jqplot/plugins/jqplot.barRenderer.min", "js");
        $this->includes("jquery/jqplot/plugins/jqplot.pointLabels.min", "js");
        $data["totalCases"] = $totalCases;
        $data["slaList"] = json_encode($slaList);
        $data["values"] = json_encode($values);
        $data["valuesNames"] = json_encode($valuesNames);
        $data["results"] = !empty($dataResult);
        $data["hideNoLogs"] = $hideNoLogs;
        $data["workflows"] = $this->workflow_status->loadListWorkflows();
        $data["showNoLogsButton"] = $showNoLogs;
        $this->load->view("partial/header");
        $this->load->view("reports/sla_met_vs_breached_bar_chart", $data);
        $this->load->view("partial/footer");
    }
    public function companies_assets()
    {
        $this->pageTitle = sprintf($this->lang->line("sheria360_browser_title"), $this->lang->line("reports") . " | " . $this->lang->line("companies_assets"));
        $this->load->model("company_asset", "company_assetfactory");
        $this->company_asset = $this->company_assetfactory->get_instance();
        $this->load->model("custom_field", "custom_fieldfactory");
        $this->custom_field = $this->custom_fieldfactory->get_instance();
        if ($this->input->post(NULL)) {
            if (!$this->input->is_ajax_request()) {
                redirect("companies");
            }
            $filter = $this->input->post("filter");
            $sortable = $this->input->post("sort");
            $response = $this->company_asset->k_load_all_company_assets($filter, $sortable);
            $this->output->set_content_type("application/json");
            $this->output->set_output(json_encode($response));
        } else {
            $data = [];
            $this->load->model("company_asset_type");
            $data["companyAssetTypes"] = $this->company_asset_type->load_list([], ["firstLine" => ["" => ""]]);
            $data["operators"]["text"] = $this->get_filter_operators("text");
            $data["operators"]["big_text"] = $this->get_filter_operators("bigText");
            $data["operators"]["number"] = $this->get_filter_operators("number");
            $data["operators"]["date"] = $this->get_filter_operators("date");
            $data["operators"]["time"] = $this->get_filter_operators("time");
            $data["operators"]["list"] = $this->get_filter_operators("list");
            $data["operators"]["group_list"] = $this->get_filter_operators("groupList");
            $data["dataCustomFields"] = $this->custom_field->load_list_per_language($this->company_asset->get("modelName"));
            $data["columns"] = ["company_name", "company_id", "name", "type", "ref", "description"];
            foreach ($data["dataCustomFields"] as $field) {
                $data["columns"][] = $field["id"];
                $data["custom_names"][$field["id"]] = $field["customName"];
            }
            $this->includes("kendoui/js/kendo.web.min", "js");
            $this->includes("kendoui/styles/kendo.common-bootstrap.min", "css");
            $this->includes("kendoui/styles/kendo.bootstrap.min", "css");
            if ($this->is_auth->is_layout_rtl()) {
                $this->includes("styles/rtl/fixes", "css");
            }
            $this->includes("jquery/form2js", "js");
            $this->includes("jquery/toObject", "js");
            $this->includes("scripts/reports/companies_assets", "js");
            $this->includes("scripts/advance_search_custom_field_template", "js");
            $this->load->view("partial/header");
            $this->load->view("reports/companies_assets", $data);
            $this->load->view("partial/footer");
        }
    }
    public function task_roll_session()
    {
        $this->load->helper("text");
        if ($this->input->post(NULL)) {
            $filters = $this->input->post("filter");
        } else {
            $filters = [];
            $_POST["skip"] = 0;
            $_POST["take"] = 100;
        }
        $this->pageTitle = sprintf($this->lang->line("sheria360_browser_title"), $this->lang->line("reports") . " | " . $this->lang->line("task_roll_session"));
        $data = $this->cases_related_models_data();
        foreach ($data as $key => $val) {
            if ($key != "lawSuits" && $key != "archivedValues" && $key != "case_statuses_values" && $key != "categories" && $key != "companyRoles" && $key != "contactRoles") {
                if (isset($val[""])) {
                    unset($data[$key][""]);
                }
                if (isset($val[" "])) {
                    unset($data[$key][" "]);
                }
            }
        }
        $this->load->model("task", "taskfactory");
        $this->task = $this->taskfactory->get_instance();
        $this->load->model("legal_case", "legal_casefactory");
        $this->legal_case = $this->legal_casefactory->get_instance();
        $sortable = [["field" => "tasks.assigned_to", "dir" => "asc"], ["field" => "tasks.due_date", "dir" => "desc"]];
        $data["records"] = $this->task->roll_session($filters, $sortable, false, $query, $stringQuery);
        foreach ($data["records"]["data"] as $index => $record) {
            $data["records"]["data"][$index]["taskFullDescription"] = strip_tags($data["records"]["data"][$index]["taskFullDescription"]);
        }
        $data["countGrouping"] = $this->legal_case->get_count_by_a_b_numbers_roll($stringQuery, $query, $sortable, "tasks_detailed_view as tasks");
        $this->load->model("task_status");
        $this->load->model("task_type", "task_typefactory");
        $this->task_type = $this->task_typefactory->get_instance();
        $data["types"] = $this->task_type->load_list_per_language();
        unset($data["types"][""]);
        $data["statuses"] = $this->task_status->load_list([], ["value" => "name"]);
        $data["take"] = $this->input->post("take");
        $data["skip"] = $this->input->post("skip");
        $data["currPage"] = $this->uri->segment(3, "1");
        $data["selectedFilters"] = $filters;
        $data["submitBtn2"] = $this->input->post("submitBtn2") == NULL ? "0" : "1";
        $data["perPageList"] = ["10" => 10, "20" => 20, "50" => 50, "100" => 100];
        $data["direction"] = in_array($this->session->userdata("AUTH_language"), ["arabic", "persian", "hibrew"]) ? "rtl" : "ltr";
        $this->includes("jquery/jquery.fixedheadertable", "js");
        $this->includes("jquery/form2js", "js");
        $this->includes("jquery/toObject", "js");
        $this->includes(app_url("compressed_asset/system_defaults.js?a=" . random_string("alpha")), "js", true);
        if ($this->is_auth->is_layout_rtl()) {
            $this->includes("styles/rtl/fixes", "css");
        }
        $this->includes("scripts/reports/task_roll_session", "js");
        $this->load->view("partial/header");
        $this->load->view("reports/task/roll_session/report", $data);
        $this->load->view("partial/footer");
    }
    public function task_roll_session_pdf()
    {
        if ($this->input->post(NULL)) {
            $filters = $this->input->post("filter");
        } else {
            $filters = [];
        }
        $_POST["skip"] = 0;
        $_POST["take"] = 2147483647;
        $sortable = [["field" => "tasks.assigned_to", "dir" => "asc"], ["field" => "tasks.due_date", "dir" => "desc"]];
        $this->load->model("task", "taskfactory");
        $this->task = $this->taskfactory->get_instance();
        $response = $this->task->roll_session($filters, $sortable, false, $query, $stringQuery);
        $this->load->model("legal_case", "legal_casefactory");
        $this->legal_case = $this->legal_casefactory->get_instance();
        $data["countGrouping"] = $this->legal_case->get_count_by_a_b_numbers_roll($stringQuery, $query, $sortable, "tasks_detailed_view as tasks");
        $title = $this->lang->line("task_roll_session");
        $data["title"] = $title;
        $data["tasks"] = $response["data"];
        $this->load->model("system_configuration");
        $data["cellSpacingInPDF"] = $this->system_configuration->get_value_by_key("taskReportCellSpacingInPDF");
        $data["cellSpacingInPDF"] = 0 < $data["cellSpacingInPDF"] ? $data["cellSpacingInPDF"] : 0;
        $systemPreferences1 = $this->system_preference->get_key_groups();
        if (isset($systemPreferences1["SystemValues"]["exportFilters"]) && $systemPreferences1["SystemValues"]["exportFilters"] == "1") {
            $filter_info = json_decode($this->input->post("filter_info"), true);
            $data["filters"] = $this->load->view("reports/pdf_filter", ["filter_info" => $filter_info], true);
        }
        $html = $this->load->view("reports/task/roll_session/pdf", $data, true);
        $file_name = $this->lang->line("task_roll_session_export") . "_" . date("Ymd") . ".pdf";
        require_once substr(COREPATH, 0, -12) . "/application/libraries/mpdf/vendor/autoload.php";
        $mpdf = new Mpdf\Mpdf(["mode" => "utf-8", "format" => "A4-L", "default_font_size" => 8, "default_font" => "dejavusans", "pagenumPrefix" => $this->lang->line("page") . " ", "nbpgPrefix" => " " . $this->lang->line("of") . " "]);
        $mpdf->shrink_tables_to_fit = 0;
        if ($this->is_auth->is_layout_rtl()) {
            $mpdf->SetDirectionality("rtl");
            $mpdf->autoScriptToLang = true;
            $mpdf->autoLangToFont = true;
        }
        $footer = $this->load->view("reports/task/roll_session/pdf_footer", [], true);
        $mpdf->SetHTMLFooter($footer);
        ini_set("pcre.backtrack_limit", "150000000");
        $mpdf->WriteHTML($html);
        $mpdf->Output($file_name, "D");
    }
    public function task_roll_session_settings()
    {
        if ($this->input->is_ajax_request()) {
            $response = [];
            $this->load->model("system_configuration");
            if (!$this->input->post(NULL)) {
                $data = [];
                $this->load->model("task_status");
                $data["task_statuses"] = $this->task_status->load_all();
                $data["saved_statuses"] = $this->system_configuration->get_value_by_key("taskReportExcludedStatuses");
                $data["fetch_cases"] = $this->system_configuration->get_value_by_key("taskReportFetchOnlyRelatedToMatters");
                $data["cellSpacingInPDF"] = $this->system_configuration->get_value_by_key("taskReportCellSpacingInPDF");
                $data["cellSpacingInPDF"] = 0 < $data["cellSpacingInPDF"] ? $data["cellSpacingInPDF"] : 0;
                $response["html_task_excluded_statuses"] = $this->load->view("reports/task/roll_session/settings_excluded_statuses", $data, true);
                $response["html"] = $this->load->view("reports/task/roll_session/settings", $data, true);
            } else {
                $this->system_configuration->set_value_by_key("taskReportCellSpacingInPDF", $this->input->post("cellSpacingInPDF"));
                $this->system_configuration->set_value_by_key("taskReportExcludedStatuses", $this->input->post("task_statuses"));
                $fetch_cases = $this->input->post("fetch-cases-checkbox");
                $this->system_configuration->set_value_by_key("taskReportFetchOnlyRelatedToMatters", $fetch_cases == "yes" ? "yes" : "");
            }
            $this->output->set_content_type("application/json")->set_output(json_encode($response));
        } else {
            show_404();
        }
    }
    public function hearing_roll_session_per_court()
    {
        $this->load->helper("text");
        if ($this->input->post(NULL)) {
            $filters = $this->input->post("filter");
        } else {
            $filters = [];
            $_POST["skip"] = 0;
            $_POST["take"] = 100;
        }
        $this->pageTitle = sprintf($this->lang->line("sheria360_browser_title"), $this->lang->line("reports") . " | " . $this->lang->line("hearing_roll_session_per_court_circuit"));
        $data = $this->cases_related_models_data();
        foreach ($data as $key => $val) {
            if ($key != "lawSuits" && $key != "archivedValues" && $key != "case_statuses_values" && $key != "categories" && $key != "companyRoles" && $key != "contactRoles") {
                if (isset($val[""])) {
                    unset($data[$key][""]);
                }
                if (isset($val[" "])) {
                    unset($data[$key][" "]);
                }
            }
        }
        $this->load->model("legal_case_hearing", "legal_case_hearingfactory");
        $this->legal_case_hearing = $this->legal_case_hearingfactory->get_instance();
        $systemPreferences = $this->session->userdata("systemPreferences");
        $data["hijri_calendar_enabled"] = isset($systemPreferences["hijriCalendarFeature"]) && $systemPreferences["hijriCalendarFeature"] ? $systemPreferences["hijriCalendarFeature"] : 0;
        $grouping = [["field" => "legal_case_litigation_details.court_id", "dir" => "asc"], ["field" => "legal_case_hearings.startDate", "dir" => "asc"]];
        if (!isset($filters["filters"][3]) && !isset($filters["logic"])) {
            $filters["logic"] = "and";
            $filters["filters"][3]["filters"][0]["field"] = "legal_case_hearings.startDate";
            $filters["filters"][3]["filters"][0]["operator"] = "cast_gte";
            $filters["filters"][3]["filters"][0]["value"] = $data["hijri_calendar_enabled"] ? gregorianToHijri(date("Y-m-d"), "Y-m-d") : date("Y-m-d");
        }
        $data["records"] = $this->legal_case_hearing->roll_session_per_court($filters, false, $data["hijri_calendar_enabled"], $query, $stringQuery);
        $data["countGrouping"] = $this->legal_case->get_count_by_a_b_numbers_roll($stringQuery, $query, $grouping, $this->db->dbdriver == "mysqli" ? "mv_hearings as hearings" : "legal_case_hearings_full_details as hearings");
        $data["take"] = $this->input->post("take");
        $data["skip"] = $this->input->post("skip");
        $data["currPage"] = $this->uri->segment(3, "1");
        $data["selectedFilters"] = $filters;
        $data["submitBtn2"] = $this->input->post("submitBtn2") == NULL ? "0" : "1";
        $data["perPageList"] = ["10" => 10, "20" => 20, "50" => 50, "100" => 100];
        $data["direction"] = in_array($this->session->userdata("AUTH_language"), ["arabic", "persian", "hibrew"]) ? "rtl" : "ltr";
        $this->load->model("hearing_types_languages", "hearing_types_languagesfactory");
        $this->hearing_types_languages = $this->hearing_types_languagesfactory->get_instance();
        $data["types"] = $this->hearing_types_languages->load_list_per_language();
        unset($data["types"][""]);
        $data["types"] = [$this->lang->line("none")] + $data["types"];
        $data["operators"]["group_list"] = $this->get_filter_operators("groupList");
        $this->includes("jquery/jquery.fixedheadertable", "js");
        $this->includes("jquery/form2js", "js");
        $this->includes("jquery/toObject", "js");
        $this->includes(app_url("compressed_asset/system_defaults.js?a=" . random_string("alpha")), "js", true);
        if ($this->is_auth->is_layout_rtl()) {
            $this->includes("styles/rtl/fixes", "css");
        }
        $data["records"]["data"] = $this->group_by_court_region($data["records"]["data"]);
        $this->includes("scripts/reports/hearing_roll_session_per_court", "js");
        $this->load->view("partial/header");
        $this->load->view("reports/hearing_roll_session_per_court/report", $data);
        $this->load->view("partial/footer");
    }
    private function group_by_court_region($records)
    {
        $result = [];
        foreach ($records as $hearing) {
            if (!empty($hearing["court_region"]) && !empty($hearing["court_name"])) {
                $result[$hearing["court_region"] . " / " . $hearing["court_name"]][] = $hearing;
            } else {
                if (empty($hearing["court_region"]) && !empty($hearing["court_name"])) {
                    $result[$hearing["court_name"]][] = $hearing;
                } else {
                    if (!empty($hearing["court_region"]) && empty($hearing["court_name"])) {
                        $result[$hearing["court_region"]][] = $hearing;
                    } else {
                        $result[$this->lang->line("stage_court_not_assigned")][] = $hearing;
                    }
                }
            }
        }
        ksort($result);
        return $result ?? $records;
    }
    private function sort_arr($a, $b)
    {
        return strcmp($a["sorting_key"], $b["sorting_key"]);
    }
    public function hearing_roll_session_per_court_pdf()
    {
        if ($this->input->post(NULL)) {
            $filters = $this->input->post("filter");
        } else {
            $filters = [];
        }
        $_POST["skip"] = "";
        $_POST["take"] = "";
        $this->load->model("legal_case_hearing", "legal_case_hearingfactory");
        $this->legal_case_hearing = $this->legal_case_hearingfactory->get_instance();
        $systemPreferences = $this->session->userdata("systemPreferences");
        $data["hijri_calendar_enabled"] = isset($systemPreferences["hijriCalendarFeature"]) && $systemPreferences["hijriCalendarFeature"] ? $systemPreferences["hijriCalendarFeature"] : 0;
        $response = $this->legal_case_hearing->roll_session_per_court($filters, true, $data["hijri_calendar_enabled"]);
        $title = $this->lang->line("hearing_roll_session_per_court_circuit");
        $data["title"] = $title;
        $data["hearings"] = $response["data"];
        $systemPreferences = $this->session->userdata("systemPreferences");
        $data["hijri_calendar_enabled"] = isset($systemPreferences["hijriCalendarFeature"]) && $systemPreferences["hijriCalendarFeature"] ? $systemPreferences["hijriCalendarFeature"] : 0;
        $this->load->model("system_configuration");
        $data["cellSpacingInPDF"] = $this->system_configuration->get_value_by_key("hearingReportCellSpacingInPDF");
        $data["cellSpacingInPDF"] = 0 < $data["cellSpacingInPDF"] ? $data["cellSpacingInPDF"] : 0;
        $data["hearings"] = $this->group_by_court_region($data["hearings"]);
        $systemPreferences1 = $this->system_preference->get_key_groups();
        if (isset($systemPreferences1["SystemValues"]["exportFilters"]) && $systemPreferences1["SystemValues"]["exportFilters"] == "1") {
            $filter_info = json_decode($this->input->post("filter_info"), true);
            $data["filters"] = $this->load->view("reports/pdf_filter", ["filter_info" => $filter_info], true);
        }
        $html = $this->load->view("reports/hearing_roll_session_per_court/pdf", $data, true);
        $file_name = $this->lang->line("hearing_roll_session_per_court_export") . "_" . date("Ymd") . ".pdf";
        require_once substr(COREPATH, 0, -12) . "/application/libraries/mpdf/vendor/autoload.php";
        $mpdf = new Mpdf\Mpdf(["mode" => "utf-8", "format" => "A4-L", "default_font_size" => 8, "default_font" => "dejavusans", "pagenumPrefix" => $this->lang->line("page") . " ", "nbpgPrefix" => " " . $this->lang->line("of") . " "]);
        $mpdf->shrink_tables_to_fit = 0;
        if ($this->is_auth->is_layout_rtl()) {
            $mpdf->SetDirectionality("rtl");
            $mpdf->autoScriptToLang = true;
            $mpdf->autoLangToFont = true;
        }
        $footer = $this->load->view("reports/hearing_roll_session_per_court/pdf_footer", [], true);
        $mpdf->SetHTMLFooter($footer);
        ini_set("pcre.backtrack_limit", "150000000");
        $mpdf->WriteHTML($html);
        $mpdf->Output($file_name, "D");
    }
    public function hearing_roll_session_per_court_settings()
    {
        if ($this->input->is_ajax_request()) {
            $response = [];
            $this->load->model("system_configuration");
            if (!$this->input->post(NULL)) {
                $data = [];
                $this->load->model("workflow_status", "workflow_statusfactory");
                $this->workflow_status = $this->workflow_statusfactory->get_instance();
                $data["hearing_statuses"] = $this->workflow_status->load_all();
                $data["saved_statuses"] = $this->system_configuration->get_value_by_key("hearingReportExcludedStatuses");
                $data["cellSpacingInPDF"] = $this->system_configuration->get_value_by_key("hearingReportCellSpacingInPDF");
                $data["cellSpacingInPDF"] = 0 < $data["cellSpacingInPDF"] ? $data["cellSpacingInPDF"] : 0;
                $response["html_hearing_excluded_statuses"] = $this->load->view("reports/hearing_roll_session_per_court/settings_excluded_statuses", $data, true);
                $response["html"] = $this->load->view("reports/hearing_roll_session_per_court/settings", $data, true);
            } else {
                $this->system_configuration->set_value_by_key("hearingReportCellSpacingInPDF", $this->input->post("cellSpacingInPDF"));
                $this->system_configuration->set_value_by_key("hearingReportExcludedStatuses", $this->input->post("hearing_statuses"));
            }
            $this->output->set_content_type("application/json")->set_output(json_encode($response));
        } else {
            show_404();
        }
    }
    public function my_time_tracking_kpi()
    {
        $this->authenticate_exempted_actions();
        $this->pageTitle = sprintf($this->lang->line("sheria360_browser_title"), $this->lang->line("reports") . " | " . $this->lang->line("my_time_tracking_kpi_report"));
        $data = [];
        $this->load->model("user_rate_per_hour_per_case", "user_rate_per_hour_per_casefactory");
        $this->user_Rate = $this->user_rate_per_hour_per_casefactory->get_instance();
        $data["organizations"] = $this->user_Rate->get_entities();
        $data["displayTasks"] = !strcmp($this->input->post("displayTasks"), "yes") || !$this->input->post(NULL) ? true : false;
        $data["organizationId"] = $data["organizations"][0]["id"];
        $filter = [];
        $this->load->library("TimeMask");
        $savedFilters = ["userIdValue" => "", "userNameValue" => "", "caseValue" => "", "taskValue" => "", "dateField" => "", "dateOperator" => "cast_lte", "dateValue" => "", "dateEndValue" => ""];
        if ($this->input->post(NULL)) {
            $filter = $this->input->post("filter");
            $data["organizationId"] = $this->input->post("organizationId");
            if (isset($filter["filters"][1]["filters"][0]["value"]) && !empty($filter["filters"][1]["filters"][0]["value"])) {
                $savedFilters["userIdValue"] = $filter["filters"][1]["filters"][0]["value"];
                $this->load->model("user_profile");
                $this->user_profile->fetch(["user_id" => $savedFilters["userIdValue"]]);
                $savedFilters["userNameValue"] = $this->user_profile->get_field("firstName") . " " . $this->user_profile->get_field("lastName");
            }
            if (isset($filter["filters"][2]["filters"][0]["value"])) {
                if (!is_numeric($filter["filters"][2]["filters"][0]["value"])) {
                    $this->set_flashmessage("error", $this->lang->line("invalid_case_id"));
                    redirect("reports/time_tracking_kpi");
                }
                $savedFilters["caseValue"] = $filter["filters"][2]["filters"][0]["value"];
            }
            if (isset($filter["filters"][3]["filters"][0]["value"])) {
                if (!is_numeric($filter["filters"][3]["filters"][0]["value"])) {
                    $this->set_flashmessage("error", $this->lang->line("invalid_task_id"));
                    redirect("reports/time_tracking_kpi");
                }
                $savedFilters["taskValue"] = $filter["filters"][3]["filters"][0]["value"];
            }
            if (isset($filter["filters"][0]["filters"][0]["value"])) {
                $savedFilters["dateOperator"] = $filter["filters"][0]["filters"][0]["operator"];
                $savedFilters["dateValue"] = $filter["filters"][0]["filters"][0]["value"];
                if (isset($filter["filters"][0]["filters"][1]["value"]) && !empty($filter["filters"][0]["filters"][0]["value"])) {
                    $savedFilters["dateEndOperator"] = $filter["filters"][0]["filters"][1]["operator"];
                    $savedFilters["dateEndValue"] = $filter["filters"][0]["filters"][1]["value"];
                }
            } else {
                unset($filter["filters"][0]);
            }
        } else {
            $filter["logic"] = "and";
            $filter["filters"][] = ["filters" => [["field" => "ual.logDate", "operator" => "cast_lte", "value" => date("Y-m-d")]]];
        }
        $data["savedFilters"] = $savedFilters;
        $this->load->model("legal_case", "legal_casefactory");
        $this->legal_case = $this->legal_casefactory->get_instance();
        $casesTimeTracking = $this->legal_case->user_rates_per_cases_per_assignees($data["organizationId"], $filter, $this->session->userdata("AUTH_user_id"));
        $data["operatorsDate"] = $this->get_filter_operators("date");
        $initialArr = ["userName" => "", "billableNbOfHours" => 0, "nonBillableNbOfHours" => 0, "billableAmount" => 0, "nonBillableAmount" => 0];
        $casesPerUser = [];
        if (!empty($casesTimeTracking["data"])) {
            foreach ($casesTimeTracking["data"] as $usrLog) {
                if (!isset($casesPerUser[$usrLog["userId"]])) {
                    $casesPerUser[$usrLog["userId"]] = $initialArr;
                    $casesPerUser[$usrLog["userId"]]["userName"] = $usrLog["worker"];
                }
                if (!strcmp($usrLog["timeStatus"], "billable")) {
                    $billableNbOfHours = $usrLog["effectiveEffort"];
                    $nonBillableNbOfHours = 0;
                    $billableAmount = isset($usrLog["ratePerHour"]) ? $billableNbOfHours * $usrLog["ratePerHour"] : 0;
                    $nonBillableAmount = 0;
                } else {
                    $billableNbOfHours = 0;
                    $nonBillableNbOfHours = $usrLog["effectiveEffort"];
                    $billableAmount = 0;
                    $nonBillableAmount = isset($usrLog["ratePerHour"]) ? $nonBillableNbOfHours * $usrLog["ratePerHour"] : 0;
                }
                $casesPerUser[$usrLog["userId"]]["billableNbOfHours"] += $billableNbOfHours;
                $casesPerUser[$usrLog["userId"]]["nonBillableNbOfHours"] += $nonBillableNbOfHours;
                $casesPerUser[$usrLog["userId"]]["billableAmount"] += $billableAmount;
                $casesPerUser[$usrLog["userId"]]["nonBillableAmount"] += $nonBillableAmount;
            }
        }
        $data["cases_relatedTasks"] = $casesPerUser;
        $tasksPerUser = [];
        if ($data["displayTasks"]) {
            $this->load->model("task", "taskfactory");
            $this->task = $this->taskfactory->get_instance();
            $tasksTimeTracking = $this->task->user_rates_per_tasks_per_assignees($data["organizationId"], $filter, $this->session->userdata("AUTH_user_id"));
            if (!empty($tasksTimeTracking["data"])) {
                foreach ($tasksTimeTracking["data"] as $usrLog) {
                    if (!isset($tasksPerUser[$usrLog["userId"]])) {
                        $tasksPerUser[$usrLog["userId"]] = $initialArr;
                        $tasksPerUser[$usrLog["userId"]]["userName"] = $usrLog["worker"];
                    }
                    if (!strcmp($usrLog["timeStatus"], "billable")) {
                        $billableNbOfHours = $usrLog["effectiveEffort"];
                        $nonBillableNbOfHours = 0;
                        $billableAmount = isset($usrLog["ratePerHour"]) ? $billableNbOfHours * $usrLog["ratePerHour"] : 0;
                        $nonBillableAmount = 0;
                    } else {
                        $billableNbOfHours = 0;
                        $nonBillableNbOfHours = $usrLog["effectiveEffort"];
                        $billableAmount = 0;
                        $nonBillableAmount = isset($usrLog["ratePerHour"]) ? $nonBillableNbOfHours * $usrLog["ratePerHour"] : 0;
                    }
                    $tasksPerUser[$usrLog["userId"]]["billableNbOfHours"] += $billableNbOfHours;
                    $tasksPerUser[$usrLog["userId"]]["nonBillableNbOfHours"] += $nonBillableNbOfHours;
                    $tasksPerUser[$usrLog["userId"]]["billableAmount"] += $billableAmount;
                    $tasksPerUser[$usrLog["userId"]]["nonBillableAmount"] += $nonBillableAmount;
                }
            }
        }
        $data["tasks"] = $tasksPerUser;
        $data["my_report"] = true;
        $this->load->view("partial/header");
        $this->load->view("reports/time_tracking_kpi", $data);
        $this->load->view("partial/footer");
    }
    public function my_time_tracking_kpi_micro()
    {
        $this->load->library("TimeMask");
        $this->authenticate_exempted_actions();
        $this->pageTitle = sprintf($this->lang->line("sheria360_browser_title"), $this->lang->line("reports") . " | " . $this->lang->line("my_time_tracking_kpi_report"));
        $data = [];
        $this->load->model("user_rate_per_hour_per_case", "user_rate_per_hour_per_casefactory");
        $this->user_Rate = $this->user_rate_per_hour_per_casefactory->get_instance();
        $data["organizations"] = $this->user_Rate->get_entities();
        $data["displayAll"] = true;
        $data["displayTasks"] = !strcmp($this->input->post("displayTasks"), "yes") || !$this->input->post(NULL) ? true : false;
        $data["organizationId"] = $data["organizations"][0]["id"];
        $filter = [];
        $savedFilters = ["userIdValue" => "", "userNameValue" => "", "caseValue" => "", "taskValue" => "", "clientIdValue" => "", "clientNameValue" => "", "dateField" => "", "dateOperator" => "cast_lte", "dateValue" => "", "dateEndValue" => ""];
        if ($this->input->post(NULL)) {
            $filter = $this->input->post("filter");
            $data["organizationId"] = $this->input->post("organizationId");
            if (isset($filter["filters"][1]["filters"][0]["value"]) && !empty($filter["filters"][1]["filters"][0]["value"])) {
                $savedFilters["userIdValue"] = $filter["filters"][1]["filters"][0]["value"];
                $this->load->model("user_profile");
                $this->user_profile->fetch(["user_id" => $savedFilters["userIdValue"]]);
                $savedFilters["userNameValue"] = $this->user_profile->get_field("firstName") . " " . $this->user_profile->get_field("lastName");
            }
            if (isset($filter["filters"][2]["filters"][0]["value"])) {
                if (!is_numeric($filter["filters"][2]["filters"][0]["value"])) {
                    $this->set_flashmessage("error", $this->lang->line("invalid_case_id"));
                    redirect("reports/time_tracking_kpi_micro");
                }
                $savedFilters["caseValue"] = $filter["filters"][2]["filters"][0]["value"];
            }
            if (isset($filter["filters"][3]["filters"][0]["value"])) {
                if (!is_numeric($filter["filters"][3]["filters"][0]["value"])) {
                    $this->set_flashmessage("error", $this->lang->line("invalid_task_id"));
                    redirect("reports/time_tracking_kpi_micro");
                }
                $savedFilters["taskValue"] = $filter["filters"][3]["filters"][0]["value"];
            }
            if (isset($filter["filters"][4]["filters"][0]["value"]) && !empty($filter["filters"][4]["filters"][0]["value"])) {
                $savedFilters["clientIdValue"] = $filter["filters"][4]["filters"][0]["value"];
                $this->load->model("client");
                $client_data = $this->client->fetch_client($savedFilters["clientIdValue"]);
                $savedFilters["clientNameValue"] = $client_data["clientName"];
            }
            if (isset($filter["filters"][0]["filters"][0]["value"])) {
                $savedFilters["dateOperator"] = $filter["filters"][0]["filters"][0]["operator"];
                $savedFilters["dateValue"] = $filter["filters"][0]["filters"][0]["value"];
                if (isset($filter["filters"][0]["filters"][1]["value"]) && !empty($filter["filters"][0]["filters"][0]["value"])) {
                    $savedFilters["dateEndOperator"] = $filter["filters"][0]["filters"][1]["operator"];
                    $savedFilters["dateEndValue"] = $filter["filters"][0]["filters"][1]["value"];
                }
            } else {
                unset($filter["filters"][0]);
            }
        } else {
            $filter["logic"] = "and";
            $filter["filters"][] = ["filters" => [["field" => "ual.logDate", "operator" => "cast_lte", "value" => date("Y-m-d")]]];
        }
        $data["savedFilters"] = $savedFilters;
        $this->load->model("legal_case", "legal_casefactory");
        $this->legal_case = $this->legal_casefactory->get_instance();
        $casesTimeTracking = $this->legal_case->user_rates_per_cases_per_assignees($data["organizationId"], $filter, $this->session->userdata("AUTH_user_id"));
        $userLogs = [];
        if (!empty($casesTimeTracking["data"])) {
            foreach ($casesTimeTracking["data"] as $usrLog) {
                $userLogs[$usrLog["userId"]][] = $usrLog;
            }
        }
        if ($data["displayTasks"]) {
            $this->load->model("task", "taskfactory");
            $this->task = $this->taskfactory->get_instance();
            $tasksTimeTracking = $this->task->user_rates_per_tasks_per_assignees($data["organizationId"], $filter, $this->session->userdata("AUTH_user_id"));
            if (!empty($tasksTimeTracking["data"])) {
                foreach ($tasksTimeTracking["data"] as $usrTaskLog) {
                    $userLogs[$usrTaskLog["userId"]][] = $usrTaskLog;
                }
            }
        }
        $data["userLogs"] = $userLogs;
        $data["operatorsDate"] = $this->get_filter_operators("date");
        $data["my_report"] = true;
        $this->load->view("partial/header");
        $this->load->view("reports/time_tracking_kpi_micro", $data);
        $this->load->view("partial/footer");
    }
    public function hearings_pending_updates()
    {
        $this->load->helper("text");
        if (!empty($_POST)) {
            $filters = $this->input->post("filter", true);
        } else {
            $filters = [];
            $_POST["skip"] = 0;
            $_POST["take"] = 100;
        }
        $this->pageTitle = sprintf($this->lang->line("sheria360_browser_title"), $this->lang->line("reports") . " | " . $this->lang->line("hearings_pending_updates"));
        $data = $this->cases_related_models_data();
        foreach ($data as $key => $val) {
            if ($key != "lawSuits" && $key != "archivedValues" && $key != "case_statuses_values" && $key != "categories" && $key != "companyRoles" && $key != "contactRoles") {
                if (isset($val[""])) {
                    unset($data[$key][""]);
                }
                if (isset($val[" "])) {
                    unset($data[$key][" "]);
                }
            }
        }
        $this->load->model("legal_case_hearing", "legal_case_hearingfactory");
        $this->legal_case_hearing = $this->legal_case_hearingfactory->get_instance();
        $systemPreferences = $this->session->userdata("systemPreferences");
        $data["hijri_calendar_enabled"] = isset($systemPreferences["hijriCalendarFeature"]) && $systemPreferences["hijriCalendarFeature"] ? $systemPreferences["hijriCalendarFeature"] : 0;
        $data["records"] = $this->legal_case_hearing->pending_updates($filters, false, $data["hijri_calendar_enabled"]);
        $data["take"] = $this->input->post("take");
        $data["skip"] = $this->input->post("skip");
        $data["currPage"] = $this->uri->segment(3, "1");
        $data["selectedFilters"] = $filters;
        $data["submitBtn2"] = $this->input->post("submitBtn2", true) == NULL ? "0" : "1";
        $data["perPageList"] = ["10" => 10, "20" => 20, "50" => 50, "100" => 100];
        $data["direction"] = in_array($this->session->userdata("AUTH_language"), ["arabic", "persian", "hibrew"]) ? "rtl" : "ltr";
        $this->load->model("hearing_types_languages", "hearing_types_languagesfactory");
        $this->hearing_types_languages = $this->hearing_types_languagesfactory->get_instance();
        $data["types"] = $this->hearing_types_languages->load_list_per_language();
        unset($data["types"][""]);
        $data["types"] = [$this->lang->line("none")] + $data["types"];
        $data["operators"]["group_list"] = $this->get_filter_operators("groupList");
        $this->load->model("court_region");
        $data["courtRegions"] = $this->court_region->load_list([], ["firstLine" => [" " => " "]]);
        $data["operatorsGroupList"] = $this->get_filter_operators("groupList");
        $this->includes("jquery/jquery.fixedheadertable", "js");
        $this->includes("jquery/form2js", "js");
        $this->includes("jquery/toObject", "js");
        $this->includes(app_url("compressed_asset/system_defaults.js?a=" . random_string("alpha")), "js", true);
        if ($this->is_auth->is_layout_rtl()) {
            $this->includes("styles/rtl/fixes", "css");
        }
        $this->includes("scripts/reports/hearings_pending_updates", "js");
        $this->load->view("partial/header");
        $this->load->view("reports/hearing/pending_updates/report", $data);
        $this->load->view("partial/footer");
    }
    public function hearings_pending_updates_pdf()
    {
        if (!empty($_POST)) {
            $filters = $this->input->post("filter", true);
        } else {
            $filters = [];
        }
        $_POST["skip"] = "";
        $_POST["take"] = "";
        $this->load->model("legal_case_hearing", "legal_case_hearingfactory");
        $this->legal_case_hearing = $this->legal_case_hearingfactory->get_instance();
        $systemPreferences = $this->session->userdata("systemPreferences");
        $data["hijri_calendar_enabled"] = isset($systemPreferences["hijriCalendarFeature"]) && $systemPreferences["hijriCalendarFeature"] ? $systemPreferences["hijriCalendarFeature"] : 0;
        $response = $this->legal_case_hearing->pending_updates($filters, true, $data["hijri_calendar_enabled"]);
        $title = $this->lang->line("hearings_pending_updates");
        $data["title"] = $title;
        $data["hearings"] = $response["data"];
        $systemPreferences = $this->session->userdata("systemPreferences");
        $data["hijri_calendar_enabled"] = isset($systemPreferences["hijriCalendarFeature"]) && $systemPreferences["hijriCalendarFeature"] ? $systemPreferences["hijriCalendarFeature"] : 0;
        $this->load->model("system_configuration");
        $data["cellSpacingInPDF"] = $this->system_configuration->get_value_by_key("hearingUpdatesReportCellSpacingInPDF");
        $data["cellSpacingInPDF"] = 0 < $data["cellSpacingInPDF"] ? $data["cellSpacingInPDF"] : 0;
        $systemPreferences1 = $this->system_preference->get_key_groups();
        if (isset($systemPreferences1["SystemValues"]["exportFilters"]) && $systemPreferences1["SystemValues"]["exportFilters"] == "1") {
            $filter_info = json_decode($this->input->post("filter_info"), true);
            $data["filters"] = $this->load->view("reports/pdf_filter", ["filter_info" => $filter_info], true);
        }
        $html = $this->load->view("reports/hearing/pending_updates/pdf", $data, true);
        $file_name = $this->lang->line("hearings_pending_updates_export") . "_" . date("Ymd") . ".pdf";
        require_once substr(COREPATH, 0, -12) . "/application/libraries/mpdf/vendor/autoload.php";
        $mpdf = new Mpdf\Mpdf(["mode" => "utf-8", "format" => "A4-L", "default_font_size" => 8, "default_font" => "dejavusans", "pagenumPrefix" => $this->lang->line("page") . " ", "nbpgPrefix" => " " . $this->lang->line("of") . " "]);
        $mpdf->shrink_tables_to_fit = 0;
        if ($this->is_auth->is_layout_rtl()) {
            $mpdf->SetDirectionality("rtl");
            $mpdf->autoScriptToLang = true;
            $mpdf->autoLangToFont = true;
        }
        $footer = $this->load->view("reports/hearing/pending_updates/pdf_footer", [], true);
        $mpdf->SetHTMLFooter($footer);
        ini_set("pcre.backtrack_limit", "150000000");
        $mpdf->WriteHTML($html);
        $mpdf->Output($file_name, "D");
    }
    public function hearings_pending_updates_settings()
    {
        if ($this->input->is_ajax_request()) {
            $response = [];
            $this->load->model("system_configuration");
            if (empty($_POST)) {
                $data = [];
                $this->load->model("workflow_status", "workflow_statusfactory");
                $this->workflow_status = $this->workflow_statusfactory->get_instance();
                $this->load->model("legal_case_hearing", "legal_case_hearingfactory");
                $this->legal_case_hearing = $this->legal_case_hearingfactory->get_instance();
                $data["business_rules"] = $this->legal_case_hearing->get("pendingUpdatesBusinessRules");
                $data["hearing_statuses"] = $this->workflow_status->load_all();
                $data["saved_statuses"] = $this->system_configuration->get_value_by_key("hearingUpdatesReportExcludedStatuses");
                $data["saved_business_rules"] = $this->system_configuration->get_value_by_key("hearingUpdatesReportBusinessRules");
                $data["cellSpacingInPDF"] = $this->system_configuration->get_value_by_key("hearingUpdatesReportCellSpacingInPDF");
                $data["cellSpacingInPDF"] = 0 < $data["cellSpacingInPDF"] ? $data["cellSpacingInPDF"] : 0;
                $response["html_report_filter_criteria"] = $this->load->view("reports/hearing/pending_updates/settings_report_filter_criteria", $data, true);
                $response["html_hearing_excluded_statuses"] = $this->load->view("reports/hearing/pending_updates/settings_excluded_statuses", $data, true);
                $response["html"] = $this->load->view("reports/hearing/pending_updates/settings", $data, true);
            } else {
                $this->system_configuration->set_value_by_key("hearingUpdatesReportCellSpacingInPDF", $this->input->post("cellSpacingInPDF"));
                $this->system_configuration->set_value_by_key("hearingUpdatesReportExcludedStatuses", $this->input->post("hearing_statuses"));
                $this->system_configuration->set_value_by_key("hearingUpdatesReportBusinessRules", $this->input->post("report_filter_criteria"));
            }
            $this->output->set_content_type("application/json")->set_output(json_encode($response));
        } else {
            show_404();
        }
    }
    public function sla()
    {
        $data = [];
        if ($this->input->is_ajax_request()) {
            $workflow = $this->input->post("workflow", true);
            $response["status"] = false;
            if ($workflow) {
                $this->load->model("sla_management_mod", "sla_management_modfactory");
                $this->sla_management_mod = $this->sla_management_modfactory->get_instance();
                $status = $this->input->post("status", true) ? array_filter($this->input->post("status", true)) : [];
                $data["data"] = $this->sla_management_mod->return_cases_slas_per_workflow($workflow, $status);
                if (!empty($data["data"])) {
                    $response["status"] = true;
                    $response["html"] = $this->load->view("reports/sla/body", $data, true);
                }
            } else {
                $response["error"] = sprintf($this->lang->line("required_rule"), $this->lang->line("workflow"));
            }
            $this->output->set_content_type("application/json")->set_output(json_encode($response));
        } else {
            $this->pageTitle = sprintf($this->lang->line("sheria360_browser_title"), $this->lang->line("reports") . " | " . $this->lang->line("sla"));
            $this->load->model("workflow_status", "workflow_statusfactory");
            $this->workflow_status = $this->workflow_statusfactory->get_instance();
            $data["workflows"] = ["" => ""] + $this->workflow_status->loadListWorkflows();
            $data["statuses"] = ["in_progress" => $this->lang->line("in_progress"), "paused" => $this->lang->line("paused"), "finished" => $this->lang->line("finished"), "breached" => $this->lang->line("breached")];
            $this->includes("scripts/reports/sla", "js");
            $this->load->view("partial/header");
            $this->load->view("reports/sla/header", $data);
            $this->load->view("partial/footer");
        }
    }
    public function sla_pdf()
    {
        $workflow = $this->input->post("workflow", true);
        $data = [];
        if ($workflow) {
            $this->load->model("sla_management_mod", "sla_management_modfactory");
            $this->sla_management_mod = $this->sla_management_modfactory->get_instance();
            $status = $this->input->post("status", true) ? array_filter($this->input->post("status", true)) : [];
            $data["data"] = $this->sla_management_mod->return_cases_slas_per_workflow($workflow, $status);
        }
        $html = $this->load->view("reports/sla/pdf", $data, true);
        $file_name = $this->lang->line("sla_report") . "_" . date("Ymd") . ".pdf";
        require_once substr(COREPATH, 0, -12) . "/application/libraries/mpdf/vendor/autoload.php";
        $mpdf = new Mpdf\Mpdf(["mode" => "utf-8", "format" => "A4-L", "default_font_size" => 8, "default_font" => "dejavusans", "pagenumPrefix" => $this->lang->line("page") . " ", "nbpgPrefix" => " " . $this->lang->line("of") . " "]);
        $mpdf->shrink_tables_to_fit = 0;
        if ($this->is_auth->is_layout_rtl()) {
            $mpdf->SetDirectionality("rtl");
            $mpdf->autoScriptToLang = true;
            $mpdf->autoLangToFont = true;
        }
        $footer = $this->load->view("reports/pdf_footer", [], true);
        $mpdf->SetHTMLFooter($footer);
        ini_set("pcre.backtrack_limit", "150000000");
        $mpdf->WriteHTML($html);
        $mpdf->Output($file_name, "D");
    }
    private function get_legal_cases_sortable_fields($fields, $is_group_by)
    {
        $this->load->model("legal_case", "legal_casefactory");
        $this->legal_case = $this->legal_casefactory->get_instance();
        $sortable = $this->legal_case->get_legal_cases_order_by_fields($fields, $is_group_by);
        return $sortable;
    }
    public function display_tree_node($tree, $parentCompany, $first = false)
    {
        $category = $parentCompany["category"];
        $companyLinkHref = site_url("companies/tab_company/" . $parentCompany["id"]);
        if ($category == "Group") {
            echo "<li>&nbsp;";
            echo !$first ? "<span title=\"" . str_pad(number_format($parentCompany["percentage"] * 100, 4), 8, "0", STR_PAD_LEFT) . "%\">" . round($parentCompany["percentage"] * 100) . "%</span> - " : "";
            echo "<span>";
            echo $parentCompany["name"];
            echo "</span>";
        } else {
            echo "<li>&nbsp;";
            echo !$first ? "<span title=\"" . str_pad(number_format($parentCompany["percentage"] * 100, 4), 8, "0", STR_PAD_LEFT) . "%\">" . round($parentCompany["percentage"] * 100) . "%</span> - " : "";
            echo "<span><a href=\"";
            echo $companyLinkHref;
            echo "\" class=\"";
            echo $parentCompany["member_id"];
            echo "\">";
            echo $parentCompany["name"];
            echo "</a></span>";
        }
        if (isset($tree[$parentCompany["member_id"]]["children"])) {
            echo "<ul>";
            foreach ($tree[$parentCompany["member_id"]]["children"] as $child) {
                unset($tree[$parentCompany["member_id"]]);
                display_tree_node($tree, $child);
            }
            echo "</ul>";
        }
        echo "</li>";
    }
    public function case_status_risks_fee_notes()
    {
        $data=[];
        $this->includes("kendoui/js/kendo.web.min", "js");
        $this->includes("kendoui/styles/kendo.common-bootstrap.min", "css");
        $this->includes("kendoui/styles/kendo.bootstrap.min", "css");

        $this->load->view("partial/header");
        $this->load->view("reports/case_status_risks_fee_notes", $data);
        $this->load->view("partial/footer");

    }
    public function get_case_status_risks_fee_notes(){
//load from legal_case_risks load_cases_summary_report_with_risks() method
        $this->load->model("legal_case_risks", "legal_case_risksfactory");
        $this->legal_case_risks = $this->legal_case_risksfactory->get_instance();
        $response = $this->legal_case_risks->load_cases_summary_report_with_risks();
        $this->output->set_content_type("application/json")->set_output(json_encode($response));

    }
}

