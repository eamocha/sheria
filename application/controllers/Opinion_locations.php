<?php

if (!defined("BASEPATH")) {
    exit("No direct script access allowed");
}
require "Top_controller.php";
class Opinion_locations extends Top_controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model("opinion_location");
    }
    public function index()
    {
        $this->pageTitle = sprintf($this->lang->line("app4legal_browser_title"), $this->lang->line("locations"));
        $data["records"] = $this->opinion_location->paginate(["order_by" => ["name asc"]]);
        $data["fb"] = $this->session->flashdata("fb");
        $this->load->view("opinion_locations/index", $data);
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
        if ($this->input->is_ajax_request()) {
            $response = [];
            if ($this->input->get("quick_add_form")) {
                $data = [];
                $data["title"] = $this->lang->line("opinion_location");
                $data["field_label"] = $this->lang->line("name");
                $response["html"] = $this->load->view("administration/quick_dialog_form", $data, true);
            }
            if ($this->input->post(NULL)) {
                $locationName = $this->input->post("name");
                $this->opinion_location->set_field("name", trim($locationName));
                if ($this->opinion_location->insert()) {
                    $response["id"] = $this->opinion_location->get_field("id");
                    $response["name"] = $this->opinion_location->get_field("name");
                } else {
                    $response["validationErrors"] = $this->opinion_location->get("validationErrors");
                }
            }
            $this->output->set_content_type("application/json")->set_output(json_encode($response));
        } else {
            $data = [];
            if ($this->input->post(NULL)) {
                $this->opinion_location->set_fields($this->input->post(NULL));
                if (empty($id)) {
                    $result = $this->opinion_location->insert();
                } else {
                    $result = $this->opinion_location->update();
                }
                if ($result) {
                    $this->set_flashmessage("success", $this->lang->line("record_saved"));
                    redirect("opinion_locations/index");
                } else {
                    if ($this->opinion_location->is_valid()) {
                        $this->set_flashmessage("error", $this->lang->line("record_could_not_be_saved"));
                        redirect("opinion_locations/index");
                    }
                }
            } else {
                $this->opinion_location->fetch($id);
            }
            $data["id"] = $id;
            $this->pageTitle = sprintf($this->lang->line("app4legal_browser_title"), $this->lang->line("locations"));
            $this->load->view("opinion_locations/form", $data);
        }
    }
    public function delete($id)
    {
        $response = [];
        $numRows = $this->opinion_location->count_field_rows("opinions", "opinion_location_id", $id);
        $numRows_event = $this->opinion_location->count_field_rows("events", "opinion_location_id", $id);
        if (0 < $numRows || 0 < $numRows_event) {
            $response["status"] = false;
            $response["message"] = sprintf($this->lang->line("delete_record_related_object_failed"), $this->lang->line("opinion") . " " . $this->lang->line("or") . " " . $this->lang->line("meeting"));
        }
        if ($this->opinion_location->delete($id)) {
            $response["status"] = true;
            $response["message"] = $this->lang->line("record_deleted");
        } else {
            $response["status"] = false;
            $response["message"] = $this->lang->line("record_not_deleted");
        }
        $this->output->set_content_type("application/json")->set_output(json_encode($response));
    }
}

?>