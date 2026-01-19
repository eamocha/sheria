<?php

if (!defined("BASEPATH")) {
    exit("No direct script access allowed");
}
class Contract_workflow extends My_Model_Factory
{
}
class mysql_Contract_workflow extends My_Model
{
    protected $modelName = "contract_workflow";
    protected $_table = "contract_workflow";
    protected $_listFieldName = "name";
    protected $_fieldsNames = ["id", "name", "category", "createdOn", "createdBy", "modifiedOn", "modifiedBy"];
    protected $builtInLogs = true;
    protected $system_workflow_id = 1;
    public function __construct()
    {
        parent::__construct();
        $this->validate = ["name" => ["required" => ["required" => true, "allowEmpty" => false, "rule" => ["minLength", 1], "message" => $this->ci->lang->line("cannot_be_blank_rule")], "maxLength" => ["rule" => ["maxLength", 255], "message" => sprintf($this->ci->lang->line("max_characters"), 255)], "isUnique" => ["rule" => "isUnique", "message" => sprintf($this->ci->lang->line("field_must_be_unique_rule"), $this->ci->lang->line("name"))]], "category" => ["required" => true, "allowEmpty" => false, "rule" => ["minLength", 1], "message" => $this->ci->lang->line("cannot_be_blank_rule")]];
    }
    public function load_workflows()
    {
        $query = [];
        $this->ci->load->model("language");
        $query["select"] = ["contract_workflow.id, contract_workflow.name, contract_workflow.category, contract_workflow.createdOn, contract_workflow.modifiedOn,\r\n                (SELECT GROUP_CONCAT(DISTINCT contract_type_language.name SEPARATOR ',' ) from contract_workflow_per_type  \r\n                LEFT JOIN contract_type_language ON contract_type_language.type_id = contract_workflow_per_type.type_id AND contract_type_language.language_id = " . $this->ci->language->get_id_by_session_lang() . "\r\n                where contract_workflow_per_type.workflow_id = contract_workflow.id) as contract_types_names", false];
        $query["order_by"] = ["contract_workflow.id desc"];
        $response = parent::load_all($query);
        return $response;
    }
    public function load_workflows_sla()
    {
        $table = $this->_table;
        $query = [];
        $query["select"] = ["contract_workflow.id, contract_workflow.name", false];
        $query["order_by"] = ["contract_workflow.id desc"];
        $response = parent::load_list($query);
        $this->_table = $table;
        return $response;
    }
    public function add($data)
    {
        $this->set_fields($data);
        $this->set_field("category", "default");
        if ($this->validate()) {
            if (isset($data["type_id"]) && !empty($data["type_id"])) {
                $workflows_exists = $this->load_contract_workflow_per_types($data["type_id"]);
                if ($workflows_exists) {
                    return ["result" => false, "display_message" => $this->ci->lang->line("workflow_type_exists")];
                }
                $this->insert();
                $workflow_id = $this->get_field("id");
                $this->ci->load->model("contract_workflow_per_type", "contract_workflow_per_typefactory");
                $this->ci->contract_workflow_per_type = $this->ci->contract_workflow_per_typefactory->get_instance();
                $error = false;
                foreach ($data["type_id"] as $type_id) {
                    $this->ci->contract_workflow_per_type->reset_fields();
                    $this->ci->contract_workflow_per_type->set_field("workflow_id", $workflow_id);
                    $this->ci->contract_workflow_per_type->set_field("type_id", $type_id);
                    if (!$this->ci->contract_workflow_per_type->insert()) {
                        $error = true;
                        if ($error) {
                            $this->delete($workflow_id);
                            return ["result" => false, "validation_errors" => $this->ci->contract_workflow_per_type->get("validationErrors")];
                        }
                    }
                    return ["result" => true, "workflow_id" => $workflow_id];
                }
            } else {
                return ["result" => false, "validation_errors" => ["type_id" => $this->ci->lang->line("cannot_be_blank_rule")]];
            }
        } else {
            return ["result" => false, "validation_errors" => $this->get("validationErrors")];
        }
    }

    public function validate_workflow_edit($data)
    {
        if (isset($data["type_id"]) && !empty($data["type_id"])) {
            $workflows_exists = $this->load_contract_workflow_per_types($data["type_id"], $data["id"]);
            if ($workflows_exists) {
                return ["result" => false, "display_message" => $this->ci->lang->line("contract_type_related_workflow")];
            }
            $this->ci->load->model("contract_workflow_per_type", "contract_workflow_per_typefactory");
            $this->ci->contract_workflow_per_type = $this->ci->contract_workflow_per_typefactory->get_instance();
            $related_types = $this->ci->contract_workflow_per_type->load_all(["where" => ["workflow_id", $data["id"]]]);
            $old_type_ids = array_column($related_types, "type_id");
            $diff_types = array_diff($old_type_ids, $data["type_id"]);
            if (!empty($diff_types)) {
                $related_contracts = $this->check_used_workflow($data["id"], $diff_types);
                if (!empty($related_contracts)) {
                    return ["result" => false, "related_contracts" => $related_contracts];
                }
            }
            return ["result" => true];
        }
        return ["result" => false, "validation_errors" => ["type_id" => $this->ci->lang->line("cannot_be_blank_rule")]];
    }
    public function edit_workflow($data)
{
    $this->fetch($data["id"]);
    $this->set_field("name", $data["name"]);
    
    if (!$this->update()) {
        return false;
    }
    
    // Delete existing type associations
    $this->ci->contract_workflow_per_type->delete(["where" => ["workflow_id", $data["id"]]]);
    
    // Insert new type associations
    foreach ($data["type_id"] as $type_id) {
        $this->ci->contract_workflow_per_type->reset_fields();
        $this->ci->contract_workflow_per_type->set_field("workflow_id", $data["id"]);
        $this->ci->contract_workflow_per_type->set_field("type_id", $type_id);
        
        if (!$this->ci->contract_workflow_per_type->insert()) {
            return false; // Failed to insert type
        }
    }
    
    return true; // All operations successful
}
    public function load_contract_workflow_per_types($type_ids, $worflow_id = 0)
    {
        $ids = implode(",", $type_ids);
        $_table = $this->_table;
        $this->_table = "contract_workflow_per_type";
        $query["select"] = ["contract_workflow_per_type.workflow_id", false];
        $query["join"] = [["contract_workflow", "contract_workflow.id=contract_workflow_per_type.workflow_id", "inner"]];
        $query["where"][] = ["contract_workflow_per_type.type_id IN (" . $ids . ")"];
        if ($worflow_id) {
            $query["where"][] = ["contract_workflow_per_type.workflow_id != " . $worflow_id];
        }
        $result = parent::load($query);
        $this->_table = $_table;
        return $result;
    }
    public function load_all_statuses_per_workflow($workflow_id, $list_view = false)
    {
        $this->ci->load->model("language");
        $lang_id = $this->ci->language->get_id_by_session_lang();
        $query = [];
        $table = $this->_table;
        $this->_table = "contract_workflow_status_relation as relation";
        $query["select"] = ["contract_status.id, relation.start_point, relation.approval_start_point, csl.name as step_name,csl.responsible_user_roles,csl.step_icon,csl.activity,csl.step_input,csl.step_output,contract_status.category_id, contract_status.is_global", false];
        $query["join"][] = ["contract_status", "contract_status.id = relation.status_id", "left"];
        $query["join"][] = ["contract_status_language csl", "csl.status_id = contract_status.id AND csl.language_id = " . $lang_id, "left"];
        $query["where"][] = ["relation.workflow_id", $workflow_id];
        $query["order_by"] = ["csl.name asc"];
        $response = $list_view ? parent::load_list($query, ["key" => "id", "value" => "name"]) : parent::load_all($query);
        $this->_table = $table;
        return $response;
    }
    public function load_all_statuses_per_workflow_sla($workflow_id)
    {
        $this->ci->load->model("language");
        $lang_id = $this->ci->language->get_id_by_session_lang();
        $query = [];
        $response = [];
        $table = $this->_table;
        $this->_table = "contract_workflow_status_relation as relation";
        $query["select"] = ["contract_status.id, contract_status_language.name", false];
        $query["join"][] = ["contract_status", "contract_status.id = relation.status_id", "left"];
        $query["join"][] = ["contract_status_language", "contract_status_language.status_id = contract_status.id AND contract_status_language.language_id = " . $lang_id, "left"];
        $query["where"][] = ["relation.workflow_id", $workflow_id];
        $query["order_by"] = ["contract_status_language.name asc"];
        $response = parent::load_list($query);
        $this->_table = $table;
        return $response;
    }
    public function load_all_transitions_per_workflow($workflow_id, $from_status = 0)
    {
        $this->ci->load->model("language");
        $lang_id = $this->ci->language->get_id_by_session_lang();
        $table = $this->_table;
        $this->_table = "contract_workflow_status_transition as transition";
        $query = [];
        $query["select"] = ["transition.id, transition.from_step, transition.to_step, from_status.name as from_step_name, to_status.name as to_step_name, transition.name, transition.comment", false];
        $query["join"] = [["contract_status_language as from_status", "from_status.status_id = transition.from_step AND from_status.language_id = " . $lang_id, "left"], ["contract_status_language as to_status", "to_status.status_id = transition.to_step AND to_status.language_id = " . $lang_id, "left"]];
        $query["where"][] = ["workflow_id", $workflow_id];
        if ($from_status) {
            $query["where"][] = ["transition.from_step", $from_status];
        }
        $return = parent::load_all($query);
        $this->_table = $table;
        return $return;
    }
    //loading steps transitions
    public function load_status_transitions($status_id, $workflow_id)
    {
        $table = $this->_table;
        $this->_table = "contract_workflow_status_transition";
        $query = [];
        $query["select"] = ["contract_workflow_status_transition.id", false];
        $query["where"][] = ["workflow_id", $workflow_id];
        $query["where"][] = ["(from_step = " . $status_id . " OR to_step = " . $status_id . ")"];
        $return = parent::load_all($query);
        $this->_table = $table;
        return $return;
    }
    public function delete_workflow($workflow_id)
    {
        $contracts_related = $this->check_workflow_relation($workflow_id);
        if (!$contracts_related) {
            $transitions = $this->ci->contract_workflow_status_transition->load_all(["where" => ["workflow_id", $workflow_id]]);
            foreach ($transitions as $transition) {
                $this->ci->contract_workflow_status_transition_permission->delete(["where" => ["transition_id", $transition["id"]]]);
                $this->ci->contract_workflow_status_transition_screen_field->delete(["where" => ["transition_id", $transition["id"]]]);
            }
            $this->ci->contract_workflow_status_transition->delete(["where" => ["workflow_id", $workflow_id]]);
            $results = $this->ci->contract_workflow_status_relation->load_all(["where" => ["workflow_id", $workflow_id]]);
            $this->ci->contract_workflow_status_relation->delete(["where" => ["workflow_id", $workflow_id]]);
            $this->ci->load->model("contract_workflow_per_type", "contract_workflow_per_typefactory");
            $this->ci->contract_workflow_per_type = $this->ci->contract_workflow_per_typefactory->get_instance();
            $this->ci->contract_workflow_per_type->delete(["where" => ["workflow_id", $workflow_id]]);
            if ($this->ci->contract_workflow->delete(["where" => ["id", $workflow_id]])) {
                return true;
            }
        }
        return false;
    }
    public function check_workflow_relation($id)
    {
        $table = $this->_table;
        $this->_table = "contract";
        $query = [];
        $query["join"][] = ["contract_workflow", "contract_workflow.id = contract.workflow_id", "inner"];
        $query["join"][] = ["contract_workflow_status_relation", "contract_workflow_status_relation.status_id = contract.status_id AND contract_workflow_status_relation.workflow_id = contract_workflow.id", "left"];
        $query["where"][] = ["contract_workflow.id", $id];
        $contract_status = parent::load_all($query);
        $this->_table = $table;
        if ($contract_status) {
            return true;
        }
        return false;
    }
    public function load_workflow_contract_status_per_type($type_id)
    {
        $query = [];
        $query["select"] = ["contract_status.id as status, contract_workflow.id as workflow_id", false];
        $query["join"][] = ["contract_workflow_per_type", "contract_workflow.id = contract_workflow_per_type.workflow_id", "inner"];
        $query["join"][] = ["contract_workflow_status_relation", "contract_workflow_status_relation.workflow_id = contract_workflow.id AND contract_workflow_status_relation.start_point = 1", "inner"];
        $query["join"][] = ["contract_status", "contract_workflow_status_relation.status_id = contract_status.id", "inner"];
        $query["where"][] = ["contract_workflow_per_type.type_id", $type_id];
        $return = parent::load($query);
        return $return;
    }
    public function load_default_system_workflow()
    {
        $table = $this->_table;
        $this->_table = "contract_workflow";
        $query = [];
        $query["select"] = ["contract_status.id as status, contract_workflow.id as workflow_id", false];
        $query["join"][] = ["contract_workflow_status_relation", "contract_workflow_status_relation.workflow_id = contract_workflow.id AND contract_workflow_status_relation.start_point = 1", "inner"];
        $query["join"][] = ["contract_status", "contract_workflow_status_relation.status_id = contract_status.id", "inner"];
        $query["where"][] = ["contract_workflow.category", "system"];
        $return = parent::load($query);
        $this->_table = $table;
        return $return;
    }
    public function check_used_workflow($id, $types)
    {
        $query = [];
        $this->ci->load->model("language");
        $lang_id = $this->ci->language->get_id_by_session_lang();
        $table = $this->_table;
        $this->_table = "contract_status";
        $query["select"] = ["DISTINCT contract.status_id,count(contract.id) as contracts_count, status.name, contract.type_id as type_id", false];
        $query["join"][] = ["contract_status_language as status", "status.status_id = contract_status.id", "inner"];
        $query["join"][] = ["contract", "contract.status_id = contract_status.id", "inner"];
        $query["where"][] = ["contract.workflow_id", $id];
        $query["where"][] = ["status.language_id", $lang_id];
        $type_ids = implode(",", $types);
        if ($type_ids) {
            $query["where"][] = ["contract.type_id IN (" . $type_ids . ")"];
        }
        $query["group_by"] = ["contract_status.id"];
        $response = parent::load_all($query);
        $this->_table = $table;
        return $response;
    }
    public function update_contract_status_workflow($data)
    {
        $this->ci->db->where(["status_id" => $data["old_status"], "type_id" => $data["type"], "workflow_id" => $data["workflow_id"]]);
        return $this->ci->db->update("contract", ["status_id" => $data["new_status"], "workflow_id" => $this->system_workflow_id, "modifiedBy" => $this->ci->is_auth->get_user_id(), "modifiedOn" => date("Y-m-d H:i:s")]);
    }
    public function load_workflow_transitions($workflow_id = NULL)
    {
        $lang_id = $this->ci->language->get_id_by_session_lang();
        $table = $this->_table;
        $this->_table = "contract_workflow_status_transition";
        $query = [];
        $query["select"] = ["contract_workflow.name as workflow_name,contract_workflow_status_transition.id, contract_workflow_status_transition.from_step, contract_workflow_status_transition.to_step,from_status.name as fromStepName, to_status.name as toStepName, contract_workflow_status_transition.name, contract_workflow_status_transition.comment, contract_workflow_status_transition.workflow_id", false];
        $query["join"] = [["contract_status_language as from_status", "from_status.status_id = contract_workflow_status_transition.from_step AND from_status.language_id = " . $lang_id, "left"], ["contract_status_language as to_status", "to_status.status_id = contract_workflow_status_transition.to_step AND to_status.language_id = " . $lang_id, "left"], ["contract_workflow ", "contract_workflow.id = contract_workflow_status_transition.workflow_id", "inner"]];
        $query["where"][] = ["contract_workflow_status_transition.workflow_id", $workflow_id];
        $query["order_by"] = ["from_status.name asc"];
        $return = parent::load_all($query);
        $this->_table = $table;
        return $return;
    }
}
class mysqli_Contract_workflow extends mysql_Contract_workflow
{
}
class sqlsrv_Contract_workflow extends mysql_Contract_workflow
{
    public function load_workflows()
    {
        $query = [];
        $this->ci->load->model("language");
        $query["select"] = ["contract_workflow.id, contract_workflow.name, contract_workflow.category, contract_workflow.createdOn, contract_workflow.modifiedOn,\r\n                contract_types_names = STUFF((SELECT DISTINCT ', ' + contract_type_language.name from contract_workflow_per_type \r\n                LEFT JOIN contract_type_language ON contract_type_language.type_id = contract_workflow_per_type.type_id AND contract_type_language.language_id = " . $this->ci->language->get_id_by_session_lang() . "\r\n                where contract_workflow_per_type.workflow_id = contract_workflow.id FOR XML PATH('')), 1, 1, '')", false];
        $query["order_by"] = ["contract_workflow.id desc"];
        $response = parent::load_all($query);
        return $response;
    }
    public function check_used_workflow($id, $types)
    {
        $query = [];
        $this->ci->load->model("language");
        $lang_id = $this->ci->language->get_id_by_session_lang();
        $table = $this->_table;
        $this->_table = "contract_status";
        $query["select"] = ["DISTINCT contract.status_id,count(contract.id) as contracts_count, status.name, contract.type_id as type_id", false];
        $query["join"][] = ["contract_status_language as status", "status.status_id = contract_status.id", "inner"];
        $query["join"][] = ["contract", "contract.status_id = contract_status.id", "inner"];
        $query["where"][] = ["contract.workflow_id", $id];
        $query["where"][] = ["status.language_id", $lang_id];
        $type_ids = implode(",", $types);
        if ($type_ids) {
            $query["where"][] = ["contract.type_id IN (" . $type_ids . ")"];
        }
        $query["group_by"] = ["contract.id, contract.status_id, status.name, contract.type_id"];
        $response = parent::load_all($query);
        $this->_table = $table;
        return $response;
    }
}

?>