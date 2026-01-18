<?php



if (!defined("BASEPATH")) {
    exit("No direct script access allowed");
}
class Opinion_workflow_status_transition_history extends My_Model
{
    protected $modelName = "opinion_workflow_status_transition_history";
    protected $_table = "opinion_workflow_status_transition_history";
    protected $_listFieldName = "legal_case_id";
    protected $_fieldsNames = ["id", "opinion_id", "from_step", "to_step", "user_id", "changed_on"];
    public function __construct()
    {
        parent::__construct();
        $this->validate = ["opinion_id" => ["required" => ["required" => true, "allowEmpty" => false, "rule" => "numeric", "message" => $this->ci->lang->line("cannot_be_blank_rule")]], "from_step" => ["required" => true, "allowEmpty" => false, "rule" => "numeric", "message" => $this->ci->lang->line("cannot_be_blank_rule")], "to_step" => ["required" => true, "allowEmpty" => false, "rule" => "numeric", "message" => $this->ci->lang->line("cannot_be_blank_rule")], "user_id" => ["required" => true, "allowEmpty" => false, "rule" => "numeric", "message" => $this->ci->lang->line("cannot_be_blank_rule")], "changed_on" => ["required" => true, "allowEmpty" => false, "rule" => "date", "message" => $this->ci->lang->line("cannot_be_blank_rule")]];
    }
    public function log_transition_history($opinion_id, $from, $to, $user_id)
    {
        $this->reset_fields();
        $this->set_field("opinion_id", $opinion_id);
        $this->set_field("from_step", $from);
        $this->set_field("to_step", $to);
        $this->set_field("user_id", $user_id);
        $this->set_field("changed_on", date("Y-m-d H:i:s"));
        return $this->insert();
    }
}

?>