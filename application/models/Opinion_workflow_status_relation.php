<?php



if (!defined("BASEPATH")) {
    exit("No direct script access allowed");
}
class Opinion_workflow_status_relation extends My_Model_Factory
{
}
class mysql_Opinion_workflow_status_relation extends My_Model
{
    protected $modelName = "opinion_workflow_status_relation";
    protected $_table = "opinion_workflow_status_relation";
    protected $_listFieldName = "workflow_id";
    protected $_fieldsNames = ["id", "workflow_id", "status_id", "start_point"];
    protected $ci;
    public function __construct()
    {
        $this->ci =& get_instance();
        parent::__construct();
        $this->validate = ["status_id" => ["required" => true, "allowEmpty" => false, "rule" => "numeric", "message" => $this->ci->lang->line("cannot_be_blank_rule")]];
    }
    public function move_opinions_to_start_point_status($old_status, $new_status, $workflow_id)
    {
        $data = ["opinion_status_id" => $new_status];
        $this->ci->db->where("opinion_status_id", $old_status)->where("workflow", $workflow_id)->update("opinions", $data);
    }
}
class mysqli_Opinion_workflow_status_relation extends mysql_Opinion_workflow_status_relation
{
}
class sqlsrv_Opinion_workflow_status_relation extends mysql_Opinion_workflow_status_relation
{
}

?>