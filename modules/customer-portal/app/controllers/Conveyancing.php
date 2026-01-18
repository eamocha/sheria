<?php
require "Auth_controller.php";

class Conveyancing extends Auth_Controller
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

        $this->load->model("external_counsel", "external_counselfactory");
        $this->external_counsel = $this->external_counselfactory->get_instance();

        $this->load->model("user", "userfactory");
        $this->user = $this->userfactory->get_instance();

        $this->load->model("conveyancing_instrument_types", "conveyancing_instrument_typesfactory");
        $this->conveyancing_instrument_types = $this->conveyancing_instrument_typesfactory->get_instance();
        $this->load->model("conveyancing_transaction_types", "conveyancing_transaction_typesfactory");
        $this->conveyancing_transaction_types = $this->conveyancing_transaction_typesfactory->get_instance();

        $this->pageTitle = sprintf($this->lang->line("sheria360_browser_title"), $this->lang->line("conveyancing_management"));

        // Load libraries
        $this->load->library("dmsnew");
        $this->load->library("is_auth");
        $this->load->model("reminder", "reminderfactory");
        $this->reminder = $this->reminderfactory->get_instance();
        // $this->load->model("conveyancing_instrument_status", "conveyancing_instrument_statusfactory");
        // $this->conveyancing_instrument_status = $this->conveyancing_instrument_statusfactory->get_instance();
        // // $this->load->model("conveyancing_document_status", "conveyancing_document_statusfactory");
        // $this->conveyancing_document_status = $this->conveyancing_document_statusfactory->get_instance();
        // $this->load->library("conveyancing_document_type", "conveyancing_document_typefactory");
        // $this->conveyancing_document_type = $this->conveyancing_document_typefactory->get_instance();
        // $this->load->model("conveyancing_instrument_type", "conveyancing_instrument_typefactory");
        
    }

    public function all_instruments()
    {
        $data = [];
        $logged_in_user = $this->ci->session->userdata("CP_user_id");

        // Get counts for dashboard
       /* $all_instruments = $this->conveyancing_instrument->load_all_instruments($logged_in_user);
        $pending_instruments = $this->conveyancing_instrument->get_pending_instruments($logged_in_user);
        $in_progress_instruments = $this->conveyancing_instrument->get_in_progress_instruments($logged_in_user);

        $data["count"] = [
            "all_instruments" => count($all_instruments),
            "pending_instruments" => count($pending_instruments),
            "in_progress_instruments" => count($in_progress_instruments)
        ];
*/


        $this->includes("kendoui/js/kendo.web.min", "js");
        $this->includes("jquery/dropzone", "js");
        $this->includes("jquery/css/dropzone", "css");
        $this->includes("jquery/jquery.shiftcheckbox", "js");
        $this->includes("kendoui/styles/kendo.common-bootstrap.min", "css");
        $this->includes("kendoui/styles/kendo.bootstrap.min", "css");
        $this->includes("styles/contract/main", "css");

        $this->includes("customerPortal/clientPortal/js/conveyancing", "js");
        $this->load->view("partial/header");
        $this->load->view("conveyancing/list", $data);
        $this->load->view("partial/footer");
    }

    public function index()
    {
        $data = [];
        $logged_in_user = $this->ci->session->userdata("CP_user_id");

        $data["instruments"] = $this->conveyancing_instrument->load_cp_conveyancing_instruments($logged_in_user);


        $this->includes("kendoui/js/kendo.web.min", "js");
        $this->includes("jquery/dropzone", "js");
        $this->includes("jquery/css/dropzone", "css");
        $this->includes("jquery/jquery.shiftcheckbox", "js");
        $this->includes("kendoui/styles/kendo.common-bootstrap.min", "css");
        $this->includes("kendoui/styles/kendo.bootstrap.min", "css");
        $this->includes("styles/contract/main", "css");
        $this->includes("customerPortal/clientPortal/js/conveyancing", "js");

        $this->load->view("partial/header");
        $this->load->view("conveyancing/list", $data);
        $this->load->view("partial/footer");
    }

    /**
     * @return mixed
     */
    public function view($instrument_id)
    {
        if (!$instrument_id || !ctype_digit($instrument_id) || !$this->conveyancing_instrument->fetch($instrument_id)) {
            $this->setPinesMessage("error", $this->lang->line("invalid_record"));
            redirect("conveyancing");
        }


        $data["title"] = $this->lang->line("conveyancing_instrument_details");
       

        // Load instrument data
        $data["instrument"] = $this->conveyancing_instrument->cp_load_conveyancing_instrument_by_id($instrument_id);
     //   $data["documents"] = $this->conveyancing_document->get_documents_for_instrument($instrument_id);
     //   $data["activities"] = $this->conveyancing_activity->get_activities_for_instrument($instrument_id);

        // Load stakeholders
      //  $data["ca_staff"] = $this->user->fetch($this->conveyancing_instrument->get_field("initiated_by"));
       // $data["external_counsel"] = $this->external_counsel->fetch(
       //     $this->conveyancing_instrument->get_field("external_counsel_id"));

        // Related documents count
       // $data["related_documents_count"] = $this->dms->count_conveyancing_related_documents($instrument_id, true);

        // Load views and assets
      //  $this->includes("jquery/tinymce/tinymce.min", "js");
      //  $this->includes("customerPortal/clientPortal/js/conveyancing_view", "js");
        //$this->includes("customerPortal/clientPortal/js/conveyancing", "js");
        $this->includes("customerPortal/clientPortal/js/conveyancing_documents","js");
       // $this->includes("conveyancing/cp_conveyancing_common", "js");
       // $this->includes("conveyancing/related_documents", "js");
       // $this->includes("styles/conveyancing/main", "css");

        $this->load->view("partial/header");
        $this->load->view("conveyancing/conveyancing-detail", $data);
        $this->load->view("partial/footer");
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


        $conveyancing_instrument_types = $this->conveyancing_instrument_types->load_list();
        $transaction_types = $this->conveyancing_transaction_types->load_list();
        $this->load->helper("revert_comment_html");
        $this->load->helper("format_comment_patterns");
        if (!$this->input->post(NULL)) {

            $data["archivedValues"] = array_combine($this->conveyancing_instrument->get("archivedValues"), [$this->lang->line("yes"), $this->lang->line("no")]);
            $data["defaultArchivedValue"] = "no";
            $data["system_preferences"] = $this->session->userdata("systemPreferences");
            $data["types"] = $conveyancing_instrument_types;
            $data["transaction_types"]=$transaction_types;
            $data["status"] = "pending";

            $sDate = date("Y-m-d", time());
            $data["conveyancingData"] = ["id" => "", "instrument_type_id" => "","transaction_type_id" => "","user_id" => "", "title" => "", "parties" => "", "initiated_by" => "", "staff_pf_no" => "","date_initiated" => $sDate,"description" => "", "archived" => "no","status" => "", "external_counsel" => "", "property_value" => "", "amount_requested" => "", "assigned_to" => "", "amount_approved" => "", "assignee_fullname" => "", "assignee_status" => ""  ];
            $data["title"] = $this->lang->line("conveyancing_request");
            $data["system_preferences"]["conveyancingInstrumentTypeId"]=1;//addded by ating as this is not fetched
            $data["assignments"] = "";//$this->return_assignments_rules($data["system_preferences"]["conveyancingInstrumentTypeId"]);
            if ($data["assignments"]) {
                $data["conveyancingData"]["assignee_fullname"] = $data["assignments"]["user"]["name"];
            }

            $data["hide_show_notification"] = $this->email_notification_scheme->get_hide_show_send_email_notification_by_trigger_action("add_opinions");//consider revision to conveyancing
            $data["custom_fields"] = $this->custom_field->get_field_html("conveyancing_instrument",0);//($this->conveyancing_instrument->get("modelName"), 0);
            $response["result"]= true;
            $response["html"] = $this->load->view("conveyancing/add", $data, true);
        }//loading the form
        else { //process form

            $post_data = $this->input->post(NULL);
            $this->conveyancing_instrument->set_fields($post_data);
            $description = $this->input->post("description", true, "<h1><h2><h3><h4><h5><h6><strong><em><u><a><p><img>");
            $_POST["description"] = format_comment_patterns($this->regenerate_note($description));
            $is_clone = $this->input->post("clone");

            $this->conveyancing_instrument->set_field("initiated_by", $post_data['initiated_by_id']);
            $this->conveyancing_instrument->set_field("createdBy", $this->session->userdata('CP_user_id'));
            $this->conveyancing_instrument->set_field("createdOn", date('Y-m-d H:i'));
            $this->conveyancing_instrument->set_field("status", "pending");
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

                        $this->load->library("dmsnew");
                        $failed_uploads_count = 0;
                        $failed_uploads_messages = [];

                        if (isset($_FILES['documents']) && is_array($_FILES['documents']['name'])) {
                            $total_files = count($_FILES['documents']['name']);

                            for ($i = 0; $i < $total_files; $i++) {
                                // Only process if an actual file was uploaded (UPLOAD_ERR_OK is 0)
                                if ($_FILES['documents']['error'][$i] === UPLOAD_ERR_OK) {
                                    // Prepare the single file data in a format your dmsnew library might expect
                                    // You may need to adjust this array structure based on how dmsnew->upload_file() is designed
                                    $single_file_data = [
                                        'name'     => $_FILES['documents']['name'][$i],
                                        'type'     => $_FILES['documents']['type'][$i],
                                        'tmp_name' => $_FILES['documents']['tmp_name'][$i],
                                        'error'    => $_FILES['documents']['error'][$i],
                                        'size'     => $_FILES['documents']['size'][$i],
                                    ];

                                    $upload_response = $this->dmsnew->upload_file([
                                        "module" => "conveyancing",
                                        "module_record_id" => $conveyancing_instrument_id,
                                        "lineage" => 0,
                                        // Pass the actual temporary file path for dmsnew to process
                                        "file_path" => $single_file_data['tmp_name'],
                                        "file_name" => $single_file_data['name'],
                                        // 'upload_key' here might refer to the original form field name or a specific identifier
                                        "upload_key" => 'documents_' . $i // Using an index for uniqueness if needed
                                        // Add any other parameters your dmsnew->upload_file expects (e.g., file_type, file_size)
                                    ]);

                                    if (!$upload_response["status"]) {
                                        $failed_uploads_count++;
                                        $failed_uploads_messages[] = $single_file_data['name'] . ": " . ($upload_response['message'] ?? 'Unknown error during DMS processing.');
                                    }
                                }
                                // Files with UPLOAD_ERR_NO_FILE (4) are automatically skipped here.
                            }
                        }

                        if (0 < $failed_uploads_count) {
                            // Concatenate all specific error messages
                            $response["validationErrors"]["files"] = implode("<br>", $failed_uploads_messages);
                            if(empty($failed_uploads_messages)){
                                // Fallback generic message if no specific messages were collected
                                $response["validationErrors"]["files"] = sprintf($this->lang->line("files_were_not_uploaded"), $failed_uploads_count);
                            }
                        }

                    }
                }
                $clone_result = true;
                if (isset($is_clone) && !strcmp($is_clone, "yes")) {
                    $conveyancing_instrument_id = $this->conveyancing_instrument->get_field("id");
                    // $this->load->model("conveyancing_instrument_status", "conveyancing_instrument_statusfactory");
                    // $this->conveyancing_instrument_status = $this->conveyancing_instrument_statusfactory->get_instance();
                    // $this->load->model("conveyancing_instrument_type", "conveyancing_instrument_typefactory");
                    $this->conveyancing_instrument_type = $this->conveyancing_instrument_typefactory->get_instance();
                    $data = [];
                    $data["conveyancingData"] = $this->conveyancing_instrument->load_conyencing_instrument($conveyancing_instrument_id);
                    if ($data["conveyancingData"]) {
                        $data["conveyancingData"]["createdBy"] = "";
                        $data["conveyancingData"]["id"] = "";
                        $this->user_profile->fetch(["user_id" => $data["conveyancingData"]["assigned_to"]]);
                        $data["type"] = $this->conveyancing_instrument_type->load_list_per_language();
                        $data["status"] ="";// $this->conveyancing_instrument_status->load_list();
                        $data["toMeId"] = $this->is_auth->get_user_id();
                        $data["toMeFullName"] = $this->is_auth->get_fullname();
                        $data["conveyancingModelCode"] = $this->conveyancing_instrument->get("modelCode");
                        //  $data["opinionUsers"] = $this->opinion->load_conveyancing_instrument_users($conveyancing_instrument_id);
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
       // $refNumber = $this->conveyancing_instrument->generate_reference_number();
        // Get user ID from session
        $userId = $this->ci->session->userdata("CP_user_id");
;
        $postData=$this->input->post(null);
        $this->conveyancing_instrument->set_fields($postData);

        $this->conveyancing_instrument->set_field("initiated_by", $postData['initiated_by_id']);
        $this->conveyancing_instrument->set_field("createdBy", $userId);
        $this->conveyancing_instrument->set_field("status", "pending");
if ($this->conveyancing_instrument->validate()) {
    if ($this->conveyancing_instrument->insert()) {
        $instrumentId = $this->conveyancing_instrument->get_insert_id();

        // Add initial activity
        $this->conveyancing_activity->insert([
            "conveyancing_id" => $instrumentId,
            "activity_type" => "Creation",
            "activity_details" => "Conveyancing instrument created",
            "performed_by" => $userId,
            "activity_date" => date("Y-m-d H:i:s")
        ]);

        return redirect()->to("/conveyancing/view/$instrumentId")
            ->with('message', $this->lang->line("conveyancing_instrument_created"));
    } else {
        return redirect()->back()
            ->withInput()
            ->with('error', $this->lang->line("failed_to_create_conveyancing_instrument"));
    }
}else{
    $response["result"] = false;
    $response["validationErrors"] = $this->contract->get_validation_errors();
    $this->output->set_content_type("application/json")->set_output(json_encode($response));
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
    {
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
                $response = $this->dmsnew->upload_file(["module" => $this->input->post("module"), "module_record_id" => $this->input->post("module_record_id"), "lineage" => $this->input->post("lineage"), "upload_key" => "uploadDoc", "document_type_id" => $this->input->post("document_type_id"), "document_status_id" => $this->input->post("document_status_id"),"visible_in_cp" => 1, "comment" => $this->input->post("comment")]);
                $this->load->model("document_management_system", "document_management_systemfactory");
             
                $this->document_management_system = $this->document_management_systemfactory->get_instance();
               // $response["related_documents_count"] = $this->document_management_system->count_contract_related_documents($this->input->post("module_record_id"));
                $response["module_record_id"] = $this->input->post("module_record_id");
            }
        }
        $this->output->set_content_type("application/json")->set_output(json_encode($response));
    }
    public function load_documents()
    {
        $data = $this->dmsnew->load_documents(["module" => $this->input->post("module"), "module_record_id" => $this->input->post("module_record_id"), "lineage" => $this->input->post("lineage"), "visible_in_cp" => 1,"term" => $this->input->post("term")]);

        $response["commentHtml"] = $this->load->view("conveyancing/document_item",  $data
            , true);
        if ($this->input->is_ajax_request()) {
            $this->output->set_content_type("application/json");
            $this->output->set_output(json_encode($response));
        } else {
            return $response;
        }
    }
    private function load_documents_form_data($conveyancing_id, $lineage)
    {
        $this->load->model("conveyancing_document_status", "conveyancing_document_statusfactory");
        $this->conveyancing_document_status = $this->conveyancing_document_statusfactory->get_instance();
        $this->load->model("conveyancing_document_type", "conveyancing_document_typefactory");
        $this->conveyancing_document_type = $this->conveyancing_document_typefactory->get_instance();
        $data["document_statuses"] = $this->conveyancing_document_status->load_list_statuses();
        $data["document_types"] = $this->conveyancing_document_type->load_list_types();
        $data["module_record"] = "conveyancing";
        $data["module_record_id"] = $conveyancing_id;
        return $data;
    }
    public function download_file($file_id, $newest_version = false)
    {
        $this->load->library("dmsnew");
        $this->dmsnew->download_file("conveyancing", $file_id, false);
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
    public function add_activity($instrument_id)
    {
        $rules = [
            'activity_type' => 'required',
            'activity_details' => 'required'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $userId = $this->ci->session->userdata("CP_user_id");

        $data = [
            "conveyancing_id" => $instrument_id,
            "activity_type" => $this->request->getPost("activity_type"),
            "activity_details" => $this->request->getPost("activity_details"),
            "performed_by" => $userId,
            "activity_date" => date("Y-m-d H:i:s")
        ];

        // If this is a status change, update the instrument status
        if ($this->request->getPost("activity_type") === "Status Change") {
            $this->conveyancing_instrument->update($instrument_id, [
                "status" => $this->request->getPost("status"),
                "last_updated" => date("Y-m-d H:i:s")
            ]);
        }

        if ($this->conveyancing_activity->insert($data)) {
            return redirect()->back()->with('message', $this->lang->line("activity_logged_successfully"));
        } else {
            return redirect()->back()->with('error', $this->lang->line("failed_to_log_activity"));
        }
    }

    public function pending_instruments($filter = "all")
    {
        $data = [];
        $logged_in_user = $this->ci->session->userdata("CP_user_id");

        $data["active_quick_filter"] = $filter;
        $data["instruments"] = $this->conveyancing_instrument->get_pending_instruments($logged_in_user, $filter);
        $data["model_code"] = $this->conveyancing_instrument->get("modelCode");

        $this->includes("customerPortal/clientPortal/js/conveyancing", "js");
        $this->includes("customerPortal/clientPortal/js/pending_instruments", "js");

        $this->load->view("partial/header");
        $this->load->view("conveyancing/pending_instruments", $data);
        $this->load->view("partial/footer");
    }

    public function in_progress_instruments($filter = "all")
    {
        $data = [];
        $logged_in_user = $this->ci->session->userdata("CP_user_id");

        $data["active_quick_filter"] = $filter;
        $data["instruments"] = $this->conveyancing_instrument->get_in_progress_instruments($logged_in_user, $filter);
        $data["model_code"] = $this->conveyancing_instrument->get("modelCode");

        $this->includes("customerPortal/clientPortal/js/conveyancing", "js");
        $this->includes("customerPortal/clientPortal/js/in_progress_instruments", "js");

        $this->load->view("partial/header");
        $this->load->view("conveyancing/in_progress_instruments", $data);
        $this->load->view("partial/footer");
    }

    // AJAX methods
    public function get_external_counsels()
    {
        $term = $this->request->getGet("term");
        $results = $this->external_counsel->search($term);

        return $this->response->setJSON($results);
    }

    public function update_status($instrument_id)
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setStatusCode(403);
        }

        $status = $this->request->getPost("status");
        $response = ["success" => false];

        if ($this->conveyancing_instrument->update($instrument_id, ["status" => $status])) {
            // Log activity
            $this->conveyancing_activity->insert([
                "conveyancing_id" => $instrument_id,
                "activity_type" => "Status Change",
                "activity_details" => "Status changed to " . $status,
                "performed_by" => $this->ci->session->userdata("CP_user_id"),
                "activity_date" => date("Y-m-d H:i:s")
            ]);

            $response["success"] = true;
            $response["message"] = $this->lang->line("status_updated_successfully");
        } else {
            $response["message"] = $this->lang->line("failed_to_update_status");
        }

        return $this->response->setJSON($response);
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
                "activity_type" => "Counsel Assignment",
                "activity_details" => "Assigned to external counsel: " . $counsel['firm_name'],
                "performed_by" => $this->ci->session->userdata("CP_user_id"),
                "activity_date" => date("Y-m-d H:i:s")
            ]);

            $response["success"] = true;
            $response["message"] = $this->lang->line("counsel_assigned_successfully");
        } else {
            $response["message"] = $this->lang->line("failed_to_assign_counsel");
        }

        return $this->response->setJSON($response);
    }
    public function edit($id)
    {
        $response["result"] = false;
        if ($this->input->post(null)){
            $this->conveyancing_instrument->reset_fields();
            $postData=$this->input->post(null);
           if($this->conveyancing_instrument->fetch($postData["id"])) {

               $this->conveyancing_instrument->set_fields($postData, true);
               $this->conveyancing_instrument->set_field("initiated_by",$postData["initiated_by_id"]);
              // $this->conveyancing_instrument->set_field("modifiedBy",$this->ci->session->userdata("CP_user_id"));
               if ($this->conveyancing_instrument->update($postData, ["id" => $id])) {
                   $response["result"] = true;
                   $response["id"] = $id;
                   $this->conveyancing_instrument->reset_fields();
               } else {
                   $response["result"] = false;
                   $response["message"]="unable to save changes";
               }
           }else{
               $response["result"] = false;
               $response["message"]="Invalid ID";
           }

        }else {
            $data["types"] = $this->conveyancing_instrument_types->load_list();
            $data["transaction_types"] = $this->conveyancing_transaction_types->load_list();
            $data["conveyancingData"] = $this->conveyancing_instrument->cp_load_conveyancing_instrument_by_id($id);
            $data["title"] = $this->lang->line("update");
            $data["system_preferences"]["conveyancingInstrumentTypeId"]=1;

            $data["type"]=$data["conveyancingData"]["instrument_type_id"];
            $response["result"] = true;
            $response["html"] = $this->load->view("conveyancing/add", $data, true);
        }
        $this->output->set_content_type("application/json");
        $this->output->set_output(json_encode($response));
    }
    public  function  delete_document()
    {
        $instrument_id=$this->input->post("module_record_id");
        $module=$this->input->post("module");
        $document_id=$this->input->post("document_id");
         $logged_in_user = $this->ci->session->userdata("CP_user_id");
        $response['success']=false;
        $response['message']="";
         if (!$logged_in_user) {
             $response['success']=false;
             $response['message']="Login first";
         }else{
        if (isset($instrument_id) && 0<$instrument_id) {

        $this->load->library('dmsnew');
        $this->dmsnew->delete_document("conveyancing", $document_id, $newest_version = false);
            $response['success']=true;
            $response['message']="Successfully deleted instrument document";
         }
        $this->output->set_content_type("application/json")->set_output(json_encode($response));
    }
}
}