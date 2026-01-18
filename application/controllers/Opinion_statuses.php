<?php

if (!defined("BASEPATH")) {
    exit("No direct script access allowed");
}
require "Top_controller.php";
class Opinion_statuses extends Top_controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model("opinion_status");
    }
    public function index()
    {
        $this->pageTitle = sprintf($this->lang->line("app4legal_browser_title"), $this->lang->line("opinion_statuses"));
        $data["records"] = $this->opinion_status->paginate(["order_by" => ["name asc"]]);
        $data["fb"] = $this->session->flashdata("fb");
        $this->load->view("opinion_statuses/index", $data);
    }
    public function add()
    {
        $this->save(0);
    }
    public function edit($id = "0")
    {
        $this->save($id);
    }
    private function save($id = "0")
    {
        if (!$this->input->is_ajax_request()) {
            show_404();
        }
        $response = [];
        if (!$this->input->post(NULL)) {
            $data = [];
            if ($id) {
                $this->opinion_status->fetch($id);
            }
            $data["title"] = $this->lang->line("opinion_status");
            $data["status"] = $this->opinion_status->get_fields();
            $response["html"] = $this->load->view("opinion_statuses/form", $data, true);
        } else {
            $this->opinion_status->set_fields($this->input->post(NULL));
            $result = $id ? $this->opinion_status->update() : $this->opinion_status->insert();
            if ($result) {
                $workflow_id = $this->input->post("workflow_id");
                if ($workflow_id) {
                    $this->load->model("opinion_workflow_status_relation", "opinion_workflow_status_relationfactory");
                    $this->opinion_workflow_status_relation = $this->opinion_workflow_status_relationfactory->get_instance();
                    $related_statuses = $this->opinion_workflow_status_relation->load_all(["where" => ["workflow_id", $workflow_id]]);
                    $start_point = empty($related_statuses) ? 1 : 0;
                    $this->opinion_workflow_status_relation->set_field("workflow_id", $workflow_id);
                    $this->opinion_workflow_status_relation->set_field("status_id", $this->opinion_status->get_field("id"));
                    $this->opinion_workflow_status_relation->set_field("start_point", $start_point);
                    $this->opinion_workflow_status_relation->insert();
                }
            } else {
                $response["validation_errors"] = $this->opinion_status->get("validationErrors");
            }
            $response["result"] = $result;
        }
        $this->output->set_content_type("application/json")->set_output(json_encode($response));
    }
    public function delete($id)
    {
        $numRows = $this->opinion_status->count_field_rows("opinions", "opinion_status_id", $id);
        if (0 < $numRows) {
            $this->set_flashmessage("warning", sprintf($this->lang->line("delete_record_related_object_failed"), $this->lang->line("opinion")));
            redirect("opinion_statuses/index");
        }
        $numRows = $this->opinion_status->count_field_rows("opinion_board_column_options", "opinion_status_id", $id);
        if (0 < $numRows) {
            $this->set_flashmessage("warning", sprintf($this->lang->line("delete_record_related_object_failed"), $this->lang->line("opinion_board")));
            redirect("opinion_statuses/index");
        }
        $numRows = $this->opinion_status->count_field_rows("opinion_workflow_status_relation", "status_id", $id);
        if (0 < $numRows) {
            $this->set_flashmessage("warning", sprintf($this->lang->line("delete_record_related_object_failed"), $this->lang->line("opinion_workflows")));
            redirect("opinion_statuses/index");
        }
        if ($this->opinion_status->delete($id)) {
            $this->set_flashmessage("information", $this->lang->line("record_deleted"));
            redirect("opinion_statuses/index");
        } else {
            $this->set_flashmessage("error", $this->lang->line("record_not_deleted"));
            redirect("opinion_statuses/index");
        }
    }
}

?>