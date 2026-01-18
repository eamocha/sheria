<?php

if (!defined("BASEPATH")) {
    exit("No direct script access allowed");
}
class Opinion_board_column_option extends My_Model
{
    protected $modelName = "opinion_board_column_option";
    protected $_table = "opinion_board_column_options";
    protected $_listFieldName = "name";
    protected $_fieldsNames = ["id", "opinion_board_id", "opinion_board_column_id", "opinion_status_id"];
    protected $allowedNulls = ["opinion_board_id"];
    public function __construct()
    {
        parent::__construct();
        $this->validate = ["opinion_board_id" => ["required" => true, "allowEmpty" => false, "rule" => "numeric", "message" => sprintf($this->ci->lang->line("is_numeric_rule"), $this->ci->lang->line("opinion_board"))], "opinion_board_column_id" => ["required" => true, "allowEmpty" => false, "rule" => "numeric", "message" => sprintf($this->ci->lang->line("is_numeric_rule"), $this->ci->lang->line("opinion_board_column"))], "opinion_status_id" => ["required" => true, "allowEmpty" => false, "rule" => "numeric", "message" => sprintf($this->ci->lang->line("is_numeric_rule"), $this->ci->lang->line("opinion_status"))]];
    }
}

?>