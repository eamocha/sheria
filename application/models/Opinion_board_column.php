<?php
if (!defined("BASEPATH")) {
    exit("No direct script access allowed");
}
class Opinion_board_column extends My_Model_Factory
{
}
class mysql_Opinion_board_column extends My_Model
{
    protected $modelName = "opinion_board_column";
    protected $_table = "opinion_board_columns";
    protected $_listFieldName = "name";
    protected $_fieldsNames = ["id", "opinion_board_id", "columnOrder", "name", "color"];
    protected $minNbOfColumns = "2";
    protected $maxNbOfColumns = "6";
    public function __construct()
    {
        parent::__construct();
        $this->validate = ["opinion_board_id" => ["required" => true, "allowEmpty" => false, "rule" => "numeric", "message" => sprintf($this->ci->lang->line("is_numeric_rule"), $this->ci->lang->line("opinion_board"))], "columnOrder" => ["required" => true, "allowEmpty" => false, "rule" => "numeric", "message" => sprintf($this->ci->lang->line("is_numeric_rule"), $this->ci->lang->line("number_of_columns"))], "name" => ["required" => true, "allowEmpty" => false, "rule" => ["maxLength", 255], "message" => sprintf($this->ci->lang->line("required__max_length_rule"), $this->ci->lang->line("name"), 255)], "color" => ["required" => true, "allowEmpty" => false, "rule" => ["maxLength", 255], "message" => sprintf($this->ci->lang->line("required__max_length_rule"), $this->ci->lang->line("name"), 255)]];
        $this->logged_user_id = $this->ci->is_auth->get_user_id();
        $this->override_privacy = $this->ci->is_auth->get_override_privacy();
    }
    public function load_all_options($id)
    {
        if ($id < 1) {
            return [];
        }
        return $this->load_all(["select" => ["opinion_board_column_id, GROUP_CONCAT(opinion_status_id SEPARATOR '|') as opinion_status_id"], "join" => ["opinion_board_column_options", "opinion_board_column_options.opinion_board_column_id = opinion_board_columns.id", "INNER"], "where" => [$this->_table . ".opinion_board_id", $id], "group_by" => ["opinion_board_column_id"]]);
    }
    public function get_opinion_board_column_options_data($opinionBoardId, $filters, $saved_filters = [], $quick_filter = [], $limit = true)
    {
        $return = [];
        $return["columns"] = [];
        $return["opinions"] = [];
        $table = $this->_table . " pbc";
        $this->ci->load->model("language");
        $lang_id = $this->ci->language->get_id_by_session_lang();
        $this->prep_query(["select" => ["pbc.id, pbc.name, pbc.color, tbco.opinion_status_id,ts.name as status_name, (SELECT GROUP_CONCAT(relation.workflow_id) from opinion_workflow_status_relation as relation WHERE relation.status_id = tbco.opinion_status_id) as workflows_related", false], "join" => [["opinion_board_column_options tbco", "tbco.opinion_board_column_id = pbc.id", "left"], ["opinion_statuses ts", "ts.id = tbco.opinion_status_id", "left"]], "where" => ["pbc.opinion_board_id", $opinionBoardId], "order_by" => ["pbc.columnOrder", "asc"]]);
        $result = $this->ci->db->get($table);
        $columns = $result->result_array();
        if (isset($columns) && !empty($columns)) {
            $columns_ids = implode(",", array_column($columns, "id"));
        }
        $table = "opinions ta";
        $query["where"][] = ["ta.archived ", "no"];
        $query["where"][] = ["ta.hideFromBoard IS NULL", NULL, false];
        $query["where"][] = ["(ta.legal_case_id IS NULL OR (ca.isDeleted = '0' AND(ca.private IS NULL OR ca.private = 'no' OR (ca.private = 'yes' AND (" . "ca.createdBy = '" . $this->logged_user_id . "' OR ca.user_id = '" . $this->logged_user_id . "' OR ca.id IN (SELECT legal_case_id FROM legal_case_users WHERE user_id = '" . $this->logged_user_id . "') OR '" . $this->override_privacy . "' = 'yes'" . ")" . ")" . ")" . ")" . ")" . " AND " . "(" . "ta.private IS NULL OR ta.private = 'no' OR" . "(" . "ta.private = 'yes' AND " . "(" . "ta.createdBy = '" . $this->logged_user_id . "' OR ta.assigned_to = '" . $this->logged_user_id . "' OR " . "ta.id IN (SELECT opinion_id FROM opinion_users WHERE user_id = '" . $this->logged_user_id . "') OR '" . $this->override_privacy . "' = 'yes'" . ")" . ")" . ")"];
        $this->ci->load->model("opinion", "opinionfactory");
        $this->ci->opinion = $this->ci->opinionfactory->get_instance();
        $this->get_saved_filter($saved_filters, $quick_filter);
        $query["select"] = ["ta.id, ta.title, ta.description,ta.opinion_status_id, ta.workflow, ta.priority, \r\n        us.user_id as assignee_user_id, us.profilePicture as assignee_profile_pic, CONCAT( us.firstName, ' ', us.lastName ) as assignee,us.status, \r\n        ta.legal_case_id as caseId, ta.due_date, ca.subject as caseSubject, ca.category as case_category, \r\n        pbco.opinion_board_column_id AS column_id, users.user_group_id, ta.reporter, CONCAT( us_reporter.firstName, ' ', us_reporter.lastName ) as reporter_name, ttl.name", false];
        $query["join"] = [["user_profiles us", "us.user_id = ta.assigned_to", "left"], ["user_profiles us_reporter", "us_reporter.user_id = ta.reporter", "left"], ["legal_cases ca", "ca.id = ta.legal_case_id", "left"], ["opinion_board_column_options pbco", "pbco.opinion_status_id = ta.opinion_status_id", "left"], ["users", "users.id = us.id", "left"], ["provider_groups_users", "provider_groups_users.user_id = us.user_id", "left"], ["opinion_types_languages ttl", "ttl.opinion_type_id = ta.opinion_type_id and ttl.language_id = '" . $lang_id . "'", "left"]];
        $query["group_by"] = ["ta.id"];
        if (isset($columns_ids) && !empty($columns_ids)) {
            $query["where"][] = ["opinion_board_column_id IN (" . $columns_ids . ")", NULL, false];
        }
        $query["order_by"] = "ta.due_date ASC, ta.id ASC";
        if (isset($saved_filters) && is_array($saved_filters) && isset($saved_filters["filters"])) {
            foreach ($saved_filters["filters"] as $_filter) {
                $this->prep_k_filter($_filter, $query, $saved_filters["logic"]);
            }
            unset($_filter);
        }
        $this->prep_query($query);
        $temp_result = $this->ci->db->get($table);
        $opinions_result = $temp_result->result_array();
        if (isset($columns) && !empty($columns)) {
            foreach ($columns as $column) {
                $return["columns"][$column["id"]][] = $column;
                $return["opinions"][$column["id"]] = [];
            }
            if (isset($opinions_result) && !empty($opinions_result)) {
                foreach ($opinions_result as $opinion) {
                    array_push($return["opinions"][$opinion["column_id"]], $opinion);
                }
            }
        }
        return $return;
    }
    public function get_opinion_Board_columns()
    {
        $table = $this->_table;
        $this->_table = "opinion_boards pb";
        $query = ["select" => ["pb.id, pb.name, COUNT(opinion_board_columns.id) as nbOfColumns, CONCAT( modifiedUser.firstName, ' ', modifiedUser.lastName ) as lastModifiedBy, pb.modifiedOn as lastModifiedOn, CONCAT( createdUser.firstName, ' ', createdUser.lastName ) as createdBy,createdUser.status as createdUserStatus,modifiedUser.status as modifiedUserStatus, pb.createdOn as createdOn", false], "join" => [["opinion_board_columns", "pb.id = opinion_board_columns.opinion_board_id", "left"], ["user_profiles createdUser", "createdUser.user_id = pb.createdBy", "left"], ["user_profiles modifiedUser", "modifiedUser.user_id = pb.modifiedBy", "left"]], "group_by" => "pb.id", "order_by" => ["pb.name asc"]];
        $records = $this->load_all($query);
        $this->_table = $table;
        return $records;
    }
    public function get_saved_filter(&$saved_filters, $quick_filter)
    {
        if (!empty($saved_filters)) {
            $saved_filters = json_decode($saved_filters["gridFilters"], true);
        }
        if (!empty($quick_filter)) {
            if (!empty($saved_filters)) {
                if (isset($quick_filter["quickFilter"]["filters"]) && isset($quick_filter["postFilter"]["filters"])) {
                    array_push($saved_filters["filters"], ...$quick_filter["quickFilter"]["filters"]);
                    array_push($saved_filters["filters"], ...$quick_filter["postFilter"]["filters"]);
                } else {
                    if (isset($quick_filter["quickFilter"]["filters"])) {
                        array_push($saved_filters["filters"], ...$quick_filter["quickFilter"]["filters"]);
                    } else {
                        if (isset($quick_filter["postFilter"]["filters"])) {
                            array_push($saved_filters["filters"], ...$quick_filter["postFilter"]["filters"]);
                        }
                    }
                }
            } else {
                if (isset($quick_filter["quickFilter"]["filters"]) && isset($quick_filter["postFilter"]["filters"])) {
                    $saved_filters["filters"] = [];
                    array_push($saved_filters["filters"], ...$quick_filter["postFilter"]["filters"]);
                    array_push($saved_filters["filters"], ...$quick_filter["quickFilter"]["filters"]);
                    $saved_filters["logic"] = "and";
                } else {
                    if (isset($quick_filter["quickFilter"]["filters"])) {
                        $saved_filters = $quick_filter["quickFilter"];
                    } else {
                        if (isset($quick_filter["postFilter"]["filters"])) {
                            $saved_filters = $quick_filter["postFilter"];
                            $saved_filters["logic"] = "and";
                        }
                    }
                }
            }
        }
    }
}
class mysqli_Opinion_board_column extends mysql_Opinion_board_column
{
}
class sqlsrv_Opinion_board_column extends mysql_Opinion_board_column
{
    public function load_all_options($id)
    {
        if ($id < 1) {
            return [];
        }
        return $this->load_all(["select" => ["id AS opinion_board_column_id, opinion_status_id=STUFF((SELECT '|'+CAST(opinion_status_id AS VARCHAR) FROM opinion_board_column_options WHERE opinion_board_column_options.opinion_board_column_id = opinion_board_columns.id FOR XML PATH('')), 1,1,'')"], "where" => ["opinion_board_id", 1 * $id]]);
    }
    public function get_opinion_Board_columns()
    {
        $table = $this->_table;
        $this->_table = "opinion_boards pb";
        $query = ["select" => ["pb.id, pb.name, (SELECT COUNT(0) FROM opinion_board_columns WHERE pb.id = opinion_board_columns.opinion_board_id) AS nbOfColumns, ( modifiedUser.firstName + ' ' + modifiedUser.lastName ) as lastModifiedBy,createdUser.status as createdUserStatus,modifiedUser.status as modifiedUserStatus, pb.modifiedOn as lastModifiedOn, ( createdUser.firstName + ' ' + createdUser.lastName ) as createdBy, pb.createdOn as createdOn", false], "join" => [["user_profiles createdUser", "createdUser.user_id = pb.createdBy", "left"], ["user_profiles modifiedUser", "modifiedUser.user_id = pb.modifiedBy", "left"]], "order_by" => ["pb.name asc"]];
        $records = $this->load_all($query);
        $this->_table = $table;
        return $records;
    }
    public function get_opinion_board_column_options_data($opinionBoardId, $filters, $saved_filters = [], $quick_filter = [], $limit = true)
    {
        $return = [];
        $return["columns"] = [];
        $return["opinions"] = [];
        $table = $this->_table . " pbc";
        $this->ci->load->model("language");
        $lang_id = $this->ci->language->get_id_by_session_lang();
        $this->prep_query(["select" => ["pbc.id, pbc.name, pbc.color, tbco.opinion_status_id,ts.name as status_name,workflows_related = STUFF((SELECT DISTINCT ',' + CAST(relation.workflow_id AS varchar) from opinion_workflow_status_relation as relation WHERE relation.status_id = tbco.opinion_status_id FOR XML PATH('')), 1, 1, '')"], "join" => [["opinion_board_column_options tbco", "tbco.opinion_board_column_id = pbc.id", "left"], ["opinion_statuses ts", "ts.id = tbco.opinion_status_id", "left"]], "where" => ["pbc.opinion_board_id", $opinionBoardId], "order_by" => ["pbc.columnOrder", "asc"]]);
        $result = $this->ci->db->get($table);
        $columns = $result->result_array();
        if (isset($columns) && !empty($columns)) {
            $columns_ids = implode(",", array_column($columns, "id"));
        }
        $table = "opinions ta";
        $this->ci->load->model("opinion", "opinionfactory");
        $this->ci->opinion = $this->ci->opinionfactory->get_instance();
        $this->get_saved_filter($saved_filters, $quick_filter);
        $query["select"] = ["ta.id, ta.title, Max(cast(ta.description as varchar(max))) as description,Max(ta.opinion_status_id) as opinion_status_id, Max(ta.workflow) as workflow, Max(ta.priority) as priority, \r\n        Max(us.user_id) as assignee_user_id, Max(us.profilePicture) as assignee_profile_pic, ( Max(us.firstName) + ' ' + Max(us.lastName) ) as assignee,Max(us.status) as status, \r\n        Max(ta.legal_case_id) as caseId, Max(ta.due_date) as due_date, Max(ca.subject) as caseSubject, Max(ca.category) as case_category, \r\n        Max(pbco.opinion_board_column_id) AS column_id, Max(users.user_group_id) as user_group_id, Max(ta.reporter) as reporter, (Max(us_reporter.firstName) + ' ' + Max(us_reporter.lastName)) as reporter_name, Max(cast(ttl.name as varchar(max))) as name", false];
        $query["join"] = [["user_profiles us", "us.user_id = ta.assigned_to", "left"], ["user_profiles us_reporter", "us_reporter.user_id = ta.reporter", "left"], ["legal_cases ca", "ca.id = ta.legal_case_id", "left"], ["opinion_board_column_options pbco", "pbco.opinion_status_id = ta.opinion_status_id", "left"], ["users", "users.id = us.id", "left"], ["provider_groups_users", "provider_groups_users.user_id = us.user_id", "left"], ["opinion_types_languages ttl", "ttl.opinion_type_id = ta.opinion_type_id and ttl.language_id = '" . $lang_id . "'", "left"]];
        $query["group_by"] = ["ta.id"];
        $query["where"][] = ["ta.archived ", "no"];
        $query["where"][] = ["ta.hideFromBoard IS NULL", NULL, false];
        $query["where"][] = ["(ta.legal_case_id IS NULL OR (ca.isDeleted = '0' AND(ca.private IS NULL OR ca.private = 'no' OR (ca.private = 'yes' AND (" . "ca.createdBy = '" . $this->logged_user_id . "' OR ca.user_id = '" . $this->logged_user_id . "' OR ca.id IN (SELECT legal_case_id FROM legal_case_users WHERE user_id = '" . $this->logged_user_id . "') OR '" . $this->override_privacy . "' = 'yes'" . ")" . ")" . ")" . ")" . ")" . " AND " . "(" . "ta.private IS NULL OR ta.private = 'no' OR" . "(" . "ta.private = 'yes' AND " . "(" . "ta.createdBy = '" . $this->logged_user_id . "' OR ta.assigned_to = '" . $this->logged_user_id . "' OR " . "ta.id IN (SELECT opinion_id FROM opinion_users WHERE user_id = '" . $this->logged_user_id . "') OR '" . $this->override_privacy . "' = 'yes'" . ")" . ")" . ")"];
        if (isset($columns_ids) && !empty($columns_ids)) {
            $query["where"][] = ["opinion_board_column_id IN (" . $columns_ids . ")", NULL, false];
        }
        $query["order_by"] = "Max(ta.due_date) ASC, ta.id ASC";
        if (isset($saved_filters) && is_array($saved_filters) && isset($saved_filters["filters"])) {
            foreach ($saved_filters["filters"] as $_filter) {
                $this->prep_k_filter($_filter, $query, $saved_filters["logic"]);
            }
            unset($_filter);
        }
        $this->prep_query($query);
        $temp_result = $this->ci->db->get($table);
        $opinions_result = $temp_result->result_array();
        if (isset($columns) && !empty($columns)) {
            foreach ($columns as $column) {
                $return["columns"][$column["id"]][] = $column;
                $return["opinions"][$column["id"]] = [];
            }
            if (isset($opinions_result) && !empty($opinions_result)) {
                foreach ($opinions_result as $opinion) {
                    array_push($return["opinions"][$opinion["column_id"]], $opinion);
                }
            }
        }
        return $return;
    }
}

?>