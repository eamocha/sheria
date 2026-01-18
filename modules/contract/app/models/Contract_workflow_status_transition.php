<?php

if (!defined("BASEPATH")) {
    exit("No direct script access allowed");
}
class Contract_workflow_status_transition extends My_Model_Factory
{
}
class mysql_Contract_workflow_status_transition extends My_Model
{
    protected $modelName = "contract_workflow_status_transition";
    protected $_table = "contract_workflow_status_transition";
    protected $_listFieldName = "name";
    protected $_fieldsNames = ["id", "workflow_id", "from_step", "to_step", "name", "comment", "approval_needed"];
    public function __construct()
    {
        $this->ci =& get_instance();
        parent::__construct();
        $this->validate = ["workflow_id" => ["required" => true, "allowEmpty" => false, "rule" => "numeric", "message" => $this->ci->lang->line("cannot_be_blank_rule")], "from_step" => ["required" => true, "allowEmpty" => false, "rule" => "numeric", "message" => $this->ci->lang->line("cannot_be_blank_rule")], "to_step" => ["required" => true, "allowEmpty" => false, "rule" => "numeric", "message" => $this->ci->lang->line("cannot_be_blank_rule")], "name" => ["isRequired" => ["required" => true, "allowEmpty" => false, "rule" => ["minLength", 1], "message" => $this->ci->lang->line("cannot_be_blank_rule")], "maxLength" => ["rule" => ["maxLength", 255], "message" => sprintf($this->ci->lang->line("max_characters"), 255)]], "approval_needed" => ["required" => true, "allowEmpty" => false, "rule" => "numeric", "message" => $this->ci->lang->line("cannot_be_blank_rule")]];
    }
    public function load_available_steps($status, $workflow, $lang = 0)
    {
        $user_group_id = $this->ci->session->userdata("AUTH_user_group_id");
        $user_id = $this->ci->session->userdata("AUTH_user_id");
        $this->ci->load->model("contract_status");
        $this->ci->contract_status->fetch($status);
        $status_transitions = [];
        if ($this->ci->contract_status->get_field("is_global") == 1) {
            $available_statuses = $this->ci->contract_status->load_list_workflow_statuses($workflow);
            unset($available_statuses[$status]);
        } else {
            $transitions = $this->load_available_statuses_per_workflow($status, $workflow, $lang);
            $global_statuses = $this->load_global_statuses_per_workflow($workflow, $lang);
            $available_statuses = $transitions + $global_statuses;
            $status_transitions = $this->load_available_transitions($status, $workflow);
            $transition_permissions = $this->load_transitions_permissions($workflow);
            $permitted_transition = true;
            foreach ($status_transitions as $key => $transition) {
                if (isset($transition["id"])) {
                    foreach ($transition_permissions as $permission) {
                        if (intval($permission["transition_id"]) === intval($transition["id"])) {
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
        }
        return ["status_transitions" => $status_transitions, "available_statuses" => $available_statuses];
    }
    public function load_available_statuses_per_workflow($status, $workflow, $lang = 0)
    { //for possible next steps from a certain step
        $this->ci->load->model("language");
        $lang === 0 ? $lang_id = $this->ci->language->get_id_by_session_lang() : ($lang_id = $lang);
        $query["select"] = ["contract_status.id as id, status.name as name", false];
        $query["join"] = [["contract_status", "contract_status.id = contract_workflow_status_transition.to_step", "left"], ["contract_status_language as status", "status.status_id = contract_status.id", "inner"], ["contract_workflow_status_relation", "contract_workflow_status_relation.status_id = contract_status.id", "left"]];
        $query["where"][] = ["contract_workflow_status_transition.from_step", $status];
        $query["where"][] = ["contract_workflow_status_transition.workflow_id", $workflow];
        $query["where"][] = ["status.language_id", $lang_id];
        $response = parent::load_list($query, ["key" => "id", "value" => "name"]);
        return $response;
    }
    public function load_global_statuses_per_workflow($workflow, $lang = 0)
    {// for global steps available in a certain workflow s
        $query = [];
        $this->ci->load->model("language");
        $lang === 0 ? $lang_id = $this->ci->language->get_id_by_session_lang() : ($lang_id = $lang);
        $table = $this->_table;
        $this->_table = "contract_status";
        $query["select"] = ["contract_status.id, status.name", false];
        $query["join"][] = ["contract_status_language as status", "status.status_id = contract_status.id", "inner"];
        $query["join"][] = ["contract_workflow_status_relation", "contract_workflow_status_relation.status_id = contract_status.id", "inner"];
        $query["where"][] = ["contract_workflow_status_relation.workflow_id", $workflow];
        $query["where"][] = ["contract_status.is_global", 1];
        $query["where"][] = ["status.language_id", $lang_id];
        $response = $this->load_list($query, ["key" => "id", "value" => "name"]);
        $this->_table = $table;
        return $response;
    }
    public function load_available_transitions($from_status, $workflow)
    { //for all transition options from a certain step. this represents the different scenarios from a certain step
        $query = [];
        $query["select"] = ["contract_workflow_status_transition.id, contract_workflow_status_transition.from_step,
         contract_workflow_status_transition.to_step,from_status.name as from_status_name, to_status.name as to_status_name,
          contract_workflow_status_transition.name, contract_workflow_status_transition.comment", false];
        $query["join"] = [["workflow_status as from_status", "from_status.id = contract_workflow_status_transition.from_step", "left"],
            ["workflow_status as to_status", "to_status.id = contract_workflow_status_transition.to_step", "left"]];
        $query["where"][] = ["from_step", $from_status];
        $query["where"][] = ["workflow_id", $workflow];
        $response = parent::load_all($query);
        return array_combine(array_column($response, "to_step"), $response);
    }
    public function load_transitions_permissions($workflow = "")
    {
        $query = [];
        $query["select"] = ["permissions.transition_id,permissions.users,permissions.user_groups, contract_workflow_status_transition.workflow_id", false];
        $query["join"] = [["contract_workflow_status_transition_permission as permissions", "permissions.transition_id = contract_workflow_status_transition.id", "inner"]];
        if ($workflow) {
            $query["where"][] = ["contract_workflow_status_transition.workflow_id = " . $workflow];
        }
        return $this->load_all($query);
    }
    public function load_all_possible_transitions()
    {
        $table = $this->_table;
        $this->_table = "contract_status status";
        $query = [];
        $query["select"] = ["status.id, status.is_global, GROUP_CONCAT(steps.to_step) AS allowed_transitions, steps.workflow_id, steps.id as transition_id"];
        $query["join"][] = ["contract_workflow_status_transition steps", "status.id = steps.from_step", "left"];
        $query["join"][] = ["contract_workflow_status_transition_permission permission", "permission.transition_id = steps.id", "left"];
        $query["where"][] = ["(permission.users is null or permission.users like '%" . $this->ci->session->userdata("AUTH_user_id") . "%') or (permission.user_groups is null or permission.user_groups like '%" . $this->ci->session->userdata("AUTH_user_group_id") . "%')"];
        $query["group_by"] = ["status.id"];
        $return = parent::load_all($query);
        $this->_table = $table;
        return $return;
    }
    public function load_workflows_transitions()
    {
        $this->ci->load->model("contract_status");
        $possible_transitions = $this->load_all_possible_transitions();
        $global_transitions = $this->ci->contract_status->load_all_global_statuses();
        if (!empty($possible_transitions) && !empty($global_transitions)) {
            foreach ($possible_transitions as $transition_key => $transition) {
                foreach ($global_transitions as $global) {
                    if ($transition["workflow_id"] === $global["workflow_id"]) {
                        $possible_transitions[$transition_key]["allowed_transitions"] .= "," . $global["id"];
                    }
                }
            }
        }
        foreach ($global_transitions as $t_key => $t_global) {
            $possible_transitions[] = ["id" => $t_global["id"], "is_global" => 1, "allowed_transitions" => "", "workflow_id" => ""];
        }
        foreach ($possible_transitions as $trans => $trans_value) {
            if ($trans_value["is_global"] == 1) {
                foreach ($global_transitions as $global) {
                    if ($trans_value["id"] === $global["id"]) {
                        $possible_transitions[$trans]["workflow_id"] = empty($possible_transitions[$trans]["workflow_id"]) ? $global["workflow_id"] : $possible_transitions[$trans]["workflow_id"] . "," . $global["workflow_id"];
                    }
                }
            }
        }
        foreach ($possible_transitions as $key => $possible_transition) {
            $transitions[$possible_transition["id"]] = $possible_transition;
        }
        return $transitions;
    }
}
class mysqli_Contract_workflow_status_transition extends mysql_Contract_workflow_status_transition
{
}
class sqlsrv_Contract_workflow_status_transition extends mysql_Contract_workflow_status_transition
{
    public function load_all_possible_transitions()
    {
        $table = $this->_table;
        $this->_table = "contract_status status";
        $query = [];
        $query["select"] = ["status.id, status.is_global, contract_workflow_status_transition.workflow_id, allowed_transitions = STUFF((\r\n          SELECT ',' + CAST(steps.to_step AS nvarchar)\r\n          FROM contract_workflow_status_transition steps\r\n          LEFT JOIN contract_workflow_status_transition_permission permission ON permission.transition_id = steps.id\r\n          WHERE status.id = steps.from_step AND ((permission.users is null or permission.users like '%" . $this->ci->session->userdata("AUTH_user_id") . "%') OR (permission.user_groups is null or permission.user_groups like '%" . $this->ci->session->userdata("AUTH_user_group_id") . "%'))\r\n          FOR XML PATH(''), TYPE).value('.', 'NVARCHAR(MAX)'), 1, 1, '')"];
        $query["join"] = [["contract_workflow_status_transition", "contract_workflow_status_transition.from_step = status.id", "left"]];
        $query["group_by"] = ["status.id,status.is_global, contract_workflow_status_transition.workflow_id"];
        $return = parent::load_all($query);
        $this->_table = $table;
        return $return;
    }
}

?>