<?php

if (!defined("BASEPATH")) {
    exit("No direct script access allowed");
}
require "Core_controller.php";
class Conveyancing_instrument_type extends Core_controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model("conveyancing_instrument_types","conveyancing_instrument_typesfactory");
        $this->conveyancing_instrument_types = $this->conveyancing_instrument_typesfactory->get_instance();
    }
    public function index()
    {
        $this->pageTitle = sprintf($this->lang->line("sheria360_browser_title"), $this->lang->line("conveyancing_instrument_types"));
        $data["records"] = $this->conveyancing_instrument_types->paginate(["order_by" => ["name asc"]]);
        $data["fb"] = $this->session->flashdata("fb");
        $this->load->view("conveyancing_instrument_types/index", $data);
    }
    public function add()
    {
        if ($this->input->is_ajax_request()) {
            $response = [];
            if ($this->input->get("quick_add_form")) {
                $data = [];
                $data["title"] = $this->lang->line("add");
                $data["field_label"] = $this->lang->line("name");
                $response["html"] = $this->load->view("administration/quick_dialog_form", $data, true);
                $response["isLayoutRTL"] = $this->is_auth->is_layout_rtl();
            }
            if ($this->input->post(NULL)) {
                $response = [];
                $this->conveyancing_instrument_types->set_fields($this->input->post(NULL));
                if (!$this->conveyancing_instrument_types->insert()) {
                    $response["validationErrors"] = $this->conveyancing_instrument_types->get("validationErrors");
                }
                $response["id"] = $this->conveyancing_instrument_types->get_field("id");
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
            $this->conveyancing_instrument_types->set_fields($this->input->post(NULL));
            if (empty($id)) {
                $result = $this->conveyancing_instrument_types->insert();
            } else {
                $result = $this->conveyancing_instrument_types->update();
                if ($result) {
                  //  $mv_data = ["where" => ["conveyancing_instrument_type_id" => $id], "dataset" => ["instrument_types" => $this->conveyancing_instrument_types->get_field("name")]];
                    //$this->conveyancing_instrument_types->refresh_mv_data("mv_hearings", $mv_data);
                }
            }
            if ($this->input->is_ajax_request()) {
                if ($result) {
                    $response["msg"] = $this->lang->line("record_saved");
                    $response["id"] = $this->conveyancing_instrument_types->get_field("id");
                    $response["result"] = true;
                } else {
                    $response["msg"] = $this->lang->line("record_could_not_be_saved");
                    $response["result"] = false;
                    $response["validationErrors"] = $this->conveyancing_instrument_types->get("validationErrors");
                }
                return $this->output->set_content_type("application/json")->set_output(json_encode($response));
            }
            if ($result) {
                $this->set_flashmessage("success", $this->lang->line("record_saved"));
                redirect("conveyancing_instrument_type/index");
            } else {
                if ($this->conveyancing_instrument_types->is_valid()) {
                    $this->set_flashmessage("error", $this->lang->line("record_could_not_be_saved"));
                    redirect("conveyancing_instrument_type/index");
                }
            }
        } else {
            $this->conveyancing_instrument_types->fetch($id);
        }
        $data["id"] = $id;
        $this->pageTitle = sprintf($this->lang->line("sheria360_browser_title"), $this->lang->line("conveyancing_instrument_types"));
        $this->load->view("conveyancing_instrument_types/form", $data);
    }
    public function delete($id)
    {
        $numRows = $this->conveyancing_instrument_types->count_field_rows("conveyancing_instrument_types", "id", $id);
        if (0 < $numRows) {
            $this->set_flashmessage("warning", sprintf($this->lang->line("delete_record_related_object_failed_Case"), $this->lang->line("conveyancing_instrument")));
            redirect("conveyancing_instrument_type/index");
        }
        if ($this->conveyancing_instrument_types->delete($id)) {
            $this->set_flashmessage("information", $this->lang->line("record_deleted"));
            redirect("conveyancing_instrument_type/index");
        } else {
            $this->set_flashmessage("error", $this->lang->line("record_not_deleted"));
            redirect("conveyancing_instrument_type/index");
        }
    }
}

?>