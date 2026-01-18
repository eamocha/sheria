<?php



if (!defined("BASEPATH")) {
    exit("No direct script access allowed");
}
class Opinion_workflow_status_transition_permission extends My_Model
{
    protected $modelName = "opinion_workflow_status_transition_permission";
    protected $_table = "opinion_workflow_status_transition_permissions";
    protected $_fieldsNames = ["id", "transition", "users", "user_groups"];
    public function __construct()
    {
        parent::__construct();
        $this->validate = ["transition" => ["required" => true, "allowEmpty" => false, "rule" => "numeric", "message" => $this->ci->lang->line("cannot_be_blank_rule")]];
    }
    public function save_value($param)
    {
        $data_set = ["transition" => $param["transition"], "users" => $param["users"], "user_groups" => $param["user_groups"]];
        return $this->insert_on_duplicate_key_update($data_set, ["transition"]);
    }
    public function delete_transition_permission($transition_id)
    {
        $query = [];
        $query["where"][] = ["transition = " . $transition_id];
        return $this->delete($query);
    }
    public function load_permissions($transition_id = "")
    {
        $query = [];
        $query["select"] = ["transition,users,user_groups", false];
        if ($transition_id) {
            $query["where"][] = ["transition = " . $transition_id];
        }
        return $this->load_all($query);
    }
}

?>