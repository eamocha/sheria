<?php

if (!defined("BASEPATH")) {
    exit("No direct script access allowed");
}
require "Top_controller.php";

class Exhibit_locations extends Top_controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model("exhibit_location","exhibit_locationfactory");
        $this->exhibit_location=$this->exhibit_locationfactory->get_instance();
    }

    public function index()
    {
        $this->pageTitle = sprintf($this->lang->line("sheria360_browser_title"), $this->lang->line("exhibit_locations"));
        $data["records"] = $this->exhibit_location->paginate(["order_by" => ["name asc"]]);
        $data["fb"] = $this->session->flashdata("fb");
        $this->load->view("exhibit_locations/index", $data);
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
                $data["title"] = $this->lang->line("exhibit_location");
                $data["field_label"] = $this->lang->line("name");
                $response["html"] = $this->load->view("exhibit_locations/quick_add_form", $data, true);
            }
            if ($this->input->post(NULL)) {
                $this->exhibit_location->set_fields($this->input->post(NULL));
                if ($this->exhibit_location->insert()) {
                    $response["id"] = $this->exhibit_location->get_field("id");
                    $response["name"] = $this->exhibit_location->get_field("name");
                    $response["success"] =true;
                } else {
                    $response["validationErrors"] = $this->exhibit_location->get("validationErrors");
                }
            }
            $this->output->set_content_type("application/json")->set_output(json_encode($response));
        } else {
            $data = [];
            if ($this->input->post(NULL)) {
                $this->exhibit_location->set_fields($this->input->post(NULL));
                if (empty($id)) {
                    $result = $this->exhibit_location->insert();
                } else {
                    $result = $this->exhibit_location->update();
                }
                if ($result) {
                    $this->set_flashmessage("success", $this->lang->line("record_saved"));
                    redirect("exhibit_locations/index");
                } else {
                    if ($this->exhibit_location->is_valid()) {

                        $this->set_flashmessage("error", $this->lang->line("record_could_not_be_saved"));
                        redirect("exhibit_locations/index");
                    }
                }
            } else {
                $this->exhibit_location->fetch($id);
            }
            $data["id"] = $id;
            $this->pageTitle = sprintf($this->lang->line("sheria360_browser_title"), $this->lang->line("exhibit_locations"));
            $this->load->view("exhibit_locations/form", $data);
        }
    }

    public function delete($id)
    {
        $response = [];
        $numRows = $this->exhibit_location->count_field_rows("exhibits", "current_location", $id);

        if (0 < $numRows) {
            $response["status"] = false;
            $response["message"] = sprintf($this->lang->line("delete_record_related_object_failed"), $this->lang->line("exhibit"));
        } elseif ($this->exhibit_location->delete($id)) {
            $response["status"] = true;
            $response["message"] = $this->lang->line("record_deleted");
        } else {
            $response["status"] = false;
            $response["message"] = $this->lang->line("record_not_deleted");
        }
        $this->output->set_content_type("application/json")->set_output(json_encode($response));
    }
    public function autocomplete()
    {
        if (!$this->input->is_ajax_request()) {
            show_404();
        }
        if (!$this->is_auth->is_logged_in()) {
            exit("login_needed");
        }
        $term = trim((string) $this->input->get("term"));
        $results = $this->exhibit_location->lookup($term);
        $this->output->set_content_type("application/json");
        $this->output->set_output(json_encode($results));
    }
}
?>