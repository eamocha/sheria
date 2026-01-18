<?php

if (!defined("BASEPATH")) {
    exit("No direct script access allowed");
}

class Exhibit_activities_log extends My_Model_Factory
{}
class mysql_Exhibit_activities_log extends My_Model
{
    protected $modelName = "exhibit_activities_log";
    protected $_table = "exhibit_activities_log";
    protected $_listFieldName = "subject";
    protected $_fieldsNames = [
        "id",
        "exhibit_id",
        "subject",
        "remarks",
        "createdBy",
        "createdOn",
        "modifiedBy",
        "modifiedOn",
        "note_type","tags","requires_followup","priority"


    ];
    protected $allowedNulls = ["remarks", "createdBy", "createdOn"];
    protected $builtInLogs = true;

    public function __construct()
    {
        parent::__construct();
        $this->validate = [
            "exhibit_id" => [
                "required" => true,
                "allowEmpty" => false,
                "rule" => ["numeric"],
                "message" => $this->ci->lang->line("required")
            ],
            "subject" => [
                "required" => true,
                "allowEmpty" => false,
                "rule" => ["maxLength", 255],
                "message" => sprintf(
                    $this->ci->lang->line("required__max_length_rule"),
                    $this->ci->lang->line("subject"),
                    255
                )
            ]
        ];
    }

    public function get_activity_log_by_exhibit_id($id)
    {
        $query = [];
        $this->_table = 'exhibit_activities_log eal';
        $query["select"] = [            "eal.*,              CONCAT(created.firstName, ' ', created.lastName) as createdBy ",  false        ];
        $query["where"][] = ["eal.exhibit_id", $id];
        $query["join"] = [ ["user_profiles created", "created.user_id = eal.createdBy", "left"],
        ];
        return $this->load_all($query);

    }
}

class mysqli_Exhibit_activities_log extends mysql_Exhibit_activities_log
{}
class sqlsrv_Exhibit_activities_log extends mysql_Exhibit_activities_log
{}
