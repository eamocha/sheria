<?php

if (!defined("BASEPATH")) {
    exit("No direct script access allowed");
}
class Opinion_board extends My_Model
{
    protected $modelName = "opinion_board";
    protected $_table = "opinion_boards";
    protected $_listFieldName = "name";
    protected $_fieldsNames = ["id", "name", "createdOn", "createdBy", "modifiedOn", "modifiedBy"];
    protected $allowedNulls = ["createdOn", "createdBy", "modifiedOn", "modifiedBy"];
    protected $builtInLogs = true;
    public function __construct()
    {
        parent::__construct();
        $this->validate = ["name" => ["required" => true, "allowEmpty" => false, "rule" => ["maxLength", 255], "message" => sprintf($this->ci->lang->line("required__max_length_rule"), $this->ci->lang->line("name"), 255)]];
    }
}

?>