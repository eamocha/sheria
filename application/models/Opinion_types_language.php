<?php



if (!defined("BASEPATH")) {
    exit("No direct script access allowed");
}
class Opinion_types_language extends My_Model
{
    protected $modelName = "opinion_types_language";
    protected $_table = "opinion_types_languages";
    protected $_listFieldName = "name";
    protected $_fieldsNames = ["id", "opinion_type_id", "language_id", "name", "applies_to"];
    protected $categoryValues = ["Opinions", "Conveyancing"];
    public function __construct()
    {
        parent::__construct();
        $this->validate = ["opinion_type_id" => ["required" => true, "allowEmpty" => false, "rule" => "numeric", "message" => sprintf($this->ci->lang->line("is_numeric_rule"), $this->ci->lang->line("opinion_type"))], "language_id" => ["required" => true, "allowEmpty" => false, "rule" => "numeric", "message" => sprintf($this->ci->lang->line("is_numeric_rule"), $this->ci->lang->line("language"))], "name" => ["required" => ["required" => true, "allowEmpty" => false, "rule" => ["minLength", 1], "message" => $this->ci->lang->line("cannot_be_blank_rule")], "unique" => ["rule" => ["combinedUnique", ["language_id"]], "message" => $this->ci->lang->line("already_exists")], "maxLength" => ["rule" => ["maxLength", 255], "message" => sprintf($this->ci->lang->line("max_characters"), 255)]]];
    }
}

?>