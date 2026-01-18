<?php

if (!defined("BASEPATH")) {
    exit("No direct script access allowed");
}
class Contract_workflow_status_relation extends My_Model_Factory
{
}
class mysql_Contract_workflow_status_relation extends My_Model
{
    protected $modelName = "contract_workflow_status_relation";
    protected $_table = "contract_workflow_status_relation";
    protected $_listFieldName = "workflow_id";
    protected $_fieldsNames = ["id", "workflow_id", "status_id", "start_point", "approval_start_point"];
    protected $ci;
    public function __construct()
    {
        $this->ci =& get_instance();
        parent::__construct();
        $this->validate = ["workflow_id" => ["required" => true, "allowEmpty" => false, "rule" => ["minLength", 1], "message" => $this->ci->lang->line("cannot_be_blank_rule")], "status_id" => ["required" => true, "allowEmpty" => false, "rule" => ["minLength", 1], "message" => $this->ci->lang->line("cannot_be_blank_rule")]];
    }
    public function move_contracts_to_start_point_status($old_status, $new_status, $workflow_id)
    {
        $data = ["status_id" => $new_status];
        $this->ci->db->where("status_id", $old_status)->where("workflow_id", $workflow_id)->update("contract", $data);
    }
    public function get_approval_start_point_status($workflow_id)
    {
        $this->ci->load->model("contract_status_language", "contract_status_languagefactory");
        $this->ci->contract_status_language = $this->ci->contract_status_languagefactory->get_instance();
        $this->ci->contract_workflow_status_relation->fetch(["workflow_id" => $workflow_id, "approval_start_point" => 1]);
        $this->ci->load->model("language");
        $this->ci->contract_status_language->fetch(["status_id" => $this->ci->contract_workflow_status_relation->get_field("status_id"), "language_id" => $this->ci->language->get_id_by_session_lang()]);
        return $this->ci->contract_status_language->get_field("name");
    }
    
}
class mysqli_Contract_workflow_status_relation extends mysql_Contract_workflow_status_relation
{
}
class sqlsrv_Contract_workflow_status_relation extends mysql_Contract_workflow_status_relation
{
    //get all steps in a certain workflow
    public function get_all_steps_in_workflow_without_progression($workflow_id)
    { $this->ci->load->model("language");
        $lang_id = $this->ci->language->get_id_by_session_lang();
        $query = [];
        $query["select"] = ["contract_workflow_status_relation.status_id as relation_id,contract_workflow_status_relation.start_point, contract_workflow_status_relation.approval_start_point,csl.id ,csl.status_id as step_id,csl.step_icon, csl.name as step_name,csl.description,csl.responsible_user_roles,csl.step_icon,step_output,activity,step_input", false];
        $query["join"] = [["contract_status_language csl", "csl.status_id = contract_workflow_status_relation.status_id AND csl.language_id = " . $lang_id, "left"]];
        $query["where"] = ["contract_workflow_status_relation.workflow_id", $workflow_id];
      
      $result = $this->load_all($query);
        return $result;
    }  
public function get_all_steps_in_workflow($workflow_id, $contract_id = null)
{
    $this->ci->load->model("language");
    $lang_id = $this->ci->language->get_id_by_session_lang();

    $query = [];

    $query["select"] = ["
        csl.status_id AS step_id,
        csl.id,
        contract_workflow_status_relation.status_id AS relation_id,
        contract_workflow_status_relation.start_point,
        contract_workflow_status_relation.approval_start_point,
        csl.name AS step_name,
        csl.description,
        csl.responsible_user_roles,
        csl.step_icon,
        csl.step_output,
        csl.activity,
        csl.step_input,
        MAX(log.id) AS log_entry_id,
        MAX(log.status) AS step_status
    "];

    $query["join"] = [
        ["
            contract_status_language csl",
            "csl.status_id = contract_workflow_status_relation.status_id 
             AND csl.language_id = " . $lang_id,
            "left"
        ],
        ["
            contract_workflow_status_transition t",
            "t.from_step = contract_workflow_status_relation.status_id 
             AND t.workflow_id = contract_workflow_status_relation.workflow_id",
            "left"
        ],
        ["
            contract_workflow_status_transition_log log",
            "log.transition_id = t.id" . ($contract_id ? " AND log.contract_id = " . intval($contract_id) : ""),
            "left"
        ]
    ];

    $query["where"] = [
        ["contract_workflow_status_relation.workflow_id", $workflow_id]
    ];

    $query["group_by"] = ["
        csl.status_id,
        csl.id,
        contract_workflow_status_relation.status_id,
        contract_workflow_status_relation.start_point,
        contract_workflow_status_relation.approval_start_point,
        csl.name,
        csl.description,
        csl.responsible_user_roles,
        csl.step_icon,
        csl.step_output,
        csl.activity,
        csl.step_input
    "];

    $result = $this->load_all($query);
    return $result;
}


    
    //get the combined workflow steps, functions and checklists
    public function get_combined_workflow_steps($workflow_id)
    {
        $this->ci->load->model("language");
        $lang_id = $this->ci->language->get_id_by_session_lang();
        $query = [];
        $query["select"] = ["contract_workflow_status_relation.status_id as relation_id,csl.id as step_id, csl.name as title,csl.responsible_user_roles,csl.step_icon,step_output,activity,step_input", false];
        $query["join"] = [["contract_status_language csl", "csl.status_id = contract_workflow_status_relation.status_id AND csl.language_id = " . $lang_id, "left"]];
        $query["where"] = ["contract_workflow_status_relation.workflow_id", $workflow_id];
        $steps = $this->load_all($query);
        
        $this->ci->load->model("contract_workflow_step_function", "contract_workflow_step_functionfactory");
        $this->ci->contract_workflow_step_function = $this->ci->contract_workflow_step_functionfactory->get_instance();
        $this->ci->load->model("contract_workflow_step_checklist", "contract_workflow_step_checklistfactory");
        $this->ci->contract_workflow_step_checklist = $this->ci->contract_workflow_step_checklistfactory->get_instance();
        $combined = [];
        foreach ($steps as $step) {
            $step_id = $step["step_id"];
            $functions = $this->ci->contract_workflow_step_function->load_all(["where" => ["step_id", $step_id]]);
            $checklists = $this->ci->contract_workflow_step_checklist->load_all(["where" => ["step_id", $step_id]]);
            $combined[] = ["step" => $step, "functions" => $functions, "checklists" => $checklists];
        }
        return $combined;   
    } 
    //get all steps in a certain workflow that have progression of a certain contract

    public function get_progressed_steps_in_workflow($workflow_id, $contract_id)
{
        $this->ci->load->model("language");
    $lang_id = $this->ci->language->get_id_by_session_lang();

    // Base query (same structure as before)
    $query = [];
    $query["select"] = ["
        csl.status_id AS step_id,
        csl.id,
        contract_workflow_status_relation.status_id AS relation_id,
        contract_workflow_status_relation.start_point,
        contract_workflow_status_relation.approval_start_point,
        csl.name AS step_name,
        csl.description,
        csl.responsible_user_roles,
        csl.step_icon,
        csl.step_output,
        csl.activity,
        csl.step_input,
        MAX(log.id) AS log_entry_id,
        MAX(log.status) AS step_status
    "];

    $query["join"] = [
        ["
            contract_status_language csl",
            "csl.status_id = contract_workflow_status_relation.status_id 
             AND csl.language_id = " . $lang_id,
            "left"
        ],
        ["
            contract_workflow_status_transition t",
            "t.from_step = contract_workflow_status_relation.status_id 
             AND t.workflow_id = contract_workflow_status_relation.workflow_id",
            "left"
        ],
        ["
            contract_workflow_status_transition_log log",
            "log.transition_id = t.id AND log.contract_id = " . intval($contract_id),
            "left"
        ]
    ];

    $query["where"] = [
        ["contract_workflow_status_relation.workflow_id", $workflow_id]
    ];

    $query["group_by"] = ["
        csl.status_id,
        csl.id,
        contract_workflow_status_relation.status_id,
        contract_workflow_status_relation.start_point,
        contract_workflow_status_relation.approval_start_point,
        csl.name,
        csl.description,
        csl.responsible_user_roles,
        csl.step_icon,
        csl.step_output,
        csl.activity,
        csl.step_input
    "];

    $result = $this->load_all($query);

    // 🔎 Post-process to determine what to return
    $hasLogs = array_filter($result, function ($row) {
        return !empty($row['log_entry_id']);
    });

    if (empty($hasLogs)) {
        // No logs at all → return only the first step with start_point = 1
        foreach ($result as $row) {
            if ((int)$row['start_point'] === 1) {
                return [$row]; // return as array with only this step
            }
        }
        return []; // failsafe: no start_point found
    }

    // Else → return only steps that have logs
    return array_values($hasLogs);
}

}

?>