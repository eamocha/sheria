<?php

if (!defined("BASEPATH")) {
    exit("No direct script access allowed");
}
class Opinion_document_status_language extends My_Model_Factory
{
}
class mysqli_Opinion_document_status_language extends My_Model
{
    protected $modelName = "opinion_document_status_language";
    protected $_table = "opinion_document_status_language";
    protected $_listFieldName = "id";
    protected $_fieldsNames = ["id", "status_id", "language_id", "name"];
    public function __construct()
    {
        parent::__construct();
        $this->validate = ["language_id" => ["required" => true, "allowEmpty" => false, "rule" => "numeric", "message" => $this->ci->lang->line("cannot_be_blank_rule")], "name" => ["required" => ["required" => true, "allowEmpty" => false, "rule" => ["minLength", 1], "message" => $this->ci->lang->line("cannot_be_blank_rule")], "maxLength" => ["rule" => ["maxLength", 255], "message" => sprintf($this->ci->lang->line("max_characters"), 255)]]];
    }
    public function load_all_records()
    {
        $query = ["select" => "opinion_document_status_language.status_id, opinion_document_status_language.name,languages.name as lang_name", "join" => [["languages", "languages.id = opinion_document_status_language.language_id", "left"]], "order_by" => ["opinion_document_status_language.name", "asc"]];
        return $this->load_all($query);
    }
    public function load_record($id)
    {
        $query = ["select" => "opinion_document_status_language.status_id, opinion_document_status_language.name, languages.name as lang_name", "join" => [["languages", "languages.id = opinion_document_status_language.language_id", "left"]], "where" => ["opinion_document_status_language.status_id", $id], "order_by" => ["languages.id", "asc"]];
        $records = $this->load_all($query);
        foreach ($records as $record) {
            $data["name_" . $record["lang_name"]] = $record["name"];
        }
        return $data;
    }
    public function load_list_per_language($lang = NULL)
    {
        $language = $lang ?? strtolower(substr($this->ci->session->userdata("AUTH_language"), 0, 2));
        $query = ["select" => "opinion_document_status_language.status_id, opinion_document_status_language.name,languages.name as lang_name", "join" => [["languages", "languages.id = opinion_document_status_language.language_id", "left"]], "where" => ["languages.name", $language]];
        $config_list = ["key" => "status_id", "value" => "name", "firstLine" => ["" => $this->ci->lang->line("none")]];
        return $this->load_list($query, $config_list);
    }
    public function insert_record()
    {
        $status_id = $this->insert_id($this->ci->opinion_document_status->get("_table"));
        $response["result"] = false;
        $languages = $this->ci->language->load_all();
        $system_lang = substr($this->ci->session->userdata("AUTH_language"), 0, 2);
        foreach ($languages as $lang) {
            $name = $this->ci->input->post("name_" . $lang["name"]) && $this->ci->input->post("name_" . $lang["name"]) ? trim($this->ci->input->post("name_" . $lang["name"])) : trim($this->ci->input->post("name_" . $system_lang));
            $fields = ["status_id" => $status_id, "language_id" => $lang["id"], "name" => $name];
            $this->ci->opinion_document_status_language->set_fields($fields);
            if ($this->ci->opinion_document_status_language->validate()) {
                $query = [];
                $query["select"] = ["opinion_document_status_language.id"];
                $query["where"] = [["opinion_document_status_language.name", $name], ["opinion_document_status_language.language_id", $lang["id"]]];
                $query_result = $this->load_all($query);
                if (!empty($query_result)) {
                    $response["validationErrors"]["name_" . $lang["name"]] = $this->ci->lang->line("already_exists");
                } else {
                    $this->ci->opinion_document_status_language->insert();
                    $this->ci->opinion_document_status_language->reset_fields();
                }
            } else {
                $validation_errors = $this->ci->opinion_document_status_language->get("validationErrors");
                $response["validationErrors"]["name_" . $system_lang] = $validation_errors["name"];
            }
            if (!isset($response["validationErrors"])) {
                $response["id"] = $status_id;
                $response["name"] = trim($this->ci->input->post("name_" . $system_lang));
                $response["result"] = true;
                $response["records"] = array_values($this->ci->opinion_document_status_language->load_record($status_id));
                $response["type"] = "opinion_document_statuses";
            } else {
                $this->ci->opinion_document_status_language->delete(["where" => [["status_id", $status_id]]]);
                $this->ci->opinion_document_status->delete(["where" => [["id", $status_id]]]);
            }
            return $response;
        }
    }
    public function update_record($id)
    {
        $languages = $this->ci->language->load_all();
        $response["result"] = false;
        $system_lang = substr($this->ci->session->userdata("AUTH_language"), 0, 2);
        foreach ($languages as $lang) {
            $name = $this->ci->input->post("name_" . $lang["name"]) && $this->ci->input->post("name_" . $lang["name"]) ? trim($this->ci->input->post("name_" . $lang["name"])) : trim($this->ci->input->post("name_" . $system_lang));
            $fields = ["status_id" => $id, "language_id" => $lang["id"], "name" => $name];
            $this->ci->opinion_document_status_language->fetch(["status_id" => $id, "language_id" => $lang["id"]]);
            $this->ci->opinion_document_status_language->set_fields($fields);
            if ($this->ci->opinion_document_status_language->validate()) {
                $query = [];
                $query["select"] = ["opinion_document_status_language.id"];
                $query["where"] = [["opinion_document_status_language.name", $name], ["opinion_document_status_language.language_id", $lang["id"]], ["opinion_document_status_language.status_id !=", $id]];
                $query_result = $this->load_all($query);
                if (!empty($query_result)) {
                    $response["validationErrors"]["name_" . $lang["name"]] = $this->ci->lang->line("already_exists");
                } else {
                    $this->ci->opinion_document_status_language->update();
                    $this->ci->opinion_document_status_language->reset_fields();
                }
            } else {
                $validation_errors = $this->ci->opinion_document_status_language->get("validationErrors");
                $response["validationErrors"]["name_" . $system_lang] = $validation_errors["name"];
            }
            if (!isset($response["validationErrors"])) {
                $response["id"] = $id;
                $response["name"] = trim($this->ci->input->post("name_" . $system_lang));
                $response["result"] = true;
                $response["records"] = array_values($this->ci->opinion_document_status_language->load_record($id));
                $response["type"] = "opinion_document_statuses";
            }
            return $response;
        }
    }
}
class mysql_Opinion_document_status_language extends mysqli_Opinion_document_status_language
{
}
class sqlsrv_Opinion_document_status_language extends mysqli_Opinion_document_status_language
{
}

?>