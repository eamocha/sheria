<?php

if (!defined("BASEPATH")) {
    exit("No direct script access allowed");
}
class Exhibit_location extends My_Model_Factory{

}
class mysql_Exhibit_location extends My_Model
{
    protected $modelName = "exhibit_location";
    protected $_table = "exhibit_locations";
    protected $_listFieldName = "name";
    protected $_fieldsNames = ["id", "name", "longitude", "latitude", "description", "createdOn", "createdBy", "modifiedOn", "modifiedBy"];
    protected $allowedNulls = ["longitude", "latitude", "description", "createdOn", "createdBy", "modifiedOn", "modifiedBy"];
    protected $builtInLogs = true;

    public function __construct()
    {
        parent::__construct();
        $this->validate = [
            "name" => [
                "required" => true,
                "allowEmpty" => false,
                "rule" => ["maxLength", 255],
                "message" => sprintf($this->ci->lang->line("required__max_length_rule"), $this->ci->lang->line("name"), 255)
            ]
        ];
    }


    public function load_all_exhibit_locations()
    {

    }


    public function get_exhibit_location($id)
    {

    }
    public function get_exhibit_location_by_id($id){

    }

    public function lookup($term)
    {
        $term = $this->ci->db->escape_like_str($term);
        $table = $this->_table;
        $this->_table = "exhibit_locations";
        $configList = ["key" => "exhibit_locations.id", "value" => "location"];
        $configQury["select"] = ["exhibit_locations.id, exhibit_locations.name as location", false];
        $configQury["where"] = [["exhibit_locations.name LIKE '%" . $term . "%'", NULL, false], ["exhibit_locations.name <> ''", NULL, false]];
        $return = $this->load_all($configQury, $configList);
        $this->_table = $table;
        return $return;
    }
}
class sqlsrv_Exhibit_location extends mysql_Exhibit_location {
    }
class mysqli_Exhibit_location extends mysql_Exhibit_location{

}