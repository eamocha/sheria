<?php



if (!defined("BASEPATH")) {
    exit("No direct script access allowed");
}
class Opinion_workflow_status_transition_screen_field extends My_Model_Factory
{
}
class mysql_Opinion_workflow_status_transition_screen_field extends My_Model
{
    protected $modelName = "opinion_workflow_status_transition_screen_field";
    protected $_table = "opinion_workflow_status_transition_screen_fields";
    protected $_listFieldName = "data";
    protected $_fieldsNames = ["id", "transition", "data"];
    public function __construct()
    {
        $this->ci =& get_instance();
        parent::__construct();
        $this->validate = ["transition" => ["required" => true, "allowEmpty" => false, "rule" => "numeric", "message" => $this->ci->lang->line("cannot_be_blank_rule")]];
    }
    public function check_field_relation($field = 0)
    {
        $records = $this->load_all();
        if (!empty($records)) {
            foreach ($records as $record) {
                $data = unserialize($record["data"]);
                if (in_array($field, array_keys($data))) {
                    return false;
                }
            }
        }
        return true;
    }
}
class mysqli_Opinion_workflow_status_transition_screen_field extends mysql_Opinion_workflow_status_transition_screen_field
{
}
class sqlsrv_Opinion_workflow_status_transition_screen_field extends mysql_Opinion_workflow_status_transition_screen_field
{
}

?>