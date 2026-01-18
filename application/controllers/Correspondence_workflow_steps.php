<?php

if (!defined("BASEPATH")) {
    exit("No direct script access allowed");
}
require "Core_controller.php";
class Correspondence_workflow_steps extends Core_controller
{
    public function __construct()
    {
        parent::__construct();
         $this->load->model("correspondence_workflow_step","correspondence_workflow_stepfactory");
        $this->correspondence_workflow_step= $this->correspondence_workflow_stepfactory->get_instance();
             $this->load->model("correspondence_type","correspondence_typefactory");
        $this->correspondence_type= $this->correspondence_typefactory->get_instance();
    }
    public function index($type_id=0)
    { 
         $data["records"] =[];
        if($type_id && $type_id>0){
            $data["records"] = $this->correspondence_workflow_step->load_steps_by_type($type_id);
        }else{
            redirect("correspondence_types");
        }

         $this->pageTitle = sprintf($this->lang->line("sheria360_browser_title"), $this->lang->line("correspondence_workflow_steps"));
      //  $data["records"] = $this->correspondence_workflow_step->paginate(["order_by" => ["sequence_order asc"]]);
      //  $data["correspondence_types"] = $this->correspondence_type->load_list([], ["firstLine" => ["" => "All "]]);
        $data["type_id"] =$type_id;
        $data["type_name"] ="";
       
      $data["fb"] = $this->session->flashdata("fb");
        $this->load->view("correspondence_workflow_steps/index", $data);
    }
    public function add($type_id=0)
    {
        if ($this->input->is_ajax_request()) {
            $response = [];
            if ($this->input->get("quick_add_form")) {
                $data = [];
                $data["title"] = $this->lang->line("add_correspondence_workflow_steps");
                $data["field_label"] = $this->lang->line("name");
                
                $response["html"] = $this->load->view("administration/quick_dialog_form", $data, true);
                $response["isLayoutRTL"] = $this->is_auth->is_layout_rtl();
            }
            if ($this->input->post(NULL)) {
                $response = [];
                $this->correspondence_workflow_step->set_fields($this->input->post(NULL));
                if (!$this->correspondence_workflow_step->insert()) {
                    $response["validationErrors"] = $this->correspondence_workflow_step->get("validationErrors");
                }
                $response["id"] = $this->correspondence_workflow_step->get_field("id");
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
            $this->correspondence_workflow_step->set_fields($this->input->post(NULL));
            if (empty($id)) {
                $result = $this->correspondence_workflow_step->insert();
            } else {
                $result = $this->correspondence_workflow_step->update();
                // if ($result) {
                //     $mv_data = ["where" => ["correspondence_workflow_steps_id" => $id], "dataset" => ["name" => $this->correspondence_workflow_step->get_field("name")]];
                //     $this->correspondence_workflow_step->refresh_mv_data("mv_hearings", $mv_data);
                // }
            }
            if ($this->input->is_ajax_request()) {
                if ($result) {
                    $response["msg"] = $this->lang->line("record_saved");
                    $response["id"] = $this->correspondence_workflow_step->get_field("id");
                    $response["result"] = true;
                } else {
                    $response["msg"] = $this->lang->line("record_could_not_be_saved");
                    $response["result"] = false;
                    $response["validationErrors"] = $this->correspondence_workflow_step->get("validationErrors");
                }
                return $this->output->set_content_type("application/json")->set_output(json_encode($response));
            }
            if ($result) {
                $this->set_flashmessage("success", $this->lang->line("record_saved"));
                redirect("correspondence_workflow_steps/index");
            } else {
                if ($this->correspondence_workflow_step->is_valid()) {
                    $this->set_flashmessage("error", $this->lang->line("record_could_not_be_saved"));
                    redirect("correspondence_workflow_steps/index");
                }
            }
        } else {
            $this->correspondence_workflow_step->fetch($id);
        }
        $data["id"] = $id;
        $this->pageTitle = sprintf($this->lang->line("app4legal_browser_title"), $this->lang->line("correspondence_workflow_steps"));
        $this->load->view("correspondence_workflow_steps/form", $data);
    }
    public function delete($id)
    {
        $numRows = $this->correspondence_workflow_step->count_field_rows("correspondence_document", "document_type_id", $id);
        if (0 < $numRows) {
            $this->set_flashmessage("warning", sprintf($this->lang->line("delete_record_related_object_failed_Case"), $this->lang->line("litigation_data")));
            redirect("correspondence_workflow_steps/index");
        }
        if ($this->correspondence_workflow_step->delete($id)) {
            $this->set_flashmessage("information", $this->lang->line("record_deleted"));
            redirect("correspondence_workflow_steps/index");
        } else {
            $this->set_flashmessage("error", $this->lang->line("record_not_deleted"));
            redirect("correspondence_workflow_steps/index");
        }
    }
}

?>