<?php

if (!defined("BASEPATH")) {
    exit("No direct script access allowed");
}

class Contract_workflow_step_function extends My_Model_Factory
{
}

class mysqli_Contract_workflow_step_function extends My_Model
{
    protected $modelName = "contract_workflow_step_functions";
    protected $_table = "contract_workflow_step_functions";
    protected $_fieldsNames = [
        "id", "step_id", "function_name", "label", "icon_class", "data_action", "created_at"
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
            "function_name" => [
                "required" => true,
                "allowEmpty" => false,
                "rule" => ["minLength", 1],
                "message" => $this->ci->lang->line("cannot_be_blank_rule")
            ],
            "label" => [
                "required" => true,
                "allowEmpty" => false,
                "rule" => ["minLength", 1],
                "message" => $this->ci->lang->line("cannot_be_blank_rule")
            ],
            "icon_class" => [
                "required" => true,
                "allowEmpty" => false,
                "rule" => ["minLength", 1],
                "message" => $this->ci->lang->line("cannot_be_blank_rule")
            ]
        ];
    }
}

class mysql_Contract_workflow_step_function extends mysqli_Contract_workflow_step_function
{
}

class sqlsrv_Contract_workflow_step_function extends mysqli_Contract_workflow_step_function
{
}

?>
