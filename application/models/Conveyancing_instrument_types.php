<?php
if (!defined("BASEPATH")) {
    exit("No direct script access allowed");
}

class Conveyancing_instrument_types extends My_Model_Factory
{
}
class mysqli_Conveyancing_instrument_types extends My_Model
{
    protected $modelName = "conveyancing_instrument_types";
    protected $_table = "conveyancing_instrument_types";
    protected $_listFieldName = "name";
    protected $_fieldsNames = ["id", "name", "applies_to"];

    public function __construct()
    {
        parent::__construct();
        $this->validate = ["name" => ["required" => ["required" => true, "allowEmpty" => false, "rule" => ["minLength", 1], "message" => $this->ci->lang->line("cannot_be_blank_rule")], "unique" => ["rule" => ["combinedUnique", ["applies_to"]], "message" => $this->ci->lang->line("already_exists")], "maxLength" => ["rule" => ["maxLength", 255], "message" => sprintf($this->ci->lang->line("max_characters"), 255)]]];
    }
}

class mysql_Conveyancing_instrument_types extends mysqli_Conveyancing_instrument_types
{
}
class sqlsrv_Conveyancing_instrument_types extends mysqli_Conveyancing_instrument_types
{
    public function insert_new_record()
    {
        $this->ci->db->simple_query("INSERT INTO conveyancing_instrument_types DEFAULT VALUES");
        return $this->ci->db->insert_id();
    }
}