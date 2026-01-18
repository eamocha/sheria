<?php



if (!defined("BASEPATH")) {
    exit("No direct script access allowed");
}
class Opinion_workflow_status_transition extends My_Model_Factory
{
}
class mysql_Opinion_workflow_status_transition extends My_Model
{
    protected $modelName = "opinion_workflow_status_transition";
    protected $_table = "opinion_workflow_status_transition";
    protected $_listFieldName = "name";
    protected $_fieldsNames = ["id", "workflow_id", "from_step", "to_step", "name", "comments"];
    public function __construct()
    {
        $this->ci =& get_instance();
        parent::__construct();
        $this->validate = ["workflow_id" => ["required" => true, "allowEmpty" => false, "rule" => "numeric", "message" => $this->ci->lang->line("cannot_be_blank_rule")], "from_step" => ["required" => true, "allowEmpty" => false, "rule" => "numeric", "message" => $this->ci->lang->line("cannot_be_blank_rule")], "to_step" => ["required" => true, "allowEmpty" => false, "rule" => "numeric", "message" => $this->ci->lang->line("cannot_be_blank_rule")], "name" => ["isRequired" => ["required" => true, "allowEmpty" => false, "rule" => ["minLength", 1], "message" => $this->ci->lang->line("cannot_be_blank_rule")], "maxLength" => ["rule" => ["maxLength", 255], "message" => sprintf($this->ci->lang->line("max_characters"), 255)]]];
    }
    public function load_available_steps($status, $workflow, $logged_user_id = "", $logged_user_group_id = "")
    {
        $user_group_id = $logged_user_group_id ? $logged_user_group_id : $this->ci->session->userdata("AUTH_user_group_id");
        $user_id = $logged_user_id ? $logged_user_id : $this->ci->session->userdata("AUTH_user_id");
        $this->ci->load->model("opinion_status");
        $this->ci->opinion_status->fetch($status);
        if ($this->ci->opinion_status->get_field("isGlobal") == 1) {
            $available_statuses = $this->ci->opinion_status->load_list_workflow_statuses($workflow);
            unset($available_statuses[$status]);
        } else {
            $transitions = $this->load_available_statuses_per_workflow($status, $workflow);
            $global_statuses = $this->load_global_statuses_per_workflow($workflow);
            $available_statuses = $transitions + $global_statuses;
        }
        $status_transitions = $this->load_available_transitions($status, $workflow);
        $transition_permissions = $this->load_transitions_permissions($workflow);
        $permitted_transition = true;
        foreach ($status_transitions as $key => $transition) {
            if (isset($transition["id"])) {
                foreach ($transition_permissions as $permission) {
                    if (intval($permission["transition"]) === intval($transition["id"])) {
                        if (in_array(intval($user_id), explode(",", $permission["users"])) || in_array(intval($user_group_id), explode(",", $permission["user_groups"]))) {
                            $permitted_transition = true;
                            if (!$permitted_transition) {
                                unset($available_statuses[$transition["to_step"]]);
                                unset($status_transitions[$key]);
                            }
                        } else {
                            $permitted_transition = false;
                        }
                    }
                }
            }
        }
        return ["status_transitions" => $status_transitions, "available_statuses" => $available_statuses];
    }
    public function load_available_statuses_per_workflow($status, $workflow)
    {
        $query["select"] = ["opinion_statuses.id as id, opinion_statuses.name as name", false];
        $query["join"] = [["opinion_statuses", "opinion_statuses.id = opinion_workflow_status_transition.to_step", "left"], ["opinion_workflow_status_relation", "opinion_workflow_status_relation.status_id = opinion_statuses.id", "left"]];
        $query["where"][] = ["opinion_workflow_status_transition.from_step", $status];
        $query["where"][] = ["opinion_workflow_status_transition.workflow_id", $workflow];
        $response = parent::load_list($query, ["key" => "id", "value" => "name"]);
        return $response;
    }
    public function load_global_statuses_per_workflow($workflow)
    {
        $query = [];
        $table = $this->_table;
        $this->_table = "opinion_statuses";
        $query["select"] = ["opinion_statuses.id, opinion_statuses.name", false];
        $query["join"][] = ["opinion_workflow_status_relation", "opinion_workflow_status_relation.status_id = opinion_statuses.id", "inner"];
        $query["where"][] = ["opinion_workflow_status_relation.workflow_id", $workflow];
        $query["where"][] = ["opinion_statuses.isGlobal", 1];
        $response = $this->load_list($query, ["key" => "id", "value" => "name"]);
        $this->_table = $table;
        return $response;
    }
    public function load_available_transitions($from_status, $workflow)
    {
        $query = [];
        $query["select"] = ["opinion_workflow_status_transition.id, opinion_workflow_status_transition.from_step, opinion_workflow_status_transition.to_step,from_status.name as from_status_name, to_status.name as to_status_name, opinion_workflow_status_transition.name, opinion_workflow_status_transition.comments", false];
        $query["join"] = [["workflow_status as from_status", "from_status.id = opinion_workflow_status_transition.from_step", "left"], ["workflow_status as to_status", "to_status.id = opinion_workflow_status_transition.to_step", "left"]];
        $query["where"][] = ["from_step", $from_status];
        $query["where"][] = ["workflow_id", $workflow];
        $response = parent::load_all($query);
        return array_combine(array_column($response, "to_step"), $response);
    }
    public function load_transitions_permissions($workflow = "")
    {
        $query = [];
        $query["select"] = ["permissions.transition,permissions.users,permissions.user_groups, opinion_workflow_status_transition.workflow_id", false];
        $query["join"] = [["opinion_workflow_status_transition_permissions as permissions", "permissions.transition = opinion_workflow_status_transition.id", "inner"]];
        if ($workflow) {
            $query["where"][] = ["opinion_workflow_status_transition.workflow_id = " . $workflow];
        }
        return $this->load_all($query);
    }
    public function check_transition_allowed($opinion_id, $status_id, $logged_user_id, $logged_user_grp_id = "")
    {
        if (!$this->ci->opinion->fetch($opinion_id)) {
            return false;
        }
        $workflow_applicable = 0 < $this->ci->opinion->get_field("workflow") ? $this->ci->opinion->get_field("workflow") : 1;
        $old_status = $this->ci->opinion->get_field("opinion_status_id");
        $allowed_statuses = $this->load_available_steps($old_status, $workflow_applicable, $logged_user_id, $logged_user_grp_id);
        if ($status_id === $old_status || !in_array($status_id, array_keys($allowed_statuses["available_statuses"]))) {
            return false;
        }
        return true;
    }
    public function load_all_transitions_per_workflow($logged_user_id = "", $logged_user_group_id = "")
    {
        $user_group_id = $logged_user_group_id ? $logged_user_group_id : $this->ci->session->userdata("AUTH_user_group_id");
        $user_id = $logged_user_id ? $logged_user_id : $this->ci->session->userdata("AUTH_user_id");
        $this->ci->load->model("opinion_workflow_status_relation", "opinion_workflow_status_relationfactory");
        $this->ci->opinion_workflow_status_relation = $this->ci->opinion_workflow_status_relationfactory->get_instance();
        $transition_ids = $this->load_available_statuses();
        $global_statuses = $this->load_global_statuses();
        $permissions = $this->load_transitions_permissions();
        if (!empty($global_statuses)) {
            foreach ($global_statuses as $val) {
                $global[$val["workflow_id"]][] = $val["id"];
            }
        }
        if (!empty($transition_ids)) {
            foreach ($transition_ids as $val) {
                $permitted_transition = true;
                foreach ($permissions as $permission) {
                    if (intval($permission["transition"]) === intval($val["id"])) {
                        if (in_array(intval($user_id), explode(",", $permission["users"])) || in_array(intval($user_group_id), explode(",", $permission["user_groups"]))) {
                            $permitted_transition = true;
                            if ($permitted_transition) {
                                $transitions[$val["workflow_id"]][] = ["from" => $val["from_step"], "to" => $val["to_step"]];
                            }
                        } else {
                            $permitted_transition = false;
                        }
                    }
                }
            }
        }
        $steps = [];
        $all_statuses = $this->ci->opinion_workflow_status_relation->load_all();
        foreach ($all_statuses as $status) {
            $workflow_id = $status["workflow_id"];
            $steps[$workflow_id][$status["status_id"]] = $steps[$workflow_id][$status["status_id"]] ?? [];
            if (!empty($transitions[$workflow_id])) {
                if (empty($steps[$workflow_id][$status["status_id"]])) {
                    foreach ($transitions[$workflow_id] as $transition) {
                        if ($transition["from"] === $status["status_id"]) {
                            if (isset($global[$workflow_id])) {
                                $steps[$workflow_id][$transition["from"]] = $global[$workflow_id];
                                $steps[$workflow_id][$transition["to"]] = $global[$workflow_id];
                                foreach ($global[$workflow_id] as $global_id) {
                                    $steps[$workflow_id][$global_id][] = $transition["from"];
                                    $steps[$workflow_id][$global_id][] = $transition["to"];
                                }
                            }
                            $steps[$workflow_id][$transition["from"]][] = $transition["to"];
                        } else {
                            if (isset($global[$workflow_id])) {
                                $steps[$workflow_id][$status["status_id"]] = $global[$workflow_id];
                                if (in_array($status["status_id"], $global[$workflow_id])) {
                                    $key = array_search($status["status_id"], $global[$workflow_id]);
                                    unset($steps[$workflow_id][$status["status_id"]][$key]);
                                }
                                foreach ($global[$workflow_id] as $global_id) {
                                    $steps[$workflow_id][$global_id][] = $status["status_id"];
                                }
                            }
                        }
                    }
                }
            } else {
                if (isset($global[$workflow_id])) {
                    $steps[$workflow_id][$status["status_id"]] = $global[$workflow_id];
                    if (in_array($status["status_id"], $global[$workflow_id])) {
                        $key = array_search($status["status_id"], $global[$workflow_id]);
                        unset($steps[$workflow_id][$status["status_id"]][$key]);
                    } else {
                        foreach ($global[$workflow_id] as $global_id) {
                            $steps[$workflow_id][$global_id][] = $status["status_id"];
                        }
                    }
                }
            }
        }
        return $steps;
    }
    public function load_available_statuses()
    {
        $query["select"] = ["relation.workflow_id, opinion_workflow_status_transition.id, opinion_workflow_status_transition.name,  opinion_workflow_status_transition.from_step, opinion_workflow_status_transition.to_step", false];
        $query["join"] = [["opinion_workflow_status_relation as relation", "relation.workflow_id = opinion_workflow_status_transition.workflow_id AND relation.status_id = opinion_workflow_status_transition.from_step", "left"]];
        $response = parent::load_all($query);
        return $response;
    }
    public function load_global_statuses()
    {
        $query = [];
        $table = $this->_table;
        $this->_table = "opinion_statuses";
        $query["select"] = ["opinion_statuses.id, opinion_statuses.name, opinion_workflow_status_relation.workflow_id", false];
        $query["join"][] = ["opinion_workflow_status_relation", "opinion_workflow_status_relation.status_id = opinion_statuses.id", "inner"];
        $query["where"][] = ["opinion_statuses.isGlobal", 1];
        $response = $this->load_all($query);
        $this->_table = $table;
        return $response;
    }
}
class mysqli_Opinion_workflow_status_transition extends mysql_Opinion_workflow_status_transition
{
}
class sqlsrv_Opinion_workflow_status_transition extends mysql_Opinion_workflow_status_transition
{
}

?>