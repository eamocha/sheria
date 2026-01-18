<?php
defined('BASEPATH') OR exit('No direct script access allowed');
require "Top_controller.php";

class Conveyancing extends Top_Controller
{
    public function __construct()
    {
        parent::__construct();

        // Load models with factory pattern
        $this->load->model("conveyancing_instrument", "conveyancing_instrumentfactory");
        $this->conveyancing_instrument = $this->conveyancing_instrumentfactory->get_instance();

        $this->load->model("conveyancing_document", "conveyancing_documentfactory");
        $this->conveyancing_document = $this->conveyancing_documentfactory->get_instance();

        $this->load->model("conveyancing_activity", "conveyancing_activityfactory");
        $this->conveyancing_activity = $this->conveyancing_activityfactory->get_instance();

        $this->load->model("advisor_users", "advisor_usersfactory");
        $this->advisor_users = $this->advisor_usersfactory->get_instance();

        $this->load->model("user", "userfactory");
        $this->user = $this->userfactory->get_instance();

        $this->load->model("conveyancing_instrument_types", "conveyancing_instrument_typesfactory");
        $this->conveyancing_instrument_types = $this->conveyancing_instrument_typesfactory->get_instance();
        $this->load->model("conveyancing_transaction_types", "conveyancing_transaction_typesfactory");
        $this->conveyancing_transaction_types = $this->conveyancing_transaction_typesfactory->get_instance();

        $this->load->model("conveyancing_process_stage", "conveyancing_process_stagefactory");
        $this->conveyancing_process_stage = $this->conveyancing_process_stagefactory->get_instance();


        $this->pageTitle = sprintf($this->lang->line("sheria360_browser_title"), $this->lang->line("conveyancing_management"));

        // Load libraries
        $this->load->library("dmsnew");
    }


    public function index()
    {
        $data = [];

        $this->load->model("grid_saved_column");
        $this->load->model("custom_field", "custom_fieldfactory");
        $this->custom_field = $this->custom_fieldfactory->get_instance();
        $systemPreferences = $this->session->userdata("systemPreferences");
        $hijri_calendar_enabled = isset($systemPreferences["hijriCalendarFeature"]) && $systemPreferences["hijriCalendarFeature"] ? $systemPreferences["hijriCalendarFeature"] : 0;
        $data["model"] = "conveyancing_instrument";
        $grid_details = $this->grid_saved_column->get_user_grid_details($data["model"]);
        if ($this->input->post(NULL)) {
            if (!$this->input->is_ajax_request()) {
                redirect("dashboard");
            }
            $response = [];
            $filter = $this->input->post("filter");
            $sortable = $this->input->post("sort");
            $response =$this->conveyancing_instrument->k_load_all_instruments($filter, $sortable, "", false, $hijri_calendar_enabled);
            if ($this->input->post("savePageSize") || $this->input->post("sortData")) {
                $_POST["model"] = $data["model"];
                $this->grid_saved_column->save();
            }
            $response["columns_html"] = $this->load->view("grid_saved_columns/dialog_columns_contents", $grid_details, true);
            $this->output->set_content_type("application/json");
            $this->output->set_output(json_encode($response));
        }else {
            $this->load->helper(["text"]);
//        $this->load->model("conveyancing_instrument_status");

            $data["types"] = $this->conveyancing_instrument_types->load_list();///**
            $data["transaction_types"] = $this->conveyancing_transaction_types->load_list();
            unset($data["types"][""]);

            $data["statuses"] = ["Pending" => "Pending", "In-progress" => "In-progress", "Completed" => "Completed", "Delayed" => "Delayed", "Closed" => "Closed"];
            $data["operators"]["text"] = $this->get_filter_operators("text");
            $data["operators"]["number"] = $this->get_filter_operators("number");
            $data["operators"]["number_only"] = $this->get_filter_operators("number_only");
            $data["operators"]["list"] = $this->get_filter_operators("list");
            $data["operators"]["date"] = $this->get_filter_operators("date");
            $data["operators"]["lookup"] = $this->get_filter_operators("lookUp");
            $data["operators"]["group_list"] = $this->get_filter_operators("groupList");
            $data["archivedValues"] = array_combine($this->conveyancing_instrument->get("archivedValues"), [$this->lang->line("either"), $this->lang->line("yes"), $this->lang->line("no")]);
            $systemPreferences = $this->session->userdata("systemPreferences");
            $data["systemPreferences"] = $systemPreferences;
            $data["priorityValues"] = array_combine($this->conveyancing_instrument->get("priorityValues"), [$this->lang->line("critical"), $this->lang->line("high"), $this->lang->line("medium"), $this->lang->line("low")]);
            $data["assignedToFixedFilter"] = "";
            $data["reported_by_me_auth"] = "";
            $data["contributed_by_me_auth"] = "";
            $data["authUserId"] = $this->session->userdata("AUTH_user_id") * 1;

            $this->load->model("user", "userfactory");
            $this->user = $this->userfactory->get_instance();
            $data["usersList"] = $this->user->load_users_list();
            $this->load->model("opinion_status");
            $data["instrumentStatusValues"] = $data["statuses"];
            $this->load->model("user_preference");
            $data["conveyancing_instrumentStatusesSavedFilters"] = $this->user_preference->get_value("conveyancing_instrumentStatusesSavedFilters");
            $data["defaultArchivedValue"] = "no";
            $data["custom_fields"] = $this->custom_field->load_list_per_language("conveyancing");
            $data["businessWeekDays"] = $systemPreferences["businessWeekEquals"];
            $data["businessDayHours"] = $systemPreferences["businessDayEquals"];

            $this->includes("kendoui/js/kendo.web.min", "js");
            $this->includes("kendoui/styles/kendo.common-bootstrap.min", "css");
            $this->includes("kendoui/styles/kendo.bootstrap.min", "css");
            $this->includes("jquery/form2js", "js");
            $this->includes("jquery/toObject", "js");
            $this->includes("scripts/conveyancing", "js");
            $this->includes("scripts/advance_search_custom_field_template", "js");
            $this->includes(app_url("compressed_asset/system_defaults.js?a=" . random_string("alpha")), "js", true);
            $this->includes("jquery/timemask", "js");
            if ($this->is_auth->is_layout_rtl()) {
                $this->includes("styles/rtl/fixes", "css");
            }
            $this->load->view("partial/header");
            $this->load->view("conveyancing/index", $data + $grid_details);
            $this->load->view("partial/footer");
        }
    }

    /**
     * @param $instrument_id
     * @return void
     */
    public function view($instrument_id)
    {
        if (!$instrument_id || !ctype_digit($instrument_id) || !$this->conveyancing_instrument->fetch($instrument_id)) {
            $this->setPinesMessage("error", $this->lang->line("invalid_record"));
            redirect("conveyancing");
        }

        $this->conveyancing_instrument->fetch($instrument_id);
        $data["title"] = $this->lang->line("conveyancing_instrument_details");

        // Load instrument data
        $data["instrument"] = $this->conveyancing_instrument->get_instrument_by_id($instrument_id);
        //   $data["documents"] = $this->conveyancing_document->get_documents_for_instrument($instrument_id);
        //   $data["activities"] = $this->conveyancing_activity->get_activities_for_instrument($instrument_id);

        // Load stakeholders
      //  $data["ca_staff"] = $this->user->fetch($this->conveyancing_instrument->get_field("initiated_by"));
        // $data["external_counsel"] = $this->external_counsel->fetch(
        //     $this->conveyancing_instrument->get_field("external_counsel_id"));

        // Related documents count
        // $data["related_documents_count"] = $this->dms->count_conveyancing_related_documents($instrument_id, true);

        // Load views and assets

        $this->includes("kendoui/js/kendo.web.min", "js");
        $this->includes("styles/contract/main", "css");
        $this->includes("kendoui/styles/kendo.common-bootstrap.min", "css");
        $this->includes("kendoui/styles/kendo.bootstrap.min", "css");
        $this->includes("jquery/form2js", "js");
        $this->includes("jquery/toObject", "js");
      $this->includes(app_url("compressed_asset/system_defaults.js?a=" . random_string("alpha")), "js", true);
        $this->includes("jquery/timemask", "js");

        $this->includes("customerPortal/clientPortal/js/conveyancing_view", "js");
        $this->includes("scripts/conveyancing_view", "js");
        $this->includes("customerPortal/clientPortal/js/conveyancing_progress_stages", "js");


        $this->load->view("partial/header");
        $this->load->view("conveyancing/conveyancing-detail", $data);
        $this->load->view("partial/footer");
    }
    public function  get_provider_groups(){
        $this->load->model(["provider_group"]);
        $data["assigned_teams"] = $this->provider_group->load_list([]);
        $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode($data));
    }

    public function get_provider_group_users($provider_group_id = 0)
    {
        $data = [];
        if (0 < $provider_group_id) {
            $this->load->model("provider_group");
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
         $data["usersProviderGroup"];
        $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode($data));
    }
    public function get_timeline($instrument_id) {
        $data = $this->conveyancing_instrument->get_process_timeline($instrument_id);
        $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode($data));
    }
    public function get_process_stages()
    {

        $data["stages"] = $this->conveyancing_process_stage->load_list();
        $response["result"]= true;
        $response["html"] = $this->load->view("conveyancing/update-progress-form", $data, true);
        $this->output->set_content_type("application/json")->set_output(json_encode($response));
    }
   public function update_instrument_stage_progress()
    {

        $response[]="";
        if (!$this->input->is_ajax_request()) {
            show_404();
        }
        $this->load->model("conveyancing_stage_progress","conveyancing_stage_progressfactory");
        $this->conveyancing_stage_progress=$this->conveyancing_stage_progressfactory->get_instance();
        //update the conveyancing_stage_progress table
        $stage_id=$this->input->post("stage_id" );
        $instrument_id=$this->input->post("instrument_id");
        $dataSet=array("instrument_id" =>$instrument_id , "status" => $this->input->post("status"),"stage_id"=>$stage_id,"comments"=>$this->input->post("comments" ),
           "updated_by"=>$this->is_auth->get_user_id(), "completion_date"=>$this->input->post("status")=="completed"?date("Y-m-d H:i" ):null);
        $keys = ['instrument_id', 'stage_id'];

        if($this->conveyancing_stage_progress->insert_on_duplicate_key_update($dataSet,$keys)){

            $this->conveyancing_stage_progress->reset_fields();
            //if completed, updated activities table and set the next stage current if it is not the last step

            $activitiesTableFields=["conveyancing_instrument_id"=>$instrument_id,"createdByChannel"=>"A4L",
                "createdBy"=>$this->is_auth->get_user_id(), "createdOn"=>date("Y-m-d H:i:s"), "action"=>"Workflow process update ",
                "activity_details"=>"updated status '".$this->input->post("status")."', in the '". $this->input->post("stageText")."' Stage. Comments: ". $this->input->post("comments")];
            $this->conveyancing_activity->set_fields($activitiesTableFields);
            if($this->conveyancing_activity->insert()){
                $response["result"]= true;
            }else{
                $response["validationErrors"] = $this->conveyancing_activity->get("validationErrors");
                $response["result"]= false;
            }


            if ($this->input->post("status")=="completed")
            {
                $next_stage_id = $this->conveyancing_process_stage->get_next_stage_id($stage_id);
                if ($next_stage_id) {
                //insert the next stage to db
                $nextStepFieldsToSet=["instrument_id"=>$instrument_id, "stage_id"=>$next_stage_id,"status"=>"current", "updated_by"=>$this->is_auth->get_user_id(),"start_date"=>date("Y-m-d H:i:s")];
                $this->conveyancing_stage_progress->insert_on_duplicate_key_update($nextStepFieldsToSet,$keys);
                //update activity table
                $response["result"]=true;
                } else {
                //  at final stage
                //update the conveyancing item to completetd
                $result=$this->conveyancing_instrument->update($instrument_id, [
                    "status" => "completed",
                    "modifiedOn" => date("Y-m-d H:i:s")
                ]);
//                $update_data = ['status' => 'completed'];
//                $conditions = ['id' => $instrument_id];
//                $result = $this->conveyancing_instrument->update($update_data, false, false, $conditions);

                if(!$result){
                    $response["validationErrors"] = $this->conveyancing_instrument->get("validationErrors");
                } else {
                    $response["result"] = true;
                    $response["final_stage_reached"] = $this->lang->line("instrument_completed");
                }
                //update the actity table
                }
            }
        }else{
            $response["validationErrors"] = $this->conveyancing_stage_progress->get("validationErrors");
        }
            //update the logs table
        $this->output->set_content_type("application/json")->set_output(json_encode($response));

    }
    public function get_activities_log($instrument_id) {
        $data = $this->conveyancing_activity->load_conveyancing_activities($instrument_id);
        $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode($data));
    }


public function nominate_counsel($instrument_id)
{    $data["id"] = $instrument_id;
    $response["result"]=false;
    if ($this->input->post(null)) {
        $external_counsel_id=$this->input->post("external_counsel_id");
        $conveyancing_instrument_id=$this->input->post("conveyancing_instrument_id");
            $nomination_notes=$this->input->post("nomination_notes");//not stored
            if ($this->conveyancing_instrument->fetch($conveyancing_instrument_id)){
                $this->conveyancing_instrument->set_field('external_counsel_id',$external_counsel_id);
           if($this->conveyancing_instrument->update_external_counsel_nomination($conveyancing_instrument_id,["external_counsel_id"=>$external_counsel_id])){
               $response["result"]=true;

           }
            }

    } else {
        $response["result"]=true;
    $response["html"] = $this->load->view("conveyancing/nominate_lawyer", $data, true);
}$this->output->set_content_type("application/json")->set_output(json_encode($response));
}
    public function add()
    {
        if (!$this->input->is_ajax_request()) {
            show_404();
        }
        $this->load->model("email_notification_scheme");
        $response = [];
        $result = false;
        $this->load->model("custom_field", "custom_fieldfactory");
        $this->custom_field = $this->custom_fieldfactory->get_instance();


        // $this->load->library("TimeMask");
        $system_preferences = $this->session->userdata("systemPreferences");
        $conveyancing_instrument_types = $this->conveyancing_instrument_types->load_list();
        $transaction_types = $this->conveyancing_transaction_types->load_list();
        $this->load->helper("revert_comment_html");
        $this->load->helper("format_comment_patterns");
        if (!$this->input->post(NULL)) {
            $this->load->model("system_preference");
            $system_preference = $this->system_preference->get_key_groups();

            $data["archivedValues"] = ["no","yes"];//array_combine($this->conveyancing_instrument->get("archivedValues"), $this->conveyancing_instrument->get("archivedValues"));
            $data["system_preferences"] = $this->session->userdata("systemPreferences");
            $data["loggedInUser"] = $this->session->userdata("AUTH_userProfileName");
            $data["type"] = $conveyancing_instrument_types;
            $data["transaction_types"]=$transaction_types;
            $data["status"] = "";//$this->conveyancing_instrument->get("statuses");///this should come from db
            $data["toMeId"] = $this->is_auth->get_user_id();
            $data["toMeFullName"] = $this->is_auth->get_fullname();
            $sDate = date("Y-m-d", time());
            $data["conveyancingData"] = ["id" => "", "instrument_type_id" => "","transaction_type_id" => "","user_id" => "", "title" => "", "parties" => "", "initiated_by" => "", "staff_pf_no" => "","date_initiated" => $sDate,"description" => "", "archived" => "","status" => "", "external_counsel" => "", "property_value" => "", "amount_requested" => "", "assigned_to" => "", "amount_approved" => "", "assignee_fullname" => "", "assignee_status" => ""  ];
            $data["title"] = $this->lang->line("conveyancing_request");

            $data["system_preferences"]["conveyancingInstrumentTypeId"]=1;//addded by ating as this is not fetched
            $data["assignments"] = "";//$this->return_assignments_rules($data["system_preferences"]["conveyancingInstrumentTypeId"]);
            if ($data["assignments"]) {
                $data["conveyancingData"]["assignee_fullname"] = $data["assignments"]["user"]["name"];
            }

            $data["hide_show_notification"] = $this->email_notification_scheme->get_hide_show_send_email_notification_by_trigger_action("add_opinions");//consider revision to conveyancing
//            $this->load->model("reminder", "reminderfactory");
//            $this->reminder = $this->reminderfactory->get_instance();
//            $data["notify_me_before_time_types"] = array_combine($this->reminder->get("notify_me_before_time_type"), [$this->lang->line("day_or_days"), $this->lang->line("week_or_weeks"), $this->lang->line("month")]);
//            $data["notify_me_before_types"] = array_combine($this->reminder->get("notify_me_before_type"), [$this->lang->line("reminder_popup"), $this->lang->line("reminder_popup_and_email")]);
//            $data["notify_before"] = false;
            $data["custom_fields"] = $this->custom_field->get_field_html("conveyancing_instrument",0);//($this->conveyancing_instrument->get("modelName"), 0);

            $response["result"]= true;
            $response["html"] = $this->load->view("conveyancing/add", $data, true);
        }//loading the form
        else { //process form
            $description = $this->input->post("description", true, "<h1><h2><h3><h4><h5><h6><strong><em><u><a><p><img>");
            $_POST["description"] = format_comment_patterns($this->regenerate_note($description));
            $is_clone = $this->input->post("clone");
            $post_data = $this->input->post(NULL);
            $this->conveyancing_instrument->set_fields($post_data);
            $this->conveyancing_instrument->set_field("description", $this->input->post("description", true, "<h1><h2><h3><h4><h5><h6><strong><em><u><a><p><img>"));
            $this->conveyancing_instrument->set_field("initiated_by",$this->ci->session->userdata("CP_user_id"));
            $this->conveyancing_instrument->set_field("archived", "no");
            $this->conveyancing_instrument->set_field("createdBy", $this->session->userdata('CP_user_id'));

            $lookup_validate = false;//$this->conveyancing_instrument->get_lookup_validation_errors($this->conveyancing_instrument->get("lookupInputsToValidate"), $post_data);

            $custom_field_validation = !empty($post_data["customFields"]) ? $this->custom_field->validate_custom_field($post_data["customFields"]) : "";
            if ($this->conveyancing_instrument->validate() && !$lookup_validate && (empty($post_data["customFields"]) || $custom_field_validation["result"])) {
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
                    $result = $this->conveyancing_instrument->insert();




                    if ($result && $this->ci->session->userdata("CP_user_id")){ //cp logged in
                        $response["id"] = $this->conveyancing_instrument->get_field("id");
                        // $this->conveyancing_activity->update_activity_status($response["id"] , "Application Initiated");


                    }
                    if ($result && $this->is_auth->get_user_id()) { // teh $this->is_auth->get_user_id(); is used to check if is core user
                        $conveyancing_instrument_id = $this->conveyancing_instrument->get_field("id");
                        // $this->conveyancing_activity->update_activity_status($conveyancing_instrument_id, "conveyancing");
                        $response["conveyancing_instrument_code"] = $this->conveyancing_instrument->get("modelCode") . $conveyancing_instrument_id;
                        $response["id"] = $conveyancing_instrument_id;

                        if (!empty($post_data["customFields"]) && is_array($post_data["customFields"]) && count($post_data["customFields"])) {
                            foreach ($post_data["customFields"] as $key => $field) {
                                $post_data["customFields"][$key]["recordId"] = $conveyancing_instrument_id;
                            }
                            $this->custom_field->update_custom_fields($post_data["customFields"]);
                        }
                        $this->notify_me_before_due_date($conveyancing_instrument_id);
                        $getting_started_settings = unserialize($this->user_preference->get_value("getting_started"));
                        $getting_started_settings["add_conveyancing_step_done"] = true;
                        $this->user_preference->set_value("getting_started", serialize($getting_started_settings), true);
                        $inserted = $this->insert_related_users($conveyancing_instrument_id, "add_conveyancing_instrument");
                        $response["totalNotifications"] = $inserted["total_notifications"];

                        $failed_uploads_count = 0;
                        foreach ($_FILES as $file_key => $file) {
                            if ($file["error"] != 4) {
                                $upload_response = $this->dmsnew->upload_file(["module" => "conveyancing", "module_record_id" => $conveyancing_instrument_id, "lineage" => $this->input->post("lineage"), "upload_key" => $file_key]);
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
                    $conveyancing_instrument_id = $this->conveyancing_instrument->get_field("id");
                    $this->load->model(["conveyancing_instrument_status", "user_profile"]);
                    $this->load->model("conveyancing_instrument_type", "conveyancing_instrument_typefactory");
                    $this->conveyancing_instrument_type = $this->conveyancing_instrument_typefactory->get_instance();
                    $data = [];
                    $data["conveyancingData"] = $this->conveyancing_instrument->load_conyencing_instrument($conveyancing_instrument_id);
                    if ($data["conveyancingData"]) {
                        $data["conveyancingData"]["createdBy"] = "";
                        $data["conveyancingData"]["id"] = "";
                        $this->user_profile->fetch(["user_id" => $data["conveyancingData"]["assigned_to"]]);
                        $data["type"] = $this->conveyancing_instrument_type->load_list_per_language();
                        $data["status"] = $this->conveyancing_instrument_status->load_list();
                        $data["toMeId"] = $this->is_auth->get_user_id();
                        $data["toMeFullName"] = $this->is_auth->get_fullname();
                        $data["conveyancingModelCode"] = $this->conveyancing_instrument->get("modelCode");
                        $data["opinionUsers"] = $this->opinion->load_conveyancing_instrument_users($conveyancing_instrument_id);
                        $response["cloned"] = true;
                    }
                }
                $response["result"] = $result && $clone_result;
            } else {
                $response["validationErrors"] = $this->conveyancing_instrument->get_validation_errors($lookup_validate);
                if (!empty($post_data["customFields"]) && !$custom_field_validation["result"]) {
                    $response["validationErrors"] = $response["validationErrors"] + $custom_field_validation["validationErrors"];
                }
            }
        }
        $response["instrument"] = $this->conveyancing_instrument->get_fields();

        $this->output->set_content_type("application/json");
        $this->output->set_output(json_encode($response));
    }

    public function save()
    {
        // Validation rules
        $rules = [
            "instrument_type" => "required",
            "parties_involved" => "required",
            "description" => "permit_empty"
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        // Generate reference number
        $refNumber = $this->conveyancing_instrument->generate_reference_number();

        // Get user ID from session
        $userId = $this->ci->session->userdata("CP_user_id");

        $data = [
            "reference_number" => $refNumber,
            "instrument_type" => $this->request->getPost("instrument_type"),
            "parties_involved" => $this->request->getPost("parties_involved"),
            "description" => $this->request->getPost("description"),
            "status" => "pending",
            "initiated_by" => $userId,
            "external_counsel_id" => $this->request->getPost("external_counsel_id"),
            "date_initiated" => date("Y-m-d H:i:s")
        ];

        if ($this->conveyancing_instrument->insert($data)) {
            $instrumentId = $this->conveyancing_instrument->get_insert_id();

            // Add initial activity
            $this->conveyancing_activity->insert([
                "conveyancing_id" => $instrumentId,
                "activity_type_id" => "Creation",
                "activity_details" => "Conveyancing instrument created",
                "createdBy" => $userId,
                "CreatedOn" => date("Y-m-d H:i:s")
            ]);

            return redirect()->to("/conveyancing/view/$instrumentId")
                ->with('message', $this->lang->line("conveyancing_instrument_created"));
        } else {
            return redirect()->back()
                ->withInput()
                ->with('error', $this->lang->line("failed_to_create_conveyancing_instrument"));
        }
    }
    private function notify_me_before_due_date($conveyancing_instrumentId)
    {
        $notify_before = $this->input->post("notify_me_before");
        $due_date = $this->input->post("due_date");
        $current_reminder = $this->reminder->load_notify_before_data_to_related_object($conveyancing_instrumentId, $this->conveyancing_instrument->get("_table"));
        if ($current_reminder && !$notify_before) {
            return $this->reminder->remind_before_due_date([], $current_reminder["id"]);
        }
        if ($notify_before && $due_date) {
            $reminder = ["user_id" => $this->is_auth->get_user_id(), "remindDate" => $due_date, "conveyancing_instrument_id" => $conveyancing_instrumentId, "related_object" => $this->conveyancing_instrument->get("_table"), "notify_before_time" => $notify_before["time"], "notify_before_time_type" => $notify_before["time_type"], "notify_before_type" => $notify_before["type"]];
            $reminder["summary"] = sprintf($this->lang->line("notify_me_before_message"), $this->lang->line("conveyancing"), $this->opinion->get("modelCode") . $conveyancing_instrumentId, $due_date);
            return $this->reminder->remind_before_due_date($reminder, isset($notify_before["id"]) ? $notify_before["id"] : NULL);
        }
        return false;
    }
    private function return_assignments_rules($type)
    {
        $response = false;
        $this->load->model("assignment", "assignmentfactory");
        $this->assignment = $this->assignmentfactory->get_instance();
        if ($type && $this->assignment->fetch(["category" => "conveyancing", "type" => $type])) {
            $response = $this->assignment->get_fields();
        }
        if ($response) {
            $response["assignment_relation"] = "";
            switch ($response["assignment_rule"]) {
                case "rr_algorithm":
                    $next_assignee = $this->assignment->load_next_conveyancing_instrument_assignee($response["id"]);
                    $user_id = $next_assignee["user_id"] ?: "";
                    break;
                default:
                    $user_id = $response["assignment_rule"];
            }
            $response["user"] = $this->user->get_name_by_id($user_id);

        }
        return $response;
    }
    public function upload_file()
    { $response=[];
        if ($this->input->get(NULL, true)) {
            $conveyancing_id = $this->input->get("conveyancing_id", true);
            $data = $this->load_documents_form_data($conveyancing_id, $this->input->get("lineage", true));
            $data["title"] = $this->lang->line("upload_file");
            $data["module"] = "conveyancing";
            $response["result"] = true;
            $response["html"] = $this->load->view("conveyancing/documents/upload_form", $data, true);
        }
        if ($this->input->post(NULL, true)) {
            if (!$_FILES["uploadDoc"]["name"]) {
                $response["status"] = false;
                $response["validation_errors"]["uploadDoc"] = $this->lang->line("file_required");
            } else {
                $response = $this->dmsnew->upload_file(["module" => $this->input->post("module"), "module_record_id" => $this->input->post("module_record_id"), "lineage" => $this->input->post("lineage"), "upload_key" => "uploadDoc", "document_type_id" => $this->input->post("document_type_id"), "document_status_id" => $this->input->post("document_status_id"), "comment" => $this->input->post("comment")]);
                $this->load->model("document_management_system", "document_management_systemfactory");
                $this->document_management_system = $this->document_management_systemfactory->get_instance();
                $response["related_documents_count"] = $this->document_management_system->count_contract_related_documents($this->input->post("module_record_id"));
                $response["module_record_id"] = $this->input->post("module_record_id");
            }
        }
        $this->output->set_content_type("application/json")->set_output(json_encode($response));
    }
    public function load_documents()
    {
        $data = $this->dmsnew->load_documents(["module" => $this->input->post("module"), "module_record_id" => $this->input->post("module_record_id"), "lineage" => $this->input->post("lineage"), "term" => $this->input->post("term")]);

        $response["html"] = $this->load->view("conveyancing/document_item",  $data
        , true);
        if ($this->input->is_ajax_request()) {
            $this->output->set_content_type("application/json");
            $this->output->set_output(json_encode($response));
        } else {
            return $response;
        }
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
            $response["document"] = $this->dmsnew->get_document_details(["id" => $id]);
            $response["document"]["url"] = app_url("contacts/preview_document/" . $id);
        }
        $response["html"] = $this->load->view("documents_management_system/view_document", ["mode" => "preview"], true);
        $this->output->set_content_type("application/json")->set_output(json_encode($response));
    }

    public function download_file($file_id, $newest_version = false)
    {
        if (!$this->is_auth->is_logged_in()) {
            redirect("users/login");
        }
        $this->load->library("dmsnew");
        $this->dmsnew->download_file("conveyancing", $file_id, $newest_version);
    }

      // AJAX methods
    public function get_external_counsels()
    {
        $term = $this->request->getGet("term");
        $results = $this->external_counsel->search($term);

        return $this->response->setJSON($results);
    }
    public function assign_external_counsel($instrument_id)
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setStatusCode(403);
        }

        $counsel_id = $this->request->getPost("counsel_id");
        $response = ["success" => false];

        if ($this->conveyancing_instrument->update($instrument_id, ["external_counsel_id" => $counsel_id])) {
            // Log activity
            $counsel = $this->external_counsel->find($counsel_id);
            $this->conveyancing_activity->insert([
                "conveyancing_id" => $instrument_id,
                "activity_type_id" => 1,
                "activity_details" => "Assigned to external counsel: " . $counsel['firm_name'],
                "createdBy" => $this->ci->session->userdata("CP_user_id"),
                "createdOn" => date("Y-m-d H:i:s")
            ]);

            $response["success"] = true;
            $response["message"] = $this->lang->line("counsel_assigned_successfully");
        } else {
            $response["message"] = $this->lang->line("failed_to_assign_counsel");
        }

        return $this->response->setJSON($response);
    }

    public function update_status($instrument_id)
    {
        if (!$this->input->is_ajax_request()) {
            show_404();
        }

        $status = $this->request->getPost("status");
        $response = ["success" => false];

        if ($this->conveyancing_instrument->update($instrument_id, ["status" => $status])) {
            // Log activity
            $this->conveyancing_activity->insert([
                "conveyancing_id" => $instrument_id,
                "activity_type" => "Status Change",
                "activity_details" => "Status changed to " . $status,
                "createdBy" => $this->ci->session->userdata("CP_user_id"),
                "createdOn" => date("Y-m-d H:i:s")
            ]);

            $response["success"] = true;
            $response["message"] = $this->lang->line("status_updated_successfully");
        } else {
            $response["message"] = $this->lang->line("failed_to_update_status");
        }

        return $this->response->setJSON($response);
    }


    public function add_note_update()
    {
        if (!$this->input->is_ajax_request()) {
            show_404();
        }

        $updateType = $this->input->post('updateType');
        $params = $this->input->post('params'); // expects an array

        // Check for file upload
        if (isset($_FILES['attachment']) && $_FILES['attachment']['error'] == UPLOAD_ERR_OK) {
            $upload_path = FCPATH . 'attachments/';
            if (!is_dir($upload_path)) {
                mkdir($upload_path, 0777, true);
            }
            $filename = uniqid() . '_' . basename($_FILES['attachment']['name']);
            $target = $upload_path . $filename;
            if (move_uploaded_file($_FILES['attachment']['tmp_name'], $target)) {
                // Save $filename or $target in your DB as needed
                $params['file'] = $filename;
            } else {
                $response = [
                    'result' => false,
                    'display_message' => 'Failed to upload attachment.'
                ];
                return $this->output->set_content_type('application/json')->set_output(json_encode($response));
            }
        } else {
            // If you want to require a file, check here:
            // $response = ['result' => false, 'display_message' => 'Attachment is required.'];
            // return $this->output->set_content_type('application/json')->set_output(json_encode($response));
        }

        // Validate input
        if (!$updateType || !is_array($params) || empty($params['instrument_id']) || empty($params['details'])) {
            $response = [
                'result' => false,
                'display_message' => $this->lang->line("data_missing")
            ];
            return $this->output->set_content_type('application/json')->set_output(json_encode($response));
        }

        $data = [
            'instrument_id' => $params['instrument_id'],
            'details' => $params['details'],
            'update_type' => $updateType,
            'createdBy' => $this->session->userdata('user_id'),
            'createdOn' => date('Y-m-d H:i:s')
        ];
        $action="";
        switch ($updateType){
            case "status":
                $action ="Status update";
                $data['details']= "Changed status to '" .$params['status']. "' with remarks: ". $data['details'];
                if ($this->conveyancing_instrument->fetch($params['instrument_id'])){
                    $this->conveyancing_instrument->set_field("status", $params['status']);
                    //$this->conveyancing_instrument->set_field("modifiedOn", date("Y-m-d H:i:s"));
                    if (!$this->conveyancing_instrument->update()){
                       return $response["validationErrors"] = $this->conveyancing_instrument->get("validationErrors");
                    }
                }
                break;
            case "note":
                $action ="Adding a Note";
                    break;
            case "reassign":
                $action ="User assignment ";

                $data['details']= "Assigned/Re-Assigned an officer. ". $data['details'];

                if ($this->conveyancing_instrument->fetch($params['instrument_id'])) {
                    $this->conveyancing_instrument->set_field("assignee_id", $params['assignee_id']);
                    $this->conveyancing_instrument->set_field("assignee_team_id", $params['provider_group_id']);
                    //$this->conveyancing_instrument->set_field("modifiedOn", date("Y-m-d H:i:s"));
                    if (!$this->conveyancing_instrument->update()) {
                        return $response["validationErrors"] = $this->conveyancing_instrument->get("validationErrors");
                    }
                }
                break;
            case "document":
                $action ="Document request";
                $document_type=$params['document_type'];

                $failed_uploads_count = 0;
                foreach ($_FILES as $file_key => $file) {
                    if ($file["error"] != 4) {
                        $upload_response = $this->dmsnew->upload_file(["module" => "case", "module_record_id" => $data['instrument_id'], "lineage" => "", "upload_key" => $file_key]);
                        if (!$upload_response["status"]) {
                            $failed_uploads_count++;
                        }
                    }
                }
                if (0 < $failed_uploads_count) {
                    $response = [
                        'result' => false,
                        'display_message' => sprintf($this->lang->line("files_were_not_uploaded"), $failed_uploads_count)
                    ];
                    return $this->output->set_content_type('application/json')->set_output(json_encode($response));

                }
                break;
                default:
                {
                  return  $response = [
                        'result' => false,
                        'display_message' => $this->lang->line("cannot_be_blank_rule")
                    ];
                }
        }

        $activityFields=[ "conveyancing_instrument_id"=> $data['instrument_id'],"createdByChannel"=>"A4L", "createdBy"=>$this->is_auth->get_user_id(), "createdOn"=>date("Y-m-d H:i:s"), "action"=>$action, "activity_details"=> $data['details']];
        $this->conveyancing_activity->set_fields($activityFields);
        if($this->conveyancing_activity->insert()){
            $response["result"]= true;
        }else{
            $response["display_message"] = $this->conveyancing_activity->get("validationErrors");
            $response["result"]= false;
        }
        return $this->output->set_content_type('application/json')->set_output(json_encode($response));
    }

    public function instrument_types(){
        echo "coming soon";
    }
    public function document_status(){  echo "coming soon";}
    public function transaction_types(){  echo "coming soon";}
    public function document_types(){  echo "coming soon";}
    public function manage_workflows(){  echo "coming soon";}

}