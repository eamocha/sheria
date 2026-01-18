<?php



if (!defined("BASEPATH")) {
    exit("No direct script access allowed");
}
class Opinion_user extends My_Model
{
    protected $modelName = "opinion_user";
    protected $_table = "opinion_users";
    protected $_listFieldName = "opinion_id";
    protected $_fieldsNames = ["id", "opinion_id", "user_id"];
    public function __construct()
    {
        parent::__construct();
        $this->validate = ["opinion_id" => ["required" => true, "allowEmpty" => false, "rule" => "numeric", "message" => sprintf($this->ci->lang->line("required__is_numeric_rule"), $this->ci->lang->line("opinion"))], "user_id" => ["required" => true, "allowEmpty" => false, "rule" => "numeric", "message" => sprintf($this->ci->lang->line("required__is_numeric_rule"), $this->ci->lang->line("user"))]];
    }
}

?>