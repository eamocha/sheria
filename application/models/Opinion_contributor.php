<?php

if (!defined("BASEPATH")) {
    exit("No direct script access allowed");
}
class Opinion_contributor extends My_Model
{
    protected $modelName = "opinion_contributor";
    protected $_table = "opinion_contributors";
    protected $_listFieldName = "opinion_id";
    protected $_fieldsNames = ["id", "opinion_id", "user_id"];
    public function __construct()
    {
        parent::__construct();
        $this->validate = ["opinion_id" => ["required" => true, "allowEmpty" => false, "rule" => "numeric", "message" => sprintf($this->ci->lang->line("required__is_numeric_rule"), $this->ci->lang->line("opinion"))], "user_id" => ["required" => true, "allowEmpty" => false, "rule" => "numeric", "message" => sprintf($this->ci->lang->line("required__is_numeric_rule"), $this->ci->lang->line("user"))]];
    }
    public function insert_contributors($data = [])
    {
        $delete_sql = [];
        $rows = [];
        extract($data);
        if (is_array($users)) {
            $sub_delete = "(opinion_id = '" . $opinion_id . "' and user_id NOT IN (0";
            foreach ($users as $user_id) {
                if (strcmp($user_id, "")) {
                    $rows[] = compact("opinion_id", "user_id");
                    $sub_delete .= ", '" . $user_id . "'";
                }
            }
            $sub_delete .= "))";
        } else {
            $sub_delete = "(opinion_id = '" . $opinion_id . "')";
        }
        $delete_sql[] = [$sub_delete];
        $this->prep_query(["or_where" => $delete_sql]);
        $this->ci->db->delete($this->_table);
        $this->reset_write();
        if (count($rows)) {
            $this->insert_on_duplicate_update_batch($rows, ["opinion_id", "user_id"]);
        }
        return true;
    }
}

?>