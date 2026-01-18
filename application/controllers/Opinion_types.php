<?php

if (!defined("BASEPATH")) {
    exit("No direct script access allowed");
}
require "Top_controller.php";
class Opinion_types extends Top_controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model("opinion_type", "opinion_typefactory");
        $this->opinion_type = $this->opinion_typefactory->get_instance();
    }
    public function index()
    {
        $this->load->model("opinion", "opinionfactory");
        $this->opinion = $this->opinionfactory->get_instance();
        $this->pageTitle = sprintf($this->lang->line("sheria360_browser_title"), $this->lang->line("opinion_types"));
        $data["tmp"] = $this->opinion_type->load_all_records();
        $data["tmp1"] = [];
        $data["records"] = [];
        $language = strtolower(substr($this->session->userdata("AUTH_language"), 0, 2));
        foreach ($data["tmp"] as $key => $val) {
            if (!array_key_exists($val["id"], $data["tmp1"])) {
                $data["tmp1"][$val["id"]] = $data["tmp"][$key];
            }
            $data["tmp1"][$val["id"]]["name_" . $val["langName"]] = $val["name"];
            unset($data["tmp1"][$val["id"]]["name"]);
            unset($data["tmp1"][$val["id"]]["langName"]);
        }
        $data["records"] = $data["tmp1"];
        $this->load->model("language");
        $data["langCount"] = sizeof($this->language->load_all());
        $data["languages"] = $this->language->loadAvailableLanguages(true);
        $data["fb"] = $this->session->flashdata("fb");
        $this->load->view("opinion_types/index", $data);
    }
    public function add()
    {
        if ($this->input->is_ajax_request()) {
            $response = [];
            $this->load->model(["legal_case_stage_language", "language"]);
            $availableLanguages = $this->language->loadAvailableLanguages(true);
            $current_language = strtolower(substr($this->session->userdata("AUTH_language"), 0, 2));
            if ($this->input->get("quick_add_form")) {
                $data = [];
                $data["title"] = $this->lang->line("add_opinion_type");
                $data["field_label"] = $this->lang->line("opinion_type");
                $data["component"] = "opinion_types";
                $data["multi_language"] = $current_language;
                $data["availableLanguages"] = $availableLanguages;
                $data["language_label"] = "opinion_type_language_";
                $response["html"] = $this->load->view("administration/quick_dialog_form", $data, true);
                $response["isLayoutRTL"] = $this->is_auth->is_layout_rtl();
            }
            if ($this->input->post(NULL)) {
                $this->load->model(["opinion_types_language", "language"]);
                $languages = $this->language->load_all();
                $this->opinion_type->set_fields($this->input->post(NULL));
                $opinionTypeId = $this->opinion_type->insert_new_record();
                $opinion_types_langs = [];
                foreach ($languages as $language) {
                    $fieldsToInsert = ["opinion_type_id" => $opinionTypeId, "language_id" => $language["id"], "name" => $this->input->post("name_" . $language["name"]) ? $this->input->post("name_" . $language["name"]) : $this->input->post("name_" . $current_language)];
                    $this->opinion_types_language->set_fields($fieldsToInsert);
                    if (!$this->opinion_types_language->insert()) {
                        $this->opinion_types_language->delete(["where" => [["opinion_type_id", $opinionTypeId]]]);
                        $this->opinion_type->delete(["where" => [["id", $opinionTypeId]]]);
                        $response["validationErrors"] = $this->opinion_types_language->get("validationErrors");
                        $response["validationErrors"]["name_" . $language["name"]] = $response["validationErrors"]["name"];
                        $response["id"] = $opinionTypeId;
                        $response["name"] = $this->input->post("name_" . $current_language);
                    } else {
                        $this->opinion_types_language->reset_fields();
                    }
                }
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
        $this->load->model(["opinion_types_language", "language"]);
        $languages = $this->language->loadAvailableLanguages(true);
        if ($this->input->post(NULL)) {
            if (empty($id)) {
                $opinionTypeId = $this->opinion_type->insert_new_record();
                $result = $opinionTypeId? true : false;
            } else {
                if (0 < $id && !$this->opinion_type->fetch($id)) {
                    $this->set_flashmessage("error", $this->lang->line("invalid_record"));
                    redirect("opinion_types/index");
                }
                $opinionTypeId = $id;
                $result = true;
            }
            if ($result) {
                $opinion_types_langs = [];
                if ($this->db->dbdriver == "sqlsrv") {
                    $languages = $this->language->load_all();
                    $_POST["name_sp"] = $this->input->post("name_en");
                }
                $flag = false;
                if (!empty($id)) {
                    foreach ($languages as $language) {
                        $fields_to_insert = ["opinion_type_id !=" => $opinionTypeId, "language_id" => $language["id"], "name" => $this->input->post("name_" . $language["name"])];
                        $result = $this->opinion_types_language->fetch($fields_to_insert);
                        if ($result) {
                            $flag = true;
                        }
                    }
                }
                if (!$flag) {
                    if (!empty($id)) {
                        $result = $this->opinion_types_language->delete(["where" => [["opinion_type_id", $opinionTypeId]]]);
                    }
                    foreach ($languages as $language) {
                        $fieldsToInsert = ["opinion_type_id" => $opinionTypeId, "language_id" => $language["id"], "name" => $this->input->post("name_" . $language["name"]),"applies_to" => $this->input->post("applies_to")];
                        $this->opinion_types_language->set_fields($fieldsToInsert);
                        if (!$this->opinion_types_language->insert()) {
                            $this->opinion_types_language->delete(["where" => [["opinion_type_id", $opinionTypeId]]]);
                            $this->opinion_type->delete(["where" => [["id", $opinionTypeId]]]);
                            $error = $this->opinion_types_language->get("validationErrors");
                            $this->set_flashmessage("error", $error["name"]);
                            redirect("opinion_types/index");
                        }
                        $this->opinion_types_language->reset_fields();
                    }
                } else {
                    $this->set_flashmessage("error", sprintf($this->lang->line("is_unique_rule"), $this->lang->line("name")));
                    redirect("opinion_types/index");
                }
                $this->set_flashmessage("success", $this->lang->line("record_saved"));
                redirect("opinion_types/index");
            } else {
                if ($this->opinion_type->is_valid()) {
                    $this->set_flashmessage("error", $this->lang->line("record_could_not_be_saved"));
                    redirect("opinion_types/index");
                }
            }
        } else {
            if (0 < $id && !$this->opinion_type->fetch($id)) {
                $this->set_flashmessage("error", $this->lang->line("invalid_record"));
                redirect("opinion_types/index");
            }
            foreach ($languages as $lang) {
                $this->opinion_types_language->reset_fields();
                $this->opinion_types_language->fetch(["opinion_type_id" => $this->opinion_type->get_field("id"), "language_id" => $lang["id"]]);
                $data["name_" . $lang["name"]] = $this->opinion_types_language->get_field("name");
            }
        }
        $data["id"] = $id;
        $data["languages"] = $languages;
        $this->pageTitle = sprintf($this->lang->line("sheria360_browser_title"), $this->lang->line("opinion_types"));
        $data["applies_to_field"] = ["label" => $this->lang->line("applies_to"), "name" => "applies_to"];
        $data["appliesToValues"] = array_combine($this->opinion_types_language->get("categoryValues"),[$this->lang->line("Opinions"), $this->lang->line("Conveyancing")]);
        $data["defaultAppliesToValue"] ="Opinions";
        $data["system_lang"] = substr($this->session->userdata("AUTH_language"), 0, 2);
        $this->load->view("opinion_types/form", $data);
    }
    public function delete($id)
    {
        $numRows = $this->opinion_type->count_field_rows("opinions", "opinion_type_id", $id);
        if (0 < $numRows) {
            $this->set_flashmessage("warning", sprintf($this->lang->line("delete_record_related_object_failed"), $this->lang->line("opinion")));
            redirect("opinion_types/index");
        }
        $this->load->model("opinion_types_language");
        $recordsDeleted = $this->opinion_types_language->delete(["where" => [["opinion_type_id", $id]]]);
        if ($recordsDeleted) {
            if ($this->opinion_type->delete($id)) {
                $this->set_flashmessage("information", $this->lang->line("record_deleted"));
                redirect("opinion_types/index");
            } else {
                $this->set_flashmessage("error", $this->lang->line("record_not_deleted"));
                redirect("opinion_types/index");
            }
        } else {
            $this->set_flashmessage("error", $this->lang->line("record_not_deleted"));
            redirect("opinion_types/index");
        }
    }
}

