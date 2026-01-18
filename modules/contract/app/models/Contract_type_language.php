<?php

if (!defined("BASEPATH")) {
    exit("No direct script access allowed");
}
class Contract_type_language extends My_Model_Factory
{
}
class mysqli_Contract_type_language extends My_Model
{
    protected $modelName = "contract_type_language";
    protected $_table = "contract_type_language";
    protected $_listFieldName = "name";
    protected $_fieldsNames = ["id", "type_id", "language_id", "name","applies_to"];
    public function __construct()
    {
        parent::__construct();
        $this->validate = ["type_id" => ["required" => true, "allowEmpty" => false, "rule" => "numeric", "message" => $this->ci->lang->line("cannot_be_blank_rule")], "language_id" => ["required" => true, "allowEmpty" => false, "rule" => "numeric", "message" => $this->ci->lang->line("cannot_be_blank_rule")], "name" => ["required" => ["required" => true, "allowEmpty" => false, "rule" => ["minLength", 1], "message" => $this->ci->lang->line("cannot_be_blank_rule")], "maxLength" => ["rule" => ["maxLength", 255], "message" => sprintf($this->ci->lang->line("max_characters"), 255)]]];
    }
    public function insert_record()
    {
        $type_id = $this->insert_id($this->ci->contract_type->get("_table"));
        $response["result"] = false;
        $this->ci->load->model("language");
        $languages = $this->ci->language->load_all();
        $system_lang = substr($this->ci->session->userdata("AUTH_language"), 0, 2);
        foreach ($languages as $lang) {
            $name = $this->ci->input->post("name_" . $lang["name"]) ? trim($this->ci->input->post("name_" . $lang["name"])) : trim($this->ci->input->post("name_" . $system_lang));
            $applies_to= $this->ci->input->post("applies_to");
            $fields = ["type_id" => $type_id, "language_id" => $lang["id"], "name" => $name, "applies_to" => $applies_to];
            $this->ci->contract_type_language->set_fields($fields);
            if ($this->ci->contract_type_language->validate()) {
                $query = [];
                $query["select"] = ["contract_type_language.id"];
                $query["where"] = [["contract_type_language.name", $name], ["contract_type_language.language_id", $lang["id"]]];
                $query_result = $this->load_all($query);
                if (!empty($query_result)) {
                    $response["validationErrors"]["name_" . $lang["name"]] = $this->ci->lang->line("already_exists");
                } else {
                    $this->ci->contract_type_language->insert();
                    $this->ci->contract_type_language->reset_fields();
                }
            } else {
                $validation_errors = $this->ci->contract_type_language->get("validationErrors");
                $response["validationErrors"]["name_" . $system_lang] = $validation_errors["name"];
            }
            if (!isset($response["validationErrors"])) {
                $response["id"] = $type_id;
                $response["name"] = trim($this->ci->input->post("name_" . $system_lang));
                $response["result"] = true;
                $response["records"] = array_values($this->ci->contract_type_language->load_record($type_id));
                $response["type"] = "contract_types";
                $response["applies_to"] = trim($this->ci->input->post("applies_to"));
            } else {
                $this->ci->contract_type_language->delete(["where" => [["type_id", $type_id]]]);
                $this->ci->contract_type->delete(["where" => [["id", $type_id]]]);
            }

        }
        return $response;
    }
    public function update_record($id)
    {
        $this->ci->load->model("language");
        $languages = $this->ci->language->load_all();
        $response["result"] = false;
        $system_lang = substr($this->ci->session->userdata("AUTH_language"), 0, 2);
        foreach ($languages as $lang) {
            $name = $this->ci->input->post("name_" . $lang["name"]) && $this->ci->input->post("name_" . $lang["name"]) ? trim($this->ci->input->post("name_" . $lang["name"])) : trim($this->ci->input->post("name_" . $system_lang));
            $applies_to= $this->ci->input->post("applies_to");
            $fields = ["type_id" => $id, "language_id" => $lang["id"], "name" => $name,"applies_to"=>$applies_to];
            $this->ci->contract_type_language->fetch(["type_id" => $id, "language_id" => $lang["id"]]);
            $this->ci->contract_type_language->set_fields($fields);
            if ($this->ci->contract_type_language->validate()) {
                $query = [];
                $query["select"] = ["contract_type_language.id"];
                $query["where"] = [["contract_type_language.name", $name], ["contract_type_language.language_id", $lang["id"]], ["contract_type_language.type_id !=", $id]];
                $query_result = $this->load_all($query);
                if (!empty($query_result)) {
                    $response["validationErrors"]["name_" . $lang["name"]] = $this->ci->lang->line("already_exists");
                } else {
                    $this->ci->contract_type_language->update();
                    $this->ci->contract_type_language->reset_fields();
                }
            } else {
                $validation_errors = $this->ci->contract_type_language->get("validationErrors");
                $response["validationErrors"]["name_" . $system_lang] = $validation_errors["name"];
            }
            if (!isset($response["validationErrors"])) {
                $response["id"] = $id;
                $response["name"] = trim($this->ci->input->post("name_" . $system_lang));
                $response["result"] = true;
                $response["records"] = array_values($this->ci->contract_type_language->load_record($id));
                $response["type"] = "contract_types";
                $response["applies_to"] = trim($this->ci->input->post("applies_to"));
            }

        }
        return $response;
    }
    public function insert_id_record()
    {
        return $this->ci->db->insert_id();
    }
    public function load_all_records()
    {
        $query = ["select" => "contract_type_language.applies_to,contract_type_language.type_id, contract_type_language.name,languages.name as lang_name", "join" => [["languages", "languages.id = contract_type_language.language_id", "left"]], "order_by" => ["contract_type_language.name", "asc"]];
        return $this->load_all($query);
    }
    public function load_record($id)
    {
        $query = ["select" => "contract_type_language.applies_to,contract_type_language.type_id, contract_type_language.name, languages.name as lang_name", "join" => [["languages", "languages.id = contract_type_language.language_id", "left"]], "where" => ["contract_type_language.type_id", $id], "order_by" => ["languages.id", "asc"]];
        $records = $this->load_all($query);
        foreach ($records as $record) {
            $data["name_" . $record["lang_name"]] = $record["name"];
        }
        return $data;
    }
    public function delete_record($id)
    {
        $num_rows = $this->ci->contract_type_language->count_field_rows("contract_workflow_per_type", "type_id", $id) || $this->ci->contract_type_language->count_field_rows("contract", "type_id", $id) || $this->ci->contract_type_language->count_field_rows("contract_clause_type", "type_id", $id);
        if (0 < $num_rows) {
            return false;
        }
        if ($this->ci->contract_type_language->delete(["where" => [["type_id", $id]]])) {
            $this->ci->contract_type->delete($id);
            return true;
        }
        return false;
    }
    public function load_list_per_language($applies_to="contract",$lang = NULL)
    {
        $applies_to=$applies_to??"contract";
        $language = $lang ?? strtolower(substr($this->ci->session->userdata("AUTH_language"), 0, 2));
        $query = ["select" => "contract_type_language.type_id, contract_type_language.name,languages.name as lang_name", "join" => [["languages", "languages.id = contract_type_language.language_id", "left"]], "where" =>["languages.name",$language],"where_in"=>['contract_type_language.applies_to', [$applies_to, 'both']]];
        $config_list = ["key" => "type_id", "value" => "name", "firstLine" => ["" => $this->ci->lang->line("none")]];
        return $this->load_list($query, $config_list);
    }
    public  function load_all_types_per_language()
    {
        $language = $lang ?? strtolower(substr($this->ci->session->userdata("AUTH_language"), 0, 2));
        $query = ["select" => "contract_type_language.type_id, contract_type_language.name,languages.name as lang_name", "join" => [["languages", "languages.id = contract_type_language.language_id", "left"]], "where" =>["languages.name",$language]];
        $config_list = ["key" => "type_id", "value" => "name", "firstLine" => ["" => $this->ci->lang->line("none")]];
        return $this->load_list($query, $config_list);
    }
    public function load_all_per_language($lang = 0)
    {
        $language = $lang ?: strtolower(substr($this->ci->session->userdata("AUTH_language"), 0, 2));
        $query = ["select" => "contract_type.id, contract_type_language.name as name", "join" => [["contract_type", "contract_type_language.type_id = contract_type.id", "left"], ["languages", "languages.id = contract_type_language.language_id", "left"]], "where" => ["languages.name", $language]];
        return $this->load_all($query);
    }
}
class mysql_Contract_type_language extends mysqli_Contract_type_language
{
}
class sqlsrv_Contract_type_language extends mysqli_Contract_type_language
{
    public function insert_id_record()
    {
        return $this->ci->db->insert_id();
    }
}

?>