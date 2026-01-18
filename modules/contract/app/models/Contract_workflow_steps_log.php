<?php

if (!defined("BASEPATH")) {
    exit("No direct script access allowed");
}
class Contract_workflow_steps_log extends My_Model_Factory
{
}
class mysql_Contract_workflow_steps_log extends My_Model
{
    protected $modelName = "contract_workflow_steps_log";
    protected $_table = "contract_workflow_steps_log";
    protected $_listFieldName = "action_type";
    protected $_fieldsNames = [
        "id",
        "step_id",
        "contract_id",
        "user_id",
        "action_type",
        "details",
        "createdBy"
    ];
    public function __construct()
    {
        $this->ci =& get_instance();
        parent::__construct();
        $this->validate = [
            "action_type" => [
                "required" => true,
                "allowEmpty" => false,
                "rule" => ["minLength", 1],
                "message" => $this->ci->lang->line("cannot_be_blank_rule")
            ],
           
            "createdBy" => [
                "required" => true,
                "allowEmpty" => false,
                "rule" => ["minLength", 1],
                "message" => $this->ci->lang->line("cannot_be_blank_rule")
            ]
        ];
    }
  
}

class mysqli_Contract_workflow_steps_log extends mysql_Contract_workflow_steps_log
{
}
class sqlsrv_Contract_workflow_steps_log extends mysql_Contract_workflow_steps_log
{
}   