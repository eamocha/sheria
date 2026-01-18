<?php

if (!defined("BASEPATH")) {
    exit("No direct script access allowed");
}
class System_configuration extends My_Model
{
    protected $modelName = "system_configuration";
    protected $_table = "system_configurations";
    protected $_listFieldName = "keyValue";
    protected $_fieldsNames = ["keyName", "keyValue"];
    protected $allowedNulls = ["keyValue"];
    public function __construct()
    {
        parent::__construct();
        $this->validate = ["keyName" => ["required" => true, "allowEmpty" => false, "message" => $this->ci->lang->line("empty")], "keyValue" => ["required" => false, "allowEmpty" => true, "message" => $this->ci->lang->line("empty")]];
    }
    public function get_value_by_key($keyName)
    {
        if (!empty($keyName)) {
//            $result = $this->load(["where" => ["keyName", $keyName]]);
//            return unserialize($result["keyValue"]);
            $result = $this->load(["where" => ["keyName", $keyName]]);
            if ($result && is_array($result) && isset($result["keyValue"])) {
                return unserialize($result["keyValue"]);
            }
        }
        return "";
    }
    public function set_value_by_key($keyName, $keyValue)
    {
        $keyValue = !empty($keyValue) ? serialize($keyValue) : "";
        $dataSet = ["keyName" => $keyName, "keyValue" => $keyValue];
        if ($this->insert_on_duplicate_key_update($dataSet, ["keyName"])) {
            return true;
        }
        return false;
    }
    public function get_values()
    {
        $return = [];
        $values = $this->load_all();
        foreach ($values as $val) {
            $return[$val["keyName"]] = $val["keyValue"];
        }
        return $return;
    }
}

