<?php

if (!defined("BASEPATH")) {
    exit("No direct script access allowed");
}

class Contract_workflow_status_transition_log extends My_Model_Factory
{
}

class mysql_Contract_workflow_status_transition_log extends My_Model
{
    protected $modelName = "contract_workflow_status_transition_log";
    protected $_table = "contract_workflow_status_transition_log";
  
    protected $_fieldsNames = [
        "id",
        "contract_id",
        "workflow_id",
        "transition_id",
        "from_step",
        "to_step",
        "status",
        "comments",
        "created_by",
        "created_on"
    ];

    public function __construct()
    {
        $this->ci =& get_instance();
        parent::__construct();

        $this->validate = [
            "contract_id" => [
                "required" => true,
                "allowEmpty" => false,
                "rule" => "numeric",
                "message" => $this->ci->lang->line("cannot_be_blank_rule")
            ],
            "workflow_id" => [
                "required" => true,
                "allowEmpty" => false,
                "rule" => "numeric",
                "message" => $this->ci->lang->line("cannot_be_blank_rule")
            ],
            "transition_id" => [
                "required" => true,
                "allowEmpty" => false,
                "rule" => "numeric",
                "message" => $this->ci->lang->line("cannot_be_blank_rule")
            ],
            "from_step" => [
                "required" => true,
                "allowEmpty" => false,
                "rule" => "numeric",
                "message" => $this->ci->lang->line("cannot_be_blank_rule")
            ],
            "to_step" => [
                "required" => true,
                "allowEmpty" => false,
                "rule" => "numeric",
                "message" => $this->ci->lang->line("cannot_be_blank_rule")
            ],
            "status" => [
                "isRequired" => [
                    "required" => true,
                    "allowEmpty" => false,
                    "rule" => ["minLength", 1],
                    "message" => $this->ci->lang->line("cannot_be_blank_rule")
                ],
                "maxLength" => [
                    "rule" => ["maxLength", 255],
                    "message" => sprintf($this->ci->lang->line("max_characters"), 255)
                ]
            ],
            "comments" => [
                "maxLength" => [
                    "rule" => ["maxLength", 500],
                    "message" => sprintf($this->ci->lang->line("max_characters"), 500)
                ]
            ],
            "created_by" => [
                "required" => true,
                "allowEmpty" => false,
                "rule" => "numeric",
                "message" => $this->ci->lang->line("cannot_be_blank_rule")
            ],
        ];
    }

    /**
     * Load all transition logs for a specific contract
     */
    public function load_logs_by_contract($contract_id)
    {
        $query = [];
        $query["where"][] = ["contract_id", $contract_id];
        $query["order_by"] = ["created_on" => "DESC"];
        return parent::load_all($query);
    }

    /**
     * Load all logs for a specific workflow
     */
    public function load_logs_by_workflow($workflow_id)
    {
        $query = [];
        $query["where"][] = ["workflow_id", $workflow_id];
        $query["order_by"] = ["created_on" => "DESC"];
        return parent::load_all($query);
    }
}

class mysqli_Contract_workflow_status_transition_log extends mysql_Contract_workflow_status_transition_log
{
}

class sqlsrv_Contract_workflow_status_transition_log extends mysql_Contract_workflow_status_transition_log
{
}

?>
