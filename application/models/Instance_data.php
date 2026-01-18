<?php

if (!defined("BASEPATH")) {
    exit("No direct script access allowed");
}
class Instance_data extends My_Model
{
    protected $modelName = "instance_data";
    protected $_table = "instance_data";
    protected $_fieldsNames = ["keyName", "keyValue"];
    protected $allowedNulls = ["keyValue"];
    public function __construct()
    {
        parent::__construct();
    }
    public function get_value_by_key($keyName)
    {
        if (!empty($keyName)) {
            return $this->load(["where" => ["keyName", $keyName]]);
        }
        return "";
    }
    public function set_value_by_key($keyName, $keyValue)
    {
        $dataSet = ["keyName" => $keyName, "keyValue" => $keyValue];
        if ($this->insert_on_duplicate_key_update($dataSet, ["keyName"])) {
            $this->ci->session->set_userdata("instance_data", $this->get_values());
            return true;
        }
        return false;
    }
    public function set_values($data)
    {
        foreach ($data as $key => $value) {
            $this->ci->db->where("keyName", $key)->update($this->_table, ["keyValue" => $value]);
        }
        $this->ci->session->set_userdata("instance_data", $this->get_values());
        return true;
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
    public function validate_sqlsrv_version($version)
    {
        if ($this->ci->db->dbdriver == "sqlsrv") {
            $query = $this->ci->db->query("SELECT @@VERSION");
            if (0 < $query->num_rows()) {
                $results = $query->result_array();
                if (isset($results[0][""]) && !empty($results[0][""])) {
                    return strpos($results[0][""], "Microsoft SQL Server " . $version) !== false;
                }
            }
        }
        return false;
    }
}

