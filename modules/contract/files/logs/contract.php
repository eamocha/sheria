<?php

if (!defined("BASEPATH")) {
    exit("No direct script access allowed");
}
require APPPATH . "controllers/Top_controller.php";
class Contracts extends Contract_controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model("party_category_language", "party_category_languagefactory");
        $this->party_category_language = $this->party_category_languagefactory->get_instance();
        $this->load->model("contract_type_language", "contract_type_languagefactory");
        $this->contract_type_language = $this->contract_type_languagefactory->get_instance();
        $this->load->model("sub_contract_type_language", "sub_contract_type_languagefactory");
        $this->sub_contract_type_language = $this->sub_contract_type_languagefactory->get_instance();
        $this->load->model("applicable_law_language", "applicable_law_languagefactory");
        $this->applicable_law_language = $this->applicable_law_languagefactory->get_instance();
        $this->load->model("contract_status");
        $this->load->model("contract_party", "contract_partyfactory");
        $this->contract_party = $this->contract_partyfactory->get_instance();
        $this->load->model("custom_field", "custom_fieldfactory");
        $this->custom_field = $this->custom_fieldfactory->get_instance();
        $this->load->model("custom_fields_per_model_type", "custom_fields_per_model_typefactory");
        $this->custom_fields_per_model_type = $this->custom_fields_per_model_typefactory->get_instance();
        $this->load->model("contract", "contractfactory");
        $this->contract = $this->contractfactory->get_instance();
        $this->load->model("contract_user", "contract_userfactory");
        $this->contract_user = $this->contract_userfactory->get_instance();
        $this->load->model("contract_contributor", "contract_contributorfactory");
        $this->contract_contributor = $this->contract_contributorfactory->get_instance();
        $this->load->library("dmsnew");
        $this->load->model("iso_currency");
        $this->load->model("contract_renewal_notification_assigned_team", "contract_renewal_notification_assigned_teamfactory");
        $this->contract_renewal_notification_assigned_team = $this->contract_renewal_notification_assigned_teamfactory->get_instance();
        $this->load->model("contract_renewal_notification_email", "contract_renewal_notification_emailfactory");
        $this->contract_renewal_notification_email = $this->contract_renewal_notification_emailfactory->get_instance();
        $this->load->model("contract_collaborator", "contract_collaboratorfactory");
        $this->contract_collaborator = $this->contract_collaboratorfactory->get_instance();
        $this->load->model("contract_approval_submission", "contract_approval_submissionfactory");
        $this->contract_approval_submission = $this->contract_approval_submissionfactory->get_instance();
        $this->load->model("contract_signature_submission", "contract_signature_submissionfactory");
        $this->contract_signature_submission = $this->contract_signature_submissionfactory->get_instance();
        $this->load->model("contract_workflow_status_transition", "contract_workflow_status_transitionfactory");
        $this->contract_workflow_status_transition = $this->contract_workflow_status_transitionfactory->get_instance();
        $this->load->model("email_notification_scheme");
        $this->load->model("contract_sla_management", "contract_sla_managementfactory");
        $this->contract_sla_management = $this->contract_sla_managementfactory->get_instance();
    }
   

    /**
     * Add a new contract or generate a contract from a template.
     * Handles both the initial form display and the submission of contract data.
     */
    public function add()
    {
        if (!$this->input->is_ajax_request()) {
            $this->upload_contract();
        } else {
            if (!$this->input->post(NULL)) {
                $response["result"] = true;
                if ($this->input->get("option", true) && $this->input->get("step", true)) {
                    $option = $this->input->get("option", true);
                    $step = $this->input->get("step", true);
                    $commercial_service_category= $this->input->get("commercial_service_category");
                    switch ($option) {
                        case "choose":
                            if ($step == 1) {
                                $data["types"] = $this->contract_type_language->load_list_per_language($commercial_service_category);
                                $data["title"] = $commercial_service_category=="mou"?$this->lang->line("generate_mou"):$this->lang->line("generate_contract");
                                $response["result"] = true;

                                $data["hide_show_notification"] = $this->email_notification_scheme->get_hide_show_send_email_notification_by_trigger_action("add_contract");
                                $data["show_notification"] = true;
                                $data["option"] = $option;
                                $data["commercial_service_category"] = $commercial_service_category;
                                $response["html"] = $this->load->view("contracts/generate/form", $data, true);

                            } else {
                                if ($this->input->get("template_id")) {
                                    $this->load->model("contract_template", "contract_templatefactory");
                                    $this->contract_template = $this->contract_templatefactory->get_instance();
                                    $data = $this->contract_template->load_template_data($this->input->get("template_id"));
                                    $users_emails = $this->user->load_active_emails();
                                    $data["users_emails"] = array_map(function ($users_emails) {
                                        return ["email" => $users_emails];
                                    }, array_keys($users_emails));
                                    $this->load->model(["provider_group"]);
                                    $data["assigned_teams_list"] = $this->provider_group->load_all();
                                    $data["today"] = date("Y-m-d");
                                    $this->load->model("reminder", "reminderfactory");
                                    $this->reminder = $this->reminderfactory->get_instance();
                                    $data["notify_me_before_time_types"] = array_combine($this->reminder->get("notify_me_before_time_type"), [$this->lang->line("day_or_days"), $this->lang->line("week_or_weeks"), $this->lang->line("month")]);
                                    $data["channel"] = $this->web_channel;
                                    $data["reference_number"] = $this->get_new_ref_number(); //set default reference number
                                    $response["pages"] = count($data["pages"]);
                                    $response["html"] = $this->load->view("contracts/generate/questionnaire_form", $data, true);
                                } else {
                                    $response["result"] = false;
                                    $response["display_message"] = $this->lang->line("no_contract_template_chosen");
                                }
                            }
                            break;
                        default:
                            if ($step == 1) {
                                $data["hide_show_notification"] = $this->email_notification_scheme->get_hide_show_send_email_notification_by_trigger_action("add_contract");
                                $data["show_notification"] = true;
                                $data["option"] = $option;
                                $data["commercial_service_category"] = $commercial_service_category;
                                $response["html"] = $this->load->view("contracts/generate/form", $data, true);
                            }
                    }
                }
            } else {
                $option = $this->input->post("option", true);
                switch ($option) {
                    case "upload":
                        $response = $this->upload_contract();
                        break;
                    case "choose":
                        $response = $this->contract->save_contract_from_template();
                        if ($response["result"]) {

                            $this->notify_before_end_date($response["id"]);
                        }
                        break;
                }
            }
            $this->output->set_content_type("application/json")->set_output(json_encode($response));
        }
    }
    private function upload_contract()
    {
        if (!$this->is_auth->is_logged_in()) {
            exit("login_needed");
        }
        if (!$this->input->post(NULL)) {
            $this->pageTitle = sprintf($this->lang->line("sheria360_browser_title"), $this->lang->line("upload_contract"));
            $data = $this->load_common_data("add_contract");
            $users_emails = $this->user->load_active_emails();
            $data["users_emails"] = array_map(function ($users_emails) {
                return ["email" => $users_emails];
            }, array_keys($users_emails));
            $this->provider_group->fetch(["allUsers" => 1]);
            $data["title"] = $this->lang->line("upload_contract");
            $data["all_teams_id"] = $this->provider_group->get_field("id");
            $data["assignees"] = ["" => "---"] + $this->user->load_users_list("", ["key" => "id", "value" => "name"]);
            $data["default_priority"] = "medium";
            $data["reference_number"] = $this->get_new_ref_number(); //set default reference number

            $data["custom_fields"] = $this->custom_field->get_field_html($this->contract->get("modelName"), 0);
            $this->includes("jquery/dropzone_v6/dropzone.min", "js");
            $this->includes("jquery/dropzone_v6/dropzone", "css");
            $this->includes("contract/upload_contract", "js");
            $this->load->view("partial/header");
            $this->load->view("contracts/generate/upload_contract", $data);
        } else {
            $response = $this->save();
            if ($response["result"]) {
                $this->load->library("dmsnew");
                $failed_uploads_count = 0;
                foreach ($_FILES as $file_key => $file) {
                    if ($file["error"] != 4) {
                        $upload_response = $this->dmsnew->upload_file(["module" => "contract", "module_record_id" => $response["id"], "createdByChannel" => "A4L", "upload_key" => $file_key]);
                        if (!$upload_response["status"]) {
                            $failed_uploads_count++;
                        }
                    }
                }
                if (0 < $failed_uploads_count) {
                    $response["validationErrors"]["files"] = sprintf($this->lang->line("files_were_not_uploaded"), $failed_uploads_count);
                }
            }
            return $response;
        }
    }
    /**
     * Get a new reference number for a contract.
     * This method can be called via AJAX to retrieve a new reference number.
     */
public function get_new_ref_number()
{
    $this->load->model("contract", "contractfactory");
    $this->contract = $this->contractfactory->get_instance();
   return $this->contract->get_new_ref_number();

}
    /**
     * Add custom fields for contract types.
     * This method handles AJAX requests to add custom fields based on the contract type ID.
     */
    public function add_contract_type_custom_fields()
    {
        if (!$this->input->is_ajax_request()) {
            show_404();
        }
        if ($this->input->get("type_id")) {
            $data["custom_fields"] = $this->custom_field->get_field_html($this->contract->get("modelName"), 0, NULL, false, [], $this->input->get("type_id"));
            $response["html"] = $this->load->view("custom_fields/dialog_form_custom_field_template", ["custom_fields" => $data["custom_fields"]], true);
        }
        $this->output->set_content_type("application/json")->set_output(json_encode($response));
    }
    public function add_negotiation()
    {
        $response["result"] = false;
        if ($this->input->get("contract_approval_status_id", true) && $this->input->get("contract_id", true)) {
            $data["title"] = $this->lang->line("start_negotiation");
            $data["module"] = "contract";
            $data["contract_id"] = $this->input->get("contract_id", true);
            $data["contract_approval_status_id"] = $this->input->get("contract_approval_status_id", true);
            $data["hide_show_notification"] = $this->email_notification_scheme->get_hide_show_send_email_notification_by_trigger_action("negotiation_requested");
            $data["show_notification"] = true;
            $response["result"] = true;
            $response["html"] = $this->load->view("contracts/view/approval_center/negotiation/form", $data, true);
        }
        if ($this->input->post(NULL, true)) {
            $this->load->model("contract_approval_negotiation", "contract_approval_negotiationfactory");
            $this->contract_approval_negotiation = $this->contract_approval_negotiationfactory->get_instance();
            $response = $this->contract_approval_negotiation->add_negotiation("", "user", $this->is_auth->get_fullname());
        }
        $this->output->set_content_type("application/json")->set_output(json_encode($response));
    }
    public function add_comment_negotiation()
    {
        $response["result"] = false;
        if ($this->input->post(NULL, true)) {
            $this->load->model("contract_approval_negotiation", "contract_approval_negotiationfactory");
            $this->contract_approval_negotiation = $this->contract_approval_negotiationfactory->get_instance();
            $response = $this->contract_approval_negotiation->add_comment_negotiation("", "user", $this->is_auth->get_fullname());
        }
        $this->output->set_content_type("application/json")->set_output(json_encode($response));
    }
    public function forward_negotiation()
    {
        $response["result"] = false;
        if ($this->input->get("negotiation_id", true) && $this->input->get("contract_id", true)) {
            $data["title"] = $this->lang->line("forward_negotiation");
            $data["module"] = "contract";
            $data["contract_id"] = $this->input->get("contract_id", true);
            $data["negotiation_id"] = $this->input->get("negotiation_id", true);
            $response["result"] = true;
            $response["html"] = $this->load->view("contracts/view/approval_center/negotiation/forward", $data, true);
        }
        if ($this->input->post(NULL, true)) {
            $this->load->model("contract_approval_negotiation", "contract_approval_negotiationfactory");
            $this->contract_approval_negotiation = $this->contract_approval_negotiationfactory->get_instance();
            $response = $this->contract_approval_negotiation->forward_negotiation();
        }
        $this->output->set_content_type("application/json")->set_output(json_encode($response));
    }
    public function complete_negotiation()
    {
        $response["result"] = false;
        $this->load->model("contract_approval_negotiation", "contract_approval_negotiationfactory");
        $this->contract_approval_negotiation = $this->contract_approval_negotiationfactory->get_instance();
        if ($this->input->post(NULL, true)) {
            $response = $this->contract_approval_negotiation->complete_negotiation("", "user", $this->is_auth->get_fullname());
        }
        $this->output->set_content_type("application/json")->set_output(json_encode($response));
    }
    public function download_docs_zip_file()
    {
        $this->_download_docs_zip_file("contract", "contracts", $_GET["selected_items"]);
    }
    public function load_templates_per_contract_types()
    {
        if (!$this->input->is_ajax_request()) {
            show_404();
        }
        if (!$this->is_auth->is_logged_in()) {
            exit("login_needed");
        }
        $response["templates"] = [];
        $this->load->model("contract_template", "contract_templatefactory");
        $this->contract_template = $this->contract_templatefactory->get_instance();
        if ($this->input->get("type_id")) {
            $where[] = ["type_id", $this->input->get("type_id")];
            if ($this->input->get("sub_type_id")) {
                $where[] = ["sub_type_id", $this->input->get("sub_type_id")];
            }
            $response["templates"] = $this->contract_template->load_list(["where" => $where]);
        }
        $this->output->set_content_type("application/json")->set_output(json_encode($response));
    }
    public function load_sub_contract_types()
    {
        if (!$this->input->is_ajax_request()) {
            show_404();
        }
        if (!$this->is_auth->is_logged_in()) {
            exit("login_needed");
        }
        $response = $this->sub_contract_type_language->load_all_per_type($this->input->get("type_id"));
        $this->output->set_content_type("application/json")->set_output(json_encode($response));
    }
    public function load_board_members_per_party()
    {
        $this->authenticate_exempted_actions();
        $response["result"] = true;
        if ($this->input->get("party_id")) {
            $response["users"] = $this->contract_party->fetch_bm_contacts_per_company($this->input->get("party_id"), $this->input->get("role_id"));
        } else {
            $response["result"] = false;
        }
        $this->output->set_content_type("application/json")->set_output(json_encode($response));
    }
    public function load_shareholders_per_party()
    {
        $this->authenticate_exempted_actions();
        $response["result"] = true;
        if ($this->input->get("party_id")) {
            $response["users"] = $this->contract_party->fetch_sh_contacts_per_company($this->input->get("party_id"));
        } else {
            $response["result"] = false;
        }
        $this->output->set_content_type("application/json")->set_output(json_encode($response));
    }
    public function archive_unarchive_contracts()
    {
        $response = [];
        $contracts_ids = $this->input->post("contracts_ids");
        if ($contracts_ids[0] == "archive") {
            $all_archived = [];
            for ($x = 0; $x < count($contracts_ids[1]); $x++) {
                $id = $contracts_ids[1][$x];
                $result = $this->db->where("contract.id = " . $id)->update("contract", ["archived" => "yes"]);
                if ($result) {
                    array_push($all_archived, "yes");
                }
            }
            $response["status"] = count($all_archived) == count($contracts_ids[1]) ? true : false;
        } else {
            if ($contracts_ids[0] == "unarchive") {
                $all_unarchived = [];
                for ($x = 0; $x < count($contracts_ids[1]); $x++) {
                    $id = $contracts_ids[1][$x];
                    $result = $this->db->where("contract.id = " . $id)->update("contract", ["archived" => "no"]);
                    if ($result) {
                        array_push($all_unarchived, "no");
                    }
                }
                $response["status"] = count($all_unarchived) == count($contracts_ids[1]) ? true : false;
            } else {
                if (!empty($contracts_ids)) {
                    $contract_details = $this->contract->load(["select" => ["id,archived"], "where" => ["id", $contracts_ids]]);
                    $result = $this->db->where("contract.id = (" . $contracts_ids . ")")->update("contract", ["archived" => $contract_details["archived"] == "no" ? "yes" : "no"]);
                    $response["archived"] = $result ? $contract_details["archived"] == "no" ? "yes" : "no" : $contract_details["archived"];
                    $response["status"] = $result;
                }
            }
        }
        $this->output->set_content_type("application/json")->set_output(json_encode($response));
    }
    public function draft_collaborate($contract_id = "", $document_id = "")
    {
        $data = [];
        $this->contract->fetch($contract_id);
        $contract = $this->contract->get_fields();
        $data["contract"] = $contract;
        $data["contract_id"] = $contract_id;
        $data["model_code"] = $this->contract->get("modelCode");
        $data["docs"] = $this->dmsnew->load_documents_for_collaboration($this->contract->get("modelName"), $contract_id);
        $data["document_id"] = $document_id;


        if (!$this->input->is_ajax_request()) {
            $data["show_toolbar"]=true;
            $this->document_management_system->fetch(["id" => $document_id]);//to get the parent_lineage
            $data["parent_lineage"] = $this->document_management_system->get_field("parent");

            $this->pageTitle = sprintf($this->lang->line("sheria360_browser_title"), $this->lang->line("draft_and_collaborate"));
            $this->includes("contract/cp_contract_common", "js");
            $this->load->view("partial/header");
            $this->load->view("contracts/view/draft_collaborate", $data);
            $this->load->view("partial/footer");
        } else {
            $data["show_share_button"] = true;
            $response["html"] = $this->load->view("contracts/view/draft_collaborate", $data, true);
            $this->output->set_content_type("application/json")->set_output(json_encode($response));
        }
    }
    public function load_docs_count()
    {
        $this->authenticate_exempted_actions();
        if (!$this->input->is_ajax_request()) {
            redirect("dashboard");
        }
        $this->load->model("document_management_system", "document_management_systemfactory");
        $this->document_management_system = $this->document_management_systemfactory->get_instance();
        $response["count"] = $this->document_management_system->count_contract_related_documents($this->input->get("contract_id"));
        $this->output->set_content_type("application/json")->set_output(json_encode($response));
    }
    public function share_doc()
    {
        $this->authenticate_exempted_actions();
        $response["result"] = true;
        if (!$this->input->is_ajax_request()) {
            redirect("dashboard");
        }
        if ($this->input->post()) {
            if (!$this->input->post("emails")) {
                $response["result"] = false;
                $response["validation_errors"]["emails"] = $this->lang->line("cannot_be_blank_rule");
            } else {
                $emails = explode(";", $this->input->post("emails"));
                $this->load->model("system_preference");
                $systemPreferences = $this->system_preference->get_key_groups();
                $subject = $systemPreferences["OutgoingMail"]["outgoingMailSubjectPrefix"] . " " . $this->contract->get("modelCode") . $this->input->post("module_id") . ": " . sprintf($this->lang->line("share_doc_email_subject"), $this->input->post("doc_name"));
                $active_emails = $this->user->load_active_emails();
                $module_id = $this->input->post("module_id");
                foreach ($emails as $email) {
                    $is_external_email = !in_array($email, $active_emails);
                    if ($is_external_email) {
                        $this->load->model("external_user_token", "external_user_tokenfactory");
                        $this->external_user_token = $this->external_user_tokenfactory->get_instance();
                        $this->load->model("external_share_document", "external_share_documentfactory");
                        $this->external_share_document = $this->external_share_documentfactory->get_instance();
                        $external_user_token_data = $this->external_user_token->generate_external_user_token();
                        $this->external_share_document->set_field("token_id", $external_user_token_data["external_user_token_id"]);
                        $this->external_share_document->set_field("document_id", $this->input->post("doc_id"));
                        $this->external_share_document->set_field("share_type", $this->input->post("action"));
                        $this->external_share_document->set_field("external_user_email", $email);
                        $this->external_share_document->insert();
                        $url = base_url() . "external_actions/draft_collaborate_otp/" . $external_user_token_data["external_user_token_id"] . "/" . $external_user_token_data["external_user_token"];
                    } else {
                        $url = base_url() . "contracts/draft_collaborate/" . $module_id . "/" . $this->input->post("doc_id");
                    }
                    $view_data = ["user_profile_name" => $this->session->userdata("AUTH_userProfileName"), "action" => $this->input->post("action"), "url" => $url];
                    $content = $this->load->view("templates/email", ["content" => $this->load->view("notifications/share_doc_email_content", $view_data, true)], true);
                    $this->load->library("email_notifications");
                    $response["result"] = $this->email_notifications->send_email([$email], $subject, $content);
                }
            }
        } else {
            $this->load->model("email_notification_scheme");
            $data["users_emails"] = $this->email_notification_scheme->load_available_users_emails();
            $data["module_id"] = $this->input->get("module_id");
            $data["doc_id"] = $this->input->get("doc_id");
            $this->load->model("document_management_system", "document_management_systemfactory");
            $this->document_management_system = $this->document_management_systemfactory->get_instance();
            $this->document_management_system->fetch($data["doc_id"]);
            $data["doc_name"] = $this->document_management_system->get_field("name");
            $data["doc_extension"] = $this->document_management_system->get_field("extension");
            $data["actions"] = ["edit" => $this->lang->line("edit"), "view" => $this->lang->line("view")];
            $response["html"] = $this->load->view("documents_management_system/share_form", $data, true);
        }
        $this->output->set_content_type("application/json")->set_output(json_encode($response));
    }
    public function resend_approval_email()
    {
        $this->authenticate_exempted_actions();
        if (!$this->input->is_ajax_request()) {
            redirect("dashboard");
        }
        $this->load->model("approval", "approvalfactory");
        $this->approval = $this->approvalfactory->get_instance();
        $this->load->model("contract_approval_status", "contract_approval_statusfactory");
        $this->contract_approval_status = $this->contract_approval_statusfactory->get_instance();
        $this->contract_approval_status->fetch($this->input->post("approval_status_id"));
        $response = $this->approval->notify_next_approvers((int) $this->contract_approval_status->get_field("rank") - 1, $this->input->post("contract_id"));
        $this->output->set_content_type("application/json")->set_output(json_encode($response));
    }
    private function move_contract_attachments_to_parent_folder($comment, $parent_folder_name, $contract_id, $newComment = NULL)
    {
        $ids = $this->get_comment_attachments($comment);
        if (0 < count($ids) && isset($newComment)) {
            $parent_id = $this->dmsnew->get_document_details(["id" => end($ids)])["parent"];
            $this->dmsnew->rename_document("contract", $parent_id, "folder", $parent_folder_name);
            $newIds = $this->get_comment_attachments($newComment);
            $ids = array_diff($newIds, $ids);
        } else {
            if (isset($newComment)) {
                $ids = $this->get_comment_attachments($newComment);
            }
        }
        if (0 < count($ids)) {
            $parent_folder = $this->dmsnew->create_note_parent_folder($contract_id, $parent_folder_name, "contract");
            $this->dmsnew->move_document_handler($parent_folder["id"], $ids, [], "contract");
        }
    }
    private function get_comment_attachments($comment)
    {
        $ids = [];
        preg_match_all("/[|]+\\d+/", $comment, $ids);
        $ids = $ids[0];
        for ($index = 0; $index < count($ids); $index++) {
            $id = $ids[$index];
            $id = ltrim($id, $id[0]);
            $ids[$index] = $id;
        }
        return $ids;
    }
    public function autocomplete_party()
    {
        if (!$this->input->is_ajax_request()) {
            show_404();
        }
        if (!$this->is_auth->is_logged_in()) {
            exit("login_needed");
        }
        $term = trim((string) $this->input->get("term"));
        $results = $this->contract_party->lookup_contact_company($term);
        $this->output->set_content_type("application/json");
        $this->output->set_output(json_encode($results));
    }

    /*
     * Milestones
     */
    public function milestone($contract_id = 0){
        $data = [];
        if($this->input->is_ajax_request()){
            $this->load->model("milestone", "milestonefactory");
            $this->milestone = $this->milestonefactory->get_instance();
            $data["milestones"] = $this->milestone->load_milestones_per_contract($contract_id);
            $this->contract->fetch($contract_id);
            $contract = $this->contract->get_fields();
            $data["visible_to_cp"] = (!strcmp($this->contract->get_field("channel"), $this->cp_channel) || ($this->contract->get_field("visible_to_cp") == "1"));
            $data["milestone_visible_to_cp"] = $this->contract->get_field("milestone_visible_to_cp");
            $data["model_code"] = $this->contract->get("modelCode");
            $data["contract"] = $contract;
            $data["contract_full_name"] = $contract["name"];
            $data["rtl"] = $this->is_auth->is_layout_rtl();
            $data["language"] = "en";// note that I hardcorded this. $this->session->userdata("AUTH_language_key");
//            $data["progress_statuses"] = [["value"=>"open","data-content"=>"<i class=\"fa-solid fa-hourglass-start fa-lg mr-2 ml-2\"></i>" . $this->lang->line("open_status")],["value"=>"in_progress","data-content"=>'<i class="fa-solid fa-spinner fa-lg mr-2 ml-2"></i>' . $this->lang->line("in_progress")],["value"=>"on_hold","data-content"=>'<i class="fa-solid fa-hand fa-lg mr-2 ml-2"></i>' . $this->lang->line("on_hold")],["value"=>"completed","data-content"=>'<i class="fa-solid fa-circle-check fa-lg mr-2 ml-2"></i>' . $this->lang->line("completed")],["value"=>"cancelled","data-content"=>'<i class="fa-solid fa-ban fa-lg mr-2 ml-2"></i>' . $this->lang->line("cancelled")]];
//            $data["financial_statuses"] = [["value"=>null,"data-content"=>$this->lang->line("select_financial_status")],["value"=>"paid","data-content"=>$this->lang->line("paid")],["value"=>"partially_paid","data-content"=>'<i class="fa-solid fa-percent mr-3 cancelled fa-lg ml-2"></i>' . $this->lang->line("partially_paid")],["value"=>"not_paid","data-content"=>'<i class="fa-brands fa-creative-commons-nc mr-3 cancelled fa-lg ml-2"></i>' . $this->lang->line("not_paid")],["value"=>"non_billable","data-content"=>'<i class="fa-solid fa-file-invoice-dollar mr-3 cancelled fa-lg ml-2"></i>' . $this->lang->line("non_billable")],["value"=>"not_applicable","data-content"=>'<i class="fa-solid fa-xmark mr-3 cancelled fa-lg ml-2"></i>' . $this->lang->line("not_applicable")],["value"=>"cancelled","data-content"=>'<i class="fa-solid fa-ban mr-3 cancelled fa-lg ml-2"></i>' . $this->lang->line("cancelled")]];

            $data["progress_statuses"] = [
                [
                    "value" => "open",
                    "data-content" => "<i class='fa-solid fa-hourglass-start fa-lg mr-2 ml-2'></i>Open"
                ],
                [
                    "value" => "in_progress",
                    "data-content" => "<i class='fa-solid fa-spinner fa-lg mr-2 ml-2'></i>In Progress"
                ],
                [
                    "value" => "on_hold",
                    "data-content" => "<i class='fa-solid fa-hand fa-lg mr-2 ml-2'></i>On Hold"
                ],
                [
                    "value" => "completed",
                    "data-content" => "<i class='fa-solid fa-circle-check fa-lg mr-2 ml-2'></i>Completed"
                ],
                [
                    "value" => "cancelled",
                    "data-content" => "<i class='fa-solid fa-ban fa-lg mr-2 ml-2'></i>Cancelled"
                ]
            ];
            $data["financial_statuses"] = [
                [
                    "value" => null,
                    "data-content" => "Financial Status"
                ],
                [
                    "value" => "paid",
                    "data-content" => "<i class='fa-solid fa-hand-holding-dollar mr-3 cancelled fa-lg ml-2'></i>Paid"
                ],
                [
                    "value" => "partially_paid",
                    "data-content" => "<i class='fa-solid fa-percent mr-3 cancelled fa-lg ml-2'></i>Partially Paid"
                ],
                [
                    "value" => "not_paid",
                    "data-content" => "<i class='fa-brands fa-creative-commons-nc mr-3 cancelled fa-lg ml-2'></i>Not Paid"
                ],
                [
                    "value" => "non_billable",
                    "data-content" => "<i class='fa-solid fa-file-invoice-dollar mr-3 cancelled fa-lg ml-2'></i>Non-billable"
                ],
                [
                    "value" => "not_applicable",
                    "data-content" => "<i class='fa-solid fa-xmark mr-3 cancelled fa-lg ml-2'></i>Not Applicable"
                ],
                [
                    "value" => "cancelled",
                    "data-content" => "<i class='fa-solid fa-ban mr-3 cancelled fa-lg ml-2'></i>Cancelled"
                ]
            ];
            $response["data"] = $data;
            $response["html"] = $this->load->view("contracts/view/milestones", $data, true);
        }
        $this->output->set_content_type("application/json")->set_output(json_encode($response));
    }
    /*
     *
     */

    public function add_milestone(){
        $response = $this->milestone_per_contract();
        $this->output->set_content_type("application/json")->set_output(json_encode($response));
    }
    public function edit_milestone(){
        $response = $this->milestone_per_contract("edit");
        $this->output->set_content_type("application/json")->set_output(json_encode($response));
    }
    /*
     * Milestones per contract function
     */

    private function milestone_per_contract($action = "add") {
        $response = ["result" => false, "html" => ""]; // Initialize response

        if ($this->input->get(null, true)) {
            $milestone_id = $this->input->get("milestone_id");
            $contract_id = $this->input->get("contract_id");
            $data["notify_me_before_time_types"] = array_combine($this->reminder->get("notify_me_before_time_type"), [$this->lang->line("day_or_days"), $this->lang->line("week_or_weeks"), $this->lang->line("month")]);
            $data["notify_me_before_types"] = array_combine($this->reminder->get("notify_me_before_type"), [$this->lang->line("reminder_popup"), $this->lang->line("reminder_popup_and_email")]);
            $data["currencies"] = $this->iso_currency->load_list(["order_by" => ["id", "asc"]], ["firstLine" => [""=>$this->lang->line("none")]]);
            $this->contract->fetch($contract_id);
            $contract = $this->contract->get_fields();
            $data["contract"]["id"] = $contract_id;
            $data["milestone_id"] = ($milestone_id ? $milestone_id : 0);

            if ($action == "edit") {
                $data["milestone_data"] = $this->load_milestone_data($milestone_id);
                $data["target"] = (($data["milestone_data"]["percentage"] == null) ? "amount" : "percentage");
                $data["page_title"] = $this->lang->line("edit_milestone");
                $data["notify_before"] = $this->reminder->load_notify_before_data_to_related_object($milestone_id, $this->milestone->get("_table"));
                !isset($data["notify_before"]["time"]);///check this---Atinga
                $data["reminder_interval_date"] = $this->system_preferences["reminderIntervalDate"] ?? ''; // Use null coalescing operator
                $response["notification_available"] = ($data["notify_before"] ? true : false);
            } else {
                $data["reminder_interval_date"] = $this->system_preferences["reminderIntervalDate"] ?? ''; // Use null coalescing operator
                $data["target"] = "amount";
                $data["milestone_data"]["currency_id"] = ($contract["currency_id"] ? $contract["currency_id"] : null);
                $data["page_title"] = $this->lang->line("add_milestone");
            }
            $response["result"] = true;
            $response["html"] = $this->load->view("contracts/view/milestone_form", $data, true);
        } elseif ($this->input->post(null, true) && $this->contract->fetch($this->input->post("contract_id", true))) {
            $response = $this->save_milestone();
        }

        return $response;
    }

    private function save_milestone()
    {

        $post_data = $this->input->post(null, true);
        $this->load->model("milestone", "milestonefactory");
        $this->milestone = $this->milestonefactory->get_instance();
        $action = $this->milestone->fetch($post_data["id"]) ? "edit" : "add";
        $response["result"] = false;

        if ($action == "add") {
            $post_data["status"] = "open";
        } else {
            $this->milestone->fetch($post_data["id"]);
            $post_data["status"] = $this->milestone->get_field("status");
        }

        $this->milestone->set_fields($post_data);

        if ($this->milestone->validate()) {
            $contract_value=$this->contract->get_field("value");
            $contract_currency=$this->contract->get_field("currency_id");
           $contract_value_currency_isValid= (isset($contract_value, $contract_currency) && is_numeric($contract_value) && is_numeric($contract_currency) && $contract_value > 0 && $contract_currency > 0);

            $notify_before = $this->input->post("notify_me_before");
            $due_date = $this->input->post("due_date");
            $is_not_nb = (isset($notify_before["time"]) && !is_numeric($notify_before["time"]));

            if ($notify_before && ($due_date == null)) {
                $response["validationErrors"]["due_date"] = $this->lang->line("cannot_be_blank_rule");
            }
            elseif ($notify_before && ((!$notify_before["time"] || !$notify_before["time_type"] || !$notify_before["type"]) || $is_not_nb))
            {
                if ($is_not_nb) {
                    $response["validationErrors"]["notify_before"] = sprintf($this->lang->line("is_numeric_rule"), $this->lang->line("notify_before"));
                } else {
                    $response["validationErrors"]["notify_before"] = $this->lang->line("cannot_be_blank_rule");
                }
            }
            //check contract value is valid

            if ($contract_value_currency_isValid){
                if (isset($post_data["target"]) && $post_data["target"] == "on") {
                    $perc=(double)$post_data["percentage"];
                    if(isset($post_data["percentage"]) && $perc<=100 and $perc>0) {
                        $post_data["amount"]=$contract_value*$perc/100;
                        $post_data["currency_id"]=$contract_currency;
                    } else{
                        $response["validationErrors"]["percentage"]=$this->lang->line("percentage_value_incorrect");
                    }//end checking percentage
                } else {
                    $post_data["percentage"] = "";
                }
            }
            else{
                $response["validationErrors"]["percentage"]=$this->lang->line("contract_value_or_currency_not_set");
            }
            if (!isset($response["validationErrors"])) {

                // The following line seems redundant as it's already handled by the if-else above
                // $post_data["percentage"] = "";
               // echo  $post_data["amount"]." and ". $post_data["currency_id"];

                $this->milestone->set_field("channel", "A4L");
                $this->milestone->set_fields($post_data);
                $this->load->model("contract_milestone_document", "contract_milestone_documentfactory");
                $this->contract_milestone_document = $this->contract_milestone_documentfactory->get_instance();
               //exit(json_encode($this->milestone->get_fields()));
                if (($action == "add") ? $this->milestone->insert() : $this->milestone->update()) {
                    $milestone_id = $this->milestone->get_field("id");
                    if (isset($milestone_title) && ($milestone_title != $post_data["title"])) {
                        $ids = $this->contract_milestone_document->load_attachments_per_contract($milestone_id, $post_data["contract_id"]);
                        if (0 < count($ids)) {
                            $result = end($ids);
                            $parent_id = $this->dmsnew->get_document_details(["id" => $result, "module" => "contract", "module_record_id" => $post_data["contract_id"]])["parent"];
                            $this->dmsnew->rename_document("contract", $parent_id, "folder", $post_data["title"]);
                        }
                    }
                    $this->notify_me_before_due_date($milestone_id, $post_data["contract_id"]);
                    $this->load->library("dmsnew");
                    $failed_uploads_count = 0;
                    foreach ($_FILES as $file_key => $file) {
                        if ($file["error"] != 4) {
                            $upload_response = $this->dmsnew->upload_file(["module" => "contract", "module_record_id" => $post_data["contract_id"], "createdByChannel" => "A4L", "upload_key" => $file_key]);
                            if ($upload_response["status"]) {
                                $this->contract_milestone_document->set_field("document_id", $upload_response["file"]["id"]);
                                $this->contract_milestone_document->set_field("milestone_id", $milestone_id);
                                $this->contract_milestone_document->insert();
                                $this->contract_milestone_document->reset_fields();
                                $this->move_milestone_attachments_to_parent_folder($milestone_id, $post_data["contract_id"], $upload_response["file"]["id"], $post_data["title"]);
                            }
                            if (!$upload_response["status"]) {
                                $failed_uploads_count++;
                                continue;
                            }
                        }
                    }
                    if (0 < $failed_uploads_count) {
                        $response["validationErrors"]["files"] = sprintf($this->lang->line("files_were_not_uploaded"), $failed_uploads_count);
                        $this->milestone->delete(["where" => ["id", $milestone_id]]);
                    } else {
                        $response["result"] = true;
                    }
                }
            }
        } else {
            $response["result"] = false;
            $response["validationErrors"] = $this->milestone->get("validationErrors");
        }
        return $response;
    }

    public function milestone_documents($milestone_id){
        $this->load->model("contract_milestone_document", "contract_milestone_documentfactory");
        $this->contract_milestone_document = $this->contract_milestone_documentfactory->get_instance();
        $data = [];
        $response["status"] = true;
        $data["milestone_id"] = $milestone_id;
        $this->load->model("milestone", "milestonefactory");
        $this->milestone = $this->milestonefactory->get_instance();
        $this->milestone->fetch($milestone_id);
        $data["contract_id"] = $this->milestone->get_field("contract_id");
        $data["documents"] = $this->contract_milestone_document->load_all_attachments($milestone_id);
        $response["html"] = $this->load->view("contracts/view/milestone_documents", $data, true);
        $this->output->set_content_type("application/json")->set_output(json_encode($response));
    }
    public function delete_document_milestone(){
        $response["status"] = false;
        $this->load->model("contract_milestone_document", "contract_milestone_documentfactory");
        $this->contract_milestone_document = $this->contract_milestone_documentfactory->get_instance();
        if($parent_id = $this->contract_milestone_document->delete_document($this->input->post("document_id"))){
            $this->delete_document();
            if(!$this->document_management_system->fetch(["parent"=>$parent_id])){
                $this->dmsnew->delete_document("contract", $parent_id, ($this->input->post("newest_version") == "true"));
            }
            $response["status"] = true;
        }
        $this->output->set_content_type("application/json")->set_output(json_encode($response));
    }
    public function delete_milestone(){
        $this->load->model("milestone", "milestonefactory");
        $this->milestone = $this->milestonefactory->get_instance();
        $response["status"] = $this->milestone->delete_milestone_documents($this->input->post("milestone_id"));
        $this->reminder->dismiss_related_reminders_by_related_object_ids($this->input->post("milestone_id"), "related_id");
        $this->output->set_content_type("application/json")->set_output(json_encode($response));
    }
    private function load_milestone_data($milestone_id = 0){
        $this->load->model("milestone", "milestonefactory");
        $this->milestone = $this->milestonefactory->get_instance();
        $this->milestone->load_milestone_data($milestone_id);
        return $this->milestone->load_milestone_data($milestone_id);
    }
    private function notify_me_before_due_date($milestone_id, $contract_id){
        $notify_before = $this->input->post("notify_me_before");
        $due_date = $this->input->post("due_date");
        $title = $this->input->post("title");
        $current_reminder = $this->reminder->load_notify_before_data_to_related_object($milestone_id, $this->milestone->get("_table"));
        if(($current_reminder && !$notify_before)){
            return $this->reminder->remind_before_due_date([], $current_reminder["id"]);
        }
        if(($notify_before && $due_date)){
            $reminder = ["user_id"=>$this->is_auth->get_user_id(),"remindDate"=>$due_date,"contract_id"=>$contract_id,"related_id"=>$milestone_id,"related_object"=>$this->milestone->get("_table"),"notify_before_time"=>$notify_before["time"],"notify_before_time_type"=>$notify_before["time_type"],"notify_before_type"=>$notify_before["type"]];
            $reminder["summary"] = sprintf($this->lang->line("notify_me_before_message"), $this->lang->line("milestone"), $title . " " . $this->lang->line("related_to") . " " . $this->contract->get("modelCode") . $contract_id, $due_date);
            return $this->reminder->remind_before_due_date($reminder, (isset($current_reminder["id"]) ? $current_reminder["id"] : null));
        }
        return false;
    }
    public function change_milestone_status(){
        $this->load->model("milestone", "milestonefactory");
        $this->milestone = $this->milestonefactory->get_instance();
        $milestone_id = $this->input->post("milestone_id");
        $status = $this->input->post("status");
        $response = $this->milestone->change_status($milestone_id, $status);
        $this->output->set_content_type("application/json")->set_output(json_encode($response));
    }
    public function change_milestone_financial_status(){
        $this->load->model("milestone", "milestonefactory");
        $this->milestone = $this->milestonefactory->get_instance();
        $milestone_id = $this->input->post("milestone_id");
        $status = $this->input->post("financial_status");
        $response = $this->milestone->change_financial_status($milestone_id, $status);
        $this->output->set_content_type("application/json")->set_output(json_encode($response));
    }
    public function preview_document($id = 0){
        $response = [];
        if((0 < $id)){
            echo $this->dmsnew->get_preview_document_content($id);
            exit;
        }else{
            $id = $this->input->post("id");
            if(!empty($id)){
                $response["document"] = $this->dmsnew->get_document_details(["id"=>$id]);
                $response["document"]["url"] = app_url("modules/contract/contracts/preview_document/" . $id);
            }
            $response["html"] = $this->load->view("documents_management_system/view_document", ["mode"=>"preview"], true);
            $this->output->set_content_type("application/json")->set_output(json_encode($response));
        }
    }

    public function show_hide_milestone_cp(){
        if(!$this->input->is_ajax_request()){
            show_404();
        }
        $this->load->model("milestone", "milestonefactory");
        $this->milestone = $this->milestonefactory->get_instance();
        $post_data = $this->input->post(null);
        $response["result"] = false;
        if($this->contract->fetch($post_data["id"])){
            $this->contract->set_field("milestone_visible_to_cp", $post_data["flag"]);
            $milestone_parent_folder_array = $this->dmsnew->get_document_details(["name"=>"Milestones_attachments","module"=>"contract","module_record_id"=>$post_data["id"]]);
            if(is_array($milestone_parent_folder_array)){
                $milestone_parent_folder = $milestone_parent_folder_array["id"];
                $this->dmsnew->update_show_hide_in_cp($milestone_parent_folder, $post_data["flag"]);
            }
            if(!$this->contract->update()){
                $response["display_message"] = $this->lang->line("updates_failed");
            }else{
                $response["result"] = true;
            }
        }else{
            $response["display_message"] = $this->lang->line("invalid_record");
        }
        $this->output->set_content_type("application/json")->set_output(json_encode($response));
    }
   

}

