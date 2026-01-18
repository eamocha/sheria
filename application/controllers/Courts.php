<?php

if (!defined("BASEPATH")) {
    exit("No direct script access allowed");
}
require "Core_controller.php";
class Courts extends Core_controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model("court");
        $this->load->model("court_type");
        $this->load->model("court_degree");
        $this->load->model("court_region");

    }
    public function index()
    {
        $this->pageTitle = sprintf($this->lang->line("app4legal_browser_title"), $this->lang->line("courts"));
       // $data["records"] = $this->court->paginate(["order_by" => ["name asc"]]);

        $filters = [];

        if ($this->input->get('name')) {
            $filters['name LIKE'] = '%' . $this->input->get('name') . '%';
        }
        if ($this->input->get('type')) {
            $filters['court_type_id'] = $this->input->get('type');
        }
        if ($this->input->get('rank')) {
            $filters['court_rank_id'] = $this->input->get('rank');
        }
        if ($this->input->get('region')) {
            $filters['court_region_id'] = $this->input->get('region');
        }
        if ($this->input->get('hierarchy')) {
            $filters['court_hierarchy'] = $this->input->get('hierarchy');
        }


       // $records = $this->court->get_paginated();


        $data["records"] = $this->court->k_load_all_courts($filters, []);
        $data["court_types"]=$this->court_type->load_list();
        $data["court_ranks"]=$this->court_degree->load_list();
        $data["court_regions"]=$this->court_region->load_list();

        $data["fb"] = $this->session->flashdata("fb");
        $this->includes("scripts/courts", "js");
        $this->load->view("courts/index", $data);
    }
    public function add()
    {
        if ($this->input->is_ajax_request()) {
            $response = [];
            if ($this->input->get("quick_add_form")) {
                $data = [];
                $data["title"] = $this->lang->line("add_court");
                $data["field_label"] = $this->lang->line("name");
                $response["html"] = $this->load->view("administration/quick_dialog_form", $data, true);
                $response["isLayoutRTL"] = $this->is_auth->is_layout_rtl();
            }
            if ($this->input->post(NULL)) {
                $response = [];
                $this->court->set_fields($this->input->post(NULL));
                if (!$this->court->insert()) {
                    $response["validationErrors"] = $this->court->get("validationErrors");
                }
                $response["id"] = $this->court->get_field("id");
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
            $this->court->set_fields($this->input->post(NULL));
            if (empty($id)) {
                $result = $this->court->insert();
            } else {
                $result = $this->court->update();
                if ($result) {
                    $mv_data = ["where" => ["court_id" => $id], "dataset" => ["court" => $this->court->get_field("name")]];
                    $this->court->refresh_mv_data("mv_hearings", $mv_data);
                }
            }
            if ($this->input->is_ajax_request()) {
                if ($result) {
                    $response["msg"] = $this->lang->line("record_saved");
                    $response["id"] = $this->court->get_field("id");
                    $response["result"] = true;
                } else {
                    $response["msg"] = $this->lang->line("record_could_not_be_saved");
                    $response["result"] = false;
                    $response["validationErrors"] = $this->court->get("validationErrors");
                }
                return $this->output->set_content_type("application/json")->set_output(json_encode($response));
            }
            if ($result) {
                $this->set_flashmessage("success", $this->lang->line("record_saved"));
                redirect("courts/index");
            } else {
                if ($this->court->is_valid()) {
                    $this->set_flashmessage("error", $this->lang->line("record_could_not_be_saved"));
                    redirect("courts/index");
                }
            }
        } else {
            $this->court->fetch($id);
        }
        $data["id"] = $id;
        $this->pageTitle = sprintf($this->lang->line("app4legal_browser_title"), $this->lang->line("courts"));
        $data["court_types"]=$this->court_type->load_list();
        $data["court_ranks"]=$this->court_degree->load_list();
        $data["court_regions"]=$this->court_region->load_list();


        $data["title"] = $this->pageTitle;
        $this->load->view("courts/form", $data);
    }

    public function delete($id)
    {
        $numRows = $this->court->count_field_rows("legal_case_litigation_details", "court_id", $id);
        if (0 < $numRows) {
            $this->set_flashmessage("warning", sprintf($this->lang->line("delete_record_related_object_failed_Case"), $this->lang->line("litigation_data")));
            redirect("courts/index");
        }
        if ($this->court->delete($id)) {
            $this->set_flashmessage("information", $this->lang->line("record_deleted"));
            redirect("courts/index");
        } else {
            $this->set_flashmessage("error", $this->lang->line("record_not_deleted"));
            redirect("courts/index");
        }
    }
}

?>