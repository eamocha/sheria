<?php



if (!defined("BASEPATH")) {
    exit("No direct script access allowed");
}
class Opinion_location extends My_Model
{
    protected $modelName = "opinion_location";
    protected $_table = "opinion_locations";
    protected $_listFieldName = "name";
    protected $_fieldsNames = ["id", "name"];
    public function __construct()
    {
        parent::__construct();
        $this->validate = ["name" => ["isRequired" => ["required" => true, "allowEmpty" => false, "rule" => ["minLength", 1], "message" => $this->ci->lang->line("cannot_be_blank_rule")], "isUnique" => ["rule" => "isUnique", "message" => $this->ci->lang->line("already_exists")], "maxLength" => ["rule" => ["maxLength", 255], "message" => sprintf($this->ci->lang->line("max_characters"), 255)]]];
    }
    public function api_lookup($term)
    {
        $configList = ["key" => "id", "value" => "name"];
        $configQury = ["select" => ["id,name", false]];
        if (!empty($term)) {
            $configQury["like"] = ["name", $term];
        }
        return $this->load_all($configQury, $configList);
    }
}

?>