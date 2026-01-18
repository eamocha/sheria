<?php

if (!defined("BASEPATH")) {
    exit("No direct script access allowed");
}

class Contract_workflow_step_checklist extends My_Model_Factory
{
}

class mysqli_Contract_workflow_step_checklist extends My_Model
{
    protected $modelName = "contract_workflow_step_checklist";
    protected $_table = "contract_workflow_step_checklist";
    protected $_fieldsNames = [
        "id", "step_id", "item_text", "input_type", "is_required", "created_at"
    ];

    public function __construct()
    {
        parent::__construct();
        $this->validate = [
            "step_id" => [
                "required" => true,
                "allowEmpty" => false,
                "rule" => "numeric",
                "message" => $this->ci->lang->line("cannot_be_blank_rule")
            ],
            "item_text" => [
                "required" => true,
                "allowEmpty" => false,
                "rule" => ["minLength", 1],
                "message" => $this->ci->lang->line("cannot_be_blank_rule")
            ],
            "input_type" => [
                "required" => true,
                "allowEmpty" => false,
                "rule" => ["minLength", 1],
                "message" => $this->ci->lang->line("cannot_be_blank_rule")
            ],
            "is_required" => [
                "required" => true,
                "allowEmpty" => false,
                "rule" => "boolean",
                "message" => $this->ci->lang->line("cannot_be_blank_rule")
            ]
        ];
    }
}

class mysql_Contract_workflow_step_checklist extends mysqli_Contract_workflow_step_checklist
{
}

class sqlsrv_Contract_workflow_step_checklist extends mysqli_Contract_workflow_step_checklist
{
}

?>
