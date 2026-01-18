<?php
if (!defined("BASEPATH")) {
    exit("No direct script access allowed");
}

class Correspondence_workflow extends My_Model_Factory
{
}
class mysqli_Correspondence_workflow extends My_model
{

    protected $_table = 'correspondence_workflow';
    protected $modelName = "correspondence_workflow";
    protected $_listFieldName = "correspondence_id";
    
    protected $_fieldsNames = ['id', 'correspondence_id', 'workflow_step_id', 'status', 'createdOn', 'modifiedOn', 'createdBy', 'modifiedBy', 'comments'];

    protected $primaryKey = 'id';

    protected $builtInLogs = true;

 
    public function __construct()
    {
        parent::__construct(); 
        $this->ci =& get_instance(); 

        $this->validate = [
            "correspondence_id" => [
                "required" => true,
                "allowEmpty" => false,
                "rule" => "numeric",
                "message" => $this->ci->lang->line("cannot_be_blank_rule") 
            ],
            "workflow_step_id" => [
                "required" => true,
                "allowEmpty" => false,
                "rule" => "numeric",
                "message" => $this->ci->lang->line("cannot_be_blank_rule")
            ],
            "status" => [
                "required" => true,
                "allowEmpty" => false,
                "rule" => ["minLength", 1], 
                "message" => $this->ci->lang->line("cannot_be_blank_rule")
            ],
            "createdBy" => [ // Assuming createdBy is typically required and numeric
                "required" => true,
                "allowEmpty" => false,
                "rule" => "numeric",
                "message" => $this->ci->lang->line("cannot_be_blank_rule")
            ],
            // 'comments' can be optional, 'createdOn' and 'modifiedOn' often handled by database defaults/triggers
        ];
    }

    /**
     * Loads workflow entries for a specific correspondence.
     * This is an example method, similar to load_progress or load_stage_progress.
     *
     * @param int $correspondence_id The ID of the correspondence to load workflow entries for.
     * @param bool $show_all Whether to show all entries or paginate (similar to load_progress).
     * @return array An array containing 'records' and 'count' for the workflow entries.
     */
    public function load_correspondence_workflow_entries($correspondence_id, $show_all = true)
    {
        // Example of pagination setup, mimicking Conveyancing_stage_progress
        if (!$show_all) {
            $this->pagination_config_set("inPage", 10); // Example pagination size
        }

        $query = [];
        $query["select"] = [
            // Select all columns from correspondence_workflow, and join to get step_name
            "SQL_CALC_FOUND_ROWS correspondence_workflow.id,
            correspondence_workflow.correspondence_id,
            correspondence_workflow.workflow_step_id,
            correspondence_workflow.status,
            correspondence_workflow.createdOn,
            correspondence_workflow.modifiedOn,
            correspondence_workflow.createdBy,
            correspondence_workflow.comments,
            cws.step_name AS workflow_step_name,
            CONCAT(creator.firstName, ' ', creator.lastName) AS created_by_name",
            false // Allows raw SQL in select, like CONCAT
        ];

        $query["join"] = [
            ["correspondence_workflow_steps cws", "cws.id = correspondence_workflow.workflow_step_id", "left"],
            ["user_profiles creator", "creator.user_id = correspondence_workflow.createdBy", "left"]
        ];

        $query["where"] = ["correspondence_workflow.correspondence_id" , $correspondence_id];
        $query["order_by"] = ["correspondence_workflow.createdOn ASC"]; // Order by creation date

        $response["records"] = $show_all ? parent::load_all($query) : parent::paginate($query, ["urlPrefix" => ""]);

        // Fetch total count for pagination if not showing all
        if (!$show_all) {
            $response["count"] = $this->ci->db->query("SELECT FOUND_ROWS() AS `count`")->row()->count;
        } else {
            $response["count"] = count($response["records"]); // Simple count for load_all
        }

        return $response;
    }

    /**
     * Loads a single workflow entry by its ID.
     *
     * @param int $id The ID of the workflow entry.
     * @return array|null The workflow entry data, or null if not found.
     */
    public function load_workflow_entry($id)
    {
        $query = [];
        $query["select"] = [
            "correspondence_workflow.id,
            correspondence_workflow.correspondence_id,
            correspondence_workflow.workflow_step_id,
            correspondence_workflow.status,
            correspondence_workflow.createdOn,
            correspondence_workflow.modifiedOn,
            correspondence_workflow.createdBy,
            correspondence_workflow.comments,
            cws.step_name AS workflow_step_name,
            CONCAT(creator.firstName, ' ', creator.lastName) AS created_by_name",
            false
        ];

        $query["join"] = [
            ["correspondence_workflow_steps cws", "cws.id = correspondence_workflow.workflow_step_id", "left"],
            ["user_profiles creator", "creator.user_id = correspondence_workflow.createdBy", "left"]
        ];

        $query["where"] = ["correspondence_workflow.id" => $id];
        $response = parent::load($query); // Assuming parent::load returns a single row or null
        return $response;
    }

}

class mysql_Correspondence_workflow extends mysqli_Correspondence_workflow
{

}


class sqlsrv_Correspondence_workflow extends mysql_Correspondence_workflow
{
    public function load_correspondence_workflow_entries($correspondence_id, $show_all = true)
    {
        if (!$show_all) {
            $this->pagination_config_set("inPage", 10);
        }

        $query = [];
        $query["select"] = [
            "COUNT(*) OVER() AS total_rows,
            correspondence_workflow.id,
            correspondence_workflow.correspondence_id,
            correspondence_workflow.workflow_step_id,
            correspondence_workflow.status,
            correspondence_workflow.createdOn,
            correspondence_workflow.modifiedOn,
            correspondence_workflow.createdBy,
            correspondence_workflow.comments,
            cws.step_name AS workflow_step_name,
            (creator.firstName + ' ' + creator.lastName) AS created_by_name", // SQL Server CONCAT
            false
        ];

        $query["join"] = [
            ["correspondence_workflow_steps cws", "cws.id = correspondence_workflow.workflow_step_id", "left"],
            ["user_profiles creator", "creator.user_id = correspondence_workflow.createdBy", "left"]
        ];

        $query["where"] = ["correspondence_workflow.correspondence_id" => $correspondence_id];
        $query["order_by"] = ["correspondence_workflow.createdOn ASC"];

        $response["records"] = $show_all ? parent::load_all($query) : parent::paginate($query, ["urlPrefix" => ""]);

        // For SQL Server, total_rows is part of the first record if COUNT(*) OVER() is used
        $response["count"] = $response["records"][0]["total_rows"] ?? false;

        return $response;
    }

    public function load_workflow_entry($id)
    {
        $query = [];
        $query["select"] = [
            "correspondence_workflow.id,
            correspondence_workflow.correspondence_id,
            correspondence_workflow.workflow_step_id,
            correspondence_workflow.status,
            correspondence_workflow.createdOn,
            correspondence_workflow.modifiedOn,
            correspondence_workflow.createdBy,
            correspondence_workflow.comments,
            cws.step_name AS workflow_step_name,
            (creator.firstName + ' ' + creator.lastName) AS created_by_name", // SQL Server CONCAT
            false
        ];

        $query["join"] = [
            ["correspondence_workflow_steps cws", "cws.id = correspondence_workflow.workflow_step_id", "left"],
            ["user_profiles creator", "creator.user_id = correspondence_workflow.createdBy", "left"]
        ];

        $query["where"] = ["correspondence_workflow.id" => $id];
        $response = parent::load($query);
        return $response;
    }
}
