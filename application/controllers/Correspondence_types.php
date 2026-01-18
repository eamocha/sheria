<?php

if (!defined("BASEPATH")) {
    exit("No direct script access allowed");
}
require "Core_controller.php";
class Correspondence_types extends Core_controller
{
    public function __construct()
    {
        parent::__construct();
         $this->load->model("correspondence_type","correspondence_typefactory");
        $this->correspondence_type= $this->correspondence_typefactory->get_instance();
    }
    public function index()
    {
         $this->pageTitle = sprintf($this->lang->line("sheria360_browser_title"), $this->lang->line("correspondence_types"));
        $data["records"] = $this->correspondence_type->paginate(["order_by" => ["name asc"]]);
        $data["fb"] = $this->session->flashdata("fb");
        $this->load->view("correspondence_types/index", $data);
    }
    public function add()
    {
        if ($this->input->is_ajax_request()) {
            $response = [];
            if ($this->input->get("quick_add_form")) {
                $data = [];
                $data["title"] = $this->lang->line("add_correspondence_type");
                $data["field_label"] = $this->lang->line("name");
                $response["html"] = $this->load->view("administration/quick_dialog_form", $data, true);
                $response["isLayoutRTL"] = $this->is_auth->is_layout_rtl();
            }
            if ($this->input->post(NULL)) {
                $response = [];
                $this->correspondence_type->set_fields($this->input->post(NULL));
                if (!$this->correspondence_type->insert()) {
                    $response["validationErrors"] = $this->correspondence_type->get("validationErrors");
                }
                $response["id"] = $this->correspondence_type->get_field("id");
                $response["name"] = $this->input->post("name");
            }
            $this->output->set_content_type("application/json")->set_output(json_encode($response));
        } else {
            $this->save(0);
        }
    }
    public function edit($id = "0")
    {
        $this->save($id);
    }
    private function save($id = "0")
    {
        $data = [];
        if ($this->input->post(NULL)) {
            $this->correspondence_type->set_fields($this->input->post(NULL));
            if (empty($id)) {
                $result = $this->correspondence_type->insert();
            } else {
                $result = $this->correspondence_type->update();
                if ($result) {
                    $mv_data = ["where" => ["correspondence_type_id" => $id], "dataset" => ["courtType" => $this->correspondence_type->get_field("name")]];
                    $this->correspondence_type->refresh_mv_data("mv_hearings", $mv_data);
                }
            }
            if ($this->input->is_ajax_request()) {
                if ($result) {
                    $response["msg"] = $this->lang->line("record_saved");
                    $response["id"] = $this->correspondence_type->get_field("id");
                    $response["result"] = true;
                } else {
                    $response["msg"] = $this->lang->line("record_could_not_be_saved");
                    $response["result"] = false;
                    $response["validationErrors"] = $this->correspondence_type->get("validationErrors");
                }
                return $this->output->set_content_type("application/json")->set_output(json_encode($response));
            }
            if ($result) {
                $this->set_flashmessage("success", $this->lang->line("record_saved"));
                redirect("correspondence_types/index");
            } else {
                if ($this->correspondence_type->is_valid()) {
                    $this->set_flashmessage("error", $this->lang->line("record_could_not_be_saved"));
                    redirect("correspondence_types/index");
                }
            }
        } else {
            $this->correspondence_type->fetch($id);
        }
        $data["id"] = $id;
        $this->pageTitle = sprintf($this->lang->line("app4legal_browser_title"), $this->lang->line("correspondence_types"));
        $this->load->view("correspondence_types/form", $data);
    }
    public function delete($id)
    {
        $numRows = $this->correspondence_type->count_field_rows("correspondences", "correspondence_type_id", $id);
        if (0 < $numRows) {
            $this->set_flashmessage("warning", sprintf($this->lang->line("delete_record_related_object_failed_Case"), $this->lang->line("litigation_data")));
            redirect("correspondence_types/index");
        }
        if ($this->correspondence_type->delete($id)) {
            $this->set_flashmessage("information", $this->lang->line("record_deleted"));
            redirect("correspondence_types/index");
        } else {
            $this->set_flashmessage("error", $this->lang->line("record_not_deleted"));
            redirect("correspondence_types/index");
        }
    }
}

?>