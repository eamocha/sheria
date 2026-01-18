<?php

if (!defined("BASEPATH")) {
    exit("No direct script access allowed");
}

class Department extends My_Model
{
    protected $modelName = "department";
    protected $_table = "departments";
    protected $_listFieldName = "name";
    protected $_fieldsNames = ["id", "name"];

    public function __construct()
    {
        parent::__construct();
        $this->validate = [
            "name" => [
                "required" => [
                    "required" => true,
                    "allowEmpty" => false,
                    "rule" => ["maxLength", 100],
                    "message" => sprintf($this->ci->lang->line("required__max_length_rule"), $this->ci->lang->line("name"), 100)
                ],
                "unique" => [
                    "rule" => "isUnique",
                    "message" => sprintf($this->ci->lang->line("is_unique_rule"), $this->ci->lang->line("name"))
                ]
            ]
        ];
    }

    /**
     * Loads all active departments.
     * @return array
     */
    public function load_all_departments() // Renamed method
    {
        return $this->get_all(['order_by' => ['name ASC']]);
    }
    public function lookup($term)
    {
        $term = $this->ci->db->escape_like_str($term);
        $table = $this->_table;
        $configList = ["key" => "id", "value" => "location"];
        $configQury["select"] = ["id,  name", false];
        $configQury["where"] = [["name LIKE '%" . $term . "%'", NULL, false], ["name <> ''", NULL, false]];
        $return = $this->load_all($configQury, $configList);
        $this->_table = $table;
        return $return;
    }
}