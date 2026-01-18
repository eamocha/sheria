<?php

if (!defined("BASEPATH")) {
    exit("No direct script access allowed");
}
require "Core_controller.php";
class Conveyancing_transactions_types extends Core_controller
{
    public function __construct()
    {
        parent::__construct();
         $this->load->model("conveyancing_transaction_types","conveyancing_transaction_typesfactory");
        $this->conveyancing_transaction_types= $this->conveyancing_transaction_typesfactory->get_instance();
    }
    public function index()
    {
         $this->pageTitle = sprintf($this->lang->line("sheria360_browser_title"), $this->lang->line("conveyancing_transactions_types"));
        $data["records"] = $this->conveyancing_transaction_types->paginate(["order_by" => ["name asc"]]);
        $data["fb"] = $this->session->flashdata("fb");
        $this->load->view("conveyancing_transactions_types/index", $data);
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
                $this->conveyancing_transaction_types->set_fields($this->input->post(NULL));
                if (!$this->conveyancing_transaction_types->insert()) {
                    $response["validationErrors"] = $this->conveyancing_transaction_types->get("validationErrors");
                }
                $response["id"] = $this->conveyancing_transaction_types->get_field("id");
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
           $postData= $this->input->post(NULL);
           $postData['applies_to']="land";
             $postData['createdOn']=date("Y-m-d H:i:s");
             $postData['createdBy']=$this->is_auth->get_user_id();

            $this->conveyancing_transaction_types->set_fields($postData);
            if (empty($id)) {
                $result = $this->conveyancing_transaction_types->insert();
            } else {
                $result = $this->conveyancing_transaction_types->update();
                if ($result) {
                    $mv_data = ["where" => ["conveyancing_transactions_type_id" => $id], "dataset" => ["conveyancing_transactions_types" => $this->conveyancing_transaction_types->get_field("name")]];
                    $this->conveyancing_transaction_types->refresh_mv_data("mv_hearings", $mv_data);
                }
            }
            if ($this->input->is_ajax_request()) {
                if ($result) {
                    $response["msg"] = $this->lang->line("record_saved");
                    $response["id"] = $this->conveyancing_transaction_types->get_field("id");
                    $response["result"] = true;
                } else {
                    $response["msg"] = $this->lang->line("record_could_not_be_saved");
                    $response["result"] = false;
                    $response["validationErrors"] = $this->conveyancing_transaction_types->get("validationErrors");
                }
                return $this->output->set_content_type("application/json")->set_output(json_encode($response));
            }
            if ($result) {
                $this->set_flashmessage("success", $this->lang->line("record_saved"));
                redirect("conveyancing_transactions_types/index");
            } else {
                if ($this->conveyancing_transaction_types->is_valid()) {
                    $this->set_flashmessage("error", $this->lang->line("record_could_not_be_saved"));
                    redirect("conveyancing_transactions_types/index");
                }
            }
        } else {
            $this->conveyancing_transaction_types->fetch($id);
        }
        $data["id"] = $id;
        $this->pageTitle = sprintf($this->lang->line("app4legal_browser_title"), $this->lang->line("conveyancing_transactions_types"));
        $this->load->view("conveyancing_transactions_types/form", $data);
    }
    public function delete($id)
    {
        $numRows = $this->conveyancing_transaction_types->count_field_rows("correspondence_document", "document_type_id", $id);
        if (0 < $numRows) {
            $this->set_flashmessage("warning", sprintf($this->lang->line("delete_record_related_object_failed_Case"), $this->lang->line("conveyancing_documents")));
            redirect("conveyancing_transactions_types/index");
        }
        if ($this->conveyancing_transaction_types->delete($id)) {
            $this->set_flashmessage("information", $this->lang->line("record_deleted"));
            redirect("conveyancing_transactions_types/index");
        } else {
            $this->set_flashmessage("error", $this->lang->line("record_not_deleted"));
            redirect("conveyancing_transactions_types/index");
        }
    }
}

?>