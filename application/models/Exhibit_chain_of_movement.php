<?php

if (!defined("BASEPATH")) {
    exit("No direct script access allowed");
}
class Exhibit_chain_of_movement extends My_Model_Factory{

}
class mysql_Exhibit_chain_of_movement extends My_Model
{
    protected $modelName = "exhibit_chain_of_movement";
    protected $_table = "exhibit_chain_of_movement";
    protected $_listFieldName = "purpose";
    protected $_fieldsNames = [
        "id",
        "transfer_from_id",
        "transfer_to_id",
        "purpose",
        "remarks",
        "action_date_time",
        "officer_receiving",
        "createdBy",
        "createdOn",
        "modifiedBy",
        "modifiedOn"
    ];
    protected $allowedNulls = ["purpose", "remarks", "officer_receiving", "createdBy", "createdOn", "modifiedBy", "modifiedOn"];
    protected $builtInLogs = true;

    public function __construct()
    {
        parent::__construct();
        $this->validate = [
            "transfer_from_id" => [
                "required" => true,
                "allowEmpty" => false,
                "rule" => ["numeric"],
                "message" => $this->ci->lang->line("required")
            ],
            "transfer_to_id" => [
                "required" => true,
                "allowEmpty" => false,
                "rule" => ["numeric"],
                "message" => $this->ci->lang->line("required")
            ]
        ];
    }
/**
 * get_movement_by_exhibit_id
 */
public function get_movement_by_exhibit_id($id)
{
    $query = [];
    $this->_table = 'exhibit_chain_of_movement ecm';
    $query["select"] = [            "ecm.*,              CONCAT(created.firstName, ' ', created.lastName) as createdBy,
     CONCAT(officer.firstName, ' ', officer.lastName) as officer_receivingName,
    frm.name as location_fromName,lto.name as location_toName",  false        ];
    $query["where"][] = ["ecm.exhibit_id", $id];
    $query["join"] = [ ["user_profiles created", "created.user_id = ecm.createdBy", "left"],
        ["user_profiles officer", "officer.user_id = ecm.officer_receiving", "left"],
        ["exhibit_locations frm", "frm.id = ecm.transfer_from_id", "left"],
        ["exhibit_locations lto", "lto.id = ecm.transfer_to_id", "left"],
    ];
    return $this->load_all($query);


}
}

class sqlsrv_Exhibit_chain_of_movement extends mysql_Exhibit_chain_of_movement {
}
class mysqli_Exhibit_chain_of_movement extends mysql_Exhibit_chain_of_movement{

}
