<?php

if (!defined("BASEPATH")) {
    exit("No direct script access allowed");
}

class Legal_case_risks extends My_Model_Factory
{
}

class mysql_Legal_case_risks extends My_Model
{
    protected $modelName = "legal_case_risks";
    protected $_table = "legal_case_risks";
    protected $_listFieldName = "risk_type"; // default display field
    protected $_fieldsNames = [
        "id",
        "case_id",
        "risk_category",
        "riskLevel",
        "risk_type",
        "details",
        "mitigation",
        "responsible_actor_id",
        "status",
        "createdBy",
        "createdOn"
    ];

    public function __construct()
    {
        $this->ci =& get_instance();
        parent::__construct();

        // Validation rules
        $this->validate = [
            "case_id" => [
                "required" => true,
                "allowEmpty" => false,
                "rule" => "numeric",
                "message" => $this->ci->lang->line("cannot_be_blank_rule")
            ],
            "risk_category" => [
                "isRequired" => [
                    "required" => true,
                    "allowEmpty" => false,
                    "rule" => ["minLength", 1],
                    "message" => $this->ci->lang->line("cannot_be_blank_rule")
                ],
                "maxLength" => [
                    "rule" => ["maxLength", 100],
                    "message" => sprintf($this->ci->lang->line("max_characters"), 100)
                ]
            ],
            "riskLevel" => [
                "isRequired" => [
                    "required" => true,
                    "allowEmpty" => false,
                    "rule" => ["minLength", 1],
                    "message" => $this->ci->lang->line("cannot_be_blank_rule")
                ],
                "maxLength" => [
                    "rule" => ["maxLength", 50],
                    "message" => sprintf($this->ci->lang->line("max_characters"), 50)
                ]
            ],
            "risk_type" => [
                "isRequired" => [
                    "required" => true,
                    "allowEmpty" => false,
                    "rule" => ["minLength", 1],
                    "message" => $this->ci->lang->line("cannot_be_blank_rule")
                ],
                "maxLength" => [
                    "rule" => ["maxLength", 100],
                    "message" => sprintf($this->ci->lang->line("max_characters"), 100)
                ]
            ],
            "details" => [
                "maxLength" => [
                    "rule" => ["maxLength", 4000],
                    "message" => sprintf($this->ci->lang->line("max_characters"), 4000)
                ]
            ],
            "mitigation" => [
                "maxLength" => [
                    "rule" => ["maxLength", 4000],
                    "message" => sprintf($this->ci->lang->line("max_characters"), 4000)
                ]
            ],
            "status" => [
                "maxLength" => [
                    "rule" => ["maxLength", 50],
                    "message" => sprintf($this->ci->lang->line("max_characters"), 50)
                ]
            ],
            "createdBy" => [
                "required" => true,
                "allowEmpty" => false,
                "rule" => "numeric",
                "message" => $this->ci->lang->line("cannot_be_blank_rule")
            ]
        ];
    }

    /**
     * Load all risks for a given legal case
     */
    public function load_risks_by_case($case_id)
    {
        $query = [];
        $query["where"][] = ["case_id", $case_id];
        return $this->load_all($query);
    }

    /**
     * Load risks created by a given user
     */
    public function load_risks_by_user($user_id)
    {
        $query = [];
        $query["where"][] = ["createdBy", $user_id];
        return $this->load_all($query);
    }

    
}

class mysqli_Legal_case_risks extends mysql_Legal_case_risks {}
class sqlsrv_Legal_case_risks extends mysql_Legal_case_risks {

    public function load_cases_summary_report_with_risks()
    {
       
        $original_table = $this->_table;       
        $this->_table = "legal_cases_risks_and_feeNotes";
        $query["select"][] = ["*", false];
        $result = $this->load_all($query);
        $this->_table = $original_table;

        return $result;
    }
}
