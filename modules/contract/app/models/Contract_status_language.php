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
        
        $all_validations_passed = true;
        $response["validationErrors"] = [];
        
        foreach ($languages as $lang) {
            $name = $this->ci->input->post("name_" . $lang["name"]) && $this->ci->input->post("name_" . $lang["name"]) ? trim($this->ci->input->post("name_" . $lang["name"])) : trim($this->ci->input->post("name_" . $system_lang));
            $responsible_user_roles= $this->ci->input->post("responsible_user_roles");
            $step_icon= $this->ci->input->post("step_icon");
            $activity= $this->ci->input->post("activity");
            $step_input= $this->ci->input->post("step_input");
            $step_output= $this->ci->input->post("step_output");
            
            $fields = ["status_id" => $status_id, "language_id" => $lang["id"], "name" => $name, "responsible_user_roles"=>$responsible_user_roles, "activity"=>$activity, "step_icon"=>$step_icon, "step_input"=>$step_input, "step_output"=>$step_output];
            
            $this->ci->contract_status_language->set_fields($fields);
            
            if ($this->ci->contract_status_language->validate()) {
                $query = [];
                $query["select"] = ["contract_status_language.id"];
                $query["where"] = [["contract_status_language.name", $name], ["contract_status_language.language_id", $lang["id"]]];
                $query_result = $this->load_all($query);
                
                if (!empty($query_result)) {
                    $response["validationErrors"]["name_" . $lang["name"]] = $this->ci->lang->line("already_exists");
                    $all_validations_passed = false;
                } else {
                    $this->ci->contract_status_language->insert();
                    $this->ci->contract_status_language->reset_fields();
                }
            } else {
                $validation_errors = $this->ci->contract_status_language->get("validationErrors");
                $response["validationErrors"]["name_" . $system_lang] = $validation_errors["name"];
                $all_validations_passed = false;
            }
        }
        
        // Only proceed with workflow and functions if ALL language validations passed
        if ($all_validations_passed && empty($response["validationErrors"])) {
            $workflow_id = $this->ci->input->post("workflow_id", true);
            
            if ($workflow_id) {
                $this->ci->load->model("contract_workflow_status_relation", "contract_workflow_status_relationfactory");
                $this->ci->contract_workflow_status_relation = $this->ci->contract_workflow_status_relationfactory->get_instance();
                
                $related_statuses = $this->ci->contract_workflow_status_relation->load_all(["where" => ["workflow_id", $workflow_id]]);
                $start_point = empty($related_statuses) ? 1 : 0;
                $step_exist = $this->ci->contract_workflow_status_relation->load_all(["where" => [["workflow_id", $workflow_id],["status_id", $status_id]]]);

                if (empty($step_exist)) {
                    $this->ci->contract_workflow_status_relation->set_field("workflow_id", $workflow_id);
                    $this->ci->contract_workflow_status_relation->set_field("status_id", $status_id);
                    $this->ci->contract_workflow_status_relation->set_field("start_point", $start_point);
                    $this->ci->contract_workflow_status_relation->set_field("approval_start_point", 0);
                    $this->ci->contract_workflow_status_relation->insert();
                }
                
                // Insert functions and checklist only if status_id is valid and all validations passed
                $this->save_step_functions($status_id, $this->ci->input->post("functions"));
                $this->save_checklist_items($status_id, $this->ci->input->post("checklist"));
            }
            
            $response["id"] = $status_id;
            $response["name"] = trim($this->ci->input->post("name_" . $system_lang));
            $response["result"] = true;
            $response["records"] = $this->ci->contract_status_language->load_data($status_id);
            $response["type"] = "contract_statuses";
        } else {
            // Rollback: Delete any partially inserted records
            $this->ci->contract_status_language->delete(["where" => [["status_id", $status_id]]]);
            $this->ci->contract_status->delete(["where" => [["id", $status_id]]]);
            
            // Ensure result is false when validation fails
            $response["result"] = false;
        }
    } else {
        $response["validationErrors"] = "Step could not be saved. Ensure its not a duplicate of existing contract step";
        $response["result"] = false;
    }
    
    return $response;
}
//**same as update record above */
public function update_step($id)
{
    error_log("update_record called with id: " . $id);
    error_log("POST data: " . print_r($this->ci->input->post(), true));
    
    $response["result"] = false;
    $response["validationErrors"] = [];
    
    // 1. Update main status record
    $this->ci->contract_status->fetch($id);
    $this->ci->contract_status->set_field("category_id", $this->ci->input->post("category_id"));
    $this->ci->contract_status->set_field("is_global", $this->ci->input->post("is_global"));
    
    if (!$this->ci->contract_status->update()) {
        $validationErrors = $this->ci->contract_status->get("validationErrors");
        error_log("contract_status update failed: " . print_r($validationErrors, true));
        $response["validationErrors"] = $validationErrors;
        return $response;
    }
    
    error_log("contract_status updated successfully");
    
    $this->ci->load->model("language");
    $languages = $this->ci->language->load_all();
    $system_lang = substr($this->ci->session->userdata("AUTH_language"), 0, 2);
    $all_validations_passed = true;

    foreach ($languages as $lang) {
        $lang_name_key = "name_" . $lang["name"];
        $name = $this->ci->input->post($lang_name_key) ? trim($this->ci->input->post($lang_name_key)) : trim($this->ci->input->post("name_" . $system_lang));
        
        error_log("Processing language: " . $lang["name"] . " with name: " . $name);
        
        // Get all the step-specific fields
        $responsible_user_roles = $this->ci->input->post("responsible_user_roles");
        $step_icon = $this->ci->input->post("step_icon");
        $activity = $this->ci->input->post("activity");
        $step_input = $this->ci->input->post("step_input");
        $step_output = $this->ci->input->post("step_output");

        $fields = [
            "status_id" => $id, 
            "language_id" => $lang["id"], 
            "name" => $name,
            "responsible_user_roles" => $responsible_user_roles,
            "activity" => $activity,
            "step_icon" => $step_icon,
            "step_input" => $step_input,
            "step_output" => $step_output
        ];

        // Try to fetch existing record
        $existing_record = $this->ci->contract_status_language->fetch(["status_id" => $id, "language_id" => $lang["id"]]);
        
        if ($existing_record) {
            $this->ci->contract_status_language->set_fields($fields);
            
            if ($this->ci->contract_status_language->validate()) {
                // Check for duplicates (excluding current record)
                $query = [
                    "select" => ["contract_status_language.id"],
                    "where"  => [
                        ["contract_status_language.name", $name], 
                        ["contract_status_language.language_id", $lang["id"]], 
                        ["contract_status_language.status_id !=", $id]
                    ]
                ];
                
                $duplicates = $this->load_all($query);
                if (!empty($duplicates)) {
                    error_log("Duplicate found for name: " . $name . " in language: " . $lang["name"]);
                    $response["validationErrors"][$lang_name_key] = $this->ci->lang->line("already_exists");
                    $all_validations_passed = false;
                } else {
                    if ($this->ci->contract_status_language->update()) {
                        error_log("Updated contract_status_language for language: " . $lang["name"]);
                    } else {
                        error_log("Failed to update contract_status_language for language: " . $lang["name"]);
                        $all_validations_passed = false;
                    }
                }
            } else {
                $errs = $this->ci->contract_status_language->get("validationErrors");
                error_log("Validation failed for language " . $lang["name"] . ": " . print_r($errs, true));
                $response["validationErrors"][$lang_name_key] = $errs["name"];
                $all_validations_passed = false;
            }
        } else {
            error_log("No existing record found for language: " . $lang["name"] . ", creating new");
            // If record doesn't exist, create it
            $this->ci->contract_status_language->set_fields($fields);
            if ($this->ci->contract_status_language->validate()) {
                $this->ci->contract_status_language->insert();
                error_log("Created new contract_status_language for language: " . $lang["name"]);
            } else {
                $errs = $this->ci->contract_status_language->get("validationErrors");
                error_log("Validation failed for new language record " . $lang["name"] . ": " . print_r($errs, true));
                $response["validationErrors"][$lang_name_key] = $errs["name"];
                $all_validations_passed = false;
            }
        }
    }

    // Only proceed with workflow, functions and checklist if ALL language validations passed
    if ($all_validations_passed && empty($response["validationErrors"])) {
        error_log("All validations passed, proceeding with functions and checklist");
        
        // Get functions and checklist from POST
        $functions = $this->ci->input->post("functions");
        $checklist = $this->ci->input->post("checklist");
        
        error_log("Functions to save: " . print_r($functions, true));
        error_log("Checklist to save: " . print_r($checklist, true));
        
        // SAFELY update functions and checklist (preserve existing data)
        $this->save_step_functions($id, $functions);
        $this->save_checklist_items($id, $checklist);
        
        $response["id"] = $id;
        $response["name"] = trim($this->ci->input->post("name_" . $system_lang));
        $response["result"] = true;
        $response["records"] = $this->ci->contract_status_language->load_data($id);
        $response["type"] = "contract_statuses";
        
        error_log("Update successful, returning response: " . print_r($response, true));
    } else {
        // If validation failed, ensure result is false
        $response["result"] = false;
        error_log("Validations failed, returning: " . print_r($response, true));
    }

    return $response;
}
    public function update_recordold($id)
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
        
        foreach ($checklist_items as $index => $item) {
            if (!empty($item['item_text'])) {
                $checklist_data[] = [
                    'step_id'     => $step_id,
                    'item_text'   => trim($item['item_text']),
                    'input_type'  => $item['input_type'] ?? 'yesno',
                    'is_required' => isset($item['is_required']) ? (int)$item['is_required'] : 1,
                    'sort_order'  => $item['sort_order'] ?? $index,
                     
                ];
            }
        }

        if (!empty($checklist_data)) {
         
            $this->ci->contract_workflow_step_checklist->insert_on_duplicate_update_batch(
                $checklist_data, 
                ['step_id', 'item_text'], // Unique keys
                ['input_type', 'is_required', 'sort_order'] // Fields to update on duplicate
            );
        }
    }
}
    private function save_checklist_itemsold($step_id = 0, $checklist_items = [])
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

private function save_step_functions($step_id = 0, $functions = [])
{
    $this->ci->load->model('contract_workflow_step_function', "contract_workflow_step_functionfactory");
    $this->ci->contract_workflow_step_function = $this->ci->contract_workflow_step_functionfactory->get_instance();
    
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
                    'sort_order' => $function['sort_order'] ?? 0,
                    'created_at' => date('Y-m-d H:i:s'),
                ];
            }
        }

        if (!empty($functions_data)) {
            // This will update existing records and insert new ones
            $this->ci->contract_workflow_step_function->insert_on_duplicate_update_batch(
                $functions_data, 
                ['step_id', 'function_name'] // Unique keys
            );
        }
    }
}
    private function save_step_functionsold($step_id=0, $functions=[])
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
                        'sort_order' => $function['sort_order'] ?? 0,
                        'created_at' => date('Y-m-d H:i:s'),
                        
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