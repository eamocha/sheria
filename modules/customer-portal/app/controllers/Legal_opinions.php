<?php
require "Auth_controller.php";
class Legal_opinions extends Auth_Controller
{
    public $licenses_validity = "";
    public $license_type = "";

    public function __construct()
    {
        parent::__construct();
        $this->load->model("opinion", "opinionfactory");
        $this->opinion = $this->opinionfactory->get_instance();
        
        $this->pageTitle = sprintf($this->lang->line("sheria360_browser_title"), $this->lang->line("legal_opinions_management"));

        $this->load->model("opinion_comment", "opinion_commentfactory");
        $this->opinion_comment = $this->opinion_commentfactory->get_instance();

        $this->load->model("opinion_document", "opinion_documentfactory");

        $this->load->model("opinion_status");
        $this->load->model("customer_portal_users", "customer_portal_usersfactory");
        $this->customer_portal_users = $this->customer_portal_usersfactory->get_instance();

        $this->licenses_validity = $this->session->userdata("licenses_validity");
        $this->license_type = $this->session->userdata("license_type");
        $this->load->helper("revert_comment_html");
        $this->load->helper("format_comment_patterns");

        if (!$this->licenses_validity["both"] && $this->license_type !== "admin") {
            $this->setPinesMessage("error", $this->lang->line("invalid_license"));
            redirect("home");
        }
    }

    public function index()
    {
        $data = [];
        $status_filter = $this->input->get("status");
        $search_term = $this->input->get("search");
        $date_filter = $this->input->get("date_filter");
        $cp_user = $this->ci->session->userdata("CP_user_id");

        $data['opinions']= $this->opinion->k_load_all_cp_opinions($cp_user,$status_filter, $search_term, $date_filter);

        $this->includes("jquery/popper.min", "js");
        $this->includes("bootstrap/js/bootstrap4.6.1.bundle.min", "js");
        $this->includes("jquery/tinymce/tinymce.min","js");

        $this->includes("customerPortal/clientPortal/js/legal_opinions", "js");
        $this->load->view("partial/header");
        $this->load->view("legal_opinions/list", $data);
        $this->load->view("partial/footer");
    }

    public function add()
    {
        if (!$this->input->is_ajax_request()) {
            show_404();
        }


        if (!$this->input->post(NULL)) {
            $data["archivedValues"] = ["no","yes"];//array_combine($this->opinion->get("archivedValues"), $this->opinion->get("archivedValues"));
            $data["system_preferences"] = $this->session->userdata("systemPreferences");
            $data["loggedInCPUser"] = $this->session->userdata("AUTH_cp_user");

            $this->load->model("opinion_types_language");
            $this->load->model("language");
            $langId = $this->language->get_id_by_session_lang();
            $this->opinion_types_language->fetch(["opinion_type_id" => $this->opinion->get_field("opinion_type_id"), "language_id" => $langId]);
            $data["types"]=$this->opinion_types_language->get_field("name");;

            $this->load->model("opinion_status");
            $this->opinion_status->fetch($this->opinion->get_field("opinion_status_id"));
            $data["status"] = $this->opinion_status->get_field("name");;


            $data["priorities"] = array_combine($this->opinion->get("priorityValues"), [$this->lang->line("critical"), $this->lang->line("high"), $this->lang->line("medium"), $this->lang->line("low")]);
            $sDate = date("Y-m-d", time());
            $data["opinionData"] = ["id" => "", "contract_id" => "", "contract_name" => "", "legal_case_id" => "", "caseSubject" => "", "caseCategory" => "", "user_id" => "", "reporter" => "", "title" => "", "assigned_to" => "", "due_date" => $sDate, "private" => "", "priority" => "", "opinion_location_id" => "", "location" => "", "estimated_effort" => "", "detailed_info" => "", "background_info"=>"","requester"=>"","legal_question", "opinion_status_id" => "", "opinion_type_id" => "", "assignee_fullname" => "", "assignee_status" => "", "reporter_fullname" => "", "reporter_status" => "", "archived" => ""];
            $data["title"] = $this->lang->line("add_opinions");

            $data["system_preferences"]["conveyancingInstrumentTypeId"]=1;//addded by ating as this is not fetched
            $data["assignments"] = "";//$this->return_assignments_rules($data["system_preferences"]["conveyancingInstrumentTypeId"]);
            if ($data["assignments"]) {
                $data["opinionData"]["assignee_fullname"] = $data["assignments"]["user"]["name"];
            }
            $this->load->model("email_notification_scheme");
            $data["hide_show_notification"] = $this->email_notification_scheme->get_hide_show_send_email_notification_by_trigger_action("add_opinions");//consider revision to conveyancing
         //   $this->load->model("reminder", "reminderfactory");
//            $this->reminder = $this->reminderfactory->get_instance();
//            $data["notify_me_before_time_types"] = array_combine($this->reminder->get("notify_me_before_time_type"), [$this->lang->line("day_or_days"), $this->lang->line("week_or_weeks"), $this->lang->line("month")]);
//            $data["notify_me_before_types"] = array_combine($this->reminder->get("notify_me_before_type"), [$this->lang->line("reminder_popup"), $this->lang->line("reminder_popup_and_email")]);
//            $data["notify_before"] = false;
            $data["custom_fields"] ="";// $this->custom_field->get_field_html($this->opinion->get("modelName"), 0);

            $response["result"]= true;
            $response["html"] = $this->load->view("legal_opinions/add_form", $data, true);
        }else{

            $detailed_info = $this->input->post("detailed_info", true, "<h1><h2><h3><h4><h5><h6><strong><em><u><a><p><img>");
            $legal_question = $this->input->post("legal_question", true, "<h1><h2><h3><h4><h5><h6><strong><em><u><a><p><img>");
            $background_info = $this->input->post("background_info", true, "<h1><h2><h3><h4><h5><h6><strong><em><u><a><p><img>");
            $_POST["detailed_info"] = format_comment_patterns($this->regenerate_note($detailed_info));
            $_POST["legal_question"] = format_comment_patterns($this->regenerate_note($legal_question));
            $_POST["background_info"] = format_comment_patterns($this->regenerate_note($background_info));
            $is_clone = $this->input->post("clone");

            $post_data = $this->input->post(NULL);
            $this->opinion->set_fields($post_data);
            $this->opinion->set_field("detailed_info", $this->input->post("detailed_info", true, "<h1><h2><h3><h4><h5><h6><strong><em><u><a><p><img>"));
            $this->opinion->set_field("legal_question", $this->input->post("legal_question", true, "<h1><h2><h3><h4><h5><h6><strong><em><u><a><p><img>"));
            $this->opinion->set_field("background_info", $this->input->post("background_info", true, "<h1><h2><h3><h4><h5><h6><strong><em><u><a><p><img>"));
            $this->opinion->set_field("user_id",1);// $this->is_auth->get_user_id());
            $this->opinion->set_field("assigned_to",1);//unassigned
            $this->opinion->set_field("reporter",1);
            $this->opinion->set_field("is_visible_to_cp",1);
            $this->opinion->set_field("channel","CP");

            $this->opinion->set_field("requester", $this->ci->session->userdata("CP_user_id"));



            $this->opinion->set_field("archived", "no");
            $this->load->model("opinion_workflow", "opinion_workflowfactory");
            $this->opinion_workflow = $this->opinion_workflowfactory->get_instance();
            $workflow_applicable = $this->opinion_workflow->load_workflow_opinion_status_per_type($this->input->post("opinion_type_id")) ?: $this->opinion_workflow->load_default_system_workflow();
            $this->opinion->set_field("opinion_status_id", $workflow_applicable["status"]);
            $this->opinion->set_field("workflow", $workflow_applicable["workflow_id"]);
            $lookup_validate = $this->opinion->get_lookup_validation_errors($this->opinion->get("lookupInputsToValidate"), $post_data);

            $custom_field_validation = !empty($post_data["customFields"]) ? $this->custom_field->validate_custom_field($post_data["customFields"]) : "";
            if ($this->opinion->validate() && !$lookup_validate && (empty($post_data["customFields"]) || $custom_field_validation["result"])) {
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
                    $result = $this->opinion->insert();
                    if ($result) {
                        $opinion_id = $this->opinion->get_field("id");
                      //  $this->opinion->update_recent_ids($opinion_id, "opinions");
                        $response["opinion_code"] = "L".$this->opinion->get("modelCode") . $opinion_id;
                        $response["id"] = $opinion_id;
                        $case_id = $this->opinion->get_field("legal_case_id");
                        if ($case_id) {
                            $this->legal_case->set_field("id", $case_id);
                            $this->legal_case->touch_logs();
                            if ($this->opinion->get_field("stage")) {
                                $this->load->model("legal_case_litigation_detail", "legal_case_litigation_detailfactory");
                                $this->legal_case_litigation_detail = $this->legal_case_litigation_detailfactory->get_instance();
                                $this->legal_case_litigation_detail->update_stage_order($this->opinion->get_field("stage"));
                            }
                        }
                        if (!empty($post_data["customFields"]) && is_array($post_data["customFields"]) && count($post_data["customFields"])) {
                            foreach ($post_data["customFields"] as $key => $field) {
                                $post_data["customFields"][$key]["recordId"] = $opinion_id;
                            }
                            $this->custom_field->update_custom_fields($post_data["customFields"]);
                        }



//                        $this->load->library("dmsnew");
//                        $failed_uploads_count = 0;
//                        foreach ($_FILES as $file_key => $file) {
//                            if ($file["error"] != 4) {
//                                $upload_response = $this->dmsnew->upload_file(["module" => "opinion", "module_record_id" => $opinion_id, "lineage" => $this->input->post("lineage"), "upload_key" => $file_key]);
//                                if (!$upload_response["status"]) {
//                                    $failed_uploads_count++;
//                                }
//                            }
//                        }
//                        if (0 < $failed_uploads_count) {
//                            $response["validationErrors"]["files"] = sprintf($this->lang->line("files_were_not_uploaded"), $failed_uploads_count);
//                        }
                    }
                }
                $clone_result = true;
                if (isset($is_clone) && !strcmp($is_clone, "yes")) {
                    $opinion_id = $this->opinion->get_field("id");
                    $this->load->model(["opinion_status", "user_profile"]);
                    $this->load->model("opinion_type", "opinion_typefactory");
                    $this->opinion_type = $this->opinion_typefactory->get_instance();
                    $data = [];
                    $data["opinionData"] = $this->opinion->load_opinion($opinion_id);
                    if ($data["opinionData"]) {
                        $data["opinionData"]["createdBy"] = "";
                        $data["opinionData"]["id"] = "";
                        $this->user_profile->fetch(["user_id" => $data["opinionData"]["assigned_to"]]);
                        $data["type"] = $this->opinion_type->load_list_per_language();
                        $data["status"] = $this->opinion_status->load_list();
                        $data["toMeId"] = $this->is_auth->get_user_id();
                        $data["toMeFullName"] = $this->is_auth->get_fullname();
                        $data["opinionModelCode"] = $this->opinion->get("modelCode");
                        $data["priorities"] = array_combine($this->opinion->get("priorityValues"), [$this->lang->line("critical"), $this->lang->line("high"), $this->lang->line("medium"), $this->lang->line("low")]);
                        $data["opinionUsers"] = $this->opinion->load_opinion_users($opinion_id);
                        $response["cloned"] = true;
                    }
                }
                $response["result"] = $result && $clone_result;
            } else {
                $response["validationErrors"] = $this->opinion->get_validation_errors($lookup_validate);
                if (!empty($post_data["customFields"]) && !$custom_field_validation["result"]) {
                    $response["validationErrors"] = $response["validationErrors"] + $custom_field_validation["validationErrors"];
                }
            }
        }
        $this->output->set_content_type("application/json");
        $this->output->set_output(json_encode($response));

    }

    private function send_notification($id, $object, $data)
    { 
        $this->load->model("email_notification_scheme");
        $model = $this->opinion->get("_table"); 
        $model_data["id"] = $id;
        $data["contributors"] = $this->opinion->load_opinion_contributors($id);
        $model_data["contributors_ids"] = $data["contributors"] ? array_column($data["contributors"], "id") : [];
        $this->load->model("opinion_types_language");
        $this->load->model("language");
        $langId = $this->language->get_id_by_session_lang();
        $this->opinion_types_language->fetch(["opinion_type_id" => $this->opinion->get_field("opinion_type_id"), "language_id" => $langId]);
        $opinionType = $this->opinion_types_language->get_field("name");
        if ($this->input->post("send_notifications_email") || $object == "edit_opinion_status") {
            $this->load->library("email_notifications");
            $objectType = $object;
            $notifications_emails = $this->email_notification_scheme->get_emails($objectType, $model, $model_data);
            extract($notifications_emails);
            $notificationsData["to"] = $to_emails;
            $notificationsData["cc"] = $cc_emails;
            $notificationsData["object_id"] = (int) $id;
            $notificationsData["fromLoggedUser"] = $this->is_auth->get_fullname();
            $notificationsData["object"] = $objectType;
            $notificationsData["objectModelCode"] = $this->opinion->get("modelCode");
            $this->opinion->fetch($id);
            $data["opinion_data"]["opinion_id"] = $id;
            $data["opinion_data"]["priority"] = $this->opinion->get_field("priority");
            $data["opinion_data"]["dueDate"] = $this->opinion->get_field("due_date");
            $data["opinion_data"]["opiniondetailed_info"] = nl2br($this->opinion->get_field("detailed_info"));
            $data["opinion_data"]["opinionType"] = $opinionType;
            $data["opinion_data"]["assignee"] = $this->email_notification_scheme->get_user_full_name($this->opinion->get_field("assigned_to"));
            $data["opinion_data"]["created_by"] = $notificationsData["fromLoggedUser"];
            $notificationsData["opinionData"] = $data["opinion_data"];
            $this->email_notifications->notify($notificationsData);
        }
        $assignee_id = str_pad($this->opinion->get_field("assigned_to"), 10, "0", STR_PAD_LEFT);
        $reporter_id = str_pad($this->opinion->get_field("reporter"), 10, "0", STR_PAD_LEFT);
        $toIds = [$assignee_id, $reporter_id];
        if (!empty($model_data["contributors_ids"])) {
            $toIds = array_merge($toIds, $model_data["contributors_ids"]);
        }
        $notificationsData = ["toIds" => array_unique($toIds), "object" => $object, "object_id" => (int) $id, "objectModelCode" => $this->opinion->get("modelCode"), "targetUser" => $assignee_id, "opinionData" => ["opinionID" => (int) $id, "priority" => $this->opinion->get_field("priority"), "dueDate" => $this->opinion->get_field("due_date"), "opiniondetailed_info" => nl2br($this->opinion->get_field("detailed_info")), "modifiedBy" => $this->email_notification_scheme->get_user_full_name($this->opinion->get_field("modifiedBy"))], "attachments" => []];
        $this->load->library("system_notification");
        $this->system_notification->notification_add($notificationsData);
    }

    public function view($request_id)
    {
        if (!$request_id || !$this->legal_opinion->fetch($request_id)) {
            $this->setPinesMessage("error", $this->lang->line("invalid_record"));
            redirect("legal_opinions");
        }

        $data = [];
        $data["request"] = $this->legal_opinion->get_fields();
        $data["comments"] = $this->legal_opinion_comment->get_comments($request_id);
        $data["attachments"] = $this->legal_opinion_attachment->get_attachments($request_id);
        $data["workflow"] = $this->legal_opinion->get_workflow_history($request_id);

        // Check permissions
        $user_id = $this->session->userdata("user_id");
        $data["can_edit"] = $this->can_edit_request($request_id, $user_id);
        $data["can_assign"] = $this->can_assign_request($user_id);


        $this->includes("legal_opinions/js/view", "js");
        $this->load->view("partial/header");
        $this->load->view("legal_opinions/view", $data);
        $this->load->view("partial/footer");
    }

    public function edit($request_id)
    {
        if (!$this->input->is_ajax_request() && !$this->input->post()) {
            if (!$request_id || !$this->legal_opinion->fetch($request_id)) {
                $this->setPinesMessage("error", $this->lang->line("invalid_record"));
                redirect("legal_opinions");
            }

            $data = [];
            $data["request"] = $this->legal_opinion->get_fields();
            $this->includes("legal_opinions/js/edit", "js");
            $this->load->view("partial/header");
            $this->load->view("legal_opinions/edit", $data);
            $this->load->view("partial/footer");
            return;
        }

        $response = ["result" => false];

        $this->form_validation->set_rules("subject", "Subject", "required");
        $this->form_validation->set_rules("background", "Background", "required");
        $this->form_validation->set_rules("details", "Detailed Information", "required");
        $this->form_validation->set_rules("legal_question", "Legal Question", "required");
        $this->form_validation->set_rules("due_date", "Due Date", "required");

        if ($this->form_validation->run()) {
            $this->legal_opinion->fetch($request_id);
            $this->legal_opinion->set_fields($this->input->post());
            $this->legal_opinion->set_field("modified_by", $this->session->userdata("user_id"));
            $this->legal_opinion->set_field("modified_on", date("Y-m-d H:i:s"));

            if ($this->legal_opinion->update()) {
                $response["result"] = true;
                $response["message"] = $this->lang->line("request_updated_successfully");
                $response["redirect"] = site_url("legal_opinions/view/" . $request_id);
            } else {
                $response["validation_errors"] = $this->legal_opinion->get("validationErrors");
            }
        } else {
            $response["validation_errors"] = $this->form_validation->error_array();
        }

        $this->output->set_content_type("application/json")->set_output(json_encode($response));
    }

   

   
    //function to fetch and display legal item on a modal
    public function fetch_legal_opinion_item($item_id)
    {
        if (!$this->input->is_ajax_request()) {
            show_404();
        }

        $response = ["result" => false];
        $data["request"]=$this->opinion->load_opinion($item_id);
        $this->load->model("opinion_comment", "opinion_commentfactory");

        $this->opinion_comment = $this->opinion_commentfactory->get_instance();
        $data["comments"] = $this->opinion_comment->load_comments($item_id, "showAll");  

        //$data["attachments"] = $this->legal_opinion_attachment->get_attachments($request_id);
       // $data["workflow"] = $this->legal_opinion->get_workflow_history($request_id);
  

        if ($item_id && $this->opinion->fetch($item_id)) {
            $response["result"] = true;
              $response["result"]= true;
           // $data["request"] = $this->opinion->get_fields();
            $response["html"] = $this->load->view("legal_opinions/view_item", $data, true);
        } else {
            $response["error"] = $this->lang->line("invalid_record");
        }

        $this->output->set_content_type("application/json")->set_output(json_encode($response));
    }

    public function add_comment($opinion_id)
    {
        if (!$this->input->is_ajax_request()) {
            show_404();
        }

        $response = ["result" => false];
        //load opinion comment model
        $this->load->model("opinion_comment", "opinion_commentfactory");
        $this->opinion_comment = $this->opinion_commentfactory->get_instance();
    
            $this->opinion_comment->set_field("opinion_id", $opinion_id);
            $this->opinion_comment->set_field("comment", $this->input->post("comment", true, "<h1><h2><h3><h4><h5><h6><strong><em><u><a><p><img>"));
            $this->opinion_comment->set_field("createdBy", $this->ci->session->userdata("CP_user_id"));
            $this->opinion_comment->set_field("createdOn", date("Y-m-d H:i:s"));
            $this->opinion_comment->set_field("edited", 0);
            $this->opinion_comment->set_field("added_from_channel", "CP");
           

            if ($this->opinion_comment->insert()) {
              // exit(json_encode($this->opinion_comment->get_fields()));
                $response["result"] = true;
                $response["message"] = "comment_added_successfully";
                $response["commentHtml"] = $this->load->view("legal_opinions/cp_comments", [
                    "comment" => $this->opinion_comment->get_fields()
                ], true);
            } else {
                $response["validation_errors"] = $this->opinion_comment->get("validationErrors");
            }        

        $this->output->set_content_type("application/json")->set_output(json_encode($response));
    }

    public function assign($request_id)
    {
        if (!$this->input->is_ajax_request()) {
            show_404();
        }

        $response = ["result" => false];

        $this->form_validation->set_rules("assignee_id", "Assignee", "required");
        $this->form_validation->set_rules("assignee_type", "Assignee Type", "required");

        if ($this->form_validation->run()) {
            $this->legal_opinion->fetch($request_id);
            $this->legal_opinion->set_field("assigned_to", $this->input->post("assignee_id"));
            $this->legal_opinion->set_field("assigned_type", $this->input->post("assignee_type"));
            $this->legal_opinion->set_field("status", "assigned");
            $this->legal_opinion->set_field("modified_by", $this->session->userdata("user_id"));
            $this->legal_opinion->set_field("modified_on", date("Y-m-d H:i:s"));

            if ($this->legal_opinion->update()) {
                // Add to workflow history
                $this->add_workflow_history($request_id, "assigned", "Assigned to " . $this->get_assignee_name());

                // Notify assignee
                $this->notify_assignee();

                $response["result"] = true;
                $response["message"] = $this->lang->line("request_assigned_successfully");
                $response["status_badge"] = '<span class="badge badge-info status-badge">Assigned</span>';
            } else {
                $response["validation_errors"] = $this->legal_opinion->get("validationErrors");
            }
        } else {
            $response["validation_errors"] = $this->form_validation->error_array();
        }

        $this->output->set_content_type("application/json")->set_output(json_encode($response));
    }

    public function update_status($request_id)
    {
        if (!$this->input->is_ajax_request()) {
            show_404();
        }

        $response = ["result" => false];

        $this->form_validation->set_rules("status", "Status", "required");

        if ($this->form_validation->run()) {
            $this->legal_opinion->fetch($request_id);
            $old_status = $this->legal_opinion->get_field("status");
            $new_status = $this->input->post("status");

            $this->legal_opinion->set_field("status", $new_status);
            $this->legal_opinion->set_field("modified_by", $this->session->userdata("user_id"));
            $this->legal_opinion->set_field("modified_on", date("Y-m-d H:i:s"));

            if ($this->legal_opinion->update()) {
                // Add to workflow history
                $this->add_workflow_history($request_id, $old_status, $new_status);

                // Notify relevant parties
                $this->notify_status_change($request_id, $old_status, $new_status);

                $response["result"] = true;
                $response["message"] = $this->lang->line("status_updated_successfully");
                $response["status_badge"] = $this->get_status_badge($new_status);
            } else {
                $response["validation_errors"] = $this->legal_opinion->get("validationErrors");
            }
        } else {
            $response["validation_errors"] = $this->form_validation->error_array();
        }

        $this->output->set_content_type("application/json")->set_output(json_encode($response));
    }

    public function upload_attachment($request_id)
    {
        if (!$this->input->is_ajax_request()) {
            show_404();
        }

        $response = ["result" => false];

        if (!empty($_FILES["file"]["name"])) {
            $config["upload_path"] = "./uploads/legal_opinions/";
            $config["allowed_types"] = "pdf|doc|docx|xls|xlsx|jpg|jpeg|png";
            $config["max_size"] = 5120; // 5MB

            $this->load->library("upload", $config);

            if ($this->upload->do_upload("file")) {
                $upload_data = $this->upload->data();

                $this->legal_opinion_attachment->set_field("request_id", $request_id);
                $this->legal_opinion_attachment->set_field("file_name", $upload_data["file_name"]);
                $this->legal_opinion_attachment->set_field("original_name", $upload_data["orig_name"]);
                $this->legal_opinion_attachment->set_field("file_size", $upload_data["file_size"]);
                $this->legal_opinion_attachment->set_field("file_type", $upload_data["file_type"]);
                $this->legal_opinion_attachment->set_field("uploaded_by", $this->session->userdata("user_id"));
                $this->legal_opinion_attachment->set_field("uploaded_on", date("Y-m-d H:i:s"));

                if ($this->legal_opinion_attachment->insert()) {
                    $response["result"] = true;
                    $response["message"] = $this->lang->line("attachment_uploaded_successfully");
                    $response["attachment"] = $this->legal_opinion_attachment->get_fields();
                } else {
                    $response["error"] = $this->lang->line("error_saving_attachment");
                }
            } else {
                $response["error"] = $this->upload->display_errors("", "");
            }
        } else {
            $response["error"] = $this->lang->line("no_file_selected");
        }

        $this->output->set_content_type("application/json")->set_output(json_encode($response));
    }

    public function download_attachment($attachment_id)
    {
        if (!$this->legal_opinion_attachment->fetch($attachment_id)) {
            $this->setPinesMessage("error", $this->lang->line("invalid_record"));
            redirect("legal_opinions");
        }

        $file_path = "./uploads/legal_opinions/" . $this->legal_opinion_attachment->get_field("file_name");

        if (file_exists($file_path)) {
            $this->load->helper("download");
            force_download(
                $this->legal_opinion_attachment->get_field("original_name"),
                file_get_contents($file_path)
            );
        } else {
            $this->setPinesMessage("error", $this->lang->line("file_not_found"));
            redirect("legal_opinions/view/" . $this->legal_opinion_attachment->get_field("request_id"));
        }
    }

    public function sign_document($request_id)
    {
        if (!$this->input->is_ajax_request()) {
            show_404();
        }

        $response = ["result" => false];

        $this->form_validation->set_rules("signature_type", "Signature Type", "required");

        if ($this->form_validation->run()) {
            $this->legal_opinion->fetch($request_id);

            if ($this->legal_opinion->get_field("status") !== "completed") {
                $response["error"] = $this->lang->line("only_completed_requests_can_be_signed");
            } else {
                // In a real implementation, you would handle the digital signing process here
                // This is just a placeholder for the functionality

                $this->legal_opinion->set_field("signed_by", $this->session->userdata("user_id"));
                $this->legal_opinion->set_field("signed_on", date("Y-m-d H:i:s"));
                $this->legal_opinion->set_field("signature_type", $this->input->post("signature_type"));

                if ($this->legal_opinion->update()) {
                    $response["result"] = true;
                    $response["message"] = $this->lang->line("document_signed_successfully");
                } else {
                    $response["validation_errors"] = $this->legal_opinion->get("validationErrors");
                }
            }
        } else {
            $response["validation_errors"] = $this->form_validation->error_array();
        }

        $this->output->set_content_type("application/json")->set_output(json_encode($response));
    }

    private function handle_attachments($request_id)
    {
        $files = $_FILES["attachments"];
        $file_count = count($files["name"]);

        $config["upload_path"] = "./uploads/legal_opinions/";
        $config["allowed_types"] = "pdf|doc|docx|xls|xlsx|jpg|jpeg|png";
        $config["max_size"] = 5120; // 5MB

        $this->load->library("upload", $config);

        for ($i = 0; $i < $file_count; $i++) {
            $_FILES["file"]["name"] = $files["name"][$i];
            $_FILES["file"]["type"] = $files["type"][$i];
            $_FILES["file"]["tmp_name"] = $files["tmp_name"][$i];
            $_FILES["file"]["error"] = $files["error"][$i];
            $_FILES["file"]["size"] = $files["size"][$i];

            if ($this->upload->do_upload("file")) {
                $upload_data = $this->upload->data();

                $this->legal_opinion_attachment->set_field("request_id", $request_id);
                $this->legal_opinion_attachment->set_field("file_name", $upload_data["file_name"]);
                $this->legal_opinion_attachment->set_field("original_name", $upload_data["orig_name"]);
                $this->legal_opinion_attachment->set_field("file_size", $upload_data["file_size"]);
                $this->legal_opinion_attachment->set_field("file_type", $upload_data["file_type"]);
                $this->legal_opinion_attachment->set_field("uploaded_by", $this->session->userdata("user_id"));
                $this->legal_opinion_attachment->set_field("uploaded_on", date("Y-m-d H:i:s"));

                $this->legal_opinion_attachment->insert();
                $this->legal_opinion_attachment->reset_fields();
            }
        }
    }

    private function notify_requester($request_id)
    {
        // Implementation would send email notification to requester
        // Placeholder for actual notification logic
    }

    private function notify_assignee()
    {
        // Implementation would send email notification to assignee
        // Placeholder for actual notification logic
    }

    private function notify_status_change($request_id, $old_status, $new_status)
    {
        // Implementation would send notifications based on status change
        // Placeholder for actual notification logic
    }

    private function add_workflow_history($request_id, $action, $details)
    {
        $this->load->model("legal_opinion_workflow", "legal_opinion_workflowfactory");
        $this->legal_opinion_workflow = $this->legal_opinion_workflowfactory->get_instance();

        $this->legal_opinion_workflow->set_field("request_id", $request_id);
        $this->legal_opinion_workflow->set_field("action", $action);
        $this->legal_opinion_workflow->set_field("details", $details);
        $this->legal_opinion_workflow->set_field("performed_by", $this->session->userdata("user_id"));
        $this->legal_opinion_workflow->set_field("performed_on", date("Y-m-d H:i:s"));

        $this->legal_opinion_workflow->insert();
    }

    private function get_assignee_name()
    {
        $assignee_id = $this->input->post("assignee_id");
        $assignee_type = $this->input->post("assignee_type");

        if ($assignee_type === "user") {
            $this->user->fetch($assignee_id);
            return $this->user->get_field("name");
        } else {
            // Handle other assignee types (groups, roles, etc.)
            return "Assignee " . $assignee_id;
        }
    }

    private function get_status_badge($status)
    {
        $badges = [
            "new" => "badge-secondary",
            "assigned" => "badge-info",
            "in_progress" => "badge-warning",
            "review" => "badge-primary",
            "completed" => "badge-success",
            "rejected" => "badge-danger"
        ];

        return '<span class="badge ' . ($badges[$status] ?? "badge-secondary") . ' status-badge">' .
            ucwords(str_replace("_", " ", $status)) . '</span>';
    }

    private function can_edit_request($request_id, $user_id)
    {
        // Implement logic to check if user can edit this request
        // This is a simplified example - adjust based on your requirements
        $this->legal_opinion->fetch($request_id);

        return $this->legal_opinion->get_field("created_by") == $user_id ||
            $this->legal_opinion->get_field("assigned_to") == $user_id ||
            $this->session->userdata("is_admin");
    }

    private function can_assign_request($user_id)
    {
        // Implement logic to check if user can assign requests
        // This is a simplified example - adjust based on your requirements
        return $this->session->userdata("is_admin") ||
            in_array($this->session->userdata("role_id"), [/* roles that can assign */]);
    }
}