<?php

if (!defined("BASEPATH")) {
    exit("No direct script access allowed");
}
class Contract_status extends My_Model
{
    protected $modelName = "contract_status";
    protected $_table = "contract_status";
    protected $_listFieldName = "name";
    protected $_fieldsNames = ["id", "category_id", "is_global"];
    public function __construct()
    {
        parent::__construct();
        $this->validate = [];
    }
    public function load_allowed_to_statuses($status_id, $workflow_id, $to_step_exception_id = 0)
    {
        $this->ci->load->model("language");
        $language = $this->ci->language->get_id_by_session_lang();
        $query = [];
        $query["select"] = ["contract_status.id, status.name", false];
        $query["join"] = [["contract_status_language as status", "status.status_id = contract_status.id AND status.language_id = " . $language, "left"], ["contract_workflow_status_relation as relation", "relation.status_id = contract_status.id", "left"]];
        $query["where"][] = ["contract_status.is_global != ", "1", false];
        $query["where"][] = ["relation.workflow_id", $workflow_id];
        $query["where"][] = ["contract_status.id !=", $status_id, false];
        $where = "";
        if ($to_step_exception_id) {
            $where = " AND contract_workflow_status_transition.to_step != " . $to_step_exception_id . " ";
        }
        $query["where"][] = ["contract_status.id NOT IN (SELECT contract_workflow_status_transition.to_step from contract_workflow_status_transition WHERE contract_workflow_status_transition.from_step = '" . $status_id . "' AND contract_workflow_status_transition.workflow_id = " . $workflow_id . " " . $where . " )", NULL, false];
        return parent::load_list($query);
    }
    public function load_list_workflow_statuses($workflow_id)
    {
        $query = [];
        $this->ci->load->model("language");
        $lang_id = $this->ci->language->get_id_by_session_lang();
        $query["select"] = ["contract_status.id, status.name", false];
        $query["join"][] = ["contract_status_language as status", "status.status_id = contract_status.id", "inner"];
        $query["join"][] = ["contract_workflow_status_relation", "contract_workflow_status_relation.status_id = contract_status.id", "inner"];
        $query["where"][] = ["contract_workflow_status_relation.workflow_id", $workflow_id];
        $query["where"][] = ["status.language_id", $lang_id];
        return $this->load_list($query);
    }
    public function load_workflow_status_per_type($type_id)
    {
        $table = $this->_table;
        $this->_table = "contract_workflow";
        $query = [];
        $query["select"] = ["contract_status.id as status_id, contract_workflow.id as workflow_id", false];
        $query["join"][] = ["contract_workflow_per_type", "contract_workflow.id = contract_workflow_per_type.workflow_id", "inner"];
        $query["join"][] = ["contract_workflow_status_relation", "contract_workflow_status_relation.workflow_id = contract_workflow.id", "inner"];
        $query["join"][] = ["contract_status", "contract_workflow_status_relation.status_id = contract_status.id", "inner"];
        $query["where"][] = ["contract_workflow_status_relation.start_point", 1];
        $query["where"][] = ["contract_workflow_per_type.type_id", $type_id];
        $return = parent::load($query);
        $this->_table = $table;
        return $return;
    }
    public function load_system_workflow_status()
    {
        $table = $this->_table;
        $this->_table = "contract_workflow";
        $query = [];
        $query["select"] = ["contract_status.id as status_id, contract_workflow.id as workflow_id", false];
        $query["join"][] = ["contract_workflow_status_relation", "contract_workflow_status_relation.workflow_id = contract_workflow.id", "inner"];
        $query["join"][] = ["contract_status", "contract_workflow_status_relation.status_id = contract_status.id", "inner"];
        $query["where"][] = ["contract_workflow_status_relation.start_point", 1];
        $query["where"][] = ["contract_workflow.category", "system"];
        $return = parent::load($query);
        $this->_table = $table;
        return $return;
    }
    public function load_status_details($status_id)
    {
        $this->ci->load->model("language");
        $lang_id = $this->ci->language->get_id_by_session_lang();
        $query["select"][] = ["status.name as status_name, status_category.color as status_color", false];
        $query["where"][] = ["contract_status.id", $status_id];
        $query["join"] = [["status_category", "status_category.id = contract_status.category_id", "left"], ["contract_status_language as status", "status.status_id = contract_status.id and status.language_id = '" . $lang_id . "'", "left"]];
        return $this->load($query);
    }
    public function load_all_global_statuses()
    {
        $this->ci->load->model("language");
        $lang_id = $this->ci->language->get_id_by_session_lang();
        $query = [];
        $query["select"] = ["contract_status.id, status.name, contract_workflow_status_relation.workflow_id", false];
        $query["join"][] = ["contract_status_language as status", "status.status_id = contract_status.id", "inner"];
        $query["join"][] = ["contract_workflow_status_relation", "contract_workflow_status_relation.status_id = contract_status.id", "inner"];
        $query["where"][] = ["contract_status.is_global", 1];
        $query["where"][] = ["status.language_id", $lang_id];
        return $this->load_all($query);
    }
    public function load_step_transitions($from_step, $workflow_id)
    {
        $table = $this->_table;
        $this->_table = "contract_workflow_status_transition";
        $query = [];
        $query["select"] = ["contract_workflow_status_transition.id, contract_workflow_status_transition.from_step, contract_workflow_status_transition.to_step,fromStatus.name as from_stepName, toStatus.name as to_stepName, contract_workflow_status_transition.name, contract_workflow_status_transition.comment", false];
        $query["join"] = [["workflow_status as fromStatus", "fromStatus.id = contract_workflow_status_transition.from_step", "left"], ["workflow_status as toStatus", "toStatus.id = contract_workflow_status_transition.to_step", "left"]];
        $query["where"][] = ["from_step", $from_step];
        $query["where"][] = ["workflow_id", $workflow_id];
        $return = parent::load_all($query);
        $this->_table = $table;
        return $return;
    }
    public function get_available_steps($status_id, $workflow_applicable)
    {
        $this->ci->load->model("language");
        $lang_id = $this->ci->language->get_id_by_session_lang();
        $table = $this->_table;
        $this->_table = "contract_workflow_status_transition";
        $query = [];
        $query["select"] = ["to_step as id, to_status_language.name as name", false];
        $query["join"] = [["contract_status as to_status", "to_status.id = contract_workflow_status_transition.to_step", "left"], ["contract_status_language as to_status_language", "to_status_language.status_id = to_status.id and to_status_language.language_id = '" . $lang_id . "'", "left"]];
        $query["where"][] = ["contract_workflow_status_transition.from_step", $status_id];
        $query["where"][] = ["contract_workflow_status_transition.workflow_id", $workflow_applicable];
        $return = parent::load_list($query, ["key" => "id", "value" => "name"]);
        $this->_table = $table;
        return $return;
    }
    public function get_value($id, $key)
    {
        $this->fetch($id);
        return $this->get_field($key);
    }
    public function get_available_contract_statuses($status, $workflow_applicable)
    {
        $this->fetch($status);
        $return = [];
        if ($this->get_field("is_global") == 1) {
            $return = $this->load_list_workflow_statuses($workflow_applicable);
        } else {
            $return = $this->get_available_steps($status, $workflow_applicable);
            $this->ci->load->model("contract_status_language", "contract_status_languagefactory");
            $this->ci->contract_status_language = $this->ci->contract_status_languagefactory->get_instance();
            $this->ci->load->model("language");
            $lang_id = $this->ci->language->get_id_by_session_lang();
            $this->ci->contract_status_language->fetch(["status_id" => $status, "language_id" => $lang_id]);
            $return[$status] = $this->ci->contract_status_language->get_field("name");
            $global_statuses = $this->get_global_statuses($workflow_applicable);
            $return = $return + $global_statuses;
        }
        return $return;
    }
    public function get_global_statuses($workflowId = "")
    {
        $this->ci->load->model("language");
        $lang_id = $this->ci->language->get_id_by_session_lang();
        $query = [];
        $query["select"] = ["contract_status.id, contract_status_language.name", false];
        if ($workflowId) {
            $query["join"][] = ["contract_workflow_status_relation", "contract_workflow_status_relation.status_id = contract_status.id", "inner"];
            $query["join"][] = ["contract_status_language", "contract_status_language.status_id = contract_status.id and contract_status_language.language_id = '" . $lang_id . "'", "left"];
            $query["where"][] = ["contract_workflow_status_relation.workflow_id", $workflowId];
        }
        $query["where"][] = ["contract_status.is_global", 1];
        return $this->load_list($query, ["key" => "id", "value" => "name"]);
    }
    public function check_transition_allowed($contract_id, $status_id, $loggedUser, $modifiedByChannel = NULL, $logged_user_grp_id = "")
    {
        $this->ci->load->model("contract", "contractfactory");
        $this->ci->contract = $this->ci->contractfactory->get_instance();
        if (!$this->ci->contract->fetch($contract_id)) {
            return false;
        }
        $workflow_applicable = 0 < $this->ci->contract->get_field("workflow_id") ? $this->ci->contract->get_field("workflow_id") : 1;
        $this->ci->load->model("contract_workflow_status_transition_permission");
        $allowed_statuses = $this->ci->contract_workflow_status_transition_permission->get_allowed_workflow_statuses($this->ci->contract->get_field("status_id"), $workflow_applicable, $modifiedByChannel, $loggedUser, $logged_user_grp_id);
        if ($status_id === $this->ci->contract->get_field("status_id") || !in_array($status_id, array_keys($allowed_statuses["contract_statuses"]))) {
            return false;
        }
        return true;
    }
    public function load_list_statuses_per_ids($status_ids)
    {
        $this->ci->load->model("language");
        $lang_id = $this->ci->language->get_id_by_session_lang();
        $query = [];
        $query["select"] = ["contract_status.id, contract_status_language.name", false];
        $query["join"][] = ["contract_status_language", "contract_status_language.status_id = contract_status.id and contract_status_language.language_id = '" . $lang_id . "'", "left"];
        $query["where"] = [["contract_status.id IN ( " . $status_ids . ")", NULL, false]];
        return $this->load_list($query);
    }
}

?>