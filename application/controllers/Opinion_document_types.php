<?php

if (!defined("BASEPATH")) {
    exit("No direct script access allowed");
}
require "Top_controller.php";
class Opinion_document_types extends Top_controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model("opinion_document_type");
        $this->load->model("opinion_document_type_language", "opinion_document_type_languagefactory");
        $this->opinion_document_type_language = $this->opinion_document_type_languagefactory->get_instance();
        $this->load->model("language");
        $this->load->model("opinion", "opinionfactory");
        $this->opinion = $this->opinionfactory->get_instance();
    }
    public function index()
    {
        $this->pageTitle = sprintf($this->lang->line("sheria360_browser_title"), $this->lang->line("opinion_document_type"));
        $records = $this->opinion_document_type_language->load_all_records();
        $data["records"] = [];
        $data["applies_to"]=[];
        foreach ($records as $record) {
            $data["records"][$record["type_id"]]["name_" . $record["lang_name"]] = $record["name"];
            $data["records"][$record["type_id"]]["apply_to"]=$record["applies_to"]??"";
        }
        $data["languages"] = $this->language->loadAvailableLanguages(true);
        $data["field_name"] = $this->lang->line("opinion_document_type");
        $data["field_type"] = "opinion_document_types";
        $data["module"] = false;//"legal_opinions"; used to link you to a page in a specific module ex legal_opinions/...
        $this->load->view("partial/header");
        $this->load->view("opinion_document_types/index", $data);
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
            $response["html"] = $this->load->view("administration/other_services_form", $data, true);
        }
        if ($this->input->post(NULL)) {
            $response = $this->opinion_document_type_language->insert_record();
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
            $data["records"] = $this->opinion_document_type_language->load_record($id);
            $response["html"] = $this->load->view("administration/other_services_form", $data, true);
        }
        if ($this->input->post(NULL)) {
            $response = $this->opinion_document_type_language->update_record($id);
        }
        $this->output->set_content_type("application/json")->set_output(json_encode($response));
    }
    private function return_data()
    {
        $data["records"] = false;
        $data["system_lang"] = substr($this->session->userdata("AUTH_language"), 0, 2);
        $data["languages"] = $this->language->loadAvailableLanguages(true);
        $data["field"] = ["label" => $this->lang->line("document_type"), "name" => "name"];
        $data["applies_to_field"] = ["label" => $this->lang->line("applies_to"), "name" => "applies_to"];
        $data["appliesToValues"] = array_combine($this->opinion->get("categoryValues"),[$this->lang->line("opinion"), $this->lang->line("convenyancing")]);
        $data["defaultAppliesToValue"] ="opinion";
        return $data;
    }
    public function delete()
    {
        if (!$this->input->is_ajax_request()) {
            redirect("dashboard");
        }
        $response["result"] = false;
        $id = $this->input->post("id");
        $num_rows = $this->opinion_document_type->count_field_rows("opinion_url", "document_type_id", $id);
        if (0 < $num_rows) {
            $response["feedback_message"] = sprintf($this->lang->line("delete_record_related_object_failed_Case"), $this->lang->line("url"));
        } else {
            $this->load->library("dms");
            $num_rows = $this->dms->model->count_field_rows($this->dms->model->_table, "document_type_id", $id);
            if (0 < $num_rows) {
                $response["feedback_message"] = sprintf($this->lang->line("delete_record_related_object_failed_Case"), $this->lang->line("opinion"));
            } else {
                if ($this->opinion_document_type->delete($id)) {
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