<?php

if (!defined("BASEPATH")) {
    exit("No direct script access allowed");
}
require APPPATH . "controllers/Top_controller.php";
class Contract_document_types extends Contract_controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model("contract_document_type");
        $this->load->model("contract_document_type_language", "contract_document_type_languagefactory");
        $this->contract_document_type_language = $this->contract_document_type_languagefactory->get_instance();
        $this->load->model("language");
    }
    public function index()
    {
        $this->pageTitle = sprintf($this->lang->line("app4legal_browser_title"), $this->lang->line("contract_document_type"));
        $records = $this->contract_document_type_language->load_all_records();
        $data["records"] = [];
        foreach ($records as $record) {
            $data["records"][$record["type_id"]]["name_" . $record["lang_name"]] = $record["name"];
        }
        $data["languages"] = $this->language->loadAvailableLanguages(true);
        $data["field_name"] = $this->lang->line("contract_document_type");
        $data["field_type"] = "contract_document_types";
        $data["module"] = "contract";
        $this->load->view("partial/header");
        $this->load->view("contract_document_types/index", $data);
        $this->load->view("partial/footer");
    }
    public function add()
    {
        if (!$this->input->is_ajax_request()) {
            show_404();
        }
        $response = $data = [];
        $data["title"] = $this->lang->line("add");
        if ($this->input->get("quick_add_form")) {
            $data["field_label"] = $this->lang->line("document_type");
            $data["field_name"] = "name_" . substr($this->session->userdata("AUTH_language"), 0, 2);
            $response["html"] = $this->load->view("administration/quick_dialog_form", $data, true);
        }
        if ($this->input->get("add_edit_form")) {
            $data += $this->return_data();
            $response["html"] = $this->load->view("administration/form", $data, true);
        }
        if ($this->input->post(NULL)) {
            $response = $this->contract_document_type_language->insert_record();
        }
        $this->output->set_content_type("application/json")->set_output(json_encode($response));
    }
    public function edit($id)
    {
        if (!$this->input->is_ajax_request()) {
            show_404();
        }
        $response = $data = [];
        $data["title"] = $this->lang->line("edit");
        if ($this->input->get("add_edit_form")) {
            $data += $this->return_data();
            $data["records"] = $this->contract_document_type_language->load_record($id);
            $response["html"] = $this->load->view("administration/form", $data, true);
        }
        if ($this->input->post(NULL)) {
            $response = $this->contract_document_type_language->update_record($id);
        }
        $this->output->set_content_type("application/json")->set_output(json_encode($response));
    }
    private function return_data()
    {
        $data["records"] = false;
        $data["system_lang"] = substr($this->session->userdata("AUTH_language"), 0, 2);
        $data["languages"] = $this->language->loadAvailableLanguages(true);
        $data["field"] = ["label" => $this->lang->line("document_type"), "name" => "name"];
        return $data;
    }
    public function delete()
    {
        if (!$this->input->is_ajax_request()) {
            redirect("dashboard");
        }
        $response["result"] = false;
        $id = $this->input->post("id");
        $num_rows = $this->contract_document_type->count_field_rows("contract_url", "document_type_id", $id);
        if (0 < $num_rows) {
            $response["feedback_message"] = sprintf($this->lang->line("delete_record_related_object_failed_Case"), $this->lang->line("url"));
        } else {
            $this->load->library("dms");
            $num_rows = $this->dms->model->count_field_rows($this->dms->model->_table, "document_type_id", $id);
            if (0 < $num_rows) {
                $response["feedback_message"] = sprintf($this->lang->line("delete_record_related_object_failed_Case"), $this->lang->line("contract"));
            } else {
                if ($this->contract_document_type->delete($id)) {
                    $response["feedback_message"] = $this->lang->line("record_deleted");
                    $response["result"] = true;
                } else {
                    $response["feedback_message"] = $this->lang->line("record_not_deleted");
                }
            }
        }
        $this->output->set_content_type("application/json");
        $this->output->set_output(json_encode($response));
    }
}

?>