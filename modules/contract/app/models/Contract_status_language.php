<?php

if (!defined("BASEPATH")) {
    exit("No direct script access allowed");
}
class Contract_status_language extends My_Model_Factory
{
}
class mysqli_Contract_status_language extends My_Model
{
    protected $modelName = "contract_status_language";
    protected $_table = "contract_status_language";
    protected $_listFieldName = "name";
    protected $_fieldsNames = ["id", "status_id", "language_id", "name","responsible_user_roles", "step_icon","activity","step_input","step_output"];
    public function __construct()
    {
        parent::__construct();
        $this->validate = ["language_id" => ["required" => true, "allowEmpty" => false, "rule" => "numeric", "message" => $this->ci->lang->line("cannot_be_blank_rule")], "name" => ["required" => ["required" => true, "allowEmpty" => false, "rule" => ["minLength", 1], "message" => $this->ci->lang->line("cannot_be_blank_rule")], "maxLength" => ["rule" => ["maxLength", 255], "message" => sprintf($this->ci->lang->line("max_characters"), 255)]]];
    }
    public function insert_record()
    {
        $response["result"] = false;
        $this->ci->contract_status->set_field("category_id", $this->ci->input->post("category_id"));
        $this->ci->contract_status->set_field("is_global", $this->ci->input->post("is_global"));
        if ($this->ci->contract_status->insert()) {
            $status_id = $this->ci->contract_status->get_field("id");
            $this->ci->load->model("language");
            $languages = $this->ci->language->load_all();
            $system_lang = substr($this->ci->session->userdata("AUTH_language"), 0, 2);
            foreach ($languages as $lang) {
                $name = $this->ci->input->post("name_" . $lang["name"]) && $this->ci->input->post("name_" . $lang["name"]) ? trim($this->ci->input->post("name_" . $lang["name"])) : trim($this->ci->input->post("name_" . $system_lang));
                $responsible_user_roles= $this->ci->input->post("responsible_user_roles");
                $step_icon= $this->ci->input->post("step_icon");
                $activity= $this->ci->input->post("activity");
                $step_input= $this->ci->input->post("step_input");
               $step_output= $this->ci->input->post("step_output");
                $fields = ["status_id" => $status_id, "language_id" => $lang["id"], "name" => $name,"responsible_user_roles"=>$responsible_user_roles,"activity"=>$activity,"step_icon"=>$step_icon,"step_input"=>$step_input,"step_output"=>$step_output];
                $this->ci->contract_status_language->set_fields($fields);
                if ($this->ci->contract_status_language->validate()) {
                    $query = [];
                    $query["select"] = ["contract_status_language.id"];
                    $query["where"] = [["contract_status_language.name", $name], ["contract_status_language.language_id", $lang["id"]]];
                    $query_result = $this->load_all($query);
                    if (!empty($query_result)) {
                        $response["validationErrors"]["name_" . $lang["name"]] = $this->ci->lang->line("already_exists");
                    } else {
                        $this->ci->contract_status_language->insert();
                        $this->ci->contract_status_language->reset_fields();
                    }
                } else {
                    $validation_errors = $this->ci->contract_status_language->get("validationErrors");
                    $response["validationErrors"]["name_" . $system_lang] = $validation_errors["name"];
                }
                if (!isset($response["validationErrors"])) {
                    $workflow_id = $this->ci->input->post("workflow_id", true);
                    if ($workflow_id) {
                        $this->ci->load->model("contract_workflow_status_relation", "contract_workflow_status_relationfactory");
                        $this->ci->contract_workflow_status_relation = $this->ci->contract_workflow_status_relationfactory->get_instance();
                        $related_statuses = $this->ci->contract_workflow_status_relation->load_all(["where" => ["workflow_id", $workflow_id]]);
                        $start_point = empty($related_statuses) ? 1 : 0;
                        $step_exist = $this->ci->contract_workflow_status_relation->load_all(["where" => [["workflow_id", $workflow_id],["status_id",$status_id]]]);

                        if (empty($step_exist)) {
                            $this->ci->contract_workflow_status_relation->set_field("workflow_id", $workflow_id);
                            $this->ci->contract_workflow_status_relation->set_field("status_id", $status_id);
                            $this->ci->contract_workflow_status_relation->set_field("start_point", $start_point);
                            $this->ci->contract_workflow_status_relation->set_field("approval_start_point", 0);
                            $this->ci->contract_workflow_status_relation->insert();
                        }
                        //insert functions
                        $this->save_step_functions($status_id,$this->ci->input->post("functions"));
                        $this->save_checklist_items($status_id,$this->ci->input->post("checklist"));


                    }
                    $response["id"] = $status_id;
                    $response["name"] = trim($this->ci->input->post("name_" . $system_lang));
                    $response["result"] = true;
                    $response["records"] = $this->ci->contract_status_language->load_data($status_id);
                    $response["type"] = "contract_statuses";
                } else {
                    $this->ci->contract_status_language->delete(["where" => [["status_id", $status_id]]]);
                    $this->ci->contract_status->delete(["where" => [["id", $status_id]]]);
                }
            }
        }else{
            $response["validationErrors"]="Step could not be saved. Ensure its not a duplicate of existing contract step";

        }
        return $response;
    }
    public function update_record($id)
    {
        $response["result"] = false;
        $this->ci->contract_status->fetch($id);
        $this->ci->contract_status->set_field("category_id", $this->ci->input->post("category_id"));
        $this->ci->contract_status->set_field("is_global", $this->ci->input->post("is_global"));
        if ($this->ci->contract_status->update()) {
            $status_id = $this->ci->contract_status->get_field("id");
            $this->ci->load->model("language");
            $languages = $this->ci->language->load_all();
            $response["result"] = false;
            $system_lang = substr($this->ci->session->userdata("AUTH_language"), 0, 2);
            foreach ($languages as $lang) {
                $name = $this->ci->input->post("name_" . $lang["name"]) && $this->ci->input->post("name_" . $lang["name"]) ? trim($this->ci->input->post("name_" . $lang["name"])) : trim($this->ci->input->post("name_" . $system_lang));
                $fields = ["status_id" => $id, "language_id" => $lang["id"], "name" => $name];
                $this->ci->contract_status_language->fetch(["status_id" => $id, "language_id" => $lang["id"]]);
                $this->ci->contract_status_language->set_fields($fields);
                if ($this->ci->contract_status_language->validate()) {
                    $query = [];
                    $query["select"] = ["contract_status_language.id"];
                    $query["where"] = [["contract_status_language.name", $name], ["contract_status_language.language_id", $lang["id"]], ["contract_status_language.status_id !=", $id]];
                    $query_result = $this->load_all($query);
                    if (!empty($query_result)) {
                        $response["validationErrors"]["name_" . $lang["name"]] = $this->ci->lang->line("already_exists");
                    } else {
                        $this->ci->contract_status_language->update();
                        $this->ci->contract_status_language->reset_fields();
                    }
                } else {
                    $validation_errors = $this->ci->contract_status_language->get("validationErrors");
                    $response["validationErrors"]["name_" . $system_lang] = $validation_errors["name"];
                }
                if (!isset($response["validationErrors"])) {
                    $response["id"] = $id;
                    $response["name"] = trim($this->ci->input->post("name_" . $system_lang));
                    $response["result"] = true;
                    $response["records"] = $this->ci->contract_status_language->load_data($id);
                    $response["type"] = "contract_statuses";
                }
            }
        } else {
            $response["validationErrors"] = $this->ci->contract_status->get("validationErrors");
        }
        return $response;
    }
    public function insert_id_record()
    {
        return $this->ci->db->insert_id();
    }
    private function save_checklist_items($step_id = 0, $checklist_items = [])
    {
        $this->ci->load->model('contract_workflow_step_checklist', "contract_workflow_step_checklistfactory");
        $this->ci->contract_workflow_step_checklist = $this->ci->contract_workflow_step_checklistfactory->get_instance();

        if (!empty($checklist_items) && is_array($checklist_items)) {
            $checklist_data = [];
            foreach ($checklist_items as $item) {
                if (!empty($item['item_text'])) {
                    $checklist_data[] = [
                        'step_id'     => $step_id,
                        'item_text'   => $item['item_text'],
                        'input_type'  => $item['input_type'] ?? 'yesno',
                        'is_required' => isset($item['is_required']) ? (int)$item['is_required'] : 1,
                        'sort_order'  => $item['sort_order'] ?? 0
                    ];
                }
            }

            if (!empty($checklist_data)) {
                $this->ci->contract_workflow_step_checklist->insert_on_duplicate_update_batch(
                    $checklist_data, ['step_id', 'item_text']
                );
            }
        }
    }


    private function save_step_functions($step_id=0, $functions=[])
    {
        $this->ci->load->model('contract_workflow_step_function',"contract_workflow_step_functionfactory");
        $this->ci->contract_workflow_step_function=$this->ci->contract_workflow_step_functionfactory->get_instance();
        // Insert new functions
        if (!empty($functions) && is_array($functions)) {
            $functions_data = [];
            foreach ($functions as $function) {
                if (!empty($function['function_name'])) {
                    $functions_data[] = [
                        'step_id' => $step_id,
                        'function_name' => $function['function_name'],
                        'label' => $function['label'],
                        'icon_class' => $function['icon_class'],
                        'data_action' => $function['data_action'],
                        'sort_order' => $function['sort_order'] ?? 0
                    ];
                }
            }

            if (!empty($functions_data)) {
               // $this->ci->contract_workflow_step_function->insert_batch($functions_data);
                $this->ci->contract_workflow_step_function->insert_on_duplicate_update_batch($functions_data,['step_id', 'function_name']);
            }
        }
    }

    public function load_all_records()
    {
        $query = ["select" => "contract_status_language.status_id, contract_status_language.name,languages.name as lang_name", "join" => [["languages", "languages.id = contract_status_language.language_id", "left"]], "order_by" => ["contract_status_language.name", "asc"]];
        return $this->load_all($query);
    }
    public function load_data($id = 0)
    {
        $query = ["select" => "contract_status.is_global, contract_status.category_id, status_category.type as category, contract_status_language.status_id, contract_status_language.name,languages.name as lang_name", "join" => [["contract_status", "contract_status.id = contract_status_language.status_id", "left"], ["status_category", "status_category.id = contract_status.category_id", "left"], ["languages", "languages.id = contract_status_language.language_id", "left"]], "order_by" => ["contract_status_language.name", "asc"]];
        if ($id) {
            $query["where"] = ["contract_status_language.status_id", $id];
        }
        $records = $this->load_all($query);
        $data = [];
        foreach ($records as $record) {
            $data[$record["status_id"]]["name_" . $record["lang_name"]] = $record["name"];
            $data[$record["status_id"]]["type"] = $record["is_global"] == 1 ? $this->ci->lang->line("global_status") : $this->ci->lang->line("transitional_status");
            $data[$record["status_id"]]["category"] = $this->ci->lang->line($record["category"]);
            $data[$record["status_id"]]["is_global"] = $record["is_global"];
            $data[$record["status_id"]]["category_id"] = $record["category_id"];
        }
        return $id ? $data[$id] : $data;
    }
    public function delete_record($id)
    {
        $num_rows = $this->ci->contract_status_language->count_field_rows("contract_workflow_status_relation", "status_id", $id);
        if (0 < $num_rows) {
            return false;
        }
        if ($this->ci->contract_status_language->delete(["where" => [["status_id", $id]]])) {
            $this->ci->contract_status->delete($id);
            return true;
        }
        return false;
    }
    public function load_list_per_language($lang = NULL)
    {
        $language = $lang ? $lang : strtolower(substr($this->ci->session->userdata("AUTH_language"), 0, 2));
        $query = ["select" => "contract_status_language.status_id, contract_status_language.name,languages.name as lang_name", "join" => [["languages", "languages.id = contract_status_language.language_id", "left"]], "where" => ["languages.name", $language]];
        $config_list = ["key" => "status_id", "value" => "name", "firstLine" => ["" => $this->ci->lang->line("none")]];
        return $this->load_list($query, $config_list);
    }
    public function load_all_per_language($lang = NULL)
    {
        $language = $lang ? $lang : strtolower(substr($this->ci->session->userdata("AUTH_language"), 0, 2));
        $query = ["select" => "contract_status_language.status_id as id, contract_status_language.name", "join" => [["languages", "languages.id = contract_status_language.language_id", "left"]], "where" => ["languages.name", $language]];
        return $this->load_all($query);
    }
      public function load_all_workflow_steps_per_language($workflow,$lang = NULL)
    {
        $language = $lang ? $lang : strtolower(substr($this->ci->session->userdata("AUTH_language"), 0, 2));
        $query = ["select" => "contract_status_language.status_id as id, contract_status_language.name", "join" => [["languages", "languages.id = contract_status_language.language_id", "left"]], "where" => ["languages.name", $language]];
        return $this->load_all($query);
    }
}
class mysql_Contract_status_language extends mysqli_Contract_status_language
{
}
class sqlsrv_Contract_status_language extends mysqli_Contract_status_language
{
    public function insert_id_record()
    {
        return $this->ci->db->insert_id();
    }
}

?>