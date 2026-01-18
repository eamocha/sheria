<?php

if (!defined("BASEPATH")) {
    exit("No direct script access allowed");
}
class Conveyancing_activity extends My_Model_Factory
{

}
class mysqli_Conveyancing_activity extends My_model
{
    protected $_table = 'conveyancing_activity';
    protected $modelName = "conveyancing_activities";

    protected $_fieldsNames = ['id', 'conveyancing_instrument_id', 'action', 'activity_details', 'activity_status', 'modifiedOn', 'modifiedBy', 'createdOn', 'createdBy', 'createdByChannel'];
    protected $primaryKey = 'id';
    public function __construct()
    {
        $this->ci =& get_instance();
        parent::__construct();
        $this->validate = [
        'conveyancing_instrument_id' => ['rule' => 'numeric', 'message' => 'Instrument ID must be a valid number'],
        'action' => ['rule' => 'required', 'message' => 'Action is required', 'pattern' => '/^[A-Za-z]{1,50}$/', 'pattern_message' => 'Action must be a string up to 50 characters'],
        'activity_details' => ['rule' => 'max_length', 'length' => 65535, 'message' => 'Activity details cannot exceed 65535 characters'],
        'activity_status' => ['rule' => 'max_length', 'length' => 50, 'message' => 'Activity status cannot exceed 50 characters', 'pattern' => '/^[A-Za-z]{0,50}$/', 'pattern_message' => 'Activity status must be a string up to 50 characters'],
        'modifiedOn' => ['rule' => 'datetime', 'message' => 'Modified date must be a valid datetime (YYYY-MM-DD HH:MM:SS)', 'pattern' => '/^\d{4}-(0[1-9]|1[0-2])-(0[1-9]|[12]\d|3[01]) [0-2]\d:[0-5]\d:[0-5]\d$/'],
        'modifiedBy' => ['rule' => 'numeric', 'message' => 'Modified by must be a valid user ID'],
        'createdOn' => ['rule' => 'required|datetime', 'message' => 'Created date is required and must be a valid datetime (YYYY-MM-DD HH:MM:SS)', 'pattern' => '/^\d{4}-(0[1-9]|1[0-2])-(0[1-9]|[12]\d|3[01]) [0-2]\d:[0-5]\d:[0-5]\d$/'],
        'createdBy' => ['rule' => 'required|numeric', 'message' => 'Creator ID is required and must be a valid user ID'],
        'createdByChannel' => ['rule' => 'regex', 'pattern' => '/^[A-Z]{1,3}$/', 'message' => 'Created by channel must be a 1-3 character uppercase code (e.g., WEB, API)']
    ];
    }

    public function load_conveyancing_activities($conveyancing_instrument_id)
    {
        // Define the query to fetch the activities
        $query["select"] = [
            "conveyancing_activity.*,
        cat.name as activity_type_name,  // Get activity type name from the join
        CONCAT(up.firstName, ' ', up.lastName) as performed_by_name, // Get performer's name
        "
        ];
        $query["join"] = [
            ["conveyancing_activity_type as cat", "cat.id = conveyancing_activity.activity_type", "left"],
            ["user_profiles as up", "up.user_id = conveyancing_activity.performed_by", "left"],
        ];
        $query["where"] = ["conveyancing_instrument_id", $conveyancing_instrument_id]; // Filter by conveyancing instrument ID
        $query["order_by"] = ["conveyancing_activity.activity_date DESC"]; // Order by date, latest first

        $result = $this->load_all($query); // Use the model's load_all method to fetch data


        return $result; // Return the fetched activities
    }

}
class mysql_Conveyancing_activity extends mysqli_Conveyancing_activity
{

}
class sqlsrv_Conveyancing_activity extends mysql_Conveyancing_activity
{

}