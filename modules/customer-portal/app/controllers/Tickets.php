<?php

require "Auth_controller.php";
class Tickets extends Auth_Controller
{
    public $Customer_Portal_Screen;
    public $Legal_Case;
    public function __construct()
    {
        parent::__construct();
        $this->load->model("workflow_status", "workflow_statusfactory");
        $this->workflow_status = $this->workflow_statusfactory->get_instance();
        $this->pageTitle = sprintf($this->lang->line("app4legal_browser_title"), $this->lang->line("customer_portal"));
        $this->load->model("customer_portal_users", "customer_portal_usersfactory");
        $this->customer_portal_users = $this->customer_portal_usersfactory->get_instance();
        $this->load->model("legal_case", "legal_casefactory");
        $this->legal_case = $this->legal_casefactory->get_instance();
        $this->load->model("customer_portal_screen", "customer_portal_screenfactory");
        $this->customer_portal_screen = $this->customer_portal_screenfactory->get_instance();
        $this->load->library("dms", ["channel" => $this->legal_case->get("portalChannel"), "user_id" => $this->ci->session->userdata("CP_user_id")]);
        $licenses_validity = $this->session->userdata("licenses_validity");
        if (!$licenses_validity["client"]) {
            $this->setPinesMessage("error", $this->lang->line("invalid_license"));
            redirect("contracts");
        }
    }
    public function index($filter = "all")
    {
        $data = [];
        $user_logged_in = $this->ci->session->userdata("CP_user_id");
        $data["active_quick_filter"] = $filter;
        $data["openedTickets"] = $this->customer_portal_screen->loadOpenedTickets($user_logged_in, $data["active_quick_filter"]);
        $this->includes("customerPortal/clientPortal/js/openList", "js");
        $this->load->view("partial/header");
        $this->load->view("tickets/openList", $data);
        $this->load->view("partial/footer");
    }
    public function add($screenId)
    {
        if (is_numeric($screenId) && $this->customer_portal_screen->fetch($screenId)) {
            $data = [];
            $data["lang"] = "english";
            $data["screenFields"] = $this->customer_portal_screen->loadScreenFields($this->customer_portal_screen->get_field("id"));
            $data["predefinedFields"] = $this->customer_portal_screen->get("screenFields");
            $validation_errors = [];
            $is_priority_visible = true;
            $priority_default = NULL;
            if ($this->input->post(NULL)) {
                $result = false;
                $post_data = strip_all_tags($this->input->post(NULL));
                foreach ($data["screenFields"] as $screenField) {
                    if ($screenField["relatedCaseField"] == "priority" && $screenField["isRequired"] == 1 && $screenField["visible"] == 0) {
                        $is_priority_visible = false;
                        $priority_default = $screenField["requiredDefaultValue"];
                    }
                    if ($screenField["isRequired"] == 1 && $screenField["visible"] == 1) {
                        if ($screenField["relatedCaseField"] != "attachment") {
                            if (!isset($post_data[$screenField["relatedCaseField"]]) || !$post_data[$screenField["relatedCaseField"]] && $post_data[$screenField["relatedCaseField"]] != "0") {
                                $validation_errors[$screenField["relatedCaseField"]] = $this->lang->line("cannot_be_blank_rule");
                            }
                        } else {
                            foreach ($_FILES as $key => $file) {
                                if (in_array($key, $post_data["requiredAttachments"]) && (!isset($file) || empty($file["name"]))) {
                                    $validation_errors[$screenField["relatedCaseField"]] = $this->lang->line("cannot_be_blank_rule");
                                }
                            }
                        }
                    }
                }
                if (isset($post_data["caseValue"]) && intval($post_data["caseValue"]) < 0) {
                    array_push($validation_errors, $this->lang->line("positive_case_value"));
                }
                if (empty($validation_errors)) {
                    $userLoggedIn = $this->ci->session->userdata("CP_user_id");
                    $screen_matter_category = strtolower($this->customer_portal_screen->get_field("applicable_on"));
                    $workflow_applicable = $this->workflow_status->getWorkflowOfAreaPractice($this->customer_portal_screen->get_field("case_type_id"), $screen_matter_category);
                    if (!empty($workflow_applicable)) {
                        $case_status_id = $this->workflow_status->get_workflow_start_point($workflow_applicable["workflow"]);
                        $workflow = $workflow_applicable["workflow"];
                    } else {
                        $workflow_applicable = $this->workflow_status->getDefaultSystemWorkflow($screen_matter_category);
                        $workflow = isset($workflow_applicable["workflow"]) ? $workflow_applicable["workflow"] : "1";
                        $case_status_id = ($status_data = $this->workflow_status->get_workflow_start_point($workflow)) ? $status_data : "1";
                    }
                    $this->customer_portal_users->fetch($post_data["requestedBy"] ?? $this->ci->session->userdata("CP_user_id"));
                    $contact_id = $this->customer_portal_users->add_cp_user_as_contact(true, false, $this->customer_portal_users->get_field("email"));
                    if (!isset($post_data["dueDate"])) {
                        $this->load->model("case_type");
                        $logged_in_user = $this->ci->session->userdata("CP_user_id");
                        $this->ci->load->model("customer_portal_users", "customer_portal_usersfactory");
                        $this->ci->customer_portal_users = $this->ci->customer_portal_usersfactory->get_instance();
                        $this->ci->customer_portal_users->fetch($logged_in_user);
                        $requested_contact = $this->ci->customer_portal_users->get_field("contact_id");
                        $this->load->model(["language", "company_customer_portal_user"]);
                        $user_companies = $this->company_customer_portal_user->get_customer_portal_user_companies($logged_in_user);
                        if ($this->input->post("companies") != NULL) {
                            $client_id_for_due = $this->input->post("companies");
                        } else {
                            $client_id_for_due = $logged_in_user;
                        }
                        $priority_for_due_date = $priority_default;
                        if ($is_priority_visible) {
                            $priority_for_due_date = $post_data["priority"];
                        }
                        list($ticket_sla, $client_id_chosen) = $this->case_type->get_due_value($this->customer_portal_screen->get_field("case_type_id"), $priority_for_due_date, $client_id_for_due, $user_companies, $contact_id);
                        if ($client_id_chosen != NULL) {
                            $this->legal_case->set_field("client_id", $client_id_chosen);
                        }
                        if (0 < $ticket_sla) {
                            $post_data["dueDate"] = date("Y-m-d", strtotime(" + " . $ticket_sla . " days"));
                        }
                    }
                    $this->legal_case->set_fields($post_data);
                    $this->legal_case->set_field("description", isset($post_data["description"]) ? $post_data["description"] : "");
                    $this->legal_case->set_field("channel", $this->legal_case->get("portalChannel"));
                    $this->legal_case->set_field("visibleToCP", 1);
                    $this->legal_case->set_field("case_status_id", $case_status_id);
                    $this->legal_case->set_field("workflow", $workflow);
                    $this->legal_case->set_field("case_type_id", $this->customer_portal_screen->get_field("case_type_id"));
                    $this->legal_case->set_field("category", $this->input->post("Legal_case[category]") ? $this->input->post("Legal_case[category]") : "Matter");
                    $this->legal_case->set_field("externalizeLawyers", "no");
                    $this->legal_case->set_field("archived", "no");
                    $this->legal_case->set_field("createdOn", date("Y-m-d H:i:s", time()));
                    $this->legal_case->set_field("modifiedOn", date("Y-m-d H:i:s", time()));
                    $this->legal_case->set_field("createdBy", $userLoggedIn);
                    $this->legal_case->set_field("modifiedBy", $userLoggedIn);
                    $this->legal_case->set_field("modifiedByChannel", $this->legal_case->get("portalChannel"));
                    $this->legal_case->set_field("isDeleted", 0);
                    $this->legal_case->set_field("user_id", isset($post_data["assignee"]) ? $post_data["assignee"] : "");
                    $this->legal_case->set_field("private", isset($post_data["shared_with"]) ? "yes" : "no");
                    $caseValue = $this->input->post("caseValue");
                    $this->legal_case->set_field("caseValue", isset($caseValue) && $caseValue ? $caseValue : "0.00");
                    $this->legal_case->set_field("requestedBy", $contact_id);
                    $this->legal_case->set_field("caseArrivalDate", date("Y-m-d", time()));
                    $external_tables = [];
                    foreach ($data["screenFields"] as $scField) {
                        if ($scField["isRequired"] == 1 && $scField["visible"] == 0) {
                            $scFieldName = $scField["relatedCaseField"];
                            $predefinedFieldData = $data["predefinedFields"][$scFieldName];
                            if (!$predefinedFieldData["customField"]) {
                                switch ($scFieldName) {
                                    case "assignee":
                                        if ($scField["requiredDefaultValue"] === "rr_algorithm") {
                                            $this->load->model("customer_portal_users_assignment", "customer_portal_users_assignmentfactory");
                                            $this->customer_portal_users_assignment = $this->customer_portal_users_assignmentfactory->get_instance();
                                            $assignment = $this->customer_portal_users_assignment->load_next_assignee($screenId);
                                            $this->legal_case->set_field("user_id", $assignment["user_id"]);
                                        } else {
                                            $this->legal_case->set_field("user_id", $scField["requiredDefaultValue"]);
                                        }
                                        break;
                                    case "shared_with":
                                        $this->legal_case->set_field("private", "yes");
                                        if ($scField["requiredDefaultValue"]) {
                                            $external_tables[$scFieldName] = explode(",", $scField["requiredDefaultValue"]);
                                        }
                                        break;
                                    default:
                                        $this->legal_case->set_field($scFieldName, $scField["requiredDefaultValue"]);
                                }
                            } else {
                                if ($scField["requiredDefaultValue"] != "") {
                                    if ($predefinedFieldData["formType"] == "lookup_multiselect") {
                                        $post_data[$scFieldName][] = $scField["requiredDefaultValue"];
                                    } else {
                                        $post_data[$scFieldName] = $predefinedFieldData["formType"] == "list" && is_array(explode(",", $scField["requiredDefaultValue"])) ? explode(",", $scField["requiredDefaultValue"]) : $scField["requiredDefaultValue"];
                                    }
                                }
                            }
                        }
                    }
                    $this->legal_case->disable_builtin_logs();
                    if ($this->legal_case->insert()) {
                        $legal_case_data = $this->legal_case->get_fields();
                        if (isset($assignment) && $assignment) {
                            $this->customer_portal_users_assignment->set_field("screen", $screenId);
                            $this->customer_portal_users_assignment->set_field("user_relation", $assignment["relation_id"]);
                            $this->customer_portal_users_assignment->insert();
                        }
                        $this->legal_case->inject_folder_templates($legal_case_data["id"], $legal_case_data["category"], $legal_case_data["case_type_id"]);
                        $watcher = $post_data["shared_with"] ?? $external_tables["shared_with"] ?? "";
                        if ($legal_case_data["private"] == "yes" && !empty($watcher)) {
                            $case_watchers["users"] = ["legal_case_id" => $legal_case_data["id"], "users" => $watcher];
                            $this->legal_case->insert_watchers_users($case_watchers);
                        }
                        $this->load->library("email_notifications");
                        $this->load->model(["system_preference", "email_notification_scheme"]);
                        $this->load->model("customer_portal_ticket_watcher", "customer_portal_ticket_watcherfactory");
                        $this->customer_portal_ticket_watcher = $this->customer_portal_ticket_watcherfactory->get_instance();
                        if (isset($post_data["watchedBy"]) && !empty($post_data["watchedBy"])) {
                            $this->customer_portal_ticket_watcher->add_watchers_to_ticket($post_data["watchedBy"], $legal_case_data["id"]);
                        }
                        $system_preferences = $this->system_preference->get_key_groups();
                        $cp_prefix = $system_preferences["CustomerPortalConfig"]["cpAppTitle"];
                        $result = true;
                        $case_id = $legal_case_data["id"];
                        $object = "cp_add_ticket";
                        $model = $this->legal_case->get("_table");
                        $model_data["id"] = $case_id;
                        $notifications_emails = $this->email_notification_scheme->get_emails($object, $model, $model_data);
                        extract($notifications_emails);
                        $to_emails = array_filter($to_emails);
                        $this->load->model("client");
                        $client_info = $this->client->fetch_client($legal_case_data["client_id"]);
                        $assignee_name = "";
                        if (!empty($legal_case_data["user_id"])) {
                            $assignee_name = $this->email_notification_scheme->get_user_full_name($legal_case_data["user_id"]);
                        }
                        if (!empty($to_emails)) {
                            $notificationsData = ["to" => $to_emails, "object" => $object, "object_id" => $case_id, "objectModelCode" => $this->legal_case->get("modelCode"), "cc" => $cc_emails, "caseSubject" => $legal_case_data["subject"], "CpProfileName" => $this->session->userdata("CP_profileName"), "created_on" => $legal_case_data["createdOn"], "subject" => $legal_case_data["subject"], "priority" => $legal_case_data["priority"], "description" => $legal_case_data["description"], "assignee" => $assignee_name, "file_reference" => $legal_case_data["internalReference"], "objectName" => strtolower($legal_case_data["category"]), "client_name" => $client_info["name"], "fromLoggedUser" => $this->ci->session->userdata("CP_profileName") . "-" . $cp_prefix];
                            $this->email_notifications->notify($notificationsData);
                        }
                        $object = "request_type_" . $screenId;
                        $notifications_emails = $this->email_notification_scheme->get_emails($object, $model, $model_data);
                        extract($notifications_emails);
                        $to_emails = array_filter($to_emails);
                        if (!empty($to_emails)) {
                            $notificationsData = ["to" => $to_emails, "object" => "request_type_notification_tab", "object_id" => $case_id, "objectModelCode" => $this->legal_case->get("modelCode"), "cc" => $cc_emails, "caseSubject" => $legal_case_data["subject"], "CpProfileName" => $this->session->userdata("CP_profileName"), "created_on" => $legal_case_data["createdOn"], "subject" => $legal_case_data["subject"], "priority" => $legal_case_data["priority"], "description" => $legal_case_data["description"], "objectName" => strtolower($legal_case_data["category"]), "assignee" => $assignee_name, "file_reference" => $legal_case_data["internalReference"], "client_name" => $client_info["name"], "fromLoggedUser" => $this->ci->session->userdata("CP_profileName") . "-" . $cp_prefix];
                            $this->email_notifications->notify($notificationsData);
                        }
                        if ($assignee = $legal_case_data["user_id"]) {
                            $object = "cp_add_ticket_inform_assignee";
                            $this->load->model("user", "userfactory");
                            $this->user = $this->userfactory->get_instance();
                            $this->user->fetch($assignee);
                            $toEmail = $this->user->get_field("email");
                            $assigne_profile_name = $this->email_notification_scheme->get_user_full_name($assignee);
                            if ($toEmail) {
                                $notificationsData = ["to" => $toEmail, "object" => $object, "object_id" => $case_id, "objectModelCode" => $this->legal_case->get("modelCode"), "cc" => "", "caseSubject" => $legal_case_data["subject"], "caseAssignee" => $assigne_profile_name, "CpProfileName" => $this->session->userdata("CP_profileName"), "created_on" => $legal_case_data["createdOn"], "priority" => $legal_case_data["priority"], "assignee" => $assignee_name, "file_reference" => $legal_case_data["internalReference"], "client_name" => $client_info["name"], "subject" => $legal_case_data["subject"], "objectName" => strtolower($legal_case_data["category"]), "description" => $legal_case_data["description"], "fromLoggedUser" => $this->ci->session->userdata("CP_profileName") . "-" . $cp_prefix];
                                $this->email_notifications->notify($notificationsData);
                            }
                        }
                        $this->load->model("sla_management_mod", "sla_management_modfactory");
                        $this->sla_management_mod = $this->sla_management_modfactory->get_instance();
                        $this->workflow_status->logTransitionHistory($case_id, NULL, $legal_case_data["case_status_id"], $userLoggedIn, $this->legal_case->get("portalChannel"));
                        $this->sla_management_mod->log_case($case_id, $legal_case_data["case_status_id"], $userLoggedIn, $this->legal_case->get("portalChannel"));
                        $tmpCustomFields = [];
                        foreach ($post_data as $pkey => $pData) {
                            if ($pData && isset($data["predefinedFields"][$pkey])) {
                                $predefinedFieldData = $data["predefinedFields"][$pkey];
                                if ($predefinedFieldData["customField"]) {
                                    $customFieldId = substr($pkey, strlen("customField_"));
                                    $tmpCustomFields[$customFieldId] = $pData;
                                }
                            }
                        }
                        $this->load->model("custom_field", "custom_fieldfactory");
                        $this->custom_field = $this->custom_fieldfactory->get_instance();
                        $customFields = $this->custom_field->load_custom_fields($case_id, $this->legal_case->modelName, "en");
                        $finalCustomFields = [];
                        foreach ($customFields as $customField) {
                            $cFieldId = $customField["id"];
                            $custom_field_value = isset($tmpCustomFields[$cFieldId]) ? $tmpCustomFields[$cFieldId] : "";
                            $custom_field_data = ["custom_field_id" => $cFieldId, "recordId" => $case_id];
                            switch ($customField["type"]) {
                                case "date":
                                    $custom_field_data["date_value"] = $custom_field_value;
                                    break;
                                case "date_time":
                                    if (!is_array($custom_field_value)) {
                                        $cf_value = explode(" ", $custom_field_value);
                                        $custom_field_data["date_value"] = isset($cf_value[0]) ? $cf_value[0] : "";
                                        $custom_field_data["time_value"] = isset($cf_value[1]) ? $cf_value[1] : "";
                                    } else {
                                        $custom_field_data["date_value"] = $custom_field_value["date_value"];
                                        $custom_field_data["time_value"] = $custom_field_value["time_value"];
                                    }
                                    break;
                                default:
                                    $custom_field_data["text_value"] = $custom_field_value;
                                    $finalCustomFields[$cFieldId] = $custom_field_data;
                            }
                        }
                        if (is_array($finalCustomFields) && count($finalCustomFields)) {
                            $this->custom_field->update_custom_fields($finalCustomFields);
                        }
                        if ($system_preferences["webhooks"]["webhooks_enabled"] == 1) {
                            $webhook_data = $this->legal_case->load_case_details($case_id);
                            $this->legal_case->trigger_web_hook($webhook_data["category"] == "Matter" ? "matter_updated" : "litigation_updated", $webhook_data);
                        }
                        $attachments_ids = $upload_errors = $upload_failed_files = [];
                        foreach ($_FILES as $file_key => $file) {
                            if (!empty($file["name"])) {
                                $validate_extension = $this->validate_attachment_extension($file);
                                if ($validate_extension["result"] === true) {
                                    $upload_response = $this->dms->upload_file(["module" => "case", "module_record_id" => $case_id, "container_name" => "Matter Notes Attachments", "upload_key" => $file_key, "visible_in_cp" => 1]);
                                    if ($upload_response["status"]) {
                                        $attachments_ids[] = $upload_response["file"]["id"];
                                    } else {
                                        $upload_failed_files[] = $file["name"];
                                    }
                                } else {
                                    $upload_errors[] = $validate_extension["error_msg"];
                                }
                            }
                        }
                        if (!empty($attachments_ids)) {
                            $current_time = date("Y-m-d H:i:s", time());
                            $comment = 1 < count($attachments_ids) ? $this->lang->line("multiple_attachment_added") : $this->lang->line("attachment_added");
                            $this->load->model("case_comment", "case_commentfactory");
                            $this->case_comment = $this->case_commentfactory->get_instance();
                            $this->case_comment->set_field("case_id", $case_id);
                            $this->case_comment->set_field("comment", nl2br($comment));
                            $this->case_comment->set_field("user_id", $userLoggedIn);
                            $this->case_comment->set_field("createdOn", $current_time);
                            $this->case_comment->set_field("modifiedBy", $userLoggedIn);
                            $this->case_comment->set_field("createdByChannel", $this->legal_case->get("portalChannel"));
                            $this->case_comment->set_field("modifiedByChannel", $this->legal_case->get("portalChannel"));
                            if (!$this->case_comment->insert()) {
                                $result = false;
                                $validation_errors = $this->case_comment->get("validationErrors");
                            }
                        }
                        if (!empty($upload_failed_files)) {
                            $upload_faild_error_message = sprintf($this->lang->line("prb_in_uploading_files"), implode(", ", $upload_failed_files));
                        }
                        if (!empty($upload_errors)) {
                            $message = sprintf($this->lang->line("file_type_not_allowed"), implode(",<br /> ", $upload_errors));
                            if (isset($upload_faild_error_message)) {
                                $message = $message . "<br>" . $upload_faild_error_message;
                            }
                            $this->setPinesMessage("warning", $message);
                            redirect("tickets/view/" . $case_id);
                        }
                        if (isset($upload_faild_error_message)) {
                            $this->setPinesMessage("warning", $upload_faild_error_message);
                            redirect("tickets/view/" . $case_id);
                        }
                    } else {
                        $validation_errors = $this->legal_case->get("validationErrors");
                    }
                    if ($result) {
                        $this->setPinesMessage("success", $this->lang->line("save_record_successfully"));
                        redirect("tickets/view/" . $case_id);
                    }
                }
            } else {
                if (isset($_SERVER["CONTENT_LENGTH"]) && $this->config->item("allowed_post_max_size_bite") < $_SERVER["CONTENT_LENGTH"]) {
                    $validation_errors = $this->lang->line("post_data_exceeds_form_limit");
                }
            }
            $data["validationErrors"] = $validation_errors;
            $data["formHtml"] = $this->customer_portal_screen->loadFieldsHtml($data["screenFields"], $data["predefinedFields"]);
            $this->ci->load->model("customer_portal_users", "customer_portal_usersfactory");
            $this->ci->customer_portal_users = $this->ci->customer_portal_usersfactory->get_instance();
            $requested_contact = $this->ci->customer_portal_users->get_field("contact_id");
            $this->load->model(["language", "company_customer_portal_user"]);
            $user_companies = $this->company_customer_portal_user->get_customer_portal_user_companies($this->ci->session->userdata("CP_user_id"));
            $this->includes("customerPortal/clientPortal/js/add", "js");
            $this->includes("jquery/css/chosen", "css");
            $this->includes("jquery/chosen.min", "js");
            $this->load->view("partial/header");
            $this->load->view("screens/add", $data);
            $this->load->view("partial/footer");
        } else {
            $this->setPinesMessage("error", $this->lang->line("invalid_error"));
            redirect("home");
        }
    }
    private function get_drop_down_array($input_data, $key, $value, $first_line = NULL)
    {
        $data = [];
        if ($first_line != NULL) {
            $data[0] = $this->lang->line("choose_all");
        }
        foreach ($input_data as $row) {
            $data[$row[$key]] = $row[$value];
        }
        return $data;
    }
    private function get_web_dav_user_info()
    {
        $this->load->model("user", "userfactory");
        $this->user = $this->userfactory->get_instance();
        $this->user->fetch(["username" => $this->user->get("isAdminUser")]);
        return ["username" => $this->user->get_field("username"), "user_id" => $this->user->get_field("id")];
    }
    private function validate_attachment_extension($file)
    {
        $response = ["result" => false, "error_msg" => ""];
        $this->config->load("allowed_file_uploads", true);
        $allowed_types = $this->config->item("case", "allowed_file_uploads");
        if (!empty($allowed_types) && $file["error"] != 4) {
            $allowed_file_uploads = explode("|", $allowed_types);
            $file_info = pathinfo($file["name"]);
            $file_ext = strtolower($file_info["extension"]);
            if (!in_array($file_ext, $allowed_file_uploads)) {
                $response["error_msg"] = $file["name"];
            } else {
                $response["result"] = true;
            }
        } else {
            $response["error_msg"] = $this->lang->line("attachment_not_allowed");
        }
        return $response;
    }
    private function check_attachments_extension()
    {
        $file = $_FILES;
        $this->config->load("allowed_file_uploads", true);
        $allowedTypes = $this->config->item("case", "allowed_file_uploads");
        if (!empty($allowedTypes)) {
            $un_allowed_files = [];
            $allowedTypesArr = explode("|", $allowedTypes);
            foreach ($file as $key => $uploadFile) {
                if ($uploadFile["error"] != 4) {
                    $fileInfo = pathinfo($uploadFile["name"]);
                    $fileExtension = strtolower($fileInfo["extension"]);
                    if (!in_array($fileExtension, $allowedTypesArr)) {
                        $un_allowed_files[] = $uploadFile["name"];
                    }
                }
            }
            if (!empty($un_allowed_files)) {
                return $un_allowed_files;
            }
            return "passed";
        } else {
            return $this->lang->line("attachment_not_allowed");
        }
    }
    public function view($ticketId)
    {
        if (!$ticketId) {
            redirect("home");
        } else {
            if (!ctype_digit($ticketId)) {
                $this->setPinesMessage("error", $this->lang->line("invalid_record"));
                redirect("home");
            }
        }
        $is_case_deleted = $this->legal_case->check_if_case_deleted($ticketId);
        if ($is_case_deleted) {
            $this->setPinesMessage("error", $this->lang->line("invalid_record"));
            redirect("tickets");
        }
        $data = [];
        $this->load->model("customer_portal_permission");
        $this->legal_case->fetch($ticketId);
        $case_status = $this->legal_case->get_field("case_status_id");
        $this->workflow_status->fetch($case_status);
        $data["systemPermissions"] = $this->customer_portal_permission->loadData($this->legal_case->get_field("workflow"));
        $data["ticketData"] = $this->customer_portal_screen->loadTicketData($ticketId);
        $user_ticket_permission = $this->customer_portal_users->get_user_ticket_permission($ticketId, $this->ci->session->userdata("CP_user_id"));
        switch ($user_ticket_permission) {
            case "none":
                $this->setPinesMessage("error", $this->lang->line("invalid_record"));
                redirect("tickets");
                break;
            case "read":
                $data["allowedToModifie"] = false;
                break;
            case "read_write":
                $allowedPermissions = [];
                foreach ($data["systemPermissions"] as $systemPermission) {
                    if ($data["ticketData"]["case_status_id"] == $systemPermission["fromStepId"]) {
                        $allowedPermissions[] = $systemPermission;
                    }
                }
                $data["allowedPermissions"] = $allowedPermissions;
                $data["allowedToModifie"] = true;
                break;
            default:}

                $systemPreferences = $this->system_preference->get_key_groups("DefaultValues");
                $data["systemPreferences"] = $systemPreferences["DefaultValues"];
                $data["userLogged"] = $this->ci->session->userdata("CP_profileName");
                $data["lang"] = "english";
                if (empty($data["ticketData"])) {
                    redirect("home");
                }
                $user_data = $this->get_web_dav_user_info();
                $user_data["cp_user_id"] = $this->ci->session->userdata("CP_user_id");
                $user_data["channel"] = $this->legal_case->get("portalChannel");
                $data["module_record_id"] = $ticketId;
                $hijri_feature = $systemPreferences["SystemValues"]["hijriCalendarFeature"] ?? false;
                $data["hijri_calendar_enabled"] = $hijri_feature ? true : false;
                $data["display_hearings_tab"] = $this->legal_case->get_field("category") == "Litigation" ? true : false;
                $this->includes("kendoui/js/kendo.web.min", "js");
                $this->includes("customerPortal/clientPortal/js/tickets", "js");
                $this->includes("customerPortal/clientPortal/js/documents", "js");
                $this->includes("customerPortal/clientPortal/js/view", "js");
                $this->includes("kendoui/styles/kendo.common-bootstrap.min", "css");
                $this->includes("kendoui/styles/kendo.bootstrap.min", "css");
                $this->load->view("partial/header");
                $this->load->view("tickets/view", $data);
                $this->load->view("partial/footer");

    }
    public function load_attachments()
    {
        $response = $this->dms->load_documents(["module" => $this->input->post("module"), "module_record_id" => $this->input->post("module_record_id"), "term" => $this->input->post("term"), "visible_in_cp" => 1, "lineage" => $this->input->post("lineage")]);
        if ($this->input->is_ajax_request()) {
            $this->output->set_content_type("application/json");
            $this->output->set_output(json_encode($response));
        } else {
            return $response;
        }
    }
    public function addComment($ticketId)
    {
        $response = ["type" => "success"];
        if (!$ticketId) {
            $response["error"] = $this->lang->line("ticket_id_not_defined");
        } else {
            if (!$this->input->post(NULL)) {
                if (isset($_SERVER["CONTENT_LENGTH"]) && $this->config->item("allowed_post_max_size_bite") < $_SERVER["CONTENT_LENGTH"]) {
                    $response["error"] = $this->lang->line("post_data_exceeds_form_limit");
                } else {
                    $response["html"] = $this->load->view("tickets/commentForm", [], true);
                }
            } else {
                if (!$this->input->post("comment") || $this->input->post("comment") == "") {
                    $response["error"] = $this->lang->line("comment_required");
                }
                $completeScript = true;
                if (!empty($_FILES)) {
                    $attachment_error = $this->check_attachments_extension();
                    if ($attachment_error !== "passed") {
                        $completeScript = false;
                        $response["error"] = sprintf($this->lang->line("file_type_not_allowed"), implode(",<br /> ", $attachment_error));
                    }
                }
                if ($completeScript && (!isset($response["error"]) || !$response["error"])) {
                    $current_time = date("Y-m-d H:i:s", time());
                    $userLoggedIn = $this->ci->session->userdata("CP_user_id");
                    $this->load->model("case_comment", "case_commentfactory");
                    $this->case_comment = $this->case_commentfactory->get_instance();
                    $this->case_comment->set_field("case_id", $ticketId);
                    $_POST["comment"] = strip_tags($this->input->post("comment"));
                    $this->case_comment->set_field("comment", nl2br($this->input->post("comment")));
                    $this->case_comment->set_field("user_id", $userLoggedIn);
                    $this->case_comment->set_field("createdOn", $current_time);
                    $this->case_comment->set_field("modifiedBy", $userLoggedIn);
                    $this->case_comment->set_field("createdByChannel", $this->legal_case->get("portalChannel"));
                    $this->case_comment->set_field("modifiedByChannel", $this->legal_case->get("portalChannel"));
                    $this->case_comment->set_field("isVisibleToCP", 1);
                    $this->case_comment->set_field("isVisibleToAP", "0");
                    if ($this->case_comment->insert()) {
                        $this->legal_case->set_field("id", $ticketId);
                        $this->legal_case->touch_logs("update", [], $userLoggedIn, $this->legal_case->get("portalChannel"));
                        $this->legal_case->fetch($ticketId);
                        $assignee = $this->legal_case->get_field("user_id");
                        $this->ci->load->model("system_preference");
                        $system_preferences = $this->ci->system_preference->get_key_groups();
                        $cp_prefix = $system_preferences["CustomerPortalConfig"]["cpAppTitle"];
                        $this->load->model("email_notification_scheme");
                        $this->load->model("user_profile");
                        $this->load->model("user", "userfactory");
                        $this->user = $this->userfactory->get_instance();
                        $this->user->fetch($assignee);
                        $toEmail = $this->user->get_field("email");
                        $assigne_profile_name = $this->email_notification_scheme->get_user_full_name($assignee);
                        $comment = nl2br($this->input->post("comment"));
                        $object = "cp_add_comment";
                        $model = $this->legal_case->get("_table");
                        $model_data["id"] = $ticketId;
                        $notifications_emails = $this->email_notification_scheme->get_emails($object, $model, $model_data);
                        extract($notifications_emails);
                        $this->load->model("client");
                        $client_info = $this->client->fetch_client($this->legal_case->get_field("client_id"));
                        $notificationsData = ["to" => $to_emails, "object" => $object, "object_id" => $ticketId, "cc" => $cc_emails, "objectName" => strtolower($this->legal_case->get_field("category")), "caseSubject" => $this->legal_case->get_field("subject"), "caseNote" => $comment, "created_by" => $this->ci->session->userdata("CP_profileName"), "assignee" => $assigne_profile_name, "file_reference" => $this->legal_case->get_field("internalReference"), "client_name" => $client_info["name"], "objectModelCode" => $this->legal_case->get("modelCode"), "fromLoggedUser" => $this->ci->session->userdata("CP_profileName") . "-" . $cp_prefix];
                        $systemNotification = isset($assignee) ? true : false;
                        $this->legal_case->notifyTicketUserByEmail($systemNotification, $this->legal_case->get_field("user_id"), $toEmail, (string) $this->ci->session->userdata("CP_profileName") . sprintf($this->lang->line("added_below_comment"), "<a href=\"" . substr(base_url(), 0, -1 * strlen(MODULE) - 9) . "cases/edit/" . $ticketId . "\">" . $notificationsData["objectModelCode"] . $ticketId . "</a>") . "<br /> " . $comment, true, $notificationsData);
                        $this->load->model("case_comment_attachment");
                        $are_files_uploaded = $this->dms->check_if_files_were_uploaded($_FILES);
                        if ($are_files_uploaded) {
                            $note_parent_folder = $this->dms->create_note_parent_folder($ticketId, $current_time, "case");
                        }
                        foreach ($_FILES as $file_key => $file) {
                            if ($file["error"] != 4) {
                                $upload_response = $this->dms->upload_file(["module" => "case", "module_record_id" => $ticketId, "container_name" => $current_time, "lineage" => $note_parent_folder["lineage"] ?? "", "upload_key" => $file_key, "visible_in_cp" => 1]);
                                if ($upload_response["status"]) {
                                    $this->case_comment_attachment->set_field("case_comment_id", $this->case_comment->get_field("id"));
                                    $this->case_comment_attachment->set_field("path", $upload_response["file"]["id"]);
                                    $this->case_comment_attachment->set_field("uploaded", "Yes");
                                    if (!$this->case_comment_attachment->insert()) {
                                        $response["status"][] = $this->lang->line("failed_to_save_case_comment");
                                        $response["type"][] = "error";
                                    } else {
                                        $this->case_comment_attachment->reset_fields();
                                    }
                                } else {
                                    $upload_failed_files[] = $file["name"];
                                }
                            }
                        }
                    } else {
                        $response["error"] = implode(",<br /> ", $this->case_comment->get("validationErrors"));
                    }
                }
            }
            if ($this->input->is_ajax_request()) {
                $this->output->set_content_type("application/json")->set_output(json_encode($response));
            } else {
                if (isset($response["error"]) && $response["error"]) {
                    $response["type"] = "error";
                } else {
                    if (!empty($upload_failed_files)) {
                        $this->setPinesMessage("warning", sprintf($this->lang->line("prb_in_uploading_files"), implode(", ", $upload_failed_files)));
                    } else {
                        $this->setPinesMessage("success", $this->lang->line("comment_added_successfully"));
                    }
                }
                $this->load->view("tickets/commentResult", $response);
            }
        }
    }
    public function moveTicketStatus($ticketId, $ticketStatus)
    {
        $userLoggedIn = $this->ci->session->userdata("CP_user_id");
        $old_values = $this->legal_case->get_old_values($ticketId);
        $old_status = $old_values["case_status_id"];
        if (!$this->workflow_status->check_transition_allowed($ticketId, $ticketStatus, $userLoggedIn, $this->legal_case->get("portalChannel"))) {
            redirect("home");
        }
        if ($this->workflow_status->moveStatus($ticketId, $ticketStatus, $userLoggedIn, $this->legal_case->get("portalChannel"))) {
            $this->legal_case->fetch($ticketId);
            $this->load->model("user", "userfactory");
            $this->user = $this->userfactory->get_instance();
            $this->user->fetch($this->legal_case->get_field("user_id"));
            $toEmail = $this->user->get_field("email");
            $this->legal_case->notifyTicketUser($ticketId, $ticketStatus, $userLoggedIn, $this->ci->session->userdata("CP_profileName"), $this->legal_case->get_field("user_id"), $toEmail, true, $this->legal_case->get("portalChannel"), $old_status);
            redirect("tickets/view/" . $ticketId);
        }
        redirect("home");
    }
    public function comment_attachments_download($commentId)
    {
        $this->load->model("case_comment_attachment");
        $comment_attachments = $this->case_comment_attachment->get_attachments_for_comment($commentId);
        $files_ids = [];
        foreach ($comment_attachments as $attachment) {
            $files_ids[] = $attachment["path"];
        }
        if (count($files_ids) == 1) {
            $response = $this->dms->download_file("case", $files_ids[0]);
        } else {
            $response = $this->dms->download_files_as_zip("case", $files_ids, "comment_attachments");
        }
        if (!$response["status"]) {
            $this->setPinesMessage("error", $response["message"]);
            $this->load->library("user_agent");
            if ($this->agent->is_referral()) {
                redirect($this->agent->referrer());
            }
        }
    }
    public function download_file($file_id, $newest_version = false)
    {
        $this->dms->download_file("case", $file_id, $newest_version);
    }
    public function customer_portal_users_autocomplete()
    {
        $term = trim((string) $this->input->get("term"));
        $results = $this->customer_portal_users->lookup($term, $this->input->get("object_id"), $this->input->get("object_category"));
        $this->output->set_content_type("application/json");
        $this->output->set_output(json_encode($results));
    }
    public function lookup_custom_fields_autocomplete()
    {
        $term = trim((string) $this->input->get("term"));
        $lookup_type = trim((string) $this->input->get("lookup_type"));
        $results = NULL;
        $this->load->model("custom_field", "custom_fieldfactory");
        $this->custom_field = $this->custom_fieldfactory->get_instance();
        $lookup_type_properties = $this->custom_field->get_lookup_type_properties($lookup_type);
        $lookup_model = $this->custom_field->load_lookup_model($lookup_type_properties);
        $results = $lookup_model->lookup($term, true);
        $this->output->set_content_type("application/json");
        $this->output->set_output(json_encode($results));
    }
    public function update_ticket_requester()
    {
        if ($this->input->post(NULL) && $this->input->post("ticketId") && $this->legal_case->fetch(["id" => $this->input->post("ticketId")])) {
            if ($this->input->post("requestedBy")) {
                $this->customer_portal_users->fetch(["id" => $this->input->post("requestedBy")]);
                $contact_id = $this->customer_portal_users->add_cp_user_as_contact(true, false, $this->customer_portal_users->get_field("email"));
                $this->legal_case->set_field("requestedBy", $contact_id);
                if ($this->legal_case->update()) {
                    $this->legal_case->touch_logs("update", [], $this->session->userdata("CP_user_id"), $this->legal_case->get("portalChannel"));
                    $response["modifiedOn"] = date("Y-m-d H:i", strtotime($this->legal_case->get_field("modifiedOn")));
                    $response["status"] = true;
                } else {
                    $response["status"] = false;
                }
            } else {
                $this->legal_case->set_field("requestedBy", NULL);
                if ($this->legal_case->update()) {
                    $this->legal_case->touch_logs("update", [], $this->ci->session->userdata("CP_user_id"), $this->legal_case->get("portalChannel"));
                    $response["modifiedOn"] = date("Y-m-d H:i", strtotime($this->legal_case->get_field("modifiedOn")));
                    $response["status"] = true;
                } else {
                    $response["status"] = false;
                }
            }
        } else {
            $response["status"] = false;
        }
        $response["user_ticket_permission"] = $this->customer_portal_users->get_user_ticket_permission($this->input->post("ticketId"), $this->session->userdata("CP_user_id"));
        $response["message"] = $this->handle_ticket_auto_update_response_message($response["status"], $response["user_ticket_permission"]);
        $this->output->set_content_type("application/json")->set_output(json_encode($response));
    }
    public function update_ticket_watchers()
    {
        $this->load->model("customer_portal_ticket_watcher", "customer_portal_ticket_watcherfactory");
        $this->customer_portal_ticket_watcher = $this->customer_portal_ticket_watcherfactory->get_instance();
        $watchers = $this->input->post("watchers") ? $this->input->post("watchers") : NULL;
        $response["status"] = $this->customer_portal_ticket_watcher->add_watchers_to_ticket($watchers, $this->input->post("ticketId"));
        $response["user_ticket_permission"] = $this->customer_portal_users->get_user_ticket_permission($this->input->post("ticketId"), $this->session->userdata("CP_user_id"));
        $response["message"] = $this->handle_ticket_auto_update_response_message($response["status"], $response["user_ticket_permission"]);
        $this->output->set_content_type("application/json")->set_output(json_encode($response));
    }
    private function handle_ticket_auto_update_response_message($response_status, $user_ticket_permission)
    {
        $message = NULL;
        if ($response_status && $user_ticket_permission == "none") {
            $message = ["type" => "warning", "text" => $this->lang->line("access_to_ticket_lost")];
        } else {
            if ($response_status) {
                $message = ["type" => "success", "text" => $this->lang->line("updates_saved_successfully")];
            } else {
                if (!empty($response_status)) {
                    $message = ["type" => "error", "text" => $this->lang->line("updates_failed")];
                }
            }
        }
        return $message;
    }
    public function ticket_tasks($ticketId)
    {
        $this->load->model("task", "taskfactory");
        $this->task = $this->taskfactory->get_instance();
        $response["data"] = $this->task->load_case_tasks($ticketId, "en");
        $response["totalRows"] = count($response["data"]);
        $this->output->set_content_type("application/json")->set_output(json_encode($response));
    }
    public function ticket_hearings($ticketId)
    {
        $this->load->model("legal_case_hearing", "legal_case_hearingfactory");
        $this->legal_case_hearing = $this->legal_case_hearingfactory->get_instance();
        $system_preferences = $this->system_preference->get_key_groups("DefaultValues");
        $hijri_feature = $system_preferences["SystemValues"]["hijriCalendarFeature"] ?? false;
        $response = $this->legal_case_hearing->k_load_all_hearings([], [], $ticketId, "", $hijri_feature ? true : false, "en");
        $this->output->set_content_type("application/json")->set_output(json_encode($response));
    }
}

?>