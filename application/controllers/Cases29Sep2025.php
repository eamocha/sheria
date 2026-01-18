<?php
if (!defined("BASEPATH")) {
    exit("No direct script access allowed");
}
require "Core_controller.php";
class Cases29Sep2025 extends Core_controller
{
    public $Legal_Case;
    public $defaultWorkflow = "";
    public $controller_name = "cases";
    public function __construct()
    {
        parent::__construct();
        $this->currentTopNavItem = "cases";
        $this->load->model("legal_case", "legal_casefactory");
        $this->legal_case = $this->legal_casefactory->get_instance();
        $this->load->model("workflow_status", "workflow_statusfactory");
        $this->workflow_status = $this->workflow_statusfactory->get_instance();
        $this->load->model("sla_management_mod", "sla_management_modfactory");
        $this->sla_management_mod = $this->sla_management_modfactory->get_instance();
        $this->load->model("legal_case_opponent", "legal_case_opponentfactory");
        $this->legal_case_opponent = $this->legal_case_opponentfactory->get_instance();

        $this->load->library("dms");
        $this->load->library("dmsnew");
        $this->load->model("email_notification_scheme");
    }
    private function index($category = "Matter")
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
        if ($this->input->post(NULL)) {
            if (!$this->input->is_ajax_request()) {
                redirect("dashboard");
            }
            $filter = $this->input->post("filter");
            $sortable = $this->input->post("sort");
            if ($this->input->post("loadWithSavedFilters") === "1") {
                $filter = json_decode($this->input->post("filter"), true);
            } else {
                $page_size_modified = $this->input->post("pageSize") != $data["grid_saved_details"]["pageSize"]??=20;//default by atinga
                $sort_modified = $this->input->post("sortData") && $this->input->post("sortData") != $data["grid_saved_details"]["sort"];
                if ($page_size_modified || $sort_modified) {
                    $_POST["model"] = $data["model"];
                    $_POST["grid_saved_filter_id"] = $data["gridDefaultFilter"]["id"];
                    $response = $this->grid_saved_column->save();
                    $response["gridDetails"] = $this->grid_saved_column->get_user_grid_details($data["model"], $data["gridDefaultFilter"]["id"]);
                }
            }
            $response["columns_html"] = $this->load->view("grid_saved_columns/dialog_columns_contents", $data, true);

            $response = array_merge($response, $this->legal_case->k_load_all_cases($filter, $sortable));
            $this->output->set_content_type("application/json");
            $this->output->set_output(json_encode($response));
        } else {
            $indexView = "legal_case";
            $gridJS = "legal_case_grid";
            $pageTitle = $this->lang->line("corporate_matters");
            if ($category == "Litigation") {
                $indexView = "litigation";
                $gridJS = "litigation_grid";
                $pageTitle = $this->lang->line("litigation_cases");
            }else if ($category == "Criminal") {
                $indexView = "criminal";
                $gridJS = "criminal_grid";
                $pageTitle = $this->lang->line("criminal_cases");
            }
            else if ($category == "Complaints") {
                $indexView = "complaints";
                $gridJS = "complaints_grid";
                $pageTitle = $this->lang->line("complaints_inquiries");
            }
            else if ($category == "Surveillance") {
                $indexView = "surveillance";
                $gridJS = "surveillance_grid";
                $pageTitle = $this->lang->line("criminal_cases");
            }
            else if ($category == "Investigation") {
                $indexView = "investigation";
                $gridJS = "investigation_grid";
                $pageTitle = $this->lang->line("surveillance_detection");
            }
            $this->pageTitle = sprintf($this->lang->line("sheria360_browser_title"), $pageTitle);
            $this->load->helper(["text"]);
            $formData = $this->_load_related_models(strtolower($category) =="criminal"?? "litigation"??false);
            foreach ($formData as $key => $val) {
                if (isset($val[""])) {
                    unset($formData[$key][""]);
                }
            }
            $formData["id"] = "";
            $formData["priorities"] = array_combine($this->legal_case->get("priorityValuesKeys"), [$this->lang->line("critical"), $this->lang->line("high"), $this->lang->line("medium"), $this->lang->line("low")]);
            $formData["externalizeLawyers"] = array_combine($this->legal_case->get("externalizeLawyersValues"), ["", $this->lang->line("yes"), $this->lang->line("no")]);
            $this->legal_case->reset_fields();
            $this->legal_case->set_field("arrivalDate", date("Y-m-d", time()));
            $this->legal_case->set_field("priority", "medium");
            $formData["externalizeLawyers"][""] = "";
            $data = array_merge($data, ["formData" => $formData]);
            $data["Case_Types"] = $this->case_type->load_list(["where" => [["litigation", "yes"], ["isDeleted", 0]], "order_by" => ["name", "asc"]], ["firstLine" => ["" => $this->lang->line("choose_case_type")]]);
            $data["archivedValues"] = array_combine($this->legal_case->get("archivedValues"), [$this->lang->line("either"), $this->lang->line("yes"), $this->lang->line("no")]);
            $data["channelValues"] = array_combine($this->legal_case->get("channelValues"), [$this->lang->line("either"), $this->lang->line("yes"), $this->lang->line("no")]);
            $data["operators"]["text"] = $this->get_filter_operators("text");
            $data["operators"]["big_text"] = $this->get_filter_operators("bigText");
            $data["operators"]["number"] = $this->get_filter_operators("number");
            $data["operators"]["number_only"] = $this->get_filter_operators("number_only");
            $data["operators"]["date"] = $this->get_filter_operators("date");
            $data["operators"]["time"] = $this->get_filter_operators("time");
            $data["operators"]["list"] = $this->get_filter_operators("list");
            $data["operators"]["text_empty"] = $this->get_filter_operators("text_empty");
            $data["operators"]["group_list"] = $this->get_filter_operators("groupList");
            $systemPreferences = $this->session->userdata("systemPreferences");
            $this->load->model(["court", "court_type", "court_region", "court_degree"]);
            $this->load->model("custom_field", "custom_fieldfactory");
            $this->custom_field = $this->custom_fieldfactory->get_instance();
            $data["dataCustomFields"] = $this->custom_field->load_list_per_language($this->legal_case->get("modelName"));
            $data["courtTypes"] = $this->court_type->load_list([], ["firstLine" => [" " => " "]]);
            $data["courtDegrees"] = $this->court_degree->load_list([], ["firstLine" => [" " => " "]]);
            $data["courtRegions"] = $this->court_region->load_list([], ["firstLine" => [" " => " "]]);
            $data["courts"] = $this->court->load_list([], ["firstLine" => [" " => " "]]);
            $data["category"] = $category;
            $data["defaultArchivedValue"] = "no";
            $data["loggedUserIsAdminForGrids"] = $this->session->userdata("AUTH_is_grid_admin");
            if (isset($systemPreferences["AllowFeatureSLAManagement"])) {
                $data["slaFeature"] = $systemPreferences["AllowFeatureSLAManagement"];
            } else {
                $data["slaFeature"] = "no";
            }
            $data["businessWeekDays"] = $systemPreferences["businessWeekEquals"];
            $data["businessDayHours"] = $systemPreferences["businessDayEquals"];
            $data["hijri_calendar_enabled"] = isset($systemPreferences["hijriCalendarFeature"]) && $systemPreferences["hijriCalendarFeature"] ? $systemPreferences["hijriCalendarFeature"] : 0;
            $decoded_filters = [];
            $decoded_filters= json_decode($data["gridSavedFiltersData"]["gridFilters"]??=null, true);//changed Atinga
            $filters = [];
            if (isset($decoded_filters["filters"])) {
                $filters = $decoded_filters["filters"];
            }
            if (!empty($filters)) {
                foreach ($filters as $index => $filter) {
                    if ($filter["filters"][0]["field"] == "contactOutsourceTo" || $filter["filters"][0]["field"] == "companyOutsourceTo") {
                        $data["outsource_to_value"] = $filter["filters"][0]["value"];
                        $data["outsource_to_function"] = $filter["filters"][0]["function"];
                    }
                }
            }
            $this->includes("kendoui/js/kendo.web.min", "js");
            $this->includes("kendoui/styles/kendo.common-bootstrap.min", "css");
            $this->includes("kendoui/styles/kendo.bootstrap.min", "css");
            $this->includes("jquery/css/chosen", "css");
            $this->includes("jquery/chosen.min", "js");
            $this->includes("jquery/form2js", "js");
            $this->includes("jquery/jquery.dirtyform", "js");
            $this->includes("jquery/toObject", "js");
            $this->includes("scripts/show_hide_customer_portal", "js");
            $this->includes("scripts/" . $gridJS, "js");
            $this->includes(app_url("compressed_asset/system_defaults.js?a=" . random_string("alpha")), "js", true);
            $this->includes("jquery/tabledit/jquery.tabledit.min", "js");
            $this->includes("scripts/advance_search_custom_field_template", "js");
            $this->includes("jquery/timemask", "js");
            if ($this->is_auth->is_layout_rtl()) {
                $this->includes("styles/rtl/fixes", "css");
            }
            $this->load->view("partial/header");
            $this->load->view("cases/index_" . $indexView, $data);
            $this->load->view("partial/footer");
        }
    }

    public function legal_matter()
    {
        $this->pageTitle = sprintf($this->lang->line("sheria360_browser_title"), $this->lang->line("legal_matter") . " | " . $this->lang->line("case_in_menu"));
        $this->index("Matter");
    }
    public function litigation_case()
    {
        $this->pageTitle = sprintf($this->lang->line("sheria360_browser_title"), $this->lang->line("litigation_case") . " | " . $this->lang->line("case_in_menu"));
        $this->index("Litigation");
    }
    public function criminal_case()
    {
        $this->pageTitle = sprintf($this->lang->line("sheria360_browser_title"), $this->lang->line("litigation_case") . " | " . $this->lang->line("criminal_cases_in_menu"));
        $this->index("Criminal");
    }
    public function complaints_inquiries()
    {
        $this->pageTitle = sprintf($this->lang->line("sheria360_browser_title"), $this->lang->line("litigation_case") . " | " . $this->lang->line("criminal_cases_in_menu"));
        $this->index("Complaints");

    }
    public function surveillance_detection()
    {
        $this->pageTitle = sprintf($this->lang->line("sheria360_browser_title"), $this->lang->line("litigation_case") . " | " . $this->lang->line("criminal_cases_in_menu"));
        $this->index("Surveillance");
    }
    public function investigation_enforcement()
    {
        $this->pageTitle = sprintf($this->lang->line("sheria360_browser_title"), $this->lang->line("litigation_case") . " | " . $this->lang->line("criminal_cases_in_menu"));
        $this->index("Investigation");
    }
    public  function investigation_case_details($id)
    {      $this->complaint_details($id);

    }

    public  function complaint_details($id)
    {
        $this->load->model("case_comment", "case_commentfactory");
        $this->case_comment = $this->case_commentfactory->get_instance();

        $this->load->model("user_preference");
        $this->load->library("TimeMask");

        $systemPreferences = $this->session->userdata("systemPreferences");
        $legal_case_related_container = $this->legal_case->get_legal_case_related_container($id);
        $this->load->model("legal_case_related_container");
        $count_legal_case_related_containers = $this->legal_case->count_legal_case_related_containers($id);
        $this->load->model("legal_case_outsource", "legal_case_outsourcefactory");
        $this->legal_case_outsource = $this->legal_case_outsourcefactory->get_instance();
        $this->load->model("criminal_case_detail", "criminal_case_detailfactory");
        $this->criminal_case_detail = $this->criminal_case_detailfactory->get_instance();
        //load the Suspect_arrest model
        $this->load->model("suspect_arrest", "suspect_arrestfactory");
        $this->suspect_arrest = $this->suspect_arrestfactory->get_instance();
        $this->load->model("provider_group");

        $this->load->helper(["text"]);
        if (!$this->input->post(NULL)) {
            if ($id < 1 || !$this->legal_case->fetch($id)) {
                $this->set_flashmessage("error", $this->lang->line("invalid_record"));
                redirect("dashboard");
            }
            $legalCase = $this->legal_case->load_case($id);
            $this->legal_case->update_recent_ids($id, $legalCase["category"] == "Criminal" ? "criminal_matters" : "litigation_cases");
        }
        else {
            $legalCase = $this->legal_case->load_case($id);
            if (!$legalCase) {
                $this->set_flashmessage("error", $this->lang->line("invalid_record"));
                redirect("dashboard");
            }
            $result_status = $this->_edit($id, $legalCase["category"], $legal_case_related_container, $count_legal_case_related_containers);
            if ($result_status) {
                $this->set_flashmessage("success", sprintf($this->lang->line("record_save_successfull"), " " . $this->legal_case->get("modelCode") . $id));
                redirect("cases/edit/" . $id, "location");
            } else {
                if ($this->legal_case->is_valid()) {
                    redirect("cases/edit/" . $id);
                }
            }
            $legalCase = $this->legal_case->load_case($id);
        }

        $case_status_id = $this->legal_case->get_field("case_status_id");
        $legalCasewatchersUsers = $this->legal_case->load_watchers_users($id);

        $this->provider_group->fetch(["allUsers" => 1]);
        $data["allUsersProviderGroupId"] = $this->provider_group->get_field("id");
        $data["usersProviderGroup"] = $this->get_provider_group_users($this->legal_case->get_field("provider_group_id"));
        $data["id"] = $id;
        $data["legalCase"] = $legalCase;
        $data["legalCasewatchersUsers"] = isset($legalCasewatchersUsers[0]) ? $legalCasewatchersUsers[0] : [];
        $data["legalCasewatchersUsersStatus"] = isset($legalCasewatchersUsers[1]) ? $legalCasewatchersUsers[1] : [];

        $this->load->model(["legal_case_contact_role", "legal_case_company_role"]);
        $data["contactGridRoles"] = $this->legal_case_contact_role->load_list([], ["firstLine" => [" " => " "]]);
        $data["contactRoles"] = $this->legal_case_contact_role->load_list([], ["firstLine" => ["" => " "]]);
        $data["companyGridRoles"] = $this->legal_case_company_role->load_list([], ["firstLine" => [" " => " "]]);
        $data["companyRoles"] = $this->legal_case_company_role->load_list([], ["firstLine" => ["" => " "]]);
        $this->load->model("client");
        $data["clientData"] = $this->client->fetch_client($this->legal_case->get_field("client_id"));
        $data["clientCompanyCategory"] = "";
        if (isset($data["clientData"]) && !empty($data["clientData"]) && $data["clientData"]["type"] === "company") {
            $this->company->fetch($data["clientData"]["member_id"]);
            $data["clientCompanyCategory"] = $this->company->get_field("category");
            $this->company->reset_fields();
        }
        $relatedOpponentData = $this->legal_case_opponent->fetch_case_opponents_data($this->legal_case->get_field("id"));
        if (!empty($relatedOpponentData)) {
            $data["relatedOpponentData"] = $relatedOpponentData;
        } else {
            $data["relatedOpponentData"] = [["opponent_member_type" => "company", "opponentName" => "", "opponent_member_id" => "", "opponentCompanyCategory" => "", "position_name" => "", "opponent_position" => "", "opponentForeignName" => ""]];
        }
        $trigger_name = $data["legalCase"]["category"] === "Litigation" ? "edit_litigation_case" : "edit_matter_case";
        $data["hide_show_notification_edit_legal_case"] = $this->email_notification_scheme->get_hide_show_send_email_notification_by_trigger_action($trigger_name) == "1" ? "yes" : "";
        $this->load->model("reminder", "reminderfactory");
        $this->reminder = $this->reminderfactory->get_instance();
        $data["notify_me_before_time_types"] = array_combine($this->reminder->get("notify_me_before_time_type"), [$this->lang->line("day_or_days"), $this->lang->line("week_or_weeks"), $this->lang->line("month")]);
        $data["notify_me_before_types"] = array_combine($this->reminder->get("notify_me_before_type"), [$this->lang->line("reminder_popup"), $this->lang->line("reminder_popup_and_email")]);
        $data["notify_before"] = $this->reminder->load_notify_before_data_to_related_object($id, $this->legal_case->get("_table"));
        $data["isCustomerPortal"] = !strcmp($this->legal_case->get_field("channel"), $this->legal_case->get("portalChannel")) ? "yes" : "no";
        $data["visibleToCustomerPortal"] = $this->legal_case->get_field("visibleToCP") == "1" ? "yes" : "no";
        $data["usersList"] = $this->user->load_available_list();


    //    $data["stageID"]= $this->legal_case->get_field("stage");
      //  $data["legal_case_related_container"] = $legal_case_related_container;
     //   $data["count_legal_case_related_containers"] = $count_legal_case_related_containers;
      //  $data["max_opponents"] = $systemPreferences["caseMaxOpponents"];
      //  $this->load->model("contact_company_category");
      //  $data["outsource_categories"] = $this->contact_company_category->load_categories_per_lang("none");
///load closure details\
        $this->load->model("case_closure_recommendation","case_closure_recommendationfactory");
        $this->case_closure_recommendation=$this->case_closure_recommendationfactory->get_instance();
        $data["closure_recommendation"]=$this->case_closure_recommendation->load_recommendation_by_case_id($id);

        $this->load->model("case_investigation_log","case_investigation_logfactory");
        $this->case_investigation_log=$this->case_investigation_logfactory->get_instance();
        $data["investigation_log"]=$this->case_investigation_log->get_investigation_log_by_case_id($id);
        //exhibits
        $this->load->model("case_exhibit","case_exhibitfactory");
        $this->case_exhibit=$this->case_exhibitfactory->get_instance();
        $data["exhibits"]=$this->case_exhibit->get_exhibits_by_case_id($id);
        $data["criminalCaseDetails"]=$this->criminal_case_detail->get_details_by_case_id($id);
        $data["arrestDetails"]=$this->suspect_arrest->get_arrest_details_by_case_id($id);


        ///documents in the main page

        $this->frontendIncludes();

        $this->load->view("partial/header");

        $this->load->view("prosecution/google/complaints",$data);
        $this->load->view("partial/footer");

    }
    public function  get_case_exhibits()
    {
        $id=$this->input->post("case_id");
        $this->load->model("case_exhibit","case_exhibitfactory");
        $this->case_exhibit=$this->case_exhibitfactory->get_instance();
        $data["exhibits"]=$this->case_exhibit->get_exhibits_by_case_id($id);
        $data["status"]=true;
        $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode($data));

    }
    public function master_register()
    { $this->frontendIncludes();


        $this->load->view("partial/header");
        // $this->load->view("prosecution/case_master_register" );
        // $this->load->view("prosecution/add_form" );
        // $this->load->view("prosecution/case_view_detail" );

        //  $this->load->view("prosecution/google/form");
        //  $this->load->view("prosecution/google/case_details_view");
        $this->load->view("prosecution/google/master_register");
        $this->load->view("partial/footer");

    }
    public function criminal_case_details($caseId)
    {
        $this->frontendIncludes();

        $this->load->view("partial/header");
        $this->load->view("prosecution/google/case_details");
        $this->load->view("partial/footer");


    }
  
    public function frontendIncludes()
    {  $this->includes("bootstrap/js/bootstrap4.6.1.bundle.min", "js");
        $this->includes("customerPortal/clientPortal/css/datatables.min", "css");
        $this->includes("customerPortal/clientPortal/js/datatables.min", "js");
        $this->includes("customerPortal/clientPortal/js/jquery.dataTables.min", "js");
        $this->includes("customerPortal/clientPortal/js/dataTables.bootstrap.min", "js");

    }
    public function process_exhibit()
    {
        $response = ["status" => false, "message" => "Failed to save exhibit"];

        $this->load->model("case_exhibit", "case_exhibitfactory");
        $this->case_exhibit = $this->case_exhibitfactory->get_instance();

        $this->load->model("case_exhibit_document", "case_exhibit_documentfactory");
        $this->case_exhibit_document = $this->case_exhibit_documentfactory->get_instance();

        $case_id = $this->input->post("case_id");

        $this->case_exhibit->set_field("case_id", $case_id);
        $this->case_exhibit->set_field("exhibit_label", $this->input->post("exhibitName"));
        $this->case_exhibit->set_field("description", $this->input->post("exhibitDescription"));
        $this->case_exhibit->set_field("date_received", $this->input->post("exhibitDateReceived"));
        $this->case_exhibit->set_field("manner_of_disposal", $this->input->post("exhibitDisposal"));
        $this->case_exhibit->set_field("temporary_removals", $this->input->post("exhibitTemporaryRemoval"));
        $this->case_exhibit->set_field("createdBy", $this->session->userdata("AUTH_user_id"));
        $this->case_exhibit->set_field("createdOn", date("Y-m-d H:i:s"));

        if ($this->case_exhibit->insert()) {
            $exhibit_id = $this->case_exhibit->get_field("id");
            $response["status"] = true;
            $response["message"] = "Exhibit saved successfully";

            if (!empty($_FILES)) {
                $failed_uploads_count = 0;
                foreach ($_FILES as $file_key => $file_array) {
                    if (is_array($file_array['name'])) {
                        foreach ($file_array['name'] as $i => $name) {
                            if ($file_array["error"][$i] != 4) {
                                $_FILES['temp'] = [
                                    "name" => $file_array["name"][$i],
                                    "type" => $file_array["type"][$i],
                                    "tmp_name" => $file_array["tmp_name"][$i],
                                    "error" => $file_array["error"][$i],
                                    "size" => $file_array["size"][$i],
                                ];

                                $upload_response = $this->dmsnew->upload_file([
                                    "module" => "case",
                                    "module_record_id" => $case_id,
                                    "container_name" => "exhibits",
                                    "upload_key" => "temp"
                                ]);

                                if (!$upload_response["status"]) {
                                    $failed_uploads_count++;
                                } else {
                                    $this->case_exhibit_document->set_field("exhibit_id", $exhibit_id);
                                    $this->case_exhibit_document->set_field("document", $upload_response["file"]["id"]);

                                    if (!$this->case_exhibit_document->insert()) {
                                        $this->dms->delete_document($upload_response["file"]["module"], $upload_response["file"]["id"]);
                                        $failed_uploads_count++;
                                    }
                                    $this->case_exhibit_document->reset_fields();
                                }
                            }
                        }
                        unset($_FILES['temp']);
                    }
                }
                if ($failed_uploads_count > 0) {
                    $response["validationErrors"]["files"] = sprintf($this->lang->line("files_were_not_uploaded"), $failed_uploads_count);
                }
            }//if files are not empty ends
        }

        $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode($response));
    }

///**
/// end of exihibt
    private function get_provider_group_users($provider_group_id = 0)
    {
        $data = [];
        if (0 < $provider_group_id) {
            $this->load->model("user", "userfactory");
            $this->user = $this->userfactory->get_instance();
            $data["usersProviderGroup"] = $this->user->load_users_list($provider_group_id, ["key" => "id", "value" => "name"]);
            $this->provider_group->fetch($provider_group_id);
            $display_all_users_flag = $this->provider_group->get_field("allUsers");
            if ($display_all_users_flag != 1) {
                $data["usersProviderGroup"] = ["" => "---", "quick_add" => $this->lang->line("click_to_add_user_to_team")] + $data["usersProviderGroup"];
            } else {
                $data["usersProviderGroup"] = ["" => "---"] + $data["usersProviderGroup"];
            }
        } else {
            $data["usersProviderGroup"] = $this->user->load_users_list("", ["key" => "id", "value" => "name"]);
            $data["usersProviderGroup"] = ["" => "---"] + $data["usersProviderGroup"];
        }
        return $data["usersProviderGroup"];
    }
    public function edit($id = 0)
    {
        $this->load->model("case_comment", "case_commentfactory");
        $this->case_comment = $this->case_commentfactory->get_instance();

        $this->load->model("user_preference");
        $this->load->library("TimeMask");

        $systemPreferences = $this->session->userdata("systemPreferences");
        $legal_case_related_container = $this->legal_case->get_legal_case_related_container($id);
        $this->load->model("legal_case_related_container");
        $count_legal_case_related_containers = $this->legal_case->count_legal_case_related_containers($id);
        $this->load->model("legal_case_outsource", "legal_case_outsourcefactory");
        $this->legal_case_outsource = $this->legal_case_outsourcefactory->get_instance();
        if ($this->input->is_ajax_request()) {
            $action = $this->input->post("action");
            switch ($action) {
                case "editCapAmount":
                    $post_data = $this->input->post(NULL);
                    $id = (int) $this->input->post("id");
                    $this->legal_case->fetch($id);
                    $time_logs_cap_ratio = $this->input->post("time_logs_cap_ratio");
                    $expenses_cap_ratio = $this->input->post("expenses_cap_ratio");
                    $case_currency_id = $this->legal_case->get_money_currency();
                    if ($this->legal_case->get_field("client_id") && !empty($id) && !empty($case_currency_id)) {
                        $validate_capping_amount = $this->legal_case->validate_capping_amount($this->legal_case->get_field("client_id"), $id, $case_currency_id, false, false, $this->input->post(NULL));
                    }
                    if ($time_logs_cap_ratio < 0 || 100 < $time_logs_cap_ratio) {
                        $response["validationErrors"]["time_logs_cap_ratio"] = $this->lang->line("percentage_max_value");
                    } else {
                        if ($expenses_cap_ratio < 0 || 100 < $expenses_cap_ratio) {
                            $response["validationErrors"]["expenses_cap_ratio"] = $this->lang->line("percentage_max_value");
                        } else {
                            if ($this->legal_case->get_field("client_id") && !empty($id) && !empty($case_currency_id) && $validate_capping_amount == "disallow") {
                                $response["result"] = false;
                                $response["message"] = sprintf($this->lang->line("capping_amount_validation_save"), $this->legal_case->get_field("category") == "Matter" ? $this->lang->line("matter") : $this->lang->line("litigation"));
                            } else {
                                if ($this->input->post("cap_amount") == 0 && $this->input->post("cap_amount_enable") == 1) {
                                    $response["result"] = false;
                                    $response["message"] = $this->lang->line("capping_zero");
                                } else {
                                    if ($this->legal_case->get_field("client_id") && !empty($id) && !empty($case_currency_id) && $validate_capping_amount == "warning") {
                                        $response["warning"] = sprintf($this->lang->line("cap_amount_warning"), $this->legal_case->get_field("category") == "Matter" ? $this->lang->line("matter") : $this->lang->line("litigation"));
                                    }
                                    $this->legal_case->set_fields($post_data);
                                    $result = $this->legal_case->update();
                                    $this->legal_case->touch_logs();
                                    $response["result"] = $result ? true : false;
                                    $response["validationErrors"] = $this->legal_case->get("validationErrors");
                                }
                            }
                        }
                    }
                    $this->output->set_content_type("application/json")->set_output(json_encode($response));
                    break;
                case "assigneeForm":
                    $this->load->model("provider_group");
                    $data["providerGroupsList"] = $this->provider_group->load_list([], ["firstLine" => ["" => "--"]]);
                    $data["usersList"] = ["" => "--"];
                    $data["title"] = sprintf($this->lang->line("add_user_to_case"), $this->input->post("id"));
                    $data["id"] = $this->input->post("id");
                    $response["html"] = $this->load->view("cases/add_assignee", $data, true);
                    $this->output->set_content_type("application/json")->set_output(json_encode($response));
                    break;
                case "assigneeFormSubmit":
                    $validate = $this->legal_case->get("validate");
                    $validate["user_id"]["required"] = true;
                    $validate["user_id"]["message"] = $this->lang->line("cannot_be_blank_rule");
                    $this->legal_case->set("validate", $validate);
                    $this->legal_case->fetch($id);
                    $this->legal_case->set_fields($this->input->post(NULL));
                    $this->legal_case->set_field("estimatedEffort", $this->legal_case->get_field("estimatedEffort") * 1);
                    $result = $this->legal_case->update();
                    $this->legal_case->touch_logs();
                    $response["result"] = $result ? true : false;
                    $response["validationErrors"] = $this->legal_case->get("validationErrors");
                    $this->output->set_content_type("application/json")->set_output(json_encode($response));
                    break;
                case "contact_add":
                    $data["title"] = $this->input->post("title");
                    $data["field_name"] = $this->input->post("field_name");
                    $data["field_name_id"] = $this->input->post("field_name_id");
                    $data["type"] = $this->input->post("type");
                    $id = (int) $this->input->post("id");
                    $data["contactId"] = (int) $this->input->post("contactId");
                    $data["caseContactId"] = (int) $this->input->post("caseContactId");
                    $response["status"] = true;
                    $data["id"] = $id;
                    if (0 < $data["contactId"] && $this->input->post("type") == "contactType") {
                        $this->load->model("contact", "contactfactory");
                        $this->contact = $this->contactfactory->get_instance();
                        list($data["model_data"]) = $this->contact->loadAllContactData($data["contactId"]);
                    } else {
                        if (0 < $data["contactId"] && $this->input->post("type") == "companyType") {
                            $this->load->model("company", "companyfactory");
                            $this->company = $this->companyfactory->get_instance();
                            list($data["model_data"]) = $this->company->load_all_company_data($data["contactId"]);
                            $data["model_data"]["firstName"] = $data["model_data"]["name"];
                            $data["model_data"]["father"] = "";
                            $data["model_data"]["lastName"] = "";
                        } else {
                            $data["model_data"]["id"] = "";
                            $data["model_data"]["firstName"] = "";
                            $data["model_data"]["father"] = "";
                            $data["model_data"]["lastName"] = "";
                            $data["model_data"]["name"] = "";
                            $data["model_data"]["legal_case_contact_role_id"] = "";
                            $data["model_data"]["legal_case_company_role_id"] = "";
                            $data["model_data"]["comments"] = "";
                        }
                    }
                    if (0 < $data["caseContactId"] && $this->input->post("type") == "contactType") {
                        $this->load->model("legal_case_contact");
                        if ($this->legal_case_contact->fetch($data["caseContactId"])) {
                            $legal_case_contact_data = $this->legal_case_contact->get_fields();
                            $data["model_data"]["comments"] = $legal_case_contact_data["comments"];
                            $data["model_data"]["legal_case_contact_role_id"] = $legal_case_contact_data["legal_case_contact_role_id"];
                        }
                    } else {
                        if (0 < $data["caseContactId"] && $this->input->post("type") == "companyType") {
                            $this->load->model("legal_case_company");
                            if ($this->legal_case_company->fetch($data["caseContactId"])) {
                                $legal_case_company_data = $this->legal_case_company->get_fields();
                                $data["model_data"]["comments"] = $legal_case_company_data["comments"];
                                $data["model_data"]["legal_case_company_role_id"] = $legal_case_company_data["legal_case_company_role_id"];
                            }
                        } else {
                            $data["model_data"]["id"] = "";
                            $data["model_data"]["firstName"] = "";
                            $data["model_data"]["father"] = "";
                            $data["model_data"]["lastName"] = "";
                            $data["model_data"]["name"] = "";
                            $data["model_data"]["legal_case_contact_role_id"] = "";
                            $data["model_data"]["legal_case_company_role_id"] = "";
                            $data["model_data"]["comments"] = "";
                        }
                    }
                    $this->load->model(["legal_case_contact_role", "legal_case_company_role"]);
                    switch ($data["type"]) {
                        case "companyType":
                            $data["contactRoles"] = $this->legal_case_company_role->load_list([], ["firstLine" => ["" => " "]]);
                            break;
                        case "contactType":
                            $data["contactRoles"] = $this->legal_case_contact_role->load_list([], ["firstLine" => ["" => " "]]);
                            break;
                        default:
                    }
                    $response["html"] = $this->load->view("cases/contact_add", $data, true);
                    $this->output->set_content_type("application/json")->set_output(json_encode($response));
                    break;
                case "save_case":
                    $response = [];
                    $legal_case_id = $this->input->post("legal_case_id");
                    $legalCase = $this->legal_case->load_case($legal_case_id);
                    if (!$legalCase) {
                        $response["result"] = false;
                        $response["validationErrors"] = ["error_record" => $this->lang->line("invalid_record")];
                    } else {
                        $result_status = $this->_edit($legal_case_id, $legalCase["category"], $legal_case_related_container, $count_legal_case_related_containers, $legalCase["client_id"]);
                        $response["result"] = $result_status;
                        if ($result_status) {
                            $this->sla_management_mod->log_case($legal_case_id, $this->legal_case->get_field("case_status_id"), $this->is_auth->get_user_id());
                            $response["message"] = sprintf($this->lang->line("record_save_successfull"), " " . $this->legal_case->get("modelCode") . $legal_case_id);
                        } else {
                            if ($this->legal_case->is_valid()) {
                                $response["message"] = sprintf($this->lang->line("record_save_successfull"), " " . $this->legal_case->get("modelCode") . $legal_case_id);
                            } else {
                                $response["validationErrors"] = $this->legal_case->get("validationErrors");
                            }
                        }
                    }
                    $this->output->set_content_type("application/json")->set_output(json_encode($response));
                    break;
                case "add_comment":
                    $legal_case_id = (int) $this->input->post("legal_case_id");
                    $legal_case = $this->legal_case->load_case($legal_case_id);
                    if (!$legal_case) {
                        $response["status"] = false;
                        $response["validationErrors"] = ["error_record" => $this->lang->line("invalid_record")];
                    } else {
                        $response["status"] = true;
                    }
                    $data["legalCase"] = $legal_case;
                    $data["isCustomerPortal"] = !strcmp($legal_case["channel"], $this->legal_case->get("portalChannel")) ? "yes" : "no";
                    $data["visibleToCustomerPortal"] = $legal_case["visibleToCP"] == "1" ? "yes" : "no";
                    $data["isOutsourcedToAdvisors"] = $this->legal_case->case_outsourced($legal_case_id);
                    $data["id"] = $legal_case_id;
                    $data["title"] = $this->lang->line("add_note");
                    $data["hide_show_notification"] = $this->email_notification_scheme->get_hide_show_send_email_notification_by_trigger_action("add_note_case") == "1" ? "yes" : "";
                    $response["html"] = $this->load->view("cases/add_comment", $data, true);
                    $this->output->set_content_type("application/json")->set_output(json_encode($response));
                    break;
                default:

                    $this->actions_related_contacts_companies();
            }

        } else {
            $this->load->helper(["text"]);
            if (!$this->input->post(NULL)) {
                if ($id < 1 || !$this->legal_case->fetch($id)) {
                    $this->set_flashmessage("error", $this->lang->line("invalid_record"));
                    redirect("dashboard");
                }
                $legalCase = $this->legal_case->load_case($id);
                $this->legal_case->update_recent_ids($id, $legalCase["category"] == "Matter" ? "corporate_matters" : "litigation_cases");
            } else {
                $legalCase = $this->legal_case->load_case($id);
                if (!$legalCase) {
                    $this->set_flashmessage("error", $this->lang->line("invalid_record"));
                    redirect("dashboard");
                }
                $result_status = $this->_edit($id, $legalCase["category"], $legal_case_related_container, $count_legal_case_related_containers);
                if ($result_status) {
                    $this->set_flashmessage("success", sprintf($this->lang->line("record_save_successfull"), " " . $this->legal_case->get("modelCode") . $id));
                    redirect("cases/edit/" . $id, "location");
                } else {
                    if ($this->legal_case->is_valid()) {
                        redirect("cases/edit/" . $id);
                    }
                }
                $legalCase = $this->legal_case->load_case($id);
            }
            $legalCasewatchersUsers = $this->legal_case->load_watchers_users($id);
            $isLitigationCase = $legalCase["category"] == "Matter" ?false:true;
            $data = $this->_load_related_models($isLitigationCase);
            if (!empty($legalCase["legal_case_stage_id"]) && $isLitigationCase && !empty($data["caseStages"])) {
                unset($data["caseStages"][""]);
            }
            $data["partnersCommissions"] = "no";
            if (!empty($systemPreferences["partnersCommissions"])) {
                $partnersCommissions = unserialize($systemPreferences["partnersCommissions"]);
                $organization_id = $this->user_preference->get_value("organization");
                if (!empty($organization_id) && isset($partnersCommissions[$organization_id]) && !empty($partnersCommissions[$organization_id])) {
                    $data["partnersCommissions"] = $partnersCommissions[$organization_id];
                }
            }
            if (isset($systemPreferences["AllowFeatureSLAManagement"])) {
                $data["slaFeature"] = $systemPreferences["AllowFeatureSLAManagement"];
            } else {
                $data["slaFeature"] = "no";
            }
            $data["sharedDocumentsWithAdvisors"] = $this->legal_case->shared_documents_with_advisors($id);
            $data["disableArchivedMatters"] = $systemPreferences["disableArchivedMatters"];
            $data["hide_show_notification"] = $this->email_notification_scheme->get_hide_show_send_email_notification_by_trigger_action("add_note_case") == "1" ? "yes" : "";
            $data["tabsNLogs"] = $this->get_vertical_case_tabs($id, site_url("cases/edit/"));
            $case_status_id = $this->legal_case->get_field("case_status_id");
            $this->workflow->fetch(0 < $this->legal_case->get_field("workflow") ? $this->legal_case->get_field("workflow") : 1);
            $data["workflow_applicable"] = $this->workflow->get_fields();
            $this->load->model("workflow_status_transition_permission");
            $statuses_accessible = $this->workflow_status_transition_permission->get_allowed_workflow_statuses($case_status_id, $data["workflow_applicable"]["id"]);
            $data["Case_Statuses"] = $statuses_accessible["case_statuses"];
            $data["statusTransitions"] = $statuses_accessible["status_transitions"];
            $this->provider_group->fetch(["allUsers" => 1]);
            $data["allUsersProviderGroupId"] = $this->provider_group->get_field("id");
            $data["usersProviderGroup"] = $this->get_provider_group_users($this->legal_case->get_field("provider_group_id"));
            $data["id"] = $id;
            $data["legalCase"] = $legalCase;
            $data["legalCasewatchersUsers"] = isset($legalCasewatchersUsers[0]) ? $legalCasewatchersUsers[0] : [];
            $data["legalCasewatchersUsersStatus"] = isset($legalCasewatchersUsers[1]) ? $legalCasewatchersUsers[1] : [];
            $this->load->model("custom_field", "custom_fieldfactory");
            $this->custom_field = $this->custom_fieldfactory->get_instance();
            $customFields = $this->custom_field->get_field_html("legal_case", $id, NULL, false, ["form" => "legalCaseAddForm"]);
            $data["custom_fields"] = [];
            $section_types = $this->custom_field->section_types;
            if (!empty($customFields)) {
                foreach ($customFields as $field) {
                    if ($field["type"] === "lookup") {
                        $field["value"] = $this->custom_field->get_lookup_data($field);
                    }
                    $data["custom_fields"][$section_types[$field["type"]]][] = $field;
                }
            }
            $data["nbOfNotesHistory"] = $this->case_comment->count_all_case_comments($id);
            $this->load->model(["legal_case_contact_role", "legal_case_company_role"]);
            $data["contactGridRoles"] = $this->legal_case_contact_role->load_list([], ["firstLine" => [" " => " "]]);
            $data["contactRoles"] = $this->legal_case_contact_role->load_list([], ["firstLine" => ["" => " "]]);
            $data["companyGridRoles"] = $this->legal_case_company_role->load_list([], ["firstLine" => [" " => " "]]);
            $data["companyRoles"] = $this->legal_case_company_role->load_list([], ["firstLine" => ["" => " "]]);
            $this->load->model("client");
            $data["clientData"] = $this->client->fetch_client($this->legal_case->get_field("client_id"));
            $data["clientCompanyCategory"] = "";
            if (isset($data["clientData"]) && !empty($data["clientData"]) && $data["clientData"]["type"] === "company") {
                $this->company->fetch($data["clientData"]["member_id"]);
                $data["clientCompanyCategory"] = $this->company->get_field("category");
                $this->company->reset_fields();
            }
            $relatedOpponentData = $this->legal_case_opponent->fetch_case_opponents_data($this->legal_case->get_field("id"));
            if (!empty($relatedOpponentData)) {
                $data["relatedOpponentData"] = $relatedOpponentData;
            } else {
                $data["relatedOpponentData"] = [["opponent_member_type" => "company", "opponentName" => "", "opponent_member_id" => "", "opponentCompanyCategory" => "", "position_name" => "", "opponent_position" => "", "opponentForeignName" => ""]];
            }
            $trigger_name = $data["legalCase"]["category"] === "Litigation" ? "edit_litigation_case" : "edit_matter_case";
            $data["hide_show_notification_edit_legal_case"] = $this->email_notification_scheme->get_hide_show_send_email_notification_by_trigger_action($trigger_name) == "1" ? "yes" : "";
            $this->load->model("reminder", "reminderfactory");
            $this->reminder = $this->reminderfactory->get_instance();
            $data["notify_me_before_time_types"] = array_combine($this->reminder->get("notify_me_before_time_type"), [$this->lang->line("day_or_days"), $this->lang->line("week_or_weeks"), $this->lang->line("month")]);
            $data["notify_me_before_types"] = array_combine($this->reminder->get("notify_me_before_type"), [$this->lang->line("reminder_popup"), $this->lang->line("reminder_popup_and_email")]);
            $data["notify_before"] = $this->reminder->load_notify_before_data_to_related_object($id, $this->legal_case->get("_table"));
            $data["isCustomerPortal"] = !strcmp($this->legal_case->get_field("channel"), $this->legal_case->get("portalChannel")) ? "yes" : "no";
            $data["visibleToCustomerPortal"] = $this->legal_case->get_field("visibleToCP") == "1" ? "yes" : "no";
            $data["usersList"] = $this->user->load_available_list();

            $data["category"] ="matter_";
            $view_file="";//to differentiate the edit-view file. Atinga
            if ($legalCase["category"] == "Litigation") {
                $data["category"] ="litigation_";
            } elseif ($legalCase["category"] == "Criminal") {
                $data["category"] = "criminal_";
                $view_file="_criminal";
            }
            $data["stageID"]= $this->legal_case->get_field("stage");
            $data["legal_case_related_container"] = $legal_case_related_container;
            $data["count_legal_case_related_containers"] = $count_legal_case_related_containers;
            $data["max_opponents"] = $systemPreferences["caseMaxOpponents"];
            $this->load->model("contact_company_category");
            $data["outsource_categories"] = $this->contact_company_category->load_categories_per_lang("none");

            ///documents in the main page
            ///
            $this->load->model(["case_document_status", "case_document_classification", "case_document_type", "legal_case_archived_hard_copy"]);
            $this->load->model("legal_case_document", "legal_case_documentfactory");

            $data["documentStatuses"] = $this->case_document_status->load_list([], ["firstLine" => ["" => " "]]);
            $data["documentTypes"] = $this->case_document_type->load_list([], ["firstLine" => ["" => " "]]);
            $data["module"] = "case";
            $data["module_record_id"] = $id;
            $data["module_controller"] = "cases";
            $data["module_prefix"] = "legal_case";
            /// docs in main page
            $this->includes("kendoui/js/kendo.web.min", "js");
            $this->includes("kendoui/styles/kendo.common-bootstrap.min", "css");
            $this->includes("kendoui/styles/kendo.bootstrap.min", "css");
            $this->includes("jquery/css/chosen", "css");
            $this->includes("jquery/form2js", "js");
            $this->includes("jquery/toObject", "js");
            $this->includes("jquery/chosen.min", "js");
            $this->includes("jquery/tinymce/tinymce.min", "js");
            $this->includes("jquery/jquery.dirtyform", "js");
            $this->includes("scripts/legal_case_grid", "js");
            $this->includes("scripts/show_hide_customer_portal", "js");
            $this->includes("scripts/legal_case_events", "js");
            $this->includes("scripts/legal_case_contacts_companies", "js");
            $this->includes("scripts/legal_case_outsource", "js");
            $this->includes("scripts/legal_case_court_activities_basic_list","js");//include basic list for court activities in general tab

              $this->includes("scripts/case_opponents","js");
            $this->includes("scripts/case_tasks_summary","js");


            $this->includes(app_url("compressed_asset/system_defaults.js?a=" . random_string("alpha")), "js", true);
            $this->includes("scripts/form_custom_field_template", "js");
            $this->includes("customerPortal/clientPortal/css/datatables.min", "css");
            $this->includes("customerPortal/clientPortal/js/datatables.min", "js");
            $this->includes("customerPortal/clientPortal/js/jquery.dataTables.min", "js");
            $this->includes("customerPortal/clientPortal/js/dataTables.bootstrap.min", "js");
            $this->includes("scripts/litigation_stage", "js");
            $this->includes("scripts/courts", "js");
            $this->includes("jquery/spectrum", "js");
            $this->includes("styles/ltr/fixes", "css");
            $this->includes("autonumeric/autoNumeric.min", "js");
            $this->includes("jquery/selectize.min", "js");
            $this->includes("jquery/css/selectize", "css");
            if ($this->is_auth->is_layout_rtl()) {
                $this->includes("styles/rtl/fixes", "css");
            }

            $title_trans = "legal_matters";
            $this->pageTitle = sprintf($this->lang->line("sheria360_browser_title"), $this->lang->line($title_trans));
            $this->load->view("partial/header");
            $this->load->view("cases/edit".$view_file, $data);
        }
    }
    private function _edit($caseId, $caseType, $legal_case_related_container = NULL, $count_legal_case_related_containers = NULL, $legal_case_client_id = NULL)
    {
        if ($this->license_availability === false) {
            $this->set_flashmessage("error", $this->licensor->get_license_message(MODULE));
            redirect("cases/edit/" . $caseId);
        }
        $oldValues = $this->legal_case->get_old_values($caseId);
        $workflow_applicable = 0 < $this->input->post("workflow") ? $this->input->post("workflow") : 1;
        $availableStatuses = $this->workflow_status->getAvailableCaseStatuses($oldValues["case_status_id"], $workflow_applicable);
        $availableStatusesKeys = array_keys($availableStatuses);
        if ($this->input->post("case_status_id") && $this->input->post("case_status_id") && !in_array($this->input->post("case_status_id"), $availableStatusesKeys)) {
            $this->set_flashmessage("error", $this->lang->line("invalid_record"));
            redirect("dashboard");
        }
        $opponent_member_types = $this->input->post("opponent_member_type");
        $opponent_member_ids = $this->input->post("opponent_member_id");
        $opponent_positions = $this->input->post("opponent_position");
       
        $LegalCaseFields = $this->input->post(NULL);
        if ($oldValues["channel"] == "CP") {
            $this->legal_case->fetch(["id" => $caseId]);
            $assigned_on = $this->legal_case->get_field("assignedOn");
            if (isset($LegalCaseFields["user_id"]) && !empty($LegalCaseFields["user_id"]) && !isset($oldValues["user_id"]) && !$oldValues["user_id"] && !isset($assigned_on) && !$assigned_on) {
                $this->legal_case->set_field("assignedOn", date("Y-m-d H:i:s"));
            }
            if (isset($assigned_on) && $assigned_on) {
                $this->legal_case->set_field("assignedOn", $assigned_on);
            }
        }
        if ($this->input->post("case_type_id") && $this->input->post("case_type_id") != $oldValues["case_type_id"]) {
            $workflow_applicable = $this->workflow_status->getWorkflowOfAreaPractice($this->input->post("case_type_id"), strtolower($caseType));
            if (!empty($workflow_applicable)) {
                $LegalCaseFields["case_status_id"] = $this->workflow_status->get_workflow_start_point($workflow_applicable["workflow"]);
                $LegalCaseFields["workflow"] = $workflow_applicable["workflow"];
                $_POST["case_status_id"] = $LegalCaseFields["case_status_id"];
                $_POST["workflow"] = $LegalCaseFields["workflow"];
            } else {
                $workflow_applicable = $this->workflow_status->getDefaultSystemWorkflow(strtolower($caseType));
                if (isset($workflow_applicable["workflow"]) && $workflow_applicable["workflow"] != $LegalCaseFields["workflow"]) {
                    $LegalCaseFields["workflow"] = $workflow_applicable["workflow"] ? $workflow_applicable["workflow"] : "1";
                    $LegalCaseFields["case_status_id"] = ($status_data = $this->workflow_status->get_workflow_start_point($LegalCaseFields["workflow"])) ? $status_data : "1";
                    $_POST["case_status_id"] = $LegalCaseFields["case_status_id"];
                    $_POST["workflow"] = $LegalCaseFields["workflow"];
                }
            }
        }
        unset($LegalCaseFields["opponent_position"]);
        $this->legal_case->set_fields($LegalCaseFields);
        $postCaseValue = str_replace(",", "", $this->input->post("caseValue"));
        $postRecoveredValue = str_replace(",", "", $this->input->post("recoveredValue"));
        $postJudgmentValue = str_replace(",", "", $this->input->post("judgmentValue"));
        $this->legal_case->set_field("caseValue", isset($postCaseValue) && $postCaseValue ? $postCaseValue : "0.00");
        $this->legal_case->set_field("recoveredValue", isset($postRecoveredValue) && $postRecoveredValue ? $postRecoveredValue : "0.00");
        $this->legal_case->set_field("judgmentValue", isset($postJudgmentValue) && $postJudgmentValue ? $postJudgmentValue : "0.00");
        $estimated_effort_value = $this->timemask->humanReadableToHours($this->input->post("estimatedEffort"));
        if ($estimated_effort_value != -1) {
            $this->legal_case->set_field("estimatedEffort", $estimated_effort_value);
        }
        $contact_company_id = $this->input->post("contact_company_id");
        $clientModel = $this->input->post("clientType") == "contacts" ? "contact" : "company";
        $this->load->model("client");
        $clientId = $contact_company_id ? $this->client->get_client($clientModel, $contact_company_id) : NULL;
        $this->legal_case->set_field("client_id", $clientId);
        $caseId = $this->legal_case->get_field("id");
        $this->legal_case->set_field("channel", $oldValues["channel"]);
        $this->legal_case->set_field("modifiedByChannel", $this->legal_case->get("webChannel"));
        $this->legal_case->set_field("isDeleted", 0);
        $this->load->model("opponent");
        $opponents_old_data = $this->legal_case_opponent->fetch_case_opponents_data($this->input->post("id"));
        $opponentsData = is_array($opponent_member_types) && !empty($opponent_member_types) && is_array($opponent_member_ids) && !empty($opponent_member_ids) ? $this->opponent->get_opponents($opponent_member_types, $opponent_member_ids) : [];
        if (!empty($opponentsData)) {
            foreach ($opponentsData as $key => $value) {
                $opponentsData[$key]["case_id"] = $caseId;
                $opponentsData[$key]["opponent_position"] = isset($opponent_positions[$key]) && !empty($opponent_positions[$key]) ? $opponent_positions[$key] : NULL;
            }
        }
        $this->legal_case_opponent->insert_case_opponents($caseId, $opponentsData);
        if ($result = $this->legal_case->update()) {
            if ($this->system_preference->get_values()["webhooks_enabled"] == 1) {
                $webhook_data = $this->legal_case->load_case_details($caseId);
                $this->legal_case->trigger_web_hook($webhook_data["category"] == "Matter" ? "matter_updated" : "litigation_updated", $webhook_data);
            }
            $client_id_old = $legal_case_client_id;
            $client_id = $this->legal_case->get_field("client_id");
            if (isset($client_id) && isset($client_id_old) && $client_id != $client_id_old) {
                $this->load->model("client_partner_share");
                $shares = $this->client_partner_share->load_partners_shares($client_id);
                $partners_shares = [];
                $this->load->model("legal_case_partner_share");
                foreach ($shares as $key => $partner_share) {
                    $partners_shares[$key]["case_id"] = $caseId;
                    $partners_shares[$key]["account_id"] = $partner_share["id"];
                    $partners_shares[$key]["percentage"] = $partner_share["percentage"];
                }
                $this->legal_case_partner_share->save_partners_shares($caseId, $partners_shares);
            }
            $this->_delete_related_contacts_companies_for_changed_values($oldValues);
            $this->_delete_related_opponents_for_changed_values($opponents_old_data);
            $this->_feed_related_contacts_from_referred_by("edit");
            $this->_feed_related_contacts_from_requested_by("edit");
            $this->_feed_related_contacts_from_client_contact("edit");
            $this->_feed_related_contacts_from_opponent_contact("edit");
            $this->_feed_related_companies_from_client_company("edit");
            $this->_feed_related_companies_from_opponent_company("edit");
            $LegalCaseCustomFields = $this->input->post("customFields");
            if (is_array($LegalCaseCustomFields) && count($LegalCaseCustomFields)) {
                $this->load->model("custom_field", "custom_fieldfactory");
                $this->custom_field = $this->custom_fieldfactory->get_instance();
                $this->custom_field->update_custom_fields($LegalCaseCustomFields);
            }
            $LegalCaseWatchersUsers["users"] = ["legal_case_id" => $caseId, "users" => $this->input->post("private") == "yes" ? $this->input->post("Legal_Case_Watchers_Users") : []];
            $this->legal_case->insert_watchers_users($LegalCaseWatchersUsers);
            $this->legal_case->touch_logs("update", $oldValues);
            if ($caseType == "Matter") {
                $this->load->model("legal_case_stage_changes", "legal_case_stage_changesfactory");
                $this->legal_case_stage_changes = $this->legal_case_stage_changesfactory->get_instance();
                $case_stage_changes = ["legal_case_id" => $this->legal_case->get_field("id"), "oldValue" => $oldValues["legal_case_stage_id"], "legal_case_stage_id" => $this->legal_case->get_field("legal_case_stage_id"), "modifiedOn" => $this->legal_case->get_field("modifiedOn")];
                $this->legal_case_stage_changes->log_changes($case_stage_changes);
            }
            $this->notify_me_before_due_date($caseId);
            $assignment = $this->legal_case->get_field("user_id");
            $object = "edit_" . strtolower($caseType) . "_case";
            $notifications_data["object"] = $object;
            $notifications_data["object_id"] = $caseId;
            $notifications_data["caseSubject"] = $this->legal_case->get_field("subject");
            $notifications_data["objectName"] = strtolower($caseType);
            if ($assignment) {
                $this->load->library("system_notification");
                $this->load->model("user_profile");
                $this->load->model("user", "userfactory");
                $this->user = $this->userfactory->get_instance();
                $this->legal_case->fetch($caseId);
                $this->user->fetch($this->legal_case->get_field("createdBy"));
                $creator_email = $creator_id = [];
                if ($oldValues["channel"] != "CP") {
                    $creator_email = [$this->user->get_field("email")];
                    $creator_id = [str_pad($this->legal_case->get_field("createdBy"), 10, "0", STR_PAD_LEFT)];
                }
                $notifications_data["to"] = $assignment;
                $notifications_data["cc"] = $creator_email;
                $notifications_data["ccIds"] = $creator_id;
                $notifications_data["targetUser"] = $assignment;
                $notifications_data["secondTargetUser"] = $creator_id;
                $notifications_data["objectModelCode"] = $this->legal_case->get("modelCode");
                $this->system_notification->notification_add($notifications_data);
                $model_data = [];
                $this->load->model("user_rate_per_hour_per_case", "user_rate_per_hour_per_casefactory");
                $this->user_rate = $this->user_rate_per_hour_per_casefactory->get_instance();
                $userRateExists = $this->user_rate->check_user_rate_per_case_existence($assignment, $caseId);
                if (!$userRateExists) {
                    $organizations = $this->user_rate->get_entities();
                    $organizationID = $organizations[0]["id"];
                    $userRate = $this->get_rate_by_user_id($assignment, $caseId, $organizationID, true);
                    if (0 < $userRate) {
                        $this->user_rate->add_rate($assignment, $caseId, $organizationID, $userRate);
                    }
                }
            }
            $sendEmailFlag = $this->input->post("send_notifications_email");
            if ($sendEmailFlag) {
                $this->load->library("email_notifications");
                if ($caseType == "Matter" && !strcmp($this->legal_case->get_field("channel"), $this->legal_case->get("portalChannel"))) {
                    $model_data["unsetCreator"] = "yes";
                }
                $model_data["id"] = $caseId;
                $modified_by = $this->email_notification_scheme->get_user_full_name($this->legal_case->get_field("modifiedBy"));
                $model = $this->legal_case->get("_table");
                $notifications_emails = $this->email_notification_scheme->get_emails($object, $model, $model_data);
                $client_data = $this->client->fetch_client($this->legal_case->get_field("client_id"));
                extract($notifications_emails);
                $notifications_data["to"] = $to_emails;
                $notifications_data["cc"] = $cc_emails;
                $notifications_data["client_name"] = $client_data["clientName"] ?? "";
                $notifications_data["assignee"] = $this->email_notification_scheme->get_user_full_name($this->legal_case->get_field("user_id"));
                $notifications_data["file_reference"] = $this->legal_case->get_field("internalReference");
                $notifications_data["modified_by"] = $modified_by;
                $notifications_data["fromLoggedUser"] = $this->is_auth->get_fullname();
                $this->email_notifications->notify($notifications_data);
            }
            if ($this->input->post("legalCaseRelatedContainerId") && (!empty($legal_case_related_container["data"]["id"]) && $this->input->post("legalCaseRelatedContainerId") != $legal_case_related_container["data"]["id"] || !empty($count_legal_case_related_containers["data"]["totalRows"]) && 1 < $count_legal_case_related_containers["data"]["totalRows"] || empty($legal_case_related_container["data"]["id"]))) {
                $this->legal_case_related_container->set_legal_case_container($caseId, $this->input->post("legalCaseRelatedContainerId"));
            } else {
                if (!$this->input->post("legalCaseRelatedContainerId")) {
                    $this->legal_case_related_container->delete(["where" => ["legal_case_id", $caseId]]);
                }
            }
            return true;
        } else {
            return false;
        }
    }

///save single paty
   public function save_party() {
    if (!$this->input->is_ajax_request() || !$this->is_auth->get_user_id()) {
        show_404();
    }

    $this->load->model("opponent");
    
    $case_id = $this->input->post("case_id");

    $opponent_member_type = $this->input->post("opponent_member_type");
    $opponent_member_id = $this->input->post("opponent_member_id");
    $opponent_position = $this->input->post("opponent_position");


    $response = ['success' => false, 'message' => ''];
    $proceed = true;

if( $this->legal_case->fetch($case_id)){
       $proceed = true;
}
  

    // Early validation checks
    if ($proceed && (!is_numeric($opponent_position) || $opponent_position <= 0)) {
        $response = ['success' => false, 'message' => 'Valid Party position is required.'];
        $proceed = false;
    }

    if ($proceed && (empty($case_id) || empty($opponent_member_type) || empty($opponent_member_id) || empty($opponent_position))) {
        $response = ['success' => false, 'message' => 'Missing required opponent data.'];
        $proceed = false;
    }

    try {
        if ($proceed) {
            // Verify opponent position exists in database
            $position_exists = $this->db->where('id', $opponent_position)
                                       ->get('legal_case_opponent_positions')
                                       ->row();
            
            if (!$position_exists) {
                $response = ['success' => false, 'message' => 'Invalid opponent position selected.'];
                $proceed = false;
            }
        }

        if ($proceed) {
            // 1. GET EXISTING OPPONENTS (for the complete set)
            $existing_opponents = $this->legal_case_opponent->fetch_case_opponents_data($case_id);
            
            // 2. GET OPPONENT RECORD (this finds or creates the opponent entry)
            $opponentData = $this->opponent->get_opponents([$opponent_member_type], [$opponent_member_id]);
            
            if (empty($opponentData)) {
                $response = ['success' => false, 'message' => 'Opponent not found or could not be created.'];
                $proceed = false;
            }
        }

        if ($proceed) {
            // 3. PREPARE ALL OPPONENTS FOR THE CASE (existing + new)
            $all_opponents_data = [];
            
            // Add existing opponents first (if any) - WITH POSITION VALIDATION
            if (is_array($existing_opponents) && !empty($existing_opponents)) {
                foreach ($existing_opponents as $existing) {
                    // Validate existing opponent position
                    $existing_position = !empty($existing['opponent_position']) && is_numeric($existing['opponent_position']) 
                                       ? $existing['opponent_position'] 
                                       : null;
                    
                    // Only add if position is valid
                    if ($existing_position) {
                        $all_opponents_data[] = [
                            'case_id' => $case_id,
                            'opponent_id' => $existing['opponent_id'],
                            'opponent_member_type' => $existing['opponent_member_type'],
                            'opponent_position' => $existing_position,
                          
                        ];
                    }
                }
            }
            
            // Add the new opponent (avoid duplicates)
            $new_opponent_id = $opponentData[0]['opponent_id'] ?? null;
            $is_duplicate = false;
            
            if ($new_opponent_id) {
                // Check if this opponent already exists in the case
                foreach ($all_opponents_data as $opponent) {
                    if ($opponent['opponent_id'] == $new_opponent_id) {
                        $is_duplicate = true;
                        break;
                    }
                }
                
                if (!$is_duplicate) {
                    $all_opponents_data[] = [
                        'case_id' => $case_id,
                        'opponent_id' => $new_opponent_id,
                        'opponent_member_type' => $opponent_member_type,
                        'opponent_position' => $opponent_position,
                        
                    ];
                }
            }

            // Final validation - ensure no empty positions
            $valid_opponents_data = array_filter($all_opponents_data, function($opponent) {
                return !empty($opponent['opponent_position']) && is_numeric($opponent['opponent_position']);
            });

            // Check if we have any valid opponents to save
            if (empty($valid_opponents_data)) {
                $response = ['success' => false, 'message' => 'No valid opponents data to save.'];
                $proceed = false;
            }
        }

        if ($proceed) {
            // 4. SAVE ALL OPPONENTS TO DATABASE
            $result = $this->legal_case_opponent->insert_case_opponents($case_id, $valid_opponents_data);

            if ($result) {
                // 5. UPDATE RELATIONSHIPS (only if this is a new opponent)
                if (!$is_duplicate) {
                    $this->_feed_related_contacts_from_opponent_contact("edit");
                    $this->_feed_related_companies_from_opponent_company("edit");
                }

                $response = [
                    'success' => true, 
                    'message' => $is_duplicate ? 'Party already exists.' : 'Party added successfully.',
                    'is_duplicate' => $is_duplicate,
                    'opponents_count' => count($valid_opponents_data)
                ];
            } else {
                $response = ['success' => false, 'message' => 'Failed to save opponents.'];
            }
        }

    } catch (Exception $e) {
        $this->write_log('Save party failed: ' . $e->getMessage(), "ERROR");
        $response = ['success' => false, 'message' => 'An error occurred while saving opponent.'];
    }

    $this->output->set_content_type("application/json")->set_output(json_encode($response));
}
//refresh the list of parties/opponents
public function refresh_party_list(){
   
         $response=["success"=>false,"message"=>""];
             $case_id=$this->input->post("case_id");
  
          
            $data["parties"]=  $this->legal_case_opponent->fetch_case_opponents_data($case_id);
            $data['case_id']=$case_id; 
            $response["html"] =  $this->load->view("cases/parties/view2", $data, true);

             $this->output->set_content_type("application/json")->set_output(json_encode($response));
}
///remove_party
public function remove_party() {
    $response = ["success" => false, "message" => ""];

    if (!$this->input->is_ajax_request() || !$this->is_auth->get_user_id()) {
        show_404();
    }

    $case_id = $this->input->post("case_id");
    $party_id = $this->input->post("opponent_id"); // legal_case_opponent.id or opponent_id

    if (empty($case_id) || empty($party_id)) {
        $response["message"] = "Missing required data.";
        $this->output->set_content_type("application/json")->set_output(json_encode($response));
        return;
    }
    // Remove the party/opponent from the case
    $delete_result = $this->legal_case_opponent->delete([
        "where" => [
            ["case_id", $case_id],
            ["opponent_id", $party_id]
        ]
    ]);
    // Remove related contact if exists. Later implementation    
    //$this->legal_case_contact->delete([ "where" => [ ["case_id", $case_id], ["contact_id", $party_id] ]    ]);
    if ($delete_result) {
        $response["success"] = true;
        $response["message"] = "Party removed successfully.";
    } else {
        $response["message"] = "Failed to remove party.";
    }
    $this->output->set_content_type("application/json")->set_output(json_encode($response));
}

    public function getLegalCaseSpecificFieldValue($case_id,$field)///to fetch the  specific field of a case
    {
        $response["value"]="";
        $response["result"] = false;
        if (0<$case_id && !empty($field)) {
            $response["value"]= $this->legal_case->getLegalCaseSpecificFieldValue($case_id, $field);
            $response["result"] = true;
        }else{
            $response['result'] = false;
        }
        return $response;
    }
    private function _delete_related_contacts_companies_for_changed_values($oldValues)
    {
        $old_client_type = "";
        $this->load->model("legal_case_company");
        $this->load->model("legal_case_contact");
        $this->load->model("client");
        if ($oldValues["client_id"]) {
            $this->client->fetch($oldValues["client_id"]);
            $old_client = $this->client->get_fields();
            $old_client_type = $old_client["company_id"] ? "companies" : "contacts";
            $old_client_id = $old_client["company_id"] ? $old_client["company_id"] : $old_client["contact_id"];
            if ($old_client_type == $this->input->post("clientType") && $old_client_id == $this->input->post("contact_company_id")) {
                return NULL;
            }
            if ($old_client_type == "companies") {
                $this->legal_case_company->delete(["where" => [["case_id", $this->input->post("id")], ["company_id", $old_client_id]]]);
            } else {
                $this->legal_case_contact->delete(["where" => [["case_id", $this->input->post("id")], ["contact_id", $old_client_id]]]);
            }
        }
    }
    private function _delete_related_opponents_for_changed_values($opponents_old_data)
    {
        $opponents_new_values_ids = $this->input->post("opponent_member_id");
        $opponents_new_values_types = $this->input->post("opponent_member_type");
        foreach ($opponents_old_data as $index => $opponent_memmber) {
            if ($opponent_memmber["opponent_member_type"] === "contact") {
                $key = array_search($opponent_memmber["opponent_member_id"], $opponents_new_values_ids);
                if (false === $key || $opponents_new_values_types[$key] !== "contact") {
                    $this->legal_case_contact->delete(["where" => [["case_id", $this->input->post("id")], ["contact_id", $opponent_memmber["opponent_member_id"]]]]);
                }
            } else {
                $key = array_search($opponent_memmber["opponent_member_id"], $opponents_new_values_ids);
                if (false === $key || $opponents_new_values_types[$key] !== "company") {
                    $this->legal_case_company->delete(["where" => [["case_id", $this->input->post("id")], ["company_id", $opponent_memmber["opponent_member_id"]]]]);
                }
            }
        }
    }
    private function _load_related_models($litigationFlag = "litigation")
    {
        $data = [];
        $this->load->model("legal_case_stage", "legal_case_stagefactory");
        $this->legal_case_stage = $this->legal_case_stagefactory->get_instance();
        $this->load->model(["case_type", "provider_group"]);
        $this->load->model("company", "companyfactory");
        $this->company = $this->companyfactory->get_instance();
        $this->load->model("user", "userfactory");
        $this->user = $this->userfactory->get_instance();
        $this->load->model("contact", "contactfactory");
        $this->contact = $this->contactfactory->get_instance();
        $data["Case_Statuses"] = $this->workflow_status->loadStatusesUniqueList();

        if (strtolower($litigationFlag)==="litigation"||strtolower($litigationFlag==='criminal')) {
            $cat=$litigationFlag;
          //  $cat==1?"litigation":$cat ;//to cater for previus records that dont have category- legacy//
            $data["Case_Types"] = $this->case_type->load_list(["where" => [[$cat, "yes"], ["isDeleted", 0]], "order_by" => ["name", "asc"]], ["firstLine" => ["" => $this->lang->line("choose_case_type")]]);
            $data["caseStages"] = $this->legal_case_stage->load_list_per_case_category_per_language("litigation");
            if($cat=="criminal"){
                $this->load->model("case_offense_subcategory","case_offense_subcategoryfactory");
                $this->case_offense_subcategory=$this->case_offense_subcategoryfactory->get_instance();
                $data["offense_subcategories"] = $this->case_offense_subcategory->load_list();
            }
        } else {
            $data["Case_Types"] = $this->case_type->load_list(["where" => [["corporate", "yes"], ["isDeleted", 0]], "order_by" => ["name", "asc"]], ["firstLine" => ["" => $this->lang->line("choose_case_type")]]);
            $data["caseStages"] = $this->legal_case_stage->load_list_per_case_category_per_language("corporate");
        }
        $data["workflowStatusesStartCase"] = $this->workflow_status->getGlobalStatuses($this->defaultWorkflow);
        $this->load->model("legal_case_client_position", "legal_case_client_positionfactory");
        $this->legal_case_client_position = $this->legal_case_client_positionfactory->get_instance();
        $data["clientPositions"] = ["" => $this->lang->line("none")] + $this->legal_case_client_position->load_list_per_language();
        $this->load->model("legal_case_success_probability", "legal_case_success_probabilityfactory");
        $this->legal_case_success_probability = $this->legal_case_success_probabilityfactory->get_instance();
        $data["successProbabilities"] = $this->legal_case_success_probability->load_list_per_language();
        $data["Provider_Groups"] = ["" => $this->lang->line("none")] + $this->provider_group->load_list([]);
        $data["priorities"] = array_combine($this->legal_case->get("priorityValues"), [$this->lang->line("critical"), $this->lang->line("high"), $this->lang->line("medium"), $this->lang->line("low")]);
        $data["externalizeLawyers"] = array_combine($this->legal_case->get("externalizeLawyersValues"), ["", $this->lang->line("yes"), $this->lang->line("no")]);
        $data["externalizeLawyers"][""] = "";
        $data["usersProviderGroup"] = [];
        $this->load->model("legal_case_opponent_position", "legal_case_opponent_positionfactory");
        $this->legal_case_opponent_position = $this->legal_case_opponent_positionfactory->get_instance();
        $data["opponent_positions"] = ["" => $this->lang->line("none")] + $this->legal_case_opponent_position->load_list_per_language();
        return $data;
    }
    private function get_vertical_case_tabs(&$id = "", $active = "")
    {
        $have_expenses_tab_access = $this->is_auth->check_uri_permissions("/cases/", "/cases/expenses/", "core", true, true);
        $have_time_logs_access = $this->is_auth->check_uri_permissions("/cases/", "/cases/time_logs/", "core", true, true);
        $this->legal_case->reset_fields();
        if ($id < 1 || !$this->legal_case->fetch($id) || $this->legal_case->get_field("category") == "IP") {
            redirect("dashboard");
        }
        $is_case_deleted = $this->legal_case->get_field("isDeleted");
        if ($is_case_deleted == 1) {
            $this->set_flashmessage("error", $this->lang->line("invalid_record"));
            redirect("dashboard");
        }
        $isFetched = $this->legal_case->load_visible_case($id);
        if ($id && !$isFetched) {
            $this->set_flashmessage("warning", $this->lang->line("case_access_denied"));
            redirect("dashboard");
        }
        $this->pageTitle = sprintf($this->lang->line("sheria360_browser_title"), $this->lang->line("case_in_menu"));
        $id = $this->legal_case->get_field("id");
        $data = [];
        $data["subNavItems"] = [];
        $category = $this->legal_case->get_field("category") == "Litigation" ? "litigation_" : "matter_";
        $data["category"] = $category;
        $data["id"] = $id;
        if ($data["id"]) {
            $data["subNavItems"][site_url("cases/edit/")] = ["icon" => "spr spr-matter", "name" => $this->lang->line("case_public_info"), "sub-menu" => ["custom_fields_div" => "custom_fields", "outsourcing_to_lawyers_div" => "outsourcing_to_lawyers", "related_contributors_div" => "related_contributors", "case_notes_tabs_btn_container" => "notes", "case_history_div" => "case_history"]];
            if ($this->legal_case->get_field("category") == "Litigation") {
                $data["subNavItems"][site_url("cases/events/")] = ["icon" => "spr2 spr-hearing", "name" => $this->lang->line("hearings_activities"), "class_a_href" => "related-stages-tab-case"];
            }
            else if($this->legal_case->get_field("category") == "Criminal"){
                $data["subNavItems"][site_url("cases/tasks/")] = ["icon" => "spr spr-tasks", "name" => $this->lang->line("case_related_tasks"), "class_a_href" => "related-tasks-tab-case"];
            } else {
                $data["subNavItems"][site_url("cases/tasks/")] = ["icon" => "spr spr-tasks", "name" => $this->lang->line("case_related_tasks"), "class_a_href" => "related-tasks-tab-case"];
                $data["subNavItems"][site_url("cases/reminders/")] = ["icon" => "spr spr-reminders", "name" => $this->lang->line("reminders"), "class_a_href" => "related-reminders-tab-case"];
            }
            $systemPreferences = $this->session->userdata("systemPreferences");
            $count_case_outsources = $this->legal_case->count_case_outsources($id);
            if ($systemPreferences["AllowFeatureAdvisor"] === "yes" && 0 < $count_case_outsources) {
                $data["subNavItems"][site_url("cases/advisor_tasks")] = ["icon" => "spr spr-tasks", "name" => $this->lang->line("case_related_advisor_tasks")];
            }
            $data["subNavItems"][site_url("cases/documents/")] = ["icon" => "spr spr-folder", "name" => $this->lang->line("related_documents"), "class_a_href" => "related-documents-tab-case"];
            if ($have_expenses_tab_access) {
                $data["subNavItems"][site_url("cases/expenses/")] = ["icon" => "spr spr-expenses", "name" => $this->lang->line("expenses"), "class_a_href" => "related-expenses-tab-case"];
            } else {
                $data["subNavItems"][site_url("cases/my_expenses/")] = ["icon" => "spr spr-expenses", "name" => $this->lang->line("expenses"), "class_a_href" => "related-expenses-tab-case"];
            }
            if ($have_time_logs_access) {
                $data["subNavItems"][site_url("cases/time_logs/")] = ["icon" => "spr spr-time-logs", "name" => $this->lang->line("time_logs"), "class_a_href" => "related-time-logs-tab-case"];
            } else {
                $data["subNavItems"][site_url("cases/my_time_logs/")] = ["icon" => "spr spr-time-logs", "name" => $this->lang->line("time_logs"), "class_a_href" => "related-time-logs-tab-case"];
            }
            $data["subNavItems"][site_url("cases/related/")] = ["icon" => "spr spr-legal", "name" => $this->lang->line($category . "case_related_cases"), "class_a_href" => "related-cases-tab-case"];
            $data["subNavItems"][site_url("cases/related_contracts/")] = ["icon" => "spr spr-contract", "name" => $this->lang->line("contracts"), "class_a_href" => "contract-access"];
            $data["subNavItems"][site_url("cases/settings/")] = ["icon" => "spr spr-settings", "name" => $this->lang->line("settings"), "class_a_href" => "related-settings-tab-case"];
            $data["actionLogs"] = $this->legal_case->load_last_action_log($id);
            $this->load->model("user_profile");
            if (isset($data["actionLogs"]["update"]["user_id"]) && $data["actionLogs"]["update"]["user_id"] != "---") {
                $this->user_profile->fetch(["user_id" => $data["actionLogs"]["update"]["user_id"]]);
                $data["actionLogs"]["update"]["status"] = $this->user_profile->get_field("status");
                $this->user_profile->reset_fields();
            }
            $this->load->model("customer_portal_users", "customer_portal_usersfactory");
            $this->customer_portal_users = $this->customer_portal_usersfactory->get_instance();
            if (!strcmp($this->legal_case->get_field("channel"), $this->legal_case->get("portalChannel"))) {
                $this->customer_portal_users->fetch($this->legal_case->get_field("createdBy"));
                $data["actionLogs"]["insert"]["email"] = $this->customer_portal_users->get_field("email");
                $data["actionLogs"]["insert"]["by"] = $this->customer_portal_users->get_field("firstName") . " " . $this->customer_portal_users->get_field("lastName") . " (Portal User)";
                $data["actionLogs"]["insert"]["status"] = $this->customer_portal_users->get_field("status");
                $data["actionLogs"]["insert"]["on"] = "---";
                if (!isset($data["actionLogs"]["insert"]["on"]) || isset($data["actionLogs"]["insert"]["on"]) && $data["actionLogs"]["insert"]["on"]) {
                    $data["actionLogs"]["insert"]["on"] = date("Y-m-d H:i", strtotime($this->legal_case->get_field("createdOn")));
                }
            } else {
                if (isset($data["actionLogs"]["insert"]["user_id"]) && $data["actionLogs"]["insert"]["user_id"] != "---") {
                    $this->user_profile->fetch(["user_id" => $data["actionLogs"]["insert"]["user_id"]]);
                    $data["actionLogs"]["insert"]["status"] = $this->user_profile->get_field("status");
                    $this->user_profile->reset_fields();
                }
            }
            if (!strcmp($this->legal_case->get_field("modifiedByChannel"), $this->legal_case->get("portalChannel"))) {
                $this->user_profile->reset_fields();
                $this->customer_portal_users->fetch($this->legal_case->get_field("modifiedBy"));
                $data["actionLogs"]["update"]["email"] = $this->customer_portal_users->get_field("email");
                $data["actionLogs"]["update"]["by"] = $this->customer_portal_users->get_field("firstName") . " " . $this->customer_portal_users->get_field("lastName") . " (Portal User)";
                $data["actionLogs"]["update"]["status"] = $this->customer_portal_users->get_field("status");
                $data["actionLogs"]["update"]["on"] = "---";
                if (!isset($data["actionLogs"]["update"]["on"]) || isset($data["actionLogs"]["update"]["on"]) && $data["actionLogs"]["update"]["on"]) {
                    $data["actionLogs"]["update"]["on"] = date("Y-m-d H:i", strtotime($this->legal_case->get_field("modifiedOn")));
                }
            }
        } else {
            $data["subNavItems"][site_url("cases/add")] = $this->lang->line("case");
            $data["subNavItems"][site_url("cases/documents") . "\" onclick=\"return false;"] = $this->lang->line("case_related_docs");
            $data["subNavItems"][site_url("cases/tasks") . "\" onclick=\"return false;"] = $this->lang->line("case_related_tasks");
            $data["subNavItems"][site_url("cases/related") . "\" onclick=\"return false;"] = $this->lang->line("case_related_cases");
            $data["subNavItems"][site_url("cases/related_contracts") . "\" onclick=\"return false;"] = $this->lang->line("case_related_related_contracts");
            $data["subNavItems"][site_url("cases/reminders") . "\" onclick=\"return false;"] = $this->lang->line("reminders");
            if ($have_expenses_tab_access) {
                $data["subNavItems"][site_url("cases/expenses") . "\" onclick=\"return false;"] = $this->lang->line("expenses");
            } else {
                $data["subNavItems"][site_url("cases/my_expenses") . "\" onclick=\"return false;"] = $this->lang->line("expenses");
            }
            $data["subNavItems"][site_url("cases/time_logs") . "\" onclick=\"return false;"] = $this->lang->line("time_logs");
        }
        $data["activeSubNavItem"] = $active;
        return $data;
    }
    private function get_case_tabs(&$id = "", $active = "")
    {
        if ($id < 1 || !$this->legal_case->fetch($id) || $this->legal_case->get_field("category") == "IP") {
            $this->set_flashmessage("error", $this->lang->line("invalid_record"));
            redirect("dashboard");
        }
        $is_case_deleted = $this->legal_case->get_field("isDeleted");
        if ($is_case_deleted == 1) {
            $this->set_flashmessage("error", $this->lang->line("invalid_record"));
            redirect("dashboard");
        }
        $isFetched = $this->legal_case->load_visible_case($id);
        if ($id && !$isFetched) {
            $this->set_flashmessage("warning", $this->lang->line("case_access_denied"));
            redirect("dashboard");
        }
        $this->pageTitle = sprintf($this->lang->line("sheria360_browser_title"), $this->lang->line("case_in_menu"));
        $id = $this->legal_case->get_field("id");
        $data = [];
        $data["subNavItems"] = [];
        $category = $this->legal_case->get_field("category") == "Litigation" ? "litigation_" : "matter_";
        $data["category"] = $category;
        $data["id"] = $id;
        if ($data["id"]) {
            $data["subNavItems"][site_url("cases/edit/")] = $this->lang->line($category . "case_public_info");
            if ($this->legal_case->get_field("category") == "Litigation") {
                $data["subNavItems"][site_url("cases/events/")] = $this->lang->line("activities");
            } else {
                $data["subNavItems"][site_url("cases/tasks/")] = $this->lang->line("case_related_tasks");
                $data["subNavItems"][site_url("cases/reminders/")] = $this->lang->line("reminders");
            }
            $data["subNavItems"][site_url("cases/documents/")] = $this->lang->line("case_related_docs");
            $data["subNavItems"][site_url("cases/expenses/")] = $this->lang->line("expenses");
            $data["subNavItems"][site_url("cases/time_logs/")] = $this->lang->line("time_logs");
            $data["subNavItems"][site_url("cases/related/")] = $this->lang->line($category . "case_related_cases");
            $systemPreferences = $this->session->userdata("systemPreferences");
            if ($systemPreferences["AllowFeatureAdvisor"] === "yes") {
                $data["subNavItems"][site_url("cases/advisor_tasks")] = $this->lang->line("case_related_advisor_tasks");
            }
            $data["subNavItems"][site_url("cases/settings/")] = $this->lang->line("settings");
            $data["actionLogs"] = $this->legal_case->load_last_action_log($id);
            $this->load->model("user_profile");
            if (isset($data["actionLogs"]["update"]["user_id"]) && $data["actionLogs"]["update"]["user_id"] != "---") {
                $this->user_profile->fetch(["user_id" => $data["actionLogs"]["update"]["user_id"]]);
                $data["actionLogs"]["update"]["status"] = $this->user_profile->get_field("status");
                $this->user_profile->reset_fields();
            }
            $this->load->model("customer_portal_users", "customer_portal_usersfactory");
            $this->customer_portal_users = $this->customer_portal_usersfactory->get_instance();
            if (!strcmp($this->legal_case->get_field("channel"), $this->legal_case->get("portalChannel"))) {
                $this->customer_portal_users->fetch($this->legal_case->get_field("createdBy"));
                $data["actionLogs"]["insert"]["email"] = $this->customer_portal_users->get_field("email");
                $data["actionLogs"]["insert"]["by"] = $this->customer_portal_users->get_field("firstName") . " " . $this->customer_portal_users->get_field("lastName") . " (Portal User)";
                $data["actionLogs"]["insert"]["status"] = $this->customer_portal_users->get_field("status");
                $data["actionLogs"]["insert"]["on"] = "---";
                if (!isset($data["actionLogs"]["insert"]["on"]) || isset($data["actionLogs"]["insert"]["on"]) && $data["actionLogs"]["insert"]["on"]) {
                    $data["actionLogs"]["insert"]["on"] = date("Y-m-d H:i", strtotime($this->legal_case->get_field("createdOn")));
                }
            } else {
                if (isset($data["actionLogs"]["insert"]["user_id"]) && $data["actionLogs"]["insert"]["user_id"] != "---") {
                    $this->user_profile->fetch(["user_id" => $data["actionLogs"]["insert"]["user_id"]]);
                    $data["actionLogs"]["insert"]["status"] = $this->user_profile->get_field("status");
                    $this->user_profile->reset_fields();
                }
            }
            if (!strcmp($this->legal_case->get_field("modifiedByChannel"), $this->legal_case->get("portalChannel"))) {
                $this->user_profile->reset_fields();
                $this->customer_portal_users->fetch($this->legal_case->get_field("modifiedBy"));
                $data["actionLogs"]["update"]["email"] = $this->customer_portal_users->get_field("email");
                $data["actionLogs"]["update"]["by"] = $this->customer_portal_users->get_field("firstName") . " " . $this->customer_portal_users->get_field("lastName") . " (Portal User)";
                $data["actionLogs"]["update"]["status"] = $this->customer_portal_users->get_field("status");
                $data["actionLogs"]["update"]["on"] = "---";
                if (!isset($data["actionLogs"]["update"]["on"]) || isset($data["actionLogs"]["update"]["on"]) && $data["actionLogs"]["update"]["on"]) {
                    $data["actionLogs"]["update"]["on"] = date("Y-m-d H:i", strtotime($this->legal_case->get_field("modifiedOn")));
                }
            }
        } else {
            $data["subNavItems"][site_url("cases/add")] = $this->lang->line("case");
            $data["subNavItems"][site_url("cases/documents") . "\" onclick=\"return false;"] = $this->lang->line("case_related_docs");
            $data["subNavItems"][site_url("cases/tasks") . "\" onclick=\"return false;"] = $this->lang->line("case_related_tasks");
            $data["subNavItems"][site_url("cases/related") . "\" onclick=\"return false;"] = $this->lang->line("case_related_cases");
            $data["subNavItems"][site_url("cases/reminders") . "\" onclick=\"return false;"] = $this->lang->line("reminders");
            $data["subNavItems"][site_url("cases/expenses") . "\" onclick=\"return false;"] = $this->lang->line("expenses");
            $data["subNavItems"][site_url("cases/time_logs") . "\" onclick=\"return false;"] = $this->lang->line("time_logs");
        }
        $data["activeSubNavItem"] = $active;
        return $data;
    }
    public function urls()
    {
        if ($this->input->post(NULL)) {
            $response = [];
            $mainClassificationId = $this->input->post("mainClassificationId");
            $archivedHardCopiesFormData = $this->input->post("archivedHardCopiesFormData");
            $archivedHardCopyIdToDelete = $this->input->post("archivedHardCopyId");
            if ($mainClassificationId) {
                $this->load->model("case_document_classification");
                $response["subList"] = $this->case_document_classification->load_sub_classification_list($mainClassificationId);
            } else {
                if ($archivedHardCopiesFormData) {
                    $this->load->model("legal_case_archived_hard_copy");
                    $archivedHardCopiesId = isset($archivedHardCopiesFormData["archivedId"]) ? $archivedHardCopiesFormData["archivedId"] : "";
                    if ($archivedHardCopiesId) {
                        $this->legal_case_archived_hard_copy->fetch($archivedHardCopiesId);
                    }
                    $archivedHardCopiesFormData["notes"] = isset($archivedHardCopiesFormData["notes"]) ? $archivedHardCopiesFormData["notes"] : "";
                    $this->legal_case_archived_hard_copy->set_fields($archivedHardCopiesFormData);
                    $response["result"] = $archivedHardCopiesId ? $this->legal_case_archived_hard_copy->update() : $this->legal_case_archived_hard_copy->insert();
                    $response["record"] = $this->legal_case_archived_hard_copy->get_fields();
                    if (!$response["result"]) {
                        $response["errors"] = $this->legal_case_archived_hard_copy->get("validationErrors");
                    } else {
                        $this->legal_case->set_field("id", $this->legal_case_archived_hard_copy->get_field("case_id"));
                        $this->legal_case->touch_logs();
                    }
                } else {
                    if ($archivedHardCopyIdToDelete) {
                        $this->load->model("legal_case_archived_hard_copy");
                        $this->legal_case_archived_hard_copy->fetch($archivedHardCopyIdToDelete);
                        $caseIdDeleted = $this->legal_case_archived_hard_copy->get_field("case_id");
                        $response["status"] = $this->legal_case_archived_hard_copy->delete(["where" => ["id", $archivedHardCopyIdToDelete]]) ? 202 : 101;
                        if ($response["status"]) {
                            $this->legal_case->set_field("id", $caseIdDeleted);
                            $this->legal_case->touch_logs();
                        }
                    } else {
                        $this->load->model("legal_case_document", "legal_case_documentfactory");
                        $this->legal_case_document = $this->legal_case_documentfactory->get_instance();
                        $filter = $this->input->post("filter");
                        $sortable = $this->input->post("sort");
                        if ($this->input->post("returnData")) {
                            $response = $this->legal_case_document->k_load_all_legal_case_documents($filter, $sortable);
                        }
                    }
                }
            }
            $this->output->set_content_type("application/json");
            $this->output->set_output(json_encode($response));
        }
    }
    public function documents($id = "")
    {
        if (!strcmp($this->is_auth->get_email_address(), "")) {
            redirect("users/login");
        }
        $this->load->model(["case_document_status", "case_document_classification", "case_document_type", "legal_case_archived_hard_copy"]);
        $this->load->model("legal_case_document", "legal_case_documentfactory");
        $this->legal_case_document = $this->legal_case_documentfactory->get_instance();
        $data = [];
        $data["tabsNLogs"] = $this->get_vertical_case_tabs($id, site_url("cases/documents/"));
        $legalCase = $this->legal_case->get_fields();
        $legalCase["Status"] = $this->legal_case->get_case_status($legalCase["case_status_id"]);
        $data["legalCase"] = $legalCase;
        $data["mainClassifications"] = $this->case_document_classification->load_list(["where" => ["case_document_classification_id IS NULL"]], ["firstLine" => ["" => $this->lang->line("choose_main_classification")]]);
        $data["subClassifications"] = ["" => $this->lang->line("choose_sub_classification")];
        $data["caseId"] = $id;
        $data["pathTypes"] = array_combine($this->legal_case_document->get("pathTypeValues"), ["-", $this->lang->line("network_drive"), $this->lang->line("web")]);
        $data["documentStatuses"] = $this->case_document_status->load_list([], ["firstLine" => ["" => " "]]);
        $data["documentTypes"] = $this->case_document_type->load_list([], ["firstLine" => ["" => " "]]);
        $data["systemPreferences"] = $this->session->userdata("systemPreferences");
        $data["archivedHardCopiesRecords"] = $this->legal_case_archived_hard_copy->get_all_archived_hard_copies_cases($id);
        $data["module"] = "case";
        $data["module_record_id"] = $id;
        $data["module_controller"] = "cases";
        $data["module_prefix"] = "legal_case";
        $data["urlGrid"] = true;
        $data["crumbParent"] = $this->legal_case->get("modelCode") . $data["tabsNLogs"]["id"];
        $data["isCustomerPortal"] = !strcmp($this->legal_case->get_field("channel"), $this->legal_case->get("portalChannel")) ? "yes" : "no";
        $data["visibleToCustomerPortal"] = $this->legal_case->get_field("visibleToCP") == "1" ? "yes" : "no";
        $this->legal_case->fetch($id);
        $data["A4L_doc_tab_name"] = $this->lang->line("a4l_documents");
        $this->load->model("document_management_system", "document_management_systemfactory");
        $this->document_management_system = $this->document_management_systemfactory->get_instance();
        $data["integration_settings"] = $this->document_management_system->get_integration_settings(["model" => strtolower($this->legal_case->get_field("category")), "root_dir" => $this->legal_case->get_field("category") == "Litigation" ? "litigation cases" : "matters", "model_id" => $id, "model_name" => $this->legal_case->get_field("subject")]);
        $data["category"] = $legalCase["category"] == "Litigation" ? "litigation_" : "matter_";
        $this->load->model("user_preference");
        $document_editor = $this->user_preference->get_value_by_user("document_editor", $this->is_auth->get_user_id());
        if (!empty($document_editor)) {
            $document_editor = unserialize($this->user_preference->get_value_by_user("document_editor", $this->is_auth->get_user_id()));
        }
        if (isset($document_editor["installation_popup_displayed"])) {
            if (!$document_editor["installation_popup_displayed"]) {
                $data["show_document_editor_installation_modal"] = true;
                $document_editor["installation_popup_displayed"] = true;
                $this->user_preference->set_value("document_editor", serialize($document_editor), $this->is_auth->get_user_id());
            }
        } else {
            $this->user_preference->set_value("document_editor", serialize(["installation_popup_displayed" => true]), $this->is_auth->get_user_id());
            $data["show_document_editor_installation_modal"] = true;
        }
        $this->load->model("integration");
        $data["integrations"] = $this->integration->find_all(["is_active", 1]);
        $this->includes("kendoui/js/kendo.web.min", "js");
        $this->includes("kendoui/styles/kendo.common-bootstrap.min", "css");
        $this->includes("kendoui/styles/kendo.bootstrap.min", "css");
        $this->includes("jquery/form2js", "js");
        $this->includes("jquery/toObject", "js");
        $this->includes("scripts/legal_case_archived_hard_copies", "js");
        $this->includes("scripts/documents_management_system", "js");
        $this->includes("scripts/documents_integration", "js");
        $this->includes("jquery/dropzone", "js");
        $this->includes("jquery/css/dropzone", "css");
        $this->includes("jquery/tabledit/jquery.tabledit.min", "js");
        $this->includes("jstree/jstree.min", "js");
        $this->includes("jstree/themes/default/style.min", "css");
        $this->includes("jquery/jquery.shiftcheckbox", "js");
        $this->includes("scripts/show_hide_customer_portal", "js");
        if ($this->is_auth->is_layout_rtl()) {
            $this->includes("styles/rtl/fixes", "css");
        }
        $this->load->view("partial/header");
        $this->load->view("documents_management_system/index", $data);
    }
    public function show_hide_document_in_cp()
    {
        $this->dms->model->fetch($this->input->post("id"));
        $this->legal_case->fetch($this->dms->model->get_field("module_record_id"));
        if ($this->legal_case->get_field("visibleToCP") || $this->legal_case->get_field("channel") == "CP") {
            $module = "case";
            $response = $this->dms->show_hide_document_in_cp($this->input->post("id"), $module);
        } else {
            $response["info"] = sprintf($this->lang->line("matter_not_shared_in_cp"), $this->lang->line($this->dms->model->get_field("type")));
        }
        $response["result"] = !isset($response["error"]) && !isset($response["info"]) ? true : false;
        $this->output->set_content_type("application/json")->set_output(json_encode($response));
    }
    public function show_hide_document_in_ap()
    {
        $module = "case";
        $response = $this->dms->show_hide_document_in_ap($this->input->post("id"), $module);
        $response["result"] = !isset($response["error"]) && !isset($response["info"]) ? true : false;
        $this->output->set_content_type("application/json")->set_output(json_encode($response));
    }
    public function show_children_documents_in_cp()
    {
        $this->dms->show_hide_children_documents($this->input->post("id"), "case", "show");
    }
    public function show_children_documents_in_ap()
    {
        $this->dms->show_hide_ap_children_documents($this->input->post("id"), "case", "show");
    }
    public function load_documents()
    {
        $response = $this->dms->load_documents(["module" => $this->input->post("module"), "module_record_id" => $this->input->post("module_record_id"), "lineage" => $this->input->post("lineage"), "term" => $this->input->post("term"),"type"=> $this->input->post("type")]);
        if ($this->input->is_ajax_request()) {
            $this->output->set_content_type("application/json");
            $this->output->set_output(json_encode($response));
        } else {
            return $response;
        }
    }
    public function load_all_documents()
    { echo $this->input->post("module");
        $response = $this->document_management_system->load_all_documents( $this->input->post("module"), $this->input->post("module_record_id"));
        if ($this->input->is_ajax_request()) {
            $this->output->set_content_type("application/json");
            $this->output->set_output(json_encode($response));
        } else {
            return $response;
        }
    }
    public function upload_file()
    {
        $response = $this->dms->upload_file(["module" => $this->input->post("module"), "module_record_id" => $this->input->post("module_record_id"), "lineage" => $this->input->post("lineage"), "upload_key" => "uploadDoc", "document_type_id" => $this->input->post("document_type_id"), "document_status_id" => $this->input->post("document_status_id"), "comment" => $this->input->post("comment")]);
        if ($this->input->post("dragAndDrop")) {
            $this->output->set_content_type("application/json");
            $this->output->set_output(json_encode($response));
        } else {
            $html = "<html>\r\n                <head>\r\n                    <meta http-equiv=\"Content-Type\" content=\"text/html; charset=utf-8\" />\r\n                    <script type=\"text/javascript\">\r\n                        if(window.top.uploadDocumentDone) window.top.uploadDocumentDone('" . $response["message"] . "', '" . ($response["status"] ? "success" : "error") . "');\r\n                    </script>\r\n                </head>\r\n            </html>";
            $this->output->set_content_type("text/html")->set_output($html);
        }
    }
    public function upload_directory()
    {
        $response = $this->dms->upload_directory(["module" => $this->input->post("module"), "module_record_id" => $this->input->post("module_record_id"), "lineage" => $this->input->post("lineage"), "upload_key" => "uploadDir", "folderext" => $this->input->post("folderext")]);
        $html = "<html>\r\n            <head>\r\n            <meta http-equiv=\"Content-Type\" content=\"text/html; charset=utf-8\" />\r\n            <script type=\"text/javascript\">";
        foreach ($response as $file_response) {
            $html .= "if(window.top.uploadDirectoryDocumentDone) window.top.uploadDirectoryDocumentDone('" . $file_response["message"] . "', '" . ($file_response["status"] ? "success" : "error") . "');";
        }
        $html .= "</script>\r\n            </head>\r\n            </html>";
        $this->output->set_content_type("text/html")->set_output($html);
    }
    public function create_folder()
    {
        $response = $this->dms->create_folder(["module" => $this->input->post("module"), "module_record_id" => $this->input->post("module_record_id"), "lineage" => $this->input->post("lineage"), "name" => $this->input->post("name")]);
        $this->output->set_content_type("application/json");
        $this->output->set_output(json_encode($response));
    }
    public function rename_file()
    {
        $response = $this->dms->rename_document("case", $this->input->post("document_id"), "file", $this->input->post("new_name"), true);
        if ($this->input->is_ajax_request()) {
            $this->output->set_content_type("application/json");
            $this->output->set_output(json_encode($response));
        } else {
            return $response;
        }
    }
    public function rename_folder()
    {
        $response = $this->dms->rename_document("case", $this->input->post("document_id"), "folder", $this->input->post("new_name"));
        if ($this->input->is_ajax_request()) {
            $this->output->set_content_type("application/json");
            $this->output->set_output(json_encode($response));
        } else {
            return $response;
        }
    }
    public function edit_documents()
    {
        $response = $this->dms->edit_documents(json_decode($this->input->post("models"), true));
        $this->output->set_content_type("application/json");
        $this->output->set_output(json_encode($response));
    }
    public function share_folder()
    {
        if (!$this->input->is_ajax_request()) {
            redirect("dashboard");
        }
        $response = [];
        if ($this->dms->model->fetch(["module" => "case", "id" => $this->input->post("folder_id")])) {
            $this->load->model("document_managment_user", "document_managment_userfactory");
            $this->document_managment_user = $this->document_managment_userfactory->get_instance();
            if ($this->input->post("modeType") == "getHtml") {
                $data["isPrivate"] = $this->input->post("private");
                $share_users = $this->document_managment_user->load_watchers_users($this->input->post("folder_id"));
                $data["sharedWithUsers"] = isset($share_users[0]) ? $share_users[0] : [];
                $data["sharedWithUsersStatus"] = isset($share_users[1]) ? $share_users[1] : [];
                $response["html"] = $this->load->view("documents_management_system/shared_with_form", $data, true);
            } else {
                $response = $this->dms->share_folder("case", $this->input->post("folder_id"), $this->input->post("private"), $this->input->post("watchers_users"));
            }
        }
        $this->output->set_content_type("application/json");
        $this->output->set_output(json_encode($response));
    }
    public function download_file($file_id, $newest_version = false)
    {
        $newest_version = $newest_version == "true" ? true : false;
        $response = $this->dms->download_file("case", $file_id, $newest_version);
        if (!$response["status"]) {
            $this->set_flashmessage("error", $response["message"]);
            redirect($this->agent->referrer());
        }
    }
    private function open_document($file_id, $extension)
    {
        $data = $this->dms->open_document($file_id, $extension, "case");
        $this->load->view("partial/header");
        if ($data["is_office_file"]) {
            $this->load->view("documents_management_system/office_file_template", $data);
        } else {
            if ($data["is_openable_file"]) {
                $this->load->view("documents_management_system/openable_file_template", $data);
            }
        }
        $this->load->view("partial/footer");
    }
    public function list_file_versions()
    {
        $list_file_verions_response = $this->dms->list_file_versions("case", $this->input->post("file_id"), true);
        if (!empty($list_file_verions_response["data"]["file_versions"])) {
            $response["html"] = $this->load->view("documents_management_system/file_document_versions", $list_file_verions_response["data"], true);
        }
        $response["status"] = $list_file_verions_response["status"];
        $this->output->set_content_type("application/json");
        $this->output->set_output(json_encode($response));
    }
    public function delete_document()
    {
        $response = $this->dms->delete_document("case", $this->input->post("document_id"), $this->input->post("newest_version") == "true");
        $this->output->set_content_type("application/json")->set_output(json_encode($response));
    }
    public function delete_document_comment()
    {
        $this->load->model("case_comment_attachment");
        $this->case_comment_attachment->delete_attachment($this->input->post("id"));
        $response = $this->dms->delete_document("case", $this->input->post("document_id"), $this->input->post("newest_version") == "true");
        $this->output->set_content_type("application/json")->set_output(json_encode($response));
    }
    public function delete_document_hearing()
    {
        $document_id = $this->input->post("document_id");
        $response = $this->dms->delete_document("case", $document_id, true);
        $this->output->set_content_type("application/json")->set_output(json_encode($response));
    }
    public function document_delete()
    {
        if (!$this->input->is_ajax_request()) {
            redirect("dashboard");
        }
        $docId = $this->input->post("docId");
        if (!empty($docId)) {
            $this->load->model("legal_case_document", "legal_case_documentfactory");
            $this->legal_case_document = $this->legal_case_documentfactory->get_instance();
            $this->legal_case_document->fetch($docId);
            $result = $this->db->where("id", $docId)->delete($this->legal_case_document->get("_table"));
            $response["status"] = $result ? 202 : 101;
            if ($response["status"] == 202) {
                $this->legal_case_document->touch_logs("delete");
                $this->legal_case->set_field("id", $this->legal_case_document->get_field("legal_case_id"));
                $this->legal_case->touch_logs();
            }
        }
        $this->output->set_content_type("application/json");
        $this->output->set_output(json_encode($response));
    }
    public function document_add()
    {
        if (!$this->input->is_ajax_request()) {
            redirect("dashboard");
        }
        if ($this->input->post(NULL)) {
            $this->load->model("legal_case_document", "legal_case_documentfactory");
            $this->legal_case_document = $this->legal_case_documentfactory->get_instance();
            $legal_case_id = $this->input->post("legal_case_id");
            $this->legal_case_document->set_fields($this->input->post(NULL));
            $this->legal_case_document->set_field("modifiedOn", date("Y-m-d H:i:s"));
            $this->legal_case_document->set_field("modifiedBy", $this->is_auth->get_user_id());
            $response["result"] = $this->legal_case_document->insert();
            if ($response["result"]) {
                $this->legal_case->set_field("id", $legal_case_id);
                $this->legal_case->touch_logs();
            }
        }
        $response["validationErrors"] = $this->legal_case_document->get("validationErrors");
        $this->output->set_content_type("application/json");
        $this->output->set_output(json_encode($response));
    }
    public function document_edit()
    {
        $response = [];
        $response["result"] = false;
        if ($this->input->post(NULL)) {
            $this->load->model("legal_case_document", "legal_case_documentfactory");
            $this->legal_case_document = $this->legal_case_documentfactory->get_instance();
            $documentsData = json_decode($this->input->post("models"), true);
            foreach ($documentsData as $documentData) {
                $this->legal_case_document->fetch($documentData["id"]);
                if ($documentData["pathType"] == "web" && substr($documentData["path"], 0, 7) !== "http://" && substr($documentData["path"], 0, 8) !== "https://") {
                    $documentData["path"] = "http://" . $documentData["path"];
                }
                $this->legal_case_document->set_fields($documentData);
                $this->legal_case_document->set_field("legal_case_document_type_id", $documentData["document_type_id"]);
                $this->legal_case_document->set_field("legal_case_document_status_id", $documentData["document_status_id"]);
                $response["result"] = $this->legal_case_document->update();
            }
            if ($response["result"]) {
                $this->legal_case->set_field("id", $this->legal_case_document->get_field("legal_case_id"));
                $this->legal_case->touch_logs();
            }
        }
        $response["validationErrors"] = $this->legal_case_document->get("validationErrors");
        $this->output->set_content_type("application/json")->set_output(json_encode($response));
    }
    public function related_delete()
    {
        $this->load->model("related_case", "related_casefactory");
        $this->related_case = $this->related_casefactory->get_instance();
        if ($this->input->post(NULL)) {
            $response = [];
            $recordId = $this->input->post("recordId");
            if ($recordId) {
                $this->related_case->fetch($recordId);
                $case_a_id = $this->related_case->get_field("case_a_id");
                $case_b_id = $this->related_case->get_field("case_b_id");
                $result = false;
                $this->db->where("id", $recordId);
                if ($this->db->delete("related_cases")) {
                    $this->related_case->fetch(["case_a_id" => $case_b_id, "case_b_id" => $case_a_id]);
                    $id = $this->related_case->get_field("id");
                    $caseIdDeleted = $this->related_case->get_field("case_b_id");
                    $this->db->where("id", $id);
                    if ($this->db->delete("related_cases")) {
                        $result = true;
                        $this->related_case->touch_logs("delete");
                        $this->legal_case->set_field("id", $caseIdDeleted);
                        $this->legal_case->touch_logs();
                    }
                }
                $response["status"] = $result ? 202 : 101;
            }
            $this->output->set_content_type("application/json");
            $this->output->set_output(json_encode($response));
        }
    }
    public function related($id = "")
    {
        $this->load->model("related_case", "related_casefactory");
        $this->related_case = $this->related_casefactory->get_instance();
        if ($this->input->post(NULL)) {
            $filter = $this->input->post("filter");
            $sortable = $this->input->post("sort");
            if ($this->input->post("returnData")) {
                $response = $this->related_case->k_load_all_related_cases($filter, $sortable);
            }
            $this->output->set_content_type("application/json");
            $this->output->set_output(json_encode($response));
        } else {
            $data = [];
            $data["tabsNLogs"] = $this->get_vertical_case_tabs($id, site_url("cases/related/"));
            $legalCase = $this->legal_case->get_fields();
            $legalCase["Status"] = $this->legal_case->get_case_status($legalCase["case_status_id"]);
            $data["legalCase"] = $legalCase;
            $systemPreferences = $this->session->userdata("systemPreferences");
            $data["systemPreferences"] = $systemPreferences;
            $data["caseId"] = $id;
            $data["category"] = $legalCase["category"] == "Litigation" ? "litigation_" : "matter_";
            $this->includes("kendoui/js/kendo.web.min", "js");
            $this->includes("kendoui/styles/kendo.common-bootstrap.min", "css");
            $this->includes("kendoui/styles/kendo.bootstrap.min", "css");
            $this->includes("jquery/form2js", "js");
            $this->includes("jquery/toObject", "js");
            $this->includes("scripts/related_cases", "js");
            $this->includes("scripts/show_hide_customer_portal", "js");
            if ($this->is_auth->is_layout_rtl()) {
                $this->includes("styles/rtl/fixes", "css");
            }
            $this->load->view("partial/header");
            $this->load->view("cases/related", $data);
        }
    }
    public function related_contract_delete()
    {
        $response = [];
        if ($this->input->is_ajax_request()) {
            $legal_case_id = $this->input->post("caseId");
            $contract_id = $this->input->post("contract_id");
            $response["result"] = false;
            if ($legal_case_id && $contract_id) {
                $this->load->model("case_related_contract", "case_related_contractfactory");
                $this->case_related_contract = $this->case_related_contractfactory->get_instance();
                $response["result"] = $this->case_related_contract->delete_related_contract($legal_case_id, $contract_id);
            }
            $this->output->set_content_type("application/json")->set_output(json_encode($response));
        }
    }
    public function related_contracts($legal_case_id = "")
    {
        $this->authenticate_actions_per_license("contract");
        if ($this->input->post(NULL)) {
            $filter = $this->input->post("filter");
            $sortable = $this->input->post("sort");
            $this->load->model("case_related_contract", "case_related_contractfactory");
            $this->case_related_contract = $this->case_related_contractfactory->get_instance();
            $response = $this->case_related_contract->k_load_all_case_related_contracts($filter, $sortable, $legal_case_id);
            $this->output->set_content_type("application/json")->set_output(json_encode($response));
        } else {
            $data = [];
            $data["tabsNLogs"] = $this->get_vertical_case_tabs($legal_case_id, site_url("cases/related_contracts/"));
            $legalCase = $this->legal_case->get_fields();
            $legalCase["Status"] = $this->legal_case->get_case_status($legalCase["case_status_id"]);
            $data["legalCase"] = $legalCase;
            $systemPreferences = $this->session->userdata("systemPreferences");
            $data["systemPreferences"] = $systemPreferences;
            $data["caseId"] = $legal_case_id;
            $data["category"] = $legalCase["category"] == "Litigation" ? "litigation_" : "matter_";
            $this->includes("kendoui/js/kendo.web.min", "js");
            $this->includes("kendoui/styles/kendo.common-bootstrap.min", "css");
            $this->includes("kendoui/styles/kendo.bootstrap.min", "css");
            $this->includes("jquery/form2js", "js");
            $this->includes("jquery/toObject", "js");
            $this->includes("scripts/case_related_contracts", "js");
            $this->includes("money/js/accounting", "js");
            if ($this->is_auth->is_layout_rtl()) {
                $this->includes("styles/rtl/fixes", "css");
            }
            $this->load->view("partial/header");
            $this->load->view("cases/related_contracts", $data);
        }
    }
    public function related_contract_add()
    {
        if ($this->input->post(NULL)) {
            $response = [];
            $legal_case_id = $this->input->post("legal_case_id");
            $related_contract_id = $this->input->post("related_contract_id");
            $this->load->model("case_related_contract", "case_related_contractfactory");
            $this->case_related_contract = $this->case_related_contractfactory->get_instance();
            $response["result"] = $this->case_related_contract->check_related_contract_existence($legal_case_id, $related_contract_id);
            if ($response["result"]) {
                $this->load->model("case_related_contract", "case_related_contractfactory");
                $data = ["legal_case_id" => $legal_case_id, "contract_id" => $related_contract_id];
                $this->case_related_contract->set_fields($data);
                if ($this->case_related_contract->insert()) {
                    $response["result"] = true;
                    $response["display_message"] = $this->lang->line("updates_saved_successfully");
                } else {
                    $response["display_message"] = $this->lang->line("updates_failed");
                }
            } else {
                $response["display_message"] = sprintf($this->lang->line("legal_case_contract_relation_exists"), $legal_case_id, $related_contract_id);
            }
            $this->output->set_content_type("application/json")->set_output(json_encode($response));
        }
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
        $results = $this->legal_case->lookup($term);
        $this->output->set_content_type("application/json");
        $this->output->set_output(json_encode($results));
    }
    public function related_add()
    {
        if ($this->input->post(NULL)) {
            $this->load->model("related_case", "related_casefactory");
            $this->related_case = $this->related_casefactory->get_instance();
            $response = [];
            $caseId = $this->input->post("caseId");
            $newCaseId = $this->input->post("newCaseId");
            $response["status"] = "";
            $response["status"] = $this->related_case->check_cases_excluded($caseId, $newCaseId);
            if (!$response["status"]) {
                $this->related_case->set_field("case_a_id", $caseId);
                $this->related_case->set_field("case_b_id", $newCaseId);
                $response["status"] = $this->related_case->insert() ? 202 : 101;
                if ($response["status"] == 202) {
                    $this->legal_case->set_field("id", $caseId);
                    $this->related_case->reset_fields();
                    $this->related_case->set_field("case_a_id", $newCaseId);
                    $this->related_case->set_field("case_b_id", $caseId);
                    $response["status"] = $this->related_case->insert() ? 202 : 101;
                    $this->legal_case->touch_logs();
                }
            }
            $this->output->set_content_type("application/json");
            $this->output->set_output(json_encode($response));
        }
    }
    public function delete_comment($id)
    {
        if (0 < $id && $this->input->is_ajax_request()) {
            $this->load->model("case_comment", "case_commentfactory");
            $this->case_comment = $this->case_commentfactory->get_instance();
            $response["status"] = 101;
            if ($this->case_comment->fetch($id)) {
                $response["status"] = 102;
                $this->load->model("case_comment_attachment");
                $data["caseCommentAttachment"] = $this->case_comment_attachment->get_attachments_for_comment($id);
                if (!empty($data["caseCommentAttachment"])) {
                    $response["status"] = 101;
                    if ($this->case_comment_attachment->delete_all_attachments($id)) {
                        $response["status"] = 102;
                    }
                }
                if ($response["status"] == 102) {
                    if (!$this->case_comment->delete($id)) {
                        $response["status"] = 101;
                    } else {
                        $response["status"] = 500;
                    }
                }
            } else {
                $response["status"] = 101;
            }
            $this->output->set_content_type("application/json");
            $this->output->set_output(json_encode($response));
        } else {
            show_404();
        }
    }
    public function add_comment()
    {
        $data = $response = [];
        if ($this->input->post(NULL)) {
            $this->load->model("case_comment", "case_commentfactory");
            $this->case_comment = $this->case_commentfactory->get_instance();
            $this->load->model("case_comment_attachment");
            $caseId = $this->input->post("case_id");
            $comment = $this->case_comment->regenerate_comment($this->input->post("comment", false, true));
            if ($comment == "") {
                $data["caseCommentId"] = 0;
                $data["type"][] = "error";
                $response["status"] = false;
                $response["validationErrors"]["comment"] = $this->lang->line("empty_case_comment");
                $this->load->view("partial/upload_result", $data);
            } else {
                $createdOn = date("Y-m-d H:i:s", strtotime($this->input->post("createdOn") . " " . date("H:i:s", time())));
                $user_id = $this->input->post("user_id");
                $attachments = $_FILES;
                $paths = $this->input->post("paths");
                $prefixDate = date("Ymd");
                $this->case_comment->set_field("case_id", $caseId);
                $this->case_comment->set_field("comment", $comment);
                $this->case_comment->set_field("createdOn", $createdOn);
                $this->case_comment->set_field("user_id", $user_id);
                $this->case_comment->set_field("modifiedBy", $this->is_auth->get_user_id());
                $this->case_comment->set_field("createdByChannel", $this->legal_case->get("webChannel"));
                $this->case_comment->set_field("modifiedByChannel", $this->legal_case->get("webChannel"));
                $this->case_comment->set_field("isVisibleToCP", $this->input->post("isVisible") == "yes" ? "1" : "0");
                $this->case_comment->set_field("isVisibleToAP", $this->input->post("isVisibleToAP") == "yes" ? "1" : "0");
                $this->load->model("client");
                $client_info = $this->client->fetch_client($this->legal_case->get_field("client_id"));
                if ($this->case_comment->insert()) {
                    $this->legal_case->set_field("id", $caseId);
                    $this->legal_case->touch_logs();
                    $this->legal_case->fetch($caseId);
                    $this->load->library("system_notification");
                    $this->load->library("email_notifications");

                    $this->load->model("user", "userfactory");
                    $this->user = $this->userfactory->get_instance();
                    $this->user->fetch($user_id);
                    $noteCreatedByEmail = $this->user->get_field("email");
                    $note_created_by = $this->email_notification_scheme->get_user_full_name($user_id);
                    $caseCreatedBy = $this->legal_case->get_field("createdBy");
                    $caseAssignee = $this->legal_case->get_field("user_id");
                    $case_comment_id = $this->case_comment->get_field("id");
                    $model = $this->legal_case->get("_table");
                    $notifications_data = [];
                    if (!strcmp($this->legal_case->get_field("channel"), $this->legal_case->get("portalChannel"))) {
                        if ($this->input->post("isVisible") == "yes" && $this->email_notification_scheme->get_hide_show_send_email_notification_by_trigger_action("legal_add_comment")) {
                            $this->load->model("customer_portal_users", "customer_portal_usersfactory");
                            $this->customer_portal_users = $this->customer_portal_usersfactory->get_instance();
                            $this->customer_portal_users->fetch($caseCreatedBy);
                            $this->load->model("user_profile");
                            $this->user_profile->fetch(["user_id" => $user_id]);
                            $caseCreatedByEmail = $this->customer_portal_users->get_field("email");
                            $case_created_by_profile_name = $this->customer_portal_users->get_field("firstName") . " " . $this->customer_portal_users->get_field("lastName");
                            $noteCreatedProfileName = $this->user_profile->get_field("firstName") . " " . $this->user_profile->get_field("lastName");
                            $object = "legal_add_comment";
                            $sendEmail = $this->input->post("send_notifications_email");
                            if ($sendEmail) {
                                $notifications_emails = $this->email_notification_scheme->get_emails($object, $model, ["id" => $caseId]);
                                extract($notifications_emails);
                                $notifications_data = ["to" => $to_emails, "object" => $object, "object_id" => $caseId, "objectName" => strtolower($this->legal_case->get_field("category")), "cc" => $cc_emails, "caseSubject" => $this->legal_case->get_field("subject"), "caseNote" => $comment, "created_by" => $note_created_by, "requested_by" => $case_created_by_profile_name, "objectModelCode" => $this->legal_case->get("modelCode"), "assignee" => $this->email_notification_scheme->get_user_full_name($caseAssignee), "file_reference" => $this->legal_case->get_field("internalReference"), "priority" => $this->legal_case->get_field("priority"), "client_name" => $client_info["name"], "fromLoggedUser" => $this->is_auth->get_fullname()];
                            }
                            $this->legal_case->notifyTicketUserByEmail(false, false, $caseCreatedByEmail, $noteCreatedProfileName . " added the below comment: <br /> " . $comment, $sendEmail, $notifications_data);
                        }
                    } else {
                        if ($this->legal_case->case_outsourced($caseId)) {
                            $object = "core_user_add_comment";
                            $notifications_emails = $this->email_notification_scheme->get_emails($object, $model, ["id" => $caseId, "case_comment_id" => $case_comment_id]);
                            $to_emails = $notifications_emails["to_emails"] ?? [];
                            $cc_emails = $notifications_emails["cc_emails"] ?? [];
                            $created_by_user_id = str_pad($this->legal_case->get_field("createdBy"), 10, "0", STR_PAD_LEFT);
                            $email_notifications_data = ["to" => $to_emails, "object" => $object, "object_id" => $caseId, "objectName" => strtolower($this->legal_case->get_field("category")), "cc" => $cc_emails, "ccIds" => [$user_id, $created_by_user_id], "targetUser" => $user_id, "secondTargetUser" => $created_by_user_id, "caseSubject" => $this->legal_case->get_field("subject"), "caseNote" => $comment, "objectModelCode" => $this->legal_case->get("modelCode"), "assignee" => $this->email_notification_scheme->get_user_full_name($caseAssignee), "file_reference" => $this->legal_case->get_field("internalReference"), "client_name" => $client_info["name"]??'', "created_by" => $note_created_by, "fromLoggedUser" => $this->is_auth->get_fullname()];
                            $this->email_notifications->notify($email_notifications_data);
                        } else {
                            $this->user->fetch($caseCreatedBy);
                            $caseCreatedByEmail = $this->user->get_field("email");
                            $object = "add_note_case";
                            $sendEmailFlag = $this->input->post("send_notifications_email");
                            $created_by_user_id = str_pad($this->legal_case->get_field("createdBy"), 10, "0", STR_PAD_LEFT);
                            $system_notifications_data = ["to" => strcmp($caseAssignee, "") ? $caseAssignee : $created_by_user_id, "object" => $object, "object_id" => $caseId, "objectName" => strtolower($this->legal_case->get_field("category")), "cc" => [$noteCreatedByEmail, $caseCreatedByEmail], "ccIds" => [$user_id, $created_by_user_id], "targetUser" => $user_id, "secondTargetUser" => $created_by_user_id, "caseSubject" => $this->legal_case->get_field("subject"), "file_reference" => $this->legal_case->get_field("internalReference"), "caseNote" => $comment, "assignee" => $this->email_notification_scheme->get_user_full_name($caseAssignee), "client_name" => $client_info["name"], "objectModelCode" => $this->legal_case->get("modelCode")];
                            $this->system_notification->notification_add($system_notifications_data);
                            if ($sendEmailFlag) {
                                $notifications_emails = $this->email_notification_scheme->get_emails($object, $model, ["id" => $caseId, "case_comment_id" => $case_comment_id]);
                                extract($notifications_emails);
                                $email_notifications_data = ["to" => $to_emails, "object" => $object, "object_id" => $caseId, "objectName" => strtolower($this->legal_case->get_field("category")), "cc" => $cc_emails, "ccIds" => [$user_id, $created_by_user_id], "targetUser" => $user_id, "secondTargetUser" => $created_by_user_id, "caseSubject" => $this->legal_case->get_field("subject"), "caseNote" => $comment, "objectModelCode" => $this->legal_case->get("modelCode"), "assignee" => $this->email_notification_scheme->get_user_full_name($caseAssignee), "created_by" => $note_created_by, "client_name" => $client_info["name"], "file_reference" => $this->legal_case->get_field("internalReference"), "fromLoggedUser" => $this->is_auth->get_fullname()];
                                $this->email_notifications->notify($email_notifications_data);
                            }
                        }
                    }
                    $failed_uploads_count = 0;
                    $are_files_uploaded = $this->dms->check_if_files_were_uploaded($_FILES);
                    if ($are_files_uploaded) {
                        $note_parent_folder = $this->dms->create_note_parent_folder($caseId, $createdOn, "case");
                    }
                    foreach ($_FILES as $file_key => $file) {
                        if ($file["error"] != 4) {
                            $upload_response = $this->dms->upload_file(["module" => "case", "module_record_id" => $caseId, "container_name" => $createdOn, "lineage" => $note_parent_folder["lineage"] ?? "", "upload_key" => $file_key, "visible_in_cp" => $this->input->post("isVisible") == "yes" ? 1 : 0]);
                            if (!$upload_response["status"]) {
                                $failed_uploads_count++;
                            } else {
                                $this->case_comment_attachment->set_field("case_comment_id", $case_comment_id);
                                $this->case_comment_attachment->set_field("path", $upload_response["file"]["id"]);
                                $this->case_comment_attachment->set_field("uploaded", "Yes");
                                if (!$this->case_comment_attachment->insert()) {
                                    $response["validationErrors"]["record"] = $this->lang->line("failed_to_save_case_comment");
                                    $response["status"] = false;
                                } else {
                                    $this->case_comment_attachment->reset_fields();
                                }
                            }
                        }
                    }
                    if (!empty($paths)) {
                        foreach ($paths as $path) {
                            if (!empty($path)) {
                                $this->case_comment_attachment->set_field("case_comment_id", $case_comment_id);
                                $this->case_comment_attachment->set_field("path", $path);
                                $this->case_comment_attachment->set_field("uploaded", "No");
                                if (!$this->case_comment_attachment->insert()) {
                                    $response["validationErrors"]["record"] = $this->lang->line("failed_to_save_case_comment");
                                    $response["status"] = false;
                                } else {
                                    $this->case_comment_attachment->reset_fields();
                                }
                            }
                        }
                    }
                    if (0 < $failed_uploads_count) {
                        $response["message"] = sprintf($this->lang->line("files_were_not_uploaded"), $failed_uploads_count);
                        $response["status"] = true;
                        $response["warning"] = true;
                    }
                    $data["caseCommentId"] = $case_comment_id;
                    $response["message"] = $this->lang->line("record_saved");
                    $response["data"] = $data;
                    $response["status"] = true;
                } else {
                    $response["validationErrors"] = $this->case_comment->get("validationErrors");
                    $response["validationErrors"]["record"] = $this->lang->line("failed_to_save_this_record");
                    $response["status"] = false;
                }
                $this->load->view("partial/upload_result", $data);
            }
            $this->output->set_content_type("application/json")->set_output(json_encode($response));
        } else {
            $data["status"][] = $this->lang->line("data_missing_or_incomplete");
            $data["type"][] = "error";
            $this->load->view("partial/upload_result", $data);
        }
    }
    public function edit_comment($id = 0, $case_id = 0)
    {
        $data = $response = [];
        $this->load->model("case_comment", "case_commentfactory");
        $this->case_comment = $this->case_commentfactory->get_instance();
        $this->load->model("case_comment_attachment");
        if (0 < $id) {
            $legal_case_id = (int) $this->input->post("legal_case_id");
            $legal_case = $this->legal_case->load_case($case_id);
            if (!$legal_case) {
                $response["status"] = false;
                $response["validationErrors"] = ["error_record" => $this->lang->line("invalid_record")];
            } else {
                $data["caseChannel"] = $legal_case["channel"] ?? NULL;
                $data["visibleToCP"] = $legal_case["visibleToCP"] ?? NULL;
                $data["isOutsourcedToAdvisors"] = $this->legal_case->case_outsourced($case_id);
                $response["status"] = true;
                $data["legalCase"] = $legal_case;
                $data["id"] = $legal_case_id;
                $data["title"] = $this->lang->line("edit_note");
                $data["caseComment"] = $this->case_comment->load_case_comment_data($id);
                $data["caseCommentAttachment"] = $this->case_comment_attachment->load_all(["where" => ["case_comment_id", $id]]);
                $response["html"] = $this->load->view("cases/edit_comment", $data, true);
            }
            $this->output->set_content_type("application/json")->set_output(json_encode($response));
        } else {
            if ($this->input->post(NULL)) {
                $prefixDate = date("Ymd");
                $createdOn = date("Y-m-d H:i:s", strtotime($this->input->post("createdOnEdit") . " " . date("H:i:s", time())));
                $user_id = $this->input->post("user_idEdit");
                $createdByChannel = $this->input->post("createdByChannel");
                $caseCommentId = $this->input->post("caseCommentIdEdit");
                $comment = $this->case_comment->regenerate_comment($this->input->post("commentEdit", false, true));
                if ($comment == "") {
                    $data["caseCommentId"] = $id;
                    $response["validationErrors"]["comment"] = $this->lang->line("empty_case_comment");
                    $data["type"][] = "error";
                    $data["status"] = false;
                    $this->load->view("partial/upload_result", $data);
                } else {
                    $oldPaths = $this->input->post("oldPaths");
                    $paths = $this->input->post("paths");
                    if ($this->case_comment->fetch($caseCommentId)) {
                        $this->case_comment->set_field("comment", $comment);
                        $this->case_comment->set_field("createdOn", $createdOn);
                        $this->case_comment->set_field("user_id", $user_id);
                        $this->case_comment->set_field("modifiedBy", $this->is_auth->get_user_id());
                        $this->case_comment->set_field("createdByChannel", $createdByChannel);
                        $this->case_comment->set_field("modifiedByChannel", $this->legal_case->get("webChannel"));
                        $this->case_comment->set_field("isVisibleToCP", $this->input->post("isVisible") ? $this->input->post("isVisible") == "yes" ? "1" : "0" : $this->case_comment->get_field("isVisibleToCP"));
                        $this->case_comment->set_field("isVisibleToAP", $this->input->post("isVisibleToAP") ? $this->input->post("isVisibleToAP") == "yes" ? "1" : "0" : $this->case_comment->get_field("isVisibleToAP"));
                        if ($this->case_comment->update()) {
                            $this->legal_case->set_field("id", $this->case_comment->get_field("case_id"));
                            $this->legal_case->touch_logs();
                            $failed_uploads_count = 0;
                            $case_comment_id = $this->input->post("caseCommentIdEdit");
                            $attachments = $this->case_comment_attachment->get_attachments_name($case_comment_id);
                            if (0 < count($attachments)) {
                                $parent_id = $this->dms->get_document_details(["id" => end($attachments)["path"]])["parent"];
                                $this->dms->rename_document("case", $parent_id, "folder", $createdOn);
                            }
                            $are_files_uploaded = $this->dms->check_if_files_were_uploaded($_FILES);
                            if ($are_files_uploaded) {
                                $note_parent_folder = $this->dms->create_note_parent_folder($this->case_comment->get_field("case_id"), $createdOn, "case");
                            }
                            foreach ($_FILES as $file_key => $file) {
                                if ($file["error"] != 4) {
                                    $upload_response = $this->dms->upload_file(["module" => "case", "module_record_id" => $this->case_comment->get_field("case_id"), "container_name" => $createdOn, "lineage" => $note_parent_folder["lineage"] ?? "", "upload_key" => $file_key, "visible_in_cp" => $this->input->post("isVisible") ? $this->input->post("isVisible") == "yes" ? 1 : 0 : $this->case_comment->get_field("isVisibleToCP")]);
                                    if (!$upload_response["status"]) {
                                        $failed_uploads_count++;
                                    } else {
                                        $this->case_comment_attachment->set_field("case_comment_id", $this->case_comment->get_field("id"));
                                        $this->case_comment_attachment->set_field("path", $upload_response["file"]["id"]);
                                        $this->case_comment_attachment->set_field("uploaded", "Yes");
                                        if (!$this->case_comment_attachment->insert()) {
                                            $data["message"] = $this->lang->line("failed_to_save_case_comment");
                                            $data["type"][] = "error";
                                            $data["status"] = false;
                                        } else {
                                            $this->case_comment_attachment->reset_fields();
                                        }
                                    }
                                }
                            }
                            $oldUploadedPaths = $this->case_comment_attachment->get_paths($caseCommentId);
                            if (!empty($oldUploadedPaths)) {
                                if (empty($oldPaths)) {
                                    foreach ($oldUploadedPaths as $oldUploadedPath) {
                                        $this->case_comment_attachment->delete($oldUploadedPath["id"]);
                                    }
                                } else {
                                    foreach ($oldUploadedPaths as $oldUploadedPath) {
                                        $array2[] = $oldUploadedPath["path"];
                                    }
                                    $mergedArray = array_diff($array2, $oldPaths);
                                    if (!empty($mergedArray)) {
                                        foreach ($mergedArray as $filePath) {
                                            foreach ($oldUploadedPaths as $oldUploadedPath) {
                                                if ($filePath == $oldUploadedPath["path"]) {
                                                    $this->case_comment_attachment->delete($oldUploadedPath["id"]);
                                                }
                                            }
                                        }
                                    }
                                }
                            }
                            if (!empty($paths)) {
                                foreach ($paths as $path) {
                                    if (!empty($path)) {
                                        $this->case_comment_attachment->set_field("case_comment_id", $caseCommentId);
                                        $this->case_comment_attachment->set_field("path", $path);
                                        $this->case_comment_attachment->set_field("uploaded", "No");
                                        if (!$this->case_comment_attachment->insert()) {
                                            $data["status"][] = $this->lang->line("failed_to_save_case_comment");
                                            $data["type"][] = "error";
                                        } else {
                                            $this->case_comment_attachment->reset_fields();
                                        }
                                    }
                                }
                            }
                            if (0 < $failed_uploads_count) {
                                $data["type"][] = "warning";
                                $data["status"] = true;
                                $data["warning"] = sprintf($this->lang->line("files_were_not_uploaded"), $failed_uploads_count);
                            }
                            $data["caseCommentId"] = $caseCommentId;
                            $data["status"] = true;
                            $data["message"] = $this->lang->line("record_saved");
                            $data["type"][] = "success";
                        } else {
                            $data["message"] = $this->lang->line("failed_to_save_this_record");
                            $data["type"][] = "error";
                            $data["status"] = false;
                        }
                    } else {
                        $data["message"] = $this->lang->line("data_missing_or_incomplete");
                        $data["type"][] = "error";
                        $data["status"] = false;
                    }
                    $this->load->view("partial/upload_result", $data);
                }
                $response = $data;
                $this->output->set_content_type("application/json")->set_output(json_encode($response));
            } else {
                $data["message"] = $this->lang->line("data_missing_or_incomplete");
                $data["type"][] = "error";
                $data["status"] = true;
                $this->load->view("partial/upload_result", $data);
            }
        }
    }
    public function get_last_comment()
    {
        $id = $this->input->post("id");
        $caseId = $this->input->post("caseId");
        $this->load->model("case_comment", "case_commentfactory");
        $this->case_comment = $this->case_commentfactory->get_instance();
        $this->load->helper("text");
        $data = [];
        $data = $this->case_comment->fetch_case_comment_data($id);
        if (!empty($data)) {
            $response["nbOfNotesHistory"] = $this->case_comment->count_all_case_comments($caseId);
            $response["html"] = $this->load->view("cases/case_comment", $data, true);
            $response["status"] = true;
        } else {
            $response["status"] = false;
        }
        $this->output->set_content_type("application/json");
        $this->output->set_output(json_encode($response));
    }
    public function get_all_comments()
    {
        $id = $this->input->post("id");
        $this->load->model("case_comment", "case_commentfactory");
        $this->case_comment = $this->case_commentfactory->get_instance();
        $this->load->helper("text");
        $data["case_id"] = $id;
        $data["case_comments"] = $this->case_comment->fetch_all_case_comment($id);
        if (!empty($data)) {
            $response["nb_of_notes_history"] = $this->case_comment->count_all_case_comments($id);
            $data["case_comments_pagination"] = $this->get_comments_pagination_data();
            $response["html"] = $this->load->view("cases/comments", $data, true);
            $response["status"] = true;
        } else {
            $response["status"] = false;
        }
        $this->output->set_content_type("application/json");
        $this->output->set_output(json_encode($response));
    }
    public function get_all_core_and_cp_comments()
    {
        $id = $this->input->post("id");
        $this->load->model("case_comment", "case_commentfactory");
        $this->case_comment = $this->case_commentfactory->get_instance();
        $this->load->helper("text");
        $data = [];
        $data["case_comments"] = $this->case_comment->fetch_all_case_core_and_cp_comments($id);
        if (!empty($data)) {
            $response["nb_of_notes_history"] = $this->case_comment->count_all_case_core_and_cp_comments($id);
            $data["case_comments_pagination"] = $this->get_comments_pagination_data();
            $response["html"] = $this->load->view("cases/comments", $data, true);
            $response["status"] = true;
        } else {
            $response["status"] = false;
        }
        $this->output->set_content_type("application/json");
        $this->output->set_output(json_encode($response));
    }
    public function get_all_email_comments()
    {
        $this->load->library("licensor");
        if ($this->licensor->check_license_date("outlook")) {
            $id = $this->input->post("id");
            $this->load->model("case_comment", "case_commentfactory");
            $this->case_comment = $this->case_commentfactory->get_instance();
            $this->load->helper("text");
            $data["case_id"] = $id;
            $data["case_comments_emails"] = $this->case_comment->fetch_all_case_comments_emails($id);
            if (!empty($data)) {
                $response["nb_of_notes_history"] = $this->case_comment->count_all_case_comments_emails($id);
                $data["case_comments_pagination"] = $this->get_comments_pagination_data();
                $response["html"] = $this->load->view("cases/email_comments", $data, true);
                $response["status"] = true;
            } else {
                $response["status"] = false;
            }
        } else {
            $response["status"] = false;
            $response["module_expired"] = true;
        }
        $this->output->set_content_type("application/json");
        $this->output->set_output(json_encode($response));
    }
    private function get_comments_pagination_data()
    {
        $paginationConfig = [];
        $paginationConfig["pagination"]["paginationLinks"] = $this->case_comment->get("paginationLinks");
        $paginationConfig["pagination"]["configPage"] = $this->case_comment->pagination_config("page");
        $paginationConfig["pagination"]["configInPage"] = $this->case_comment->pagination_config("inPage");
        $paginationConfig["pagination"]["paginationTotalRows"] = $this->case_comment->get("paginationTotalRows");
        return $paginationConfig["pagination"];
    }
    public function delete_email_comment($id)
    {
        $response = [];
        $this->load->model("case_comment_email", "case_comment_emailfactory");
        $this->case_comment_email = $this->case_comment_emailfactory->get_instance();
        if ($this->case_comment_email->fetch($id)) {
            $this->load->model("case_comment", "case_commentfactory");
            $this->load->model("case_comment_attachment");
            $this->case_comment = $this->case_commentfactory->get_instance();
            $case_comment_id = $this->case_comment_email->get_field("case_comment");
            $case_comment_email_id = $this->case_comment_email->get_field("id");
            if ((!$this->case_comment_attachment->get_attachments_for_comment($case_comment_id) || $this->case_comment_attachment->delete_all_attachments($case_comment_id)) && $this->case_comment_email->delete(["where" => ["id", $case_comment_email_id]]) && $this->case_comment->delete(["where" => ["id", $case_comment_id]])) {
                $response["status"] = 500;
            } else {
                $response["status"] = 101;
            }
            $this->output->set_content_type("application/json");
            $this->output->set_output(json_encode($response));
        } else {
            show_404();
        }
    }
    private function notes_history()
    {
        if (!$this->input->is_ajax_request()) {
            redirect("dashboard");
        }
        if ($this->input->post(NULL)) {
            $response = [];
            $filter = $this->input->post("filter");
            $sortable = $this->input->post("sort");
            $caseNoteHistoryId = $this->input->post("caseNoteHistoryId");
            if (isset($caseNoteHistoryId) && 0 < $caseNoteHistoryId) {
                $this->load->model("case_comment_attachment");
                $response = $this->case_comment_attachment->k_load_attachments_for_comment($filter, $sortable);
            } else {
                if ($this->input->post("returnData")) {
                    $this->load->model("case_comment", "case_commentfactory");
                    $this->case_comment = $this->case_commentfactory->get_instance();
                    $response = $this->case_comment->k_load_all_legal_case_notes_history($filter, $sortable);
                    for ($d = 0; $d < count($response["data"]); $d++) {
                        $response["data"][$d]["comment"] = strip_tags($response["data"][$d]["comment"]);
                    }
                }
            }
            $this->output->set_content_type("application/json");
            $this->output->set_output(json_encode($response));
        }
    }
    public function related_edit()
    {
        $response = [];
        $response["result"] = false;
        $response["validationErrors"] = "";
        if ($this->input->post(NULL)) {
            $this->load->model("related_case", "related_casefactory");
            $this->related_case = $this->related_casefactory->get_instance();
            $relatedData = json_decode($this->input->post("models"), true);
            if (3 <= strlen($relatedData[0]["comments"])) {
                $response["result"] = $this->related_case->update_multiple_record($relatedData);
            } else {
                $response["validationErrors"] = sprintf($this->lang->line("min_length_rule"), $this->lang->line("comments"), 3);
            }
        }
        $this->output->set_content_type("application/json");
        $this->output->set_output(json_encode($response));
    }
    public function tasks($caseId = "")
    {
        $this->load->model("task", "taskfactory");
        $this->task = $this->taskfactory->get_instance();
        if ($this->input->post(NULL)) {
            if (!$this->input->is_ajax_request()) {
                $this->set_flashmessage("error", $this->lang->line("invalid_record"));
                redirect("dashboard");
            }
            $response = [];
            $filter = $this->input->post("filter");
            $sortable = $this->input->post("sort");
            if ($this->input->post("returnData")) {
                $response = $this->task->k_load_all_tasks($filter, $sortable, "", true);
            }
            $this->output->set_content_type("application/json");
            $this->output->set_output(json_encode($response));
        } else {
            $data = [];
            $data["tabsNLogs"] = $this->get_vertical_case_tabs($caseId, site_url("cases/tasks/"));
            $legalCase = $this->legal_case->get_fields();
            $legalCase["Status"] = $this->legal_case->get_case_status($legalCase["case_status_id"]);
            $data["legalCase"] = $legalCase;
            $data["operatorsText"] = $this->get_filter_operators("text");
            $data["operatorsNumbers"] = $this->get_filter_operators("number");
            $data["operatorsList"] = $this->get_filter_operators("list");
            $data["archivedValues"] = array_combine($this->task->get("archivedValues"), ["", $this->lang->line("yes"), $this->lang->line("no")]);
            $systemPreferences = $this->session->userdata("systemPreferences");
            $data["systemPreferences"] = $systemPreferences;
            $this->load->model("task_status");
            $this->load->model("task_type", "task_typefactory");
            $this->task_type = $this->task_typefactory->get_instance();
            $data["type"] = $this->task_type->load_list_per_language();
            $configStatus = ["value" => "name", "firstLine" => ["" => $this->lang->line("choose_status")]];
            $data["status"] = $this->task_status->load_list([], $configStatus);
            $data["toMeId"] = $this->is_auth->get_user_id();
            $data["toMeFullName"] = $this->is_auth->get_fullname();
            $data["caseId"] = $caseId;
            $data["assignedToFullName"] = "";
            $data["category"] = $legalCase["category"] == "Litigation" ? "litigation_" : "matter_";
            $data["priorities"] = array_combine($this->task->get("priorityValues"), [$this->lang->line("critical"), $this->lang->line("high"), $this->lang->line("medium"), $this->lang->line("low")]);
            $this->includes("kendoui/js/kendo.web.min", "js");
            $this->includes("kendoui/styles/kendo.common-bootstrap.min", "css");
            $this->includes("kendoui/styles/kendo.bootstrap.min", "css");
            $this->includes("scripts/show_hide_customer_portal", "js");
            $this->includes("jquery/form2js", "js");
            $this->includes("jquery/toObject", "js");
            $this->includes("scripts/case_tasks", "js");
            if ($this->is_auth->is_layout_rtl()) {
                $this->includes("styles/rtl/fixes", "css");
            }
            $this->load->view("partial/header");
            $this->load->view("cases/tasks", $data);
        }
    }
    public function hearing_bulk_update_summary_to_client()
    {
        $response = [];
        $gridData = $this->input->post("gridData");
        if (isset($gridData["hearingIds"]) && !empty($gridData["hearingIds"])) {
            $hearingIds = [];
            foreach ($gridData["hearingIds"] as $key => $id) {
                array_push($hearingIds, $id);
            }
            if (!empty($hearingIds)) {
                $hearingIdsStr = implode(",", $hearingIds);
                $this->load->model("legal_case_hearing", "legal_case_hearingfactory");
                $this->legal_case_hearing = $this->legal_case_hearingfactory->get_instance();
                $response["result"] = $this->legal_case_hearing->bulk_update_summary_to_client($hearingIdsStr);
                $empty_summary = $this->legal_case_hearing->get_empty_summary($hearingIdsStr);
                $empty_summary_count = empty($empty_summary) ? 0 : count($empty_summary);
                $response["summary_msg"] = sprintf($this->lang->line("bulk_update_summary_to_client_empty_summary"), $empty_summary_count);
            }
        } else {
            $response["empty_rows"] = $this->lang->line("no_hearings_are_selected");
        }
        $this->output->set_content_type("application/json")->set_output(json_encode($response));
    }

    public function close_file(){
        if (!$this->input->is_ajax_request()) {
            show_404();
        }
        $response = ["result" => false, "message" => ""];
        //get the legal case id
        $legal_case_id = $this->input->post("legalCaseId");
        if (empty($legal_case_id)) {
            $response = ["result" => false, "message" => $this->lang->line("invalid_record")];
            $this->output->set_content_type("application/json")->set_output(json_encode($response));
            return;
        }else {
            $this->load->model("legal_case", "legal_casefactory");
            $this->legal_case = $this->legal_casefactory->get_instance();
            if ($this->legal_case->fetch($legal_case_id)) {
                $this->legal_case->set_field("closedOn", date("Y-m-d")); // Assuming 4 is the ID for closed status
                $this->legal_case->set_field("archived", "yes");
                $this->legal_case->set_field("closed_by", $this->is_auth->get_user_id());

                if ($this->legal_case->update()) {
                    $response = ["result" => true, "message" => $this->lang->line("case_closed_successfully")];
                } else {
                    $response = ["result" => false, "message" => $this->lang->line("error")];
                }
            } else {
                $response = ["result" => false, "message" => $this->lang->line("invalid_record")];
            }
        }
        $this->output->set_content_type("application/json")->set_output(json_encode($response));
    }
    /**
     * Recommend case closure
     * @param int $matter_id
     * @return void
     */
    public function recommend_case_closure($matter_id=0)
    {
        $response = ["result" => true, "error" => false, "info" => false];
        //check if its a post request type
        $this->authenticate_exempted_actions();
        if (!$this->input->is_ajax_request()) {
            show_404();
        }

        if($this->input->post(null)){
            $response = [];
            $this->load->model("legal_case", "legal_casefactory");
            $this->legal_case = $this->legal_casefactory->get_instance();
            $legal_case_id=$this->input->post("legalCaseId");
            $closureRequestedBy=$this->input->post("closureRequestedBy");
            $comments=$this->input->post("comments");
            $this->legal_case->fetch($legal_case_id);
            $this->legal_case->set_field("closure_comments",$comments);
            $this->legal_case->set_field("closure_requested_by",$closureRequestedBy);

            $response["result"] = false;
            if ($this->legal_case->update())
            {
                $response["result"]=true;
            } else {
                $response["error"] = $this->lang->line("invalid_record");
            }
        } else{
            if ($matter_id > 0 &&$this->legal_case->fetch($matter_id)) {
                $data["requestedBy"] = $this->legal_case->get_field("requestedBy");
                $data["legal_case"] = $this->legal_case->load_case($matter_id);
                $response["result"] = true;
                $data["title"] = $this->lang->line("recommend_case_closure");;
                $data["current_case_id"]=$matter_id;
                $response["html"] = $this->load->view("cases/recommend_case_closure", $data, true);
                $this->output->set_content_type("application/json");
                $this->output->set_output(json_encode($response));
            } else {
                $response["error"] = $this->lang->line("invalid_record");
                $response["result"] = false;
            }
        }
        $this->output->set_content_type("application/json")->set_output(json_encode($response));

    }
    public function archive_unarchive_cases()
    {
        $response = [];
        if (!$this->input->post(NULL)) {
            $systemPreferences = $this->session->userdata("systemPreferences");
            $affectedRows = $this->legal_case->archieved_cases_total_number();
            $this->db->where("legal_cases.case_status_id IN ( " . $systemPreferences["archiveCaseStatus"] . ")")->update("legal_cases", ["archived" => "yes"]);
            $archiveCaseStatus = $this->workflow_status->loadListWorkflowStatuses("", ["where" => [["id IN ( " . $systemPreferences["archiveCaseStatus"] . ")", NULL, false]]]);
            $archiveCaseStatusStr = implode(", ", array_values($archiveCaseStatus));
            $response["message"] = sprintf($this->lang->line("feedback_message_archived_object"), $affectedRows, $this->lang->line("cases"), $archiveCaseStatusStr);
        } else {
            if ($this->input->post("case_id")) {
                $case_details = $this->legal_case->load(["select" => ["case_status_id,archived"], "where" => ["id", $this->input->post("case_id")]]);
                $result = $this->db->where("legal_cases.id = (" . $this->input->post("case_id") . ")")->update("legal_cases", ["archived" => $case_details["archived"] == "no" ? "yes" : "no"]);
                $response["archived"] = $result ? $case_details["archived"] == "no" ? "yes" : "no" : $case_details["archived"];
                $response["status"] = $result;
            } else {
                $gridData = $this->input->post("gridData");
                foreach ($gridData["caseIds"] as $key => $id) {
                    $this->legal_case->fetch($id);
                    $this->legal_case->set_field("archived", "no");
                    $this->legal_case->set_field("modifiedByChannel", $this->legal_case->get("webChannel"));
                    $response["status"] = $this->legal_case->update() ? 202 : 101;
                }
            }
        }
        $this->output->set_content_type("application/json");
        $this->output->set_output(json_encode($response));
    }
    public function archive_selected_cases()
    {
        $gridData = $this->input->post("gridData");
        if (!empty($gridData)) {
            foreach ($gridData["caseIds"] as $key => $id) {
                $this->legal_case->fetch($id);
                $this->legal_case->set_field("archived", "yes");
                $this->legal_case->set_field("modifiedByChannel", $this->legal_case->get("webChannel"));
                $response["status"] = $this->legal_case->update() ? 202 : 101;
            }
            $this->output->set_content_type("application/json");
            $this->output->set_output(json_encode($response));
        }
    }
    public function reminders($legal_case_id = 0)
    {
        $this->load->model("reminder", "reminderfactory");
        $this->reminder = $this->reminderfactory->get_instance();
        if ($this->input->post(NULL)) {
            if (!$this->input->is_ajax_request()) {
                redirect("reminders");
            }
            $response = [];
            $filter = $this->input->post("filter");
            $sortable = $this->input->post("sort");
            if ($this->input->post("returnData")) {
                $response = $this->reminder->k_load_all_reminders($filter, $sortable);
            }
            $systemPreferences = $this->session->userdata("systemPreferences");
            $response["hijri_calendar_enabled"] = isset($systemPreferences["hijriCalendarFeature"]) && $systemPreferences["hijriCalendarFeature"] ? $systemPreferences["hijriCalendarFeature"] : 0;
            $this->output->set_content_type("application/json");
            $this->output->set_output(json_encode($response));
        } else {
            $data = [];
            $this->legal_case->fetch($legal_case_id);
            $data["tabsNLogs"] = $this->get_vertical_case_tabs($legal_case_id, site_url("cases/reminders/"));
            $legalCase = $this->legal_case->get_fields();
            $legalCase["Status"] = $this->legal_case->get_case_status($legalCase["case_status_id"]);
            $data["legalCase"] = $legalCase;
            $data["caseId"] = $legal_case_id;
            $systemPreferences = $this->session->userdata("systemPreferences");
            $data["systemPreferences"] = $systemPreferences;
            $data["case_subject"] = $legalCase["subject"] ? $this->legal_case->get("modelCode") . $legal_case_id . ": " . (42 < strlen($legalCase["subject"]) ? mb_substr($legalCase["subject"], 0, 42) . "..." : $legalCase["subject"]) : "";
            $this->load->model("task", "taskfactory");
            $this->task = $this->taskfactory->get_instance();
            $this->load->model("reminder_type", "reminder_typefactory");
            $this->reminder_type = $this->reminder_typefactory->get_instance();
            $data["operatorsText"] = $this->get_filter_operators("text");
            $data["operatorsNumbers"] = $this->get_filter_operators("number");
            $data["operatorsList"] = $this->get_filter_operators("list");
            $data["operatorsDate"] = $this->get_filter_operators("date");
            $data["operatorsGroupList"] = $this->get_filter_operators("groupList");
            $data["operatorsTime"] = $this->get_filter_operators("operatorsTime");
            $data["category"] = $legalCase["category"] == "Litigation" ? "litigation_" : "matter_";
            $data["types"] = $this->reminder_type->load_list_per_language();
            unset($data["types"][""]);
            $data["statuses"] = array_combine($this->reminder->get("reminderStatuses"), ["", $this->lang->line("open"), $this->lang->line("dismissed")]);
            if ($legal_case_id) {
                $data["legalCaseIdFixedFilter"] = $this->legal_case->get_field("id");
            } else {
                $data["legalCaseIdFixedFilter"] = "";
            }
            $data["hijri_calendar_enabled"] = isset($systemPreferences["hijriCalendarFeature"]) && $systemPreferences["hijriCalendarFeature"] ? $systemPreferences["hijriCalendarFeature"] : 0;
            $this->includes("kendoui/js/kendo.web.min", "js");
            $this->includes("kendoui/styles/kendo.common-bootstrap.min", "css");
            $this->includes("kendoui/styles/kendo.bootstrap.min", "css");
            $this->includes("scripts/show_hide_customer_portal", "js");
            $this->includes("jquery/form2js", "js");
            $this->includes("jquery/toObject", "js");
            $this->includes("scripts/legal_case_reminders", "js");
            if ($this->is_auth->is_layout_rtl()) {
                $this->includes("styles/rtl/fixes", "css");
            }
            $this->load->view("partial/header");
            $this->load->view("cases/reminders", $data);
        }
    }
    private function actions_related_contacts_companies()
    {
        $response = [];
        $action = $this->input->post("action");
        switch ($action) {
//            case "getBasicCourtActivityList":
//               $this->readBasicCourtActivityList($response);
//                break;
            case "addContributor":
                $this->add_contact("contributor", $response);
                break;
            case "readContributor":
                $this->read_contacts("contributor", $response);
                break;
            case "updateContributor":
                $this->update_contacts($response);
                break;
            case "deleteContributor":
                $this->delete_contact($response);
                break;
            case "addContact":
                $this->add_contact("contact", $response);
                break;
            case "readContacts":
                $this->read_contacts("contact", $response);
                break;
            case "updateContacts":
                $this->update_contacts($response);
                break;
            case "deleteContacts":
                $this->delete_contact($response);
                break;
            case "addCompany":
                $this->add_company("company", $response);
                break;
            case "readCompanies":
                $this->read_companies("company", $response);
                break;
            case "updateCompanies":
                $this->update_companies($response);
                break;
            case "deleteCompanies":
                $this->delete_company($response);
                break;
            case "readOutsource":
                $this->read_outsource($response);
                break;
            case "addOutsource":
                $this->add_outsource($response);
                break;
            case "updateOutsource":
                $this->update_outsource($response);
                break;
            case "deleteOutsourceCompany":
                $this->delete_company($response);
                break;
            case "deleteOutsourceContact":
                $this->delete_contact($response);
                break;
            case "deleteOutsource":
                $this->delete_outsource($response);
                break;
            default:
        }
        $this->output->set_content_type("application/json")->set_output(json_encode($response));
    }
    private function add_outsource(&$response)
    {
        $request_data = $this->input->post(NULL);
        $legal_case_outsource_contacts_arr = [];
        if ($this->input->is_ajax_request() && !empty($request_data)) {
            $legal_case_id = $request_data["case_id"] ?? NULL;
            $company_id = $request_data["outsource_company_id"] ?? NULL;
            if (!$this->legal_case_outsource->company_already_outsourced($legal_case_id, $company_id)) {
                $this->legal_case_outsource->reset_fields();
                $this->legal_case_outsource->set_field("legal_case_id", $legal_case_id);
                $this->legal_case_outsource->set_field("company_id", $company_id);
                $this->legal_case_outsource->set_field("createdBy", $this->is_auth->get_user_id());
                $this->legal_case_outsource->set_field("modifiedBy", $this->is_auth->get_user_id());
                if ($this->legal_case_outsource->insert()) {
                    $legal_case_outsource_contacts = $request_data["outsource_company_contacts"] ?? NULL;
                    $legal_case_outsource_contacts_arr = explode(",", $legal_case_outsource_contacts);
                    if (0 < count($legal_case_outsource_contacts_arr)) {
                        $this->load->model("legal_case_outsource_contact", "legal_case_outsource_contactfactory");
                        $this->legal_case_outsource_contact = $this->legal_case_outsource_contactfactory->get_instance();
                        foreach ($legal_case_outsource_contacts_arr as $contact_id) {
                            $this->legal_case_outsource_contact->set_field("legal_case_outsource_id", $this->legal_case_outsource->get_field("id"));
                            $this->legal_case_outsource_contact->set_field("contact_id", $contact_id);
                            if ($this->legal_case_outsource_contact->insert()) {
                                $this->legal_case_outsource_contact->reset_fields();
                                $this->load->model("advisor_users", "advisor_usersfactory");
                                $this->advisor_users = $this->advisor_usersfactory->get_instance();
                                if ($this->advisor_users->fetch(["contact_id" => $contact_id])) {
                                    $this->notify_advisor_on_assignment($contact_id, $legal_case_id);
                                }
                            } else {
                                $response["validationErrors"] = $this->legal_case_outsource_contact->get("validationErrors");
                            }
                        }
                        $response["status"] = !empty($response["validationErrors"]) ? 102 : 202;
                        if ($response["status"] == 202) {
                            $shareDocumentsWithAdvisors = $request_data["share_documents_with_outsource"] ?? NULL;
                            if ($shareDocumentsWithAdvisors) {
                                $sharedDocumentsWithLegalCases = $this->legal_case->get_shared_documents_with_legal_cases();
                                $sharedDocumentsWithLegalCases[] = $legal_case_id;
                                $this->system_preference->set_value_by_key("AdvisorConfig", "SharedDocumentsLegalCases", serialize($sharedDocumentsWithLegalCases));
                                $this->legal_case->share_documents_with_advisors($legal_case_id);
                                $response["hide_share_documents_with_advisors"] = true;
                            }
                        }
                    }
                } else {
                    $response["validationErrors"] = $this->legal_case_outsource->get("validationErrors");
                }
            } else {
                $response["status"] = 101;
            }
        }
    }
    private function update_outsource(&$response)
    {
        $post = $this->input->post(NULL);
        if (!empty($post)) {
            $legal_case_outsource_id = $post["legal_case_outsource_id"] ?? NULL;
            $legal_case_outsource = $this->legal_case_outsource->fetch($legal_case_outsource_id);
            if ($legal_case_outsource) {
                $this->load->model("legal_case_outsource_contact", "legal_case_outsource_contactfactory");
                $this->legal_case_outsource_contact = $this->legal_case_outsource_contactfactory->get_instance();
                $legal_case_outsource_contacts = $post["outsource_company_contacts"] ?? NULL;
                $legal_case_outsource_contacts_arr = explode(",", $legal_case_outsource_contacts);
                if (0 < count($legal_case_outsource_contacts_arr) && $this->legal_case_outsource_contact->remove_contacts_per_outsource($legal_case_outsource_id)) {
                    foreach ($legal_case_outsource_contacts_arr as $contact_id) {
                        $this->legal_case_outsource_contact->set_field("legal_case_outsource_id", $this->legal_case_outsource->get_field("id"));
                        $this->legal_case_outsource_contact->set_field("contact_id", $contact_id);
                        if ($this->legal_case_outsource_contact->insert()) {
                            $this->legal_case_outsource_contact->reset_fields();
                        } else {
                            $response["validationErrors"] = $this->legal_case_outsource_contact->get("validationErrors");
                        }
                    }
                    $response["status"] = !empty($response["validationErrors"]) ? 102 : 202;
                }
            }
        }
    }
    private function delete_outsource(&$response)
    {
        $outsource_id = $this->input->post("recordId");
        $response["status"] = 102;
        if (!empty($outsource_id)) {
            $response["status"] = $this->legal_case_outsource->delete_outsource($outsource_id) ? 202 : 102;
        }
    }
    private function read_outsource(&$response)
    {
        $legal_case_id = $this->input->post("legal_case_id");
        $response = $this->legal_case_outsource->k_load_all_outsources($legal_case_id);
    }
    private function add_contact($contact_type, &$response)
    {
        $this->load->model("legal_case_contact");
        $contact_id = $this->input->post("contact_id") ? $this->input->post("contact_id") : $this->input->post("outsource_id");
        $posted_contact_type = $this->input->post("contactType") ? $this->input->post("contactType") : $this->input->post("outsource_relation_type");
        $case_id = $this->input->post("case_id");
        $criteria = ["case_id" => $case_id, "contact_id" => $contact_id];
        if ($contact_type == $posted_contact_type) {
            $criteria["contactType"] = $posted_contact_type;
        }
        $contactExist = $this->legal_case_contact->fetch($criteria);
        if ($contactExist) {
            $response["status"] = 101;
        } else {
            $this->legal_case_contact->set_fields($this->input->post(NULL));
            $this->legal_case_contact->set_field("contact_id", $contact_id);
            $this->legal_case_contact->set_field("contactType", $posted_contact_type);
            $response["status"] = $this->legal_case_contact->insert() ? 202 : 102;
        }
        $this->load->model("advisor_users", "advisor_usersfactory");
        $this->advisor_users = $this->advisor_usersfactory->get_instance();
        if ($this->advisor_users->fetch(["contact_id" => $contact_id])) {
            $this->notify_advisor_on_assignment($contact_id, $case_id);
        }
        $response["validationErrors"] = $this->legal_case_contact->get("validationErrors");
    }
    private function notify_advisor_on_assignment($contact_id, $legal_case_id)
    {
        $this->load->model("contact", "contactfactory");
        $this->contact = $this->contactfactory->get_instance();
        $this->load->model("user", "userfactory");
        $this->user = $this->userfactory->get_instance();
        $this->load->model("user_profile");
        $legal_case = $this->legal_case->load(["where" => [["id", $legal_case_id]]]);
        $trigger = "core_user_assigned_case";
        $legal_case_id = $legal_case["id"] ?? NULL;
        $advisor_contact = $this->contact->load($contact_id);
        $advisor = new stdClass();
        $advisor->name = ($advisor_contact["firstName"] ?? NULL) . " " . ($advisor_contact["lastName"] ?? NULL);
        $advisor->email = $this->advisor_users->get_field("email");
        $matter_creator_user = $this->user->load(["where" => [["id", $legal_case["createdBy"] ?? NULL]]]);
        $matter_creator = new stdClass();
        $matter_creator->email = $matter_creator_user["email"] ?? NULL;
        $created_by = new stdClass();
        $created_by->id = $this->is_auth->get_user_id();
        $created_by->email = $this->is_auth->get_email_address();
        $cc_emails = [$matter_creator->email];
        $notifications_data = ["to" => $advisor->email, "to_name" => $advisor->name, "object" => $trigger, "object_id" => $legal_case_id, "cc" => $cc_emails, "subject" => $this->legal_case->get_field("subject"), "created_by" => $created_by->id, "created_by_email" => $created_by->email, "objectModelCode" => $this->legal_case->get("modelCode"), "fromLoggedUser" => $this->is_auth->get_fullname()];
        $this->load->library("email_notifications");
        $this->email_notifications->notify($notifications_data);
    }
    private function read_contacts($contact_type, &$response)
    {
        if ($this->input->post(NULL)) {
            $filter = $this->input->post("filter");
            $sortable = $this->input->post("sort");
            if ($this->input->post("returnData")) {
                if ($contact_type == "external lawyer") {
                    $response = $this->legal_case->k_load_all_cases_outsourcing_lawyers($filter, $sortable);
                } else {
                    if ($contact_type == "contributor") {
                        $response = $this->legal_case->k_load_all_cases_lawyers_contributors($filter, $sortable);
                    } else {
                        $response = $this->legal_case->k_load_all_cases_contacts($filter, $sortable);
                    }
                }
            }
        }
    }
    private function update_contacts(&$response, $models = [])
    {
        $response["status"] = 102;
        $this->load->model("legal_case_contact");
        $contactsData = !empty($models) ? $models : json_decode($this->input->post("models"), true);
        foreach ($contactsData as $contactData) {
            $role_id = !empty($contactData["legal_case_contact_role_id"]) ? $contactData["legal_case_contact_role_id"] : (!empty($contactData["role_id"]) ? $contactData["role_id"] : NULL);
            $response["status"] = $this->legal_case_contact->fetch_contact($contactData["id"], $role_id);
            if ($response["status"] != 101) {
                $this->legal_case_contact->fetch($contactData["id"]);
                $this->legal_case_contact->set_field("comments", $contactData["comments"]);
                $this->legal_case_contact->set_field("legal_case_contact_role_id", $role_id);
                $response["status"] = $this->legal_case_contact->update() ? 202 : 102;
            }
        }
        if ($response["status"] == 202) {
            $this->legal_case->set_field("id", $this->legal_case_contact->get_field("case_id"));
            $this->legal_case->touch_logs();
        }
        $response["validationErrors"] = $this->legal_case_contact->get("validationErrors");
    }
    private function delete_contact(&$response)
    {
        $this->load->model("legal_case_contact");
        $response["status"] = $this->legal_case_contact->delete($this->input->post("recordId")) ? 202 : 102;
    }
    private function add_company($company_type, &$response)
    {
        $this->load->model("legal_case_company");
        $company_id = $this->input->post("company_id") ? $this->input->post("company_id") : $this->input->post("outsource_id");
        $posted_company_type = $this->input->post("companyType") ? $this->input->post("companyType") : $this->input->post("outsource_relation_type");
        $criteria = ["case_id" => $this->input->post("case_id"), "company_id" => $company_id];
        if ($company_type == $posted_company_type) {
            $criteria["companyType"] = $posted_company_type;
        }
        $companyExist = $this->legal_case_company->fetch($criteria);
        if ($companyExist) {
            $response["status"] = 101;
        } else {
            $this->legal_case_company->set_fields($this->input->post(NULL));
            $this->legal_case_company->set_field("company_id", $company_id);
            $this->legal_case_company->set_field("companyType", $posted_company_type);
            $response["status"] = $this->legal_case_company->insert() ? 202 : 102;
        }
        $response["validationErrors"] = $this->legal_case_company->get("validationErrors");
    }
    private function read_companies($company_type, &$response)
    {
        if ($this->input->post(NULL)) {
            $filter = $this->input->post("filter");
            $sortable = $this->input->post("sort");
            if ($this->input->post("returnData")) {
                $response = $this->legal_case->k_load_all_cases_companies($company_type, $filter, $sortable);
            }
        }
    }
    private function update_companies(&$response, $models = [])
    {
        $response["status"] = 102;
        $this->load->model("legal_case_company");
        $companiesData = !empty($models) ? $models : json_decode($this->input->post("models"), true);
        foreach ($companiesData as $companyData) {
            $role_id = !empty($companyData["legal_case_company_role_id"]) ? $companyData["legal_case_company_role_id"] : (!empty($companyData["role_id"]) ? $companyData["role_id"] : NULL);
            $response["status"] = $this->legal_case_company->fetch_company($companyData["id"], $role_id);
            if ($response["status"] != 101) {
                $this->legal_case_company->fetch($companyData["id"]);
                $this->legal_case_company->set_field("comments", $companyData["comments"]);
                $this->legal_case_company->set_field("legal_case_company_role_id", $role_id);
                $response["status"] = $this->legal_case_company->update() ? 202 : 102;
            }
        }
        if ($response["status"] == 202) {
            $this->legal_case->set_field("id", $this->legal_case_company->get_field("case_id"));
            $this->legal_case->touch_logs();
        }
        $response["validationErrors"] = $this->legal_case_company->get("validationErrors");
    }
    private function delete_company(&$response)
    {
        $this->load->model("legal_case_company");
        $response["status"] = $this->legal_case_company->delete($this->input->post("recordId")) ? 202 : 102;
    }
    private function return_litigation_stages_details($legal_case_id)
    {
        $data["stages_data"] = $this->legal_case_litigation_detail->load_all_stages_metadata($legal_case_id);
        $this->load->model("legal_case_litigation_external_reference");
        $stages_contacts = $this->legal_case_litigation_detail->load_stage_contacts();
        foreach ($data["stages_data"] as $key => $value) {
            $till_date = strtotime(date("Y-m-d"), time());
            $createdOn = strtotime(date("Y-m-d", strtotime($value["createdOn"])));
            $data["stages_data"][$key]["previous_modified"] = false;
            $data["stages_data"][$key]["current_modified"] = false;
            if (isset($data["stages_data"][$key - 1])) {
                $till_date = strtotime($data["stages_data"][$key - 1]["createdOn"]);
                if ($till_date - $createdOn < 0) {
                    $till_date = strtotime($data["stages_data"][$key - 1]["modifiedOn"]);
                    $data["stages_data"][$key]["previous_modified"] = true;
                }
            } else {
                if ($data["stages_data"][$key]["createdOn"] != $data["stages_data"][$key]["modifiedOn"]) {
                    $createdOn = strtotime($data["stages_data"][$key]["modifiedOn"]);
                    $data["stages_data"][$key]["current_modified"] = true;
                }
            }
            $data["stages_data"][$key]["since"] = floor(abs($till_date - $createdOn) / 86400);
            $data["stages_data"][$key]["ext"] = $this->legal_case_litigation_external_reference->get_external_reference_stage_id($value["id"]);
            foreach ($stages_contacts["data"] as $contacts) {
                if ($contacts["stage"] === $value["id"]) {
                    $data["stages_data"][$key][$contacts["contact_type"]][] = $contacts;
                }
            }
        }
        return $data["stages_data"];
    }
    public function hearings($legal_case_id = 0)
    {
        $this->load->model("legal_case_hearing", "legal_case_hearingfactory");
        $this->legal_case_hearing = $this->legal_case_hearingfactory->get_instance();
        if ($this->input->post(NULL)) {
            $this->hearings_actions($legal_case_id);
        }
    }
    public function hearing_export_to_word($id)
    {
        $this->load->model("legal_case_hearing", "legal_case_hearingfactory");
        $this->legal_case_hearing = $this->legal_case_hearingfactory->get_instance();
        $data["hearing"] = $this->legal_case_hearing->load_hearing_data($id);
        $this->load->model("hearing_document", "hearing_documentfactory");
        $this->hearing_document = $this->hearing_documentfactory->get_instance();
        $attachments = $this->hearing_document->load_all_attachments($id);
        $attachments_names_list = "";
        foreach ($attachments as $attachment) {
            $attachments_names_list .= $attachment["full_name"] . "\\n";
        }
        $data["hearing"]["attachments"] = $attachments_names_list;
        $systemPreferences = $this->session->userdata("systemPreferences");
        if (isset($systemPreferences["hijriCalendarFeature"]) && $systemPreferences["hijriCalendarFeature"]) {
            $data["hearing"]["reference"] = $data["hearing"]["reference"] ? replaceGregorianByHijriInString($data["hearing"]["reference"]) : "";
            $data["hearing"]["sentenceDate"] = $data["hearing"]["sentenceDate"] ? gregorianToHijri($data["hearing"]["sentenceDate"], "Y-m-d") : "";
            $data["hearing"]["startDate"] = gregorianToHijri($data["hearing"]["startDate"], "Y-m-d");
            $data["hearing"]["postponed_date"] = $data["hearing"]["postponed_date"] != " " ? gregorianToHijri($data["hearing"]["postponed_date"]) : $data["hearing"]["postponed_date"];
        }
        $data["hearing"]["hearing_day"] = $this->lang->line(date("l", strtotime($data["hearing"]["startDate"])));
        $this->load->library("word_template_manipulator");
        $docx = $this->word_template_manipulator->get_template_docx_object("hearings");
        $this->word_template_manipulator->set_template_data($docx, $data);
        $corepath = substr(COREPATH, 0, -12);
        $temp_directory = $this->config->item("files_path") . DIRECTORY_SEPARATOR . "tmp";
        if (!is_dir($temp_directory)) {
            @mkdir($temp_directory, 493);
        }
        $file_name = $this->lang->line("hearing") . "_" . $id . "_" . date("YmdHi");
        $docx->createDocx($temp_directory . "/" . $file_name);
        $this->load->helper("download");
        $content = file_get_contents($temp_directory . "/" . $file_name . ".docx");
        unlink($temp_directory . "/" . $file_name . ".docx");
        $file_name_encoded = $this->downloaded_file_name_by_browser($file_name . ".docx");
        force_download($file_name_encoded, $content);
        exit;
    }
    public function list_hearings()
    {
        $this->get_hearings();
    }
    public function my_hearings()
    {
        $this->authenticate_exempted_actions();
        $this->get_hearings("my_hearings");
    }
    public function all_todays_hearings()
    {
        $this->get_hearings("all_todays_hearings");
    }
    public function my_todays_hearings()
    {
        $this->get_hearings("my_todays_hearings");
    }
    public function all_hearings_for_tomorrow()
    {
        $this->get_hearings("all_hearings_for_tomorrow");
    }
    public function my_hearings_for_tomorrow()
    {
        $this->get_hearings("my_hearings_for_tomorrow");
    }
    public function all_hearings_for_this_week()
    {
        $this->get_hearings("all_hearings_for_this_week");
    }
    public function my_hearings_for_this_week()
    {
        $this->get_hearings("my_hearings_for_this_week");
    }
    public function all_hearings_for_this_month()
    {
        $this->get_hearings("all_hearings_for_this_month");
    }
    public function my_hearings_for_this_month()
    {
        $this->get_hearings("my_hearings_for_this_month");
    }
    public function non_verified_hearings()
    {
        $this->get_hearings("non_verified_hearings");
    }
    public function verified_hearings()
    {
        $this->get_hearings("verified_hearings");
    }
    public function judged_hearings()
    {
        $this->get_hearings("judged_hearings");
    }
    private function get_hearings($fixed_filter = "all")
    {
        $this->pageTitle = sprintf($this->lang->line("sheria360_browser_title"), $this->lang->line("hearings") . " | " . $this->lang->line("case_in_menu"));
        $this->load->model("legal_case_hearing", "legal_case_hearingfactory");
        $this->legal_case_hearing = $this->legal_case_hearingfactory->get_instance();
        $this->load->model("grid_saved_filter", "grid_saved_filterfactory");
        $this->grid_saved_filter = $this->grid_saved_filterfactory->get_instance();
        $this->load->model("grid_saved_column");
        $data = [];
        $systemPreferences = $this->session->userdata("systemPreferences");
        $data["model"] = "Legal_Case_Hearing";
        $data["gridSavedFilters"] = $this->grid_saved_filter->loadFiltersList($data["model"], $this->session->userdata("AUTH_user_id"));
        $data["gridSavedFiltersData"] = false;
        $data["gridDefaultFilter"] = $this->grid_saved_filter->getDefaultFilter($data["model"], $this->session->userdata("AUTH_user_id"));
        if ($data["gridDefaultFilter"]) {
            $gridSavedData = $this->grid_saved_filter->load_data($data["gridDefaultFilter"]["id"]);
            $data["gridSavedFiltersData"] = unserialize($gridSavedData["formData"]);
            $data = array_merge($data, $this->grid_saved_column->get_user_grid_details($data["model"], $data["gridDefaultFilter"]["id"]));
        } else {
            $data = array_merge($data, $this->grid_saved_column->get_user_grid_details($data["model"]));
        }
        if ($this->input->post(NULL)) {
            if ($this->input->post("action") === "readHearings") {
                $filter = $this->input->post("filter");
                $sortable = $this->input->post("sort");
                $this->load->model("grid_saved_column");
                $data["model"] = "Legal_Case_Hearing";
                $grid_details = $this->grid_saved_column->get_user_grid_details($data["model"]);
                $response = [];
                $filter = $this->input->post("filter");
                $sortable = $this->input->post("sort");
                if ($this->input->post("loadWithSavedFilters") === "1") {
                    $filter = json_decode($this->input->post("filter"), true);
                } else {
                    $page_size_modified = $this->input->post("pageSize") != $data["grid_saved_details"]["pageSize"];
                    $sort_modified = $this->input->post("sortData") && $this->input->post("sortData") != $data["grid_saved_details"]["sort"];
                    if ($page_size_modified || $sort_modified) {
                        $_POST["model"] = $data["model"];
                        $_POST["grid_saved_filter_id"] = $data["gridDefaultFilter"]["id"];
                        $response = $this->grid_saved_column->save();
                        $response["gridDetails"] = $this->grid_saved_column->get_user_grid_details($data["model"], $data["gridDefaultFilter"]["id"]);
                    }
                }
                $hijri_calendar_enabled = isset($systemPreferences["hijriCalendarFeature"]) && $systemPreferences["hijriCalendarFeature"] ? $systemPreferences["hijriCalendarFeature"] : 0;
                if (isset($data["grid_columns"]["all_columns"]["verifiedSummary"]) && isset($systemPreferences["AllowFeatureHearingVerificationProcess"]) && $systemPreferences["AllowFeatureHearingVerificationProcess"] != "yes") {
                    unset($data["grid_columns"]["all_columns"]["verifiedSummary"]);
                }
                $response["columns_html"] = $this->load->view("grid_saved_columns/dialog_columns_contents", $data, true);
                $response = array_merge($response, $this->legal_case_hearing->k_load_all_hearings($filter, $sortable, 0, "", $hijri_calendar_enabled));
                $response["selected_columns"] = $response["gridDetails"] ?? [];
                $this->output->set_content_type("application/json")->set_output(json_encode($response));
            }
        } else {
            $this->includes("kendoui/js/kendo.web.min", "js");
            $this->includes("kendoui/styles/kendo.common-bootstrap.min", "css");
            $this->includes("kendoui/styles/kendo.bootstrap.min", "css");
            $data["operators"]["text"] = $this->get_filter_operators("text");
            $data["operators"]["number"] = $this->get_filter_operators("number");
            $data["operators"]["date"] = $this->get_filter_operators("date");
            $data["operators"]["time"] = $this->get_filter_operators("operatorsTime");
            $data["operators"]["list"] = $this->get_filter_operators("list");
            $data["operators"]["group_list"] = $this->get_filter_operators("groupList");
            $data["operators"]["lookup"] = $this->get_filter_operators("lookUp");
            $this->load->model(["court", "court_type", "court_region", "court_degree"]);
            $data["formData"]["courtTypes"] = $this->court_type->load_list([]);
            $data["formData"]["courtDegrees"] = $this->court_degree->load_list([]);
            $data["formData"]["courtRegions"] = $this->court_region->load_list([]);
            $data["formData"]["courts"] = $this->court->load_list([]);
            $this->load->model("case_type");
            $data["formData"]["areaOfPractices"] = $this->case_type->load_list();
            $this->load->model("legal_case_client_position", "legal_case_client_positionfactory");
            $this->legal_case_client_position = $this->legal_case_client_positionfactory->get_instance();
            $data["formData"]["clientPositions"] = $this->legal_case_client_position->load_list_per_language();
            unset($data["formData"]["clientPositions"][""]);
            $this->load->model("country", "countryfactory");
            $this->country = $this->countryfactory->get_instance();
            $data["opponentCountries"] = $this->country->load_countries_list();
            $data["assignee_fixed_filter"] = "";
            $data["date_filter_list"] = ["all_todays_hearings", "my_todays_hearings", "all_hearings_for_tomorrow", "my_hearings_for_tomorrow", "all_hearings_for_this_week", "my_hearings_for_this_week", "all_hearings_for_this_month", "my_hearings_for_this_month"];
            switch ($fixed_filter) {
                case "my_hearings":
                    $data["assignee_fixed_filter"] = $this->session->userdata("AUTH_user_id");
                    $data["myHearings"] = true;
                    break;
                case "judged_hearings":
                    $data["judgedHearings"] = true;
                    $extra_fixed_filter = ["id" => "judged", "field_name" => "legal_case_hearings_users.judged", "type" => "hidden", "value" => "yes"];
                    break;
                case "verified_hearings":
                    $data["verifiedHearings"] = true;
                    $extra_fixed_filter = ["id" => "verifiedSummary", "field_name" => "legal_case_hearings_users.verifiedSummary", "type" => "hidden", "value" => 1];
                    break;
                case "non_verified_hearings":
                    $data["verifiedHearings"] = false;
                    $extra_fixed_filter = ["id" => "verifiedSummary", "field_name" => "legal_case_hearings_users.verifiedSummary", "type" => "hidden", "value" => 0];
                    break;
                default:
                    if (in_array($fixed_filter, $data["date_filter_list"])) {
                        $data["selectedDateFilter"] = $fixed_filter;
                        if (substr($fixed_filter, 0, 3) === "my_") {
                            $data["assignee_fixed_filter"] = $this->session->userdata("AUTH_user_id");
                        }
                        if (in_array($fixed_filter, ["all_todays_hearings", "my_todays_hearings"])) {
                            $data["fixed_filter_operator"] = "tday";
                        } else {
                            if (in_array($fixed_filter, ["all_hearings_for_tomorrow", "my_hearings_for_tomorrow"])) {
                                $data["fixed_filter_operator"] = "tomorrow";
                            } else {
                                if (in_array($fixed_filter, ["all_hearings_for_this_week", "my_hearings_for_this_week"])) {
                                    $data["fixed_filter_operator"] = "tw";
                                } else {
                                    if (in_array($fixed_filter, ["all_hearings_for_this_month", "my_hearings_for_this_month"])) {
                                        $data["fixed_filter_operator"] = "tm";
                                    }
                                }
                            }
                        }
                    } else {
                        $data["allHearings"] = true;
                    }
            }
            $this->load->model("legal_case_stage", "legal_case_stagefactory");
            $this->legal_case_stage = $this->legal_case_stagefactory->get_instance();
            $data["formData"]["stages"] = $this->legal_case_stage->load_list_per_case_category_per_language("litigation");
            unset($data["formData"]["stages"][""]);
            $this->load->model("hearing_types_languages", "hearing_types_languagesfactory");
            $this->hearing_types_languages = $this->hearing_types_languagesfactory->get_instance();
            $data["formData"]["types"] = $this->hearing_types_languages->load_list_per_language();
            unset($data["formData"]["types"][""]);
            $data["formData"]["types"] = [$this->lang->line("none")] + $data["formData"]["types"];
            $data["hijri_calendar_enabled"] = isset($systemPreferences["hijriCalendarFeature"]) && $systemPreferences["hijriCalendarFeature"] ? $systemPreferences["hijriCalendarFeature"] : 0;
            $data["formData"]["judgedValues"] = array_combine($this->legal_case_hearing->get("judgedValues"), [$this->lang->line("either"), $this->lang->line("yes"), $this->lang->line("no")]);
            $data["loggedUserIsAdminForGrids"] = $this->session->userdata("AUTH_is_grid_admin");
            $this->load->model("user", "userfactory");
            $this->user = $this->userfactory->get_instance();
            $data["formData"]["users_list"] = $this->user->load_all_list();
            $data["formData"]["users_list"][0] = "";
            $this->load->helper("grid_advanced_filters_helper");
            $data["advanced_filter"] = get_model_filters("Hearing");
            if (isset($extra_fixed_filter)) {
                array_push($data["advanced_filter"], $extra_fixed_filter);
            }
            if ($data["assignee_fixed_filter"]) {
                foreach ($data["advanced_filter"] as $advanced_key => $advanced_filter) {
                    if ($advanced_filter["id"] == "user_id") {
                        $data["advanced_filter"][$advanced_key]["value"] = $data["assignee_fixed_filter"];
                    }
                }
            }
            $data["verification_process_enabled"] = isset($systemPreferences["AllowFeatureHearingVerificationProcess"]) && $systemPreferences["AllowFeatureHearingVerificationProcess"] == "yes";
            $this->includes("jquery/form2js", "js");
            $this->includes("jquery/toObject", "js");
            $this->includes("scripts/legal_case_list_hearings", "js");
            $this->includes("jquery/spectrum", "js");
            $this->includes("jquery/tabledit/jquery.tabledit.min", "js");
            if ($this->is_auth->is_layout_rtl()) {
                $this->includes("styles/rtl/fixes", "css");
            }
            $this->load->view("partial/header");
            $this->load->view("cases/hearings/index", $data);
        }
    }

    public function change_litigation_stage_status($case_id, $litigation_stage)
    {
        $this->authenticate_exempted_actions();
        if (!$this->input->is_ajax_request()) {
            show_404();
        }
        $response = [];
        $this->load->model("legal_case_litigation_detail", "legal_case_litigation_detailfactory");
        $this->legal_case_litigation_detail = $this->legal_case_litigation_detailfactory->get_instance();
        $this->load->model("legal_case_stage", "legal_case_stagefactory");
        $this->legal_case_stage = $this->legal_case_stagefactory->get_instance();
        $this->load->model("stage_status_language", "stage_status_languagefactory");
        $this->stage_status_language = $this->stage_status_languagefactory->get_instance();
        $response = [];
        if ($this->legal_case_litigation_detail->fetch($litigation_stage) && $this->legal_case->fetch($case_id)) {
            if (!$this->input->post(NULL)) {
                $data = [];
                $data["legal_case_id"] = $case_id;
                $data["litigation_stage_id"] = $litigation_stage;
                $data["statuses"] = $this->stage_status_language->load_list_per_language();
                $data["stage_status"] = $this->legal_case_litigation_detail->get_field("status");
                $response["html"] = $this->load->view("cases/litigation/change_litigation_stage_status", $data, true);
            } else {
                $this->load->model("language");
                $this->legal_case_litigation_detail->set_field("status", $this->input->post("status"));
                $response["result"] = $this->legal_case_litigation_detail->update();
                if ($response["result"] && $this->input->post("status")) {
                    $this->load->model("litigation_stage_status_history");
                    $this->litigation_stage_status_history->set_field("litigation_stage", $litigation_stage);
                    $this->litigation_stage_status_history->set_field("status", $this->input->post("status"));
                    $this->litigation_stage_status_history->set_field("action_maker", $this->is_auth->get_user_id());
                    $this->litigation_stage_status_history->set_field("movedOn", date("Y-m-d H:i:s", time()));
                    $this->litigation_stage_status_history->insert();
                }
            }
        }
        $this->output->set_content_type("application/json")->set_output(json_encode($response));
    }
    public function add_court_external_ref()
    {
        $this->authenticate_exempted_actions();
        if (!$this->input->is_ajax_request()) {
            show_404();
        }
        $response = [];
        $this->load->model("legal_case_litigation_external_reference");
        $response = [];
        if ($this->input->post(NULL)) {
            $systemPreferences = $this->session->userdata("systemPreferences");
            $hijri_calendar_enabled = isset($systemPreferences["hijriCalendarFeature"]) && $systemPreferences["hijriCalendarFeature"] ? $systemPreferences["hijriCalendarFeature"] : 0;
            if ($hijri_calendar_enabled) {
                $_POST["refDate"] = hijriToGregorian($this->input->post("refDate"));
            }
            $this->legal_case_litigation_external_reference->set_field("number", $this->input->post("number"));
            $this->legal_case_litigation_external_reference->set_field("comments", $this->input->post("comments"));
            $this->legal_case_litigation_external_reference->set_field("refDate", strtotime($this->input->post("refDate")) != NULL ? date("Y-m-d", strtotime($this->input->post("refDate"))) : "");
            $this->legal_case_litigation_external_reference->set_field("stage", $this->input->post("stage_id"));
            $response["result"] = $this->legal_case_litigation_external_reference->insert();
            if (!$response["result"]) {
                $response["validationErrors"] = $this->legal_case_litigation_external_reference->get("validationErrors");
            }
        } else {
            $response["error"] = $this->lang->line("invalid_request");
        }
        $this->output->set_content_type("application/json")->set_output(json_encode($response));
    }
    public function update_court_external_ref()
    {
        $this->authenticate_exempted_actions();
        if (!$this->input->is_ajax_request()) {
            show_404();
        }
        $response = [];
        $this->load->model("legal_case_litigation_external_reference");
        $response = [];
        if ($this->legal_case_litigation_external_reference->fetch($this->input->post("id"))) {
            if ($this->input->post(NULL)) {
                $systemPreferences = $this->session->userdata("systemPreferences");
                $hijri_calendar_enabled = isset($systemPreferences["hijriCalendarFeature"]) && $systemPreferences["hijriCalendarFeature"] ? $systemPreferences["hijriCalendarFeature"] : 0;
                if ($hijri_calendar_enabled) {
                    $_POST["refDate"] = hijriToGregorian($this->input->post("refDate"));
                }
                $this->legal_case_litigation_external_reference->set_field("number", $this->input->post("number"));
                $this->legal_case_litigation_external_reference->set_field("comments", $this->input->post("comments"));
                $this->legal_case_litigation_external_reference->set_field("refDate", strtotime($this->input->post("refDate")) != NULL ? date("Y-m-d", strtotime($this->input->post("refDate"))) : "");
                $this->legal_case_litigation_external_reference->set_field("stage", $this->input->post("stage_id"));
                $response["result"] = $this->legal_case_litigation_external_reference->update();
                if (!$response["result"]) {
                    $response["validationErrors"] = $this->legal_case_litigation_external_reference->get("validationErrors");
                }
            }
        } else {
            $response["error"] = $this->lang->line("invalid_request");
        }
        $this->output->set_content_type("application/json")->set_output(json_encode($response));
    }
    public function delete_court_external_ref()
    {
        $this->authenticate_exempted_actions();
        if (!$this->input->is_ajax_request()) {
            show_404();
        }
        $response = [];
        $this->load->model("legal_case_litigation_external_reference");
        $response = [];
        $id = $this->input->post("id");
        if ($this->legal_case_litigation_external_reference->fetch($id)) {
            $response["result"] = $this->legal_case_litigation_external_reference->delete($id);
        } else {
            $response["error"] = $this->lang->line("invalid_request");
        }
        $this->output->set_content_type("application/json")->set_output(json_encode($response));
    }
    private function hearings_actions($legal_case_id = 0)
    {
        $response = [];
        $this->load->model("legal_case_litigation_detail", "legal_case_litigation_detailfactory");
        $this->legal_case_litigation_detail = $this->legal_case_litigation_detailfactory->get_instance();
        $action = $this->input->post("action");
        $systemPreferences = $this->session->userdata("systemPreferences");
        $this->load->model("system_preference");
        $system_preference = $this->system_preference->get_key_groups();
        $hijri_calendar_enabled = isset($systemPreferences["hijriCalendarFeature"]) && $systemPreferences["hijriCalendarFeature"] ? $systemPreferences["hijriCalendarFeature"] : 0;
        switch ($action) {
            case "getHearingForm":
                $hearing_id = $this->input->post("hearingId");
                if ($hearing_id == 0 && $this->request_can_cause_insufficient_anti_automation("Hearing")) {
                    $result = false;
                    $response["error"] = $this->lang->line("insufficient_anti_automation_message");
                } else {
                    $this->load->helper("text");
                    $this->load->model("hearing_document", "hearing_documentfactory");
                    $this->hearing_document = $this->hearing_documentfactory->get_instance();
                    $data["legal_case_id"] = $legal_case_id == 0 ? "" : $legal_case_id;
                    $this->legal_case_hearing->fetch($hearing_id);
                    $this->legal_case->fetch($legal_case_id);
                    $data["case_category"] = $this->legal_case->get_field("category");
                    $data["case_subject"] = $this->legal_case->get_field("subject");
                    $data["case_model_code"] = $this->legal_case->get("modelCode");
                    $data["hearings_data"] = $this->legal_case_hearing->get_fields();
                    $data["hearings_data"]["attachments"] = $this->hearing_document->load_all_attachments($hearing_id);
                    $data["ability_set_latest_development"] = $system_preference["DefaultValues"]["abilitySetLatestDevelopment"];
                    $data["latest_development"] = $this->legal_case->get_field("latest_development");
                    if (0 < $hearing_id) {
                        $this->legal_case_hearing->update_recent_ids($hearing_id, "hearings");
                        $hearingLawyers = $this->legal_case_hearing->load_extra_users_data($hearing_id);
                        $data["hearingLawyersUsers"] = isset($hearingLawyers[0]) ? $hearingLawyers[0] : [];
                        $data["hearingLawyersStatus"] = isset($hearingLawyers[1]) ? $hearingLawyers[1] : [];
                        $data["hearingAdvisorUsers"] = isset($hearingLawyers[2]) ? $hearingLawyers[2] : [];
                        $response["stage_html"] = $this->return_litigation_stage_html($legal_case_id, $data["hearings_data"]["stage"], $hearing_id);
                        $this->legal_case_litigation_detail->fetch($data["hearings_data"]["stage"]);
                        $data["judgment_date"] = $this->legal_case_litigation_detail->get_field("sentenceDate");
                        $data["title"] = $this->lang->line("edit_hearing");
                        $data["stage_status"] = $this->legal_case_litigation_detail->get_field("status");
                    } else {
                        $data["judgment_date"] = "";
                        $data["hearingLawyersUsers"] = [];
                        if (0 < $legal_case_id) {
                            $assignee = $this->legal_case->get_field("user_id");
                            if ($assignee) {
                                $this->load->model("user_profile");
                                $this->user_profile->fetch(["user_id" => $this->legal_case->get_field("user_id")]);
                                $data["hearingLawyersUsers"] = [$this->user_profile->get_field("user_id") => $this->user_profile->get_field("firstName") . " " . $this->user_profile->get_field("lastName")];
                            }
                        }
                        $data["title"] =  $data["case_category"]=="Litigation"?$this->lang->line("add_a_hearing"):$this->lang->line("add_adr_session");
                    }

                    $data["hide_show_notification"] = $this->email_notification_scheme->get_hide_show_send_email_notification_by_trigger_action("add_hearing") == "1" ? "yes" : "";
                    $this->load->model("hearing_types_languages", "hearing_types_languagesfactory");
                    $this->hearing_types_languages = $this->hearing_types_languagesfactory->get_instance();
                    $data["types"] = $this->hearing_types_languages->load_list_per_language();
                    $data["hijri_calendar_enabled"] = $hijri_calendar_enabled;
                    $this->load->model("stage_status_language", "stage_status_languagefactory");
                    $this->stage_status_language = $this->stage_status_languagefactory->get_instance();
                    $data["stage_statuses"] = $this->stage_status_language->load_list_per_language();
                    $this->legal_case->fetch($legal_case_id);
                    $data["judgmentValue"] = $this->legal_case->get_field("judgmentValue");
                    $data["hasAccessToVerify"] = true;
                    $data["verification_process_enabled"] = false;
                    if (isset($systemPreferences["AllowFeatureHearingVerificationProcess"]) && $systemPreferences["AllowFeatureHearingVerificationProcess"] == "yes") {
                        $data["verification_process_enabled"] = true;
                        if (isset($systemPreferences["HearingVerificationProcessUserGroups"]) && $systemPreferences["HearingVerificationProcessUserGroups"]) {
                            $hearingVerificationUserGroups = explode(", ", $systemPreferences["HearingVerificationProcessUserGroups"]);
                            $data["hasAccessToVerify"] = in_array($this->session->userdata("AUTH_user_group_id"), $hearingVerificationUserGroups);
                        }
                    }
                    $response["html"] = $this->load->view("cases/hearings/form", $data, true);
                }
                $this->output->set_content_type("application/json")->set_output(json_encode($response));
                break;
            case "hearingSubmitVerifiedSummary":
                $hearing_id = $this->input->post("id");
                if ($this->legal_case_hearing->fetch($hearing_id)) {
                    if (!$this->input->post("summary")) {
                        $response["validationErrors"]["summary"] = $this->lang->line("hearing_verify_screen_required_fields");
                    } else {
                        if (!$this->input->post("summaryToClient")) {
                            $response["validationErrors"]["summaryToClient"] = $this->lang->line("hearing_verify_screen_required_fields");
                        } else {
                            $this->legal_case_hearing->set_field("verifiedSummary", "1");
                            $this->legal_case_hearing->set_field("summary", $this->input->post("summary"));
                            $this->legal_case_hearing->set_field("summaryToClient", $this->input->post("summaryToClient"));
                            $this->legal_case_hearing->set_field("judgment", $this->input->post("judgment"));
                            $response["result"] = $this->legal_case_hearing->update();
                            if ($response["result"]) {

                                $this->legal_case->fetch($this->legal_case_hearing->get_field("legal_case_id"));
                                $hearing_assignees = $this->legal_case_hearing->load_related_hearing_lawyers($this->legal_case_hearing->get_field("id"));
                                if (!empty($hearing_assignees)) {
                                    $to_names_arr = [];
                                    $to_emails_arr = [];
                                    foreach ($hearing_assignees as $hearing_assignee) {
                                        $to_names_arr[] = $hearing_assignee["name"];
                                        $to_emails_arr[] = $hearing_assignee["email"];
                                    }
                                    $to_names = implode(", ", $to_names_arr);
                                    $notifications_emails = $this->email_notification_scheme->get_emails("hearing_verify_summary", $this->legal_case->get("_table"), ["id" => $this->legal_case_hearing->get_field("legal_case_id"), "lawyers" => array_keys($hearing_assignees)]);
                                    extract($notifications_emails);
                                    $this->load->library("system_notification");
                                    $this->load->library("email_notifications");
                                    $notifications_data = ["to" => $to_emails, "toIds" => array_keys($hearing_assignees), "objectName" => "hearing_verify_summary", "cc" => $cc_emails, "ccIds" => [$this->legal_case->get_field("user_id")], "object" => "hearing_verify_summary", "object_id" => $this->legal_case->get_field("id"), "caseId" => $this->legal_case->get_field("id"), "objectModelCode" => $this->legal_case->get("modelCode"), "targetUser" => $this->legal_case->get_field("user_id"), "lawyers" => $to_names, "actionMaker" => $this->session->userdata("AUTH_userProfileName"), "caseSubject" => $this->legal_case->get("modelCode") . $this->legal_case->get_field("id") . " - " . $this->legal_case->get_field("subject"), "hearingID" => $this->legal_case_hearing->get("modelCode") . $this->legal_case_hearing->get_field("id")];
                                    $notifications_data["legal_case_object_id"] = $this->legal_case->get("modelCode") . $legal_case_id;
                                    $notifications_data["comments"] = $this->legal_case_hearing->get_field("comments");
                                    $notifications_data["summary"] = $this->legal_case_hearing->get_field("summary");
                                    $notifications_data["summaryToClient"] = $this->legal_case_hearing->get_field("summaryToClient");
                                    $notifications_data["judgment"] = $this->legal_case_hearing->get_field("judgment");
                                    $notifications_data["modified_by"] = $this->legal_case_hearing->get_field("modifiedBy");
                                    $notifications_data["date"] = $this->legal_case_hearing->get_field("startDate") . " " . $this->legal_case_hearing->get_field("startTime");
                                    //  $notifications_data["hearingID"] = $this->legal_case_hearing->get("modelCode") . $this->legal_case_hearing->get_field("id")." ".$court_activity_purpose;
                                    $notifications_data["hearingID"] =$court_activity_purpose;
                                    $this->system_notification->notification_add($notifications_data);
                                    $this->email_notifications->notify($notifications_data);
                                }
                                $this->legal_case_litigation_detail->update_stage_order($this->legal_case_hearing->get_field("stage"));
                            }
                            $response["caseId"] = $this->legal_case_hearing->get_field("legal_case_id");
                        }
                    }
                } else {
                    $response["result"] = false;
                    $response["error"] = $this->lang->line("invalid_request");
                }
                $this->output->set_content_type("application/json")->set_output(json_encode($response));
                break;
            case "hearingVerifySummaryWindow":
                $hearing_id = $this->input->post("hearingId");
                if (!$this->legal_case_hearing->fetch($hearing_id) || !$this->legal_case->fetch($legal_case_id) || $this->legal_case_hearing->get_field("verifiedSummary") == "1") {
                    $response["error"] = $this->lang->line("invalid_request");
                } else {
                    $data["hasAccessToVerify"] = true;
                    if (isset($systemPreferences["AllowFeatureHearingVerificationProcess"]) && $systemPreferences["AllowFeatureHearingVerificationProcess"] == "yes" && isset($systemPreferences["HearingVerificationProcessUserGroups"]) && $systemPreferences["HearingVerificationProcessUserGroups"]) {
                        $hearingVerificationUserGroups = explode(", ", $systemPreferences["HearingVerificationProcessUserGroups"]);
                        if (!in_array($this->session->userdata("AUTH_user_group_id"), $hearingVerificationUserGroups)) {
                            $response["error"] = $this->lang->line("hearing_verification_process_disabled_verify");
                            $this->output->set_content_type("application/json")->set_output(json_encode($response));
                        }
                    }
                    $data["title"] = $this->lang->line("verify_hearing");
                    $data["id"] = $hearing_id;
                    if ($this->legal_case_hearing->get_field("stage")) {
                        $response["stage_html"] = $this->return_litigation_stage_html($legal_case_id, $this->legal_case_hearing->get_field("stage"), $hearing_id);
                    }
                    $data["litigation_data"] = $this->legal_case->load_case_details($legal_case_id);
                    $data["hearing_data"] = $this->legal_case_hearing->get_fields();
                    $hearingLawyers = $this->legal_case_hearing->load_extra_users_data($hearing_id);
                    $data["hearingLawyersUsers"] = isset($hearingLawyers[0]) ? $hearingLawyers[0] : [];
                    $response["html"] = $this->load->view("cases/hearings/form_verify_summary", $data, true);
                }
                $this->output->set_content_type("application/json")->set_output(json_encode($response));
                break;
            case "hearingSetJudgment":
                $hearing_id = $this->input->post("hearingId");
                if (!$this->legal_case_hearing->fetch($hearing_id) || !$this->legal_case->fetch($legal_case_id)) {
                    $response["error"] = $this->lang->line("invalid_request");
                } else {
                    $data["judgment"] = $this->legal_case_hearing->get_field("judgment");
                    if ($this->legal_case_hearing->get_field("stage")) {
                        if (!$this->legal_case_litigation_detail->fetch($this->legal_case_hearing->get_field("stage"))) {
                            $response["error"] = $this->lang->line("invalid_request");
                        } else {
                            $data["stage_status"] = $this->legal_case_litigation_detail->get_field("status");
                            $data["id"] = $hearing_id;
                            $data["sentence_date"] = $this->legal_case_litigation_detail->get_field("sentenceDate");
                            $this->load->model("stage_status_language", "stage_status_languagefactory");
                            $this->stage_status_language = $this->stage_status_languagefactory->get_instance();
                            $data["stage_statuses"] = $this->stage_status_language->load_list_per_language();
                            $data["title"] = $this->lang->line("set_judgment");
                            $systemPreferences = $this->session->userdata("systemPreferences");
                            $data["hijri_calendar_enabled"] = isset($systemPreferences["hijriCalendarFeature"]) && $systemPreferences["hijriCalendarFeature"] ? $systemPreferences["hijriCalendarFeature"] : 0;
                            $data["systemPreferences"] = $systemPreferences;
                            $this->legal_case->fetch($legal_case_id);
                            $data["judgmentValue"] = $this->legal_case->get_field("judgmentValue");
                            $data["reason_of_win_or_lose"] = $this->legal_case_hearing->get_field("reason_of_win_or_lose");
                            $data["hearing_outcome"] = $this->legal_case_hearing->get_field("hearing_outcome");
                            $data["hearing_outcome_list"] = array_combine($this->legal_case_hearing->get("hearingOutcomeValues"), [$this->lang->line("none"), $this->lang->line("won"), $this->lang->line("lost")]);
                            $this->load->model("hearing_outcome_reasons_language", "hearing_outcome_reasons_languagefactory");
                            $this->hearing_outcome_reasons_language = $this->hearing_outcome_reasons_languagefactory->get_instance();
                            $data["reason_of_win_or_lose_list"] = $this->hearing_outcome_reasons_language->load_list_per_language();
                            $response["html"] = $this->load->view("cases/hearings/form_judgment", $data, true);
                        }
                    } else {
                        $response["error"] = $this->lang->line("hearing_stage_not_set");
                    }
                }
                $this->output->set_content_type("application/json")->set_output(json_encode($response));
                break;
            case "hearingSubmitJudgment":
                $hearing_id = $this->input->post("id");
                if ($this->legal_case_hearing->fetch($hearing_id)) {
                    $judgment_value = "";
                    if (is_numeric($this->input->post("judgmentValue")) || $this->input->post("judgmentValue") == "") {
                        $judgment_value = floor((double)$this->input->post("judgmentValue") * 100) / 100;
                    }
                    if (!$this->input->post("sentenceDate")) {
                        $response["validationErrors"]["sentenceDate"] = $this->lang->line("cannot_be_blank_rule");
                    } else {
                        if ($this->input->post("judgmentValue") && $judgment_value === "") {
                            $response["validationErrors"]["judgmentValue"] = sprintf($this->lang->line("is_numeric_rule"), $this->lang->line("judgmentValue"));
                        } else {
                            $this->legal_case_hearing->set_field("judged", "yes");
                            $this->legal_case_hearing->set_field("judgment", $this->input->post("judgment"));
                            $this->legal_case_hearing->set_field("hearing_outcome", $this->input->post("hearing_outcome"));
                            $this->legal_case_hearing->set_field("reason_of_win_or_lose", $this->input->post("reason_of_win_or_lose"));
                            if ($this->legal_case_hearing->update() && $this->legal_case_hearing->get_field("stage")) {
                                $this->legal_case_litigation_detail->fetch($this->legal_case_hearing->get_field("stage"));
                                $old_status = $this->legal_case_litigation_detail->get_field("status");
                                $this->legal_case_litigation_detail->set_field("status", $this->input->post("stage_status"));
                                if (isset($systemPreferences["hijriCalendarFeature"]) && $systemPreferences["hijriCalendarFeature"]) {
                                    $_POST["sentenceDate"] = hijriToGregorian($this->input->post("sentenceDate"));
                                }
                                $this->legal_case_litigation_detail->set_field("sentenceDate", $this->input->post("sentenceDate"));
                                if ($this->legal_case_litigation_detail->update() && $this->input->post("stage_status") && $old_status != $this->input->post("stage_status")) {
                                    $this->load->model("litigation_stage_status_history");
                                    $this->litigation_stage_status_history->set_field("litigation_stage", $this->legal_case_hearing->get_field("stage"));
                                    $this->litigation_stage_status_history->set_field("status", $this->input->post("stage_status"));
                                    $this->litigation_stage_status_history->set_field("action_maker", $this->is_auth->get_user_id());
                                    $this->litigation_stage_status_history->set_field("movedOn", date("Y-m-d H:i:s", time()));
                                    $this->litigation_stage_status_history->insert();
                                }
                            }
                            if (0 < $judgment_value) {
                                $this->legal_case->fetch($legal_case_id);
                                $this->legal_case->set_field("judgmentValue", $judgment_value ?? "0.00");
                                $this->legal_case->update();
                            }
                            $response["result"] = true;
                            $response["judged_label"] = $this->lang->line("yes");
                            $response["caseId"] = $this->legal_case_hearing->get_field("legal_case_id");
                        }
                    }
                } else {
                    $response["result"] = false;
                    $response["error"] = $this->lang->line("invalid_request");
                }
                $this->output->set_content_type("application/json")->set_output(json_encode($response));
                break;
            case "submitHearingForm":
                $result = true;
                $hearing_id = $this->input->post("id");
                //get court activity type name
                $this->load->model("hearing_types_languages", "hearing_types_languagesfactory");
                $this->hearing_types_languages = $this->hearing_types_languagesfactory->get_instance();
                $court_activity_purpose = $this->input->post("type") ? $this->hearing_types_languages->load_type_per_language($this->input->post("type")) : "";


                if ($hearing_id == 0 && $this->request_can_cause_insufficient_anti_automation("Hearing")) {
                    $result = false;
                    $response["error"] = $this->lang->line("insufficient_anti_automation_message");
                } else {
                    if ($hijri_calendar_enabled) {
                        $_POST["startDate"] = hijriToGregorian($this->input->post("startDate"));
                        if ($this->input->post("postponedDate") && $this->input->post("postponedDate")) {
                            $_POST["postponedDate"] = hijriToGregorian($this->input->post("postponedDate"));
                        }
                    }
                    if (0 < $hearing_id) {
                        $newPostponedDate = $this->input->post("postponedDate") ? date("Y-m-d H:i:s", strtotime($this->input->post("postponedDate") . " " . $this->input->post("postponedTime"))) : NULL;
                        $newStartDate = date("Y-m-d H:i:s", strtotime($this->input->post("startDate") . " " . $this->input->post("startTime")));
                        if (strcmp($this->input->post("postponedDate"), "") && $newPostponedDate <= $newStartDate) {
                            $result = false;
                            $response["message"] = ["type" => "error", "text" => $this->lang->line("potsponed_date_greater_than_start_date")];
                        } else {
                            if (strcmp($this->input->post("postponedDate"), "") && $newStartDate < $newPostponedDate) {
                                $response["postponed"] = true;
                            }
                        }
                        $this->legal_case_hearing->fetch($hearing_id);
                        $old_summary = $this->legal_case_hearing->get_field("summary");
                        $mv_data = ["startDate" => ["old" => $this->legal_case_hearing->get_field("startDate"), "new" => $_POST["startDate"]], "startTime" => ["old" => $this->legal_case_hearing->get_field("startTime"), "new" => $_POST["startTime"] . ":00"]];
                        $this->legal_case_hearing->set_fields($this->input->post(NULL));
                    } else {
                        $old_summary = "";
                        $this->legal_case_hearing->set_fields($this->input->post(NULL));
                        $this->legal_case_hearing->set_field("is_deleted", 0);
                        $this->legal_case_hearing->set_field("verifiedSummary", 0);
                        $this->legal_case_hearing->set_field("judgment", NULL);
                        $this->legal_case_hearing->set_field("judged", "no");
                    }
                    $lookup_errors = $this->legal_case_hearing->get_lookup_validation_errors($this->legal_case_hearing->get("case_lookup_inputs_validation"), $this->input->post(NULL));
                    $assignees_lookup_errors = $this->legal_case_hearing->get_lookup_validation_errors($this->legal_case_hearing->get("assignees_lookup_inputs_validation"), $this->input->post(NULL));
                    if ($this->legal_case_hearing->validate() && !$lookup_errors && !$assignees_lookup_errors) {
                        $this->input->post("type");
                        $this->input->post("type") == 0 ? $this->legal_case_hearing->set_field("type", NULL) : $this->legal_case_hearing->set_field("type", $this->input->post("type"));
                        $result = 0 < $hearing_id ? $this->legal_case_hearing->update() : $this->legal_case_hearing->insert();
                    } else {
                        $result = false;
                    }
                    $systemPreferences = $this->session->userdata("systemPreferences");
                    if ($result) {
                        if ($hearing_id == 0) {
                            $this->increase_count_for_anti_automation_prevention("Hearing");
                        }
                        $this->legal_case_hearing->update_recent_ids($hearing_id, "hearings");
                        $hearingLawyers = $this->input->post("Hearing_Lawyers");
                        if (isset($systemPreferences["AllowFeatureHearingVerificationProcess"]) && $systemPreferences["AllowFeatureHearingVerificationProcess"] == "yes" && isset($systemPreferences["HearingVerificationProcessUserGroups"]) && $systemPreferences["HearingVerificationProcessUserGroups"]) {
                            $hearingVerificationUserGroups = explode(", ", $systemPreferences["HearingVerificationProcessUserGroups"]);
                            if (0 < $hearing_id && $old_summary != $this->input->post("summary") || $hearing_id <= 0 && $this->legal_case_hearing->get_field("summary")) {

                                $notifications_emails = $this->email_notification_scheme->get_emails("hearing_save_summary_to_notify_managers", $this->legal_case_hearing->get("_table"), ["id" => $this->legal_case_hearing->get_field("id"), "user_groups" => $hearingVerificationUserGroups]);
                                extract($notifications_emails);
                                $toIds = $this->user->get_users_ids_by_group_id($hearingVerificationUserGroups);
                                if (!empty($toIds)) {
                                    $this->load->library("system_notification");
                                    $this->load->library("email_notifications");
                                    $this->legal_case->fetch($this->legal_case_hearing->get_field("legal_case_id"));
                                    $notifications_data = ["to" => $to_emails, "toIds" => $toIds, "objectName" => "hearing_save_summary_to_notify_managers", "cc" => [], "ccIds" => [], "object" => "hearing_save_summary_to_notify_managers", "object_id" => $this->legal_case->get_field("id"), "caseId" => $this->legal_case->get_field("id"), "objectModelCode" => $this->legal_case->get("modelCode"), "targetUser" => $this->legal_case->get_field("user_id"), "actionMaker" => $this->session->userdata("AUTH_userProfileName"), "modified_by" => $this->legal_case_hearing->get_field("modifiedBy"), "caseSubject" => $this->legal_case->get("modelCode") . $this->legal_case->get_field("id") . " - " . $this->legal_case->get_field("subject"), "hearingID" => $this->legal_case_hearing->get("modelCode") . $this->legal_case_hearing->get_field("id")];
                                    $notifications_data["legal_case_object_id"] = $this->legal_case->get("modelCode") . $legal_case_id;
                                    $notifications_data["comments"] = $this->legal_case_hearing->get_field("comments");
                                    $notifications_data["summary"] = $this->legal_case_hearing->get_field("summary");
                                    $notifications_data["summaryToClient"] = $this->legal_case_hearing->get_field("summaryToClient");
                                    $notifications_data["judgment"] = $this->legal_case_hearing->get_field("judgment");
                                    $notifications_data["modified_by"] = $this->legal_case_hearing->get_field("modifiedBy");
                                    $notifications_data["date"] = $this->legal_case_hearing->get_field("startDate") . " " . $this->legal_case_hearing->get_field("startTime");
                                    // $notifications_data["hearingID"] = $this->legal_case_hearing->get("modelCode") . $this->legal_case_hearing->get_field("id").$court_activity_purpose;
                                    $notifications_data["hearingID"] = $court_activity_purpose;
                                    $notifications_data["lawyers"] = $this->email_notification_scheme->get_user_full_name($hearingLawyers);
                                    $this->system_notification->notification_add($notifications_data);
                                    $this->email_notifications->notify($notifications_data);
                                }
                            }
                        }
                        if ($hearing_id && $this->legal_case_hearing->get_field("stage")) {
                            $this->call_materialized_view_triggers("hearing_stage", $this->legal_case_hearing->get_field("stage"), $mv_data);
                        }
                        $this->legal_case_litigation_detail->update_stage_order($this->legal_case_hearing->get_field("stage"));
                        $this->load->model("time_types_languages", "time_types_languagesfactory");
                        $this->time_types_languages = $this->time_types_languagesfactory->get_instance();
                        $this->time_types_languages->fetch(["name" => "Attending", "language_id" => 1]);
                        $time_type_attending_id = $this->time_types_languages->get_field("type");
                        if (!$time_type_attending_id) {
                            $this->load->model("time_type");
                            $this->time_type->reset_fields();
                            $this->time_type->insert();
                            $time_type_attending_id = $this->time_type->get_field("id");
                            $this->load->model("language");
                            $languages = $this->language->load_all();
                            foreach ($languages as $language) {
                                $this->time_types_languages->reset_fields();
                                $attending_label = $language["id"] == 1 ? "Attending" : ($language["id"] == 2 ? "حضور" : ($language["id"] == 3 ? "Assister" : ($language["id"] == 4 ? "Asistiendo" : "Attending")));
                                $this->time_types_languages->set_field("type", $time_type_attending_id);
                                $this->time_types_languages->set_field("language_id", $language["id"]);
                                $this->time_types_languages->set_field("name", $attending_label);
                                $this->time_types_languages->insert();
                            }
                        }
                        if ($this->input->post("timeSpent") && !empty($hearingLawyers)) {
                            $this->load->library("TimeMask");
                            $this->load->model("user_activity_log", "user_activity_logfactory");
                            $this->user_activity_log = $this->user_activity_logfactory->get_instance();
                            $_POST["time_type_id"] = $time_type_attending_id;
                            $_POST["logDate"] = $this->legal_case_hearing->get_field("startDate");
                            $event_id = $this->input->post("task_id");
                            $_POST["task_id"] = NULL;
                            $effective_effort_value = $this->timemask->humanReadableToHours($this->input->post("timeSpent"));
                            foreach ($hearingLawyers as $time_log_user) {
                                $_POST["user_id"] = $time_log_user;
                                $this->user_activity_log->save(NULL, $effective_effort_value, $systemPreferences["roundUpTimeLogs"] ?? 0, false, true);
                            }
                            $_POST["task_id"] = $event_id;
                        }
                        $ordinal_hearing_id = $this->input->post("id");
                        $_POST["id"] = $this->legal_case_hearing->get_field("id");
                        $extraData = [];
                        $extraData["users"] = ["legal_case_hearing_id" => $this->input->post("id"), "users" => $hearingLawyers];
                        $this->legal_case_hearing->update_extra_users_fields($extraData);
                        $this->legal_case_hearing->update();
                        $_POST["old_id"] = "";
                        if (0 < $hearing_id && $newStartDate < $newPostponedDate) {
                            $_POST["startDate"] = $this->input->post("postponedDate");
                            $_POST["startTime"] = $this->input->post("postponedTime");
                            if ($this->input->post("add_new_hearing") && !strcmp($this->input->post("add_new_hearing"), "yes")) {
                                $_POST["id"] = "";
                                $_POST["postponedDate"] = "";
                                $_POST["postponedTime"] = "";
                                $this->legal_case_hearing->set_fields($this->input->post(NULL));
                                $this->legal_case_hearing->set_field("is_deleted", 0);
                                $this->legal_case_hearing->set_field("verifiedSummary", 0);
                                if (!$systemPreferences["copySummaryAndCommentsToPostponedHearing"]) {
                                    $this->legal_case_hearing->set_field("summary", NULL);
                                    $this->legal_case_hearing->set_field("comments", NULL);
                                }
                                $result = $this->legal_case_hearing->insert();
                                if ($result) {
                                    $_POST["id"] = $this->legal_case_hearing->get_field("id");
                                    $extraData = [];
                                    $extraData["users"] = ["legal_case_hearing_id" => $this->input->post("id"), "users" => $hearingLawyers];
                                    $this->legal_case_hearing->update_extra_users_fields($extraData);
                                    $this->legal_case_hearing->update();
                                    $_POST["old_id"] = $hearing_id;
                                } else {
                                    $response["validationErrors"] = $this->legal_case_hearing->get("validationErrors");
                                    if (isset($response["validationErrors"]["startDate"])) {
                                        $response["validationErrors"]["postponedDate"] = $response["validationErrors"]["startDate"];
                                        unset($response["validationErrors"]["startDate"]);
                                    }
                                    if (isset($response["validationErrors"]["startTime"])) {
                                        $response["validationErrors"]["postponedTime"] = $response["validationErrors"]["startTime"];
                                        unset($response["validationErrors"]["startTime"]);
                                    }
                                }
                            }
                        }
                        $legal_case_id = $this->legal_case_hearing->get_field("legal_case_id");
                        $this->legal_case->fetch($legal_case_id);
                        $ability_set_latest_development = $system_preference["DefaultValues"]["abilitySetLatestDevelopment"];
                        if ($ability_set_latest_development) {
                            $post_latest_development = $this->input->post("latest_development");
                            $this->legal_case->set_field("latest_development", $post_latest_development);
                            $this->legal_case->update();
                        }
                        $assignee = $this->legal_case->get_field("user_id");
                        $object = "add_hearing";
                        $notifications_data["object"] = $object;
                        $notifications_data["caseSubject"] = $this->legal_case->get_field("subject");
                        $notifications_data["object_id"] = $legal_case_id;
                        $notifications_data["objectName"] = "hearing";
                        $sendEmailFlag = $this->input->post("send_notifications_email");
                        if ($sendEmailFlag) {
                            $startDate = $this->legal_case_hearing->get_field("startDate");
                            $startTime = is_null($this->legal_case_hearing->get_field("startTime")) ? "14:00" : $this->legal_case_hearing->get_field("startTime");
                            $timezone = isset($systemPreferences["systemTimezone"]) && $systemPreferences["systemTimezone"] ? $systemPreferences["systemTimezone"] : $this->config->item("default_timezone");
                            $this->load->model("language");
                            $languages = $this->language->load_all();
                            $ical_languages = [];
                            foreach ($languages as $value) {
                                $ical_languages[$value["fullName"]] = strtoupper($value["name"]);
                            }
                            $this->load->helper("ical");
                            $eventData = ["startDateTime" => $startDate . " " . $startTime, "endDateTime" => "", "userEmail" => $this->is_auth->get_email_address(), "timezone" => $timezone, "meetingLocation" => "", "summary" => $this->legal_case_hearing->get_field("summary"), "subject" => $this->legal_case_hearing->get_field("summary"), "language" => $this->session->userdata("AUTH_language"), "languages" => $ical_languages];
                            $attachments[0]["path"] = create_ical_event($this->config->item("files_path"), $eventData);
                            $attachments[0]["name"] = "event";
                            $notifications_data["attachments"] = $attachments;
                        }
                        if (is_array($hearingLawyers) && count($hearingLawyers)) {
                            $_POST["legalCaseSubject"] = $this->legal_case->get_field("subject");
                            $this->load->model("user", "userfactory");
                            $this->user = $this->userfactory->get_instance();
                            foreach ($hearingLawyers as $userId) {
                                $this->user->fetch($userId);
                                $userIds[] = $userId;
                                $userEmails[] = $this->user->get_field("email");
                            }
                            if ($assignee) {
                                $attachments = [];
                                $this->user->fetch($assignee);
                                $assigneeEmail = $this->user->get_field("email");
                                $this->load->library("system_notification");
                                $notifications_data["to"] = $userEmails;
                                $notifications_data["toIds"] = $userIds;
                                $notifications_data["cc"] = [$assigneeEmail];
                                $notifications_data["ccIds"] = [$assignee];
                                $notifications_data["targetUser"] = $assignee;
                                $notifications_data["modified_by"] = $this->legal_case_hearing->get_field("modifiedBy");
                                $notifications_data["objectModelCode"] = $this->legal_case->get("modelCode");
                                $this->system_notification->notification_add($notifications_data);
                            }

                            $this->load->model("court");
                            $court_name = $this->input->post("stage") && $this->legal_case_litigation_detail->fetch($this->input->post("stage")) && $this->legal_case_litigation_detail->get_field("court_id") && $this->court->fetch($this->legal_case_litigation_detail->get_field("court_id")) ? " - " . $this->lang->line("court") . ": " . $this->court->get_field("name") : "";
                            // $hearingID = $this->legal_case_hearing->get("modelCode") . $this->legal_case_hearing->get_field("id")." - ".$court_activity_purpose;
                            $hearingID =$court_activity_purpose;
                            $matterID = $this->legal_case->get("modelCode") . $this->input->post("legal_case_id");
                            //  $summaryText =  ($this->is_auth->is_layout_rtl() ? $matterID : $hearingID) . " - " . ($this->is_auth->is_layout_rtl() ? $hearingID : $matterID) . " " . mb_substr($this->input->post("legalCaseSubject"), 0, 30) . $court_name;
                            $summaryText =  ($this->is_auth->is_layout_rtl() ? $matterID : $hearingID) . " - " . mb_substr($this->legal_case->get_field("subject"), 0, 30) . $court_name;
                            $_POST["summaryText"] = $summaryText;
                            if (0 < $hearing_id) {
                                if (isset($mv_data) && (strtotime($mv_data["startDate"]["old"]) != strtotime($mv_data["startDate"]["new"]) || strtotime($mv_data["startTime"]["old"]) != strtotime($mv_data["startTime"]["new"]))) {
                                    $reminders_results = $this->hearings_inject_reminders($this->input->post(NULL), "dismissReminders");
                                }
                            } else {
                                $reminders_results = $this->hearings_inject_reminders($this->input->post(NULL), false);
                            }
                            if (isset($reminders_results) && $reminders_results["result"]) {
                                $response["message"] = ["type" => "success", "text" => $this->lang->line("reminder_added")];
                            } else {
                                if (isset($reminders_results["message"])) {
                                    $response["message"] = $reminders_results["message"];
                                }
                            }
                            $this->hearings_inject_calendar_events($this->input->post(NULL), $hearingLawyers, 0 < $hearing_id ? false : true);
                        }
                        if ($sendEmailFlag) {
                            $this->load->library("email_notifications");

                            $this->load->model("user_profile");
                            $model = $this->legal_case->get("_table");
                            $notifications_emails = $this->email_notification_scheme->get_emails($object, $model, ["id" => $legal_case_id, "lawyers" => $hearingLawyers]);
                            extract($notifications_emails);
                            $notifications_data["to"] = $to_emails;
                            $notifications_data["cc"] = $cc_emails;
                            $notifications_data["legal_case_object_id"] = $this->legal_case->get("modelCode") . $legal_case_id;
                            $notifications_data["comments"] = $this->legal_case_hearing->get_field("comments");
                            $notifications_data["summary"] = $this->legal_case_hearing->get_field("summary");
                            $notifications_data["summaryToClient"] = $this->legal_case_hearing->get_field("summaryToClient");
                            $notifications_data["judgment"] = $this->legal_case_hearing->get_field("judgment");
                            $notifications_data["modified_by"] = $this->legal_case_hearing->get_field("modifiedBy");
                            $notifications_data["date"] = $this->legal_case_hearing->get_field("startDate") . " " . $this->legal_case_hearing->get_field("startTime");
                            //$notifications_data["hearingID"] = $this->legal_case_hearing->get("modelCode") . $this->legal_case_hearing->get_field("id")." - ". $court_activity_purpose;//for rmail
                            $notifications_data["hearingID"] =  $court_activity_purpose;//for rmail
                            $notifications_data["lawyers"] = $this->email_notification_scheme->get_user_full_name($hearingLawyers);
                            $notifications_data["fromLoggedUser"] = $this->is_auth->get_fullname();
                            $this->email_notifications->notify($notifications_data);
                        }
                        $this->load->model("hearing_document", "hearing_documentfactory");
                        $this->hearing_document = $this->hearing_documentfactory->get_instance();
                        $failed_uploads_count = 0;
                        foreach ($_FILES as $file_key => $file) {
                            if ($file["error"] != 4) {
                                $upload_response = $this->dms->upload_file(["module" => "case", "module_record_id" => $legal_case_id, "container_name" => "Hearings", "upload_key" => $file_key]);
                                if (!$upload_response["status"]) {
                                    $failed_uploads_count++;
                                } else {
                                    $this->hearing_document->set_field("hearing", !empty($ordinal_hearing_id) ? $ordinal_hearing_id : $this->legal_case_hearing->get_field("id"));
                                    $this->hearing_document->set_field("document", $upload_response["file"]["id"]);
                                    if (!$this->hearing_document->insert()) {
                                        $this->dms->delete_document($upload_response["file"]["module"], $upload_response["file"]["id"]);
                                        $failed_uploads_count++;
                                    }
                                    $this->hearing_document->reset_fields();
                                }
                            }
                        }
                        if (0 < $failed_uploads_count) {
                            $response["validationErrors"]["files"] = sprintf($this->lang->line("files_were_not_uploaded"), $failed_uploads_count);
                        }
                    } else {
                        $response["validationErrors"] = $this->legal_case_hearing->get_validation_errors($lookup_errors) + ($assignees_lookup_errors ? $assignees_lookup_errors : []);
                    }
                }
                $response["result"] = $result;
                $response["id"] = $this->legal_case_hearing->get_field("id");
                $response["case_id"] = $this->legal_case_hearing->get_field("legal_case_id");
                $response["triggerCreateTask"] = $this->input->post("trigger-create-task");
                $response["triggerCreateAnother"] = $this->input->post("trigger-create-another");
                $this->output->set_content_type("application/json")->set_output(json_encode($response));
                break;
            case "fetchRelatedCaseData":
                $legalCase_id = $this->input->post("caseId");
                $this->legal_case->fetch($legalCase_id);
                $assignee = $this->legal_case->get_field("user_id");
                if ($assignee) {
                    $this->load->model("user_profile");
                    $this->user_profile->fetch(["user_id" => $this->legal_case->get_field("user_id")]);
                    $response["hearingLawyers"] = [$this->user_profile->get_field("user_id") => $this->user_profile->get_field("firstName") . " " . $this->user_profile->get_field("lastName")];
                } else {
                    $response["hearingLawyers"] = [];
                }
                break;
        }
        $response["judgmentValue"] = $this->legal_case->get_field("judgmentValue");
        $this->output->set_content_type("application/json")->set_output(json_encode($response));


    }
    private function hearings_inject_reminders($hearingData, $mode)
    {
        if (!$hearingData["startDate"]) {
            return ["result" => false, "message" => ["type" => "warning", "text" => $this->lang->line("reminders_date_not_set")]];
        }
        $this->load->model("reminder", "reminderfactory");
        $this->reminder = $this->reminderfactory->get_instance();
        $hearingData["startTime"] = $hearingData["startTime"] ? $hearingData["startTime"] : $this->reminder->get("reminderTimeQuickAddDefaultValue");
        $systemPreferences = $this->session->userdata("systemPreferences");
        $reminderType = $systemPreferences["hearingReminderType"];
        if (!isset($reminderType) || !$reminderType) {
            return ["result" => false, "message" => ["type" => "warning", "text" => $this->lang->line("default_reminder_type_not_set")]];
        }
        $hearingLawyers = $this->input->post("Hearing_Lawyers");
        if ($mode === "dismissReminders") {
            $result = $this->reminder->dismiss_related_reminders_by_related_object_ids($hearingData["id"], "legal_case_hearing_id");
        }
        if (isset($hearingData["old_id"]) && strcmp($hearingData["old_id"], "")) {
            $result = $this->reminder->dismiss_related_reminders_by_related_object_ids($hearingData["old_id"], "legal_case_hearing_id");
        }
        $postDateTime = new DateTime($hearingData["startDate"] . " " . $hearingData["startTime"]);
        $currentDateTime = new DateTime(date("Y-m-d H:i"));
        $interval = date_diff($currentDateTime, $postDateTime);
        if ($currentDateTime <= $postDateTime) {
            foreach ($hearingLawyers as $userId) {
                $this->reminder->reset_fields();
                $this->reminder->set_field("legal_case_hearing_id", $hearingData["id"]);
                $this->reminder->set_field("legal_case_id", $hearingData["legal_case_id"]);
                $this->reminder->set_field("user_id", $userId);
                $this->reminder->set_field("reminder_type_id", $reminderType);
                $this->reminder->set_field("remindDate", $hearingData["startDate"]);
                $this->reminder->set_field("remindTime", $hearingData["startTime"]);
                $this->reminder->set_field("summary", $hearingData["summaryText"]);
                $this->reminder->set_field("status", "Open");
                if (intval($interval->format("%a")) == $systemPreferences["reminderIntervalDate"]) {
                    $this->reminder->set_field("notify_before_time", 1);
                } else {
                    $this->reminder->set_field("notify_before_time", $systemPreferences["reminderIntervalDate"]);
                }
                $this->reminder->set_field("notify_before_time_type", $this->reminder->get("default_notify_me_before_time_type"));
                $this->reminder->set_field("notify_before_type", $this->reminder->get("default_notify_me_before_type"));
                $result = $this->reminder->insert();
            }
        } else {
            $result = false;
        }
        return ["result" => $result];
    }
    private function hearings_inject_calendar_events($hearingData, $hearingLawyers, $addMeeting)
    {
        $this->load->model("event", "eventfactory");
        $this->event = $this->eventfactory->get_instance();
        $result = false;
        if (!$addMeeting && 0 < $hearingData["task_id"]) {
            $this->event->fetch($hearingData["task_id"]);
        }
        if ($this->input->post("stage") && $this->legal_case_litigation_detail->fetch($this->input->post("stage"))) {
            $stage_metadata = $this->legal_case_litigation_detail->load_stage_metadata($hearingData["legal_case_id"], $this->input->post("stage"));
            $this->load->model("hearing_types_languages", "hearing_types_languagesfactory");
            $this->hearing_types_languages = $this->hearing_types_languagesfactory->get_instance();
            $hearing_type = $hearingData["type"] ? $this->hearing_types_languages->load_type_per_language($hearingData["type"]) : "";
            $stage_judges = $this->legal_case_litigation_detail->load_stage_contacts($this->input->post("stage"), "judge");
            $judges = "";
            if (isset($stage_judges["data"]) && !empty($stage_judges["data"])) {
                foreach ($stage_judges["data"] as $judge) {
                    $judges .= $judge["contactName"] . ", ";
                }
                $judges = mb_substr($judges, 0, -2);
            }
            $description = sprintf($this->lang->line("hearing_injected_meeting_description"), $stage_metadata["ext_references"], $hearing_type, $stage_metadata["legal_case_stage_name"], $stage_metadata["court_type"], $stage_metadata["court_degree"], $stage_metadata["court_region"], $stage_metadata["court"], $judges, $hearingData["comments"]);
            $this->event->set_field("description", $description);
        }
        $this->event->set_field("legal_case_id", $hearingData["legal_case_id"]);
        $this->event->set_field("start_date", $hearingData["startDate"]);
        $this->event->set_field("end_date", $hearingData["startDate"]);
        $this->event->set_field("start_time", $hearingData["startTime"]);
        $this->event->set_field("end_time", date("H:i", strtotime($hearingData["startTime"]) + 1800));
        $this->event->set_field("title", mb_substr($hearingData["summaryText"], 0, 255));
        $this->event->set_field("priority", "medium");
        $result = $addMeeting ? $this->event->insert() : (0 < $hearingData["task_id"] ? $this->event->update() : $this->event->insert());
        if ($result) {
            $EventUsers = ["event_id" => $this->event->get_field("id"), "attendees" => $hearingLawyers];
            $this->load->model("event_attendee");
            $this->event_attendee->insert_attendees($EventUsers);
            if (0 < $this->event->get_field("id")) {
                $this->legal_case_hearing->fetch($hearingData["id"]);
                $this->legal_case_hearing->set_field("task_id", $this->event->get_field("id"));
                $this->legal_case_hearing->update();
                $this->event->update_integration_provider_calendar($this->event->get_field("id"), "add");
            }
        }
        return $result;
    }
    public function hearing_send_report_to_client($hearing_id)
    {
        $this->generate_hearing_summary_report($hearing_id);
    }
    public function hearing_prepare_report_to_client($hearing_id)
    {
        $file_id = $this->session->userdata("tmp_file_id");
        $this->session->set_userdata("tmp_file_id", NULL);
        $response = [];
        if ($file_id) {
            $this->document_management_system->fetch($file_id);
            $doc_details = $this->document_management_system->get_fields();
            $core_path = substr(COREPATH, 0, -12);
            $documents_root_direcotry = $this->config->item("files_path") . DIRECTORY_SEPARATOR . "attachments" . DIRECTORY_SEPARATOR;
            $tmp_file = $this->config->item("files_path") . DIRECTORY_SEPARATOR . "tmp" . DIRECTORY_SEPARATOR . "cases" . DIRECTORY_SEPARATOR . rand(1000, 9999) . $doc_details["name"] . "." . $doc_details["extension"];
            $this->document_management_system->reset_fields();
            $this->document_management_system->fetch($doc_details["parent"]);
            $lineage = $this->document_management_system->get_field("lineage");
            $template_dir = $documents_root_direcotry . "cases" . $lineage;
            if (is_file($template_dir . DIRECTORY_SEPARATOR . $file_id)) {
                copy($template_dir . DIRECTORY_SEPARATOR . $file_id, $tmp_file);
                $doc_details["extension"] = "pdf";
                $file_existant_version = $this->document_management_system->get_document_existant_version($doc_details["name"] . "." . $doc_details["extension"], "file", $lineage);
                require_once $core_path . "/application/libraries/phpdocx-premium-12.5-ns/Classes/Phpdocx/Create/CreateDocx.php";
                $file_path = $template_dir . DIRECTORY_SEPARATOR . $doc_details["name"];
                Phpdocx\Utilities\PhpdocxUtilities::parseConfig($core_path . "application/config/phpdocx.ini", true);
                $docx = new Phpdocx\Create\CreateDocx($tmp_file);
                $docx->transformDocument($tmp_file, $file_path . ".pdf", "libreoffice");
                $this->document_management_system->reset_fields();
                $this->document_management_system->set_fields($doc_details);
                $this->document_management_system->set_fields(["id" => NULL, "extension" => $doc_details["extension"], "size" => filesize($file_path . "." . $doc_details["extension"]), "version" => empty($file_existant_version) ? 1 : $file_existant_version["version"] + 1, "createdOn" => date("Y-m-d H:i:s"), "createdBy" => $this->is_auth->get_user_id(), "createdByChannel" => "A4L", "modifiedOn" => date("Y-m-d H:i:s"), "modifiedBy" => $this->is_auth->get_user_id(), "modifiedByChannel" => "A4L"]);
                if ($this->document_management_system->insert()) {
                    $data["file_id"] = $this->document_management_system->get_field("id");
                    $this->document_management_system->set_field("lineage", $lineage . DIRECTORY_SEPARATOR . $this->document_management_system->get_field("id"));
                    if ($this->document_management_system->update() && rename($file_path . "." . $doc_details["extension"], $template_dir . DIRECTORY_SEPARATOR . $this->document_management_system->get_field("id"))) {
                        unlink($tmp_file);
                        $uploaded_file = $this->document_management_system->get_document_full_details(["d.id" => $this->document_management_system->get_field("id")]);
                        if (empty($file_existant_version)) {
                            $response["result"] = true;
                        } else {
                            $this->file_versioning($file_existant_version, $uploaded_file, $response);
                        }
                    }
                }
                if ($response["result"]) {
                    $data["title"] = $this->lang->line("send_report_to_client");
                    $this->load->model("legal_case_hearing", "legal_case_hearingfactory");
                    $this->legal_case_hearing = $this->legal_case_hearingfactory->get_instance();
                    $this->legal_case_hearing->fetch($hearing_id);
                    $legal_case_data = $this->legal_case->load_all_case_data($this->legal_case_hearing->get_field("legal_case_id"));
                    $legal_case_data = $legal_case_data[0];
                    $this->load->model("client");
                    $client_data = $this->client->fetch_client($legal_case_data["client_id"]);
                    $client_name = $client_data["name"] ?? "";
                    $client_name = $client_name ? $client_name . " - " : "";
                    $stage_name = $legal_case_data["caseStage"] ? $legal_case_data["caseStage"] . " - " : "";
                    $system_preferences = $this->session->userdata("systemPreferences");
                    $hearing_date = $this->legal_case_hearing->get_field("startDate");
                    if (isset($system_preferences["hijriCalendarFeature"]) && $system_preferences["hijriCalendarFeature"]) {
                        $hearing_date = gregorianToHijri($hearing_date, "Y-m-d");
                    }
                    $data["default_subject"] = $this->legal_case->get("modelCode") . $legal_case_data["id"] . " - " . $client_name . $stage_name . $hearing_date . " " . date("H:i", strtotime($this->legal_case_hearing->get_field("startTime")));
                    $this->load->model("user", "userfactory");
                    $this->user = $this->userfactory->get_instance();
                    $users_emails = $this->user->load_active_emails();
                    $data["users_emails"] = array_values($users_emails);
                    $arr = [];
                    foreach ($data["users_emails"] as $key => $value) {
                        $arr[$key]["email"] = $value;
                    }
                    if (!empty($arr)) {
                        $data["users_emails"] = $arr;
                    }
                    $data["default_to"] = [];
                    if ($legal_case_data["client_id"]) {
                        if ($client_data["type"] == "Company") {
                            $this->load->model("company", "companyfactory");
                            $this->company = $this->companyfactory->get_instance();
                            $this->company->fetch($client_data["member_id"]);
                            $this->load->model("company_address", "company_addressfactory");
                            $this->company_address = $this->company_addressfactory->get_instance();
                            $company_addresses = $this->company_address->load_company_addresses($client_data["member_id"]);
                            if (!empty($company_addresses)) {
                                foreach ($company_addresses as $address) {
                                    if ($address["email"]) {
                                        if (strpos($address["email"], ";") !== false) {
                                            $sperator_emails = explode(";", $address["email"]);
                                            if (!empty($sperator_emails)) {
                                                foreach ($sperator_emails as $sperator_email) {
                                                    array_push($data["users_emails"], ["email" => $sperator_email]);
                                                    $data["default_to"][] = $sperator_email;
                                                }
                                            }
                                        } else {
                                            array_push($data["users_emails"], ["email" => $address["email"]]);
                                            $data["default_to"][] = $address["email"];
                                        }
                                    }
                                }
                            }
                        } else {
                            $this->load->model("contact", "contactfactory");
                            $this->contact = $this->contactfactory->get_instance();
                            $this->contact->fetch($client_data["member_id"]);
                            $this->load->model("contact_emails");
                            $contact_emails = $this->contact_emails->load_all(["where" => ["contact_id", $client_data["member_id"]]]);
                            if (!empty($contact_emails)) {
                                foreach ($contact_emails as $address) {
                                    if (strpos($address["email"], ";") !== false) {
                                        $sperator_emails = explode(";", $address["email"]);
                                        if (!empty($sperator_emails)) {
                                            foreach ($sperator_emails as $sperator_email) {
                                                array_push($data["users_emails"], ["email" => $sperator_email]);
                                                $data["default_to"][] = $sperator_email;
                                            }
                                        }
                                    } else {
                                        array_push($data["users_emails"], ["email" => $address["email"]]);
                                        $data["default_to"][] = $address["email"];
                                    }
                                }
                            }
                        }
                    }
                    $data["default_email_message"] = $this->legal_case_hearing->get_field("summaryToClient");
                    $data["litigation_id"] = $this->legal_case_hearing->get_field("legal_case_id");
                    $data["attachment"] = ["id" => $this->document_management_system->get_field("id"), "lineage" => $lineage, "name" => $doc_details["name"] . "." . $doc_details["extension"]];
                    $data["mode"] = "preview";
                    $response["html"] = $this->load->view("cases/hearings/form_send_report_to_client", $data, true);
                } else {
                    $response["msg"] = $this->lang->line("failed_to_convert_report_to_pdf");
                }
            }
            $this->output->set_content_type("application/json")->set_output(json_encode($response));
        } else {
            show_404();
        }
    }
    public function hearing_submit_report_to_client($hearing_id)
    {
        $email_data = $this->input->post(NULL);
        if (!empty($email_data)) {
            $this->load->library("email_notifications");
            $email_to = explode(";", $email_data["to"]);
            $email_cc = $email_data["cc"] ? explode(";", $email_data["cc"]) : [];
            $this->document_management_system->fetch($email_data["file_id"]);
            $core_path = substr(COREPATH, 0, -12);
            $documents_root_direcotry = $this->config->item("files_path") . DIRECTORY_SEPARATOR . "attachments" . DIRECTORY_SEPARATOR . "cases";
            $attachment[0]["path"] = $this->config->item("files_path") . DIRECTORY_SEPARATOR . "tmp" . DIRECTORY_SEPARATOR . "cases" . DIRECTORY_SEPARATOR . $this->document_management_system->get_field("name") . "." . $this->document_management_system->get_field("extension");
            $attachment[0]["name"] = $this->document_management_system->get_field("name") . "." . $this->document_management_system->get_field("extension");
            copy($documents_root_direcotry . $this->document_management_system->get_field("lineage"), $attachment[0]["path"]);
            if ($this->email_notifications->send_email($email_to, $email_data["subject"], $email_data["message"], $email_cc, $attachment)) {
                unlink($attachment[0]["path"]);
                $response["result"] = true;
                $response["msg"] = $this->lang->line("email_sent_successfully");
                $this->load->model("legal_case_hearing_client_report_history");
                $this->legal_case_hearing_client_report_history->reset_fields();
                $this->legal_case_hearing_client_report_history->set_field("legal_case_hearing_id", $hearing_id);
                $this->legal_case_hearing_client_report_history->set_field("email_data", json_encode(["to" => $email_to, "cc" => $email_cc, "subject" => $email_data["subject"], "message" => $email_data["message"], "attachment_path" => $this->document_management_system->get_field("lineage")]));
                $this->legal_case_hearing_client_report_history->insert();
                $this->load->model("legal_case_hearing", "legal_case_hearingfactory");
                $this->legal_case_hearing = $this->legal_case_hearingfactory->get_instance();
                $this->legal_case_hearing->fetch($hearing_id);
                $nb_of_sent_emails = $this->legal_case_hearing->get_field("clientReportEmailSent");
                $nb_of_sent_emails++;
                $this->legal_case_hearing->set_field("clientReportEmailSent", $nb_of_sent_emails);
                $this->legal_case_hearing->update();
            } else {
                $response["result"] = false;
                $response["msg"] = $this->lang->line("email_not_Sent");
            }
            $this->output->set_content_type("application/json")->set_output(json_encode($response));
        }
    }
    private function file_versioning($file_existant_version, $uploaded_file, &$response)
    {
        $template_dir = $this->config->item("files_path") . DIRECTORY_SEPARATOR . "attachments" . DIRECTORY_SEPARATOR . "cases";
        $versions_container = [];
        if ($file_existant_version["version"] == 1) {
            $this->document_management_system->reset_fields();
            $this->document_management_system->set_fields(["name" => $uploaded_file["id"] . "_versions", "type" => "folder", "parent" => $uploaded_file["parent"], "module" => $uploaded_file["module"], "module_record_id" => $uploaded_file["module_record_id"], "system_document" => 1, "visible" => 0, "visible_in_cp" => 0, "createdOn" => date("Y-m-d H:i:s"), "createdBy" => $this->is_auth->get_user_id(), "createdByChannel" => "A4L", "modifiedOn" => date("Y-m-d H:i:s"), "modifiedBy" => $this->is_auth->get_user_id(), "modifiedByChannel" => "A4L"]);
            if ($this->document_management_system->insert()) {
                $versions_container_lineage = empty($uploaded_file["parent_lineage"]) ? DIRECTORY_SEPARATOR . $uploaded_file["parent"] : $uploaded_file["parent_lineage"];
                $this->document_management_system->set_field("lineage", $versions_container_lineage . DIRECTORY_SEPARATOR . $this->document_management_system->get_field("id"));
                if ($this->document_management_system->update() && mkdir($template_dir . DIRECTORY_SEPARATOR . $this->document_management_system->get_field("lineage"))) {
                    $versions_container = $this->document_management_system->get_fields();
                }
            }
        } else {
            $this->document_management_system->reset_fields();
            $this->document_management_system->fetch(["name" => $file_existant_version["id"] . "_versions", "system_document" => 1]);
            $this->document_management_system->set_field("name", $uploaded_file["id"] . "_versions");
            if ($this->document_management_system->update()) {
                $versions_container = $this->document_management_system->get_fields();
            }
        }
        if (!empty($versions_container)) {
            $this->document_management_system->reset_fields();
            $this->document_management_system->fetch($file_existant_version["id"]);
            $versioned_file_lineage = $versions_container["lineage"] . DIRECTORY_SEPARATOR . $this->document_management_system->get_field("id");
            $this->document_management_system->set_fields(["parent" => $versions_container["id"], "lineage" => $versioned_file_lineage, "visible" => 0, "visible_in_cp" => 0]);
            if ($this->document_management_system->update() && rename($template_dir . DIRECTORY_SEPARATOR . $file_existant_version["lineage"], $template_dir . DIRECTORY_SEPARATOR . $versioned_file_lineage)) {
                $response["result"] = true;
            }
        }
    }
    public function generate_hearing_summary_report($hearing_id)
    {
        $this->load->model("doc_generator");
        $this->load->model("legal_case_hearing", "legal_case_hearingfactory");
        $this->legal_case_hearing = $this->legal_case_hearingfactory->get_instance();
        $template_folder_path = $this->doc_generator->get_value_by_key("hearing_report_template_folder");
        if (!$template_folder_path || !$hearing_id) {
            $error_msg = !$template_folder_path ? $this->lang->line("hearing_report_template_folder_is_not_specified") : $this->lang->line("invalid_record");
            if ($this->input->is_ajax_request()) {
                $response = ["result" => false, "error" => $error_msg];
                $this->output->set_content_type("application/json")->set_output(json_encode($response));
            } else {
                $this->set_flashmessage("warning", $error_msg);
                redirect("cases/documents/" . $hearing_id);
            }
        } else {
            $this->load->library("dms");
            $template_record = $this->dms->model->get_document_details(["id" => $template_folder_path]);
            $data = $this->legal_case_hearing->load_hearing_details($hearing_id, "en");
            $data["versioning"] = true;
            $data["type"] = "hearing";
            $data["title"] = $this->lang->line("hearing_report_generator");
            if ($this->input->get("action", true) == "read") {
                $hijri_enabled = $this->session->userdata("systemPreferences")["hijriCalendarFeature"];
                $this->load->model("legal_case_opponent", "legal_case_opponentfactory");
                $this->legal_case_opponent = $this->legal_case_opponentfactory->get_instance();
                $data["current_date"] = $hijri_enabled ? gregorianToHijri(date("Y-m-d"), "Y-m-d") : date("Y-m-d");
                $data["hearing_date"] = $hijri_enabled ? gregorianToHijri($data["hearing_date"], "Y-m-d") : $data["hearing_date"];
                if (isset($data["next_hearing"])) {
                    $data["next_hearing_date"] = $hijri_enabled ? gregorianToHijri(mb_substr($data["next_hearing"], 0, 10), "Y-m-d") : mb_substr($data["next_hearing"], 0, 10);
                    $data["next_hearing_time"] = mb_substr($data["next_hearing"], 11, 15);
                    $data["next_hearing_day"] = $this->lang->line(date("l", strtotime($data["next_hearing_date"])));
                }
                $opponent_data = $this->legal_case_opponent->fetch_case_opponents_data($data["legal_case_id"]);
                if (!empty($opponent_data)) {
                    $data["opponents"] = "";
                    foreach ($opponent_data as $key => $opponent) {
                        $opponent_number = $key + 1;
                        $data["opponents"] .= (0 < $key ? ", " : "") . $opponent["opponentName"] . (isset($opponent["position_name"]) ? " - (" . $opponent["position_name"] . ")" : "");
                        $data["opponent" . $opponent_number] = $opponent_data[$key]["opponentName"];
                        $data["opponent" . $opponent_number . "_position"] = $opponent_data[$key]["position_name"];
                        $data["opponent" . $opponent_number . "_foreign_name"] = $opponent_data[$key]["opponentForeignName"];
                    }
                }
            }
            $data["hearing_id"] = $hearing_id;
            $data["file_name_prefix"] = $this->legal_case_hearing->get("modelCode") . $hearing_id . "-" . $this->legal_case->get("modelCode") . $data["legal_case_id"];
            $response = $this->dms->generate_document($template_record, "legal_case", $data["legal_case_id"], "hearing", "case", $data);
            $this->output->set_content_type("application/json")->set_output(json_encode($response));
        }
    }
    public function download_hearing_report($file_name)
    {
        $file_id = $this->session->userdata("tmp_file_id");
        $this->session->set_userdata("tmp_file_id", NULL);
        if ($file_id) {
            $this->download_file($file_id, true);
        } else {
            show_404();
        }
    }
    public function fill_hearing_summary($hearing_id)
    {
        if (!$this->input->is_ajax_request()) {
            redirect("dashboard");
        }
        $response = [];
        $this->load->model("legal_case_hearing", "legal_case_hearingfactory");
        $this->legal_case_hearing = $this->legal_case_hearingfactory->get_instance();
        $this->legal_case_hearing->fetch($hearing_id);
        if ($this->input->post("action") == "return_html") {
            $data = $this->legal_case_hearing->get_fields();
            $response["html"] = $this->load->view("cases/hearings/form_summary", $data, true);
        } else {
            $this->legal_case_hearing->set_field("summary", $this->input->post("summary"));
            $response["result"] = $this->legal_case_hearing->update();
        }
        $this->output->set_content_type("application/json")->set_output(json_encode($response));
    }
    public function convert_case_to_litigation($id = "")
    {
        if (!$this->input->is_ajax_request()) {
            redirect("dashboard");
        }
        $response = [];
        if (!empty($id)) {
            $this->legal_case->fetch($id);
            if ($this->input->post("caseType")) {
                $firstLitigationType = $this->input->post("caseType");
                $this->legal_case->set_field("case_type_id", $firstLitigationType);
            }
            $this->legal_case->set_field("category", "Litigation");
            $this->legal_case->set_field("modifiedByChannel", $this->legal_case->get("webChannel"));
            $workflow_applicable = $this->workflow_status->getWorkflowOfAreaPractice($firstLitigationType, "litigation");
            if (!empty($workflow_applicable)) {
                $this->legal_case->set_field("case_status_id", $this->workflow_status->get_workflow_start_point($workflow_applicable["workflow"]));
                $this->legal_case->set_field("workflow", $workflow_applicable["workflow"]);
            } else {
                $workflow_applicable = $this->workflow_status->getDefaultSystemWorkflow("litigation");
                if (isset($workflow_applicable["workflow"]) && $workflow_applicable["workflow"] != $this->legal_case->get_field("workflow")) {
                    $LegalCaseFields["workflow"] = $workflow_applicable["workflow"] ? $workflow_applicable["workflow"] : "1";
                    $LegalCaseFields["case_status_id"] = ($status_data = $this->workflow_status->get_workflow_start_point($LegalCaseFields["workflow"])) ? $status_data : "1";
                    $this->legal_case->set_field("case_status_id", $LegalCaseFields["case_status_id"]);
                    $this->legal_case->set_field("workflow", $LegalCaseFields["workflow"]);
                }
            }
            $response["status"] = $this->legal_case->update() ? 202 : 101;
        } else {
            $this->load->model("case_type");
            $data["title"] = $this->lang->line("convert_to_litigation");
            $data["Case_Types"] = $this->case_type->load_list(["where" => [["litigation", "yes"], ["isDeleted", 0]], "order_by" => ["name", "asc"]], ["firstLine" => ["" => $this->lang->line("choose_litigation_case_type")]]);
            $response["html"] = $this->load->view("cases/convert_to_litigation", $data, true);
            $response["status"] = true;
        }
        $this->output->set_content_type("application/json")->set_output(json_encode($response));
    }

    public function expenses($id = 0)
    {
        $this->expenses_details($id);
    }
    public function my_expenses($id = 0)
    {
        $this->expenses_details($id, true);
    }
    private function expenses_details($id = 0, $my_expenses = false)
    {
        if ($this->input->post(NULL)) {
            if (!$this->input->is_ajax_request()) {
                redirect("dashboard");
            }
            $sortable = $this->input->post("sort");
            $user_accounts = [];
            if ($my_expenses) {
                $this->load->model("account", "accountfactory");
                $this->account = $this->accountfactory->get_instance();
                $auth_user_id = $this->session->userdata("AUTH_user_id");
                $user_accounts_mapping = $this->account->load_account_user_mapping($auth_user_id);
                if (isset($user_accounts_mapping)) {
                    $user_accounts = array_column($user_accounts_mapping, "accountId");
                }
            }
            $response = $this->legal_case->k_load_all_legal_case_expenses($id, $sortable, $my_expenses, $user_accounts);
            $response["statementCurrency"] = $this->session->userdata("organizationCurrency");
            $this->output->set_content_type("application/json")->set_output(json_encode($response));
        } else {
            $data = [];
            $this->legal_case->fetch($id);
            $site_url = $my_expenses ? "cases/my_expenses/" : "cases/expenses/";
            $data["tabsNLogs"] = $this->get_vertical_case_tabs($id, site_url($site_url));
            $legalCase = $this->legal_case->get_fields();
            $legalCase["Status"] = $this->legal_case->get_case_status($legalCase["case_status_id"]);
            $data["legalCase"] = $legalCase;
            $systemPreferences = $this->session->userdata("systemPreferences");
            $data["systemPreferences"] = $systemPreferences;
            $data["caseId"] = $id;
            $data["category"] = $legalCase["category"] == "Litigation" ? "litigation_" : "matter_";
            $data["my_expenses"] = $my_expenses;
            $this->includes("kendoui/js/kendo.web.min", "js");
            $this->includes("kendoui/styles/kendo.common-bootstrap.min", "css");
            $this->includes("kendoui/styles/kendo.bootstrap.min", "css");
            $this->includes("jquery/form2js", "js");
            $this->includes("jquery/toObject", "js");
            $this->includes("scripts/legal_case_expenses", "js");
            $this->includes("scripts/show_hide_customer_portal", "js");
            if ($this->is_auth->is_layout_rtl()) {
                $this->includes("styles/rtl/fixes", "css");
            }
            $this->load->view("partial/header");
            $this->load->view("cases/expenses", $data);
        }
    }
    public function my_time_logs($id = 0)
    {
        $this->time_logs_details($id, true);
    }
    public function time_logs($id = 0)
    {
        $this->time_logs_details($id);
    }
    private function time_logs_details($id, $my_time_logs = false)
    {
        if ($this->input->post(NULL)) {
            if (!$this->input->is_ajax_request()) {
                redirect("dashboard");
            }
            $sortable = $this->input->post("sort");
            $filter = $this->input->post("filter");
            $organization_id = !$this->input->post("organization_id") ? 0 : $this->input->post("organization_id");
            $only_log_rate = !$this->input->post("only_log_rate") ? 1 : 0;
            $response = $this->legal_case->k_load_all_legal_case_time_tracking($id, $sortable, $filter, $organization_id, $only_log_rate, $my_time_logs);
            $this->output->set_content_type("application/json")->set_output(json_encode($response));
        } else {
            $data = [];
            $this->load->model("user_profile");
            $this->load->model("user_activity_log", "user_activity_logfactory");
            $this->load->model("time_types_languages", "time_types_languagesfactory");
            $this->load->model("case_rate", "case_ratefactory");
            $this->load->model("time_internal_statuses_language", "time_internal_statuses_languagefactory");
            $this->time_types_languages = $this->time_types_languagesfactory->get_instance();
            $this->user_activity_log = $this->user_activity_logfactory->get_instance();
            $this->time_internal_statuses_language = $this->time_internal_statuses_languagefactory->get_instance();
            $this->case_rate = $this->case_ratefactory->get_instance();
            $this->legal_case->fetch($id);
            $site_url = $my_time_logs ? "cases/my_time_logs/" : "cases/time_logs/";
            $data["tabsNLogs"] = $this->get_vertical_case_tabs($id, site_url($site_url));
            $legalCase = $this->legal_case->get_fields();
            $legalCase["Status"] = $this->legal_case->get_case_status($legalCase["case_status_id"]);
            $data["legalCase"] = $legalCase;
            $created_on_profile = $this->user_profile->get_profile_by_id($legalCase["createdBy"]);
            $modified_on_profile = $this->user_profile->get_profile_by_id($legalCase["modifiedBy"]);
            $data["legalCase"]["createdByName"] = $created_on_profile["firstName"] . " " . $created_on_profile["lastName"];
            $data["legalCase"]["modifiedByName"] = $modified_on_profile["firstName"] . " " . $modified_on_profile["lastName"];
            $data["caseId"] = $id;
            $client_data = $this->legal_case->get_case_client($legalCase["id"]);
            $data["clientId"] = $client_data["client_id"];
            $data["clientName"] = $client_data["clientName"];
            $systemPreferences = $this->session->userdata("systemPreferences");
            $data["disableArchivedMatters"] = $systemPreferences["disableArchivedMatters"];
            $data["defaultNewTimeLogStatus"] = $systemPreferences["defaultNewTimeLogStatus"];
            $data["businessWeekDays"] = $systemPreferences["businessWeekEquals"];
            $data["businessDayHours"] = $systemPreferences["businessDayEquals"];
            $data["category"] = $legalCase["category"] == "Litigation" ? "litigation_" : "matter_";
            $data["activityData"] = $this->user_activity_log->get_activity_details();
            $data["time_types"] = $this->time_types_languages->load_list_per_language();
            $data["time_internal_statuses"] = $this->time_internal_statuses_language->load_list_per_language();
            $organizations = $this->case_rate->get_entities();
            $data["entities"] = $this->case_rate->get_pretty_selected_entities($organizations);
            $data["legal_case_id"] = $id;
            $data["has_one_entity"] = 1 >= count($data["entities"]);
            $data["show_user_rate"] = true;
            if (isset($systemPreferences["systemUserRateViewerGroupId"])) {
                $systemUserRateViewerGroupId = explode(", ", $systemPreferences["systemUserRateViewerGroupId"]);
                $data["show_user_rate"] = !in_array($this->session->userdata("AUTH_user_group_id"), $systemUserRateViewerGroupId);
            }
            $data["organization_id"] = $this->user_preference->get_value("organization");
            $data["my_time_logs"] = $my_time_logs;
            $this->includes("kendoui/js/kendo.web.min", "js");
            $this->includes("kendoui/styles/kendo.common-bootstrap.min", "css");
            $this->includes("kendoui/styles/kendo.bootstrap.min", "css");
            $this->includes("jquery/form2js", "js");
            $this->includes("jquery/toObject", "js");
            $this->includes("scripts/legal_case_time_logs", "js");
            $this->includes(app_url("compressed_asset/system_defaults.js?a=" . random_string("alpha")), "js", true);
            $this->includes("jquery/timemask", "js");
            $this->includes("scripts/show_hide_customer_portal", "js");
            if ($this->is_auth->is_layout_rtl()) {
                $this->includes("styles/rtl/fixes", "css");
            }
            $this->load->view("partial/header");
            $this->load->view("cases/time_logs", $data);
        }
    }
    public function set_time_tracking_billable_default_status()
    {
        if (!$this->input->is_ajax_request()) {
            redirect("dashboard");
        } else {
            $case_id = $this->input->post("case_id");
            $response["result"] = false;
            if (0 < $case_id) {
                $this->legal_case->fetch($case_id);
                $timeTrackingBillable = $this->legal_case->get_field("timeTrackingBillable");
                $set_field = "0";
                if ($timeTrackingBillable != "1") {
                    $set_field = "1";
                }
                $this->legal_case->set_field("timeTrackingBillable", $set_field);
                $this->legal_case->set_field("modifiedByChannel", $this->legal_case->get("webChannel"));
                $response["result"] = $this->legal_case->update();
                $response["billable"] = $set_field;
            }
            $this->output->set_content_type("application/json")->set_output(json_encode($response));
        }
    }
    public function set_expenses_billable_default_status()
    {
        if (!$this->input->is_ajax_request()) {
            redirect("dashboard");
        } else {
            $case_id = $this->input->post("case_id");
            $response["result"] = false;
            if (0 < $case_id) {
                $this->legal_case->fetch($case_id);
                $expensesBillable = $this->legal_case->get_field("expensesBillable");
                $set_field = "0";
                if ($expensesBillable != "1") {
                    $set_field = "1";
                }
                $this->legal_case->set_field("expensesBillable", $set_field);
                $response["expensesBillable"] = $set_field;
                $this->legal_case->set_field("modifiedByChannel", $this->legal_case->get("webChannel"));
                $response["result"] = $this->legal_case->update();
            }
            $this->output->set_content_type("application/json")->set_output(json_encode($response));
        }
    }
    public function export_to_word($id)
    {
        $data["legal_data"] = $this->export_to_word_data($id);
        $data["direction"] = $this->is_auth->is_layout_rtl() ? "rtl" : "ltr";
        $corepath = substr(COREPATH, 0, -12);
        require_once $corepath . "/application/libraries/phpdocx-advanced/classes/CreateDocx.php";
        $docx = new createDocx();
        $html = $this->load->view("cases/export_to_word", $data, true);
        $docx->embedHTML($html);
        $temp_directory = $this->config->item("files_path") . DIRECTORY_SEPARATOR . "tmp" . DIRECTORY_SEPARATOR . "cases" . DIRECTORY_SEPARATOR . "usr_" . $this->is_auth->get_user_id();
        if (!is_dir($temp_directory)) {
            @mkdir($temp_directory, 493);
        }
        $file_name = $this->legal_case->get("modelCode") . $id . "_" . date("YmdHi");
        $docx->createDocx($temp_directory . "/" . $file_name);
        $this->load->helper("download");
        $content = file_get_contents($temp_directory . "/" . $file_name . ".docx");
        unlink($temp_directory . "/" . $file_name . ".docx");
        $filename_encoded = $this->downloaded_file_name_by_browser($file_name . ".docx");
        force_download($filename_encoded, $content);
        exit;
    }
    private function export_to_word_data($id)
    {
        $legal_case_data = $this->legal_case->load_all_case_data($id);
        $legal_case_data[0]["subject"] = str_replace("&", "&amp;", $legal_case_data[0]["subject"]);
        $data["legal_case"] = $legal_case_data[0];
        $template_name = $data["legal_case"]["category"] == "Litigation" ? "litigation_case" : "matters";
        $this->load->library("word_template_manipulator");
        $data["legal_case"]["assignedToName"] = isset($data["legal_case"]["userStatus"]) && $data["legal_case"]["userStatus"] == "Inactive" ? $data["legal_case"]["assignedToName"] . " (" . $this->lang->line("Inactive") . ")" : $data["legal_case"]["assignedToName"];
        $data["legal_case"]["priority"] = $this->lang->line($data["legal_case"]["priority"]);
        $this->load->model("client");
        $client_data = $this->client->fetch_client($data["legal_case"]["client_id"]);
        $client_data["type"] = strtolower($client_data["type"]) == "company" ? $this->lang->line("company") : $this->lang->line("contact");
        $data["legal_case"]["client"] = isset($client_data["name"]) && $client_data["name"] ? "(" . $client_data["type"] . ") " . $client_data["name"] : "";
        $system_preferences = $this->session->userdata("systemPreferences");
        $case_value_currency = isset($system_preferences["caseValueCurrency"]) && $system_preferences["caseValueCurrency"] != "" ? "(" . $system_preferences["caseValueCurrency"] . ")" : "";
        $data["vals"]["caseValue"] = (double) $data["legal_case"]["caseValue"] . $case_value_currency;
        if ($data["legal_case"]["category"] == "Litigation") {
            $this->load->model("legal_case_opponent", "legal_case_opponentfactory");
            $this->legal_case_opponent = $this->legal_case_opponentfactory->get_instance();
            $related_opponent_data = $this->legal_case_opponent->fetch_case_opponents_data($id);
            $opponent = "";
            foreach ($related_opponent_data as $opponent_data) {
                $opponent_member_type = $opponent_data["opponent_member_type"] == "company" ? $this->lang->line("company") : $this->lang->line("contact");
                $opponent .= isset($opponent_data["opponentName"]) && $opponent_data["opponentName"] ? "(" . $opponent_member_type . ") " . $opponent_data["opponentName"] . ($opponent_data["position_name"] ? " - " . $opponent_data["position_name"] : "") . "<br>" : "";
            }
            $data["legal_case"]["opponent"] = $opponent;
            $data["vals"]["recoveredValue"] = (double) $data["legal_case"]["recoveredValue"] . $case_value_currency;
            $data["vals"]["judgmentValue"] = (double) $data["legal_case"]["judgmentValue"] . $case_value_currency;
        }
        $data["legal_case_companies"] = $this->legal_case->load_companies($id);
        $legal_case_contacts = $this->legal_case->load_contacts($id);
        $data["legal_case_contacts"] = $legal_case_contacts["contact"];
        $data["legal_case_external_lawyers"] = [];
        $outsources = $this->legal_case->k_load_all_outsource($id);
        if (!empty($outsources["data"])) {
            foreach ($outsources["data"] as $outsource) {
                $item = ["name" => $outsource["outsource_name"], "comments" => $outsource["comments"], "role" => $outsource["role_name"], "isLawyer" => NULL];
                $data["legal_case_external_lawyers"][] = $item;
            }
        }
        $data["cases_lawyers_contributors"] = $this->legal_case->load_all_cases_lawyers_contributors($id);
        $this->load->model("task", "taskfactory");
        $this->task = $this->taskfactory->get_instance();
        $data["case_tasks"] = $this->task->load_case_tasks($id);
        foreach ($data["case_tasks"] as $index => $task) {
            $data["case_tasks"][$index]["description"] = strip_tags($task["description"]);
        }
        $this->load->model("case_comment", "case_commentfactory");
        $this->case_comment = $this->case_commentfactory->get_instance();
        $case_comments = $this->case_comment->fetch_all_case_comment($id, false);
        $data["case_comments"] = [];
        foreach ($case_comments as $comment) {
            $comment_data = ["creator" => htmlentities($comment["userFullName"]) . " " . $this->lang->line("added_a_note_on") . " " . $comment["createdOn"] . " :", "text" => str_replace(["<br>", "<br class=\"Apple-interchange-newline\">"], "<br>", str_replace("&nbsp;", "", $comment["comment"]))];
            $data["case_comments"][] = $comment_data;
        }
        $data["case_events"] = [];
        $this->load->model("legal_case_event", "legal_case_eventfactory");
        $this->legal_case_event = $this->legal_case_eventfactory->get_instance();
        $case_events = $this->legal_case_event->load_all_events($id);
        if (isset($case_events["activities"])) {
            foreach ($case_events["activities"] as $event) {
                $data["case_events"][] = $event;
            }
        }
        $this->load->model("legal_case_litigation_detail", "legal_case_litigation_detailfactory");
        $this->legal_case_litigation_detail = $this->legal_case_litigation_detailfactory->get_instance();
        $data["stages"] = $this->legal_case_litigation_detail->load_activities($id, true, true);
        return $data;
    }
    public function add_client_and_update_case()
    {
        if (!$this->input->is_ajax_request()) {
            redirect("dashboard");
        } else {
            $response["result"] = false;
            if ($this->input->get("case_id")) {
                $case_id = $this->input->get("case_id");
                $this->legal_case->fetch($case_id);
                $client_id = $this->legal_case->get_field("client_id");
                $this->load->model("client");
                if (!$client_id) {
                    $data = ["title" => $this->lang->line("client"), "case_id" => $case_id];
                    $response["html"] = $this->load->view("cases/client_type_form", $data, true);
                } else {
                    $response["client"] = $this->client->fetch_client($client_id);
                    $response["result"] = true;
                }
            }
            if ($this->input->post("case_id")) {
                $case_id = $this->input->post("case_id");
                $contact_company_id = $this->input->post("contact_company_id");
                $client_model = $this->input->post("clientType");
                $this->load->model("client");
                $client_id = $contact_company_id ? $this->client->get_client($client_model, $contact_company_id) : NULL;
                if (!empty($client_id)) {
                    $this->legal_case->fetch($case_id);
                    $this->legal_case->set_field("client_id", $client_id);
                    $this->legal_case->set_field("modifiedByChannel", $this->legal_case->get("webChannel"));
                    if ($this->legal_case->update()) {
                        $response["case_id"] = $case_id;
                        $response["client_id"] = $client_id;
                        $response["client"] = $this->client->fetch_client($client_id);
                        $response["result"] = true;
                    }
                } else {
                    $response["validation_errors"]["contact_company_id"] = $this->lang->line("cannot_be_blank_rule");
                }
            }
            $this->output->set_content_type("application/json")->set_output(json_encode($response));
        }
    }
    public function case_commissions()
    {
        if (!$this->input->is_ajax_request()) {
            $this->set_flashmessage("error", $this->lang->line("invalid_record"));
            redirect("dashboard");
        } else {
            $data["formType"] = $this->input->post("formType");
            $data["case_id"] = $this->input->post("case_id");
            $this->load->model("legal_case_commission");
            $response = [];
            $data["case_commissions"] = $this->legal_case_commission->fetch_commissions($data["case_id"]);
            switch ($data["formType"]) {
                case "fetchForm":
                    $response["html"] = $this->load->view("cases/partners_shares", $data, true);
                    $response["result"] = true;
                    break;
                case "save":
                    if (!empty($data["case_commissions"])) {
                        $this->legal_case_commission->delete(["where" => ["case_id", $data["case_id"]]]);
                    }
                    $data["commissionBenifitiaryIds"] = $this->input->post("commissionBenifitiaryIds");
                    $data["commissionRate"] = $this->input->post("commissionRate");
                    $fields = ["case_id" => $data["case_id"], "account_id" => "", "commission" => ""];
                    $data_array = [];
                    if (!empty($data["commissionBenifitiaryIds"])) {
                        foreach ($data["commissionBenifitiaryIds"] as $key => $val) {
                            $tmpArr = $fields;
                            $tmpArr["account_id"] = $val;
                            $tmpArr["commission"] = $data["commissionRate"][$key];
                            array_push($data_array, $tmpArr);
                        }
                        $response["result"] = $this->legal_case_commission->insert_batch($data_array);
                    } else {
                        $response["result"] = true;
                    }
                    break;
                default:
            }
            $this->output->set_content_type("application/json")->set_output(json_encode($response));

        }
    }
    private function _feed_related_contacts_from_requested_by($mode)
    {
        $requestedBy = $this->input->post("requestedBy");
        if (!$requestedBy) {
            return true;
        }
        if ($mode === "add") {
            $relatedContact = $this->input->post("caseContactId");
            if ($requestedBy == $relatedContact) {
                return true;
            }
        }
        $caseId = $this->legal_case->get_field("id");
        $this->load->model("legal_case_contact");
        $LegalCaseContactData = ["contact_id" => $requestedBy, "case_id" => $caseId, "contactType" => "contact"];
        $this->legal_case_contact->reset_fields();
        $requestedByInRelatedContacts = $this->legal_case_contact->fetch($LegalCaseContactData);
        if ($requestedByInRelatedContacts) {
            return true;
        }
        $this->legal_case_contact->reset_fields();
        $this->legal_case_contact->set_fields($LegalCaseContactData);
        $this->legal_case_contact->insert();
        $this->legal_case_contact->reset_fields();
    }
    private function _feed_related_contacts_from_opponent_contact($mode)
    {
        $opponent_member_types = $this->input->post("opponent_member_type") ?? [];
        $opponent_member_ids = $this->input->post("opponent_member_id") ?? [];
        $caseId = $this->legal_case->get_field("id");
        $this->load->model("legal_case_contact");
        for ($opp = 0; $opp < count($opponent_member_types); $opp++) {
            $opponentContactId = $opponent_member_ids[$opp];
            if (strcmp($opponent_member_types[$opp], "company") && $opponentContactId) {
                if ($mode === "add") {
                    $relatedContact = $this->input->post("caseContactId");
                    if ($opponentContactId != $relatedContact) {
                    }
                }
                $LegalCaseContactData = ["contact_id" => $opponentContactId, "case_id" => $caseId, "contactType" => "contact"];
                $this->legal_case_contact->reset_fields();
                $opponentContactIdInRelatedContacts = $this->legal_case_contact->fetch($LegalCaseContactData);
                if (!$opponentContactIdInRelatedContacts) {
                    $this->legal_case_contact->reset_fields();
                    $this->legal_case_contact->set_fields($LegalCaseContactData);
                    $this->legal_case_contact->insert();
                    $this->legal_case_contact->reset_fields();
                }
            }
        }
    }
    private function _feed_related_contacts_from_client_contact($mode)
    {
        if ($this->input->post("clientType") === "companies") {
            return true;
        }
        $clientContactId = $this->input->post("contact_company_id");
        if (!$clientContactId) {
            return true;
        }
        if ($mode === "add" && $this->input->post("clientType") === "company") {
            return true;
        }
        $caseId = $this->legal_case->get_field("id");
        $this->load->model("legal_case_contact");
        $LegalCaseContactData = ["contact_id" => $clientContactId, "case_id" => $caseId, "contactType" => "contact"];
        $this->legal_case_contact->reset_fields();
        $clientContactIdInRelatedContacts = $this->legal_case_contact->fetch($LegalCaseContactData);
        if ($clientContactIdInRelatedContacts) {
            return true;
        }
        $this->legal_case_contact->reset_fields();
        $this->legal_case_contact->set_fields($LegalCaseContactData);
        $this->legal_case_contact->insert();
        $this->legal_case_contact->reset_fields();
    }
    private function _feed_related_companies_from_opponent_company($mode)
    {
        $opponent_member_types = $this->input->post("opponent_member_type") ?? [];
        $opponent_member_ids = $this->input->post("opponent_member_id") ?? [];
        $caseId = $this->legal_case->get_field("id");
        $this->load->model("legal_case_company");
        for ($opp = 0; $opp < count($opponent_member_types); $opp++) {
            $opponentCompanyId = $opponent_member_ids[$opp];
            if (strcmp($opponent_member_types[$opp], "contact") && $opponentCompanyId) {
                if ($mode === "add") {
                    $relatedCompany = $this->input->post("caseCompanyId");
                    if ($opponentCompanyId != $relatedCompany) {
                    }
                }
                $LegalCaseCompanyData = ["company_id" => $opponentCompanyId, "case_id" => $caseId];
                $this->legal_case_company->reset_fields();
                $opponentCompanyIdInRelatedCompanys = $this->legal_case_company->fetch($LegalCaseCompanyData);
                if (!$opponentCompanyIdInRelatedCompanys) {
                    $this->legal_case_company->reset_fields();
                    $this->legal_case_company->set_field("companyType", "company");
                    $this->legal_case_company->set_fields($LegalCaseCompanyData);
                    $this->legal_case_company->insert();
                    $this->legal_case_company->reset_fields();
                }
            }
        }
    }
    private function _feed_related_companies_from_client_company($mode)
    {
        if ($this->input->post("clientType") === "contacts") {
            return true;
        }
        $clientCompanyId = $this->input->post("contact_company_id");
        if (!$clientCompanyId) {
            return true;
        }
        if ($mode === "add" && $this->input->post("clientType") === "contact") {
            return true;
        }
        $caseId = $this->legal_case->get_field("id");
        $this->load->model("legal_case_company");
        $LegalCaseCompanyData = ["company_id" => $clientCompanyId, "case_id" => $caseId];
        $this->legal_case_company->reset_fields();
        $clientCompanyIdInRelatedCompanys = $this->legal_case_company->fetch($LegalCaseCompanyData);
        if ($clientCompanyIdInRelatedCompanys) {
            return true;
        }
        $this->legal_case_company->reset_fields();
        $this->legal_case_company->set_field("companyType", "company");
        $this->legal_case_company->set_fields($LegalCaseCompanyData);
        $this->legal_case_company->insert();
        $this->legal_case_company->reset_fields();
    }
    public function fetch_case_stages_history()
    {
        if ($this->input->is_ajax_request()) {
            $data = [];
            $case_id = $this->input->post("id");
            $this->load->model("legal_case_stage_changes", "legal_case_stage_changesfactory");
            $this->legal_case_stage_changes = $this->legal_case_stage_changesfactory->get_instance();
            $case_stage_changes = $this->legal_case_stage_changes->load_case_stages_history($case_id);
            foreach ($case_stage_changes as $key => $value) {
                $till_date = strtotime(date("Y-m-d"), time());
                if (isset($case_stage_changes[$key + 1])) {
                    $till_date = strtotime($case_stage_changes[$key + 1]["modifiedOn"]);
                }
                $modifiedOn = strtotime(date("Y-m-d", strtotime($value["modifiedOn"])));
                $case_stage_changes[$key]["since"] = floor(($till_date - $modifiedOn) / 86400);
            }
            $data["data"] = $case_stage_changes;
            $data["case_id"] = $case_id;
            $response["html"] = $this->load->view("cases/case_stages_history", $data, true);
            $this->output->set_content_type("application/json")->set_output(json_encode($response));
        } else {
            show_404();
        }
    }
    public function edit_case_stage_history()
    {
        if ($this->input->is_ajax_request()) {
            $response["result"] = false;
            $this->load->model("legal_case_stage_changes", "legal_case_stage_changesfactory");
            $this->legal_case_stage_changes = $this->legal_case_stage_changesfactory->get_instance();
            $this->legal_case_stage_changes->fetch($this->input->post("id"));
            $this->legal_case_stage_changes->set_fields($this->input->post(NULL));
            if ($this->legal_case_stage_changes->update()) {
                $response["result"] = true;
            }
            $this->output->set_content_type("application/json")->set_output(json_encode($response));
        } else {
            show_404();
        }
    }
    public function delete_case_stage_history()
    {
        if ($this->input->is_ajax_request()) {
            $response["result"] = false;
            $id = $this->input->post("id");
            $this->load->model("legal_case_stage_changes", "legal_case_stage_changesfactory");
            $this->legal_case_stage_changes = $this->legal_case_stage_changesfactory->get_instance();
            if ($this->legal_case_stage_changes->delete(["where" => ["id", $id]])) {
                $response["result"] = true;
            }
            $this->output->set_content_type("application/json")->set_output(json_encode($response));
        } else {
            show_404();
        }
    }
    private function _load_related_audit_report_models($litigationFlag = false)
    {
        $data = [];
        $this->load->model(["case_type", "provider_group", "user_profile", "client"]);
        $this->load->model("user", "userfactory");
        $this->user = $this->userfactory->get_instance();
        $usersList = $this->user->load_all_list();
        $usersList[0] = "";
        $data["Users"] = [];
        foreach ($usersList as $userId => $userName) {
            $data["Users"][$userId * 1] = $userName;
        }
        $data["Case_Statuses"] = $this->workflow_status->loadListWorkflowStatuses("", [], ["firstLine" => [""]]);
        if ($litigationFlag) {
            $data["Case_Types"] = $this->case_type->load_list(["where" => [["litigation", "yes"], ["isDeleted", 0]], "order_by" => ["name", "asc"]], ["firstLine" => [""]]);
        } else {
            $data["Case_Types"] = $this->case_type->load_list(["where" => [["corporate", "yes"], ["isDeleted", 0]], "order_by" => ["name", "asc"]], ["firstLine" => [""]]);
        }
        $data["Provider_Groups"] = $this->provider_group->load_list([]);
        $clients = $this->client->load_client_details();
        $data["Clients"][0] = "";
        foreach ($clients as $clientArray) {
            $data["Clients"][$clientArray["id"]] = $clientArray["name"];
        }
        return $data;
    }
    public function fetch_audit_report_history()
    {
        if (!$this->input->is_ajax_request()) {
            show_404();
        }
        $data = [];
        $caseId = $this->input->post("id");
        $litigationFlag = $this->input->post("category") && $this->input->post("category") === "Litigation" ? true : false;
        $caseLogsChanges = $this->legal_case->load_case_logs($caseId, $this->legal_case->load_visible_cases_ids());
        $modifications = [];
        if (!empty($caseLogsChanges)) {
            $relatedData = $this->_load_related_audit_report_models($litigationFlag);
            $category = $this->input->post("category") == "Litigation" ? "litigation_" : "matter_";
            $outerRelatedArray = ["case_status_id" => ["mappedIndexKey" => "Case_Statuses", "keyName" => $this->lang->line($category . "case_status")], "case_type_id" => ["mappedIndexKey" => "Case_Types", "keyName" => $this->lang->line("case_type_case")], "user_id" => ["mappedIndexKey" => "Users", "keyName" => $this->lang->line("assignee")], "provider_group_id" => ["mappedIndexKey" => "Provider_Groups", "keyName" => $this->lang->line("provider_group")], "client_id" => ["mappedIndexKey" => "Clients", "keyName" => $this->lang->line("client_name")]];
            $innerRelatedArray = ["priority" => $this->lang->line("case_priority"), "caseValue" => $this->lang->line("caseValue"), "arrivalDate" => $this->lang->line("filed_on"), "dueDate" => $this->lang->line("due_date"), "closedOn" => $this->lang->line("closed_on")];
            foreach ($caseLogsChanges as $caseLog) {
                $curModification = [];
                $locallyChanges = [];
                $changes = unserialize($caseLog["changes"]);
                foreach ($changes as $changedField => $FieldChangesArray) {
                    if (array_key_exists($changedField, $outerRelatedArray)) {
                        $fieldBefore = trim($FieldChangesArray["before"]) != "" ? (int) $FieldChangesArray["before"] : 0;
                        $fieldAfter = trim($FieldChangesArray["after"]) != "" ? (int) $FieldChangesArray["after"] : 0;
                        if (!isset($relatedData[$outerRelatedArray[$changedField]["mappedIndexKey"]][$fieldBefore])) {
                            $locallyChanges[$outerRelatedArray[$changedField]["keyName"]]["before"] = $this->lang->line("deleted_value");
                            $locallyChanges[$outerRelatedArray[$changedField]["keyName"]]["old_key_deleted"] = true;
                        } else {
                            $locallyChanges[$outerRelatedArray[$changedField]["keyName"]]["before"] = $relatedData[$outerRelatedArray[$changedField]["mappedIndexKey"]][$fieldBefore];
                        }
                        if (!isset($relatedData[$outerRelatedArray[$changedField]["mappedIndexKey"]][$fieldAfter])) {
                            $locallyChanges[$outerRelatedArray[$changedField]["keyName"]]["after"] = $this->lang->line("deleted_value");
                            $locallyChanges[$outerRelatedArray[$changedField]["keyName"]]["new_key_deleted"] = true;
                        } else {
                            $locallyChanges[$outerRelatedArray[$changedField]["keyName"]]["after"] = $relatedData[$outerRelatedArray[$changedField]["mappedIndexKey"]][$fieldAfter];
                        }
                    } else {
                        if (array_key_exists($changedField, $innerRelatedArray)) {
                            $fieldBefore = $FieldChangesArray["before"];
                            $fieldAfter = $FieldChangesArray["after"];
                            if ($changedField == "caseValue") {
                                $fieldBefore = (double) $FieldChangesArray["before"];
                                $fieldAfter = (double) $FieldChangesArray["after"];
                            } else {
                                if ($changedField == "priority") {
                                    $fieldBefore = $this->lang->line($FieldChangesArray["before"]);
                                    $fieldAfter = $this->lang->line($FieldChangesArray["after"]);
                                }
                            }
                            $locallyChanges[$innerRelatedArray[$changedField]]["before"] = $fieldBefore;
                            $locallyChanges[$innerRelatedArray[$changedField]]["after"] = $fieldAfter;
                        }
                    }
                }
                $curModification["modifiedOn"] = date("Y-m-d H:i", strtotime($caseLog["changedOn"]));
                $curModification["modifiedBy"] = $caseLog["modifiedBy"];
                $curModification["changes"] = $locallyChanges;
                $modifications[] = $curModification;
            }
        }
        $data["modifications"] = $modifications;
        $data["case_id"] = $caseId;
        $data["category"] = $this->input->post("category");
        $response["html"] = $this->load->view("cases/audit_report_history", $data, true);
        $this->output->set_content_type("application/json")->set_output(json_encode($response));
    }
    public function user_rates_per_hour()
    {
        if (!$this->input->is_ajax_request()) {
            redirect("dashboard");
        }
        $requestType = $this->input->post("requestType");
        $response = [];
        switch ($requestType) {
            case "readData":
                if ($this->input->post(NULL)) {
                    $this->load->model("user_rate_per_hour_per_case", "user_rate_per_hour_per_casefactory");
                    $this->user_rate = $this->user_rate_per_hour_per_casefactory->get_instance();
                    $filter = $this->input->post("filter");
                    $sortable = $this->input->post("sort");
                    $id = $this->input->post("caseId");
                    $response = $this->user_rate->k_load_all_case_rates($id, $sortable);
                }
                $this->output->set_content_type("application/json");
                $this->output->set_output(json_encode($response));
                break;
            case "getRateForm":
                $this->load->model("user_rate_per_hour_per_case", "user_rate_per_hour_per_casefactory");
                $this->user_rate = $this->user_rate_per_hour_per_casefactory->get_instance();
                $organizations = $this->user_rate->get_entities();
                $data = ["organizations" => $organizations];
                $response["html"] = $this->load->view("cases/user_rates_form", $data, true);
                $this->output->set_content_type("application/json");
                $this->output->set_output(json_encode($response));
                break;
            case "getRatePerCurrUser":
                $userId = $this->input->post("userId");
                $caseId = $this->input->post("caseId");
                $organizationId = $this->input->post("organizationId");
                $this->get_rate_by_user_id($userId, $caseId, $organizationId, false);
                break;
        }
    }
    public function user_rate_edit($caseId)
    {
        $response = [];
        if ($this->input->post(NULL) && $this->input->is_ajax_request()) {
            $rateId = $this->input->post("id");
            $rate = $this->input->post("ratePerHour");
            $userId = $this->input->post("user_id");
            $this->load->model("user_rate_per_hour_per_case", "user_rate_per_hour_per_casefactory");
            $this->user_rate = $this->user_rate_per_hour_per_casefactory->get_instance();
            $response = $this->user_rate->update_rate($rateId, $userId, $caseId, $rate);
        }
        $this->output->set_content_type("application/json")->set_output(json_encode($response));
    }
    public function user_rate_add($caseId)
    {
        if (!$this->input->is_ajax_request()) {
            redirect("dashboard");
        }
        $response = [];
        if ($this->input->post(NULL)) {
            $this->load->model("user_rate_per_hour_per_case", "user_rate_per_hour_per_casefactory");
            $this->user_rate = $this->user_rate_per_hour_per_casefactory->get_instance();
            $userId = $this->input->post("user_id");
            $rate = $this->input->post("ratePerHour");
            $organizationId = $this->input->post("organizationId");
            $response = $this->user_rate->add_rate($userId, $caseId, $organizationId, $rate);
        }
        $this->output->set_content_type("application/json");
        $this->output->set_output(json_encode($response));
    }
    private function get_rate_by_user_id($userId, $caseId, $organizationId, $returnData = false)
    {
        $userRate = "";
        if (0 < $userId) {
            $this->load->model("user_rate_per_hour_per_case", "user_rate_per_hour_per_casefactory");
            $this->user_rate = $this->user_rate_per_hour_per_casefactory->get_instance();
            $userRate = $this->user_rate->get_default_rate_per_user_id($userId, $caseId, $organizationId);
        }
        if ($returnData) {
            return $userRate;
        }
        $response = ["userRate" => $userRate];
        $this->output->set_content_type("application/json");
        $this->output->set_output(json_encode($response));
    }
    public function delete_user_rate()
    {
        $id = $this->input->post("rateId");
        $response = [];
        $response["status"] = 101;
        if ($id) {
            $this->load->model("user_rate_per_hour_per_case", "user_rate_per_hour_per_casefactory");
            $this->user_rate = $this->user_rate_per_hour_per_casefactory->get_instance();
            $response["status"] = $this->user_rate->delete(["where" => ["user_rate_per_hour_per_case.id", $id]]) ? 202 : 101;
        }
        $this->output->set_content_type("application/json")->set_output(json_encode($response));
    }
    public function move_status($caseId, $statusId, $oldStatus = "")
    {
        $old_values = $this->legal_case->get_old_values($caseId);
        $old_status = isset($oldStatus) && $oldStatus ? $oldStatus : $old_values["case_status_id"];
        if ($this->workflow_status->check_transition_allowed($caseId, $statusId, $this->is_auth->get_user_id())) {
            $move_status = $this->workflow_status->moveStatus($caseId, $statusId, $this->is_auth->get_user_id());
        }
        if (isset($move_status) && !$move_status) {
            if ($this->input->is_ajax_request()) {
                $response = ["result" => false];
                $this->output->set_content_type("application/json")->set_output(json_encode($response));
            } else {
                $this->set_flashmessage("error", $this->lang->line("workflowActionInvalid"));
                redirect("cases/edit/" . $caseId);
            }
        } else {
            $this->notify_users($caseId, $old_status);
            if ($this->system_preference->get_values()["webhooks_enabled"] == 1) {
                $data = $this->legal_case->load_case_details($caseId);
                $this->legal_case->trigger_web_hook($data["category"] == "Matter" ? "matter_status_updated" : "litigation_status_updated", $data);
            }
            if ($this->input->is_ajax_request()) {
                $response = ["result" => true];
                $this->output->set_content_type("application/json")->set_output(json_encode($response));
            } else {
                $this->set_flashmessage("success", sprintf($this->lang->line("status_updated_message"), $old_values["category"] == "Matter" ? $this->lang->line("corporate_matter") : $this->lang->line("the_litigation_case")));
                redirect("cases/edit/" . $caseId);
            }
        }
    }
    private function check_moving_status($caseId, $fromStatus, $toStatus)
    {
        $response = ["result" => false, "msg" => ""];
        $this->legal_case->fetch($caseId);
        $workflow_applicable = 0 < $this->legal_case->get_field("workflow") ? $this->legal_case->get_field("workflow") : 1;
        $response["result"] = $this->workflow_status->checkMovingStatus($caseId, $fromStatus, $toStatus, $workflow_applicable);
        if (!$response["result"]) {
            $response["msg"] = $this->lang->line("transition_not_allowed");
        }
        $this->output->set_content_type("application/json")->set_output(json_encode($response));
    }
    public function show_sla_working_hours($caseId)
    {
        $response = $data = [];
        $data["sysPrefBusinessDate"] = $this->sla_management_mod->sys_pref_business_date();
        $response["result"] = true;
        if (!empty($data["sysPrefBusinessDate"])) {
            $this->legal_case->fetch($caseId);
            $workflow_applicable = 0 < $this->legal_case->get_field("workflow") ? $this->legal_case->get_field("workflow") : 1;
            $data["caseWorkingHours"] = $this->sla_management_mod->caseWorkingHours($caseId, $workflow_applicable, $data["sysPrefBusinessDate"]);
            $data["slaList"] = $this->sla_management_mod->loadAll($workflow_applicable);
            $data["caseId"] = $caseId;
            $response["html"] = $this->load->view("cases/sla_list", $data, true);
        } else {
            $response["result"] = false;
        }
        $this->output->set_content_type("application/json")->set_output(json_encode($response));
    }
    public function delete_case($legal_case_id)
    {
        if ($this->legal_case->fetch($legal_case_id)) {
            $case_type = $this->legal_case->get_field("category");
            $related_money = $this->legal_case->check_case_related_to_money($legal_case_id);
            if ($related_money) {
                $this->set_flashmessage("information", $this->lang->line("case_related_to_money"));
                redirect($this->agent->referrer());
            } else {
                $workflow_applicable = $this->workflow_status->getDefaultSystemWorkflow(strtolower($case_type));
                $LegalCaseFields["workflow"] = $workflow_applicable["workflow"] ? $workflow_applicable["workflow"] : "1";
                $LegalCaseFields["case_status_id"] = ($status_data = $this->workflow_status->get_workflow_start_point($LegalCaseFields["workflow"])) ? $status_data : "1";
                $this->legal_case->set_field("case_status_id", $LegalCaseFields["case_status_id"]);
                $this->legal_case->set_field("workflow", $LegalCaseFields["workflow"]);
                $this->legal_case->set_field("isDeleted", 1);
                $this->legal_case->set_field("modifiedByChannel", $this->legal_case->get("webChannel"));
                if ($this->legal_case->update()) {
                    $user_maker = $this->is_auth->get_user_id();
                    $this->legal_case->log_delete_action("delete", $user_maker);
                    $this->load->model("reminder", "reminderfactory");
                    $this->reminder = $this->reminderfactory->get_instance();
                    $this->reminder->dismiss_related_reminders_by_related_object_ids($legal_case_id, "legal_case_id");
                    if ($case_type == "Litigation") {
                        $this->load->model("mv_hearing");
                        $this->mv_hearing->delete(["where" => ["legal_case_id", $legal_case_id]]);
                    }
                    $this->load->model("legal_case_related_container");
                    $this->legal_case_related_container->delete(["where" => ["legal_case_id", $legal_case_id]]);
                    $this->set_flashmessage("information", $this->lang->line("case_deleted"));
                    redirect("cases/" . ($case_type == "Litigation" ? "litigation_case" : "legal_matter"));
                }
            }
        } else {
            $this->set_flashmessage("error", $this->lang->line("invalid_record"));
            redirect("dashboard");
        }
    }
    public function set_privacy()
    {
        if (!$this->input->is_ajax_request()) {
            redirect("dashboard");
        }
        $response = ["result" => true];
        $this->output->set_content_type("application/json")->set_output(json_encode($response));
    }
    private function notify_users($caseId, $old_status, $transition = 0)
    {
        $this->legal_case->fetch($caseId);
        if (!strcmp($this->legal_case->get_field("channel"), $this->legal_case->get("portalChannel"))) {
            $this->load->model("customer_portal_users", "customer_portal_usersfactory");
            $this->customer_portal_users = $this->customer_portal_usersfactory->get_instance();
            $this->customer_portal_users->fetch($this->legal_case->get_field("createdBy"));
            $toEmail = $this->customer_portal_users->get_field("email");
            $this->legal_case->notifyTicketUser($caseId, $this->legal_case->get_field("case_status_id"), $this->is_auth->get_user_id(), $this->session->userdata("AUTH_userProfileName"), $this->legal_case->get_field("createdBy"), $toEmail, false, $this->legal_case->get("webChannel"), $old_status);
        } else {
            if ($this->legal_case->case_outsourced($caseId)) {
                $modified_on = $this->legal_case->get_field("modifiedOn");
                $status_id = $this->legal_case->get_field("case_status_id");
                $logged_user = $this->session->userdata("AUTH_userProfileName");

                $object = "core_user_edit_case_status";
                $this->workflow_status->fetch($status_id);
                $new_status_name = $this->workflow_status->get_field("name");
                $this->workflow_status->fetch($old_status);
                $old_status_name = $this->workflow_status->get_field("name");
                $model = "legal_cases";
                $model_data["id"] = $caseId;
                $notifications_emails = $this->email_notification_scheme->get_emails($object, $model, $model_data);
                $to_emails = $notifications_emails["to_emails"] ?? [];
                $cc_emails = $notifications_emails["cc_emails"] ?? [];
                $this->load->model("client");
                $client_info = $this->client->fetch_client($this->legal_case->get_field("client_id"));
                $notifications_data = ["to" => array_filter($to_emails), "object" => $object, "object_id" => $caseId, "cc" => $cc_emails, "content" => ["modifier" => $logged_user, "from" => $old_status_name, "to" => $new_status_name, "on" => $modified_on, "assignee" => $this->email_notification_scheme->get_user_full_name($this->legal_case->get_field("user_id")), "file_reference" => $this->legal_case->get_field("internalReference"), "client_name" => $client_info["name"], "priority" => $this->legal_case->get_field("priority")], "fromLoggedUser" => $logged_user];
                $this->load->library("email_notifications");
                $this->email_notifications->notify($notifications_data);
            } else {
                $this->legal_case->notify_related_users($caseId, $old_status, $transition);
            }
        }
        return true;
    }
    public function add_litigation($container_id = 0)
    {
        $data = $response = [];
        if (!$this->input->is_ajax_request()) {
            redirect("litigation_case");
        }
        $system_preferences = $this->session->userdata("systemPreferences");


        $this->load->library("TimeMask");
        if ($this->request_can_cause_insufficient_anti_automation("Litigation_Case")) {
            $response["error"] = $this->lang->line("insufficient_anti_automation_message");
        } else {
            if (!$this->input->post(NULL)) {
                $formData = $this->_load_related_models("litigation");
                $formData["litigationCaseTypes"] = $this->case_type->load_all(["where" => [["litigation", "yes"], ["isDeleted", 0]], "order_by" => ["name", "asc"]]);
                $formData["litigationCaseType"] = $this->case_type->load(["where" => [["id", $system_preferences["caseTypeLitigationId"]], ["litigation", "yes"], ["isDeleted", 0]], "order_by" => ["name", "asc"]]);
                $selected_values["category"] = "Litigation";
                $selected_values["today"] = date("Y-m-d", time());
                $selected_values["priority"] = "medium";
                if ($formData["litigationCaseType"]) {
                    $Date = $selected_values["today"];
                    if ($formData["litigationCaseType"]["litigationSLA"]) {
                        $val = date("Y-m-d", strtotime($Date . " + " . $formData["litigationCaseType"]["litigationSLA"] . " days"));
                        $selected_values["dueDate"] = $val;
                    }
                }
                $data = $formData;
                $data["systemPreferences"] = $system_preferences;
                $data["defaultCompanyId"]=$system_preferences['defaultCompany'];
                $this->load->model("company", "companyfactory");
                $this->company = $this->companyfactory->get_instance();
                $this->company->fetch($data["defaultCompanyId"]);
                $data["defaultCompanyName"]=$this->company->get_field("name");

                $data["hide_show_notification"] = $this->email_notification_scheme->get_hide_show_send_email_notification_by_trigger_action("add_litigation_case") == "1" ? "yes" : "";
                $this->load->model("legal_case_stage", "legal_case_stagefactory");
                $this->legal_case_stage = $this->legal_case_stagefactory->get_instance();
                $data["litigationCaseStages"] = $this->legal_case_stage->load_list_per_case_category_per_language("litigation");
                $data["assignments"] = $this->return_assignments_rules($system_preferences["caseTypeLitigationId"], "litigation");
                $this->provider_group->fetch(["allUsers" => 1]);
                $data["allUsersProviderGroupId"] = $this->provider_group->get_field("id");
                $data["usersProviderGroup"] = $this->get_provider_group_users($data["assignments"]["assigned_team"]);
                $this->load->model("legal_case_container", "legal_case_containerfactory");
                $this->legal_case_container = $this->legal_case_containerfactory->get_instance();
                $selected_values["container_common_fields"] = $this->legal_case_container->load_container_common_fields($container_id, "Litigation");
                $data["selected_values"] = $selected_values;
                $this->load->model("case_types_due_condition", "case_types_due_conditionfactory");
                $this->case_types_due_condition = $this->case_types_due_conditionfactory->get_instance();
                $data["case_type_due_conditions"] = json_encode($this->case_type->get_all_case_types_with_due_conditions());
                $this->load->model("reminder", "reminderfactory");
                $this->reminder = $this->reminderfactory->get_instance();
                $data["notify_me_before_time_types"] = array_combine($this->reminder->get("notify_me_before_time_type"), [$this->lang->line("day_or_days"), $this->lang->line("week_or_weeks"), $this->lang->line("month")]);
                $data["notify_me_before_types"] = array_combine($this->reminder->get("notify_me_before_type"), [$this->lang->line("reminder_popup"), $this->lang->line("reminder_popup_and_email")]);
                $data["max_opponents"] = $system_preferences["caseMaxOpponents"];
                $response["html"] = $this->load->view("cases/add_litigation", $data, true);
            } else {
                $opponent_member_types = $this->input->post("opponent_member_type");
                $opponent_member_ids = $this->input->post("opponent_member_id");
                $opponent_positions = $this->input->post("opponent_position");
                $post_data = $this->input->post(NULL);
                array_walk($post_data, [$this, "sanitize_post"]);
                $_POST["legal_case_success_probability_id"] = $this->legal_case->get("defaultSuccessProbabilityId");
                $post_data["legal_case_success_probability_id"] = $this->input->post("legal_case_success_probability_id");
                unset($_POST["opponent_position"]);
                unset($post_data["opponent_position"]);
                $workflow_applicable = $this->workflow_status->getWorkflowOfAreaPractice($post_data["case_type_id"], "litigation");
                if (!empty($workflow_applicable)) {
                    $post_data["case_status_id"] = $this->workflow_status->get_workflow_start_point($workflow_applicable["workflow"]);
                    $post_data["workflow"] = $workflow_applicable["workflow"];
                } else {
                    $workflow_applicable = $this->workflow_status->getDefaultSystemWorkflow("litigation");
                    $post_data["workflow"] = isset($workflow_applicable["workflow"]) ? $workflow_applicable["workflow"] : "1";
                    $post_data["case_status_id"] = ($status_data = $this->workflow_status->get_workflow_start_point($post_data["workflow"])) ? $status_data : "1";
                }
                $post_data["cap_amount_enable"] = "0";
                $post_data["cap_amount_disallow"] = "0";
                $post_data["cap_amount"] = "0";
                $post_data["expenses_cap_ratio"] = "100";
                $post_data["time_logs_cap_ratio"] = "100";
                $this->legal_case->set_fields($post_data);
                $this->_set_common_fields();
                $lookup_validate = $this->legal_case->get_lookup_validation_errors($this->legal_case->get("lookupInputsToValidate"), $post_data);
                $estimated_effort = $this->input->post("estimatedEffort");
                $estimated_effort_value = 0;
                if (!empty($estimated_effort)) {
                    $estimated_effort_value = $this->timemask->humanReadableToHours($estimated_effort);
                }
                $this->legal_case->set_field("estimatedEffort", $estimated_effort_value);
                if ($this->legal_case->validate() && !$lookup_validate) {
                    $notify_before = $this->input->post("notify_me_before");
                    if ($notify_before && $this->input->post("dueDate") && (!$notify_before["time"] || !$notify_before["time_type"] || !$notify_before["type"] || ($is_not_nb = !is_numeric($notify_before["time"])))) {
                        if ($is_not_nb) {
                            $response["validationErrors"]["notify_before"] = sprintf($this->lang->line("is_numeric_rule"), $this->lang->line("notify_before"));
                        } else {
                            $response["validationErrors"]["notify_before"] = $this->lang->line("cannot_be_blank_rule");
                        }
                    } else {
                        $assignment_response = $this->save_assignment();
                        if ($this->legal_case->insert()) {
                            $this->increase_count_for_anti_automation_prevention("Litigation_Case");
                            if ($this->input->post("legalCaseRelatedContainerId")) {
                                $this->load->model("legal_case_related_container");
                                $this->load->model("legal_case_container", "legal_case_containerfactory");
                                $this->legal_case_container = $this->legal_case_containerfactory->get_instance();
                                if ($this->legal_case_container->fetch($this->input->post("legalCaseRelatedContainerId")) && $this->input->post("legalCaseRelatedContainerId")) {
                                    $this->legal_case_related_container->set_fields(["legal_case_container_id" => $this->input->post("legalCaseRelatedContainerId"), "legal_case_id" => $this->legal_case->get_field("id")]);
                                    $this->legal_case_related_container->insert();
                                }
                            }
                            if ($this->input->post("related_appeal_case_id"))//if it is an appeal, relate the two cases
                            {
                                $new_case_id= $this->legal_case->get_field("id");//new case
                                $current_case_id=$this->input->post("related_appeal_case_id");//caseId
                                $this->load->model("related_case", "related_casefactory");
                                $this->related_case = $this->related_casefactory->get_instance();
                                $this->related_case->set_field("case_a_id",$new_case_id);
                                $this->related_case->set_field("case_b_id",$current_case_id);
                                $this->related_case->set_field("comments","This was appealed to M".$new_case_id);

                                //   $this->related_case->set_field("id", $current_case_id);
                                $this->related_case->set_field("case_a_id", $current_case_id);
                                $this->related_case->set_field("case_b_id", $new_case_id);
                                $response["status"] = $this->related_case->insert() ? 202 : 101;
                                if ($response["status"] == 202) {
                                    $this->legal_case->set_field("id", $new_case_id);
                                    $this->related_case->reset_fields();
                                    $this->related_case->set_field("comments","This is an appeal to M".$current_case_id);
                                    $this->related_case->set_field("case_a_id", $new_case_id);
                                    $this->related_case->set_field("case_b_id", $current_case_id);
                                    $response["status"] = $this->related_case->insert() ? 202 : 101;
                                    $this->legal_case->touch_logs();
                                }

                            }
                            $legal_case = $this->legal_case->get_fields();
                            $this->legal_case->inject_folder_templates($this->legal_case->get_field("id"), $this->legal_case->get_field("category"), $this->legal_case->get_field("case_type_id"));
                            $this->legal_case->set_fields($legal_case);
                            $case_id = $this->legal_case->get_field("id");
                            $this->load->model("opponent");
                            $this->load->model("legal_case_litigation_stages_opponent", "legal_case_litigation_stages_opponentfactory");
                            $this->legal_case_litigation_stages_opponent = $this->legal_case_litigation_stages_opponentfactory->get_instance();
                            $opponents_data = is_array($opponent_member_types) && !empty($opponent_member_types) ? $this->opponent->get_opponents($opponent_member_types, $opponent_member_ids) : [];
                            if ($post_data["legal_case_stage_id"]) {
                                $this->load->model("legal_case_litigation_detail", "legal_case_litigation_detailfactory");
                                $this->legal_case_litigation_detail = $this->legal_case_litigation_detailfactory->get_instance();
                                $this->legal_case_litigation_detail->set_field("legal_case_id", $case_id);
                                $this->legal_case_litigation_detail->set_field("legal_case_stage", $post_data["legal_case_stage_id"]);
                                $this->legal_case_litigation_detail->set_field("client_position", $post_data["legal_case_client_position_id"]);
                                $this->legal_case_litigation_detail->insert();
                                $stage = $this->legal_case_litigation_detail->get_field("id");
                                $this->legal_case->set_field("stage", $stage);
                                $this->legal_case->update();
                                if (!empty($opponents_data)) {
                                    $stage_opponents = [];
                                    foreach ($opponents_data as $key => $value) {
                                        $stage_opponents[$key]["opponent_id"] = $value["opponent_id"];
                                        $stage_opponents[$key]["stage"] = $stage;
                                        $stage_opponents[$key]["opponent_position"] = isset($opponent_positions[$key]) && !empty($opponent_positions[$key]) ? $opponent_positions[$key] : NULL;
                                    }
                                    $this->legal_case_litigation_stages_opponent->insert_stage_opponents($stage, $stage_opponents);
                                }
                            }
                            if (!empty($opponents_data)) {
                                foreach ($opponents_data as $key => $value) {
                                    $opponents_data[$key]["case_id"] = $case_id;
                                    $opponents_data[$key]["opponent_position"] = isset($opponent_positions[$key]) && !empty($opponent_positions[$key]) ? $opponent_positions[$key] : NULL;
                                }
                                $this->legal_case_opponent->insert_case_opponents($case_id, $opponents_data);
                            }
                            $this->_feed_related_contacts_from_opponent_contact("add");
                            $this->_feed_related_companies_from_opponent_company("add");
                            $response = $this->_submit_add_form("litigation");
                            if (isset($assignment_response["display_message"])) {
                                $response["display_message"] = $assignment_response["display_message"];
                            }
                        }
                        if ($system_preferences["webhooks_enabled"] == 1) {
                            $webhook_data = $this->legal_case->load_case_details($case_id);
                            $this->legal_case->trigger_web_hook("litigation_created", $webhook_data);
                        }
                        $this->legal_case->update_recent_ids($case_id, "litigation_cases");
                    }
                } else {
                    $response["validationErrors"] = $this->legal_case->get_validation_errors($lookup_validate);
                }
            }
        }
        $this->output->set_content_type("application/json")->set_output(json_encode($response));
    }

    public function add_legal_matter($container_id = 0)
    {
        $data = $response = [];
        if (!$this->input->is_ajax_request()) {
            redirect("legal_matter");
        }
        $system_preferences = $this->session->userdata("systemPreferences");
        $this->load->library("TimeMask");

        if ($this->request_can_cause_insufficient_anti_automation("Corporate_Matter")) {
            $response["error"] = $this->lang->line("insufficient_anti_automation_message");
        } else {
            if (!$this->input->post(NULL)) {
                $formData = $this->_load_related_models(false);
                $formData["legalCaseTypes"] = $this->case_type->load_all(["where" => [["corporate", "yes"], ["isDeleted", 0]], "order_by" => ["name", "asc"]]);
                $formData["legalCaseType"] = $this->case_type->load(["where" => [["id", $system_preferences["caseTypeProjectId"]], ["corporate", "yes"], ["isDeleted", 0]], "order_by" => ["name", "asc"]]);
                $selected_values["category"] = "Matter";
                $selected_values["today"] = date("Y-m-d", time());
                $selected_values["priority"] = "medium";
                if ($formData["legalCaseType"]) {
                    $Date = $selected_values["today"];
                    if ($formData["legalCaseType"]["litigationSLA"]) {
                        $val = date("Y-m-d", strtotime($Date . " + " . $formData["legalCaseType"]["litigationSLA"] . " days"));
                        $selected_values["dueDate"] = $val;
                    }
                }
                $data = $formData;
                $data["systemPreferences"] = $system_preferences;
                $data["defaultCompanyId"]=$system_preferences['defaultCompany'];
                $data["hide_show_notification"] = $this->email_notification_scheme->get_hide_show_send_email_notification_by_trigger_action("add_matter_case") == "1" ? "yes" : "";
                $this->load->model("legal_case_stage", "legal_case_stagefactory");
                $this->legal_case_stage = $this->legal_case_stagefactory->get_instance();
                $data["corporateCaseStages"] = $this->legal_case_stage->load_list_per_case_category_per_language("corporate");

                $data["assignments"] = $this->return_assignments_rules($system_preferences["caseTypeProjectId"], "matter");
                $this->provider_group->fetch(["allUsers" => 1]);
                $data["allUsersProviderGroupId"] = $this->provider_group->get_field("id");
                $data["usersProviderGroup"] = $this->get_provider_group_users($data["assignments"]["assigned_team"]);

                $this->load->model("legal_case_container", "legal_case_containerfactory");
                $this->legal_case_container = $this->legal_case_containerfactory->get_instance();
                $selected_values["container_common_fields"] = $this->legal_case_container->load_container_common_fields($container_id, "Matter");
                $data["selected_values"] = $selected_values;

                $this->load->model("reminder", "reminderfactory");
                $this->reminder = $this->reminderfactory->get_instance();
                $this->load->model("case_types_due_condition", "case_types_due_conditionfactory");
                $this->case_types_due_condition = $this->case_types_due_conditionfactory->get_instance();
                $data["case_type_due_conditions"] = json_encode($this->case_type->get_all_case_types_with_due_conditions());
                $data["notify_me_before_time_types"] = array_combine($this->reminder->get("notify_me_before_time_type"), [$this->lang->line("day_or_days"), $this->lang->line("week_or_weeks"), $this->lang->line("month")]);
                $data["notify_me_before_types"] = array_combine($this->reminder->get("notify_me_before_type"), [$this->lang->line("reminder_popup"), $this->lang->line("reminder_popup_and_email")]);
                $response["html"] = $this->load->view("cases/add_legal_matter", $data, true);
            } else {
                $post_data = $this->input->post(NULL);
                array_walk($post_data, [$this, "sanitize_post"]);
                $_POST["legal_case_success_probability_id"] = $this->legal_case->get("defaultSuccessProbabilityId");
                $post_data["legal_case_success_probability_id"] = $this->input->post("legal_case_success_probability_id");
                $workflow_applicable = $this->workflow_status->getWorkflowOfAreaPractice($post_data["case_type_id"], "matter");
                if (!empty($workflow_applicable)) {
                    $_POST["case_status_id"] = $this->workflow_status->get_workflow_start_point($workflow_applicable["workflow"]);
                    $_POST["workflow"] = $workflow_applicable["workflow"];
                } else {
                    $workflow_applicable = $this->workflow_status->getDefaultSystemWorkflow("matter");
                    $_POST["workflow"] = isset($workflow_applicable["workflow"]) ? $workflow_applicable["workflow"] : "1";
                    $_POST["case_status_id"] = ($status_data = $this->workflow_status->get_workflow_start_point($this->input->post("workflow"))) ? $status_data : "1";
                }
                $post_data["case_status_id"] = $this->input->post("case_status_id");
                $post_data["workflow"] = $this->input->post("workflow");
                $post_data["cap_amount_enable"] = "0";
                $post_data["cap_amount_disallow"] = "0";
                $post_data["cap_amount"] = "0";
                $post_data["expenses_cap_ratio"] = "100";
                $post_data["time_logs_cap_ratio"] = "100";
                $this->legal_case->set_fields($post_data);
                $this->_set_common_fields();
                $lookup_validate = $this->legal_case->get_lookup_validation_errors($this->legal_case->get("lookupInputsToValidate"), $post_data);
                $estimated_effort = $this->input->post("estimatedEffort");
                $estimated_effort_value = 0;
                if (!empty($estimated_effort)) {
                    $estimated_effort_value = $this->timemask->humanReadableToHours($estimated_effort);
                }
                $this->legal_case->set_field("estimatedEffort", $estimated_effort_value);
                if ($this->legal_case->validate() && !$lookup_validate) {
                    $notify_before = $this->input->post("notify_me_before");
                    if ($notify_before && $this->input->post("dueDate") && (!$notify_before["time"] || !$notify_before["time_type"] || !$notify_before["type"] || ($is_not_nb = !is_numeric($notify_before["time"])))) {
                        if ($is_not_nb) {
                            $response["validationErrors"]["notify_before"] = sprintf($this->lang->line("is_numeric_rule"), $this->lang->line("notify_before"));
                        } else {
                            $response["validationErrors"]["notify_before"] = $this->lang->line("cannot_be_blank_rule");
                        }
                    } else {
                        $assignment_response = $this->save_assignment();
                        if ($this->legal_case->insert()) {
                            $this->increase_count_for_anti_automation_prevention("Corporate_Matter");
                            $case_id = $this->legal_case->get_field("id");
                            if ($this->input->post("legalCaseRelatedContainerId")) {
                                $this->load->model("legal_case_related_container");
                                $this->load->model("legal_case_container", "legal_case_containerfactory");
                                $this->legal_case_container = $this->legal_case_containerfactory->get_instance();
                                if ($this->legal_case_container->fetch($this->input->post("legalCaseRelatedContainerId")) && $this->input->post("legalCaseRelatedContainerId")) {
                                    $this->legal_case_related_container->set_fields(["legal_case_container_id" => $this->input->post("legalCaseRelatedContainerId"), "legal_case_id" => $case_id]);
                                    $this->legal_case_related_container->insert();
                                }
                            }
                            if ($system_preferences["webhooks_enabled"] == 1) {
                                $webhook_data = $this->legal_case->load_case_details($case_id);
                                $this->legal_case->trigger_web_hook("matter_created", $webhook_data);
                            }
                            $this->legal_case->update_recent_ids($case_id, "corporate_matters");
                        }
                        $legal_case = $this->legal_case->get_fields();
                        $this->legal_case->inject_folder_templates($this->legal_case->get_field("id"), $this->legal_case->get_field("category"), $this->legal_case->get_field("case_type_id"));
                        $this->legal_case->set_fields($legal_case);
                        $getting_started_settings = unserialize($this->user_preference->get_value("getting_started"));
                        $getting_started_settings["add_legal_matter_step_done"] = true;
                        $this->user_preference->set_value("getting_started", serialize($getting_started_settings), true);
                        $this->load->model("legal_case_stage_changes", "legal_case_stage_changesfactory");
                        $this->legal_case_stage_changes = $this->legal_case_stage_changesfactory->get_instance();
                        $case_stage_changes = ["legal_case_id" => $this->legal_case->get_field("id"), "oldValue" => NULL, "legal_case_stage_id" => $this->legal_case->get_field("legal_case_stage_id"), "modifiedOn" => $this->legal_case->get_field("createdOn")];
                        $this->legal_case_stage_changes->log_changes($case_stage_changes);
                        $response = $this->_submit_add_form("matter");
                        if (isset($assignment_response["display_message"])) {
                            $response["display_message"] = $assignment_response["display_message"];
                        }
                    }
                } else {
                    $response["validationErrors"] = $this->legal_case->get_validation_errors($lookup_validate);
                }
            }
        }
        $this->output->set_content_type("application/json")->set_output(json_encode($response));
    }

    /*criminal case*/
    public function add_criminal_case($container_id = 0)
    {
        $data = $response = [];
        if (!$this->input->is_ajax_request()) {
            redirect("litigation_case");
        }
        $system_preferences = $this->session->userdata("systemPreferences");
        $this->load->library("TimeMask");
        if ($this->request_can_cause_insufficient_anti_automation("Litigation_Case")) {///atinga to change
            $response["error"] = $this->lang->line("insufficient_anti_automation_message");
        } else {
            if (!$this->input->post(NULL)) {
                $formData = $this->_load_related_models("criminal");
                $formData["litigationCaseTypes"] = $this->case_type->load_all(["where" => [["criminal", "yes"], ["isDeleted", 0]], "order_by" => ["name", "asc"]]);
                $formData["litigationCaseType"] = $this->case_type->load(["where" => [["id", $system_preferences["caseTypeLitigationId"]], ["litigation", "yes"], ["isDeleted", 0]], "order_by" => ["name", "asc"]]);
                $selected_values["category"] = "Criminal";
                $selected_values["today"] = date("Y-m-d", time());
                $selected_values["priority"] = "medium";
                if ($formData["litigationCaseType"]) {
                    $Date = $selected_values["today"];
                    if ($formData["litigationCaseType"]["litigationSLA"]) {
                        $val = date("Y-m-d", strtotime($Date . " + " . $formData["litigationCaseType"]["litigationSLA"] . " days"));
                        $selected_values["dueDate"] = $val;
                    }
                }
                $data = $formData;
                $data["systemPreferences"] = $system_preferences;
                $data["defaultCompanyId"]=$system_preferences['defaultCompany']??"";
                $data["hide_show_notification"] = $this->email_notification_scheme->get_hide_show_send_email_notification_by_trigger_action("add_litigation_case") == "1" ? "yes" : "";
                $this->load->model("legal_case_stage", "legal_case_stagefactory");
                $this->legal_case_stage = $this->legal_case_stagefactory->get_instance();
                $data["litigationCaseStages"] = $this->legal_case_stage->load_list_per_case_category_per_language("litigation");
                $data["assignments"] = $this->return_assignments_rules($system_preferences["caseTypeLitigationId"], "litigation");
                $this->provider_group->fetch(["allUsers" => 1]);
                $data["allUsersProviderGroupId"] = $this->provider_group->get_field("id");
                $data["usersProviderGroup"] = $this->get_provider_group_users($data["assignments"]["assigned_team"]??=0);
                $this->load->model("legal_case_container", "legal_case_containerfactory");
                $this->legal_case_container = $this->legal_case_containerfactory->get_instance();
                $selected_values["container_common_fields"] = $this->legal_case_container->load_container_common_fields($container_id, "Litigation");
                $data["selected_values"] = $selected_values;
                $this->load->model("case_types_due_condition", "case_types_due_conditionfactory");
                $this->case_types_due_condition = $this->case_types_due_conditionfactory->get_instance();
                $data["case_type_due_conditions"] = json_encode($this->case_type->get_all_case_types_with_due_conditions());
                $this->load->model("reminder", "reminderfactory");
                $this->reminder = $this->reminderfactory->get_instance();
                $data["notify_me_before_time_types"] = array_combine($this->reminder->get("notify_me_before_time_type"), [$this->lang->line("day_or_days"), $this->lang->line("week_or_weeks"), $this->lang->line("month")]);
                $data["notify_me_before_types"] = array_combine($this->reminder->get("notify_me_before_type"), [$this->lang->line("reminder_popup"), $this->lang->line("reminder_popup_and_email")]);
                $data["max_opponents"] = $system_preferences["caseMaxOpponents"];
                
                $this->load->model("criminal_case_detail", "criminal_case_detailfactory");
                $this->criminal_case_detail = $this->criminal_case_detailfactory->get_instance();
                $data["criminal_case_status"]=array_combine($this->criminal_case_detail->get("criminalCaseStatusValues"),$this->criminal_case_detail->get("criminalCaseStatusValues"));

                $response["html"] = $this->load->view("cases/add_criminal_case", $data, true);
            } else {
                $opponent_member_types = $this->input->post("opponent_member_type");
                $opponent_member_ids = $this->input->post("opponent_member_id");
                $opponent_positions = $this->input->post("opponent_position");
                $post_data = $this->input->post(NULL);
                array_walk($post_data, [$this, "sanitize_post"]);
                $_POST["legal_case_success_probability_id"] = $this->legal_case->get("defaultSuccessProbabilityId");
                $post_data["legal_case_success_probability_id"] = $this->input->post("legal_case_success_probability_id");
                unset($_POST["opponent_position"]);
                unset($post_data["opponent_position"]);
                $workflow_applicable = $this->workflow_status->getWorkflowOfAreaPractice($post_data["case_type_id"], "litigation");
                if (!empty($workflow_applicable)) {
                    $post_data["case_status_id"] = $this->workflow_status->get_workflow_start_point($workflow_applicable["workflow"]);
                    $post_data["workflow"] = $workflow_applicable["workflow"];
                } else {
                    $workflow_applicable = $this->workflow_status->getDefaultSystemWorkflow("litigation");
                    $post_data["workflow"] = isset($workflow_applicable["workflow"]) ? $workflow_applicable["workflow"] : "1";
                    $post_data["case_status_id"] = ($status_data = $this->workflow_status->get_workflow_start_point($post_data["workflow"])) ? $status_data : "1";
                }
                $post_data["cap_amount_enable"] = "0";
                $post_data["cap_amount_disallow"] = "0";
                $post_data["cap_amount"] = "0";
                $post_data["expenses_cap_ratio"] = "100";
                $post_data["time_logs_cap_ratio"] = "100";
                $this->legal_case->set_fields($post_data);
                $this->_set_common_fields();
                $lookup_validate = $this->legal_case->get_lookup_validation_errors($this->legal_case->get("lookupInputsToValidate"), $post_data);
                $estimated_effort = $this->input->post("estimatedEffort");
                $estimated_effort_value = 0;
                if (!empty($estimated_effort)) {
                    $estimated_effort_value = $this->timemask->humanReadableToHours($estimated_effort);
                }
                $this->legal_case->set_field("estimatedEffort", $estimated_effort_value);
                if ($this->legal_case->validate() && !$lookup_validate) {
                    $notify_before = $this->input->post("notify_me_before");
                    if ($notify_before && $this->input->post("dueDate") && (!$notify_before["time"] || !$notify_before["time_type"] || !$notify_before["type"] || ($is_not_nb = !is_numeric($notify_before["time"])))) {
                        if ($is_not_nb) {
                            $response["validationErrors"]["notify_before"] = sprintf($this->lang->line("is_numeric_rule"), $this->lang->line("notify_before"));
                        } else {
                            $response["validationErrors"]["notify_before"] = $this->lang->line("cannot_be_blank_rule");
                        }
                    } else {
                        $assignment_response = $this->save_assignment();
                        if ($this->legal_case->insert()) {
                            $this->increase_count_for_anti_automation_prevention("Litigation_Case");
                            if ($this->input->post("legalCaseRelatedContainerId")) {
                                $this->load->model("legal_case_related_container");
                                $this->load->model("legal_case_container", "legal_case_containerfactory");
                                $this->legal_case_container = $this->legal_case_containerfactory->get_instance();
                                if ($this->legal_case_container->fetch($this->input->post("legalCaseRelatedContainerId")) && $this->input->post("legalCaseRelatedContainerId")) {
                                    $this->legal_case_related_container->set_fields(["legal_case_container_id" => $this->input->post("legalCaseRelatedContainerId"), "legal_case_id" => $this->legal_case->get_field("id")]);
                                    $this->legal_case_related_container->insert();
                                }
                            }
                            
                            ///
                            $legal_case = $this->legal_case->get_fields();
                            $this->legal_case->inject_folder_templates($this->legal_case->get_field("id"), $this->legal_case->get_field("category"), $this->legal_case->get_field("case_type_id"));
                            $this->legal_case->set_fields($legal_case);
                            $case_id = $this->legal_case->get_field("id");
                            //update Criminal case details
                            //load the criminal_case_details
                            $this->load->model("criminal_case_detail","criminal_case_detailfactory");
                            $this->criminal_case_detail=$this->criminal_case_detailfactory->get_instance();
                            //set the fields for criminal case detail: "case_id", "origin_of_case", "offence_subcategory_id", "status_of_case", "initial_entry_document_id",        "authorization_document_id", "date_investigation_authorized"
                            $this->criminal_case_detail->set_field("case_id", $case_id);
                            $this->criminal_case_detail->set_field("origin_of_case", $post_data["approval_step"]==1?"Public Complaint/Inquiry":"Surveillance Detection");
                            $this->criminal_case_detail->set_field("offence_subcategory_id", $post_data["offence_subcategory_id"]??"");
                            $this->criminal_case_detail->set_field("status_of_case", $post_data["status_of_case"]);
                            //$this->criminal_case_detail->set_field("initial_entry_document_id", $post_data["initial_entry_document_id"]);
                            //$this->criminal_case_detail->set_field("authorization_document_id", $post_data["authorization_document_id"]);
                            //$this->criminal_case_detail->set_field("date_investigation_authorized", $post_data["date_investigation_authorized"]);
                            $this->criminal_case_detail->set_field("police_station_reported", $post_data["police_station_reported"]);
                            $this->criminal_case_detail->set_field("police_station_ob_number", $post_data["police_station_ob_number"]);
                            $this->criminal_case_detail->set_field("police_case_file_number", $post_data["police_case_file_number"]);

                            $this->criminal_case_detail->insert();
                            //load the opponent model


                            $this->load->model("opponent");
                            $this->load->model("legal_case_litigation_stages_opponent", "legal_case_litigation_stages_opponentfactory");//legal_case_litigation_stages_opponents stores data for a stage. especially if the different stages represent an appeal
                            $this->legal_case_litigation_stages_opponent = $this->legal_case_litigation_stages_opponentfactory->get_instance();
                            $opponents_data = is_array($opponent_member_types) && !empty($opponent_member_types) ? $this->opponent->get_opponents($opponent_member_types, $opponent_member_ids) : [];
                            if ($post_data["legal_case_stage_id"]) {
                                $this->load->model("legal_case_litigation_detail", "legal_case_litigation_detailfactory");
                                $this->legal_case_litigation_detail = $this->legal_case_litigation_detailfactory->get_instance();
                                $this->legal_case_litigation_detail->set_field("legal_case_id", $case_id);
                                $this->legal_case_litigation_detail->set_field("legal_case_stage", $post_data["legal_case_stage_id"]);
                                $this->legal_case_litigation_detail->set_field("client_position", $post_data["legal_case_client_position_id"]);
                                $this->legal_case_litigation_detail->insert();
                                $stage = $this->legal_case_litigation_detail->get_field("id");
                                $this->legal_case->set_field("stage", $stage);
                                $this->legal_case->update();
                                if (!empty($opponents_data)) {
                                    $stage_opponents = [];
                                    foreach ($opponents_data as $key => $value) {
                                        $stage_opponents[$key]["opponent_id"] = $value["opponent_id"];
                                        $stage_opponents[$key]["stage"] = $stage;
                                        $stage_opponents[$key]["opponent_position"] = isset($opponent_positions[$key]) && !empty($opponent_positions[$key]) ? $opponent_positions[$key] : NULL;
                                    }
                                    $this->legal_case_litigation_stages_opponent->insert_stage_opponents($stage, $stage_opponents);
                                }
                            }
                            if (!empty($opponents_data)) {
                                foreach ($opponents_data as $key => $value) {
                                    $opponents_data[$key]["case_id"] = $case_id;
                                    $opponents_data[$key]["opponent_position"] = isset($opponent_positions[$key]) && !empty($opponent_positions[$key]) ? $opponent_positions[$key] : NULL;
                                }
                                $this->legal_case_opponent->insert_case_opponents($case_id, $opponents_data);
                            }
                            $this->_feed_related_contacts_from_opponent_contact("add");
                            $this->_feed_related_companies_from_opponent_company("add");
                            $response = $this->_submit_add_form("litigation");
                            if (isset($assignment_response["display_message"])) {
                                $response["display_message"] = $assignment_response["display_message"];
                            }
                        }
                        if ($system_preferences["webhooks_enabled"] == 1) {
                            $webhook_data = $this->legal_case->load_case_details($case_id);
                            $this->legal_case->trigger_web_hook("litigation_created", $webhook_data);
                        }
                        $this->legal_case->update_recent_ids($case_id, "litigation_cases");
                    }
                } else {
                    $response["validationErrors"] = $this->legal_case->get_validation_errors($lookup_validate);
                }
            }
        }
        $this->output->set_content_type("application/json")->set_output(json_encode($response));
    }
    /**end criminal Case*/
    public function encrypt_string($string)
    {
        require realpath(__DIR__ . "/../config/config.php");
        $key = hash("sha256", $config["secret_key"]);
        $iv = substr(hash("sha256", $config["secret_iv"]), 0, 16);
        $output = openssl_encrypt($string, $config["encrypt_method"], $key, 0, $iv);
        $output = base64_encode($output);
        return $output;
    }
    private function _set_common_fields()
    {
        $this->legal_case->update_validation_rules();
        $this->legal_case->set_field("archived", "no");
        $this->legal_case->set_field("caseValue", $this->input->post("caseValue") && $this->input->post("caseValue") ? $this->input->post("caseValue") : "0.00");
        $this->legal_case->set_field("recoveredValue", $this->input->post("recoveredValue") && $this->input->post("recoveredValue") ? $this->input->post("recoveredValue") : "0.00");
        $this->legal_case->set_field("judgmentValue", $this->input->post("judgmentValue") && $this->input->post("judgmentValue") ? $this->input->post("judgmentValue") : "0.00");
        $this->legal_case->set_field("estimatedEffort", $this->input->post("estimatedEffort") && $this->input->post("estimatedEffort") ? $this->input->post("estimatedEffort") : "0.00");
        $contact_company_id = $this->input->post("contact_company_id");
        $client_type = $this->input->post("clientType");
        $this->load->model("client");
        $client_id = $contact_company_id ? $this->client->get_client($client_type, $contact_company_id) : NULL;
        $this->legal_case->set_field("client_id", $client_id);
        $this->legal_case->set_field("isDeleted", 0);
        $this->legal_case->set_field("channel", $this->legal_case->get("webChannel"));
        $this->legal_case->set_field("modifiedByChannel", $this->legal_case->get("webChannel"));
    }
    private function _submit_add_form($type)
    {
        $response = [];
        $case_id = $this->legal_case->get_field("id");
        $this->workflow_status->logTransitionHistory($case_id, NULL, $this->legal_case->get_field("case_status_id"), $this->is_auth->get_user_id());
        $this->sla_management_mod->log_case($case_id, $this->legal_case->get_field("case_status_id"), $this->is_auth->get_user_id());
        $this->_feed_related_contacts_from_requested_by("add");
        $this->_feed_related_contacts_from_client_contact("add");
        $this->_feed_related_companies_from_client_company("add");
        $watchers = $this->input->post("case_watchers");
        if ($this->input->post("private") == "yes" && is_array($watchers) && count($watchers)) {
            $case_watchers["users"] = ["legal_case_id" => $case_id, "users" => $watchers];
            $this->legal_case->insert_watchers_users($case_watchers);
        }
        $this->notify_me_before_due_date($case_id);

        $this->load->model("user_profile");
        $assignment = $this->legal_case->get_field("user_id");
        $object = "add_" . $type . "_case";
        $notifications_data["object"] = $object;
        $notifications_data["object_id"] = $case_id;
        $notifications_data["caseSubject"] = $this->legal_case->get_field("subject");
        if ($assignment) {
            $this->load->library("system_notification");
            $notifications_data["to"] = $assignment;
            $notifications_data["objectName"] = strtolower($this->legal_case->get_field("category"));
            $notifications_data["targetUser"] = $assignment;
            $notifications_data["objectModelCode"] = $this->legal_case->get("modelCode");
            $this->system_notification->notification_add($notifications_data);
            $this->load->model("user_rate_per_hour_per_case", "user_rate_per_hour_per_casefactory");
            $this->user_rate = $this->user_rate_per_hour_per_casefactory->get_instance();
            $organizations = $this->user_rate->get_entities();
            $organization_id = $organizations[0]["id"];
            $user_rate = $this->get_rate_by_user_id($assignment, $case_id, $organization_id, true);
            if (0 < $user_rate) {
                $this->user_rate->add_rate($assignment, $case_id, $organization_id, $user_rate);
            }
        }
        $send_email = $this->input->post("send_notifications_email");
        if ($send_email) {
            $this->load->library("email_notifications");
            $created_on = $this->legal_case->get_field("createdOn");
            $assignee = $this->email_notification_scheme->get_user_full_name($assignment);
            $created_by = $this->email_notification_scheme->get_user_full_name($this->legal_case->get_field("createdBy"));
            $model = $this->legal_case->get("_table");
            $notifications_emails = $this->email_notification_scheme->get_emails($object, $model, ["id" => $case_id]);
            extract($notifications_emails);
            $notifications_data["to"] = $to_emails;
            $notifications_data["cc"] = $cc_emails;
            $notifications_data["assignee"] = $assignee;
            $notifications_data["created_by"] = $created_by;
            $notifications_data["created_on"] = $created_on;
            $notifications_data["fromLoggedUser"] = $this->is_auth->get_fullname();
            $notifications_data["file_reference"] = $this->legal_case->get_field("internalReference");
            $notifications_data["filed_on"] = $this->legal_case->get_field("caseArrivalDate");
            $notifications_data["due_date"] = $this->legal_case->get_field("dueDate");
            $notifications_data["litigation_case_court_activity_purpose"] = $this->legal_case->get_field("first_litigation_case_court_activity_purpose");
            $notifications_data["description"] = $this->legal_case->get_field("description");
            $client_info = $this->client->fetch_client($this->legal_case->get_field("client_id"));
            $notifications_data["client_name"] = $client_info["name"]??1;
            $this->email_notifications->notify($notifications_data);
        }
        $client_id = $this->legal_case->get_field("client_id");
        if (isset($client_id)) {
            $this->load->model("client_partner_share");
            $shares = $this->client_partner_share->load_partners_shares($client_id);
            if (0 < count($shares)) {
                $partners_shares = [];
                $this->load->model("legal_case_partner_share");
                foreach ($shares as $key => $partner_share) {
                    $partners_shares[$key]["case_id"] = $case_id;
                    $partners_shares[$key]["account_id"] = $partner_share["id"];
                    $partners_shares[$key]["percentage"] = $partner_share["percentage"];
                }
                if (0 < count($partners_shares)) {
                    $this->legal_case_partner_share->save_partners_shares($case_id, $partners_shares);
                }
            }
        }
        $response["result"] = true;
        $response["caseId"] = str_pad($this->legal_case->get_field("id"), 8, "0", STR_PAD_LEFT);
        $response["modelCode"] = $this->legal_case->get("modelCode");
        $response["records"] = $this->legal_case->get_fields();
        return $response;
    }
    public function fetch_case_client($legal_case_id = NULL)
    {
        if (!$this->input->is_ajax_request()) {
            redirect("dashboard");
        }
        $response = ["clientId" => NULL, "clientName" => NULL];
        if (isset($legal_case_id) && !empty($legal_case_id)) {
            $client_data = $this->legal_case->get_case_client($legal_case_id);
            $response["clientId"] = $client_data["client_id"];
            $response["clientName"] = $client_data["clientName"];
        }
        $this->output->set_content_type("application/json");
        $this->output->set_output(json_encode($response));
    }
    public function delete_case_hearing($id)
    {
        if ($this->input->is_ajax_request()) {
            $this->load->model("legal_case_hearing", "legal_case_hearingfactory");
            $this->legal_case_hearing = $this->legal_case_hearingfactory->get_instance();
            $response["result"] = false;
            if ($this->legal_case_hearing->fetch($id) && !$this->legal_case_hearing->count_related_expenses($id)) {
                $this->legal_case_hearing->set_field("is_deleted", 1);
                $event_id = $this->legal_case_hearing->get_field("task_id");
                $this->legal_case_hearing->set_field("task_id", NULL);
                if ($this->legal_case_hearing->update()) {
                    $this->legal_case_hearing->update_event_id($event_id);
                    $this->load->model("event_attendee");
                    if ($event_id && $this->event_attendee->delete(["where_in" => ["event_id", $event_id]])) {
                        $this->load->model("event", "eventfactory");
                        $this->event = $this->eventfactory->get_instance();
                        $this->event->delete(["where" => ["id", $event_id]]);
                    }
                    $this->load->model("reminder", "reminderfactory");
                    $this->reminder = $this->reminderfactory->get_instance();
                    $this->reminder->delete(["where" => [["legal_case_hearing_id", $id]]]);
                    $this->load->model("mv_hearing");
                    $this->mv_hearing->delete($id);
                    $response["result"] = true;
                }
            }
            $this->output->set_content_type("application/json")->set_output(json_encode($response));
        } else {
            show_404();
        }
    }
    public function add_event()
    {
        if (!$this->input->is_ajax_request()) {
            show_404();
        }
        $data = $errors = $case_event_validation = $calendar_event_validation = $reminder_event_validation = $validation_errors = [];
        $this->load->model("legal_case_event", "legal_case_eventfactory");
        $this->legal_case_event = $this->legal_case_eventfactory->get_instance();
        $this->load->model("legal_case_event_type_form");
        $this->load->model("system_preference");
        $system_preference = $this->system_preference->get_key_groups();
        $ability_set_latest_development = $system_preference["DefaultValues"]["abilitySetLatestDevelopment"];
        if ($this->input->post(NULL)) {
            $fields = $this->input->post("fields");
            $this->legal_case_event->set_fields($this->input->post(NULL));
            $this->legal_case_event->set_field("createdOn", date("Y-m-d H:i:s"));
            $this->legal_case_event->set_field("modifiedOn", date("Y-m-d H:i:s"));
            $this->legal_case_event->set_field("modifiedBy", $this->input->post("createdBy"));
            $this->legal_case_event->set_field("fields", serialize($fields));
            $errors = $this->validate_event_type_fields_values();
            $this->legal_case_event->validate();
            $case_event_validation = $this->legal_case_event->get("validationErrors");
            if ($this->input->post("add_calendar")) {
                $this->load->model("event", "eventfactory");
                $this->event = $this->eventfactory->get_instance();
                $this->load->model("event_attendee");
                $_POST["calendar"]["start_time"] = date("H:i", strtotime($this->input->post("calendar")["start_time"]));
                $_POST["calendar"]["end_time"] = date("H:i", strtotime($this->input->post("calendar")["end_time"]));
                $calendar_details = $this->input->post("calendar");
                $this->event->set_field("legal_case_id", $this->input->post("legal_case"));
                $this->event->set_fields($calendar_details);
                $this->event->validate();
                $calendar_event_validation = $this->event->get("validationErrors");
            }
            if ($this->input->post("reminder")) {
                $this->load->model("reminder", "reminderfactory");
                $this->reminder = $this->reminderfactory->get_instance();
                $this->load->model("reminder_type", "reminder_typefactory");
                $this->reminder_type = $this->reminder_typefactory->get_instance();
                $reminder_details = $this->input->post("reminder");
                $system_preferences = $this->session->userdata("systemPreferences");
                foreach ($reminder_details["user_id"] as $key => $user_id) {
                    $this->reminder->set_field("user_id", $user_id);
                    $this->reminder->set_field("legal_case_id", $this->input->post("legal_case"));
                    $remindDate = isset($system_preferences["hijriCalendarFeature"]) && $system_preferences["hijriCalendarFeature"] ? hijriToGregorian($reminder_details["remindDate"][$key]) : $reminder_details["remindDate"][$key];
                    $this->reminder->set_field("remindDate", $remindDate);
                    $this->reminder->set_field("remindTime", $reminder_details["remindTime"][$key]);
                    $this->reminder->set_field("summary", $reminder_details["summary"][$key]);
                    $this->reminder->set_field("reminder_type_id", $system_preferences["reminderType"]);
                    $this->reminder->validate();
                    $reminder_event_validation = array_merge($reminder_event_validation, $this->reminder->get("validationErrors"));
                    $this->reminder->reset_fields();
                }
            }
            $validation_errors = array_merge($case_event_validation, $calendar_event_validation, $reminder_event_validation) + $errors;
            if (empty($validation_errors)) {
                if ($this->legal_case_event->insert()) {
                    $this->load->model("legal_case_event_related_data", "legal_case_event_related_datafactory");
                    $this->legal_case_event_related_data = $this->legal_case_event_related_datafactory->get_instance();
                    $this->session->set_userdata("last_case_event_type_used", $this->input->post("event_type"));
                    if ($this->input->post("add_calendar") && $this->event->insert()) {
                        $event_id = $this->event->get_field("id");
                        $mandatory = $this->input->post("mandatory") ? $this->input->post("mandatory") : false;
                        $participant = $this->input->post("participant") ? $this->input->post("participant") : false;
                        if (!isset($calendar_details["attendees"]) || !in_array($this->is_auth->get_user_id(), $calendar_details["attendees"])) {
                            $calendar_details["attendees"][] = $this->is_auth->get_user_id();
                            if (!$mandatory) {
                                $mandatory[] = 1;
                            }
                        }
                        $event_data = ["event_id" => $event_id, "attendees" => $calendar_details["attendees"]];
                        $this->event_attendee->insert_attendees($event_data, $mandatory, $participant);
                        $this->event->update_integration_provider_calendar($event_id);
                        $this->legal_case_event_related_data->set_field("event", $this->legal_case_event->get_field("id"));
                        $this->legal_case_event_related_data->set_field("related_id", $event_id);
                        $this->legal_case_event_related_data->set_field("related_object", $this->event->get("modelName"));
                        $this->legal_case_event_related_data->insert();
                        $this->legal_case_event_related_data->reset_fields();
                    }
                    if ($this->input->post("reminder")) {
                        $system_preferences = $this->session->userdata("systemPreferences");
                        foreach ($reminder_details["user_id"] as $key => $user_id) {
                            $this->reminder->set_field("user_id", $user_id);
                            $this->reminder->set_field("legal_case_id", $this->input->post("legal_case"));
                            $remindDate = isset($system_preferences["hijriCalendarFeature"]) && $system_preferences["hijriCalendarFeature"] ? hijriToGregorian($reminder_details["remindDate"][$key]) : $reminder_details["remindDate"][$key];
                            $this->reminder->set_field("remindDate", $remindDate);
                            $this->reminder->set_field("remindTime", $reminder_details["remindTime"][$key]);
                            $this->reminder->set_field("summary", $reminder_details["summary"][$key]);
                            $this->reminder->set_field("reminder_type_id", $system_preferences["reminderType"]);
                            $this->reminder->set_field("status", "Open");
                            $this->reminder->set_field("notify_before_time", $system_preferences["reminderIntervalDate"]);
                            $this->reminder->set_field("notify_before_time_type", $this->reminder->get("default_notify_me_before_time_type"));
                            $this->reminder->set_field("notify_before_type", $this->reminder->get("default_notify_me_before_type"));
                            if ($this->reminder->insert()) {
                                $this->legal_case_event_related_data->set_field("event", $this->legal_case_event->get_field("id"));
                                $this->legal_case_event_related_data->set_field("related_id", $this->reminder->get_field("id"));
                                $this->legal_case_event_related_data->set_field("related_object", $this->reminder->get("modelName"));
                                $this->legal_case_event_related_data->insert();
                                $this->legal_case_event_related_data->reset_fields();
                            }
                            $this->reminder->reset_fields();
                        }
                    }
                    $ability_set_latest_development = $system_preference["DefaultValues"]["abilitySetLatestDevelopment"];
                    if ($ability_set_latest_development && $this->legal_case->fetch($this->input->post("legal_case"))) {
                        $post_latest_development = $this->input->post("latest_development");
                        $this->legal_case->set_field("latest_development", $post_latest_development);
                        $this->legal_case->update();
                    }
                    $this->notify("add_case_event");
                    $response["result"] = true;
                    $response["cloned"] = $this->input->post("clone") === "yes" ? true : false;
                    if ($this->legal_case_event->get_field("stage")) {
                        $this->load->model("legal_case_litigation_detail", "legal_case_litigation_detailfactory");
                        $this->legal_case_litigation_detail = $this->legal_case_litigation_detailfactory->get_instance();
                        $this->legal_case_litigation_detail->update_stage_order($this->legal_case_event->get_field("stage"));
                    }
                    if ($this->legal_case_event->get_field("parent")) {
                        $this->legal_case_event->fetch($this->legal_case_event->get_field("parent"));
                        if ($this->legal_case_event->get_field("stage")) {
                            $this->load->model("legal_case_litigation_detail", "legal_case_litigation_detailfactory");
                            $this->legal_case_litigation_detail = $this->legal_case_litigation_detailfactory->get_instance();
                            $this->legal_case_litigation_detail->update_stage_order($this->legal_case_event->get_field("stage"));
                        }
                    }
                }
            } else {
                $response["validation_errors"] = $validation_errors;
            }
        } else {

            $data["hide_show_notification"] = $this->email_notification_scheme->get_hide_show_send_email_notification_by_trigger_action("add_case_event") == "1" ? "yes" : "";
            if ($this->input->get("event_type")) {
                $response = $this->load_event_type_form($this->input->get("event_type"));
            }
            if ($this->input->get("return_form")) {
                $systemPreferences = $this->session->userdata("systemPreferences");
                $data["hijri_calendar_enabled"] = isset($systemPreferences["hijriCalendarFeature"]) && $systemPreferences["hijriCalendarFeature"] ? $systemPreferences["hijriCalendarFeature"] : 0;
                $data["hijriCalendarConverter"] = isset($systemPreferences["hijriCalendarConverter"]) && $systemPreferences["hijriCalendarConverter"] ? $systemPreferences["hijriCalendarConverter"] : 0;
                $this->load->model("legal_case_event_type");
                $data["title"] = $this->lang->line("add_event");
                $data["ability_set_latest_development"] = $ability_set_latest_development;
                if ($id = $this->input->get("id")) {
                    if ($this->legal_case_event->fetch(["id" => $id])) {
                        $data["event_details"] = $this->legal_case_event->get_fields();
                        $data["event_details"]["createdBy"] = "";
                        $event_types_list = $this->legal_case_event_type->load_data();
                        $data["title"] = $this->lang->line("add_event") . " - " . $event_types_list[$data["event_details"]["event_type"]];
                        $data["event_details"]["event_type"] = "";
                        $data["event_details"]["parent"] = $id;
                        $data["event_types"] = $this->legal_case_event_type->load_sub_event_type();
                    }
                } else {
                    $data["event_types"] = $this->legal_case_event_type->load_data();
                    $data["event_details"] = $this->legal_case_event->get_fields();
                    $data["event_details"]["event_type"] = $this->session->userdata("last_case_event_type_used");
                }
                $response["html"] = $this->load->view("cases/activities/event_form", $data, true);
            }
        }
        $this->output->set_content_type("application/json")->set_output(json_encode($response));
    }
    public function edit_event($event_id)
    {
        if (!$this->input->is_ajax_request()) {
            show_404();
        }
        $data = $errors = [];
        $this->load->model("legal_case_event", "legal_case_eventfactory");
        $this->legal_case_event = $this->legal_case_eventfactory->get_instance();
        $this->load->model("system_preference");
        $system_preference = $this->system_preference->get_key_groups();
        if ($this->input->post(NULL) && $this->legal_case_event->fetch(["id" => $event_id])) {
            $fields = $this->input->post("fields");
            $this->legal_case_event->set_fields($this->input->post(NULL));
            $this->legal_case_event->set_field("fields", serialize($fields));
            $this->load->model("legal_case_event_type_form");
            $errors = $this->validate_event_type_fields_values();
            if ($this->legal_case_event->validate() && empty($errors) && $this->legal_case_event->update()) {
                if ($this->legal_case_event->get_field("stage")) {
                    $this->load->model("legal_case_litigation_detail", "legal_case_litigation_detailfactory");
                    $this->legal_case_litigation_detail = $this->legal_case_litigation_detailfactory->get_instance();
                    $this->legal_case_litigation_detail->update_stage_order($this->legal_case_event->get_field("stage"));
                }
                if ($this->legal_case_event->get_field("parent")) {
                    $this->legal_case_event->reset_fields();
                    $this->legal_case_event->fetch($this->legal_case_event->get_field("parent"));
                    if ($this->legal_case_event->get_field("stage")) {
                        $this->load->model("legal_case_litigation_detail", "legal_case_litigation_detailfactory");
                        $this->legal_case_litigation_detail = $this->legal_case_litigation_detailfactory->get_instance();
                        $this->legal_case_litigation_detail->update_stage_order($this->legal_case_event->get_field("stage"));
                    }
                }
                $this->legal_case_event->reset_fields();
                $this->legal_case_event->fetch(["id" => $event_id]);
                $this->notify("edit_case_event");
                $ability_set_latest_development = $system_preference["DefaultValues"]["abilitySetLatestDevelopment"];
                if ($ability_set_latest_development && $this->legal_case->fetch($this->input->post("legal_case"))) {
                    $post_latest_development = $this->input->post("latest_development");
                    $this->legal_case->set_field("latest_development", $post_latest_development);
                    $this->legal_case->update();
                }
                $response["result"] = true;
            } else {
                $response["validation_errors"] = $this->legal_case_event->get("validationErrors") + $errors;
            }
        } else {
            $this->load->model("legal_case_event_type");
            $data["event_types"] = $this->legal_case_event_type->load_data();

            $data["hide_show_notification"] = $this->email_notification_scheme->get_hide_show_send_email_notification_by_trigger_action("edit_case_event") == "1" ? "yes" : "";
            if ($this->legal_case_event->fetch(["id" => $event_id])) {
                $data["event_details"] = $this->legal_case_event->get_fields();
                $this->load->model("user_profile");
                if ($this->user_profile->fetch(["user_id" => $data["event_details"]["createdBy"]])) {
                    $created_by = $this->user_profile->get_field("firstName") . " " . $this->user_profile->get_field("lastName");
                    $data["created_by_name"] = $this->user_profile->get_field("status") === "Inactive" ? $created_by . "(" . $this->lang->line("Inactive") . ")" : $created_by;
                }
                $event_type_fields = unserialize($data["event_details"]["fields"]);
                $this->load->model("legal_case_event_type_form");
                $response = $this->load_event_type_form($data["event_details"]["event_type"], $event_type_fields);
            }
            $data["ability_set_latest_development"] = $system_preference["DefaultValues"]["abilitySetLatestDevelopment"];
            $data["title"] = $this->lang->line("edit_event");
            $data["id"] = $event_id;
            $this->legal_case->reset_fields();
            $data["latest_development"] = "";
            if (isset($data["event_details"]["legal_case"]) && $this->legal_case->fetch($data["event_details"]["legal_case"])) {
                $data["latest_development"] = $this->legal_case->get_field("latest_development");
            }
            $response["html"] = $this->load->view("cases/activities/event_form", $data, true);
            $response["stage_html"] = $this->return_litigation_stage_html($data["event_details"]["legal_case"], $data["event_details"]["stage"], $event_id);
        }
        $this->output->set_content_type("application/json")->set_output(json_encode($response));
    }
    public function delete_event()
    {
        $this->load->model("legal_case_event", "legal_case_eventfactory");
        $this->legal_case_event = $this->legal_case_eventfactory->get_instance();
        $id = $this->input->post("id");
        if (0 < $this->legal_case_event->count_related_data($id)) {
            $response["status"] = false;
        } else {
            $response["result"] = $this->legal_case_event->delete($id) ? true : false;
        }
        $this->output->set_content_type("application/json")->set_output(json_encode($response));
    }
    public function events($id = 0)
    {
        $this->load->model("legal_case_event", "legal_case_eventfactory");
        $this->legal_case_event = $this->legal_case_eventfactory->get_instance();
        $this->load->model("legal_case_hearing", "legal_case_hearingfactory");
        $this->legal_case_hearing = $this->legal_case_hearingfactory->get_instance();
        $this->load->model("legal_case_litigation_detail", "legal_case_litigation_detailfactory");
        $this->legal_case_litigation_detail = $this->legal_case_litigation_detailfactory->get_instance();
        $data = [];
        $systemPreferences = $this->session->userdata("systemPreferences");
        $data["is_rtl"] = $this->session->userdata("AUTH_language") == "arabic";
        if ($this->input->is_ajax_request()) {
            $page_to = $this->input->post("pageTo");
            $params = $this->input->post("params");
            $return_count = $this->input->post("returnCount");
            $case_id = isset($params["id"]) ? (int) $params["id"] : 0;
            $stage_id = isset($params["stageId"]) ? $params["stageId"] : "";
            $page_number = isset($params["pageNumber"]) ? $params["pageNumber"] : 1;
            $page_limit = isset($params["pageLimit"]) ? $params["pageLimit"] : 10;
            $order_descending = isset($params["orderDes"]) ? $params["orderDes"] : "desc";
            $data["stage_id"] = $stage_id;
            $data["case_id"] = $case_id;
            $response = [];
            switch ($page_to) {
                case "events":
                    $response["status"] = true;
                    if ($return_count) {
                        $response["result"] = $this->legal_case_event->load_stage_events($case_id, $stage_id, true);
                    } else {
                        $data = $this->legal_case_event->load_stage_events($case_id, $stage_id);
                        $data["case_id"] = $case_id;
                        $data["stage_id"] = $stage_id;
                        $data["details"] = [];
                        $data["stage_name"] = "";
                        $count_events = 0;
                        if (!empty($stage_id)) {
                            if (isset($data["activities"][$stage_id]["events"])) {
                                $data["events"] = $data["activities"][$stage_id]["events"];
                            }
                            $stage = $this->legal_case_litigation_detail->load_all_stages_metadata($case_id, 0, 0, [], $stage_id);
                            $data["stage_name"] = isset($stage[0]) ? $stage[0]["stage"] : "";
                        } else {
                            if (isset($data["activities"][0]["events"])) {
                                $data["events"] = $data["activities"][0]["events"];
                            }
                        }
                        $this->legal_case->fetch($case_id);
                        $legal_case = $this->legal_case->get_fields();
                        $data["legalCase"] = $legal_case;
                        $data["systemPreferences"] = $systemPreferences;
                        $data["case_subject"] = $this->legal_case->get("modelCode") . $case_id . ": " . (39 < strlen($legal_case["subject"]) ? mb_substr($legal_case["subject"], 0, 39) . "..." : $legal_case["subject"]);
                        $data["is_rtl"] = $this->session->userdata("AUTH_language") == "arabic";
                        $data["hijri_calendar_enabled"] = isset($systemPreferences["hijriCalendarFeature"]) && $systemPreferences["hijriCalendarFeature"] ? $systemPreferences["hijriCalendarFeature"] : 0;
                        $response["html"] = $this->load->view("cases/activities/events", $data, true);
                    }
                    break;
                case "hearings":
                    $response["status"] = true;
                    $order = empty($order_descending) || $order_descending == "desc" ? [] : ["legal_case_hearings.startDate, legal_case_hearings.startTime"];
                    $data["page_limit"] = (int) $page_limit;
                    $data_hearings = $this->legal_case_litigation_detail->load_hearings_per_stage(false, $case_id, $order, $stage_id, true, $page_number, false, $page_limit);
                    $data["hearings"] = $data_hearings["hearings"];
                    $data["total_rows"] = $data_hearings["totalRows"];
                    $data["stage"] = $this->legal_case_litigation_detail->load_activities($case_id, true, true, $stage_id);
                    $data["stage"] = $data["stage"][0] ?? [];
                    $data["stage_name"] = $data["hearings"][0]["stage_name"] ?? "";
                    $data["page_number"] = $page_number;
                    $this->legal_case->fetch($case_id);
                    $legal_case = $this->legal_case->get_fields();
                    $data["legalCase"] = $legal_case;
                    $data["hearings_model_code"] = $this->legal_case_hearing->modelCode;
                    $data["systemPreferences"] = $systemPreferences;
                    if (isset($systemPreferences["AllowFeatureHearingVerificationProcess"]) && $systemPreferences["AllowFeatureHearingVerificationProcess"] == "yes") {
                        $data["verification_process_enabled"] = true;
                    } else {
                        $data["verification_process_enabled"] = false;
                    }
                    $data["external_court_ref"] = isset($systemPreferences["AllowExternalCourtRef"]) && $systemPreferences["AllowExternalCourtRef"] == 1 ? $systemPreferences["ExternalCourtRefLink"] : false;
                    $hijri_calendar_enabled = isset($systemPreferences["hijriCalendarFeature"]) && $systemPreferences["hijriCalendarFeature"] ? $systemPreferences["hijriCalendarFeature"] : 0;
                    $data["hijri_calendar_enabled"] = $hijri_calendar_enabled;
                    $response["totalRows"] = $data["total_rows"];
                    $response["html_upcoming_last_attending"] = $this->load->view("cases/activities/upcoming_last_attending", $data, true);
                    $response["html"] = $this->load->view("cases/activities/hearings", $data, true);
                    break;
                case "reminders":
                    $response["status"] = true;
                    $this->load->model("legal_case_event_related_data", "legal_case_event_related_datafactory");
                    $this->legal_case_event_related_data = $this->legal_case_event_related_datafactory->get_instance();
                    $order = empty($order_descending) || $order_descending == "desc" ? [] : ["reminders.remindDate, reminders.remindTime"];
                    $data["page_limit"] = (int) $page_limit;
                    $data_reminders = $this->legal_case_event_related_data->load_reminders([], $case_id, false, $order, $page_number, true, $page_limit);
                    $data["reminders"] = $data_reminders["reminders"];
                    $systemPreferences = $this->session->userdata("systemPreferences");
                    if (isset($systemPreferences["hijriCalendarFeature"]) && $systemPreferences["hijriCalendarFeature"]) {
                        foreach ($data["reminders"] as $key => &$val) {
                            $val["remindDate"] = gregorianToHijri($val["remindDate"], "Y-m-d");
                        }
                    }
                    $data["total_rows"] = $data_reminders["totalRows"];
                    $data["page_number"] = $page_number;
                    $this->legal_case->fetch($case_id);
                    $legal_case = $this->legal_case->get_fields();
                    $data["legalCase"] = $legal_case;
                    $data["systemPreferences"] = $systemPreferences;
                    $data["case_id"] = $case_id;
                    $response["html"] = $this->load->view("cases/activities/reminders", $data, true);
                    break;
                case "stages":
                    $response["status"] = true;
                    $data["stages"] = $this->legal_case_litigation_detail->load_activities($case_id, true, true);
                    $data["case_id"] = $case_id;
                    $data["systemPreferences"] = $systemPreferences;
                    $hijri_calendar_enabled = isset($systemPreferences["hijriCalendarFeature"]) && $systemPreferences["hijriCalendarFeature"] ? $systemPreferences["hijriCalendarFeature"] : 0;
                    $data["hijri_calendar_enabled"] = $hijri_calendar_enabled;
                    $data["external_court_ref"] = isset($systemPreferences["AllowExternalCourtRef"]) && $systemPreferences["AllowExternalCourtRef"] == 1 ? $systemPreferences["ExternalCourtRefLink"] : false;
                    $response["stages_data"] = $data["stages"];
                    $this->legal_case->fetch($case_id);
                    $legal_case = $this->legal_case->get_fields();
                    $data["legalCaseArchived"] = $legal_case["archived"];
                    $data["disableArchivedMatters"] = $systemPreferences["disableArchivedMatters"];
                    $data["case_subject"] = $this->legal_case->get("modelCode") . $legal_case["id"] . ": " . $legal_case["subject"];
                    $response["html"] = $this->load->view("cases/activities/stages", $data, true);
                    break;
                case "tasks":
                    if ($return_count) {
                        $this->load->model("task", "taskfactory");
                        $this->task = $this->taskfactory->get_instance();
                        $order = empty($order_descending) || $order_descending == "desc" ? [] : ["tasks.due_date"];
                        $response["result"] = $this->task->load_tasks_on_stage_directly($case_id, $order, false, $stage_id, $page_number, true, $page_limit, true);
                    } else {
                        $response["status"] = true;
                        $this->load->model("task", "taskfactory");
                        $this->task = $this->taskfactory->get_instance();
                        $this->load->model("language");
                        $order = empty($order_descending) || $order_descending == "desc" ? [] : ["tasks.due_date"];
                        $data["page_limit"] = (int) $page_limit;
                        $data_tasks = $this->task->load_tasks_on_stage_directly($case_id, $order, $return_count ? false : true, $stage_id, $page_number, true, $page_limit);
                        $data["tasks"] = $data_tasks["tasks"];
                        $data["total_rows"] = $data_tasks["totalRows"];
                        $data["case_id"] = $case_id;
                        $data["stage_id"] = $stage_id;
                        $this->legal_case->fetch($case_id);
                        $legal_case = $this->legal_case->get_fields();
                        $data["legalCase"] = $legal_case;
                        $data["systemPreferences"] = $systemPreferences;
                        $data["stage"] = $this->legal_case_litigation_detail->load_activities($case_id, false, false, $stage_id);
                        $data["stage"] = $data["stage"][0] ?? [];
                        $data["page_number"] = $page_number;
                        $data["order_descending"] = empty($order_descending) || $order_descending == "desc" ? "asc" : "desc";
                        $response["html"] = $this->load->view("cases/activities/tasks", $data, true);
                    }
                    break;
                case "sub_events":
                    $response["status"] = true;
                    $event_id = isset($params["eventId"]) ? $params["eventId"] : "";
                    $data = $this->legal_case_event->load_all_events($case_id, $stage_id);
                    $data["case_id"] = $case_id;
                    $data["stage_id"] = (int) $stage_id;
                    $data["stage_name"] = "";
                    $stage_id = $stage_id ? $stage_id : 0;
                    $data["sub_events_related_reminders"] = [];
                    $data["sub_events_related_tasks"] = $data["sub_events_related_reminders"];
                    $data["sub_events"] = $data["sub_events_related_tasks"];
                    $data["details"] = $data["sub_events"];
                    if (isset($data["activities"][$stage_id]["events"])) {
                        foreach ($data["activities"][$stage_id]["events"] as $key_event => $event) {
                            if ($event["id"] == $event_id) {
                                if (isset($data["activities"][$stage_id]["events"][$key_event]["sub_events"])) {
                                    $data["sub_events"] = $data["activities"][$stage_id]["events"][$key_event]["sub_events"];
                                }
                                if (isset($data["activities"][$stage_id]["events"][$key_event]["sub_events_related_reminders"])) {
                                    $data["sub_events_related_reminders"] = $data["activities"][$stage_id]["events"][$key_event]["sub_events_related_reminders"];
                                }
                                if (isset($data["activities"][$stage_id]["events"][$key_event]["sub_events_related_tasks"])) {
                                    $data["sub_events_related_tasks"] = $data["activities"][$stage_id]["events"][$key_event]["sub_events_related_tasks"];
                                }
                                $data["event_data"] = $data["activities"][$stage_id]["events"][$key_event];
                            }
                        }
                    }
                    $stage = $this->legal_case_litigation_detail->load_all_stages_metadata($case_id, 0, 0, [], $stage_id);
                    $data["stage_name"] = isset($stage[0]) ? $stage[0]["stage"] : "";
                    $this->legal_case->fetch($case_id);
                    $legal_case = $this->legal_case->get_fields();
                    $data["legalCase"] = $legal_case;
                    $data["systemPreferences"] = $systemPreferences;
                    $data["case_subject"] = $this->legal_case->get("modelCode") . $case_id . ": " . (39 < strlen($legal_case["subject"]) ? mb_substr($legal_case["subject"], 0, 39) . "..." : $legal_case["subject"]);
                    $data["is_rtl"] = $this->session->userdata("AUTH_language") == "arabic";
                    $data["hijri_calendar_enabled"] = isset($systemPreferences["hijriCalendarFeature"]) && $systemPreferences["hijriCalendarFeature"] ? $systemPreferences["hijriCalendarFeature"] : 0;
                    $response["html"] = $this->load->view("cases/activities/sub_events", $data, true);
                    break;
                case "judge":
                    $response["status"] = true;
                    $data["judges"] = $this->legal_case_litigation_detail->load_stage_contacts($stage_id, "judge");
                    $data["judges"] = !empty($data["judges"]["data"]) ? $data["judges"]["data"] : [];
                    $response["html"] = $this->load->view("cases/activities/judge_window", $data, true);
                    break;
                case "opponent_lawyer":
                    $response["status"] = true;
                    $data["opponent_lawyers"] = $this->legal_case_litigation_detail->load_stage_contacts($stage_id, "opponent-lawyer");
                    $data["opponent_lawyers"] = !empty($data["opponent_lawyers"]["data"]) ? $data["opponent_lawyers"]["data"] : [];
                    $response["html"] = $this->load->view("cases/activities/opponent_lawyer_window", $data, true);
                    break;
                case "document_dialog":
                    $data = [];
                    $response["status"] = true;
                    $data["type"] = $this->input->post("document_type");
                    $related_document_id = $this->input->post("related_document_id");
                    $data["related_document_id"] = $related_document_id;
                    $data["documents"] = $this->load_stages_documents($data["type"], $related_document_id);
                    $response["html"] = $this->load->view("cases/activities/documents_window", $data, true);
                    break;
                case "filter":
                    $this->load->model("legal_case_event_type");
                    $data["event_types"] = $this->legal_case_event_type->load_data() + ["" => $this->lang->line("all")];
                    if ($this->input->post("submit")) {
                        $filter = unserialize($this->user_preference->get_value("legal_case_events_filter"));
                        $filter[$case_id] = $this->input->post(NULL);
                        $response["result"] = $this->user_preference->set_value("legal_case_events_filter", serialize($filter), true);
                    } else {
                        $filter = unserialize($this->user_preference->get_value("legal_case_events_filter"));
                        $data["filter"] = isset($filter[$case_id]) ? $filter[$case_id] : false;
                        $response["html"] = $this->load->view("cases/activities/filter", $data, true);
                    }
                    break;
                default:
            }
            $this->output->set_content_type("application/json")->set_output(json_encode($response));

        } else {
            $data = [];
            $data["tabsNLogs"] = $this->get_vertical_case_tabs($id, site_url("cases/events/"));
            $this->legal_case->fetch($id);
            $legal_case = $this->legal_case->get_fields();
            $legal_case["Status"] = $this->legal_case->get_case_status($legal_case["case_status_id"]);
            $data["legalCase"] = $legal_case;
            $data["systemPreferences"] = $systemPreferences;
            $data["case_id"] = $id;
            $data["case_subject"] = $this->legal_case->get("modelCode") . $id . ": " . (39 < strlen($legal_case["subject"]) ? mb_substr($legal_case["subject"], 0, 39) . "..." : $legal_case["subject"]);
            $this->load->model("user", "userfactory");
            $this->user = $this->userfactory->get_instance();
            $this->includes("scripts/litigation_stage", "js");
            $this->includes("customerPortal/clientPortal/css/datatables.min", "css");
            $this->includes("customerPortal/clientPortal/js/datatables.min", "js");
            $this->includes("paginationjs/jquery-pagination-lgh.min", "css");
            $this->includes("paginationjs/jquery-pagination-lgh.min", "js");
            $this->includes("scripts/legal_case_list_hearings", "js");
            $this->includes("scripts/show_hide_customer_portal", "js");
            $this->includes("jquery/spectrum", "js");
            $this->includes("scripts/general", "js");
            $this->includes("scripts/legal_case_events", "js");
            $data["case_assignee"] = $this->user->get_name_by_id($data["legalCase"]["user_id"]);
            if ($legal_case["category"] != "Litigation") {
                $this->set_flashmessage("error", $this->lang->line("invalid_record"));
                redirect("dashboard");
            }
            $this->load->view("partial/header");
            $this->load->view("cases/activities/index", $data);
        }
    }
    private function load_stages_documents($type, $record_id)
    {
        $documents = [];
        switch ($type) {
            case "hearing":
                $this->load->model("hearing_document", "hearing_documentfactory");
                $this->hearing_document = $this->hearing_documentfactory->get_instance();
                $documents = $this->hearing_document->load_all_attachments($record_id);
                break;
            case "task":
                $this->load->model("task", "taskfactory");
                $this->task = $this->taskfactory->get_instance();
                $task_documents = $this->task->load_tasks_documents($record_id);
                $documents = [];
                if (isset($task_documents["data"])) {
                    $documents = $task_documents["data"];
                }
                break;
            case "comment":
                $this->load->model("case_comment", "case_commentfactory");
                $this->case_comment = $this->case_commentfactory->get_instance();
                $documents = $this->case_comment->get_attachments_for_comment_dialog($record_id);
                break;
            default:
        }
        return $documents;

    }
    private function notify($object_type)
    {
        $event_id = $this->legal_case_event->get_field("id");
        $this->load->library("system_notification");
        $this->legal_case->fetch(["id" => $this->input->post("legal_case")]);
        $assignee = $this->legal_case->get_field("user_id");
        $creator_id = [str_pad($this->legal_case_event->get_field("createdBy"), 10, "0", STR_PAD_LEFT)];
        $this->load->model("user", "userfactory");
        $this->user = $this->userfactory->get_instance();
        $this->user->fetch($this->legal_case_event->get_field("createdBy"));
        $creator_email = [$this->user->get_field("email")];
        $action_maker = $object_type === "add_case_event" ? $this->user->get_name_by_id($this->legal_case_event->get_field("createdBy")) : $this->user->get_name_by_id($this->legal_case_event->get_field("modifiedBy"));
        $required_fields = $this->legal_case_event->return_static_fields($this->input->post("event_type"), "subject");
        $notifications_data = ["to" => $assignee, "cc" => $creator_email, "ccIds" => $creator_id, "object" => $object_type, "object_id" => $event_id, "targetUser" => $assignee, "secondTargetUser" => $creator_id, "subject" => $required_fields["subject"], "objectModelCode" => $this->legal_case->get("modelCode"), "created_on" => $this->legal_case_event->get_field("createdOn"), "modified_on" => $this->legal_case_event->get_field("modifiedOn"), "modified_by" => $this->legal_case_event->get_field("modifiedBy"), "description" => $this->legal_case->get_field("description"), "legal_case_subject" => $this->legal_case->get_field("subject"), "legal_case_object_id" => $this->legal_case->get("modelCode") . $this->legal_case->get_field("id"), "action_maker" => $action_maker["name"]];
        if ($assignee) {
            $this->system_notification->notification_add($notifications_data);
        }
        if ($this->input->post("send_notifications_email")) {

            $this->load->library("email_notifications");
            $model = $this->legal_case->get("_table");
            $model_data["id"] = $this->input->post("legal_case");
            $model_data["watchers_ids"] = $assignee;
            $notifications_emails = $this->email_notification_scheme->get_emails($object_type, $model, $model_data);
            extract($notifications_emails);
            $notifications_data["to"] = $to_emails;
            $notifications_data["cc"] = $cc_emails;
            $notifications_data["object_id"] = (int) $event_id;
            $notifications_data["fromLoggedUser"] = $this->is_auth->get_fullname();
            $this->email_notifications->notify($notifications_data);
        }
    }
    private function load_event_type_form($id, $field_values = [])
    {
        $data = [];
        $data["fields"] = $this->legal_case_event_type_form->get_event_type_fields($id);
        $data["lookup_types_details"] = $this->legal_case_event_type_form->get("lookup_types_details");
        $data["field_values"] = $field_values;
        if (!empty($field_values)) {
            foreach ($data["fields"] as $key => $field) {
                if (in_array($field["field_type"], $this->legal_case_event_type_form->get("_link_type_options"))) {
                    $field["field_value"] = isset($field_values[$field["id"]]) && $field_values[$field["id"]] ? $field_values[$field["id"]] : "";
                    $data["fields"][$key]["field_value"] = $this->legal_case_event->load_lookup_fields_data($field);
                }
            }
        }
        $response["fields_html"] = $this->load->view("templates/form_builder", $data, true);
        return $response;
    }
    private function validate_event_type_fields_values()
    {
        $errors = [];
        $fields = $this->input->post("fields");
        $event_type = $this->input->post("event_type");
        $event_type_fields = $this->legal_case_event_type_form->get_event_type_fields($event_type);
        foreach ($event_type_fields as $field_details) {
            if ($field_details["field_required"]) {
                if (!isset($fields[$field_details["id"]]) || empty($fields[$field_details["id"]])) {
                    $errors[$field_details["id"]] = $this->lang->line("cannot_be_blank_rule");
                }
                if (isset($fields[$field_details["id"]]) && !empty($fields[$field_details["id"]]) && $field_details["field_type"] == "date_time" && (!$fields[$field_details["id"]]["date"] || !$fields[$field_details["id"]]["time"])) {
                    $errors[$field_details["id"]] = $this->lang->line("cannot_be_blank_rule");
                }
            }
        }
        return $errors;
    }
    public function hearings_autocomplete()
    {
        if (!$this->input->is_ajax_request()) {
            show_404();
        }
        if (!$this->is_auth->is_logged_in()) {
            exit("login_needed");
        }
        $this->load->model("legal_case_hearing", "legal_case_hearingfactory");
        $this->legal_case_hearing = $this->legal_case_hearingfactory->get_instance();
        $term = trim((string) $this->input->get("term"));
        $systemPreferences = $this->session->userdata("systemPreferences");
        $hijri_calendar_enabled = isset($systemPreferences["hijriCalendarFeature"]) && $systemPreferences["hijriCalendarFeature"] ? $systemPreferences["hijriCalendarFeature"] : 0;
        $results = $this->legal_case_hearing->lookup($term, $this->input->get("more_filters"), $hijri_calendar_enabled);
        if ($hijri_calendar_enabled) {
            foreach ($results as $key => $data) {
                $text = explode(" ", $data["subject"]);
                $hijri_date = gregorianToHijri($text[0], "Y-m-d");
                $results[$key]["subject"] = str_replace($text[0], $hijri_date, $data["subject"]);
            }
        }
        $this->output->set_content_type("application/json");
        $this->output->set_output(json_encode($results));
    }
    public function events_autocomplete()
    {
        if (!$this->input->is_ajax_request()) {
            show_404();
        }
        if (!$this->is_auth->is_logged_in()) {
            exit("login_needed");
        }
        $this->load->model("legal_case_event", "legal_case_eventfactory");
        $this->legal_case_event = $this->legal_case_eventfactory->get_instance();
        $term = trim((string) $this->input->get("term"));
        $results = $this->legal_case_event->lookup($term, $this->input->get("more_filters"));
        $this->output->set_content_type("application/json");
        $this->output->set_output(json_encode($results));
    }
    private function notify_me_before_due_date($case_id)
    {
        $notify_before = $this->input->post("notify_me_before");
        $due_date = $this->input->post("dueDate");
        if (empty($this->reminder)) {
            $this->load->model("reminder", "reminderfactory");
            $this->reminder = $this->reminderfactory->get_instance();
        }
        $current_reminder = $this->reminder->load_notify_before_data_to_related_object($case_id, $this->legal_case->get("_table"));
        if ($current_reminder && !$notify_before) {
            return $this->reminder->remind_before_due_date([], $current_reminder["id"]);
        }
        if ($notify_before && $due_date) {
            $reminder = ["user_id" => $this->is_auth->get_user_id(), "remindDate" => $due_date, "legal_case_id" => $case_id, "related_id" => $case_id, "related_object" => $this->legal_case->get("_table"), "notify_before_time" => $notify_before["time"], "notify_before_time_type" => $notify_before["time_type"], "notify_before_type" => $notify_before["type"]];
            $reminder["summary"] = sprintf($this->lang->line("notify_me_before_message"), $this->lang->line("legal_matter"), $this->legal_case->get("modelCode") . $case_id, $due_date);
            return $this->reminder->remind_before_due_date($reminder, isset($notify_before["id"]) ? $notify_before["id"] : NULL);
        }else{ //else statement by atinga
            return false;
        }
    }
    public function generate_document($matter_id)
    {
        $this->load->model("doc_generator");
        $template_folder_path = $this->doc_generator->get_value_by_key("template_folder_path");
        if (!$template_folder_path || !$matter_id) {
            $error_msg = !$template_folder_path ? $this->lang->line("template_folder_path_is_not_specified") : $this->lang->line("invalid_record");
            if ($this->input->is_ajax_request()) {
                $response = ["result" => false, "error" => $error_msg];
                $this->output->set_content_type("application/json")->set_output(json_encode($response));
            } else {
                $this->set_flashmessage("warning", $error_msg);
                redirect("cases/documents/" . $matter_id);
            }
        } else {
            $this->load->library("dms");
            $template_record = $this->dms->model->get_document_details(["id" => $template_folder_path]);
            $this->legal_case->fetch($matter_id);
            $response = $this->dms->generate_document($template_record, "legal_case", $matter_id, $this->legal_case->get_field("category"), "case");
            $this->output->set_content_type("application/json")->set_output(json_encode($response));
        }
    }
/**
 * @returns feenotes and expenses related to external counsel
 * should be based on a  matter per legal counsel. have matter id
*/
public function external_counsel_expenses()
{
    redirect("modules/money/vouchers/bills_list");

}
    /**
     * @return expenses incured by a case. includes fee notes etc
     */
    public function load_case_expense_widgets()
    {
$response=[];
        $this->output->set_content_type("application/json")->set_output(json_encode($response));
    }
    public function load_client_widgets($legal_case_id = "")
    {
        if (!$this->input->is_ajax_request()) {
            show_404();
        }
        $this->load->model("common", "commonfactory");
        $this->load->model("legal_case", "legal_casefactory");
        $this->common = $this->commonfactory->get_instance();
        $this->legal_case = $this->legal_casefactory->get_instance();
        $case_currency_id = $this->common->load_system_currency_id();
        $system_preferences = $this->session->userdata("systemPreferences");
        if (empty($legal_case_id)) {
            $response["amount"] = $this->common->load_client_trust_accounts($this->input->get("client_id"), $case_currency_id) . " " . $system_preferences["caseValueCurrency"];
            $client_transactions = $this->common->load_client_amount_transactions($this->input->get("client_id"), $this->input->get("case_id"), $case_currency_id);
            $response["account_transactions"]["due_balance"] = $client_transactions["due_balance"] . " " . $system_preferences["caseValueCurrency"];
            $response["account_transactions"]["paid_balance"] = $client_transactions["paid_balance"] . " " . $system_preferences["caseValueCurrency"];
            $logs = $this->common->load_client_billable_logs($this->input->get("client_id"), $this->input->get("case_id"), $case_currency_id, $this->input->get("organization"));
            $logs_amount = $logs["amount"];
            $logs["amount"] = number_format($logs_amount, 2) . " " . $system_preferences["caseValueCurrency"];
            $expenses = $this->common->load_client_billable_expenses($this->input->get("client_id"), $this->input->get("case_id"), $case_currency_id);
            $response["billable"] = ["total" => number_format($logs_amount + $expenses, 2) . " " . $system_preferences["caseValueCurrency"], "logs" => $logs, "expenses" => number_format($expenses, 2) . " " . $system_preferences["caseValueCurrency"]];
            $response["user_rate_per_hour"] = $this->common->get_user_rate_hour($this->input->get("case_id"));
            $this->legal_case->fetch($this->input->get("case_id"));
        } else {
            $response["result"] = true;
            $this->legal_case->fetch($legal_case_id);
        }
        if ($this->legal_case->get_field("id")) {
            $legal_case_fields = $this->legal_case->get_fields();
            $cap_expenses_amount = $this->legal_case->get_cap_expenses_amount($legal_case_fields["client_id"], $legal_case_fields["id"], $case_currency_id, $this->legal_case->get_field("expenses_cap_ratio"));
            $cap_time_logs_amount = $this->legal_case->get_cap_time_logs_amount($legal_case_fields["client_id"], $legal_case_fields["id"], $case_currency_id, $this->legal_case->get_field("time_logs_cap_ratio"));
            $response["capping"]["capping_amount_enable"] = $legal_case_fields["cap_amount_enable"];
            $response["capping"]["capping_amount"] = number_format($legal_case_fields["cap_amount"], 2) . " " . $system_preferences["caseValueCurrency"];
            $cap_time_logs_amount = !empty($cap_time_logs_amount) ? $cap_time_logs_amount : "0.00";
            $cap_expenses_amount = !empty($cap_expenses_amount) ? $cap_expenses_amount : "0.00";
            $response["capping"]["cap_time_logs_amount"] = $cap_time_logs_amount . " " . $system_preferences["caseValueCurrency"];
            $response["capping"]["cap_expenses_amount"] = $cap_expenses_amount . " " . $system_preferences["caseValueCurrency"];
            $response["capping"]["time_logs_cap_ratio_percentage"] = sprintf($this->lang->line("time_logs_cap_ratio_percentage"), $legal_case_fields["time_logs_cap_ratio"]) . "%";
            $response["capping"]["expenses_cap_ratio_percentage"] = sprintf($this->lang->line("expenses_cap_ratio_percentage"), $legal_case_fields["expenses_cap_ratio"]) . "%";
            $response["capping"]["remaining_cap_amount"] = $this->legal_case->get_total_remaining_cap_amount($cap_time_logs_amount, $cap_expenses_amount, $legal_case_fields["cap_amount"]);
        }
        $this->output->set_content_type("application/json")->set_output(json_encode($response));
    }
    private function _feed_related_contacts_from_referred_by($mode)
    {
        $referredBy = $this->input->post("referredBy");
        if (!$referredBy) {
            return true;
        }
        $caseId = $this->legal_case->get_field("id");
        $this->load->model("legal_case_contact");
        $LegalCaseContactData = ["contact_id" => $referredBy, "case_id" => $caseId, "contactType" => "contact"];
        $this->legal_case_contact->reset_fields();
        $referredByInRelatedContacts = $this->legal_case_contact->fetch($LegalCaseContactData);
        if ($referredByInRelatedContacts) {
            return true;
        }
        $this->legal_case_contact->reset_fields();
        $this->legal_case_contact->set_fields($LegalCaseContactData);
        $this->legal_case_contact->insert();
        $this->legal_case_contact->reset_fields();
    }
    public function matter_stage_metadata($case_id, $stage_id = 0)
    {
        if (!$this->input->is_ajax_request()) {
            show_404();
        }
        $response["html"] = $this->return_litigation_stage_html($case_id, $stage_id);
        $this->output->set_content_type("application/json")->set_output(json_encode($response));
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
    public function change_litigation_stage($case_id, $litigation_stage = 0)
    {
        $this->authenticate_exempted_actions();
        if (!$this->input->is_ajax_request()) {
            show_404();
        }
        $response = [];
        $this->load->model("legal_case_litigation_stages_opponent", "legal_case_litigation_stages_opponentfactory");
        $this->legal_case_litigation_stages_opponent = $this->legal_case_litigation_stages_opponentfactory->get_instance();
        $this->load->model("legal_case_litigation_detail", "legal_case_litigation_detailfactory");
        $this->legal_case_litigation_detail = $this->legal_case_litigation_detailfactory->get_instance();
        $this->load->model("legal_case_stage", "legal_case_stagefactory");
        $this->legal_case_stage = $this->legal_case_stagefactory->get_instance();
        $this->legal_case_litigation_detail->fetch($litigation_stage);
        $old_status = $this->legal_case_litigation_detail->get_field("status");
        $data = [];
        $system_preferences = $this->session->userdata("systemPreferences");
        $hijri_calendar_enabled = isset($system_preferences["hijriCalendarFeature"]) && $system_preferences["hijriCalendarFeature"] ? $system_preferences["hijriCalendarFeature"] : 0;
        $case_data = $this->legal_case->load_case($case_id);
        $data["stages"] = $this->legal_case_stage->load_list_per_case_category_per_language($case_data['category']??'litigation');
        if (!$this->input->post(NULL)) {
            $data["litigationStageData"] = $this->legal_case_litigation_detail->get_fields();
            $data["litigationStageData"]["legal_case_id"] = $case_id;
            $data["statuses"] = [];
            $this->load->model(["court", "court_type", "court_region", "court_degree"]);
            $data["court_types"] = $this->court_type->load_list([], ["firstLine" => ["" => ""]]);
            $data["court_degrees"] = $this->court->load_degrees_list(["firstLine" => ["" => ""]]);
            $data["court_regions"] = $this->court->load_regions_list(["firstLine" => ["" => ""]]);
            $data["courts"] = $this->court->load_courts_list(["firstLine" => ["" => ""]]);
            $data["system_preferences"] = $system_preferences;
            
            $data["from_stage"] = $data["stages"][$case_data["legal_case_stage_id"]];
            $this->load->model("legal_case_client_position", "legal_case_client_positionfactory");
            $this->legal_case_client_position = $this->legal_case_client_positionfactory->get_instance();
            $data["client_positions"] = $this->legal_case_client_position->load_list_per_language();
            $this->load->model("stage_status_language", "stage_status_languagefactory");
            $this->stage_status_language = $this->stage_status_languagefactory->get_instance();
            $data["statuses"] = $this->stage_status_language->load_list_per_language();
            $data["judges"] = [];
            $data["opponent_lawyers"] = [];
            $data["external_references"] = [];
            if ($litigation_stage) {
                $data["judges"] = $this->legal_case_litigation_detail->load_stage_contacts($litigation_stage, "judge");
                $data["opponent_lawyers"] = $this->legal_case_litigation_detail->load_stage_contacts($litigation_stage, "opponent-lawyer");
                $data["external_references"] = $this->legal_case_litigation_detail->k_load_all_external_references($litigation_stage);
                $data["title"] = $this->lang->line("edit_stage");
                $data["selected_client_poistion"] = "";
            } else {
                $data["title"] = $this->lang->line("change_litigation_stage");
                $data["selected_client_poistion"] = $case_data["legal_case_client_position_id"] ?? "";
            }
            $this->load->model("legal_case_opponent", "legal_case_opponentfactory");
            $this->legal_case_opponent = $this->legal_case_opponentfactory->get_instance();
            $data["opponents"] = $litigation_stage ? $this->legal_case_litigation_stages_opponent->fetch_stage_opponents_data($litigation_stage) : $this->legal_case_opponent->fetch_case_opponents_data($case_id);
            $this->load->model("legal_case_opponent_position", "legal_case_opponent_positionfactory");
            $this->legal_case_opponent_position = $this->legal_case_opponent_positionfactory->get_instance();
            $data["opponent_positions"] = $this->legal_case_opponent_position->load_list_per_language();
            $data["max_opponents"] = $system_preferences["caseMaxOpponents"];
            $data["hijri_calendar_enabled"] = $hijri_calendar_enabled;
            $this->load->model("system_preference");
            $system_preference = $this->system_preference->get_key_groups();
            $data["ability_set_latest_development"] = $system_preference["DefaultValues"]["abilitySetLatestDevelopment"];
            $data["latest_development"] = $case_data["latest_development"];
            $response["html"] = $this->load->view("cases/litigation/change_litigation_stage", $data, true);
        } else {
            $result = true;
            if ($case_id && $this->legal_case->fetch($case_id)) {
                if ($this->input->post("legal_case_stage") == "") {
                    $response["validation_errors"]["legal_case_stage"] = $this->lang->line("cannot_be_blank_rule");
                    $result = false;
                } else {
                    $old_values = $this->legal_case->get_old_values($case_id);
                    $post_latest_development = $this->input->post("latest_development");
                    $this->legal_case->set_field("legal_case_stage_id", $this->input->post("legal_case_stage"));
                    $ability_set_latest_development = $system_preferences["abilitySetLatestDevelopment"];
                    if ($ability_set_latest_development) {
                        $this->legal_case->set_field("latest_development", $post_latest_development);
                    }
                    if ($this->legal_case->update()) {
                        if ($hijri_calendar_enabled) {
                            $_POST["judgment_date"] = hijriToGregorian($this->input->post("judgment_date"));
                        }
                        $this->legal_case_litigation_detail->set_fields($this->input->post(NULL));
                        $this->legal_case_litigation_detail->set_field("sentenceDate", $this->input->post("judgment_date"));
                        $result = $litigation_stage ? $this->legal_case_litigation_detail->update() : $this->legal_case_litigation_detail->insert();
                        if ($result) {
                            $stage_id = $this->legal_case_litigation_detail->get_field("id");
                            if ($this->input->post("status") && $old_status != $this->input->post("status")) {
                                $this->load->model("litigation_stage_status_history");
                                $this->litigation_stage_status_history->set_field("litigation_stage", $stage_id);
                                $this->litigation_stage_status_history->set_field("status", $this->input->post("status"));
                                $this->litigation_stage_status_history->set_field("action_maker", $this->is_auth->get_user_id());
                                $this->litigation_stage_status_history->set_field("movedOn", date("Y-m-d H:i:s", time()));
                                $this->litigation_stage_status_history->insert();
                            }
                            $this->legal_case->set_field("stage", $stage_id);
                            $this->legal_case->update();
                            $opponent_member_types = $this->input->post("opponent_member_type");
                            $opponent_member_ids = $this->input->post("opponent_member_id");
                            $opponent_positions = $this->input->post("opponent_position");
                            $this->load->model("opponent");
                            $opponents_data = is_array($opponent_member_types) && !empty($opponent_member_types) ? $this->opponent->get_opponents($opponent_member_types, $opponent_member_ids) : [];
                            if (!empty($opponents_data)) {
                                foreach ($opponents_data as $key => $value) {
                                    $opponents_data[$key]["opponent_id"] = $value["opponent_id"];
                                    $opponents_data[$key]["stage"] = $stage_id;
                                    $opponents_data[$key]["opponent_position"] = isset($opponent_positions[$key]) && !empty($opponent_positions[$key]) ? $opponent_positions[$key] : NULL;
                                    unset($opponents_data[$key]["opponent_member_type"]);
                                }
                            }
                            $this->legal_case_litigation_stages_opponent->insert_stage_opponents($stage_id, $opponents_data);
                            $this->load->model("legal_case_litigation_external_reference");
                            $this->legal_case_litigation_external_reference->delete(["where" => ["stage", $stage_id]]);
                            if ($this->input->post("external_ref") && is_array($this->input->post("external_ref"))) {
                                foreach ($this->input->post("external_ref")["number"] as $key => $ref_nb) {
                                    if ($hijri_calendar_enabled) {
                                        $_POST["external_ref"]["refDate"][$key] = hijriToGregorian($this->input->post("external_ref")["refDate"][$key]);
                                    }
                                    $this->legal_case_litigation_external_reference->reset_fields();
                                    $this->legal_case_litigation_external_reference->set_field("number", $ref_nb);
                                    $this->legal_case_litigation_external_reference->set_field("comments", $this->input->post("external_ref")["comments"][$key]);
                                    $this->legal_case_litigation_external_reference->set_field("refDate", strtotime($this->input->post("external_ref")["refDate"][$key]) != NULL ? date("Y-m-d", strtotime($this->input->post("external_ref")["refDate"][$key])) : "");
                                    $this->legal_case_litigation_external_reference->set_field("stage", $stage_id);
                                    if (!$this->legal_case_litigation_external_reference->insert()) {
                                        $response["validation_errors"] = $this->legal_case_litigation_external_reference->get("validationErrors");
                                        $result = false;
                                    }
                                }
                            }
                            $this->load->model("legal_case_stage_contact", "legal_case_stage_contactfactory");
                            $this->legal_case_stage_contact = $this->legal_case_stage_contactfactory->get_instance();
                            $this->legal_case_stage_contact->delete(["where" => ["stage", $stage_id]]);
                            if ($this->input->post("litigationContact") && is_array($this->input->post("litigationContact"))) {
                                foreach ($this->input->post("litigationContact")["contact"] as $key => $contact) {
                                    $this->legal_case_stage_contact->reset_fields();
                                    $contact_exist = $this->legal_case_stage_contact->fetch(["stage" => $stage_id, "contact" => $contact, "contact_type" => $this->input->post("litigationContact")["contact_type"][$key]]);
                                    if ($contact_exist) {
                                        $response["validation_errors"]["contact"] = $this->lang->line("contact_already_exist");
                                        $result = false;
                                    } else {
                                        $this->legal_case_stage_contact->set_field("stage", $stage_id);
                                        $this->legal_case_stage_contact->set_field("contact", $contact);
                                        $this->legal_case_stage_contact->set_field("comments", $this->input->post("litigationContact")["comments"][$key]);
                                        $this->legal_case_stage_contact->set_field("contact_type", $this->input->post("litigationContact")["contact_type"][$key]);
                                        if (!$this->legal_case_stage_contact->insert()) {
                                            $response["validation_errors"] = $this->legal_case_stage_contact->get("validationErrors");
                                            $result = false;
                                        }
                                    }
                                }
                            }
                            $data["stages_data"] = $this->return_litigation_stages_details($case_id);
                            $data["legalCase"]["id"] = $case_id;
                            $response["stages_html"] = $this->load->view("cases/litigation/stages_data", $data, true);
                            $response["stage_id"] = $stage_id;
                            $response["new_stage"] = $this->input->post("legal_case_stage");
                            $response["new_stage_sentence_date"] = $this->input->post("judgment_date");
                            $response["stages"] = $data["stages"];
                            $this->legal_case_litigation_detail->update();
                        } else {
                            $response["validation_errors"] = $this->legal_case_litigation_detail->get("validationErrors");
                            $result = false;
                        }
                    } else {
                        $response["validation_errors"] = $this->legal_case->get("validationErrors");
                        $result = false;
                    }
                }
                $response["result"] = $result;
            }
        }
        $this->output->set_content_type("application/json")->set_output(json_encode($response));
    }
    public function load_litigation_stage_forms($type, $edit_mode = false)
    {
        if (!$this->input->is_ajax_request() || !isset($type) || !$type) {
            show_404();
        }
        $data["title"] = $this->lang->line(($edit_mode == "false" ? "add_" : "edit_") . $type);
        $data["system_preferences"] = $this->session->userdata("systemPreferences");
        $data["hijri_calendar_enabled"] = isset($data["system_preferences"]["hijriCalendarFeature"]) && $data["system_preferences"]["hijriCalendarFeature"] ? $data["system_preferences"]["hijriCalendarFeature"] : 0;
        $response["html"] = $this->load->view("cases/litigation/form_" . $type, $data, true);
        $this->output->set_content_type("application/json")->set_output(json_encode($response));
    }
    public function return_litigation_stages()
    {
        $case_id = $this->input->post("caseId");
        $response = [];
        if ($case_id) {
            $this->load->model("legal_case_litigation_detail", "legal_case_litigation_detailfactory");
            $this->legal_case_litigation_detail = $this->legal_case_litigation_detailfactory->get_instance();
            $data["stages"] = $this->legal_case_litigation_detail->load_all_stages_metadata($case_id);
            $response["html"] = $this->load->view("cases/litigation/litigation_history_stages", $data, true);
        }
        $this->output->set_content_type("application/json")->set_output(json_encode($response));
    }
    public function transition_screen_fields($case_id = 0, $transition = 0)
    {
        if (!$this->input->is_ajax_request()) {
            show_404();
        }
        if (!$this->validate_id($transition) || !$this->validate_id($case_id)) {
            $this->set_flashmessage("error", $this->lang->line("invalid_record"));
            redirect("dashboard");
        }
        $response["result"] = true;
        $this->load->model("matter_fields", "matter_fieldsfactory");
        $this->matter_fields = $this->matter_fieldsfactory->get_instance();
        $this->load->model("workflow_status_transition", "workflow_status_transitionfactory");
        $this->workflow_status_transition = $this->workflow_status_transitionfactory->get_instance();
        $this->workflow_status_transition->fetch($transition);
        $status = $this->workflow_status_transition->get_field("toStep");
        $old_values = $this->legal_case->get_old_values($case_id);
        $this->matter_fields->load_all_fields($old_values["category"], $old_values["case_type_id"]);
        if (!$this->input->post(NULL)) {
            if ($this->workflow_status->check_transition_allowed($case_id, $status, $this->is_auth->get_user_id())) {
                $data = $this->matter_fields->return_screen_fields($case_id, $transition);
                if ($data) {
                    $data["title"] = $this->workflow_status_transition->get_field("name");
                    $system_preferences = $this->session->userdata("systemPreferences");
                    $data["max_opponents"] = $system_preferences["caseMaxOpponents"];
                    $response["html"] = $this->load->view("templates/screen_fields", $data, true);
                } else {
                    if (!$this->update_case_status($case_id, $status, $transition)) {
                        $response["result"] = false;
                        $response["display_message"] = $this->lang->line("workflowActionInvalid");
                    }
                    $response["display_message"] = sprintf($this->lang->line("status_updated_message"), $old_values["category"] == "Matter" ? $this->lang->line("corporate_matter") : $this->lang->line("the_litigation_case"));
                }
            } else {
                $response["result"] = false;
                $response["display_message"] = $this->lang->line("transition_not_allowed");
            }
        } else {
            $validation = $this->matter_fields->validate_fields($transition);
            $response["result"] = $validation["result"];
            if (!$validation["result"]) {
                $response["validation_errors"] = $validation["errors"];
            } else {
                if ($this->update_case_status($case_id, $status, $transition)) {
                    $save_result = $this->matter_fields->save_fields($case_id);
                    $response["display_message"] = sprintf($this->lang->line("status_updated_message"), $old_values["category"] == "Matter" ? $this->lang->line("corporate_matter") : $this->lang->line("the_litigation_case"));
                    if (!$save_result["result"]) {
                        $response["result"] = $save_result["result"];
                        $response["validation_errors"] = $save_result["validation_errors"];
                        $response["display_message"] = $this->lang->line("updates_failed_invalid_form");
                    } else {
                        if ($this->system_preference->get_values()["webhooks_enabled"] == 1) {
                            $webhook_data = $this->legal_case->load_case_details($case_id);
                            $this->legal_case->trigger_web_hook($webhook_data["category"] == "Matter" ? "matter_status_updated" : "litigation_status_updated", $webhook_data);
                        }
                    }
                } else {
                    $response["result"] = false;
                    $response["display_message"] = $this->lang->line("workflowActionInvalid");
                }
            }
        }
        $this->output->set_content_type("application/json")->set_output(json_encode($response));
    }
    private function update_case_status($case_id = 0, $status_id = 0, $transition = 0)
    {
        $old_values = $this->legal_case->get_old_values($case_id);
        if (!$this->workflow_status->moveStatus($case_id, $status_id, $this->is_auth->get_user_id())) {
            return false;
        }
        $this->notify_users($case_id, $old_values["case_status_id"], $transition);
        return true;
    }
    public function move_document($module_record_id = 0)
    {
        $module = "case";
        if ($module_record_id) {
            $data = [];
            $this->legal_case->fetch($module_record_id);
            $root_folder = $this->dms->get_module_record_root_folder($module, $module_record_id);
            $all_folders = $this->dms->load_all_folders($module, $module_record_id);
            $module_model = $this->dms->load_module_model($module);
            $response["tree"] = $this->load->view("documents_management_system/move_document_get_tree", ["parent_folder_name" => $module_model->get("modelCode") . $this->legal_case->get_field("id"), "all_folders" => $all_folders, "module_record_id" => $module_record_id, "root_folder_id" => $root_folder["id"] ?? "#"], true);
            $data["footer_message"] = $this->lang->line("create_folder_options");
            $response["html"] = $this->load->view("documents_management_system/move_document", $data, true);
        } else {
            $target_folder_id = $this->input->post("target_folder");
            $selected_items_ids = $this->input->post("selected_items");
            $new_created_folders = $this->input->post("new_created_folders");
            $response = $this->dms->move_document_handler($target_folder_id, $selected_items_ids, $new_created_folders, $module);
        }
        $this->output->set_content_type("application/json")->set_output(json_encode($response));
    }
    public function check_folder_privacy()
    {
        if (!$this->is_auth->is_logged_in()) {
            exit("login_needed");
        }
        $private_folders = $this->dms->check_folder_privacy($this->input->post("id"), $this->input->post("lineage"));
        $response["result"] = empty($private_folders) ? false : true;
        $this->output->set_content_type("application/json");
        $this->output->set_output(json_encode($response));
    }
    public function view_document($id = 0)
    {
        $response = [];
        if (0 < $id) {
            echo $this->dmsnew->get_document_content($id);
            exit;
        }
        $id = $this->input->post("id");
        if (!empty($id)) {
            $response["document"] = $this->dms->get_document_details(["id" => $id]);
            $response["document"]["url"] = BASEURL . "cases/view_document/" . $id;
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
        if (0 < $id) {
            echo $this->dmsnew->get_preview_document_content($id);
            exit;
        }
        $id = $this->input->post("id");
        if (!empty($id)) {
            $response["document"] = $this->dms->get_document_details(["id" => $id]);
            $response["document"]["url"] = app_url("contacts/preview_document/" . $id);
        }
        $response["html"] = $this->load->view("documents_management_system/view_document", ["mode" => "preview"], true);
        $this->output->set_content_type("application/json")->set_output(json_encode($response));
    }
    private function return_assignments_rules($case_type, $category)
    {
        $this->load->model("assignment", "assignmentfactory");
        $this->assignment = $this->assignmentfactory->get_instance();
        if (isset($case_type) && $case_type && $this->assignment->fetch(["category" => $category, "type" => $case_type])) {
            $response = $this->assignment->get_fields();
        } else {
            $category = "default_" . $category;
            $type = "all";
            $response = $this->assignment->load(["where" => [["category", $category], ["type", $type]]]);
        }
        $response["assignment_relation"] = "";
        switch ($response["assignment_rule"]) {
            case "rr_algorithm":
                $next_assignee = $this->assignment->load_next_case_assignee($response["id"]);
                $response["assignment_relation"] = $next_assignee["relation_id"] ?: "";
                $response["user_id"] = $next_assignee["user_id"] ?: "";
                break;
            case 0:
                $response["user_id"] = false;
                break;
            default:
                $response["user_id"] = $response["assignment_rule"];
        }
        return $response;

    }
    private function save_assignment()
    {
        $response = [];
        if ($this->input->post("assignment_id") && $this->input->post("user_relation") === $this->input->post("user_id")) {
            $this->load->model("assignment", "assignmentfactory");
            $this->assignment = $this->assignmentfactory->get_instance();
            $this->assignment->fetch($this->input->post("assignment_id"));
            if ($this->assignment->get_field("assignment_rule") == "rr_algorithm" && $this->assignment->get_field("assigned_team") == $this->input->post("provider_group_id")) {
                $this->load->model("assignments_relation");
                $this->assignments_relation->set_field("relation", $this->input->post("assignment_id"));
                $this->assignments_relation->set_field("user_relation", $this->input->post("assignment_relation"));
                $next_assignee = $this->assignment->load_next_case_assignee($this->input->post("assignment_id"));
                if ($next_assignee["user_id"] !== $this->input->post("user_relation")) {
                    $this->legal_case->set_field("user_id", $next_assignee["user_id"]);
                    $this->assignments_relation->set_field("user_relation", $next_assignee["relation_id"]);
                    if ($this->assignment->get_field("visible_assignee") == 1) {
                        $response["display_message"] = $this->lang->line("assignment_validation_rr_algorithm");
                    }
                } else {
                    $this->assignments_relation->set_field("user_relation", $this->input->post("assignment_relation"));
                }
                $this->assignments_relation->insert();
            }
        }
        return $response;
    }
    public function settings($case_id = 0)
    {
        if ($this->input->post()) {
            if (!$this->input->is_ajax_request()) {
                redirect("dashboard");
            }
            if ($this->input->post("action") == "load_partners") {
                $this->load->model("legal_case_partner_share");
                $response["partners_shares"] = $this->legal_case_partner_share->load_partners_shares($case_id);
            } else {
                if ($this->input->post("action") == "save_partners") {
                    $this->load->model("legal_case_partner_share");
                    $partners = $this->input->post("partners") ?? [];
                    $percentages = $this->input->post("percentages") ?? [];
                    $partners_shares = [];
                    foreach ($partners as $key => $partner_id) {
                        if (0 < $partner_id) {
                            $partners_shares[$key]["case_id"] = $case_id;
                            $partners_shares[$key]["account_id"] = $partner_id;
                            $partners_shares[$key]["percentage"] = $percentages[$key] ?? 0;
                        }
                    }
                    $response["status"] = $this->legal_case_partner_share->save_partners_shares($case_id, $partners_shares);
                } else {
                    $sortable = $this->input->post("sort", true);
                    $response = $this->legal_case->k_load_all_legal_case_time_tracking($case_id, $sortable);
                }
            }
            $this->output->set_content_type("application/json")->set_output(json_encode($response));
        } else {
            $data = [];
            $this->legal_case->fetch($case_id);
            $data["tabsNLogs"] = $this->get_vertical_case_tabs($case_id, site_url("cases/settings/"));
            $legal_case = $this->legal_case->get_fields();
            $this->load->model("common", "commonfactory");
            $this->common = $this->commonfactory->get_instance();
            $data["user_rate_hour"] = $this->common->get_user_rate_hour($legal_case["id"]);
            $legal_case["status"] = $this->legal_case->get_case_status($legal_case["case_status_id"]);
            $data["legalCase"] = $legal_case;
            $data["case_id"] = $case_id;
            $data["action"] = "editCapAmount";
            $client_data = $this->legal_case->get_case_client($legal_case["id"]);
            $data["client_id"] = $client_data["client_id"];
            $data["client_name"] = $client_data["clientName"];
            $system_preferences = $this->session->userdata("systemPreferences");
            $data["currency_value"] = $system_preferences["caseValueCurrency"];
            $systemPreferences = $this->session->userdata("systemPreferences");
            $data["systemPreferences"] = $systemPreferences;
            $data["business_week_days"] = $system_preferences["businessWeekEquals"];
            $data["business_day_hours"] = $system_preferences["businessDayEquals"];
            $data["category"] = $legal_case["category"] == "Litigation" ? "litigation_" : "matter_";
            $this->includes("kendoui/js/kendo.web.min", "js");
            $this->includes("kendoui/styles/kendo.common-bootstrap.min", "css");
            $this->includes("kendoui/styles/kendo.bootstrap.min", "css");
            $this->includes("jquery/form2js", "js");
            $this->includes("jquery/toObject", "js");
            $this->includes("scripts/case_settings", "js");
            $this->includes(app_url("compressed_asset/system_defaults.js?a=" . random_string("alpha")), "js", true);
            $this->includes("jquery/timemask", "js");
            $this->includes("scripts/show_hide_customer_portal", "js");
            if ($this->is_auth->is_layout_rtl()) {
                $this->includes("styles/rtl/fixes", "css");
            }
            $this->load->view("partial/header");
            $this->load->view("cases/settings", $data);
        }
    }
    public function get_case_rate()
    {
        $response = [];
        if (!$this->input->is_ajax_request()) {
            redirect("dashboard");
        }
        if (!empty($_POST)) {
            $this->load->model("case_rate", "case_ratefactory");
            $this->case_rate = $this->case_ratefactory->get_instance();
            $sortable = $this->input->post("sort", true);
            $case_id = $this->input->post("caseId", true);
            $response = $this->case_rate->load_all_case_rate($case_id, $sortable);
        }
        $this->output->set_content_type("application/json");
        $this->output->set_output(json_encode($response));
    }
    public function get_add_case_rate_view($case_id)
    {
        $response = [];
        if (!$this->input->is_ajax_request()) {
            redirect("dashboard");
        }
        $this->load->model("case_rate", "case_ratefactory");
        $this->case_rate = $this->case_ratefactory->get_instance();
        $organizations = $this->case_rate->get_entities();
        $data["organizations"] = ["" => $this->lang->line("none")] + $this->case_rate->get_pretty_selected_entities($organizations);
        $data["title"] = $this->lang->line("add_case_rate");
        $data["case_id"] = $case_id;
        $data["add_action"] = $this->controller_name . "/" . "add_case_rate";
        $response["html"] = $this->load->view("cases/settings/add_case_rate", $data, true);
        $this->output->set_content_type("application/json")->set_output(json_encode($response));
    }
    public function add_case_rate()
    {
        if (!$this->input->is_ajax_request()) {
            redirect("dashboard");
        }
        $response = [];
        $case_rate_data = $this->input->post(NULL, true);
        if (!empty($case_rate_data)) {
            $this->load->model("case_rate", "case_ratefactory");
            $this->case_rate = $this->case_ratefactory->get_instance();
            $response = $this->case_rate->add_case_rate($case_rate_data);
        }
        $this->output->set_content_type("application/json");
        $this->output->set_output(json_encode($response));
    }
    public function get_case_rate_by_organization_id($organization_id)
    {
        if (!$this->input->is_ajax_request()) {
            redirect("dashboard");
        }
        $response = [];
        if (!empty($organization_id)) {
            $this->load->model("case_rate", "case_ratefactory");
            $this->case_rate = $this->case_ratefactory->get_instance();
            $response = $this->case_rate->get_case_rate_by_organization_id($organization_id);
        }
        $this->output->set_content_type("application/json");
        $this->output->set_output(json_encode($response));
    }
    public function inline_grid_edit_case_rate()
    {
        if (!$this->input->is_ajax_request()) {
            redirect("dashboard");
        }
        $response = [];
        $case_rate_data = $this->input->post(NULL, true);
        if (!empty($case_rate_data)) {
            $this->load->model("case_rate", "case_ratefactory");
            $this->case_rate = $this->case_ratefactory->get_instance();
            $response = $this->case_rate->grid_edit_case_rate($case_rate_data);
        }
        $this->output->set_content_type("application/json")->set_output(json_encode($response));
    }
    public function delete_case_rate()
    {
        $case_rate_id = $this->input->post("case_rate_id", true);
        $response = [];
        $response["status"] = false;
        if ($case_rate_id) {
            $this->load->model("case_rate", "case_ratefactory");
            $this->case_rate = $this->case_ratefactory->get_instance();
            $response["status"] = $this->case_rate->delete(["where" => ["case_rate.id", $case_rate_id]]) ? true : false;
        }
        $this->output->set_content_type("application/json")->set_output(json_encode($response));
    }
    public function delete_case_stage()
    {
        if (!$this->input->is_ajax_request()) {
            redirect("dashboard");
        }
        $data = $this->input->post(NULL, true);
        $response = ["result" => false];
        $this->load->model("legal_case_litigation_detail", "legal_case_litigation_detailfactory");
        $this->legal_case_litigation_detail = $this->legal_case_litigation_detailfactory->get_instance();
        $this->load->model("litigation_stage_status_history");
        if ($this->legal_case_litigation_detail->fetch($data["stageId"])) {
            if ($this->litigation_stage_status_history->fetch(["litigation_stage" => $data["stageId"]])) {
                $this->litigation_stage_status_history->delete(["where" => [["litigation_stage", $data["stageId"]]]]);
            }
            $this->legal_case_litigation_detail->delete_stage($data["stageId"]);
            $response["result"] = true;
        }
        $this->output->set_content_type("application/json")->set_output(json_encode($response));
    }
    private function get_legal_cases_count($category = "Matter")
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
        if (!$this->input->is_ajax_request()) {
            redirect("dashboard");
        }
        $filter = $this->input->post("filter");
        $sortable = $this->input->post("sort");
        if ($this->input->post("loadWithSavedFilters") === "1") {
            $filter = json_decode($this->input->post("filter"), true);
        } else {
            $page_size_modified = $this->input->post("pageSize") != $data["grid_saved_details"]["pageSize"];
            $sort_modified = $this->input->post("sortData") && $this->input->post("sortData") != $data["grid_saved_details"]["sort"];
            if ($page_size_modified || $sort_modified) {
                $_POST["model"] = $data["model"];
                $_POST["grid_saved_filter_id"] = $data["gridDefaultFilter"]["id"];
                $response = $this->grid_saved_column->save();
                $response["gridDetails"] = $this->grid_saved_column->get_user_grid_details($data["model"], $data["gridDefaultFilter"]["id"]);
            }
        }
        $response["columns_html"] = $this->load->view("grid_saved_columns/dialog_columns_contents", $data, true);
        $response["count"] = $this->legal_case->get_legal_cases_count($filter, $sortable);
        $this->output->set_content_type("application/json");
        $this->output->set_output(json_encode($response));
    }
    public function show_hide_matter_in_customer_portal($matter_id = 0)
    {
        $response = ["result" => true, "error" => false, "info" => false];
        if ($this->legal_case->fetch($matter_id)) {
            $response["visible"] = $this->legal_case->get_field("visibleToCP");
            if (!$response["visible"]) {
                $visible_related_containers = $this->legal_case->load_visible_related_containers($matter_id);
                if (!empty($visible_related_containers)) {
                    $response["result"] = false;
                }
            } else {
                if ($this->legal_case->get_field("channel") == "CP") {
                    $response["result"] = false;
                    $response["info"] = $this->lang->line("matter_is_imported_from_cp");
                }
            }
            if (!$response["error"] && !$response["info"]) {
                if (!$response["visible"]) {
                    $this->load->model("customer_portal_screen", "customer_portal_screenfactory");
                    $this->customer_portal_screen = $this->customer_portal_screenfactory->get_instance();
                    $this->load->model("customer_portal_ticket_watcher", "customer_portal_ticket_watcherfactory");
                    $this->customer_portal_ticket_watcher = $this->customer_portal_ticket_watcherfactory->get_instance();
                    $data["requestedBy"] = $this->legal_case->get_field("requestedBy");
                    $data["legal_case"] = $this->legal_case->load_case($matter_id);
                    $response["result"] = true;
                    $data["title"] = $this->lang->line("show_matter_in_customer_portal");
                    $data["watchers"] = $this->customer_portal_ticket_watcher->get_ticket_watchers($matter_id);
                    $response["html"] = $this->load->view("customerPortal/show_case_customer_portal", $data, true);
                    $this->output->set_content_type("application/json");
                    $this->output->set_output(json_encode($response));
                } else {
                    $this->legal_case->set_field("visibleToCP", 0);
                    $data["legal_case"] = $this->legal_case->load_case($matter_id);
                    $category = $data["legal_case"]["category"];
                    if (!$this->legal_case->update()) {
                        $response["error"] = $this->lang->line("updates_failed");
                        $response["result"] = false;
                    } else {
                        $response["result"] = true;
                        $response["visible"] = !$response["visible"];
                        $response["category"] = $category;
                    }
                }
            }
        } else {
            $response["error"] = $this->lang->line("invalid_record");
            $response["result"] = false;
        }
        $this->output->set_content_type("application/json")->set_output(json_encode($response));
    }
    public function get_case_outsource_by_category()
    {
        if (!$this->input->is_ajax_request()) {
            show_404();
        }
        if (!$this->is_auth->is_logged_in()) {
            exit("login_needed");
        }
        $term = trim((string) $this->input->get("term"));
        $category_id = trim((string) $this->input->get("categoryId"));
        $outsource_type = trim((string) $this->input->get("outsourceType"));
        $results = [];
        if ($outsource_type == "company") {
            $this->load->model("company", "companyfactory");
            $this->company = $this->companyfactory->get_instance();
            $results = $this->company->lookup_outsource($term, $category_id);
        } else {
            $this->load->model("contact", "contactfactory");
            $this->contact = $this->contactfactory->get_instance();
            $results = $this->contact->lookup_outsource($term, $category_id);
        }
        $this->output->set_content_type("application/json");
        $this->output->set_output(json_encode($results));
    }
    public function check_custom_fields_relation()
    {
        if (!$this->input->is_ajax_request()) {
            show_404();
        }
        $response["result"] = true;
        $this->load->model("custom_field", "custom_fieldfactory");
        $this->custom_field = $this->custom_fieldfactory->get_instance();
        $is_valid = $this->custom_field->validate_custom_fields_relation();
        if (!empty($is_valid)) {
            $response["result"] = false;
            $response["display_message"] = $this->lang->line("cf_validation_matter_per_practice_area");
        }
        $this->output->set_content_type("application/json")->set_output(json_encode($response));
    }
    public function save_show_hide_customer_portal()
    {
        $response["result"] = true;
        $post_data = $this->input->post(NULL);
        if (empty($post_data["requestedBy"])) {
            $response["result"] = false;
            $response["validation_errors"]["requestedBy"] = $this->lang->line("cannot_be_blank_rule");
        }
        if ($response["result"] && !empty($post_data["ticket-id"])) {
            $this->load->model("customer_portal_users", "customer_portal_usersfactory");
            $this->customer_portal_users = $this->customer_portal_usersfactory->get_instance();
            $this->load->model("contact", "contactfactory");
            $this->contact = $this->contactfactory->get_instance();
            if (!$this->contact->fetch($post_data["requestedBy"])) {
                $response["error"] = $this->lang->line("invalid_record");
            } else {
                $this->load->model("legal_case", "legal_casefactory");
                $this->legal_case = $this->legal_casefactory->get_instance();
                $this->load->library("email_notifications");
                $this->legal_case->fetch(["id" => $post_data["ticket-id"]]);
                $category = $this->legal_case->get_field("category");
                $response["result"] = true;
                if (!$this->customer_portal_users->fetch(["contact_id" => $this->contact->get_field("id")])) {
                    $add_requested_by_as_cp_user = $this->customer_portal_users->add_requested_by_as_cp_user($category == "Litigation" ? mb_strtolower($this->lang->line("litigation")) : mb_strtolower($this->lang->line("case")));
                    $response["result"] = $add_requested_by_as_cp_user["result"];
                    if (isset($add_requested_by_as_cp_user["message"])) {
                        $response["info"] = $add_requested_by_as_cp_user["message"];
                    }
                }
                if ($response["result"]) {
                    $this->legal_case->set_field("requestedBy", $post_data["requestedBy"]);
                    $this->legal_case->set_field("visibleToCP", 1);
                    $licenses = $this->licensor->get_all_licenses();

                    if ($this->email_notification_scheme->get_hide_show_send_email_notification_by_trigger_action("notify_requested_by_watchers_cp")) {
                        $this->load->model("contact_emails");
                        $to_emails = $this->contact_emails->load_contact_emails($this->contact->get_field("id"));
                        $cc_emails = "";
                        if (!empty($post_data["watchers"]) && is_array($post_data["watchers"])) {
                            foreach ($post_data["watchers"] as $item) {
                                $cc_emails_sperator = empty($cc_emails) ? "" : ";";
                                $this->customer_portal_users->reset_fields();
                                $this->customer_portal_users->fetch($item);
                                $cc_emails = $cc_emails . $cc_emails_sperator . $this->customer_portal_users->get_field("email");
                            }
                        }
                        $notifications_data = ["to" => $to_emails, "cc" => $cc_emails, "object" => "notify_requested_by_watchers_cp", "objectModelCode" => $this->legal_case->get("modelCode"), "object_name" => $this->legal_case->get_field("subject"), "object_id" => $post_data["ticket-id"], "requested_by_name_cp" => $post_data["requestedByName"], "category_cp" => $this->lang->line("ticket"), "department_cp" => $licenses["core"]["App4Legal"]["clientName"], "controller" => "tickets", "fromLoggedUser" => $this->is_auth->get_fullname()];
                        $this->email_notifications->notify($notifications_data);
                    }
                    if ($this->legal_case->update()) {
                        $this->legal_case->touch_logs("update", [], $this->session->userdata("CP_user_id"), $this->legal_case->get("portalChannel"));
                        $response["modifiedOn"] = date("Y-m-d H:i", strtotime($this->legal_case->get_field("modifiedOn")));
                        $this->load->model("customer_portal_ticket_watcher", "customer_portal_ticket_watcherfactory");
                        $this->customer_portal_ticket_watcher = $this->customer_portal_ticket_watcherfactory->get_instance();
                        $watchers = $this->input->post("watchers") ? $this->input->post("watchers") : NULL;
                        $this->customer_portal_ticket_watcher->add_watchers_to_ticket($watchers, $this->input->post("ticket-id"));
                        $response["category"] = $category;
                        $response["visible"] = true;
                    } else {
                        $response["result"] = false;
                        $response["validation_errors"] = $this->legal_case->get("validationErrors");
                    }
                }
            }
        }
        $this->output->set_content_type("application/json")->set_output(json_encode($response));
    }
    public function bulk_edit_time($legal_case_id = 0)
    {
        if (!$this->input->is_ajax_request()) {
            redirect("");
        }
        $this->load->model("user_activity_log", "user_activity_logfactory");
        $this->user_activity_log = $this->user_activity_logfactory->get_instance();
        if (!$this->input->post()) {
            $this->load->model("time_types_languages", "time_types_languagesfactory");
            $this->time_types_languages = $this->time_types_languagesfactory->get_instance();
            $this->load->model("time_internal_statuses_language", "time_internal_statuses_languagefactory");
            $this->time_internal_statuses_language = $this->time_internal_statuses_languagefactory->get_instance();
            $data["activityData"] = $this->user_activity_log->get_activity_details();
            $response["status"] = true;
            $data["title"] = $this->lang->line("bulk_edit_time");
            $data["time_types"] = $this->time_types_languages->load_list_per_language();
            $data["time_internal_statuses"] = $this->time_internal_statuses_language->load_list_per_language();
            $this->load->model("case_rate", "case_ratefactory");
            $this->case_rate = $this->case_ratefactory->get_instance();
            $organizations = $this->case_rate->get_entities();
            $data["entities"] = $this->case_rate->get_pretty_selected_entities($organizations);
            $data["legal_case_id"] = $legal_case_id;
            $data["has_one_entity"] = 1 >= count($data["entities"]);
            $data["organization_id"] = $this->user_preference->get_value("organization");
            $response["html"] = $this->load->view("time_tracking/bulk_edit_time", $data, true);
            $this->output->set_content_type("application/json")->set_output(json_encode($response));
        } else {
            $response["result"] = true;
            $this->load->library("TimeMask");
            $this->systemPreferences = $this->session->userdata("systemPreferences");
            $roundUpTimeLogs = $this->systemPreferences["roundUpTimeLogs"];
            $post_data = $this->input->post(NULL);
            $records = json_decode($post_data["data"], true);
            foreach ($records as $record) {
                $effective_effort = $record["effectiveEffort"];
                $effective_effort_value = !empty($effective_effort) ? $this->timemask->humanReadableToHours($effective_effort) : 0;
                $data = $this->user_activity_log->get_activity_details($record["itemId"]);
                if ($data["rate"] != $record["ratePerHour"]) {
                    $data["rate_system"] = "fixed_rate";
                }
                if (!isset($record["ratePerHour"]) || empty($record["ratePerHour"])) {
                    $data["rate"] = NULL;
                    $data["rate_system"] = NULL;
                } else {
                    $data["rate"] = !empty($record["ratePerHour"]) ? $record["ratePerHour"] : $data["rate"];
                }
                $data["time_type_id"] = $record["timeTypeId"];
                $data["time_internal_status_id"] = $record["timeInternalStatusId"];
                $data["comments"] = $record["comments"];
                $data["logDate"] = !empty($record["logDate"]) ? $record["logDate"] : $data["logDate"];
                $response["status"] = $this->user_activity_log->save(!$record["isNew"] ? $record["itemId"] : NULL, $effective_effort_value, $roundUpTimeLogs, false, $record["isNew"], $data);
            }
            $this->output->set_content_type("application/json")->set_output(json_encode($response));
        }
    }
    public function get_total_effort_time_logs_case($id = 0, $my_time_logs = false)
    {
        $response["result"] = true;
        $system_preferences = $this->session->userdata("systemPreferences");
        $filter = $this->input->post("filter");
        $this->load->model("user_rate_per_hour", "user_rate_per_hourfactory");
        $this->user_rate_per_hour = $this->user_rate_per_hourfactory->get_instance();
        $this->load->model("organization", "organizationfactory");
        $this->organization = $this->organizationfactory->get_instance();
        $response["currency_value"] = $system_preferences["caseValueCurrency"];
        $entity_value = "";
        if ($this->input->post("entity")) {
            $entity_value = $this->input->post("entity");
            $data = $this->legal_case->get_Total_effective_effort_cost($id, $entity_value, $filter, false, $my_time_logs);
            $response["items"] = $this->legal_case->get_Total_effective_effort_cost($id, $entity_value, $filter, true, $my_time_logs);
            if (isset($entity_value)) {
                $entity_values = $this->organization->load_active_organizations($entity_value);
                if (isset($entity_values) && !empty($entity_values) && isset($entity_values[0]["currencyCode"])) {
                    $response["currency_value"] = $entity_values[0]["currencyCode"];
                }
            }
        } else {
            $this->load->model("case_rate", "case_ratefactory");
            $this->case_rate = $this->case_ratefactory->get_instance();
            $organizations = $this->case_rate->get_entities();
            $data["entities"] = $this->case_rate->get_pretty_selected_entities($organizations);
            $entities = isset($data["entities"]) && is_array(array_keys($data["entities"])) ? array_keys($data["entities"]) : "";
            if (isset($entities[0])) {
                $entity_value = $entities[0];
                $data = $this->legal_case->get_Total_effective_effort_cost($id, $entity_value, $filter, false, $my_time_logs);
                $response["items"] = $this->legal_case->get_Total_effective_effort_cost($id, $entity_value, $filter, true, $my_time_logs);
                if (!empty($entities[0])) {
                    $entity_values = $this->organization->load_active_organizations($entities[0]);
                    if (isset($entity_values) && !empty($entity_values) && isset($entity_values[0]["currencyCode"])) {
                        $response["currency_value"] = $entity_values[0]["currencyCode"];
                    }
                }
            }
        }
        $response["system_rate"] = "";
        if ($this->input->post("legal_case_id") && $this->input->post("user_id") && !empty($entity_value)) {
            $response["system_rate"] = $this->user_rate_per_hour->get_system_rate_per_hour($id, $this->input->post("user_id"), $entity_value);
        }
        $response["data"] = $data["data"][0] ?? false;
        $this->output->set_content_type("application/json")->set_output(json_encode($response));
    }
    public function get_system_rate()
    {
        $this->load->model("user_rate_per_hour", "user_rate_per_hourfactory");
        $this->user_rate_per_hour = $this->user_rate_per_hourfactory->get_instance();
        $response["status"] = false;
        if ($this->input->post("legal_case_id") && $this->input->post("user_id") && $this->input->post("rate_system") && $this->input->post("rate_system") == "system_rate") {
            $entity = "";
            if (!$this->input->post("entity")) {
                $this->load->model("case_rate", "case_ratefactory");
                $this->case_rate = $this->case_ratefactory->get_instance();
                $organizations = $this->case_rate->get_entities();
                $data["entities"] = $this->case_rate->get_pretty_selected_entities($organizations);
                if (1 >= count($data["entities"]) && isset(array_keys($data["entities"])[0])) {
                    list($entity) = array_keys($data["entities"]);
                }
            } else {
                $entity = $this->input->post("entity");
            }
            if (!empty($entity)) {
                $rate = $this->user_rate_per_hour->get_system_rate_per_hour($this->input->post("legal_case_id"), $this->input->post("user_id"), $entity);
                $response["status"] = true;
                $response["rate"] = $rate;
            }
        } else {
            if ($this->input->post("task_id") && $this->input->post("user_id") && $this->input->post("rate_system") && $this->input->post("rate_system") == "system_rate") {
                $this->load->model("task", "taskfactory");
                $this->task = $this->taskfactory->get_instance();
                $task_data = $this->task->load_task($this->input->post("task_id"));
                if (!empty($task_data) && !empty($task_data["legal_case_id"])) {
                    $entity = "";
                    if (!$this->input->post("entity")) {
                        $this->load->model("case_rate", "case_ratefactory");
                        $this->case_rate = $this->case_ratefactory->get_instance();
                        $organizations = $this->case_rate->get_entities();
                        $data["entities"] = $this->case_rate->get_pretty_selected_entities($organizations);
                        if (1 >= count($data["entities"]) && isset(array_keys($data["entities"])[0])) {
                            list($entity) = array_keys($data["entities"]);
                        }
                    } else {
                        $entity = $this->input->post("entity");
                    }
                    if (!empty($entity)) {
                        $rate = $this->user_rate_per_hour->get_system_rate_per_hour($task_data["legal_case_id"], $this->input->post("user_id"), $entity);
                        $response["status"] = true;
                        $response["rate"] = $rate;
                    }
                }
            }
        }
        $this->output->set_content_type("application/json")->set_output(json_encode($response));
    }
    public function advisor_tasks($case_id = "")
    {
        $system_preferences = $this->session->userdata("systemPreferences");
        if ($system_preferences["AllowFeatureAdvisor"] !== "yes") {
            redirect("dashboard");
        }
        $this->load->model("advisor_task", "advisor_taskfactory");
        $this->advisor_task = $this->advisor_taskfactory->get_instance();
        if ($this->input->post(NULL)) {
            if (!$this->input->is_ajax_request()) {
                $this->set_flashmessage("error", $this->lang->line("invalid_record"));
                redirect("dashboard");
            }
            $response = [];
            $filter = $this->input->post("filter");
            $sortable = $this->input->post("sort");
            if ($this->input->post("returnData")) {
                $response = $this->advisor_task->k_load_all_tasks($filter, $sortable, "", true);
            }
            $this->output->set_content_type("application/json");
            $this->output->set_output(json_encode($response));
        } else {
            $data = [];
            $data["tabsNLogs"] = $this->get_vertical_case_tabs($case_id, site_url("cases/advisor_tasks"));
            $legal_case = $this->legal_case->get_fields();
            $data["legalCase"] = $legal_case;
            $this->pageTitle = sprintf($this->lang->line("sheria360_browser_title"), $this->lang->line("case_related_advisor_tasks"));
            $this->includes("kendoui/js/kendo.web.min", "js");
            $this->includes("kendoui/styles/kendo.common-bootstrap.min", "css");
            $this->includes("kendoui/styles/kendo.bootstrap.min", "css");
            $this->includes("jquery/form2js", "js");
            $this->includes("jquery/toObject", "js");
            $this->includes("scripts/case_advisor_tasks", "js");
            if ($this->is_auth->is_layout_rtl()) {
                $this->includes("styles/rtl/fixes", "css");
            }
            $this->load->view("partial/header");
            $this->load->view("cases/advisor_tasks", $data);
        }
    }
    public function advisor_task($advisor_task_id)
    {
        $system_preferences = $this->session->userdata("systemPreferences");
        if ($system_preferences["AllowFeatureAdvisor"] !== "yes") {
            redirect("dashboard");
        }
        $this->load->model("advisor_task", "advisor_taskfactory");
        $this->advisor_task = $this->advisor_taskfactory->get_instance();
        $this->load->model("advisor_task_comment", "advisor_task_commentfactory");
        $this->task_comment = $this->advisor_task_commentfactory->get_instance();
        $data = [];
        $advisor_task_data = $this->advisor_task->load_task($advisor_task_id);
        $advisor_task_comments = $this->task_comment->load_comments($advisor_task_id);
        $advisor_task_documents = $this->advisor_task->load_task_documents($advisor_task_id);
        $data["docs"]["module_container"] = "tasks";
        $data["docs"]["directory"] = $this->config->item("files_path") . DIRECTORY_SEPARATOR . DIRECTORY_SEPARATOR . "attachments" . DIRECTORY_SEPARATOR . DIRECTORY_SEPARATOR . $data["docs"]["module_container"];
        $data["activeComment"] = 0;
        $data["task_data"] = $advisor_task_data;
        $data["task_data"]["task_comments"] = $advisor_task_comments["records"];
        $data["task_data"]["task_documents"] = $advisor_task_documents;
        $data["task_data"]["model_code"] = $this->advisor_task->get("modelCode");
        $data["task_data"]["case_model_code"] = $this->legal_case->get("modelCode");
        $this->pageTitle = sprintf($this->lang->line("sheria360_browser_title"), $this->lang->line("task"));
        $this->includes("jquery/tinymce/tinymce.min", "js");
        $this->includes("jquery/dropzone", "js");
        $this->includes("jquery/css/dropzone", "css");
        $this->includes("scripts/advisor_task_view", "js");
        $this->load->view("partial/header");
        $this->load->view("cases/advisor_task_view/main_section", $data);
        $this->load->view("partial/footer");
    }
    public function advisor_task_comments()
    {
        if (!$this->input->is_ajax_request()) {
            show_404();
        }
        $this->load->helper("revert_comment_html");
        $this->load->helper("format_comment_patterns");
        $id = $this->input->get("id");
        $this->load->model("advisor_task_comment", "advisor_task_commentfactory");
        $this->task_comment = $this->advisor_task_commentfactory->get_instance();
        $this->load->helper("text");

        $data["hide_show_notification"] = $this->email_notification_scheme->get_hide_show_send_email_notification_by_trigger_action("add_task_note") == "1" ? "yes" : "";
        $data["id"] = $id;
        $data["comments"] = $this->task_comment->load_comments($id, true);
        if (!empty($data)) {
            $response["html"] = $this->load->view("cases/advisor_task_view/comments/index", $data, true);
            $response["status"] = true;
        } else {
            $response["status"] = false;
        }
        $this->output->set_content_type("application/json");
        $this->output->set_output(json_encode($response));
    }
    public function add_advisor_task_comment()
    {
        if (!$this->input->is_ajax_request()) {
            show_404();
        }
        $this->load->model("advisor_task", "advisor_taskfactory");
        $this->task = $this->advisor_taskfactory->get_instance();
        $this->load->model("advisor_task_comment", "advisor_task_commentfactory");
        $this->task_comment = $this->advisor_task_commentfactory->get_instance();
        $this->load->helper("revert_comment_html");
        $this->load->helper("format_comment_patterns");
        if ($this->input->get(NULL)) {
            $data = [];
            $data["comment"] = $this->task_comment->get_fields();
            $data["comment"]["advisor_task_id"] = $this->input->get("advisor_task_id");
            $data["title"] = $this->lang->line("add_comments");
            $response["html"] = $this->load->view("cases/advisor_task_view/comments/form", $data, true);
        }
        if ($this->input->post(NULL)) {
            $_POST["comment"] = format_comment_patterns($this->regenerate_note($this->input->post("comment", true, "<h1><h2><h3><h4><h5><h6><strong><em><u><a><p><img>")));
            $_POST["edited"] = 0;
            $this->task_comment->set_fields($this->input->post(NULL));
            $this->task_comment->set_field("comment", $this->input->post("comment", true, true));
            $this->task_comment->set_field("createdByChannel", "A4L");
            $this->task_comment->set_field("modifiedByChannel", "A4L");
            if ($this->task_comment->insert()) {
                $this->task->fetch($this->input->post("advisor_task_id"));
                $data["task_data"] = ["description" => $this->task->get_field("description"), "comment" => revert_comment_html($this->input->post("comment", true, true), false, true, $this->input->post("task_id"), $this->task_comment->get_field("id"))];
                $this->send_notification($this->input->post("advisor_task_id"), "core_user_add_comment_on_advisor_task", $data);
                $response["result"] = true;
                $data["comment"] = $this->task_comment->load_comment($this->task_comment->get_field("id"));
                $response["html"] = $this->load->view("cases/advisor_task_view/comments/display_form", $data, true);
                $response["data"] = ["modifiedBy" => $this->session->userdata("AUTH_user_id"), "modifier_full_name" => $this->session->userdata("AUTH_userProfileName"), "modifiedOn" => date("Y-m-d H:i:s")];
                $this->task->set_field("id", $data["comment"]["advisor_task_id"]);
            } else {
                $response["validation_errors"] = $this->task_comment->get("validationErrors");
            }
        }
        $this->output->set_content_type("application/json")->set_output(json_encode($response));
    }
    public function edit_advisor_task_comment()
    {
        if (!$this->input->is_ajax_request()) {
            show_404();
        }
        $this->load->model("advisor_task", "advisor_taskfactory");
        $this->task = $this->advisor_taskfactory->get_instance();
        $response = [];
        $this->load->model("advisor_task_comment", "advisor_task_commentfactory");
        $this->task_comment = $this->advisor_task_commentfactory->get_instance();
        $this->load->helper("revert_comment_html");
        $this->load->helper("format_comment_patterns");
        if ($this->input->get("id") && $this->input->get("advisor_task_id")) {
            $data = [];
            if ($this->task_comment->fetch(["id" => $this->input->get("id"), "advisor_task_id" => $this->input->get("advisor_task_id")])) {
                $data["comment"] = $this->task_comment->get_fields();
                $data["title"] = $this->lang->line("edit_comment");
                $response["html"] = $this->load->view("cases/advisor_task_view/comments/form", $data, true);
            }
        }
        if ($this->input->post(NULL)) {
            $_POST["comment"] = format_comment_patterns($this->regenerate_note($this->input->post("comment", true, "<h1><h2><h3><h4><h5><h6><strong><em><u><a><p><img>")), true);
            $this->task_comment->fetch(["id" => $this->input->post("id"), "advisor_task_id" => $this->input->post("advisor_task_id")]);
            if ($this->input->post("comment", true, "<h1><h2><h3><h4><h5><h6><strong><em><u><a><p><img>") != $this->task_comment->get_field("comment")) {
                $_POST["edited"] = 1;
            }
            $this->task_comment->set_fields($this->input->post(NULL));
            $this->task_comment->set_field("comment", $this->input->post("comment", true, "<h1><h2><h3><h4><h5><h6><strong><em><u><a><p><img>"));
            if ($this->task_comment->update()) {
                $response["result"] = true;
                $response["data"] = ["modifiedBy" => $this->session->userdata("AUTH_user_id"), "modifier_full_name" => $this->session->userdata("AUTH_userProfileName"), "modifiedOn" => date("Y-m-d H:i:s")];
                $this->task->set_field("id", $this->input->post("advisor_task_id"));
            } else {
                $response["validation_errors"] = $this->task_comment->get("validationErrors");
            }
        }
        $this->output->set_content_type("application/json")->set_output(json_encode($response));
    }
    public function delete_advisor_task_comment()
    {
        $id = $this->input->post("id");
        if (!$this->input->is_ajax_request() || !$this->validate_id($id)) {
            show_404();
        }
        $this->load->model("advisor_task", "advisor_taskfactory");
        $this->task = $this->advisor_taskfactory->get_instance();
        $this->load->model("advisor_task_comment", "advisor_task_commentfactory");
        $this->task_comment = $this->advisor_task_commentfactory->get_instance();
        $response["result"] = false;
        if ($this->task_comment->fetch($id) && $this->task_comment->delete($id)) {
            $response["result"] = true;
            $response["data"] = ["modifiedBy" => $this->session->userdata("AUTH_user_id"), "modifier_full_name" => $this->session->userdata("AUTH_userProfileName"), "modifiedOn" => date("Y-m-d H:i:s")];
            $this->task->set_field("id", $this->input->post("module_record_id"));
        }
        $this->output->set_content_type("application/json");
        $this->output->set_output(json_encode($response));
    }
    private function send_notification($id, $object, $data)
    {
        $this->load->model("advisor_task", "advisor_taskfactory");
        $this->task = $this->advisor_taskfactory->get_instance();

        $model = $this->task->get("_table");
        $model_data["id"] = $id;
        $this->task->fetch($id);
        $model_data["contributors_ids"] = [$this->task->get_field("assigned_to"), $this->task->get_field("advisor_id")];
        if ($this->input->post("send_notifications_email") || $object == "edit_task_status") {
            $this->load->library("email_notifications");
            $objectType = $object;
            $notifications_emails = $this->email_notification_scheme->get_emails($objectType, $model, $model_data);
            extract($notifications_emails);
            $notificationsData["to"] = $to_emails;
            $notificationsData["cc"] = $cc_emails;
            $notificationsData["object_id"] = (int) $id;
            $notificationsData["fromLoggedUser"] = $this->is_auth->get_fullname();
            $notificationsData["object"] = $objectType;
            $notificationsData["objectModelCode"] = $this->task->get("modelCode");
            $data["task_data"]["task_id"] = $id;
            $data["task_data"]["assignee"] = $this->email_notification_scheme->get_advisor_user_full_name($this->task->get_field("assigned_to"));
            $data["task_data"]["created_by"] = $notificationsData["fromLoggedUser"];
            $notificationsData["taskData"] = $data["task_data"];
            $this->email_notifications->notify($notificationsData);
        }
    }
    public function download_docs_zip_file()
    {
        $this->_download_docs_zip_file("case", "cases", $_GET["selected_items"]);
    }

    public function readBasicCourtActivityList()
    {
        $this->load->model("legal_case_hearing", "legal_case_hearingfactory");
        $this->legal_case_hearing = $this->legal_case_hearingfactory->get_instance();

        $legal_case_id = $this->input->post("case_id");
        $response=$this->legal_case_hearing->getBasicList($legal_case_id);
        //echo json_encode($response);
        $this->output->set_content_type("application/json");
        $this->output->set_output(json_encode($response));


    }
    public function getCaseTasksSummary()
    {
        $legalCaseId = (int) $this->input->post('legal_case_id');
        $this->load->model("legal_case_hearing", "legal_case_hearingfactory");
        $this->legal_case_hearing = $this->legal_case_hearingfactory->get_instance();
        // $this->output->enable_profiler(TRUE);
        if ($legalCaseId && is_numeric($legalCaseId)) {
            $tasks = $this->legal_case_hearing->get_legalCase_task_summary($legalCaseId);

            $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode($tasks));
        } else {
            // Handle the case where legal_case_id is not provided (e.g., send an error response)
            $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode(array('error' => 'legal_case_id is required')));
            $this->output->set_status_header(400); //  Set HTTP status code to 400 Bad Request
        }
    }
    //function to handle case closure actions
    public function  case_file_closure_action()
    {  if (!$this->input->is_ajax_request()) {
        show_404();
    }
        $response["result"]=false;
        $response["message"]="";
        $this->load->model("case_closure_recommendation", "case_closure_recommendationfactory");
        $this->case_closure_recommendation = $this->case_closure_recommendationfactory->get_instance();

        $actionType=$this->input->post("actionType");
        $remarks=$this->input->post("remarks");
        $caseId=$this->input->post("caseId");
        $status=$this->input->post("status");
        if ($actionType==="Recommending"){

            $this->case_closure_recommendation->set_field("investigation_officer_recommendation", $remarks);
            $this->case_closure_recommendation->set_field("recommendation_status",$status);
            $this->case_closure_recommendation->set_field("createdBy", $this->session->userdata("AUTH_user_id"));
            $this->case_closure_recommendation->set_field("case_id", $caseId    );
            $this->case_closure_recommendation->set_field("date_recommended", date("Y-m-d"));
            if ( $this->case_closure_recommendation->insert()){
                $response["result"]=true;
                $response["message"]="Successfully Saved";
            }else
            { $response["result"]=false;
                $response["message"]=$this->case_closure_recommendation->validate();
            }

        }else{
            //if post get remarks: remarks, caseId: caseId, action: actionType
            if($this->input->post(null)){

                $status=$this->input->post("status");
                $recommendation_id=$this->input->post("recommendation_id");
                if($this->case_closure_recommendation->fetch($recommendation_id)){
                    $this->case_closure_recommendation->set_field("approval_remarks", $remarks);
                    $this->case_closure_recommendation->set_field("approval_status",$status);
                    $this->case_closure_recommendation->set_field("case_id", $caseId    );
                    $this->case_closure_recommendation->set_field("approval_date", date("Y-m-d"));
                    $this->case_closure_recommendation->set_field("approvedBy", $this->session->userdata("AUTH_user_id"));

                    if($this->case_closure_recommendation->update()){
                        //update the case and move it to Pending Before court
                        if($status=="Approved") {
                            $this->legal_case->fetch($caseId);
                            $this->legal_case->set_field("approval_step", 3);
                            $this->legal_case->update();
                        }
                        $response["result"]=true;
                        $result["message"]="Successfully updated";
                        $result["moved_toPBC"]=true;
                    }else{
                        $response["result"]=false;
                        $response["error"]=$this->case_closure_recommendation->get("validationErrors");
                    }

                }else{
                    $response["result"]=false;
                    $response["error"]="Error occured while updating recommendation";
                }

            }
        }
        $this->output->set_content_type('application/json') ->set_output(json_encode($response));
    }
///investigation function
    public function process_investigation_log()
    {
        $response = ["status" => false, "message" => "Failed to process log entry"];
        $this->load->model("case_investigation_log", "case_investigation_logfactory");
        $this->case_investigation_log = $this->case_investigation_logfactory->get_instance();

        $this->load->model("case_investigation_log_document", "case_investigation_log_documentfactory");
        $this->case_investigation_log_document = $this->case_investigation_log_documentfactory->get_instance();


        ;
        $case_id = $this->input->post("case_id");
        $this->case_investigation_log->set_field("case_id", $_POST["case_id"]);
        $this->case_investigation_log->set_field("log_date", date("Y-m-d"));
        $this->case_investigation_log->set_field("action_taken", $_POST["actionTaken"]);
        $this->case_investigation_log->set_field("details", $_POST["logEntryDetails"]);
        $this->case_investigation_log->set_field("createdBy", $this->session->userdata("AUTH_user_id"));
        $this->case_investigation_log->set_field("createdOn", date("Y-m-d H:i:s"));

        if($this->case_investigation_log->insert()){
            //update the case approval_step to investigation
            $this->legal_case->fetch($case_id);
            $this->legal_case->set_field("approval_step",3);
            $this->legal_case->update();
            //get investigation log id
            $log_id=$this->case_investigation_log->get_field("id");
            if ($log_id) {
                $response["status"] = true;
                $response["message"] = "Log entry saved successfully";

                // Handle file uploads
                $failed_uploads_count = 0;
                foreach ($_FILES as $file_key => $file) {
                    if ($file["error"] != 4) {
                        $upload_response = $this->dmsnew->upload_file([
                            "module" => "case",
                            "module_record_id" => $case_id,
                            "container_name" => "investigationLogs",
                            "upload_key" => $file_key
                        ]);

                        if (!$upload_response["status"]) {
                            $failed_uploads_count++;
                        } else {
                            $this->case_investigation_log_document->set_field("investigation_id", $log_id);
                            $this->case_investigation_log_document->set_field("document", $upload_response["file"]["id"]);

                            if (!$this->case_investigation_log_document->insert()) {
                                $this->dms->delete_document($upload_response["file"]["module"], $upload_response["file"]["id"]);
                                $failed_uploads_count++;
                            }
                            $this->case_investigation_log_document->reset_fields();
                        }
                    }
                }

                if ($failed_uploads_count > 0) {
                    $response["validationErrors"]["files"] = sprintf($this->lang->line("files_were_not_uploaded"), $failed_uploads_count);
                }
            }
        }

        $this->output->set_content_type('application/json') ->set_output(json_encode($response));
    }
//function to add attachments
    public function attach_file($id=0,$attachment_type=null)
    {
        $response["result"] =false;
        if (0<$id) {
            $id = $this->input->get("id", true)??$id;
            $data = $this->load_documents_form_data($id, $this->input->get("lineage", true));
            $data["title"] = $this->lang->line("upload_file");
            $data["module"] = "case";
            $data["module_record_id"] = $id;
            $data["attachment_type"]=$attachment_type;
            $data["lineage"]=$this->input->get("lineage", true);///let it be null at this stage of criminal case investigation
            $response["result"] = true;
            $response["html"] = $this->load->view("prosecution/forms/attachments_form", $data, true);
        }
        if ($this->input->post(NULL, true)) {
            if (!$_FILES["uploadDoc"]["name"]) {
                $response["status"] = false;
                $response["validation_errors"]["uploadDoc"] = $this->lang->line("file_required");
            } else {
                $response = $this->dmsnew->upload_file(["module" => $this->input->post("module"), "module_record_id" => $this->input->post("module_record_id"), "lineage" => null, "upload_key" => "uploadDoc", "document_type_id" => $this->input->post("document_type_id"), "document_status_id" => $this->input->post("document_status_id"), "comment" => $this->input->post("comment"), "container_name" => $attachment_type??"Investigations Case Docs", "term" => ""]);
                //update the case file type
                $document_id = $response["file"]["id"];
                $attachment_type = $this->input->post("attachment_type");
                if (isset($attachment_type) && !empty($attachment_type)&& $attachment_type !="undefined") {
                    if ($attachment_type == "complaintForm") {
                        $col_to_update = "initial_entry_document_id";
                    } else if ($attachment_type == "authorizationForm") {
                        $col_to_update = "authorization_document_id";
                    }
                    if(isset($col_to_update)) $result = $this->db->where("criminal_case_details.case_id = (" . $this->input->post("module_record_id") . ")")->update("criminal_case_details", [$col_to_update => $document_id]);

                }
            }
        }
        $this->output->set_content_type("application/json")->set_output(json_encode($response));
    }
    //load file modal details
    private function load_documents_form_data($id, $lineage)
    {
        $this->load->model("case_document_type");
        $this->load->model("case_document_status");
        $data["document_statuses"] = $this->case_document_status->load_list([], ["firstLine" => ["" => " "]]);
        $data["document_types"] = $this->case_document_type->load_list([], ["firstLine" => ["" => " "]]);
        $data["module_record"] = "case";
        $data["module_record_id"] = $id;
        return $data;
    }
    //offenses types based on case type
    public function get_offense_types_based_on_case_type_id($case_type) {
        $data = [];

        if ($case_type > 0) {
            $this->load->model("case_offense_subcategory", "case_offense_subcategoryfactory");
            $subcategory = $this->case_offense_subcategoryfactory->get_instance();
            $data = $subcategory->load_list(["where" => [["offense_type_id", $case_type], ["is_active", 1]], "order_by" => ["name", "asc"]]);
        }

        $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode($data));
    }
    //assign an officer
    public function assignOfficers($case_id=0){
        //
        if (!$this->input->is_ajax_request()) {
            show_404();
        }

        $response = ["success" => false];
        $this->legal_case->fetch($case_id);
        $this->load->model("provider_group");
        $grp_id=$this->legal_case->get_field("provider_group_id");
        if ($this->input->method() === 'get') {
            if ($this->input->get("type")&&$this->input->get("type")=="getUsersByTeam")
            { $response["success"]=true;
                $grp_id=$this->input->get("team_id")??$grp_id;
                $response["users"] = $this->get_provider_group_users($grp_id);
            }else {
                $data = [];
                $data["teams"] = ["" => $this->lang->line("none")] + $this->provider_group->load_list([]);
                //$this->provider_group->fetch(["allUsers" => 1]);
                //$data["allUsersProviderGroupId"] = $this->provider_group->get_field("id");
                //
                $response["success"] = true;
                $response["html"] = $this->load->view("prosecution/forms/assign_officer", $data, true);
                $this->output->set_content_type("application/json")->set_output(json_encode($response));
                return;
            }
        }
         // Handle POST logic for assigning officers here if needed
        else{
            $team_id=$this->input->post("team_id");
            $user_id = $this->input->post("user_id");

            // Validate IDs
            if ($case_id > 0 && $team_id > 0 && $user_id > 0) {
                // Optionally, check if team/user exist in DB
                $this->legal_case->set_field("provider_group_id", $team_id);
                $this->legal_case->set_field("user_id", $user_id);
                if ($this->legal_case->update()) {
                    $response["success"] = true;
                    $response["message"] = "Officer assigned successfully.";
                } else {
                    $response["message"] = "Failed to assign officer.";
                }
            } else {
                $response["message"] = "Invalid case, team, or user ID.";
            }
        }

        $this->output->set_content_type("application/json")->set_output(json_encode($response));
    }

    //arrests processing
public function arrests()
{
    if (!$this->input->is_ajax_request()) {
        show_404();
    }
    
    $this->load->model("suspect_arrest", "suspect_arrestfactory");
    $this->suspect_arrest = $this->suspect_arrestfactory->get_instance();

    if($this->input->get(null))
    {
    // Get the case_id from GET parameters if needed
    $data["case_id"] = $this->input->get('case_id');
    $mode=$this->input->get("mode");
    $record_id=$this->input->get("record_id");
    if ((isset($mode) &&$mode=="edit") && isset($record_id) && $record_id>0){
        $this->suspect_arrest->fetch($record_id);
        $data["arrestDetails"] =  $this->suspect_arrest->get_fields();
    }


     $response["success"] = true;
    $response["html"] = $this->load->view("prosecution/forms/arrest_form", $data, true);          
     }else{
         $post_data = $this->input->post(NULL);
         $this->suspect_arrest->set_fields($post_data);
         if($this->suspect_arrest->validate()){
             if ($this->suspect_arrest->insert()) {
                $response["success"]=true;
                $response["message"]="successfully recorded";
             }else{
                $response["sucess"]=false;
                $response["message"]="Error occurred";
             }
        }else{
             $response["success"]=false;
             $response["validationErrors"] = $this->suspect_arrest->get("validationErrors");
        }
     }
     $this->output->set_content_type("application/json")->set_output(json_encode($response));

}
public function open_party_form()
{
     if (!$this->input->is_ajax_request()) {
        show_404();
    }
    $data=[];
    $response=["success"=>true, "message"=>""];
    if($this->input->post(null) && $this->input->post("caseId")){
          
     $this->load->model("legal_case_opponent_position", "legal_case_opponent_positionfactory");
     $this->legal_case_opponent_position = $this->legal_case_opponent_positionfactory->get_instance();
    $data["party_positions"] = $this->legal_case_opponent_position->load_list_per_language();
   $data["partyData"] = [];//$this->opponent->get_opponents([$opponent_member_type], [$opponent_member_id]);
   $data["case_id"] = $this->input->post("caseId");

   
    $response=["success"=>true, "message"=>"loaded successifully"];
    $response["html"] = $this->load->view("cases/parties/form", $data, true);
     }else{
         $response=["success"=>false, "message"=>"Form not loaded"];
     }
     
   

    $this->output->set_content_type("application/json")->set_output(json_encode($response));
}

public function load_quick_add_feeNote_form($case_id=0,$ext_counsel_id=0)
{
    if (!$this->input->is_ajax_request()) {
        show_404();
    }
     $data["case_id"]=$case_id;
    $data["ext_counsel_id"]=$ext_counsel_id;
    $response = ["status" => false, "message" => "Failed to load fee note form"];

  
      $response["html"] = $this->load->view("cases/fee_notes_quick_add", $data, true);
                   

    $this->output->set_content_type('application/json')->set_output(json_encode($response));
}
public function get_matter_account_status($case_id=0)
{
    if (!$this->input->is_ajax_request()) {
        show_404();
    }
    $response = ["success" => false, "message" => "Failed to retrieve matter account status"];
    if ($case_id <= 0 || !is_numeric($case_id)) {
        $response["message"] = "Invalid case ID";
      
    }else{
      
    $moneyData = $this->legal_case->load_matter_feeNotes($case_id);
     $response = ["success" => true, "message" => "successfully retrieved matter account status"];
    $response["data"]=   $moneyData;
     }
    
    $this->output->set_content_type('application/json')->set_output(json_encode($response));
}
/*list of matter fee notes
*/
public function list_matter_feenotes($case_id){
      $this->authenticate_exempted_actions();
        if (!$this->input->is_ajax_request()) {
            show_404();
        }
    
    $response["success"] = false;
    $response["message"] = "Failed to load fee notes";
    if ($case_id > 0 && is_numeric($case_id)) {
        $data["feeNotes"] = $this->legal_case->load_matter_feeNotes($case_id, true);
         $data["title"] = $this->lang->line("matter_fee_notes");
        $response["success"] = true;
        $response["message"] = "Successfully loaded fee notes";
      
        $response["html"] = $this->load->view("cases/matter_fee_notes_list", $data, true);
    }  else {
        $response["message"] = "Invalid case ID";
    }  
        
        $this->output->set_content_type("application/json")->set_output(json_encode($response));

}
public function related_risks(){
   if (!$this->input->is_ajax_request()) {
            show_404();
        }

    $response["success"] = false;
    $response["message"] = "Failed to load related risks";

   if ($this->input->get(NULL)) {
       
    if ($this->input->get("case_id") && is_numeric($this->input->get("case_id"))) {

        $case_id=$this->input->get("case_id");
        $this->load->model("legal_case_risks", "legal_case_risksfactory");
        $this->legal_case_risks = $this->legal_case_risksfactory->get_instance();
        //check if its update by checking if id is available and if that record can be fetched from db
   
         if($this->input->get("riskId") && is_numeric($this->input->get("riskId")) && $this->legal_case_risks->fetch($this->input->get("riskId"))){
       $this->legal_case_risks->fetch($this->input->get("riskId"));

         }
                $data["risk"] = $this->legal_case_risks->get_fields();
               $this->load->model("user_profile");
         $responsible= $data["risk"]["responsible_actor_id"];
        $responsible?$this->user_profile->fetch(["user_id"=>$responsible]):"";
        $data["risk"]["responsible_name"] = $this->user_profile->get_field("firstName") . "" . $this->user_profile->get_field("lastName");
   
        $data["title"] = $this->lang->line("related_risks");
      
        $data["risk"]["case_id"] = $case_id;
            $response["success"] = true;
            $response["message"] = "Successfully loaded related risks";
    
        $response["html"] = $this->load->view("cases/case_related_risks_form", $data, true);
    }  else {
        $response["message"] = "Invalid case ID";
        }  
    } else if($this->input->post(NULL)){
        $postData = $this->input->post(NULL, true);
        // Exclude 'responsible' from $postData if it exists
        if (isset($postData['responsible'])) {
            unset($postData['responsible']);
        }
        if(!isset($postData["id"]) || $postData["id"]<=0 || !is_numeric($postData["id"])){
            unset($postData["id"]);
        }
        $postData["createdBy"]=$this->session->userdata("AUTH_user_id");
        $postData["createdOn"]=date("Y-m-d H:i:s");
        $postData["risk_type"]="general";
        $postData["responsible_actor_id"]=$postData['responsible_actor_id']>0?$postData['responsible_actor_id']:null;
        
       

    $this->load->model("legal_case_risks", "legal_case_risksfactory");
    $this->legal_case_risks = $this->legal_case_risksfactory->get_instance();
 
       $this->legal_case_risks->set_fields($postData);

      if($this->legal_case_risks->validate()){    
        $keys=["case_id","risk_category"];
        if($this->legal_case_risks->insert_on_duplicate_key_update($postData,$keys)){
             $response["success"] = true;
            $response["message"] = "Successfully saved related risks";
        }else{
             $response["message"] = "Error occurred while saving related risks";
        }
    }else{
         $response["message"] = "Validation errors occurred";
         $response["validationErrors"] = $this->legal_case_risks->get("validationErrors");
    }
}

        
        $this->output->set_content_type("application/json")->set_output(json_encode($response));
}

//function to load realated risks list
public function get_related_risks(){
        $this->authenticate_exempted_actions();
            if (!$this->input->is_ajax_request()) {
                show_404();
            }
        
        $response["success"] = false;
        $response["message"] = "Failed to load related risks";
       
        $case_id=$this->input->get("case_id");
        if ($case_id > 0 && is_numeric($case_id)) {
            $this->load->model("legal_case_risks", "legal_case_risksfactory");
            $this->legal_case_risks = $this->legal_case_risksfactory->get_instance();
            

             $data["risks"] = $this->legal_case_risks->load_risks_by_case($case_id);
             $data["title"] = $this->lang->line("related_risks");
            $response["success"] = true;
            $response["message"] = "Successfully loaded related risks";
      //  exit(json_encode($data));
            $response["html"] = $this->load->view("cases/case_related_risks_list", $data, true);
        }  else {
            $response["message"] = "Invalid case ID";
        }  
            
            $this->output->set_content_type("application/json")->set_output(json_encode($response));
    }

    //post request function to delete related risk
    public function delete_related_risk(){
        if (!$this->input->is_ajax_request()) {
            show_404();
        }
    
        $response["success"] = false;
        $response["message"] = "Failed to delete related risk";
       
        $risk_id=$this->input->post("riskId");
        if ($risk_id > 0 && is_numeric($risk_id)) {
            $this->load->model("legal_case_risks", "legal_case_risksfactory");
            $this->legal_case_risks = $this->legal_case_risksfactory->get_instance();
            
           if( $this->legal_case_risks->fetch($risk_id)){
               if($this->legal_case_risks->delete($risk_id)){
                $response["success"] = true;
                $response["message"] = "Successfully deleted related risk";
               }else{
                $response["message"] = "Error occurred while deleting related risk";
               }
           }else{
            $response["message"] = "Related risk not found";
           }
           
        }  else {
            $response["message"] = "Invalid risk ID";
        }  
            
            $this->output->set_content_type("application/json")->set_output(json_encode($response));
    }
    

}
