<?php

if (!defined("BASEPATH")) {
    exit("No direct script access allowed");
}
require "Core_controller.php";
class Hearing_types extends Core_controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model("legal_case_hearing_type", "legal_case_hearing_typefactory");
        $this->legal_case_hearing_type = $this->legal_case_hearing_typefactory->get_instance();
        $this->load->model("hearing_types_languages", "hearing_types_languagesfactory");
        $this->hearing_types_languages = $this->hearing_types_languagesfactory->get_instance();
    }
    public function index()
    {
        $this->pageTitle = sprintf($this->lang->line("app4legal_browser_title"), $this->lang->line("hearing_types"));
        $records = $this->hearing_types_languages->load_all_records();
        $data["records"] = [];
        foreach ($records as $record) {
            $data["records"][$record["type"]]["name_" . $record["lang_name"]] = $record["name"];
        }
        $this->load->model("language");
        $data["languages"] = $this->language->loadAvailableLanguages(true);
        $data["field_name"] = $this->lang->line("hearing_types");
        $data["field_type"] = "hearing_types";
        $this->load->view("partial/header");
        $this->load->view("hearing_types/index", $data);
        $this->load->view("partial/footer");
    }
    public function add()
    {
        if (!$this->input->is_ajax_request()) {
            show_404();
        }
        $response = $data = [];
        $data["title"] = $this->lang->line("add_hearing_type");
        $this->load->model("language");
        $availableLanguages = $this->language->loadAvailableLanguages(true);
        $current_language = strtolower(substr($this->session->userdata("AUTH_language"), 0, 2));
        if ($this->input->get("quick_add_form")) {
            $data["field_label"] = $this->lang->line("hearing_type");
            $data["field_name"] = "name_" . substr($this->session->userdata("AUTH_language"), 0, 2);
            $data["multi_language"] = $current_language;
            $data["availableLanguages"] = $availableLanguages;
            $data["language_label"] = "hearing_type_";
            $response["html"] = $this->load->view("administration/quick_dialog_form", $data, true);
        }
        if ($this->input->get("add_edit_form")) {
            $data += $this->return_data();
            $response["html"] = $this->load->view("administration/form", $data, true);
        }
        if ($this->input->post(NULL)) {
            $response = $this->hearing_types_languages->insert_record();
        }
        $this->output->set_content_type("application/json")->set_output(json_encode($response));
    }
    public function edit($id)
    {
        if (!$this->input->is_ajax_request()) {
            show_404();
        }
        $response = $data = [];
        $data["title"] = $this->lang->line("edit_hearing_type");
        if ($this->input->get("add_edit_form")) {
            $data += $this->return_data();
            $data["records"] = $this->hearing_types_languages->load_record($id);
            $response["html"] = $this->load->view("administration/form", $data, true);
        }
        if ($this->input->post(NULL)) {
            $response = $this->hearing_types_languages->update_record($id);
        }
        $this->output->set_content_type("application/json")->set_output(json_encode($response));
    }
    private function return_data()
    {
        $this->load->model("language");
        $data["records"] = false;
        $data["system_lang"] = substr($this->session->userdata("AUTH_language"), 0, 2);
        $data["languages"] = $this->language->loadAvailableLanguages(true);
        $data["field"] = ["label" => $this->lang->line("hearing_type"), "name" => "name"];
        return $data;
    }
    public function delete()
    {
        $id = $this->input->post("id");
        if ($this->hearing_types_languages->delete_record($id)) {
            $response = ["result" => true, "feedback_message" => $this->lang->line("record_deleted")];
        } else {
            $response = ["result" => false, "feedback_message" => sprintf($this->lang->line("delete_record_related_object_failed_Case"), $this->lang->line("hearing"))];
        }
        $this->output->set_content_type("application/json")->set_output(json_encode($response));
    }
}

?>