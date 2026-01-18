<?php

if (!defined("BASEPATH")) {
    exit("No direct script access allowed");
}
require APPPATH . "controllers/Top_controller.php";
class Contract_workflow_steps extends Contract_controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model("contract_status");
        $this->load->model("contract_status_language", "contract_status_languagefactory");
        $this->contract_status_language = $this->contract_status_languagefactory->get_instance();
        $this->load->model("language");
        $this->load->model("contract", "contractfactory");
        $this->contract = $this->contractfactory->get_instance();
    }
    public function index()
    {
        $this->pageTitle = sprintf($this->lang->line("sheria360_browser_title"), $this->lang->line("contract_statuses"));
        $records = $this->contract_status_language->load_all_records();
        $data["applies_to"]=[];
        $data["records"] = [];
        foreach ($records as $record) {
            $data["records"][$record["status_id"]]["name_" . $record["lang_name"]] = $record["name"];
            $data["records"][$record["status_id"]]["apply_to"]=$record["applies_to"];

        }
        $this->load->model("language");
        $data["languages"] = $this->language->loadAvailableLanguages(true);
        $data["field_name"] = $this->lang->line("name");

        $data["field_type"] = "contract_statuses";
        $data["module"] = "contract";
        $this->load->view("partial/header");
        $this->load->view("contract_statuses/index", $data);
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
            $data["field_label"] = $this->lang->line("type");
            $data["field_name"] = "name_" . substr($this->session->userdata("AUTH_language"), 0, 2);
            $response["html"] = $this->load->view("administration/quick_dialog_form", $data, true);
        }
        if ($this->input->get("add_edit_form")) {
            $data += $this->return_data();
            $response["html"] = $this->load->view("administration/form", $data, true);
        }
        if ($this->input->post(NULL)) {

            $response = $this->contract_status_language->insert_record();
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
            $data["records"] = $this->contract_status_language->load_record($id);
            $response["html"] = $this->load->view("administration/form", $data, true);
        }
        if ($this->input->post(NULL)) {
            $response = $this->contract_status_language->update_record($id);
        }
        $this->output->set_content_type("application/json")->set_output(json_encode($response));
    }
    private function return_data()
    {
        $data["records"] = false;
        $data["system_lang"] = substr($this->session->userdata("AUTH_language"), 0, 2);
        $data["languages"] = $this->language->loadAvailableLanguages(true);
        $data["applies_to_field"] = ["label" => $this->lang->line("applies_to"), "name" => "applies_to"];
        $data["appliesToValues"] = array_combine($this->contract->get("categoryValues"),[$this->lang->line("contract"), $this->lang->line("mou")]);
        $data["defaultAppliesToValue"] ="Contract";
        $data["field"] = ["label" => $this->lang->line("type"), "name" => "name"];

        return $data;
    }
    public function delete()
    {
        $id = $this->input->post("id");
        if ($this->contract_status_language->delete_record($id)) {
            $response = ["result" => true, "feedback_message" => $this->lang->line("record_deleted")];
        } else {
            $response = ["result" => false, "feedback_message" => sprintf($this->lang->line("delete_record_related_object_failed_Case"), $this->lang->line("contract"))];
        }
        $this->output->set_content_type("application/json")->set_output(json_encode($response));
    }
}
