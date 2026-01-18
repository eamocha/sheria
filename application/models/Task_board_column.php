<?php

if (!defined("BASEPATH")) {
    exit("No direct script access allowed");
}
class Task_board_column extends My_Model_Factory
{
}
class mysql_Task_board_column extends My_Model
{
    protected $modelName = "task_board_column";
    protected $_table = "task_board_columns";
    protected $_listFieldName = "name";
    protected $_fieldsNames = ["id", "task_board_id", "columnOrder", "name", "color"];
    protected $minNbOfColumns = "2";
    protected $maxNbOfColumns = "6";
    public function __construct()
    {
        parent::__construct();
        $this->validate = ["task_board_id" => ["required" => true, "allowEmpty" => false, "rule" => "numeric", "message" => sprintf($this->ci->lang->line("is_numeric_rule"), $this->ci->lang->line("task_board"))], "columnOrder" => ["required" => true, "allowEmpty" => false, "rule" => "numeric", "message" => sprintf($this->ci->lang->line("is_numeric_rule"), $this->ci->lang->line("number_of_columns"))], "name" => ["required" => true, "allowEmpty" => false, "rule" => ["maxLength", 255], "message" => sprintf($this->ci->lang->line("required__max_length_rule"), $this->ci->lang->line("name"), 255)], "color" => ["required" => true, "allowEmpty" => false, "rule" => ["maxLength", 255], "message" => sprintf($this->ci->lang->line("required__max_length_rule"), $this->ci->lang->line("name"), 255)]];
        $this->logged_user_id = $this->ci->is_auth->get_user_id();
        $this->override_privacy = $this->ci->is_auth->get_override_privacy();
    }
    public function load_all_options($id)
    {
        if ($id < 1) {
            return [];
        }
        return $this->load_all(["select" => ["task_board_column_id, GROUP_CONCAT(task_status_id SEPARATOR '|') as task_status_id"], "join" => ["task_board_column_options", "task_board_column_options.task_board_column_id = task_board_columns.id", "INNER"], "where" => [$this->_table . ".task_board_id", $id], "group_by" => ["task_board_column_id"]]);
    }
    public function get_task_board_column_options_data($taskBoardId, $filters, $saved_filters = [], $quick_filter = [], $limit = true)
    {
        $return = [
            "columns" => [],
            "tasks" => []
        ];

        // Get columns data
        $table = $this->_table . " pbc";
        $this->ci->load->model("language");
        $lang_id = $this->ci->language->get_id_by_session_lang();

        $this->prep_query([
            "select" => [
                "pbc.id, pbc.name, pbc.color, tbco.task_status_id, ts.name as status_name, 
            (SELECT GROUP_CONCAT(relation.workflow_id) 
             FROM task_workflow_status_relation as relation 
             WHERE relation.status_id = tbco.task_status_id) as workflows_related",
                false
            ],
            "join" => [
                ["task_board_column_options tbco", "tbco.task_board_column_id = pbc.id", "left"],
                ["task_statuses ts", "ts.id = tbco.task_status_id", "left"]
            ],
            "where" => ["pbc.task_board_id", $taskBoardId],
            "order_by" => ["pbc.columnOrder", "asc"]
        ]);

        $columns = $this->ci->db->get($table)->result_array();
        $columns_ids = !empty($columns) ? implode(",", array_column($columns, "id")) : null;

        // Get tasks data
        $table = "tasks ta";
        $this->ci->load->model("task", "taskfactory");
        $this->ci->task = $this->ci->taskfactory->get_instance();

        $query = [
            "select" => [
                "ta.id, ta.title, ta.description, ta.task_status_id, ta.workflow, ta.priority,
            us.user_id as assignee_user_id, us.profilePicture as assignee_profile_pic, 
            CONCAT(us.firstName, ' ', us.lastName) as assignee, us.status,
            ta.legal_case_id as caseId, ta.due_date, ca.subject as caseSubject, 
            ca.category as case_category, pbco.task_board_column_id AS column_id, 
            users.user_group_id, ta.reporter, 
            CONCAT(us_reporter.firstName, ' ', us_reporter.lastName) as reporter_name, 
            ttl.name",
                false
            ],
            "join" => [
                ["user_profiles us", "us.user_id = ta.assigned_to", "left"],
                ["user_profiles us_reporter", "us_reporter.user_id = ta.reporter", "left"],
                ["legal_cases ca", "ca.id = ta.legal_case_id", "left"],
                ["task_board_column_options pbco", "pbco.task_status_id = ta.task_status_id", "left"],
                ["users", "users.id = us.id", "left"],
                ["provider_groups_users", "provider_groups_users.user_id = us.user_id", "left"],
                ["task_types_languages ttl", "ttl.task_type_id = ta.task_type_id AND ttl.language_id = '" . $lang_id . "'", "left"]
            ],
            "where" => [
                ["ta.archived", "no"],
                ["ta.hideFromBoard IS NULL", NULL, false],
                [
                    "(ta.legal_case_id IS NULL OR (ca.isDeleted = '0' AND (ca.private IS NULL OR ca.private = 'no' OR 
                (ca.private = 'yes' AND (ca.createdBy = '" . $this->logged_user_id . "' OR 
                ca.user_id = '" . $this->logged_user_id . "' OR 
                ca.id IN (SELECT legal_case_id FROM legal_case_users WHERE user_id = '" . $this->logged_user_id . "') OR 
                '" . $this->override_privacy . "' = 'yes')))) AND 
                (ta.private IS NULL OR ta.private = 'no' OR
                (ta.private = 'yes' AND (ta.createdBy = '" . $this->logged_user_id . "' OR 
                ta.assigned_to = '" . $this->logged_user_id . "' OR 
                ta.id IN (SELECT task_id FROM task_users WHERE user_id = '" . $this->logged_user_id . "') OR 
                '" . $this->override_privacy . "' = 'yes')))",
                    NULL, false
                ]
            ],
            "group_by" => ["ta.id", "ta.title"],
            "order_by" => "ta.due_date ASC, ta.id ASC"
        ];

        if ($columns_ids) {
            $query["where"][] = ["task_board_column_id IN (" . $columns_ids . ")", NULL, false];
        }

        // Apply saved filters
        $this->get_saved_filter($saved_filters, $quick_filter);
        if (!empty($saved_filters["filters"])) {
            foreach ($saved_filters["filters"] as $_filter) {
                $this->prep_k_filter($_filter, $query, $saved_filters["logic"]);
            }
        }

        $this->prep_query($query);
        $tasks_result = $this->ci->db->get($table)->result_array();

        // Organize results
        foreach ($columns as $column) {
            $return["columns"][$column["id"]][] = $column;
            $return["tasks"][$column["id"]] = [];
        }

        foreach ($tasks_result as $task) {
            if (isset($task["column_id"])) {
                $return["tasks"][$task["column_id"]][] = $task;
            }
        }

        return $return;
    }
    public function get_task_Board_columns()
    {
        $table = $this->_table;
        $this->_table = "task_boards pb";
        $query = ["select" => ["pb.id, pb.name, COUNT(task_board_columns.id) as nbOfColumns, CONCAT( modifiedUser.firstName, ' ', modifiedUser.lastName ) as lastModifiedBy, pb.modifiedOn as lastModifiedOn, CONCAT( createdUser.firstName, ' ', createdUser.lastName ) as createdBy,createdUser.status as createdUserStatus,modifiedUser.status as modifiedUserStatus, pb.createdOn as createdOn", false], "join" => [["task_board_columns", "pb.id = task_board_columns.task_board_id", "left"], ["user_profiles createdUser", "createdUser.user_id = pb.createdBy", "left"], ["user_profiles modifiedUser", "modifiedUser.user_id = pb.modifiedBy", "left"]], "group_by" => "pb.id", "order_by" => ["pb.name asc"]];
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
class mysqli_Task_board_column extends mysql_Task_board_column
{
}
class sqlsrv_Task_board_column extends mysql_Task_board_column
{
    public function load_all_options($id)
    {
        if ($id < 1) {
            return [];
        }
        return $this->load_all(["select" => ["id AS task_board_column_id, task_status_id=STUFF((SELECT '|'+CAST(task_status_id AS VARCHAR) FROM task_board_column_options WHERE task_board_column_options.task_board_column_id = task_board_columns.id FOR XML PATH('')), 1,1,'')"], "where" => ["task_board_id", 1 * $id]]);
    }
    public function get_task_Board_columns()
    {
        $table = $this->_table;
        $this->_table = "task_boards pb";
        $query = ["select" => ["pb.id, pb.name, (SELECT COUNT(0) FROM task_board_columns WHERE pb.id = task_board_columns.task_board_id) AS nbOfColumns, ( modifiedUser.firstName + ' ' + modifiedUser.lastName ) as lastModifiedBy,createdUser.status as createdUserStatus,modifiedUser.status as modifiedUserStatus, pb.modifiedOn as lastModifiedOn, ( createdUser.firstName + ' ' + createdUser.lastName ) as createdBy, pb.createdOn as createdOn", false], "join" => [["user_profiles createdUser", "createdUser.user_id = pb.createdBy", "left"], ["user_profiles modifiedUser", "modifiedUser.user_id = pb.modifiedBy", "left"]], "order_by" => ["pb.name asc"]];
        $records = $this->load_all($query);
        $this->_table = $table;
        return $records;
    }
    public function get_task_board_column_options_data($taskBoardId, $filters, $saved_filters = [], $quick_filter = [], $limit = true)
    {
        $return = [];
        $return["columns"] = [];
        $return["tasks"] = [];
        $table = $this->_table . " pbc";
        $this->ci->load->model("language");
        $lang_id = $this->ci->language->get_id_by_session_lang();
        $this->prep_query(["select" => ["pbc.id, pbc.name, pbc.color, tbco.task_status_id,ts.name as status_name,workflows_related = STUFF((SELECT DISTINCT ',' + CAST(relation.workflow_id AS varchar) from task_workflow_status_relation as relation WHERE relation.status_id = tbco.task_status_id FOR XML PATH('')), 1, 1, '')"], "join" => [["task_board_column_options tbco", "tbco.task_board_column_id = pbc.id", "left"], ["task_statuses ts", "ts.id = tbco.task_status_id", "left"]], "where" => ["pbc.task_board_id", $taskBoardId], "order_by" => ["pbc.columnOrder", "asc"]]);
        $result = $this->ci->db->get($table);
        $columns = $result->result_array();
        if (isset($columns) && !empty($columns)) {
            $columns_ids = implode(",", array_column($columns, "id"));
        }
        $table = "tasks ta";
        $this->ci->load->model("task", "taskfactory");
        $this->ci->task = $this->ci->taskfactory->get_instance();
        $this->get_saved_filter($saved_filters, $quick_filter);
        $query["select"] = ["ta.id, Max(ta.title) as title, Max(cast(ta.description as varchar(max))) as description,Max(ta.task_status_id) as task_status_id, Max(ta.workflow) as workflow, Max(ta.priority) as priority, \r\n        Max(us.user_id) as assignee_user_id, Max(us.profilePicture) as assignee_profile_pic, ( Max(us.firstName) + ' ' + Max(us.lastName) ) as assignee,Max(us.status) as status, \r\n        Max(ta.legal_case_id) as caseId, Max(ta.due_date) as due_date, Max(ca.subject) as caseSubject, Max(ca.category) as case_category, \r\n        Max(pbco.task_board_column_id) AS column_id, Max(users.user_group_id) as user_group_id, Max(ta.reporter) as reporter, (Max(us_reporter.firstName) + ' ' + Max(us_reporter.lastName)) as reporter_name, Max(cast(ttl.name as varchar(max))) as name", false];
        $query["join"] = [["user_profiles us", "us.user_id = ta.assigned_to", "left"], ["user_profiles us_reporter", "us_reporter.user_id = ta.reporter", "left"], ["legal_cases ca", "ca.id = ta.legal_case_id", "left"], ["task_board_column_options pbco", "pbco.task_status_id = ta.task_status_id", "left"], ["users", "users.id = us.id", "left"], ["provider_groups_users", "provider_groups_users.user_id = us.user_id", "left"], ["task_types_languages ttl", "ttl.task_type_id = ta.task_type_id and ttl.language_id = '" . $lang_id . "'", "left"]];
        $query["group_by"] = ["ta.id"];
        $query["where"][] = ["ta.archived ", "no"];
        $query["where"][] = ["ta.hideFromBoard IS NULL", NULL, false];
        $query["where"][] = ["(ta.legal_case_id IS NULL OR (ca.isDeleted = '0' AND(ca.private IS NULL OR ca.private = 'no' OR (ca.private = 'yes' AND (" . "ca.createdBy = '" . $this->logged_user_id . "' OR ca.user_id = '" . $this->logged_user_id . "' OR ca.id IN (SELECT legal_case_id FROM legal_case_users WHERE user_id = '" . $this->logged_user_id . "') OR '" . $this->override_privacy . "' = 'yes'" . ")" . ")" . ")" . ")" . ")" . " AND " . "(" . "ta.private IS NULL OR ta.private = 'no' OR" . "(" . "ta.private = 'yes' AND " . "(" . "ta.createdBy = '" . $this->logged_user_id . "' OR ta.assigned_to = '" . $this->logged_user_id . "' OR " . "ta.id IN (SELECT task_id FROM task_users WHERE user_id = '" . $this->logged_user_id . "') OR '" . $this->override_privacy . "' = 'yes'" . ")" . ")" . ")"];
        if (isset($columns_ids) && !empty($columns_ids)) {
            $query["where"][] = ["task_board_column_id IN (" . $columns_ids . ")", NULL, false];
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
        $tasks_result = $temp_result->result_array();
        if (isset($columns) && !empty($columns)) {
            foreach ($columns as $column) {
                $return["columns"][$column["id"]][] = $column;
                $return["tasks"][$column["id"]] = [];
            }
            if (isset($tasks_result) && !empty($tasks_result)) {
                foreach ($tasks_result as $task) {
                    array_push($return["tasks"][$task["column_id"]], $task);
                }
            }
        }
        return $return;
    }
}
