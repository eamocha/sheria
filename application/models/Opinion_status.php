<?php



if (!defined("BASEPATH")) {
    exit("No direct script access allowed");
}
class Opinion_status extends My_Model
{
    protected $modelName = "opinion_status";
    protected $_table = "opinion_statuses";
    protected $_listFieldName = "name";
    protected $_fieldsNames = ["id", "name", "category", "isGlobal"];
    public function __construct()
    {
        parent::__construct();
        $this->validate = ["name" => ["required" => ["required" => true, "allowEmpty" => false, "rule" => ["maxLength", 255], "message" => sprintf($this->ci->lang->line("required__max_length_rule"), $this->ci->lang->line("name"), 255)], "unique" => ["rule" => "isUnique", "message" => sprintf($this->ci->lang->line("is_unique_rule"), $this->ci->lang->line("name"))]]];
    }
    public function load_allowed_to_statuses($status_id, $workflow_id, $to_step_exception_id = 0)
    {
        $query = [];
        $query["select"] = ["opinion_statuses.id, opinion_statuses.name", false];
        $query["join"] = [["opinion_workflow_status_relation as relation", "relation.status_id = opinion_statuses.id", "left"]];
        $query["where"][] = ["opinion_statuses.isGlobal != ", "1", false];
        $query["where"][] = ["relation.workflow_id", $workflow_id];
        $query["where"][] = ["opinion_statuses.id !=", $status_id, false];
        $where = "";
        if ($to_step_exception_id) {
            $where = " AND opinion_workflow_status_transition.to_step != " . $to_step_exception_id . " ";
        }
        $query["where"][] = ["opinion_statuses.id NOT IN (SELECT opinion_workflow_status_transition.to_step from opinion_workflow_status_transition WHERE opinion_workflow_status_transition.from_step = '" . $status_id . "' AND opinion_workflow_status_transition.workflow_id = " . $workflow_id . " " . $where . " )", NULL, false];
        return parent::load_list($query);
    }
    public function load_list_workflow_statuses($workflow_id)
    {
        $query = [];
        $query["select"] = ["opinion_statuses.id, opinion_statuses.name", false];
        $query["join"][] = ["opinion_workflow_status_relation", "opinion_workflow_status_relation.status_id = opinion_statuses.id", "inner"];
        $query["where"][] = ["opinion_workflow_status_relation.workflow_id", $workflow_id];
        return $this->load_list($query);
    }
    public function loadWorkflowTransitions($workflow_id = NULL)
    {
        $table = $this->_table;
        $this->_table = "opinion_workflow_status_transition";
        $query["select"] = ["workflows.name as workflow_name, \r\n            opinion_workflow_status_transition.id, \r\n            opinion_workflow_status_transition.from_step, \r\n            opinion_workflow_status_transition.to_step, \r\n            fromStatus.name as fromStepName, \r\n            toStatus.name as toStepName, \r\n            opinion_workflow_status_transition.name, \r\n            opinion_workflow_status_transition.comments, \r\n            opinion_workflow_status_transition.workflow_id", false];
        $query["join"] = [["workflow_status as fromStatus", "fromStatus.id = opinion_workflow_status_transition.from_step", "left"], ["workflow_status as toStatus", "toStatus.id = opinion_workflow_status_transition.to_step", "left"], ["workflows ", "workflows.id = opinion_workflow_status_transition.workflow_id AND workflows.isDeleted=0", "inner"]];
        $query["where"][] = ["opinion_workflow_status_transition.workflow_id", $workflow_id];
        $query["order_by"] = ["fromStatus.name asc"];
        $return = parent::load_all($query);
        $this->_table = $table;
        return $return;
    }
}

?>