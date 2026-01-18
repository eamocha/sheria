<?php



if (!defined("BASEPATH")) {
    exit("No direct script access allowed");
}
class Opinion_workflow extends My_Model_Factory
{
}
class mysql_Opinion_workflow extends My_Model
{
    protected $modelName = "opinion_workflow";
    protected $_table = "opinion_workflows";
    protected $_listFieldName = "name";
    protected $_fieldsNames = ["id", "name", "type", "createdOn", "createdBy", "modifiedOn", "modifiedBy"];
    protected $builtInLogs = true;
    protected $system_workflow_id = 1;
    public function __construct()
    {
        parent::__construct();
        $this->validate = ["name" => ["required" => ["required" => true, "allowEmpty" => false, "rule" => ["minLength", 1], "message" => $this->ci->lang->line("cannot_be_blank_rule")], "maxLength" => ["rule" => ["maxLength", 255], "message" => sprintf($this->ci->lang->line("max_characters"), 255)], "isUnique" => ["rule" => "isUnique", "message" => sprintf($this->ci->lang->line("field_must_be_unique_rule"), $this->ci->lang->line("name"))]], "type" => ["required" => true, "allowEmpty" => false, "rule" => ["minLength", 1], "message" => $this->ci->lang->line("cannot_be_blank_rule")]];
    }
    public function load_workflows()
    {
        $query = [];
        $this->ci->load->model("language");
        $query["select"] = ["opinion_workflows.id, opinion_workflows.name, opinion_workflows.type, opinion_workflows.createdOn, opinion_workflows.modifiedOn,\r\n                (SELECT GROUP_CONCAT(DISTINCT opinion_types_languages.name SEPARATOR ',' ) from opinion_workflow_types  \r\n                LEFT JOIN opinion_types_languages ON opinion_types_languages.opinion_type_id = opinion_workflow_types.type_id AND opinion_types_languages.language_id = " . $this->ci->language->get_id_by_session_lang() . "\r\n                where opinion_workflow_types.workflow_id = opinion_workflows.id) as opinion_types_names", false];
        $query["order_by"] = ["opinion_workflows.id desc"];
        $response = parent::load_all($query);
        return $response;
    }
    public function add_workflow($data)
    {
        $this->set_fields($data);
        $this->set_field("type", "default");
        if ($this->validate()) {
            if (isset($data["opinion_type"]) && !empty($data["opinion_type"])) {
                $workflows_exists = $this->load_opinion_workflow_per_types($data["opinion_type"]);
                if ($workflows_exists) {
                    return ["result" => false, "display_message" => $this->ci->lang->line("opinion_type_related_workflow")];
                }
                $this->insert();
                $workflow_id = $this->get_field("id");
                $this->ci->load->model("opinion_workflow_type", "opinion_workflow_typefactory");
                $this->ci->opinion_workflow_type = $this->ci->opinion_workflow_typefactory->get_instance();
                $error = false;
                foreach ($data["opinion_type"] as $opinion_type) {
                    $this->ci->opinion_workflow_type->reset_fields();
                    $this->ci->opinion_workflow_type->set_field("workflow_id", $workflow_id);
                    $this->ci->opinion_workflow_type->set_field("type_id", $opinion_type);
                    if (!$this->ci->opinion_workflow_type->insert()) {
                        $error = true;
                        if ($error) {
                            $this->delete($workflow_id);
                            return ["result" => false, "validation_errors" => $this->ci->opinion_workflow_type->get("validationErrors")];
                        }
                        return ["result" => true, "workflow_id" => $workflow_id];
                    }
                }
            } else {
                return ["result" => false, "validation_errors" => ["opinion_type" => $this->ci->lang->line("cannot_be_blank_rule")]];
            }
        } else {
            return ["result" => false, "validation_errors" => $this->get("validationErrors")];
        }
        return ["result" => true, "workflow_id" => $workflow_id];
    }
    public function validate_workflow_edit($data)
    {
        if (isset($data["opinion_type"]) && !empty($data["opinion_type"])) {
            $workflows_exists = $this->load_opinion_workflow_per_types($data["opinion_type"], $data["id"]);
            if ($workflows_exists) {
                return ["result" => false, "display_message" => $this->ci->lang->line("opinion_type_related_workflow")];
            }
            $this->ci->load->model("opinion_workflow_type", "opinion_workflow_typefactory");
            $this->ci->opinion_workflow_type = $this->ci->opinion_workflow_typefactory->get_instance();
            $related_types = $this->ci->opinion_workflow_type->load_all(["where" => ["workflow_id", $data["id"]]]);
            $old_type_ids = array_column($related_types, "type_id");
            $diff_types = array_diff($old_type_ids, $data["opinion_type"]);
            if (!empty($diff_types)) {
                $related_opinions = $this->check_used_workflow($data["id"], $diff_types);
                if (!empty($related_opinions)) {
                    return ["result" => false, "related_opinions" => $related_opinions];
                }
            }
            return ["result" => true];
        }
        return ["result" => false, "validation_errors" => ["opinion_type" => $this->ci->lang->line("cannot_be_blank_rule")]];
    }
    public function edit_workflow($data)
    {
        $this->fetch($data["id"]);
        $this->set_field("name", $data["name"]);
        if ($this->update()) {
            $error = false;
            $this->ci->opinion_workflow_type->delete(["where" => ["workflow_id", $data["id"]]]);
            foreach ($data["opinion_type"] as $opinion_type) {
                $this->ci->opinion_workflow_type->reset_fields();
                $this->ci->opinion_workflow_type->set_field("workflow_id", $data["id"]);
                $this->ci->opinion_workflow_type->set_field("type_id", $opinion_type);
                if (!$this->ci->opinion_workflow_type->insert()) {
                    $error = true;
                    return $error ? false : true;
                }
                return $error ? false : true;
            }
        } else {
            return false;
        }
    }
    public function load_opinion_workflow_per_types($type_ids, $worflow_id = 0)
    {
        $ids = implode(",", $type_ids);
        $_table = $this->_table;
        $this->_table = "opinion_workflow_types";
        $query["select"] = ["opinion_workflow_types.id", false];
        $query["join"] = [["opinion_workflows", "opinion_workflows.id=opinion_workflow_types.workflow_id", "inner"]];
        $query["where"][] = ["opinion_workflow_types.type_id IN (" . $ids . ")"];
        if ($worflow_id) {
            $query["where"][] = ["opinion_workflow_types.workflow_id != " . $worflow_id];
        }
        $result = parent::load($query);
        $this->_table = $_table;
        return $result;
    }
    public function load_all_statuses_per_workflow($workflow_id, $list_view = false)
    {
        $query = [];
        $table = $this->_table;
        $this->_table = "opinion_workflow_status_relation as relation";
        $query["select"] = ["opinion_statuses.id, relation.start_point, opinion_statuses.name, opinion_statuses.isGlobal", false];
        $query["join"][] = ["opinion_statuses", "opinion_statuses.id = relation.status_id", "left"];
        $query["where"][] = ["relation.workflow_id", $workflow_id];
        $query["order_by"] = ["opinion_statuses.name asc"];
        $response = $list_view ? parent::load_list($query, ["key" => "id", "value" => "name"]) : parent::load_all($query);
        $this->_table = $table;
        return $response;
    }
    public function load_all_transitions_per_workflow($workflow_id, $from_status = 0)
    {
        $table = $this->_table;
        $this->_table = "opinion_workflow_status_transition as transition";
        $query = [];
        $query["select"] = ["transition.id, transition.from_step, transition.to_step, from_status.name as from_step_name, to_status.name as to_status_name, transition.name, transition.comments", false];
        $query["join"] = [["opinion_statuses as from_status", "from_status.id = transition.from_step", "left"], ["opinion_statuses as to_status", "to_status.id = transition.to_step", "left"]];
        $query["where"][] = ["workflow_id", $workflow_id];
        if ($from_status) {
            $query["where"][] = ["transition.from_step", $from_status];
        }
        $return = parent::load_all($query);
        $this->_table = $table;
        return $return;
    }
    public function load_status_transitions($status_id, $workflow_id)
    {
        $table = $this->_table;
        $this->_table = "opinion_workflow_status_transition";
        $query = [];
        $query["select"] = ["opinion_workflow_status_transition.id", false];
        $query["where"][] = ["workflow_id", $workflow_id];
        $query["where"][] = ["(from_step = " . $status_id . " OR to_step = " . $status_id . ")"];
        $return = parent::load_all($query);
        $this->_table = $table;
        return $return;
    }
    public function delete_workflow($workflow_id)
    {
        $opinions_related = $this->check_workflow_relation($workflow_id);
        if (!$opinions_related) {
            $transitions = $this->ci->opinion_workflow_status_transition->load_all(["where" => ["workflow_id", $workflow_id]]);
            foreach ($transitions as $transition) {
                $this->ci->opinion_workflow_status_transition_permission->delete(["where" => ["transition", $transition["id"]]]);
                $this->ci->opinion_workflow_status_transition_screen_field->delete(["where" => ["transition", $transition["id"]]]);
            }
            $this->ci->opinion_workflow_status_transition->delete(["where" => ["workflow_id", $workflow_id]]);
            $results = $this->ci->opinion_workflow_status_relation->load_all(["where" => ["workflow_id", $workflow_id]]);
            $this->ci->load->model("opinion_workflow_status_transition_history");
            foreach ($results as $status) {
                $this->ci->opinion_workflow_status_transition_history->delete(["where" => ["from_step", $status["status_id"]]], ["or_where" => ["to_step", $status["status_id"]]]);
            }
            $this->ci->opinion_workflow_status_relation->delete(["where" => ["workflow_id", $workflow_id]]);
            $this->ci->load->model("opinion_workflow_type", "opinion_workflow_typefactory");
            $this->ci->opinion_workflow_type = $this->ci->opinion_workflow_typefactory->get_instance();
            $this->ci->opinion_workflow_type->delete(["where" => ["workflow_id", $workflow_id]]);
            if ($this->ci->opinion_workflow->delete(["where" => ["id", $workflow_id]])) {
                return true;
            }
        }
        return false;
    }
    public function check_workflow_relation($id)
    {
        $table = $this->_table;
        $this->_table = "opinions";
        $query = [];
        $query["join"][] = ["opinion_workflows", "opinion_workflows.id = opinions.workflow", "inner"];
        $query["join"][] = ["opinion_workflow_status_relation", "opinion_workflow_status_relation.status_id = opinions.opinion_status_id AND opinion_workflow_status_relation.workflow_id = opinion_workflows.id", "left"];
        $query["where"][] = ["opinion_workflows.id", $id];
        $query["where"][] = ["opinions.workflow", $id];
        $opinions = parent::load_all($query);
        $this->_table = $table;
        if ($opinions) {
            return true;
        }
        return false;
    }
    public function load_workflow_opinion_status_per_type($type_id)
    {
        $query = [];
        $query["select"] = ["opinion_statuses.id as status, opinion_workflows.id as workflow_id", false];
        $query["join"][] = ["opinion_workflow_types", "opinion_workflows.id = opinion_workflow_types.workflow_id", "inner"];
        $query["join"][] = ["opinion_workflow_status_relation", "opinion_workflow_status_relation.workflow_id = opinion_workflows.id AND opinion_workflow_status_relation.start_point = 1", "inner"];
        $query["join"][] = ["opinion_statuses", "opinion_workflow_status_relation.status_id = opinion_statuses.id", "inner"];
        $query["where"][] = ["opinion_workflow_types.type_id", $type_id];
        $return = parent::load($query);
        return $return;
    }
    public function load_default_system_workflow()
    {
        $table = $this->_table;
        $this->_table = "opinion_workflows";
        $query = [];
        $query["select"] = ["opinion_statuses.id as status, opinion_workflows.id as workflow_id", false];
        $query["join"][] = ["opinion_workflow_status_relation", "opinion_workflow_status_relation.workflow_id = opinion_workflows.id AND opinion_workflow_status_relation.start_point = 1", "inner"];
        $query["join"][] = ["opinion_statuses", "opinion_workflow_status_relation.status_id = opinion_statuses.id", "inner"];
        $query["where"][] = ["opinion_workflows.type", "system"];
        $return = parent::load($query);
        $this->_table = $table;
        return $return;
    }
    public function check_used_workflow($id, $types)
    {
        $query = [];
        $table = $this->_table;
        $this->_table = "opinion_statuses";
        $query["select"] = ["DISTINCT opinions.opinion_status_id as status_id ,count(opinions.id) as opinions_count, opinion_statuses.name, opinions.opinion_type_id as type_id", false];
        $query["join"][] = ["opinions", "opinions.opinion_status_id = opinion_statuses.id", "inner"];
        $query["where"][] = ["opinions.workflow", $id];
        $opinion_type_ids = implode(",", $types);
        if ($opinion_type_ids) {
            $query["where"][] = ["opinions.opinion_type_id IN (" . $opinion_type_ids . ")"];
        }
        $query["group_by"] = ["opinion_statuses.id"];
        $response = parent::load_all($query);
        $this->_table = $table;
        return $response;
    }
    public function update_opinion_statuses_workflow($data)
    {
        $this->ci->db->where(["opinion_status_id" => $data["old_status"], "opinion_type_id" => $data["type"], "workflow" => $data["workflow_id"]]);
        return $this->ci->db->update("opinions", ["opinion_status_id" => $data["new_status"], "workflow" => $this->system_workflow_id, "modifiedBy" => $this->ci->is_auth->get_user_id(), "modifiedOn" => date("Y-m-d H:i:s")]);
    }
}
class mysqli_Opinion_workflow extends mysql_Opinion_workflow
{
}
class sqlsrv_Opinion_workflow extends mysql_Opinion_workflow
{
    public function load_workflows()
    {
        $query = [];
        $this->ci->load->model("language");
        $query["select"] = ["opinion_workflows.id, opinion_workflows.name, opinion_workflows.type, opinion_workflows.createdOn, opinion_workflows.modifiedOn,\r\n                opinion_types_names = STUFF((SELECT DISTINCT ', ' + opinion_types_languages.name from opinion_workflow_types \r\n                LEFT JOIN opinion_types_languages ON opinion_types_languages.opinion_type_id = opinion_workflow_types.type_id AND opinion_types_languages.language_id = " . $this->ci->language->get_id_by_session_lang() . "\r\n                where opinion_workflow_types.workflow_id = opinion_workflows.id FOR XML PATH('')), 1, 1, '')", false];
        $query["order_by"] = ["opinion_workflows.id desc"];
        $response = parent::load_all($query);
        return $response;
    }
    public function check_used_workflow($id, $types)
    {
        $query = [];
        $table = $this->_table;
        $this->_table = "opinion_statuses";
        $query["select"] = ["DISTINCT opinions.opinion_status_id as status_id ,count(opinions.id) as opinions_count, opinion_statuses.name, opinions.opinion_type_id as type_id", false];
        $query["join"][] = ["opinions", "opinions.opinion_status_id = opinion_statuses.id", "inner"];
        $query["where"][] = ["opinions.workflow", $id];
        $opinion_type_ids = implode(",", $types);
        if ($opinion_type_ids) {
            $query["where"][] = ["opinions.opinion_type_id IN (" . $opinion_type_ids . ")"];
        }
        $query["group_by"] = ["opinions.id, opinions.opinion_status_id, opinion_statuses.name, opinions.opinion_type_id"];
        $response = parent::load_all($query);
        $this->_table = $table;
        return $response;
    }
}

?>