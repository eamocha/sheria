<?php

if (!defined("BASEPATH")) {
    exit("No direct script access allowed");
}
class Correspondence_document_type extends My_Model_Factory
{
}
class mysqli_Correspondence_document_type extends My_Model
{
    protected $modelName = "correspondence_document_type";
    protected $_table = "correspondence_document_types";
      protected $builtInLogs = true;
      protected $_fieldsNames = ["id","name","description","createdOn", "modifiedOn","createdBy","modifiedBy"];

    public function __construct()
    {
        parent::__construct();
        $this->validate = [ "name" => ["required" => ["required" => true, "allowEmpty" => false, "rule" => ["minLength", 1], "message" => $this->ci->lang->line("cannot_be_blank_rule")], "maxLength" => ["rule" => ["maxLength", 255], "message" => sprintf($this->ci->lang->line("max_characters"), 255)]]];
    }
    public function load_all_records()
    {
        $query = ["select" => "correspondence_document_types.*"];
        return $this->load_all($query);
    }
    public function load_record($id)
    {
        $query = ["select" => "correspondence_document_types.*",
            "where" => ["correspondence_document_types.id", $id]];
        $records = $this->load_all($query);
        return $records;
    }



}
class mysql_Correspondence_document_type extends mysqli_Correspondence_document_type
{
}
class sqlsrv_Correspondence_document_type extends mysqli_Correspondence_document_type
{
}

