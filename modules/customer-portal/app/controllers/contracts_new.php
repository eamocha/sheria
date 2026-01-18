<?php
require "Auth_controller.php";
class Contracts extends Auth_Controller
{
    public $licenses_validity = "";
    public $license_type = "";
    public function __construct()
    {
        parent::__construct();
        $this->load->model("party_category_language", "party_category_languagefactory");
        $this->party_category_language = $this->party_category_languagefactory->get_instance();
        $this->pageTitle = sprintf($this->lang->line("app4legal_browser_title"), $this->lang->line("customer_portal"));
        $this->load->model("customer_portal_users", "customer_portal_usersfactory");
        $this->customer_portal_users = $this->customer_portal_usersfactory->get_instance();
        $this->load->model("contract_cp_screen", "contract_cp_screenfactory");
        $this->contract_cp_screen = $this->contract_cp_screenfactory->get_instance();
        $this->load->model("contract_approval_submission", "contract_approval_submissionfactory");
        $this->contract_approval_submission = $this->contract_approval_submissionfactory->get_instance();
        $this->load->model("contract_signature_submission", "contract_signature_submissionfactory");
        $this->contract_signature_submission = $this->contract_signature_submissionfactory->get_instance();
        $this->load->model("contract_party", "contract_partyfactory");
        $this->contract_party = $this->contract_partyfactory->get_instance();
        $this->load->model("contract", "contractfactory");
        $this->contract = $this->contractfactory->get_instance();
        $this->load->model("contract_type_language", "contract_type_languagefactory");
        $this->contract_type_language = $this->contract_type_languagefactory->get_instance();
        $this->load->model("contract_contributor", "contract_contributorfactory");
        $this->contract_contributor = $this->contract_contributorfactory->get_instance();
        $this->load->model("contract_status");
        $this->load->library("dms", ["channel" => $this->cp_channel, "user_id" => $this->ci->session->userdata("CP_user_id")]);
        $this->licenses_validity = $this->session->userdata("licenses_validity");
        $this->license_type = $this->session->userdata("license_type");
        if (!$this->licenses_validity["collaborator"] && $this->license_type !== "client") {
            $this->setPinesMessage("error", $this->lang->line("invalid_license"));
            redirect("home");
        }
    }
    public function index()
    {
        $data = [];
        $logged_in_user = $this->ci->session->userdata("CP_user_id");
        $all_contracts = $this->contract_cp_screen->load_cp_contracts($logged_in_user);
        $awaiting_approvals = $this->contract_approval_submission->load_cp_awaiting_approvals($logged_in_user);
        $awaiting_signatures = $this->contract_signature_submission->load_cp_awaiting_signatures($logged_in_user);
        $data["count"] = ["all_contracts" => count($all_contracts), "awaiting_approvals" => count($awaiting_approvals), "awaiting_signatures" => count($awaiting_signatures)];
        $this->includes("customerPortal/clientPortal/js/contracts", "js");
        $this->includes("contract/cp_contract_common", "js");
        $this->load->view("partial/header");
        $this->load->view("contracts/index", $data);
        $this->load->view("partial/footer");
    }
    public function all_contracts()
    {
        $data = [];
        $logged_in_user = $this->ci->session->userdata("CP_user_id");
        $data["contracts"] = $this->contract_cp_screen->load_cp_contracts($logged_in_user);
        $data["model_code"] = $this->contract->get("modelCode");
        $this->includes("customerPortal/clientPortal/js/contracts", "js");
        $this->load->view("partial/header");
        $this->load->view("contracts/list", $data);
        $this->load->view("partial/footer");
    }
    public function view($contract_id)
    {
        if (!$contract_id || !ctype_digit($contract_id) || !$this->contract->fetch($contract_id)) {
            $this->setPinesMessage("error", $this->lang->line("invalid_record"));
            redirect("contracts");
        }
        $this->load->model("customer_portal_contract_permission");
        $this->contract->fetch($contract_id);
        $status = $this->contract->get_field("status_id");
        $this->contract_status->fetch($status);
        $data["lang"] = 1;
        $data["permissions"] = $this->customer_portal_contract_permission->load_data($this->contract->get_field("workflow_id"), $data["lang"]);
        $data["contract_data"] = $this->contract_cp_screen->load_cp_contract_data($contract_id);
        $data["parties"] = $this->contract_party->fetch_contract_parties_data($contract_id);
        $user_container_permission = $this->customer_portal_users->get_user_contract_permission($contract_id, $this->ci->session->userdata("CP_user_id"));
        switch ($user_container_permission) {
            case "none":
                $this->setPinesMessage("error", $this->lang->line("invalid_record"));
                redirect("contracts");
                break;
            case "read":
                $data["allowed_to_modify"] = false;
                break;
            case "read_write":
                $allowed_permissions = [];
                foreach ($data["permissions"] as $permissions) {
                    if ($data["contract_data"]["status_id"] == $permissions["fromStepId"]) {
                        $allowed_permissions[] = $permissions;
                    }
                }
                $data["allowedPermissions"] = $allowed_permissions;
                $data["allowed_to_modify"] = true;
                break;
            default:
        }
        $data["contract"]["id"] = $contract_id;
        $this->load->model("document_management_system", "document_management_systemfactory");
        $this->document_management_system = $this->document_management_systemfactory->get_instance();
        $data["related_documents_count"] = $this->document_management_system->count_contract_related_documents($contract_id, true);
        $this->contract_approval_submission->fetch(["contract_id" => $contract_id]);
        $data["overall_approval_status"] = $this->contract_approval_submission->get_field("status");
        $this->contract_signature_submission->fetch(["contract_id" => $contract_id]);
        $data["overall_signature_status"] = $this->contract_signature_submission->get_field("status");
        $this->load->helper("revert_comment_html");
        $this->includes("jquery/tinymce/tinymce.min", "js");
        $this->includes("jquery/jquery.scrollTo.min", "js");
        $this->includes("customerPortal/clientPortal/js/contract_view", "js");
        $this->includes("contract/cp_contract_common", "js");
        $this->includes("contract/related_documents", "js");
        $this->includes("kendoui/js/kendo.web.min", "js");
        $this->includes("jquery/dropzone", "js");
        $this->includes("jquery/css/dropzone", "css");
        $this->includes("jquery/jquery.shiftcheckbox", "js");
        $this->includes("kendoui/styles/kendo.common-bootstrap.min", "css");
        $this->includes("kendoui/styles/kendo.bootstrap.min", "css");
        $this->includes("styles/contract/main", "css");
        $this->load->view("partial/header");
        $this->load->view("contracts/view", $data);
        $this->load->view("partial/footer");

    }
    public function load_documents()
    {
        $response = $this->dms->load_documents(["module" => $this->input->post("module"), "module_record_id" => $this->input->post("module_record_id"), "term" => $this->input->post("term"), "visible_in_cp" => 1, "lineage" => $this->input->post("lineage")]);
        if ($this->input->is_ajax_request()) {
            $this->output->set_content_type("application/json");
            $this->output->set_output(json_encode($response));
        } else {
            return $response;
        }
    }
    public function update_contract_watchers()
    {
        $this->load->model("customer_portal_contract_watcher", "customer_portal_contract_watcherfactory");
        $this->customer_portal_contract_watcher = $this->customer_portal_contract_watcherfactory->get_instance();
        $watchers = $this->input->post("watchers") ? $this->input->post("watchers") : NULL;
        $response["status"] = $this->customer_portal_contract_watcher->add_watchers_to_contract($watchers, $this->input->post("contract_id"));
        $response["user_contract_permission"] = $this->customer_portal_users->get_user_contract_permission($this->input->post("contract_id"), $this->session->userdata("CP_user_id"));
        $response["message"] = $this->handle_auto_update_response_message($response["status"], $response["user_contract_permission"]);
        $this->output->set_content_type("application/json")->set_output(json_encode($response));
    }
    private function handle_auto_update_response_message($response_status, $user_permission)
    {
        $message = NULL;
        if ($response_status && $user_permission == "none") {
            $message = ["type" => "warning", "text" => $this->lang->line("access_to_contract_lost")];
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
    public function add_comment()
    {
        if (!$this->input->is_ajax_request()) {
            show_404();
        }
        $this->load->model("contract_comment", "contract_commentfactory");
        $this->contract_comment = $this->contract_commentfactory->get_instance();
        $this->load->helper("revert_comment_html");
        $this->load->helper("format_comment_patterns");
        if ($this->input->get(NULL)) {
            $data = [];
            $data["comment"] = $this->contract_comment->get_fields();
            $data["comment"]["contract_id"] = $this->input->get("contract_id");
            $data["title"] = $this->lang->line("add_comments");
            $response["html"] = $this->load->view("contracts/comment_form", $data, true);
        }
        if ($this->input->post(NULL)) {
            $comment = $this->input->post("comment", true, "<h1><h2><h3><h4><h5><h6><strong><em><u><a><p><img>");
            $_POST["comment"] = format_comment_patterns($this->regenerate_note($comment));
            $_POST["edited"] = 0;
            $logged_user = $this->ci->session->userdata("CP_user_id");
            $this->contract_comment->set_fields($this->input->post(NULL));
            $this->contract_comment->set_field("comment", $this->input->post("comment", true, "<h1><h2><h3><h4><h5><h6><strong><em><u><a><p><img>"));
            $this->contract_comment->set_field("createdBy", $logged_user);
            $this->contract_comment->set_field("createdOn", date("Y-m-d H:i:s"));
            $this->contract_comment->set_field("modifiedOn", date("Y-m-d H:i:s"));
            $this->contract_comment->set_field("modifiedBy", $logged_user);
            $this->contract_comment->set_field("channel", $this->cp_channel);
            $this->contract_comment->set_field("modifiedByChannel", $this->cp_channel);
            $this->contract_comment->set_field("edited", "0");
            $this->contract_comment->set_field("visible_to_cp", "1");
            if ($this->contract_comment->insert()) {
                $contract_id = $this->input->post("contract_id");
                $response["result"] = true;
                $contract_comment = $this->contract_comment->get_field("comment");
                $parent_folder_name = $this->contract_comment->get_field("createdOn");
                $this->move_contract_attachments_to_parent_folder($contract_comment, $parent_folder_name, $contract_id);
            } else {
                $response["validation_errors"] = $this->contract_comment->get("validationErrors");
            }
        }
        $this->output->set_content_type("application/json")->set_output(json_encode($response));
    }
    private function load_documents_form_data($contract_id, $lineage)
    {
        $this->load->model("contract_document_status_language", "contract_document_status_languagefactory");
        $this->contract_document_status_language = $this->contract_document_status_languagefactory->get_instance();
        $this->load->model("contract_document_type_language", "contract_document_type_languagefactory");
        $this->contract_document_type_language = $this->contract_document_type_languagefactory->get_instance();
        $data["document_statuses"] = $this->contract_document_status_language->load_list_per_language();
        $data["document_types"] = $this->contract_document_type_language->load_list_per_language();
        $data["module_record"] = "contract";
        $data["module_record_id"] = $contract_id;
        return $data;
    }
    public function upload_file()
    {
        if ($this->input->get(NULL, true)) {
            $contract_id = $this->input->get("contract_id", true);
            $data = $this->load_documents_form_data($contract_id, $this->input->get("lineage", true));
            $data["title"] = $this->lang->line("upload_file");
            $data["module"] = "customer-portal";
            $response["result"] = true;
            $response["html"] = $this->load->view("documents_management_system/upload_form", $data, true);
        }
        if ($this->input->post(NULL, true)) {
            if (!$_FILES["uploadDoc"]["name"]) {
                $response["status"] = false;
                $response["validation_errors"]["uploadDoc"] = $this->lang->line("file_required");
            } else {
                $response = $this->dms->upload_file(["module" => "contract", "module_record_id" => $this->input->post("module_record_id"), "lineage" => $this->input->post("lineage"), "upload_key" => "uploadDoc", "visible_in_cp" => 1, "visible_in_ap" => 0]);
                $this->load->model("document_management_system", "document_management_systemfactory");
                $this->document_management_system = $this->document_management_systemfactory->get_instance();
                $response["related_documents_count"] = $this->document_management_system->count_contract_related_documents($this->input->post("module_record_id"), true);
            }
        }
        $this->output->set_content_type("application/json")->set_output(json_encode($response));
    }
    public function upload_signed_document()
    {
        $this->load->model("contract_signature_status", "contract_signature_statusfactory");
        $this->contract_signature_status = $this->contract_signature_statusfactory->get_instance();
        if ($this->input->get(NULL, true)) {
            $contract_id = $this->input->get("contract_id", true);
            $data = $this->load_documents_form_data($contract_id, "");
            $data["module"] = "customer-portal";
            $data["title"] = $this->lang->line("upload_signed_document");
            $response["result"] = false;
            if ($this->input->get("contract_signature_status_id")) {
                $data["contract_signature_status_id"] = $this->input->get("contract_signature_status_id");
                if ($this->contract_signature_status->fetch($data["contract_signature_status_id"])) {
                    $signees = $this->contract_signature_status->load_signature_data($data["contract_signature_status_id"]);
                    $allowed = false;
                    if ($signees["collaborators"] && in_array($this->ci->session->userdata("CP_user_id"), explode(",", $signees["collaborators"]))) {
                        $allowed = true;
                    }
                    if (!$allowed) {
                        $response["message"] = $this->lang->line("no_permission_to_sign");
                    } else {
                        $response["result"] = true;
                        $response["html"] = $this->load->view("documents_management_system/upload_form", $data, true);
                    }
                } else {
                    $response["message"] = $this->lang->line("invalid_record");
                }
            }
        }
        if ($this->input->post(NULL, true)) {
            if (!$_FILES["uploadDoc"]["name"]) {
                $response["status"] = false;
                $response["validation_errors"]["uploadDoc"] = $this->lang->line("file_required");
            } else {
                $response = $this->dms->upload_file(["module" => "contract", "module_record_id" => $this->input->post("module_record_id"), "lineage" => $this->input->post("lineage"), "upload_key" => "uploadDoc", "document_type_id" => $this->input->post("document_type_id"), "document_status_id" => $this->input->post("document_status_id"), "comment" => $this->input->post("comment")]);
                $this->load->model("document_management_system", "document_management_systemfactory");
                $this->document_management_system = $this->document_management_systemfactory->get_instance();
                $response["related_documents_count"] = $this->document_management_system->count_contract_related_documents($this->input->post("module_record_id"));
                $response["module_record_id"] = $this->input->post("module_record_id");
                if ($response["status"] && $this->input->post("contract_signature_status_id")) {
                    if ($this->contract_signature_status->fetch($this->input->post("contract_signature_status_id"))) {
                        $this->update_contract_signature_status($this->input->post("module_record_id"));
                        $this->contract_signature_submission->fetch(["contract_id" => $this->input->post("module_record_id")]);
                        $response["overall_status"] = $this->contract_signature_submission->get_field("status");
                        $response["message"] = $this->lang->line("updates_saved_successfully");
                    } else {
                        $response["message"] = $this->lang->line("invalid_record");
                    }
                }
            }
        }
        $this->output->set_content_type("application/json")->set_output(json_encode($response));
    }
    public function return_doc_thumbnail($id = 0, $name = 0)
    {
        if ($id) {
            $this->load->library("dms");
            $response = $this->dms->get_file_download_data("contract", $id);
            $content = $response["data"]["file_content"];
            if ($content) {
                $this->load->helper("download");
                force_download($name ? $name : $id, $content);
            }
        }
    }
    public function download_file($file_id, $newest_version = false)
    {
        $newest_version = $newest_version == "true" ? true : false;
        $response = $this->dms->download_file("contract", $file_id, $newest_version);
        if (!$response["status"]) {
            $this->setPinesMessage("error", $response["message"]);
            redirect($this->agent->referrer());
        }
    }
    public function load_summary()
    {
        if (!$this->input->is_ajax_request()) {
            redirect("index");
        }
        $response["result"] = false;
        $type = $this->input->get("type") ?: $this->input->post("type");
        $model = "contract_" . $type . "_status";
        $model_factory = $model . "factory";
        $this->load->model($model, $model_factory);
        $this->{$model} = $this->{$model_factory}->get_instance();
        if ($this->input->get("approval_signature_status_id") && $this->{$model}->fetch($this->input->get("approval_signature_status_id"))) {
            $data["data"] = $this->{$model}->get_fields();
            $response["result"] = true;
            $data["module"] = "client-portal";
            $data["type"] = $type;
            $response["html"] = $this->load->view("contracts/summary_form", $data, true);
        }
        if ($this->input->post("approval_signature_status_id") && $this->{$model}->fetch($this->input->post("approval_signature_status_id"))) {
            if (!$this->input->post("summary")) {
                $response["validation_errors"]["summary"] = $this->lang->line("cannot_be_blank_rule");
            } else {
                $this->{$model}->set_field("summary", $this->input->post("summary"));
                if ($this->{$model}->update()) {
                    $response["result"] = true;
                } else {
                    $response["validation_errors"] = $this->contract->get("validationErrors");
                }
            }
        }
        $this->output->set_content_type("application/json")->set_output(json_encode($response));
    }
    public function load_document_variables()
    {
        $response["result"] = false;
        $get_data = $this->input->get(NULL, true);
        if ($this->input->get("document_id", true)) {
            $this->document_management_system->fetch(["id" => $get_data["document_id"]]);
            $doc_details = $this->document_management_system->get_fields();
            $this->document_management_system->reset_fields();
            $core_path = substr(COREPATH, 0, -12);
            $documents_root_direcotry = $this->config->item("files_path") . DIRECTORY_SEPARATOR . "attachments" . DIRECTORY_SEPARATOR;
            $tmp_file = $this->config->item("files_path") . DIRECTORY_SEPARATOR . "tmp" . DIRECTORY_SEPARATOR . "contracts" . DIRECTORY_SEPARATOR . rand(1000, 9999) . $doc_details["name"] . "." . $doc_details["extension"];
            $this->document_management_system->fetch($doc_details["parent"]);
            $lineage = $this->document_management_system->get_field("lineage");
            $template_dir = $documents_root_direcotry . "contracts" . $lineage;
            if (is_file($template_dir . DIRECTORY_SEPARATOR . $get_data["document_id"])) {
                copy($template_dir . DIRECTORY_SEPARATOR . $get_data["document_id"], $tmp_file);
                require_once $core_path . "/application/libraries/phpdocx-advanced/classes/CreateDocx.php";
                $docx = new CreateDocxFromTemplate($tmp_file);
                $docx->setTemplateSymbol("%%");
                $template_variables = $docx->getTemplateVariables();
                $variables = array_filter($template_variables);
                if ($variables) {
                    $data["variables"] = array_unique($variables["document"]);
                    $response["result"] = true;
                    $response["html"] = $this->load->view("contracts/signature_center/variables", $data, true);
                } else {
                    $response["display_message"] = $this->lang->line("no_variable_to_replace");
                }
                unlink($tmp_file);
            }
        }
        $this->output->set_content_type("application/json")->set_output(json_encode($response));
    }
    public function list_contract_docs($contract_id)
    {
        if (!$this->input->is_ajax_request()) {
            redirect("index");
        }
        $data["title"] = $this->input->get("type") == "signature" ? $this->lang->line("contract_to_sign") : $this->lang->line("needs_approval");
        $data["docs"] = $this->contract->load_all_contract_docs($contract_id);
        if (empty($data["docs"])) {
            $response["result"] = false;
            $response["display_message"] = $this->lang->line("contract_unavailable");
        } else {
            $response["result"] = true;
            $data["module"] = "customer-portal";
            $response["html"] = $this->load->view("contracts/contract_list", $data, true);
        }
        $this->output->set_content_type("application/json")->set_output(json_encode($response));
    }
    public function submit_for_approval()
    {
        if (!$this->input->is_ajax_request()) {
            show_404();
        }
        $this->load->model("contract_approval_history", "contract_approval_historyfactory");
        $this->contract_approval_history = $this->contract_approval_historyfactory->get_instance();
        $this->load->model("contract_approval_status", "contract_approval_statusfactory");
        $this->contract_approval_status = $this->contract_approval_statusfactory->get_instance();
        $this->load->model("contract_approval_submission", "contract_approval_submissionfactory");
        $this->contract_approval_submission = $this->contract_approval_submissionfactory->get_instance();
        $response["result"] = false;
        $response["validation_errors"] = [];
        $post_data = $this->input->post(NULL);
        if (!empty($post_data)) {
            $this->contract->fetch($post_data["contract_id"]);
            if ($this->contract_approval_status->fetch($post_data["contract_approval_status_id"])) {
                $old_status = $this->contract_approval_status->get_field("status");
                if ($post_data["status"] === "rejected") {
                    $fields_validation = $this->contract_approval_history->get("validate");
                    $fields_validation["comment"] = ["required" => ["required" => true, "allowEmpty" => false, "rule" => ["minLength", 1], "message" => $this->lang->line("cannot_be_blank_rule")]];
                    $this->contract_approval_history->set("validate", $fields_validation);
                    if (!empty($post_data["documents"]["id"]) && empty($post_data["documents"]["status_id"])) {
                        $response["validation_errors"]["status_id"] = $this->lang->line("cannot_be_blank_rule");
                    }
                    $this->contract->send_notifications("contract_rejected", ["contributors" => [], "logged_in_user" => $this->ci->session->userdata("CP_profileName")], ["id" => $post_data["contract_id"]]);
                }
                $this->contract_approval_history->set_fields($post_data);
                $this->contract_approval_history->set_field("done_on", $post_data["done_on"] . " " . date("H:i:s"));
                $this->contract_approval_history->set_field("done_by", $this->ci->session->userdata("CP_user_id"));
                $this->contract_approval_history->set_field("done_by_type", "collaborator");
                $this->contract_approval_history->set_field("from_action", $old_status);
                $this->contract_approval_history->set_field("to_action", $post_data["status"]);
                $this->contract_approval_history->set_field("label", $this->contract_approval_status->get_field("label"));
                $this->contract_approval_history->set_field("action", "approve");
                $this->contract_approval_history->set_field("done_by_ip", $this->input->ip_address());
                $this->contract_approval_history->set_field("approval_channel", "CP");
                if ($this->contract_approval_history->validate() && empty($response["validation_errors"])) {
                    $this->contract_approval_history->insert();
                    $this->contract_approval_status->set_fields($post_data);
                    $this->contract_approval_status->update();
                    $contract_approval_status_id = $this->contract_approval_status->get_field("id");
                    $order = $this->contract_approval_status->get_field("rank");
                    if ($post_data["enforce_previous_approvals"]) {
                        $this->contract_approval_status->enforce_previous_approvals($post_data["contract_id"], $contract_approval_status_id, $post_data["enforce_previous_approvals"], $this->contract_approval_status->get_field("rank"));
                    } else {
                        if ($post_data["status"] === "approved" && $this->contract_approval_submission->fetch(["contract_id" => $post_data["contract_id"]])) {
                            if (!$this->contract_approval_status->load_pending_approvals($post_data["contract_id"])) {
                                $this->contract_approval_submission->set_field("status", "approved");
                                if ($this->contract_approval_submission->update() && $this->contract_signature_submission->fetch(["contract_id" => $post_data["contract_id"]])) {
                                    $this->contract_signature_submission->set_field("status", "awaiting_signature");
                                    $this->contract_signature_submission->update();
                                    $this->load->model("contract_signature_status", "contract_signature_statusfactory");
                                    $this->contract_signature_status = $this->contract_signature_statusfactory->get_instance();
                                    $next_signees = $this->contract_signature_status->load_next_order(0, $post_data["contract_id"]);
                                    if ($next_signees && !empty($next_signees)) {
                                        $signees["users"] = [];
                                        $signees["collaborators"] = [];
                                        $signees["user_groups"] = [];
                                        foreach ($next_signees as $next) {
                                            $signature_data = $this->contract_signature_status->load_signature_data($next["id"]);
                                            $signature_data["users"] != "" ? $signees["users"] : "";
                                            $signature_data["collaborators"] != "" ? $signees["collaborators"] : "";
                                            $signature_data["user_groups"] != "" ? $signees["user_groups"] : "";
                                        }
                                        $this->load->model("signature", "signaturefactory");
                                        $this->signature = $this->signaturefactory->get_instance();
                                        $this->signature->notify_required_signees($signees, $post_data["contract_id"]);
                                        $contributors = $this->contract_contributor->load_contributors($post_data["contract_id"]);
                                        $notify["contributors"] = $contributors ? array_column($contributors, "id") : [];
                                        $notify["logged_in_user"] = $this->ci->session->userdata("CP_profileName");
                                        $this->contract->send_notifications("contract_approved", $notify, ["id" => $post_data["contract_id"]]);
                                    }
                                }
                            } else {
                                if ($old_status === "rejected") {
                                    $this->contract_approval_submission->set_field("status", "awaiting_approval");
                                    $this->contract_approval_submission->update();
                                }
                                $this->load->model("approval", "approvalfactory");
                                $this->approval = $this->approvalfactory->get_instance();
                                $response["email_sent"] = $this->approval->notify_next_approvers($order, $post_data["contract_id"]);
                            }
                        }
                    }
                    if ($post_data["status"] === "rejected" && $this->contract_approval_submission->fetch(["contract_id" => $post_data["contract_id"]])) {
                        $this->contract_approval_submission->set_field("status", "awaiting_revision");
                        $this->contract_approval_submission->update();
                    }
                    $response["overall_status"] = $this->contract_approval_submission->get_field("status");
                    $response["result"] = true;
                } else {
                    $response["result"] = false;
                    $response["validation_errors"] = array_merge($response["validation_errors"], $this->contract_approval_history->get("validationErrors"));
                }
            }
        } else {
            $get_data = $this->input->get(NULL);
            $data = [];
            if (isset($get_data["contract_approval_status_id"]) && $this->contract_approval_status->fetch($get_data["contract_approval_status_id"])) {
                $contract_id = $this->contract_approval_status->get_field("contract_id");
                $rank = $this->contract_approval_status->get_field("rank");
                $allowed = false;
                $assignees = $this->contract_approval_status->load_approval_data($get_data["contract_approval_status_id"]);
                if ($assignees["bm_collaborators"] && in_array($this->ci->session->userdata("CP_user_id"), explode(",", $assignees["bm_collaborators"]))) {
                    $allowed = true;
                }
                if ($assignees["sh_collaborators"] && in_array($this->ci->session->userdata("CP_user_id"), explode(",", $assignees["sh_collaborators"]))) {
                    $allowed = true;
                }
                if ($assignees["collaborators"] && in_array($this->ci->session->userdata("CP_user_id"), explode(",", $assignees["collaborators"]))) {
                    $allowed = true;
                }
                if (!$allowed) {
                    $response["display_message"] = $get_data["approved"] == "true" ? $this->lang->line("no_permission_to_approve") : $this->lang->line("no_permission_to_reject");
                } else {
                    $approve = $get_data["approved"] == "true";
                    $data["title"] = $approve ? $this->lang->line("approve") : $this->lang->line("reject");
                    $data["approve"] = $approve ? true : false;
                    $data["today"] = date("Y-m-d", time());
                    $data["previous_ranks"] = ["" => ""] + $this->contract_approval_status->load_previous_ranks($contract_id, $rank);
                    if ($approve) {
                        $this->load->model("cp_user_signature_attachment", "cp_user_signature_attachmentfactory");
                        $this->cp_user_signature_attachment = $this->cp_user_signature_attachmentfactory->get_instance();
                        $data["signatures"] = $this->cp_user_signature_attachment->load_all(["where" => ["user_id", $this->ci->session->userdata("CP_user_id")], "order_by" => ["is_default", "DESC"]]);
                        $data["signature_path"] = "modules/customer-portal/contracts/get_signature_picture/";
                    }
                    $response["result"] = true;
                    $response["html"] = $this->load->view("contracts/approval_center/form", $data, true);
                }
            }
        }
        $this->output->set_content_type("application/json")->set_output(json_encode($response));
    }
    public function sign_contract_doc()
    {
        if (!$this->input->is_ajax_request()) {
            redirect("index");
        }
        $response["result"] = false;
        $this->load->model("contract_signature_status", "contract_signature_statusfactory");
        $this->contract_signature_status = $this->contract_signature_statusfactory->get_instance();
        if ($this->input->get(NULL, true)) {
            $get_data = $this->input->get(NULL, true);
            $contract_id = $get_data["contract_id"];
            if (isset($get_data["contract_signature_status_id"]) && $this->contract_signature_status->fetch($get_data["contract_signature_status_id"])) {
                $signees = $this->contract_signature_status->load_signature_data($get_data["contract_signature_status_id"]);
                $allowed = false;
                if ($signees["bm_collaborators"] && in_array($this->ci->session->userdata("CP_user_id"), explode(",", $signees["bm_collaborators"]))) {
                    $allowed = true;
                }
                if ($signees["sh_collaborators"] && in_array($this->ci->session->userdata("CP_user_id"), explode(",", $signees["sh_collaborators"]))) {
                    $allowed = true;
                }
                if ($signees["collaborators"] && in_array($this->ci->session->userdata("CP_user_id"), explode(",", $signees["collaborators"]))) {
                    $allowed = true;
                }
                if (!$allowed) {
                    $response["display_message"] = $this->lang->line("no_permission_to_sign");
                } else {
                    $data["docs"] = $this->contract->load_approval_signature_documents($contract_id, "docx");
                    if (empty($data["docs"])) {
                        $response["display_message"] = $this->lang->line("no_related_contracts");
                    } else {
                        $data["title"] = $this->lang->line("contract_to_sign");
                        $data["contract_id"] = $contract_id;
                        $data["id"] = $this->input->get("contract_signature_status_id", true);
                        $this->load->model("cp_user_signature_attachment", "cp_user_signature_attachmentfactory");
                        $this->cp_user_signature_attachment = $this->cp_user_signature_attachmentfactory->get_instance();
                        $data["signatures"] = $this->cp_user_signature_attachment->load_all(["where" => ["user_id", $this->ci->session->userdata("CP_user_id")]]);
                        $response["result"] = true;
                        $response["html"] = $this->load->view("contracts/signature_center/contract_list", $data, true);
                    }
                }
            }
        } else {
            $post_data = $this->input->post(NULL, true);
            if ($this->input->post("document_id", true)) {
                $this->document_management_system->fetch(["id" => $post_data["document_id"]]);
                $doc_details = $this->document_management_system->get_fields();
                $this->document_management_system->reset_fields();
                $core_path = substr(COREPATH, 0, -12);
                $documents_root_direcotry = $this->config->item("files_path") . DIRECTORY_SEPARATOR . "attachments" . DIRECTORY_SEPARATOR;
                $tmp_file = $this->config->item("files_path") . DIRECTORY_SEPARATOR . "tmp" . DIRECTORY_SEPARATOR . rand(1000, 9999) . $doc_details["name"] . "." . $doc_details["extension"];
                $this->document_management_system->fetch($doc_details["parent"]);
                $lineage = $this->document_management_system->get_field("lineage");
                $template_dir = $documents_root_direcotry . "contracts" . $lineage;
                if (is_file($template_dir . DIRECTORY_SEPARATOR . $post_data["document_id"])) {
                    copy($template_dir . DIRECTORY_SEPARATOR . $post_data["document_id"], $tmp_file);
                    require_once $core_path . "/application/libraries/phpdocx-advanced/classes/CreateDocx.php";
                    $docx = new CreateDocxFromTemplate($tmp_file);
                    $docx->setTemplateSymbol("%%");
                    $template_variables = $docx->getTemplateVariables();
                    $template_variables = array_filter($template_variables);
                    if (empty($template_variables)) {
                        $response["display_message"] = $this->lang->line("no_variable_to_replace");
                    } else {
                        if (isset($post_data["id"]) && $this->contract_signature_status->fetch($post_data["id"])) {
                            if (!isset($post_data["signature_id"]) || !isset($post_data["variable_name"])) {
                                $response["display_message"] = $this->lang->line("no_signature_variable");
                            } else {
                                $this->load->model("cp_user_signature_attachment", "cp_user_signature_attachmentfactory");
                                $this->cp_user_signature_attachment = $this->cp_user_signature_attachmentfactory->get_instance();
                                $this->cp_user_signature_attachment->fetch($post_data["signature_id"]);
                                $signature_variable = $post_data["variable_name"];
                                $user_signature = $this->cp_user_signature_attachment->get_field("signature");
                                $signature_picture = $this->config->item("files_path") . DIRECTORY_SEPARATOR . "attachments" . DIRECTORY_SEPARATOR . "cp_users" . DIRECTORY_SEPARATOR . $this->ci->session->userdata("CP_user_id") . DIRECTORY_SEPARATOR . "signature" . DIRECTORY_SEPARATOR . $user_signature;
                                if ($user_signature && file_exists($signature_picture)) {
                                    $wf = new WordFragment($docx, "document");
                                    $wf->addImage(["src" => $signature_picture, "width" => 150, "height" => 40]);
                                    $docx->replaceVariableByWordFragment([$signature_variable => $wf], ["type" => "inline"]);
                                    $file_path = $template_dir . DIRECTORY_SEPARATOR . rand(1000, 9999);
                                    $docx->createDocx($file_path);
                                    require_once $core_path . "/application/libraries/phpdocx-premium-12.5-ns/Classes/Phpdocx/Create/CreateDocx.php";
                                    $docx = new Phpdocx\Create\CreateDocx();
                                    $doc_details["extension"] = "pdf";
                                    $docx->transformDocument($file_path . ".docx", $file_path . "." . $doc_details["extension"], "libreoffice");
                                    $file_existant_version = $this->document_management_system->get_document_existant_version($doc_details["name"] . "." . $doc_details["extension"], "file", $lineage);
                                    $this->document_management_system->reset_fields();
                                    $this->document_management_system->set_fields(["type" => "file", "name" => $doc_details["name"], "extension" => $doc_details["extension"], "size" => filesize($file_path . "." . $doc_details["extension"]), "parent" => $doc_details["parent"], "version" => empty($file_existant_version) ? 1 : $file_existant_version["version"] + 1, "document_type_id" => NULL, "document_status_id" => NULL, "comment" => NULL, "module" => $doc_details["module"], "module_record_id" => $doc_details["module_record_id"], "system_document" => 0, "visible" => 1, "visible_in_cp" => 1, "visible_in_ap" => 0, "createdOn" => date("Y-m-d H:i:s"), "createdBy" => $this->ci->session->userdata("CP_user_id"), "createdByChannel" => "CP", "modifiedOn" => date("Y-m-d H:i:s"), "modifiedBy" => $this->ci->session->userdata("CP_user_id"), "modifiedByChannel" => "CP"]);
                                    if ($this->document_management_system->insert()) {
                                        $this->document_management_system->set_field("lineage", $lineage . DIRECTORY_SEPARATOR . $this->document_management_system->get_field("id"));
                                        if ($this->document_management_system->update() && rename($file_path . "." . $doc_details["extension"], $template_dir . DIRECTORY_SEPARATOR . $this->document_management_system->get_field("id"))) {
                                            $uploaded_file = $this->document_management_system->get_document_full_details(["d.id" => $this->document_management_system->get_field("id")]);
                                            if (empty($file_existant_version)) {
                                                $response["result"] = true;
                                                $response["display_message"] = $this->lang->line("doc_generated_successfully");
                                            } else {
                                                $this->file_versioning($file_existant_version, $uploaded_file, $response);
                                            }
                                        }
                                    }
                                } else {
                                    $response["display_message"] = $this->lang->line("no_signature_saved");
                                }
                                if ($response["result"]) {
                                    $this->update_contract_signature_status($post_data["contract_id"]);
                                }
                            }
                        }
                    }
                    unlink($tmp_file);
                } else {
                    $response["display_message"] = $this->lang->line("contract_file_not_found");
                }
            } else {
                $response["display_message"] = $this->lang->line("contract_required");
            }
            $this->contract_signature_submission->fetch(["contract_id" => $post_data["contract_id"]]);
            $response["overall_status"] = $this->contract_signature_submission->get_field("status");
            $response["related_documents_count"] = $this->document_management_system->count_contract_related_documents($post_data["contract_id"], true);
        }
        $this->output->set_content_type("application/json")->set_output(json_encode($response));
    }
    private function update_contract_signature_status($contract_id)
    {
        $this->load->model("contract_signature_history", "signature_historyfactory");
        $this->contract_signature_history = $this->signature_historyfactory->get_instance();
        $new_status = "signed";
        $this->contract_signature_history->set_field("done_on", date("Y-m-d H:i:s"));
        $this->contract_signature_history->set_field("done_by", $this->ci->session->userdata("CP_user_id"));
        $this->contract_signature_history->set_field("done_by_type", "user");
        $this->contract_signature_history->set_field("from_action", $this->contract_signature_status->get_field("status"));
        $this->contract_signature_history->set_field("to_action", $new_status);
        $this->contract_signature_history->set_field("label", $this->contract_signature_status->get_field("label"));
        $this->contract_signature_history->set_field("action", "sign");
        $this->contract_signature_history->set_field("contract_id", $contract_id);
        $this->contract_signature_history->set_field("comment", $this->lang->line("contract_signed_comment"));
        if ($this->contract_signature_history->insert()) {
            $this->contract_signature_status->set_field("status", $new_status);
            $this->contract_signature_status->update();
        }
        $this->contract->fetch($contract_id);
        if (!$this->contract_signature_status->load_pending_signatures($contract_id)) {
            if ($this->contract_signature_submission->fetch(["contract_id" => $contract_id])) {
                $this->contract_signature_submission->set_field("status", "signed");
                $this->contract_signature_submission->update();
                $this->contract_signature_submission->reset_fields();
            }
            $contributors = $this->contract_contributor->load_contributors($contract_id);
            $notify["contributors"] = $contributors ? array_column($contributors, "id") : [];
            $notify["logged_in_user"] = $this->ci->session->userdata("CP_profileName");
            $this->contract->send_notifications("contract_signed", $notify, ["id" => $contract_id]);
        } else {
            $order = $this->contract_signature_status->get_field("rank");
            $next_signees = $this->contract_signature_status->load_next_order($order, $contract_id);
            if ($next_signees && !empty($next_signees)) {
                foreach ($next_signees as $next) {
                    $this->contract_signature_status->fetch($next["id"]);
                    $this->contract_signature_status->set_field("status", "awaiting_signature");
                    $this->contract_signature_status->update();
                    $this->contract_signature_status->reset_fields();
                    $signature_data = $this->contract_signature_status->load_signature_data($next["id"]);
                    $signature_data["users"] != "" ? $signees["users"] : "";
                    $signature_data["collaborators"] != "" ? $signees["collaborators"] : "";
                    $signature_data["user_groups"] != "" ? $signees["user_groups"] : "";
                }
                $this->load->model("signature", "signaturefactory");
                $this->signature = $this->signaturefactory->get_instance();
                $this->signature->notify_required_signees($signees, $contract_id);
            }
        }
    }
    private function file_versioning($file_existant_version, $uploaded_file, &$response)
    {
        $template_dir = $this->config->item("files_path") . DIRECTORY_SEPARATOR . "attachments" . DIRECTORY_SEPARATOR . "contracts";
        $versions_container = [];
        if ($file_existant_version["version"] == 1) {
            $this->document_management_system->reset_fields();
            $this->document_management_system->set_fields(["name" => $uploaded_file["id"] . "_versions", "type" => "folder", "parent" => $uploaded_file["parent"], "module" => $uploaded_file["module"], "module_record_id" => $uploaded_file["module_record_id"], "system_document" => 1, "visible" => 0, "visible_in_cp" => 0, "visible_in_ap" => 0, "createdOn" => date("Y-m-d H:i:s"), "createdBy" => $this->ci->session->userdata("CP_user_id"), "createdByChannel" => "A4L", "modifiedOn" => date("Y-m-d H:i:s"), "modifiedBy" => $this->ci->session->userdata("CP_user_id"), "modifiedByChannel" => "A4L"]);
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
            $this->document_management_system->set_fields(["parent" => $versions_container["id"], "lineage" => $versioned_file_lineage, "visible" => 0, "visible_in_cp" => 0, "visible_in_ap" => 0]);
            if ($this->document_management_system->update() && rename($template_dir . DIRECTORY_SEPARATOR . $file_existant_version["lineage"], $template_dir . DIRECTORY_SEPARATOR . $versioned_file_lineage)) {
                $response["result"] = true;
            }
        }
    }
    public function awaiting_approvals($filter = "all")
    {
        if ($this->license_type == "client") {
            $this->setPinesMessage("error", $this->lang->line("not_collaborator_license_approve"));
            redirect("contracts");
        }
        $this->pageTitle = sprintf($this->lang->line("app4legal_browser_title"), $this->lang->line("awaiting_approvals"));
        $data = [];
        $logged_in_user = $this->ci->session->userdata("CP_user_id");
        $this->load->model("contract_approval_submission", "contract_approval_submissionfactory");
        $this->contract_approval_submission = $this->contract_approval_submissionfactory->get_instance();
        $data["active_quick_filter"] = $filter;
        $data["contracts"] = $this->contract_approval_submission->load_cp_awaiting_approvals($logged_in_user, $filter);
        $data["model_code"] = $this->contract->get("modelCode");
        $this->includes("customerPortal/clientPortal/js/contracts", "js");
        $this->includes("customerPortal/clientPortal/js/awaiting_approvals", "js");
        $this->load->view("partial/header");
        $this->load->view("contracts/awaiting_approvals", $data);
        $this->load->view("partial/footer");
    }
    public function awaiting_signatures($filter = "all")
    {
        if ($this->license_type == "client") {
            $this->setPinesMessage("error", $this->lang->line("not_collaborator_license_sign"));
            redirect("contracts");
        }
        $this->pageTitle = sprintf($this->lang->line("app4legal_browser_title"), $this->lang->line("awaiting_signatures"));
        $data = [];
        $logged_in_user = $this->ci->session->userdata("CP_user_id");
        $data["active_quick_filter"] = $filter;
        $data["contracts"] = $this->contract_signature_submission->load_cp_awaiting_signatures($logged_in_user, $filter);
        $data["model_code"] = $this->contract->get("modelCode");
        $this->includes("customerPortal/clientPortal/js/contracts", "js");
        $this->includes("customerPortal/clientPortal/js/awaiting_signatures", "js");
        $this->load->view("partial/header");
        $this->load->view("contracts/awaiting_signatures", $data);
        $this->load->view("partial/footer");
    }
    public function view_document($id = 0)
    {
        $response = [];
        if (0 < $id) {
            echo $this->dms->get_document_content($id);
            exit;
        }
        $id = $this->input->post("id");
        if (!empty($id)) {
            $response["document"] = $this->dms->get_document_details(["id" => $id]);
            $response["document"]["url"] = app_url("modules/customer-portal/contracts/view_document/" . $id);
            if (!empty($response["document"]["extension"]) && in_array($response["document"]["extension"], $this->document_management_system->image_types)) {
                $response["iframe_content"] = $this->load->view("documents_management_system/view_image_document", ["url" => $response["document"]["url"]], true);
            }
        }
        $response["html"] = $this->load->view("documents_management_system/view_document", [], true);
        $this->output->set_content_type("application/json")->set_output(json_encode($response));
    }
    public function get_signature_picture($id = 0)
    {
        $this->load->model("cp_user_signature_attachment", "cp_user_signature_attachmentfactory");
        $this->cp_user_signature_attachment = $this->cp_user_signature_attachmentfactory->get_instance();
        $this->cp_user_signature_attachment->fetch($id);
        $user_id = $this->cp_user_signature_attachment->get_field("user_id");
        $signature_picture = $this->cp_user_signature_attachment->get_field("signature");
        if (!empty($signature_picture)) {
            $fileDirectory = $this->config->item("files_path") . DIRECTORY_SEPARATOR . "attachments" . DIRECTORY_SEPARATOR . "cp_users" . DIRECTORY_SEPARATOR . str_pad($user_id, 10, "0", STR_PAD_LEFT) . DIRECTORY_SEPARATOR . "signature";
            $file = $fileDirectory . DIRECTORY_SEPARATOR . $signature_picture;
            $content = @file_get_contents($file);
            if ($content) {
                $this->load->helper("download");
                force_download($signature_picture, $content);
            }
        }
    }
    public function add()
    {
        if (!$this->input->is_ajax_request()) {
            show_404();
        }
        $this->load->model("email_notification_scheme");
        if (!$this->input->post(NULL)) {
            $response["result"] = true;
            if ($this->input->get("option", true) && $this->input->get("step", true)) {
                $option = $this->input->get("option", true);
                $step = $this->input->get("step", true);
                switch ($option) {
                    case "add":
                        $screens = $this->contract_cp_screen->load_screens();
                        if ($step == 1) {
                            if (empty($screens)) {
                                $response["result"] = false;
                                $response["display_message"] = $this->lang->line("no_contract_request_screens");
                            } else {
                                $data["screens"] = $screens;
                                $this->load->model("contract_request_type_category", "contract_request_type_categoryfactory");
                                $this->contract_request_type_category = $this->contract_request_type_categoryfactory->get_instance();
                                $data["categories"] = $this->contract_request_type_category->loadAllCategories();
                                $response["html"] = $this->load->view("contracts/generate/request_screens", $data, true);
                            }
                        } else {
                            $screen_id = $this->input->get("screen_id", true);
                            if ($screen_id && $this->contract_cp_screen->fetch($screen_id)) {
                                $data["lang"] = "english";
                                $data["screenFields"] = $this->contract_cp_screen->load_screen_fields($screen_id);
                                $data["predefinedFields"] = $this->contract_cp_screen->get("screenFields");
                                $data["categories"] = $this->party_category_language->load_list_per_language();
                                $data["formHtml"] = $this->contract_cp_screen->loadFieldsHtml($data["screenFields"], $data["predefinedFields"]);
                                $response["html"] = $this->load->view("contracts/generate/screen_form", $data, true);
                            } else {
                                $response["result"] = false;
                                $response["display_message"] = $this->lang->line("no_contract_screen_chosen");
                            }
                        }
                        break;
                    case "choose":
                        if ($step == 1 || $this->input->get("action", true)) {
                            $data["types"] = $this->contract_type_language->load_list_per_language();
                            $response["html"] = $this->load->view("contracts/generate/template_lists", $data, true);
                            $response["result"] = true;
                        } else {
                            if ($this->input->get("template_id")) {
                                $this->load->model("contract_template", "contract_templatefactory");
                                $this->contract_template = $this->contract_templatefactory->get_instance();
                                $data = $this->contract_template->load_template_data($this->input->get("template_id"));
                                $data["channel"] = $this->cp_channel;
                                $response["pages"] = count($data["pages"]);
                                $response["html"] = $this->load->view("contracts/generate/questionnaire_form", $data, true);
                            } else {
                                $response["result"] = false;
                                $response["display_message"] = $this->lang->line("no_contract_template_chosen");
                            }
                        }
                        break;
                }
            } else {
                $data["title"] = $this->lang->line("request_contract");
                $data["show_notification"] = false;
                $response["html"] = $this->load->view("contracts/generate/form", $data, true);
            }
        } else {
            $option = $this->input->post("option", true);
            switch ($option) {
                case "add":
                    $response = $this->save_contract();
                    break;
                case "choose":
                    $this->customer_portal_users->fetch(["id" =>  $this->session->userdata("CP_user_id")]);

                    $response = $this->contract->save_contract_from_template($this->cp_channel, $this->session->userdata("CP_user_id"),$this->customer_portal_users->get_field("contact_id"));
                    break;
            }
        }
        $this->output->set_content_type("application/json")->set_output(json_encode($response));
    }
    public function customer_portal_users_autocomplete()
    {
        $term = trim((string) $this->input->get("term"));
        $results = $this->customer_portal_users->lookup($term, $this->input->get("object_id"), $this->input->get("object_category"));
        $this->output->set_content_type("application/json");
        $this->output->set_output(json_encode($results));
    }
    private function save_contract()
    {
        $result = false;
        $party_member_types = $this->input->post("party_member_type");
        $party_member_ids = $this->input->post("party_member_id");
        $party_categories = $this->input->post("party_category");
        $post_data = strip_all_tags($this->input->post(NULL));
        if (!$this->contract_cp_screen->fetch($post_data["screen_id"])) {
            return false;
        }
        $data["screenFields"] = $this->contract_cp_screen->load_screen_fields($post_data["screen_id"]);
        $data["predefinedFields"] = $this->contract_cp_screen->get("screenFields");
        foreach ($data["screenFields"] as $screenField) {
            if ($screenField["isRequired"] == 1 && $screenField["visible"] == 1) {
                if ($screenField["related_field"] != "attachment") {
                    if (!isset($post_data[$screenField["related_field"]]) || !$post_data[$screenField["related_field"]] && $post_data[$screenField["related_field"]] != "0") {
                        $validation_errors[$screenField["related_field"]] = $this->lang->line("cannot_be_blank_rule");
                    }
                } else {
                    foreach ($_FILES as $key => $file) {
                        if (in_array($key, $post_data["requiredAttachments"]) && (!isset($file) || empty($file["name"]))) {
                            $validation_errors[$key] = $this->lang->line("cannot_be_blank_rule");
                        }
                    }
                }
            }
        }
        if (empty($validation_errors)) {
            $logged_user = $this->ci->session->userdata("CP_user_id");
            $workflow_applicable = $this->contract_status->load_workflow_status_per_type($this->contract_cp_screen->get_field("type_id"));
            if (empty($workflow_applicable)) {
                $workflow_applicable = $this->contract_status->load_system_workflow_status();
            }
            $post_data["status_id"] = $workflow_applicable["status_id"] ?? "1";
            $post_data["workflow_id"] = $workflow_applicable["workflow_id"] ?? "1";
            $post_data["type_id"] = $this->contract_cp_screen->get_field("type_id");
            $post_data["sub_type_id"] = $this->contract_cp_screen->get_field("sub_type_id") ? $this->contract_cp_screen->get_field("sub_type_id") : NULL;
            $this->contract->set_fields($post_data);
            $this->contract->set_field("description", isset($post_data["description"]) ? $post_data["description"] : "");
            $this->contract->set_field("contract_date", !isset($post_data["contract_date"]) || $post_data["contract_date"] == "" ? date("Y-m-d") : $post_data["contract_date"]);
            $this->contract->set_field("visible_to_cp", 1);
            $this->contract->set_field("channel", $this->cp_channel);
            $this->contract->set_field("createdOn", date("Y-m-d H:i:s", time()));
            $this->contract->set_field("modifiedOn", date("Y-m-d H:i:s", time()));
            $this->contract->set_field("createdBy", $logged_user);
            $this->contract->set_field("modifiedBy", $logged_user);
            $this->contract->set_field("modifiedByChannel", $this->cp_channel);
            $this->contract->set_field("status", "Active");
            $this->contract->set_field("private", isset($post_data["shared_with"]) ? "1" : "0");
            $this->contract->set_field("archived", "no");
            $this->customer_portal_users->fetch($post_data["requester_id"] ?? $this->ci->session->userdata("CP_user_id"));
            $contact_id = $this->customer_portal_users->add_cp_user_as_contact(true, false, $this->customer_portal_users->get_field("email"));
            $this->contract->set_field("requester_id", $contact_id);
            $external_tables = [];
            foreach ($data["screenFields"] as $scField) {
                if ($scField["isRequired"] == 1 && $scField["visible"] == 0) {
                    $scFieldName = $scField["related_field"];
                    $predefinedFieldData = $data["predefinedFields"][$scFieldName];
                    if (!$predefinedFieldData["customField"]) {
                        switch ($scFieldName) {
                            case "shared_with":
                                $this->contract->set_field("private", "yes");
                                if ($scField["requiredDefaultValue"]) {
                                    $external_tables[$scFieldName] = explode(",", $scField["requiredDefaultValue"]);
                                }
                                break;
                            default:
                        }
                        $post_data[$scFieldName] = $scField["requiredDefaultValue"];
                        $this->contract->set_field($scFieldName, $scField["requiredDefaultValue"]);

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
            $this->contract->disable_builtin_logs();
            if ($this->contract->insert()) {
                $contract_id = $this->contract->get_field("id");
                $this->load->model("contract_sla_management", "contract_sla_managementfactory");
                $this->contract_sla_management = $this->contract_sla_managementfactory->get_instance();
                $this->contract_sla_management->contract_sla($contract_id, $logged_user, $this->cp_channel);
                $post_data["id"] = $contract_id;
                $this->load->model("approval", "approvalfactory");
                $this->approval = $this->approvalfactory->get_instance();
                $data["approval_center"] = $this->approval->update_approval_contract($post_data);
                $this->load->model("signature", "signaturefactory");
                $this->signature = $this->signaturefactory->get_instance();
                $data["signature_center"] = $this->signature->update_signature_contract($post_data);
                $watchers = $post_data["shared_with"] ?? $external_tables["shared_with"] ?? "";
                if ($this->contract->get_field("private") == "yes" && !empty($watchers)) {
                    $this->load->model("contract_user", "contract_userfactory");
                    $this->contract_user = $this->contract_userfactory->get_instance();
                    $watchers_data = ["contract_id" => $contract_id, "users" => $watchers];
                    $this->contract_user->insert_users($watchers_data);
                }
                $this->load->model("party");
                if ($party_member_types && $party_member_ids) {
                    $parties_data = $this->party->return_parties($party_member_types, $party_member_ids);
                    if (!empty($parties_data)) {
                        foreach ($parties_data as $key => $value) {
                            $parties_data[$key]["contract_id"] = $contract_id;
                            $parties_data[$key]["party_category_id"] = isset($party_categories[$key]) && !empty($party_categories[$key]) ? $party_categories[$key] : NULL;
                        }
                        $this->contract_party->insert_contract_parties($contract_id, $parties_data);
                    }
                    $this->contract->feed_related_contracts_to_parties($party_member_types, $party_member_ids, $contract_id);
                }
                $this->load->model("system_preference");
                $this->load->model("customer_portal_contract_watcher", "customer_portal_contract_watcherfactory");
                $this->customer_portal_contract_watcher = $this->customer_portal_contract_watcherfactory->get_instance();
                if (isset($post_data["watchedBy"]) && !empty($post_data["watchedBy"])) {
                    $this->customer_portal_contract_watcher->add_watchers_to_contract($post_data["watchedBy"], $contract_id);
                }
                $system_preferences = $this->system_preference->get_key_groups();
                $cp_prefix = $system_preferences["CustomerPortalConfig"]["cpAppTitle"];
                $result = true;
                $response["id"] = $contract_id;
                $response["model_code"] = $this->contract->get("modelCode");
                $contributors = $this->contract_contributor->load_contributors($contract_id);
                $notify["contributors"] = $contributors ? array_column($contributors, "id") : [];
                $notify["logged_in_user"] = $this->ci->session->userdata("CP_profileName");
                $this->contract->send_notifications("cp_add_contract", $notify, ["id" => $contract_id]);
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
                $customFields = $this->custom_field->load_custom_fields($contract_id, $this->contract->modelName, "en");
                $finalCustomFields = [];
                foreach ($customFields as $customField) {
                    $cFieldId = $customField["id"];
                    $custom_field_value = isset($tmpCustomFields[$cFieldId]) ? $tmpCustomFields[$cFieldId] : "";
                    $custom_field_data = ["custom_field_id" => $cFieldId, "recordId" => $contract_id];
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
                    }
                    $finalCustomFields[$cFieldId] = $custom_field_data;

                }
                if (is_array($finalCustomFields) && count($finalCustomFields)) {
                    $this->custom_field->update_custom_fields($finalCustomFields);
                }
                if ($system_preferences["webhooks"]["webhooks_enabled"] == 1) {
                    $webhook_data = $this->contract->load_contract_details($contract_id);
                    $this->contract->trigger_web_hook("contract_status_updated", $webhook_data);
                }
                $attachments_ids = $upload_errors = $upload_failed_files = [];
                foreach ($_FILES as $file_key => $file) {
                    if (!empty($file["name"])) {
                        $upload_response = $this->dms->upload_file(["module" => "contract", "module_record_id" => $contract_id, "container_name" => "Contract_Notes_Attachments", "upload_key" => $file_key, "visible_in_cp" => 1, "visible_in_ap" => 0]);
                        if ($upload_response["status"]) {
                            $attachments_ids[] = $upload_response["file"]["id"];
                        } else {
                            $upload_failed_files[] = $file["name"];
                        }
                    }
                }
                if (!empty($attachments_ids)) {
                    $current_time = date("Y-m-d H:i:s", time());
                    $comment = 1 < count($attachments_ids) ? $this->lang->line("multiple_attachment_added") : $this->lang->line("attachment_added");
                    $this->load->model("contract_comment", "contract_commentfactory");
                    $this->contract_comment = $this->contract_commentfactory->get_instance();
                    $this->contract_comment->set_field("contract_id", $contract_id);
                    $this->contract_comment->set_field("comment", nl2br($comment));
                    $this->contract_comment->set_field("createdBy", $logged_user);
                    $this->contract_comment->set_field("createdOn", $current_time);
                    $this->contract_comment->set_field("modifiedOn", $current_time);
                    $this->contract_comment->set_field("modifiedBy", $logged_user);
                    $this->contract_comment->set_field("channel", $this->cp_channel);
                    $this->contract_comment->set_field("modifiedByChannel", $this->cp_channel);
                    $this->contract_comment->set_field("edited", "0");
                    if (!$this->contract_comment->insert()) {
                        $result = false;
                        $validation_errors = $this->contract_comment->get("validationErrors");
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
                    $response["validationErrors"] = $message;
                }
                if (isset($upload_faild_error_message)) {
                    $response["validationErrors"] = $upload_faild_error_message;
                }
                $this->contract->inject_folder_templates($contract_id, "contract", $this->contract->get_field("type_id"));
            } else {
                $response["validationErrors"] = $this->contract->get("validationErrors");
            }
        } else {
            $response["validationErrors"] = $validation_errors;
        }
        $response["result"] = $result;
        return $response;
    }
    public function add_negotiation()
    {
        $response["result"] = false;
        if ($this->input->get("contract_approval_status_id", true) && $this->input->get("contract_id", true)) {
            $data["title"] = $this->lang->line("start_negotiation");
            $data["module"] = "contract";
            $data["contract_id"] = $this->input->get("contract_id", true);
            $data["contract_approval_status_id"] = $this->input->get("contract_approval_status_id", true);
            $data["show_notification"] = false;
            $response["result"] = true;
            $response["html"] = $this->load->view("contracts/approval_center/negotiation/form", $data, true);
        }
        if ($this->input->post(NULL, true)) {
            $this->load->model("contract_approval_negotiation", "contract_approval_negotiationfactory");
            $this->contract_approval_negotiation = $this->contract_approval_negotiationfactory->get_instance();
            $response = $this->contract_approval_negotiation->add_negotiation($this->ci->session->userdata("CP_user_id"), "collaborator", $this->ci->session->userdata("CP_profileName"));
        }
        $this->output->set_content_type("application/json")->set_output(json_encode($response));
    }
    public function add_comment_negotiation()
    {
        $response["result"] = false;
        if ($this->input->post(NULL, true)) {
            $this->load->model("contract_approval_negotiation", "contract_approval_negotiationfactory");
            $this->contract_approval_negotiation = $this->contract_approval_negotiationfactory->get_instance();
            $response = $this->contract_approval_negotiation->add_comment_negotiation($this->ci->session->userdata("CP_user_id"), "collaborator", $this->ci->session->userdata("CP_profileName"));
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
            $response["html"] = $this->load->view("contracts/approval_center/negotiation/forward", $data, true);
        }
        if ($this->input->post(NULL, true)) {
            $this->load->model("contract_approval_negotiation", "contract_approval_negotiationfactory");
            $this->contract_approval_negotiation = $this->contract_approval_negotiationfactory->get_instance();
            $response = $this->contract_approval_negotiation->forward_negotiation($this->ci->session->userdata("CP_user_id"), "collaborator");
        }
        $this->output->set_content_type("application/json")->set_output(json_encode($response));
    }
    public function complete_negotiation()
    {
        $response["result"] = false;
        $this->load->model("contract_approval_negotiation", "contract_approval_negotiationfactory");
        $this->contract_approval_negotiation = $this->contract_approval_negotiationfactory->get_instance();
        if ($this->input->post(NULL, true)) {
            $response = $this->contract_approval_negotiation->complete_negotiation($this->ci->session->userdata("CP_user_id"), "collaborator", $this->ci->session->userdata("CP_profileName"));
        }
        $this->output->set_content_type("application/json")->set_output(json_encode($response));
    }
    public function approval_center($contract_id)
    {
        if (!$this->input->is_ajax_request()) {
            show_404();
        }
        $response = ["result" => false];
        if ($this->contract->fetch($contract_id)) {
            $this->load->model("contract_approval_status", "contract_approval_statusfactory");
            $this->contract_approval_status = $this->contract_approval_statusfactory->get_instance();
            $this->load->model("contract_approval_history", "contract_approval_historyfactory");
            $this->contract_approval_history = $this->contract_approval_historyfactory->get_instance();
            $data = false;
            if ($this->contract_approval_submission->fetch(["contract_id" => $contract_id])) {
                $data["overall_status"] = $this->contract_approval_submission->get_field("status");
                $data["approval_history"] = $this->contract_approval_history->load_history($contract_id);
                $data["approval_center"] = $this->contract_approval_status->load_approval_center_for_contract($contract_id);
                $this->load->model("contract_approval_negotiation_comment", "contract_approval_negotiation_commentfactory");
                $this->contract_approval_negotiation_comment = $this->contract_approval_negotiation_commentfactory->get_instance();
                $this->load->helper("revert_comment_html");
                $data["negotiations"] = $this->contract_approval_negotiation_comment->load_negotiations_for_contract($contract_id);
                $data["manager"] = $this->contract->load_requester_manager($this->contract->get_field("requester_id"));
            } else {
                if ($this->contract_signature_submission->fetch(["contract_id" => $contract_id])) {
                    $data["enable_approve_all"] = true;
                }
            }
            $data["contract"]["id"] = $contract_id;
            $data["contract"]["name"] = $this->contract->get_field("name");
            $data["contract"]["workflow_id"] = $this->contract->get_field("workflow_id");
            $data["model_code"] = $this->contract->get("modelCode");
            $response["result"] = true;
            $response["html"] = $this->load->view("contracts/approval_center/index", $data, true);
        }
        $this->output->set_content_type("application/json")->set_output(json_encode($response));
    }
    public function signature_center($contract_id)
    {
        if (!$this->input->is_ajax_request()) {
            show_404();
        }
        $response = ["result" => false];
        if ($this->contract->fetch($contract_id)) {
            $this->load->model("contract_signature_status", "contract_signature_statusfactory");
            $this->contract_signature_status = $this->contract_signature_statusfactory->get_instance();
            $this->load->model("contract_signature_history", "signature_historyfactory");
            $this->contract_signature_history = $this->signature_historyfactory->get_instance();
            $data = false;
            if ($this->contract_signature_submission->fetch(["contract_id" => $contract_id])) {
                $data["overall_status"] = $this->contract_signature_submission->get_field("status");
                $data["signature_history"] = $this->contract_signature_history->load_history($contract_id);
                $data["signature_center"] = $this->contract_signature_status->load_signature_center_for_contract($contract_id);
                $data["manager"] = $this->contract->load_requester_manager($this->contract->get_field("requester_id"));
            }
            $data["contract"]["id"] = $contract_id;
            $data["contract"]["name"] = $this->contract->get_field("name");
            $data["model_code"] = $this->contract->get("modelCode");
            $response["result"] = true;
            $response["html"] = $this->load->view("contracts/signature_center/index", $data, true);
        }
        $this->output->set_content_type("application/json")->set_output(json_encode($response));
    }
    public function documents($contract_id = "")
    {
        if (!$this->input->is_ajax_request()) {
            show_404();
        }
        $data = [];
        $this->contract->fetch($contract_id);
        $contract = $this->contract->get_fields();
        $data["contract"] = $contract;
        $data["contract_id"] = $contract_id;
        $data["module"] = "customer-portal";
        $data["module_record"] = "contract";
        $data["module_record_id"] = $contract_id;
        $data["module_controller"] = "contracts";
        $data["module_prefix"] = "contract";
        $data["model_code"] = $this->contract->get("modelCode");
        $this->load->model("contract_document_status_language", "contract_document_status_languagefactory");
        $this->contract_document_status_language = $this->contract_document_status_languagefactory->get_instance();
        $this->load->model("contract_document_type_language", "contract_document_type_languagefactory");
        $this->contract_document_type_language = $this->contract_document_type_languagefactory->get_instance();
        $data["document_statuses"] = $this->contract_document_status_language->load_list_per_language();
        $data["document_types"] = $this->contract_document_type_language->load_list_per_language();
        $data["crumbParent"] = $this->contract->get("modelCode") . $contract_id;
        $this->load->model("document_management_system", "document_management_systemfactory");
        $this->document_management_system = $this->document_management_systemfactory->get_instance();
        $this->load->model("cp_user_preference");
        $data["related_documents_count"] = $this->document_management_system->count_contract_related_documents($contract_id, true);
        $document_editor = $this->cp_user_preference->get_value_by_user("document_editor", $this->session->userdata("CP_user_id"));
        if (!empty($document_editor)) {
            $document_editor = unserialize($this->cp_user_preference->get_value_by_user("document_editor", $this->session->userdata("CP_user_id")));
        }
        if (isset($document_editor["installation_popup_displayed"])) {
            if (!$document_editor["installation_popup_displayed"]) {
                $data["show_document_editor_installation_modal"] = true;
                $document_editor["installation_popup_displayed"] = true;
                $this->cp_user_preference->set_value("document_editor", serialize($document_editor), true, $this->session->userdata("CP_user_id"));
            }
        } else {
            $this->cp_user_preference->set_value("document_editor", serialize(["installation_popup_displayed" => true]), true, $this->session->userdata("CP_user_id"));
            $data["show_document_editor_installation_modal"] = true;
        }
        $response["html"] = $this->load->view("documents_management_system/index", $data, true);
        $this->output->set_content_type("application/json")->set_output(json_encode($response));
    }
    public function create_folder()
    {
        if ($this->input->get(NULL, true)) {
            $contract_id = $this->input->get("contract_id", true);
            $data["title"] = $this->lang->line("create_folder");
            $data["module"] = "customer-portal";
            $data["module_record"] = "contract";
            $data["module_record_id"] = $contract_id;
            $response["result"] = true;
            $response["html"] = $this->load->view("documents_management_system/document_name_form", $data, true);
        }
        if ($this->input->post(NULL, true)) {
            $post_data = $this->input->post(NULL, true);
            if (!$post_data["name"]) {
                $response["status"] = false;
                $response["validation_errors"]["name"] = $this->lang->line("cannot_be_blank_rule");
            } else {
                $response = $this->dms->create_folder(["module" => "contract", "module_record_id" => $this->input->post("module_record_id"), "lineage" => $this->input->post("lineage"), "visible_in_cp" => 1, "visible_in_ap" => 0, "name" => $this->input->post("name")]);
            }
        }
        $this->output->set_content_type("application/json")->set_output(json_encode($response));
    }
    public function rename_folder()
    {
        if (!$this->input->post(NULL, true)) {
            $document_id = $this->input->get("document_id", true);
            $this->document_management_system->fetch($document_id);
            $data["title"] = $this->lang->line("rename_folder");
            $data["name"] = $this->document_management_system->get_field("name");
            $data["document_id"] = $document_id;
            $data["module"] = "customer-portal";
            $data["module_record"] = "contract";
            $response["result"] = true;
            $response["html"] = $this->load->view("documents_management_system/document_name_form", $data, true);
        } else {
            $post_data = $this->input->post(NULL, true);
            if (!$post_data["name"]) {
                $response["status"] = false;
                $response["validation_errors"]["name"] = $this->lang->line("cannot_be_blank_rule");
            } else {
                $response = $this->dms->rename_document("contract", $this->input->post("document_id"), "folder", $this->input->post("name"));
            }
        }
        $this->output->set_content_type("application/json")->set_output(json_encode($response));
    }
    public function rename_file()
    {
        if (!$this->input->post(NULL, true)) {
            $document_id = $this->input->get("document_id", true);
            $this->document_management_system->fetch($document_id);
            $data["title"] = $this->lang->line("rename_file");
            $data["name"] = $this->document_management_system->get_field("name");
            $data["document_id"] = $document_id;
            $data["module"] = "customer-portal";
            $data["module_record"] = "contract";
            $response["result"] = true;
            $response["html"] = $this->load->view("documents_management_system/document_name_form", $data, true);
        } else {
            $post_data = $this->input->post(NULL, true);
            if (!$post_data["name"]) {
                $response["status"] = false;
                $response["validation_errors"]["name"] = $this->lang->line("cannot_be_blank_rule");
            } else {
                $response = $this->dms->rename_document("contract", $this->input->post("document_id"), "file", $this->input->post("name"));
            }
        }
        $this->output->set_content_type("application/json")->set_output(json_encode($response));
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
        $response["status"] = true;
        if ($this->dms->model->fetch(["module" => "contract", "id" => $this->input->post("folder_id")])) {
            $this->load->model("document_managment_user", "document_managment_userfactory");
            $this->document_managment_user = $this->document_managment_userfactory->get_instance();
            if ($this->input->post("modeType") == "getHtml") {
                $data["isPrivate"] = $this->input->post("private");
                $share_users = $this->document_managment_user->load_watchers_users($this->input->post("folder_id"));
                $data["sharedWithUsers"] = isset($share_users[0]) ? $share_users[0] : [];
                $data["sharedWithUsersStatus"] = isset($share_users[1]) ? $share_users[1] : [];
                $data["title"] = $this->lang->line("share_with");
                $response["html"] = $this->load->view("documents_management_system/shared_with_form", $data, true);
            } else {
                $response = $this->dms->share_folder("contract", $this->input->post("folder_id"), $this->input->post("private"), $this->input->post("watchers_users"));
            }
        } else {
            $response["status"] = false;
        }
        $this->output->set_content_type("application/json")->set_output(json_encode($response));
    }
    public function generate_document($id)
    {
        $this->load->model("doc_generator");
        $template_folder_path = $this->doc_generator->get_value_by_key("contract_template_folder_path");
        if (!$template_folder_path || !$id) {
            $error_msg = !$template_folder_path ? sprintf($this->lang->line("object_template_folder_path_is_not_specified"), $this->lang->line("contracts")) : $this->lang->line("invalid_record");
            if ($this->input->is_ajax_request()) {
                $response = ["result" => false, "error" => $error_msg];
                $this->output->set_content_type("application/json")->set_output(json_encode($response));
            } else {
                $this->setPinesMessage("warning", $error_msg);
                redirect("contracts/documents/" . $id);
            }
        } else {
            $template_record = $this->dms->model->get_document_details(["id" => $template_folder_path]);
            $data["versioning"] = true;
            $data["type"] = "contract";
            if ($this->input->get("action", true) == "read") {
                $parties = $this->contract_party->fetch_contract_parties_data($id);
                if (!empty($parties)) {
                    $data["parties"] = "";
                    foreach ($parties as $key => $party) {
                        $party_number = $key + 1;
                        $data["parties"] .= (0 < $key ? ", " : "") . $party["party_name"] . (isset($party["party_category_name"]) ? " - (" . $party["party_category_name"] . ")" : "");
                        $data["party" . $party_number] = $parties[$key]["party_name"];
                        $data["party" . $party_number . "_category"] = $parties[$key]["party_category_name"];
                    }
                }
            }
            $response = $this->dms->generate_contract_document($template_record, "contract", $id, "contract", "contract", $data);
            $response["related_documents_count"] = $this->document_management_system->count_contract_related_documents($id, true);
            $this->output->set_content_type("application/json")->set_output(json_encode($response));
        }
    }
    public function list_file_versions()
    {
        $list_file_verions_response = $this->dms->list_file_versions("contract", $this->input->post("file_id"), true);
        if (!empty($list_file_verions_response["data"]["file_versions"])) {
            $response["html"] = $this->load->view("documents_management_system/file_document_versions", $list_file_verions_response["data"], true);
        }
        $response["status"] = $list_file_verions_response["status"];
        $this->output->set_content_type("application/json");
        $this->output->set_output(json_encode($response));
    }
    public function save_as_pdf()
    {
        $response["result"] = false;
        if ($this->input->post("document_id", true)) {
            $post_data = $this->input->post(NULL, true);
            $this->document_management_system->fetch($post_data["document_id"]);
            $doc_details = $this->document_management_system->get_fields();
            $core_path = substr(COREPATH, 0, -12);
            $documents_root_direcotry = $this->config->item("files_path") . DIRECTORY_SEPARATOR . "attachments" . DIRECTORY_SEPARATOR;
            $tmp_file = $this->config->item("files_path") . DIRECTORY_SEPARATOR . "tmp" . DIRECTORY_SEPARATOR . "contracts" . DIRECTORY_SEPARATOR . rand(1000, 9999) . "." . $doc_details["extension"];
            $this->document_management_system->reset_fields();
            $this->document_management_system->fetch($doc_details["parent"]);
            $lineage = $this->document_management_system->get_field("lineage");
            $template_dir = $documents_root_direcotry . "contracts" . $lineage;
            if (is_file($template_dir . DIRECTORY_SEPARATOR . $post_data["document_id"])) {
                copy($template_dir . DIRECTORY_SEPARATOR . $post_data["document_id"], $tmp_file);
                $doc_details["extension"] = "pdf";
                $file_existant_version = $this->document_management_system->get_document_existant_version($doc_details["name"] . "." . $doc_details["extension"], "file", $lineage);
                require_once $core_path . "/application/libraries/phpdocx-premium-12.5-ns/Classes/Phpdocx/Create/CreateDocx.php";
                $file_path = $template_dir;
                $docx = new Phpdocx\Create\CreateDocx();
                $docx->transformDocument($tmp_file, $file_path . ".pdf", "libreoffice");
                $this->document_management_system->reset_fields();
                $this->document_management_system->set_fields($doc_details);
                $this->document_management_system->set_fields(["id" => NULL, "extension" => $doc_details["extension"], "size" => filesize($file_path . "." . $doc_details["extension"]), "version" => empty($file_existant_version) ? 1 : $file_existant_version["version"] + 1, "createdOn" => date("Y-m-d H:i:s"), "createdBy" => $this->ci->session->userdata("CP_user_id"), "createdByChannel" => "CP", "modifiedOn" => date("Y-m-d H:i:s"), "modifiedBy" => $this->ci->session->userdata("CP_user_id"), "modifiedByChannel" => "CP", "visible_in_ap" => 1]);
                if ($this->document_management_system->insert()) {
                    $this->document_management_system->set_field("lineage", $lineage . DIRECTORY_SEPARATOR . $this->document_management_system->get_field("id"));
                    if ($this->document_management_system->update() && rename($file_path . "." . $doc_details["extension"], $template_dir . DIRECTORY_SEPARATOR . $this->document_management_system->get_field("id"))) {
                        unlink($tmp_file);
                        $uploaded_file = $this->document_management_system->get_document_full_details(["d.id" => $this->document_management_system->get_field("id")]);
                        if (empty($file_existant_version)) {
                            $response["result"] = true;
                            $response["display_message"] = $this->lang->line("doc_generated_successfully");
                        } else {
                            $this->file_versioning($file_existant_version, $uploaded_file, $response);
                        }
                    }
                    $response["related_documents_count"] = $this->document_management_system->count_contract_related_documents($doc_details["module_record_id"]);
                }
                $this->output->set_content_type("application/json")->set_output(json_encode($response));
            }
        }
    }
    public function check_folder_privacy()
    {
        if (!$this->session->userdata("CP_logged_in")) {
            exit("cp_login_needed");
        }
        $private_folders = $this->dms->check_folder_privacy($this->input->post("id"), $this->input->post("lineage"));
        $response["result"] = empty($private_folders) ? false : true;
        $this->output->set_content_type("application/json");
        $this->output->set_output(json_encode($response));
    }
    public function delete_document()
    {
        $this->load->model("document_management_system", "document_management_systemfactory");
        $this->document_management_system = $this->document_management_systemfactory->get_instance();
        $this->document_management_system->fetch($this->input->post("document_id"));
        $module_record_id = $this->document_management_system->get_field("module_record_id");
        $response = $this->dms->delete_document("contract", $this->input->post("document_id"), $this->input->post("newest_version") == "true");
        $response["related_documents_count"] = $this->document_management_system->count_contract_related_documents($module_record_id, true);
        $this->output->set_content_type("application/json")->set_output(json_encode($response));
    }
    public function move_status($contract_id, $status_id)
    {
        $needs_approval = false;
        if ($this->contract->fetch($contract_id) && $contract_id && $status_id) {
            $transition_id = $this->input->post("transition_id", true);
            $old_status = $this->contract->get_field("status_id");
            $type_id = $this->contract->get_field("type_id");
            $this->load->model("contract_workflow_status_transition", "contract_workflow_status_transitionfactory");
            $this->contract_workflow_status_transition = $this->contract_workflow_status_transitionfactory->get_instance();
            if ($transition_id && $this->contract_workflow_status_transition->fetch($transition_id) && $this->contract_workflow_status_transition->get_field("approval_needed") && $this->contract_approval_submission->fetch(["contract_id" => $contract_id]) && $this->contract_approval_submission->get_field("status") !== "approved") {
                $this->setPinesMessage("error", $this->lang->line("needs_approval_before"));
                $needs_approval = true;
            }
            if (!$needs_approval) {
                $workflow_applicable = 0 < $this->contract->get_field("workflow_id") ? $this->contract->get_field("workflow_id") : 1;
                $allowed_statuses = $this->contract_workflow_status_transition->load_available_steps($old_status, $workflow_applicable);
                if ($status_id === $old_status || !in_array($status_id, array_keys($allowed_statuses["available_statuses"]))) {
                    $this->setPinesMessage("error", $this->lang->line("permission_not_allowed"));
                } else {
                    $this->contract->fetch($contract_id);
                    $this->contract->set_field("status_id", $status_id);
                    if (!$this->contract->update()) {
                        $this->setPinesMessage("error", $this->lang->line("contract_move_status_invalid"));
                    } else {
                        $this->load->model("approval", "approvalfactory");
                        $this->approval = $this->approvalfactory->get_instance();
                        $this->approval->workflow_status_approval_events($contract_id, $this->contract->get_field("workflow_id"), $status_id);
                        $this->setPinesMessage("success", $this->lang->line("updates_saved_successfully"));
                    }
                }
            }
            redirect("contracts/view/" . $contract_id);
        }
    }
    public function load_templates_per_contract_types()
    {
        if (!$this->input->is_ajax_request()) {
            show_404();
        }
        if (!$this->session->userdata("CP_logged_in")) {
            exit("cp_login_needed");
        }
        $response["templates"] = [];
        $this->load->model("contract_template", "contract_templatefactory");
        $this->contract_template = $this->contract_templatefactory->get_instance();
        if ($this->input->get("type_id")) {
            $where[] = ["show_in_cp", 1];
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
        if (!$this->session->userdata("CP_logged_in")) {
            exit("cp_login_needed");
        }
        $this->load->model("sub_contract_type_language", "sub_contract_type_languagefactory");
        $this->sub_contract_type_language = $this->sub_contract_type_languagefactory->get_instance();
        $response = $this->sub_contract_type_language->load_all_per_type($this->input->get("type_id"));
        $this->output->set_content_type("application/json")->set_output(json_encode($response));
    }
    public function draft_collaborate($contract_id = "")
    {
        $data = [];
        $this->contract->fetch($contract_id);
        $contract = $this->contract->get_fields();
        $data["contract"] = $contract;
        $data["contract_id"] = $contract_id;
        $data["model_code"] = $this->contract->get("modelCode");
        $data["docs"] = $this->dms->load_documents_for_collaboration($this->contract->get("modelName"), $contract_id);
        $response["html"] = $this->load->view("contracts/view/draft_collaborate", $data, true);
        $this->output->set_content_type("application/json")->set_output(json_encode($response));
    }
    public function load_docs_count()
    {
        $this->authenticate_exempted_actions();
        if (!$this->input->is_ajax_request()) {
            redirect("contracts");
        }
        $this->load->model("document_management_system", "document_management_systemfactory");
        $this->document_management_system = $this->document_management_systemfactory->get_instance();
        $response["count"] = $this->document_management_system->count_contract_related_documents($this->input->get("contract_id"), true);
        $this->output->set_content_type("application/json")->set_output(json_encode($response));
    }
    private function move_contract_attachments_to_parent_folder($comment, $parent_folder_name, $contract_id)
    {
        $ids = $this->get_comment_attachments($comment);
        if (0 < count($ids)) {
            $parent_folder = $this->dms->create_note_parent_folder($contract_id, $parent_folder_name, "contract");
            $this->dms->move_document_handler($parent_folder["id"], $ids, [], "contract");
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
    public function resend_approval_email()
    {
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
}

?>