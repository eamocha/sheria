<?php

if (!defined("BASEPATH")) {
    exit("No direct script access allowed");
}
require APPPATH . "controllers/Top_controller.php";
class Contract_statuses extends Contract_controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model("contract_status");
        $this->load->model("contract_status_language", "contract_status_languagefactory");
        $this->contract_status_language = $this->contract_status_languagefactory->get_instance();
        $this->load->model("language");
    }
    public function index()
    {
        $this->pageTitle = sprintf($this->lang->line("sheria360_browser_title"), $this->lang->line("contract_statuses"));
        $data["records"] = $this->contract_status_language->load_data();
        $this->load->model("language");
        $data["languages"] = $this->language->loadAvailableLanguages(true);
        $data["field_name"] = $this->lang->line("status");
        $data["field_type"] = "contract_statuses";
        $this->load->view("partial/header");
        $this->load->view("contract_statuses/index", $data);
        $this->load->view("partial/footer");
    }
    public function add()
    {// exit("add".json_encode($this->input->post(null)));
        if (!$this->input->is_ajax_request()) {
            show_404();
        }
        $response = $data = [];
        $data["title"] = $this->lang->line("add");
        if ($this->input->get("add_edit_form")) {
            $data += $this->return_data();
            $data["extra_html"] = $this->load->view("contract_statuses/extra_html", $data, true);
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
            $data["records"] = $this->contract_status_language->load_data($id);
            $data["extra_html"] = $this->load->view("contract_statuses/extra_html", $data, true);
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
        $this->load->model("status_category");
        $data["categories"] = $this->status_category->get("categories");
        $data["field"] = ["label" => $this->lang->line("status"), "name" => "name"];
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

?>