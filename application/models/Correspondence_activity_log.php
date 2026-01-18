<?php
if (!defined("BASEPATH")) {
    exit("No direct script access allowed");
}


class Correspondence_activity_log extends My_Model_Factory
{
}

class mysqli_Correspondence_activity_log extends My_model
{
    protected $_table = 'correspondence_activity_log';
    protected $modelName = "correspondence_activity_log";
    protected $_fieldsNames = ['id', 'correspondence_id', 'user_id', 'action','details','createdOn','modifiedOn','createdBy','modifiedBy'];

    protected $primaryKey = 'id';

    protected $builtInLogs = true;
    public function __construct()
    {
        parent::__construct();
        $this->ci =& get_instance();

        $this->validate = [
            'correspondence_id' => ["required" => true, "allowEmpty" => false, "rule" => "numeric", "message" => $this->ci->lang->line("cannot_be_blank_rule")],
            'user_id' => [ "required" => true,"allowEmpty" => false,"rule" => "numeric","message" => $this->ci->lang->line("cannot_be_blank_rule")],
            'action' => [
                "required" => true,
                "allowEmpty" => false,
                "rule" => ["minLength", 1],
                "message" => $this->ci->lang->line("cannot_be_blank_rule")
            ],
            // 'details' can be optional
            // 'createdOn' has a default in the DB, so not required for validation
            'createdBy' => [ // The user who created the log entry (often same as user_id)
                "required" => true,
                "allowEmpty" => false,
                "rule" => "numeric",
                "message" => $this->ci->lang->line("cannot_be_blank_rule")
            ],
            // 'modifiedOn' and 'modifiedBy' are nullable in schema, so not required here
        ];
    }

    /**
     * Loads activity logs for a specific correspondence.
     * This method is similar to load_conveyancing_activities.
     *
     * @param int $correspondence_id The ID of the correspondence to load logs for.
     * @return array An array of activity log records.
     */
    public function load_correspondence_activity_logs($correspondence_id)
    {
        $query = [];
        $query["select"] = [
            "correspondence_activity_log.*",
            // Join to user_profiles to get the name of the user who performed the action
            "CONCAT(up.firstName, ' ', up.lastName) AS performed_by_name",
            false // Allows raw SQL in select, like CONCAT
        ];

        $query["join"] = [
            // Join to user_profiles to get the name of the user associated with the log entry
            ["user_profiles AS up", "up.user_id = correspondence_activity_log.user_id", "left"],
        ];

        // Filter by correspondence ID
        $query["where"] = ["correspondence_activity_log.correspondence_id" => $correspondence_id];
        // Order by creation date, latest first
        $query["order_by"] = ["correspondence_activity_log.createdOn DESC"];

        // Use the model's load_all method to fetch data
        $result = $this->load_all($query);

        return $result; // Return the fetched activities
    }

}

class mysql_Correspondence_activity_log extends mysqli_Correspondence_activity_log
{
}

class sqlsrv_Correspondence_activity_log extends mysql_Correspondence_activity_log
{


    /**
     * Loads activity logs for a specific correspondence (SQL Server specific).
     * Overrides the parent method to use SQL Server's CONCAT operator (+).
     *
     * @param int $correspondence_id The ID of the correspondence to load logs for.
     * @return array An array of activity log records.
     */
    public function load_correspondence_activity_logs($correspondence_id)
    {
        $query = [];
        $query["select"] = [
            "correspondence_activity_log.*",
            // SQL Server CONCAT operator (+)
            "(up.firstName + ' ' + up.lastName) AS performed_by_name",
            false
        ];

        $query["join"] = [
            ["user_profiles AS up", "up.user_id = correspondence_activity_log.user_id", "left"],
        ];

        $query["where"] = ["correspondence_activity_log.correspondence_id" => $correspondence_id];
        $query["order_by"] = ["correspondence_activity_log.createdOn DESC"];

        $result = $this->load_all($query);

        return $result;
    }
}
