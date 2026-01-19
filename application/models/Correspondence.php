<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Correspondence extends My_Model_Factory {
    // This class is typically empty and serves as a factory for the actual model.
}

class mysql_Correspondence extends My_Model { // Base model for MySQL, will be extended by sqlsrv_Correspondence
    protected $modelName = "Correspondence";
    protected $modelCode = "COR";
    protected $_table = "correspondences"; // Default table name without alias
    protected $_listFieldName = "subject"; // Default field for listing/searching
    protected $_fieldsNames = ["id", "correspondence_type_id", "sender", "recipient", "subject", "body", "date_received", "document_date", "reference_number", "status_id", "requires_signature", "assigned_to","assignee_team_id", "filename", "comments", "createdOn", "modifiedOn", "createdBy", "modifiedBy", "document_id", "document_type_id","category", "action_required", "related_to_object","related_to_object_id", "sender_contact_type", "recipient_contact_type","due_date","date_dispatched","mode_of_receipt","mode_of_dispatch"];
    protected $allowedNulls = ["sender", "recipient", "category", "action_required", "related_to_object","related_to_object_id", "sender_contact_type", "recipient_contact_type", "mode_of_dispatch","body", "date_received", "document_date", "reference_number", "assigned_to", "filename", "comments", "modifiedOn", "modifiedBy", "document_id", "document_type_id"];
    protected $priorityValues = ["critical", "high", "medium", "low"];
    protected $priorityValuesKeys = ["'critical'", "'high'", "'medium'", "'low'"];
    protected $categoryValues = ["Incoming", "Outgoing"];
    protected $actionRequiredValues = ["Review", "Sign","Draft","Action","Advice","Investigate","Respond","Diarize","Note","Other"];
    protected $relatedToObjectValues = ["Civil_case", "Criminal_case", "Matter","Opinion","Contract","Agreement","Conveyancing","Correspondence"];
    protected $requiresSignatureValues = ["yes", "no"];
    protected $builtInLogs = true; // Assuming you want built-in logging

    // Validation rules for correspondence fields
    protected $validate = [];
    protected $ci=null;

    public function __construct() {
        parent::__construct();
        $this->ci =& get_instance();
        // Define validation rules
        $this->validate = [
            "category" => [
            "required" => true,
            "allowEmpty" => false,
            "rule" => ["inList", $this->categoryValues],
            "message" => $this->ci->lang->line("cannot_be_blank_rule")
            ],
            "correspondence_type_id" => [
            "required" => true,
            "allowEmpty" => false,
            "rule" => "numeric",
            "message" => $this->ci->lang->line("cannot_be_blank_rule")
            ],
            "subject" => [
            "isRequired" => [
                "required" => true,
                "allowEmpty" => false,
                "rule" => ["minLength", 1],
                "message" => $this->ci->lang->line("cannot_be_blank_rule")
            ],
            "maxLength" => [
                "rule" => ["maxLength", 255],
                "message" => sprintf($this->ci->lang->line("max_characters"), 255)
            ]
            ],
            //body field is required for incoming correspondence
            "body" => [
            "isRequired" => [
                "required" => false,
                "allowEmpty" => true,
                "rule" => ["minLength", 1],
                "message" => $this->ci->lang->line("cannot_be_blank_rule")
            ],
            "maxLength" => [
                "rule" => ["maxLength", 255],
                "message" => sprintf($this->ci->lang->line("max_characters"), 255)
            ]
            ],


            "due_date" => [
            "isRequired" => [
                "required" => true,
                "allowEmpty" => false,
                "rule" => ["minLength", 1],
                "message" => $this->ci->lang->line("cannot_be_blank_rule")
            ]
            ],
            "document_date" => [
            "isRequired" => [
                "required" => true,
                "allowEmpty" => false,
                "rule" => ["minLength", 1],
                "message" => $this->ci->lang->line("cannot_be_blank_rule")
            ]
            ],
            "status_id" => [
            "required" => true,
            "allowEmpty" => false,
            "rule" => "numeric",
            "message" => $this->ci->lang->line("cannot_be_blank_rule")
            ],
            "sender" => [
            "required" => false,
            "allowEmpty" => true,
            "rule" => "numeric",
            "message" => sprintf($this->ci->lang->line("is_numeric_rule"), "Sender ID")
            ],
            "recipient" => [
            "required" => false,
            "allowEmpty" => true,
            "rule" => "numeric",
            "message" => sprintf($this->ci->lang->line("is_numeric_rule"), "Recipient ID")
            ],
            "date_received" => [
            "isRequired" => [
                "required" => false,
                "allowEmpty" => true,
                "rule" => ["minLength", 1],
                "message" => $this->ci->lang->line("cannot_be_blank_rule")
            ]
            ],
            "requires_signature" => [
            "required" => true,
            "allowEmpty" => false,
            "rule" => ["inList", ["Yes", "No", "yes", "no"]],
            "message" => "Invalid value for 'Requires Signature'"
            ],
            "assigned_to" => [
            "required" => false,
            "allowEmpty" => true,
            "rule" => "numeric",
            "message" => sprintf($this->ci->lang->line("is_numeric_rule"), "Assigned To ID")
            ],
            "document_id" => [
            "required" => false,
            "allowEmpty" => true,
            "rule" => "numeric",
            "message" => sprintf($this->ci->lang->line("is_numeric_rule"), "Document ID")
            ],
            "document_type_id" => [
            "required" => false,
            "allowEmpty" => true,
            "rule" => "numeric",
            "message" => sprintf($this->ci->lang->line("is_numeric_rule"), "Document Type ID")
            ],
            // "reference_number" => [
            // "required" => false,
            // "allowEmpty" => true,
            // "rule" => ["unique", "correspondences.reference_number"],
            // "message" => $this->ci->lang->line("field_must_be_unique_rule")
            // ],
        ];

        // Assuming these are loaded from session or authentication system
        $this->logged_user_id = $this->ci->session->userdata('user_id'); // Example
        $this->override_privacy = false; // Example, adjust as per your system
    }

    /**
     * Get the total count of all correspondences.
     * @return int
     */
    public function get_correspondence_count() {
        return $this->ci->db->count_all_results($this->_table);
    }

    /**
     * Get the total count of all files (from correspondence_documents table).
     * @return int
     */
    public function get_file_count() {
        return $this->ci->db->count_all_results('correspondence_documents');
    }

    /**
     * Get all incoming correspondence.
     * Assumes correspondence_type_id 1 is 'Incoming Letter' based on sample data.
     * @return array
     */
    public function get_incoming_correspondence() {
        $_original_table = $this->_table;
        $this->_table = "correspondences as c";

        $query = [];
        $query["select"] = ["c.*, ct.name as correspondence_type_name, cs.name as status_name,
         (CASE WHEN c.sender_contact_type= 'company' THEN sender_comp.name ELSE CONCAT(sender_contact.firstName, ' ', sender_contact.lastName) END) as sender,
            (CASE WHEN c.sender_contact_type= 'company' THEN recipient_comp.name ELSE CONCAT(recipient_contact.firstName, ' ', recipient_contact.lastName) END )as recipient,
           CONCAT(assignee.firstName, ' ', assignee.lastName) as assignee,
    CONCAT(creator.firstName, ' ', creator.lastName) as createdBy,
    CONCAT(modifier.firstName, ' ', modifier.lastName) as modifiedBy,
    CONCAT('COR-', c.id) as cid
        "];
        $query["join"] = [
            ["correspondence_types ct", "ct.id = c.correspondence_type_id", "left"],
            ["correspondence_statuses cs", "cs.id = c.status_id", "left"],
            ["contacts sender_contact", "sender_contact.id = c.sender", "left"],
            ["companies sender_comp", "sender_comp.id = c.sender and c.sender_contact_type= 'company'", "left"],
            ["contacts recipient_contact", "recipient_contact.id = c.recipient", "left"],
            ["companies recipient_comp", "recipient_comp.id = c.recipient and c.sender_contact_type= 'company'", "left"],
            ["user_profiles assignee", "assignee.user_id = c.assigned_to", "left"],
            ["user_profiles creator", "creator.user_id = c.createdBy", "left"],
            ["user_profiles modifier", "modifier.user_id = c.modifiedBy", "left"],
        ];
        $query["where"] = ["c.category" ,"Incoming"]; // Filter for incoming correspondence
        

        $query["order_by"] = ["c.createdOn DESC"];

        $result = $this->load_all($query);
        $this->_table = $_original_table; // Revert to original table name
        return $result;
    }

    /**
     * Get all outgoing correspondence.
     * Assumes correspondence_type_id 2 is 'Outgoing Letter' based on sample data.
     * @return array
     */
    public function get_outgoing_correspondence() {
        $_original_table = $this->_table;
        $this->_table = "correspondences as c";

        $query = [];
        $query["select"] = ["c.*,  cs.name as status_name,dt.name as document_type_name, c.date_received as date_sent,c.reference_number as ref_number,
       (CASE WHEN c.sender_contact_type= 'company' THEN sender_comp.name ELSE CONCAT(sender.firstName, ' ', sender.lastName) END )as sender,
         (CASE WHEN c.recipient_contact_type= 'company' THEN recipient_comp.name ELSE CONCAT(recipient.firstName, ' ', recipient.lastName) END )as recipient,
    CONCAT(assignee.firstName, ' ', assignee.lastName) as assignee,
    CONCAT(creator.firstName, ' ', creator.lastName) as createdBy,
    CONCAT(modifier.firstName, ' ', modifier.lastName) as modifiedBy,
    CONCAT('COR-', c.id) as cid
        "];
        $query["join"] = [
            ["correspondence_types ct", "ct.id = c.correspondence_type_id", "left"],
            ["correspondence_statuses cs", "cs.id = c.status_id", "left"],
            ["correspondence_document_types dt", "dt.id = c.document_type_id", "left"],
            ["contacts sender", "sender.id = c.sender", "left"],
            ["companies sender_comp", "sender_comp.id = c.sender and c.sender_contact_type= 'company'", "left"],
            ["contacts recipient", "recipient.id = c.recipient", "left"],
            ["companies recipient_comp", "recipient_comp.id = c.recipient and c.sender_contact_type= 'company'", "left"],
            ["user_profiles assignee", "assignee.user_id = c.assigned_to", "left"],
            ["user_profiles creator", "creator.user_id = c.createdBy", "left"],
            ["user_profiles modifier", "modifier.user_id = c.modifiedBy", "left"],

        ];
        $query["where"] = ["c.category" ,"Outgoing"]; // Filter for outgoing correspondence

        $query["order_by"] = ["c.createdOn DESC"];

        $result = $this->load_all($query);
        $this->_table = $_original_table; // Revert to original table name
        return $result;
    }

    /**
     * Get a single correspondence item by ID.
     * @param int $id
     * @return object|null
     */
    public function get_correspondence_by_id($id) {
        $_original_table = $this->_table; // Store original table name
        $this->_table = "correspondences as c"; // Temporarily set alias

        $query = [];
        $query["select"] = ["c.*, ct.name as correspondence_type_name, cs.name as status_name,
           (CASE WHEN c.sender_contact_type= 'company' THEN sender_comp.name ELSE CONCAT(sender_contact.firstName, ' ', sender_contact.lastName) END) as sender_name,
            (CASE WHEN c.sender_contact_type= 'company' THEN recipient_contact.name ELSE CONCAT(recipient_contact.firstName, ' ', recipient_contact.lastName) END )as recipient_name,
           CONCAT(assignee.firstName, ' ', assignee.lastName) as assignee_name,
          CONCAT(creator.firstName, ' ', creator.lastName) as createdBy,
       CONCAT(modifier.firstName, ' ', modifier.lastName) as modifiedBy,
       CONCAT('COR-', c.id) as id"];
        $query["join"] = [
            ["correspondence_types ct", "ct.id = c.correspondence_type_id", "left"],
            ["correspondence_statuses cs", "cs.id = c.status_id", "left"],
            ["contacts sender_contact", "sender_contact.id = c.sender", "left"],
            ["companies sender_comp", "sender_comp.id = c.sender and c.sender_contact_type= 'company'", "left"],
            ["contacts recipient_contact", "recipient_contact.id = c.recipient", "left"],

            ["users assignee", "assignee.id = c.assigned_to", "left"] ,
            ["user_profiles creator", "creator.user_id = c.createdBy", "left"],
            ["user_profiles modifier", "modifier.user_id = c.modifiedBy", "left"],
        ];
        $query["where"] = ["c.id" => $id];

        $result = $this->load($query);
        $this->_table = $_original_table; // Revert to original table name
        return $result;
    }

    /**
     * Insert a new correspondence record.
     * @param array $data
     * @return int|bool Inserted ID or FALSE on failure
     */
    public function insert_correspondence($data) {
        return $this->insert($data);
    }

    /**
     * Update an existing correspondence record.
     * @param int $id
     * @param array $data
     * @return bool
     */
    public function update_correspondence($id, $data) {
        return $this->update($id, $data);
    }

    /**
     * Delete a correspondence record.
     * Also deletes related workflow entries and activity logs.
     * @param int $id
     * @return bool
     */
    public function delete_correspondence($id) {
        // Start a transaction to ensure atomicity
        $this->ci->db->trans_start();

        // Delete from correspondence_activity_log
        $this->ci->db->where('correspondence_id', $id);
        $this->ci->db->delete('correspondence_activity_log');

        // Delete from correspondence_workflow
        $this->ci->db->where('correspondence_id', $id);
        $this->ci->db->delete('correspondence_workflow');

        // Delete the main correspondence record
        $result = $this->delete($id);

        $this->ci->db->trans_complete();

        if ($this->ci->db->trans_status() === FALSE) {
            // Transaction failed
            return FALSE;
        }
        return $result;
    }

    /**
     * Get all correspondence types.
     * @return array
     */
    public function get_correspondence_types() {
        $table = $this->_table;
        $this->_table = 'correspondence_types';
        $result = $this->load_all();
        $this->_table = $table;
        return $result;
    }

    /**
     * Get all correspondence statuses.
     * @return array
     */
    public function get_correspondence_statuses() {
        $table = $this->_table;
        $this->_table = 'correspondence_statuses';
        $result = $this->load_all();
        $this->_table = $table;
        return $result;
    }

    /**
     * Insert a new workflow entry for a correspondence.
     * @param array $data
     * @return int|bool
     */
    public function insert_workflow($data) {
        $table = $this->_table;
        $this->_table = 'correspondence_workflow';
        $result = $this->insert($data);
        $this->_table = $table;
        return $result;
    }

    /**
     * Update the status of a specific workflow step for a correspondence.
     * @param int $correspondence_id
     * @param int $workflow_process_id
     * @param array $data
     * @return bool
     */
    public function update_workflow_status($correspondence_id, $workflow_process_id, $data) {
        $table = $this->_table;
        $this->_table = 'correspondence_workflow';
        $this->ci->db->where('correspondence_id', $correspondence_id);
        $this->ci->db->where('workflow_process_id', $workflow_process_id);
        $result = $this->ci->db->update($this->_table, $data);
        $this->_table = $table;
        return $result;
    }
    public function get_workflow_processes_by_correspondence_type1($correspondence_id,$correspondence_type_id)
    {  $table = $this->_table;
        $this->_table = 'correspondence_workflow_steps cws';
        $query["select"] = ["cws.id,cws.correspondence_type_id,cws.name as step_name, 
        COALESCE(cw.comments, cws.comment) as remarks,
           COALESCE(cw.status, 'pending') as workflow_status,
         COALESCE(CAST(cw.createdOn AS nvarchar(50)), 'Not yet') as date_actioned,            
        CONCAT_WS(' ',actionBy.firstName,  actionBy.lastName) AS step_created_by_name,
        CONCAT_WS(' ',modifier.firstName,  modifier.lastName) AS step_modified_by_name,"];

        $query["join"] = [
            ["correspondence_workflow cw", "cw.workflow_step_id = cws.id AND cw.correspondence_id=".(int)$correspondence_id, "left"],
            ["user_profiles actionBy", "actionBy.user_id = cw.createdBy", "left"],
            ["user_profiles as modifier", "modifier.user_id = cw.modifiedBy", "left"]];
        $query["where"] = ['cws.correspondence_type_id', $correspondence_type_id];

        $query["order_by"] = ['sequence_order ASC'];

       // $this->ci->output->enable_profiler(TRUE);
        $result = parent::load_all($query);

        $this->_table = $table;
        return $result;

    }


    /**
     * Get activity logs for a specific correspondence.
     * @param int $correspondence_id
     * @return array
     */
    public function get_correspondence_activity_logs($correspondence_id) {
        $_original_table = $this->_table;
        $this->_table = 'correspondence_activity_log cal';

        $query["select"] = ["cal.*  ,        
          CONCAT(creator.firstName, ' ', creator.lastName) as createdBy,
       CONCAT(modifier.firstName, ' ', modifier.lastName) as modifiedBy,
       CONCAT('COR-', cal.correspondence_id) as cid"];
        $query["join"] = [
            ["user_profiles creator", "creator.user_id = cal.createdBy", "left"],
            ["user_profiles modifier", "modifier.user_id = cal.modifiedBy", "left"],
        ];
        $query["where"] = ["correspondence_id" , $correspondence_id];
        $query["order_by"] = ["createdOn ASC"]; // Order by ASC to get progression
        $result = $this->load_all($query);
        $this->_table = $_original_table;
        return $result;
    }

    /**
     * Get dashboard statistics for correspondences.
     * @param array $filters ['year' => 2024, 'month' => 5, 'type' => 'Letter']
     * @return array
     */
    public function get_dashboard_stats($filters = [])
    {
        $db = $this->ci->db;
        $where = [];

        // Filter by year
        if (!empty($filters['year'])) {
            $where[] = "YEAR(date_received) = " . (int)$filters['year'];
        }
        // Filter by month
        if (!empty($filters['month'])) {
            $where[] = "MONTH(date_received) = " . (int)$filters['month'];
        }

        if (!empty($filters['type'])) {
            $where[] = "correspondence_type_id = " . (int)$filters['type'];
        }

        // Build WHERE clause
        $where_sql = $where ? 'WHERE ' . implode(' AND ', $where) : '';

        // 1. Summary stats
        $stats_sql = "
        SELECT
            COUNT(*) as total,
            SUM(cs.name = 'Completed') as completed,
            SUM(cs.name = 'Ongoing') as ongoing,
            SUM(cs.name = 'Pending') as pending
        FROM correspondences
        LEFT JOIN correspondence_statuses cs ON cs.id = correspondences.status_id
        " . ($filters['type'] ? 'LEFT JOIN correspondence_types ct ON ct.id = correspondences.correspondence_type_id' : '') . "
        $where_sql
    ";
        $stats = $db->query($stats_sql)->row_array();

        // 2. Trend data (correspondences per day in selected month/year)
        $trend_sql = "
        SELECT DATE(date_received) as date, COUNT(*) as count
        FROM correspondences
        " . ($filters['type'] ? 'LEFT JOIN correspondence_types ct ON ct.id = correspondences.correspondence_type_id' : '') . "
        $where_sql
        GROUP BY DATE(date_received)
        ORDER BY DATE(date_received) ASC
        LIMIT 31
    ";
        $trend = $db->query($trend_sql)->result_array();

        // 3. Status distribution
        $status_sql = " SELECT cs.name as status, COUNT(*) as count
        FROM correspondences
        LEFT JOIN correspondence_statuses cs ON cs.id = correspondences.status_id
        " . ($filters['type'] ? 'LEFT JOIN correspondence_types ct ON ct.id = correspondences.correspondence_type_id' : '') . "
        $where_sql
        GROUP BY cs.name
    ";
        $status = $db->query($status_sql)->result_array();

        // 4. Recent activity (last 10 correspondences)
        $recent_sql = "
        SELECT c.id, c.reference_number, ct.name as type, cs.name as status, c.date_received, up.firstName, up.lastName
        FROM correspondences c
        LEFT JOIN correspondence_types ct ON ct.id = c.correspondence_type_id
        LEFT JOIN correspondence_statuses cs ON cs.id = c.status_id
        LEFT JOIN user_profiles up ON up.user_id = c.assigned_to
        " . ($filters['type'] ? 'LEFT JOIN correspondence_types ct2 ON ct2.id = c.correspondence_type_id' : '') . "
        $where_sql
        ORDER BY c.date_received DESC
        LIMIT 10
    ";
        $recent = $db->query($recent_sql)->result_array();

        // Format trend data for chart
        $trend_dates = [];
        $trend_counts = [];
        foreach ($trend as $row) {
            $trend_dates[] = $row['date'];
            $trend_counts[] = (int)$row['count'];
        }

        // Format status data for chart
        $status_labels = [];
        $status_series = [];
        foreach ($status as $row) {
            $status_labels[] = $row['status'];
            $status_series[] = (int)$row['count'];
        }

        // Format recent activity
        $recent_activity = [];
        foreach ($recent as $row) {
            $recent_activity[] = [
                'id' => $row['id'],
                'date' => $row['date_received'],
                'serial' => $row['reference_number'],
                'type' => $row['type'],
                'status' => $row['status'],
                'assignee' => trim($row['firstName'] . ' ' . $row['lastName'])
            ];
        }

        return [
            'stats' => [
                'total' => (int)$stats['total'],
                'completed' => (int)$stats['completed'],
                'ongoing' => (int)$stats['ongoing'],
                'pending' => (int)$stats['pending']
            ],
            'trend' => [
                'dates' => $trend_dates,
                'counts' => $trend_counts
            ],
            'status' => [
                'labels' => $status_labels,
                'series' => $status_series
            ],
            'recent' => $recent_activity
        ];
    }
    /**
     * Add an entry to the activity log for a correspondence.
     * @param int $correspondence_id
     * @param int $user_id
     * @param string $action
     * @param string $details
     * @return int|bool
     */
    public function add_activity_log($correspondence_id, $user_id, $action, $details) {
        $table = $this->_table;
        $this->_table = 'correspondence_activity_log';
        $data = [
            'correspondence_id' => $correspondence_id,
            'user_id' => $user_id,
            'action' => $action,
            'details' => $details,
            'createdOn' => date('Y-m-d H:i:s'),
            'createdBy' => $user_id // Assuming createdBy for activity log is the user_id
        ];
        $result = $this->insert($data);
        $this->_table = $table;
        return $result;
    }

    /**
     * Insert a new document record.
     * @param array $data
     * @return int|bool
     */
    public function insert_document($data) {
        $table = $this->_table;
        $this->_table = 'correspondence_documents';
        $result = $this->insert($data);
        $this->_table = $table;
        return $result;
    }

    /**
     * Get a single document by ID.
     * @param int $id
     * @return object|null
     */
    public function get_document_by_id($id) {
        $table = $this->_table;
        $this->_table = 'correspondence_documents';
        $query = [];
        $query["where"] = ["id" => $id];
        $result = $this->load($query);
        $this->_table = $table;
        return $result;
    }

    /**
     * Get all files (documents) from the correspondence_documents table.
     * @return array
     */
    public function get_all_files() {
        $table = $this->_table;
        $this->_table = 'correspondence_documents';
        $result = $this->load_all();
        $this->_table = $table;
        return $result;
    }

    /**
     * Update a file record in correspondence_documents.
     * @param int $id
     * @param array $data
     * @return bool
     */
    public function update_file($id, $data) {
        $table = $this->_table;
        $this->_table = 'correspondence_documents';
        $result = $this->update($id, $data);
        $this->_table = $table;
        return $result;
    }

    /**
     * Insert a new file record into correspondence_documents.
     * @param array $data
     * @return int|bool
     */
    public function insert_file($data) {
        $table = $this->_table;
        $this->_table = 'correspondence_documents';
        $result = $this->insert($data);
        $this->_table = $table;
        return $result;
    }

    /**
     * Delete a file record from correspondence_documents.
     * @param int $id
     * @return bool
     */
    public function delete_file($id) {
        $table = $this->_table;
        $this->_table = 'correspondence_documents';
        $result = $this->delete($id);
        $this->_table = $table;
        return $result;
    }
}

// SQL Server specific implementation
class sqlsrv_Correspondence extends mysql_Correspondence {
/*
lookup function for SQL Server specific methods
to search terms in the correspondence table.
 * This function uses SQL Server specific concatenation and joins.
 * @param string $search_term The term to search in the correspondence body.
 * @return array Result set of subject and id of the matching  search term.
 * it searches using reference number/serial number, subject,id and body.
*/
public function lookup($search_term) {
    $_original_table = $this->_table; 
    $this->_table = "correspondences as c";    

    $query = [];
    $query["select"] = ["c.id, c.subject, c.reference_number"];
    $query["join"] = [
        ["correspondence_types ct", "ct.id = c.correspondence_type_id", "left"],
        ["correspondence_statuses cs", "cs.id = c.status_id", "left"],
        ["contacts sender_contact", "sender_contact.id = c.sender", "left"],
        ["contacts recipient_contact", "recipient_contact.id = c.recipient", "left"],
        ["user_profiles assignee", "assignee.user_id = c.assigned_to", "left"]
    ];

     $safe_term = $this->ci->db->escape_like_str($search_term);
     $query["where"] = ["(c.subject LIKE '%$safe_term%' OR c.body LIKE '%$safe_term%' OR c.reference_number LIKE '%$safe_term%' OR CAST(c.id AS VARCHAR) LIKE '%$safe_term%')"];

    
    $query["order_by"] = ["c.createdOn DESC"];

    $result = $this->load_all($query);
    $this->_table = $_original_table; 
    
    return $result;
}
    

   
    /**
     * Get a single correspondence item by ID for SQL Server.
     * Overrides the parent method to use SQL Server specific concatenation.
     * @param int $id
     * @return object|null
     */
    public function get_correspondence_by_id($id) {
        $_original_table = $this->_table; // Store original table name
        $this->_table = "correspondences as c"; // Temporarily set alias

        $query = [];
        $query["select"] = ["c.*, ct.name as correspondence_type_name, cs.name as status_name,
            (CASE WHEN c.sender_contact_type= 'company' THEN sender_comp.name ELSE CONCAT(sender_contact.firstName, ' ', sender_contact.lastName) END) as sender_name,
             (CASE WHEN c.recipient_contact_type= 'company' THEN recipient_comp.name ELSE CONCAT(recipient_contact.firstName, ' ', recipient_contact.lastName) END) as recipient_name,
           CONCAT(assignee.firstName, ' ', assignee.lastName) as assignee_name,
          CONCAT(creator.firstName, ' ', creator.lastName) as createdBy,
       CONCAT(modifier.firstName, ' ', modifier.lastName) as modifiedBy,
       CONCAT('COR-', c.id) as cid"];
        $query["join"] = [
            ["correspondence_types ct", "ct.id = c.correspondence_type_id", "left"],
            ["correspondence_statuses cs", "cs.id = c.status_id", "left"],
            ["contacts sender_contact", "sender_contact.id = c.sender", "left"],
            ["companies sender_comp", "sender_comp.id = c.sender and c.sender_contact_type= 'company'", "left"],
            ["contacts recipient_contact", "recipient_contact.id = c.recipient", "left"],
            ["companies recipient_comp", "recipient_comp.id = c.recipient and c.sender_contact_type= 'company'", "left"],

            ["user_profiles assignee", "assignee.id = c.assigned_to", "left"] ,
            ["user_profiles creator", "creator.user_id = c.createdBy", "left"],
            ["user_profiles modifier", "modifier.user_id = c.modifiedBy", "left"],
        ];
        $query["where"] = ["c.id" , $id];

        $result = $this->load($query);
        $this->_table = $_original_table; // Revert to original table name
        return $result;
    }

    /**
     * Loads all correspondences with filtering, sorting, and pagination for SQL Server.
     * This method is adapted from the k_load_all_opinions in your Opinion model.
     * @param int $type_id Optional: Filter by correspondence type ID.
     * @param array $filter Associative array of filters.
     * @param array $sortable Associative array of sorting parameters.
     * @param string $page_number Page number for pagination.
     * @param bool $with_user_preferences Not used directly in this model, but kept for pattern consistency.
     * @param bool $hijri_calendar_enabled Not used directly in this model, but kept for pattern consistency.
     * @return array
     */
    public function k_load_all_correspondences($type_id = null, $filter = [], $sortable = [], $page_number = "", $with_user_preferences = false, $hijri_calendar_enabled = false) {
        $_table = $this->_table; // Store original table name
        $this->_table = "correspondences as c"; // Alias for main table for clarity in query

        $select = "SELECT c.id, c.subject, c.reference_number, c.date_received, c.document_date,
                          ct.name as correspondence_type_name, cs.name as status_name,
                          ISNULL(sender_contact.name, 'N/A') as sender_name,
                          ISNULL(recipient_contact.name, 'N/A') as recipient_name,
                          ISNULL(assigned_user.firstName + ' ' + assigned_user.lastName, 'N/A') as assigned_to_name,
                          c.requires_signature, c.comments, c.filename, c.createdOn, c.modifiedOn,
                          c.createdBy, c.modifiedBy, c.document_id, c.document_type_id";

        $response = [];
        $query = [];

        // Apply filters (similar logic to Opinion model's k_load_all_opinions)
        if (is_array($filter) && isset($filter["filters"])) {
            foreach ($filter["filters"] as $_filter) {
                // Assuming prep_k_filter is a method of My_Model and handles SQL Server dialect
                // If not, this part would need specific SQL Server conditional logic
                // For now, a basic example:
                if (isset($_filter["field"]) && isset($_filter["value"])) {
                    $query["where"][] = [$_filter["field"], $_filter["value"]];
                }
            }
        }

        // Specific filter for correspondence type
        if ($type_id !== null) { // Use strict comparison for 0
            $query["where"][] = ["c.correspondence_type_id", $type_id];
        }

        // Build joins
        $query["join"] = [
            ["correspondence_types ct", "ct.id = c.correspondence_type_id", "left"],
            ["correspondence_statuses cs", "cs.id = c.status_id", "left"],
            // Assuming 'contacts' table exists and has a 'name' column
            ["contacts sender_contact", "sender_contact.id = c.sender", "left"],
            ["contacts recipient_contact", "recipient_contact.id = c.recipient", "left"],
            // Assuming 'users' table exists and has 'firstName' and 'lastName'
            ["users assigned_user", "assigned_user.id = c.assigned_to", "left"]
        ];

        // Privacy conditions (adapt from Opinion model if applicable to correspondence)
        // This part would need to be implemented based on your specific privacy logic
        // For now, it's commented out as it relies on a 'legal_case' model and privacy conditions
        /*
        if (!empty($this->logged_user_id) && !empty($this->override_privacy)) {
            // You would need to load a relevant model for privacy conditions here
            // e.g., $this->ci->load->model("privacy_model");
            // $where_condition = $this->ci->privacy_model->get_correspondence_privacy_conditions($this->logged_user_id, $this->override_privacy);
            // $query["where"][] = ["(c.private IS NULL OR c.private = 'no' OR(c.private = 'yes' AND (" . "c.createdBy = '" . $this->logged_user_id . "' OR c.assigned_to = '" . $this->logged_user_id . "' OR " . "c.id IN (SELECT correspondence_id FROM correspondence_users WHERE user_id = '" . $this->logged_user_id . "') OR '" . $this->override_privacy . "' = 'yes'" . ")" . ") OR" . "(" . "c.related_entity_id IS NOT NULL AND " . "((c.private IS NULL OR c.private = 'no') AND " . $where_condition . ")" . ")" . ")"];
        }
        */

        // Apply sorting
        $order_by_sql = " ORDER BY ";
        if (is_array($sortable) && !empty($sortable)) {
            foreach ($sortable as $index => $_sort) {
                $order_by_sql .= $_sort["field"] . " " . $_sort["dir"] . (count($sortable) - 1 !== $index ? ", " : "");
            }
        } else {
            $order_by_sql .= "c.id DESC"; // Default sort
        }

        // Prepare the query using $this->prep_query for where/join clauses
        // This will build the CI DB query object, which we then convert to SQL
        $this->prep_query($query);

        // Get the compiled SELECT, FROM, JOIN, WHERE parts from CI DB class
        // This is a bit tricky as CI's query builder doesn't directly expose compiled parts easily.
        // We will manually construct the SQL using the $query array after prep_query.

        $from_and_joins = " FROM " . $this->_table;
        if (isset($query['join']) && is_array($query['join'])) {
            foreach ($query['join'] as $join_clause) {
                $from_and_joins .= " " . (isset($join_clause[2]) ? strtoupper($join_clause[2]) . " " : "") . "JOIN " . $join_clause[0] . " ON " . $join_clause[1];
            }
        }

        $where_sql = "";
        if (isset($query['where']) && is_array($query['where'])) {
            $where_clauses = [];
            foreach ($query['where'] as $w) {
                if (is_array($w) && count($w) === 2) {
                    $where_clauses[] = $w[0] . " = " . $this->ci->db->escape($w[1]); // Use escape for safety
                } else if (is_array($w) && count($w) === 3 && $w[2] === FALSE) { // For raw SQL conditions
                    $where_clauses[] = $w[0];
                } else {
                    // Fallback for simple string conditions (less safe, prefer array format)
                    $where_clauses[] = $w;
                }
            }
            if (!empty($where_clauses)) {
                $where_sql = " WHERE " . implode(" AND ", $where_clauses);
            }
        }

        // Build the full SQL query string for SQL Server with OFFSET/FETCH
        $sql = $select . $from_and_joins . $where_sql . $order_by_sql;

        // Apply pagination for SQL Server
        if ($page_number != "") {
            $limit_val = 10000; // Assuming a default limit for specific page number calls
            $offset_val = ($page_number - 1) * $limit_val;
            $sql .= " OFFSET " . $offset_val . " ROWS FETCH NEXT " . $limit_val . " ROWS ONLY";
        }
        if ($limit = $this->ci->input->post("take", true)) {
            $skip = $this->ci->input->post("skip", true);
            $sql .= " OFFSET " . $skip . " ROWS FETCH NEXT " . $limit . " ROWS ONLY";
        }

        $response["data"] = $this->ci->db->query($sql)->result_array();

        // For total rows, run a separate COUNT query without LIMIT/OFFSET
        $count_sql = "SELECT COUNT(c.id) AS num_rows " . $from_and_joins . $where_sql;
        $total_rows_result = $this->ci->db->query($count_sql)->row_array();
        $response["totalRows"] = $total_rows_result['num_rows']; // Access by alias

        $this->_table = $_table; // Reset table name to original
        return $response;
    }
    public function get_workflow_processes($correspondence_id, $correspondence_type_id)
    {
        $table = $this->_table;
        $this->_table = 'correspondence_workflow_steps cws';

        $query["select"] = [
            "cws.id",
            "cws.correspondence_type_id",
            "cws.name as step_name",
            "COALESCE(cw.comments, cws.comment) as remarks",
            "COALESCE(cw.status, 'pending') as workflow_status",
            "COALESCE(CONVERT(nvarchar(50), cw.createdOn, 120), 'Not yet') as date_actioned",
            "actionBy.firstName + ' ' + actionBy.lastName AS step_created_by_name",
            "modifier.firstName + ' ' + modifier.lastName AS step_modified_by_name"
        ];

        $query["join"] = [
            ["correspondence_workflow cw", "cw.workflow_step_id = cws.id AND cw.correspondence_id = " . (int)$correspondence_id, "left"],
            ["user_profiles actionBy", "actionBy.user_id = cw.createdBy", "left"], // Changed from cws to cw
            ["user_profiles as modifier", "modifier.user_id = cw.modifiedBy", "left"] // Changed from cws to cw
        ];

        $query["where"] = ['cws.correspondence_type_id', (int)$correspondence_type_id];
        $query["order_by"] = ['cws.sequence_order ASC'];

        $result = parent::load_all($query);
        $this->_table = $table;

        return $result;
    }
    /* @param int $correspondence_id The ID of the specific correspondence.
* @param int $correspondence_type_id The ID of the correspondence type.
* @return array An array of workflow steps with their status and details.
*/
    public function get_workflow_processes_by_correspondence_type($correspondence_id, $correspondence_type_id)
    {
        $table = $this->_table; // Store original table name
        $this->_table = 'correspondence_workflow_steps cws'; // Set base table for the load method, with alias

       // $correspondence_id_clean = (int)$correspondence_id;
        $correspondence_type_id_clean = (int)$correspondence_type_id;

        // Escape correspondence_id for safe use in SQL join condition.
        // Now that it's guaranteed to be an integer, escape() should return a valid numeric value.

       $str= "cw.workflow_step_id = cws.id AND cw.correspondence_id = $correspondence_id";//. $correspondence_id_escaped;
        $query["select"] = [
            "cws.id,
            cws.correspondence_type_id,
            cws.name as step_name,
            cws.sequence_order,
            COALESCE(cw.comments, cws.comment) AS remarks,
            COALESCE(cw.status, 'pending') AS workflow_status,
            COALESCE(CAST(cw.createdOn AS NVARCHAR(50)), 'Not yet') AS date_actioned,          
            cw.id AS workflow_entry_id,
              CONCAT(actionBy.firstName , ' ' , actionBy.lastName) AS step_created_by_name,         
            CONCAT (modifier.firstName , ' ' , modifier.lastName) AS step_modified_by_name,           
            CONCAT(actionedByWorkflow.firstName , ' ' ,actionedByWorkflow.lastName) AS workflow_actioned_by_name,"
        ];
        $query["join"] =[
            ["correspondence_workflow cw",$str, "left"],
            ["user_profiles actionBy", "actionBy.user_id = cws.createdBy", "left"],
            ["user_profiles modifier", "modifier.user_id = cws.modifiedBy", "left"],
            ["user_profiles actionedByWorkflow", "actionedByWorkflow.user_id = cw.createdBy", "left"],
        ];

        // Filter steps by correspondence type ID
        $query["where"] = ['cws.correspondence_type_id',$correspondence_type_id_clean];

        // Order by sequence_order to process steps in the correct progression
        $query["order_by"] = ['cws.sequence_order ASC'];

        // Execute the query to get all steps with their potential workflow details
        $result = parent::load_all($query);
        $last_query = $this->ci->db->last_query();
        log_message('debug', 'Workflow Query: ' . $last_query);
        // --- Post-processing in PHP to determine 'is_current' ---
        $is_current_found = false;
        foreach ($result as $key => $row) {
            // If this step has no matching workflow entry (workflow_entry_id is NULL)
            // AND we haven't found a 'current' step yet, then this is the current step.
            if (isset($row['workflow_entry_id']) && $row['workflow_entry_id'] === null && !$is_current_found) {
                $result[$key]['is_current'] = true;
                $is_current_found = true; // Mark that we've found the current step
            } else {
                $result[$key]['is_current'] = false;
            }
        }
        // --- End Post-processing ---

        $this->_table = $table; 
        return $result; 
    }


// Assuming this function is within a CodeIgniter Model

    public function get_dashboard_stats($filters = [])
    {
        $db = $this->ci->db;
        $where = [];

        // Filter by year - Use table alias 'c'
        if (!empty($filters['year'])) {
            $where[] = "YEAR(c.date_received) = " . (int)$filters['year'];
        }
        // Filter by month - Use table alias 'c'
        if (!empty($filters['month'])) {
            $where[] = "MONTH(c.date_received) = " . (int)$filters['month'];
        }

        // Filter by type ID directly on correspondences table - Use table alias 'c'
        if (!empty($filters['type'])) {
            $where[] = "c.correspondence_type_id = " . (int)$filters['type'];
        }

        // Build WHERE clause
        $where_sql = $where ? 'WHERE ' . implode(' AND ', $where) : '';

        // 1. Summary stats
        $stats_sql = "
    SELECT
        COUNT(*) as total,
        SUM(CASE WHEN cs.name = 'Completed' THEN 1 ELSE 0 END) as completed,
        SUM(CASE WHEN cs.name = 'Ongoing' THEN 1 ELSE 0 END) as ongoing,
        SUM(CASE WHEN cs.name = 'Pending' THEN 1 ELSE 0 END) as pending
    FROM correspondences c  -- Add alias 'c' here to match WHERE clause if using it
    LEFT JOIN correspondence_statuses cs ON cs.id = c.status_id
    $where_sql
";
        $stats = $db->query($stats_sql)->row_array();

        // 2. Trend data (correspondences per day in selected month/year)
        $trend_sql = "
    SELECT TOP 31 CAST(c.date_received AS DATE) as date, COUNT(*) as count
    FROM correspondences c
    $where_sql
    GROUP BY CAST(c.date_received AS DATE)
    ORDER BY CAST(c.date_received AS DATE) ASC
";
        $trend = $db->query($trend_sql)->result_array();

        // 3. Status distribution
        $status_sql = "
    SELECT cs.name as status, COUNT(*) as count
    FROM correspondences c -- Add alias 'c' here to match WHERE clause if using it
    LEFT JOIN correspondence_statuses cs ON cs.id = c.status_id
    $where_sql
    GROUP BY cs.name
";
        $status = $db->query($status_sql)->result_array();

        // 4. Recent activity (last 10 correspondences)
        $recent_sql = "
    SELECT TOP 10 c.id, c.reference_number, ct.name as type, cs.name as status, c.date_received, up.firstName, up.lastName
    FROM correspondences c
    LEFT JOIN correspondence_types ct ON ct.id = c.correspondence_type_id
    LEFT JOIN correspondence_statuses cs ON cs.id = c.status_id
    LEFT JOIN user_profiles up ON up.user_id = c.assigned_to
    $where_sql
    ORDER BY c.date_received DESC
";
        $recent = $db->query($recent_sql)->result_array();

        // Format trend data for chart
        $trend_dates = [];
        $trend_counts = [];
        foreach ($trend as $row) {
            $trend_dates[] = $row['date'];
            $trend_counts[] = (int)$row['count'];
        }

        // Format status data for chart
        $status_labels = [];
        $status_series = [];
        foreach ($status as $row) {
            $status_labels[] = $row['status'];
            $status_series[] = (int)$row['count'];
        }

        // Format recent activity
        $recent_activity = [];
        foreach ($recent as $row) {
            $recent_activity[] = [
                'id' => $row['id'],
                'date' => $row['date_received'],
                'serial' => $row['reference_number'],
                'type' => $row['type'],
                'status' => $row['status'],
                'assignee' => trim($row['firstName'] . ' ' . $row['lastName'])
            ];
        }

        return [
            'stats' => [
                'total' => (int)$stats['total'],
                'completed' => (int)$stats['completed'],
                'ongoing' => (int)$stats['ongoing'],
                'pending' => (int)$stats['pending']
            ],
            'trend' => [
                'dates' => $trend_dates,
                'counts' => $trend_counts
            ],
            'status' => [
                'labels' => $status_labels,
                'series' => $status_series
            ],
            'recent' => $recent_activity
        ];
    }
    public function generate_next_reference_number()
    {
        $current_year = date('Y');
        $current_month = date('m');

        $sql = "SELECT COUNT(id) AS monthly_count     FROM " . $this->ci->db->dbprefix($this->_table) . "
            WHERE YEAR(createdOn) = ?      AND MONTH(createdOn) = ?    ";


        $query = $this->ci->db->query($sql, [(int)$current_year, (int)$current_month]);
        $result = $query->row();

        $monthly_record_count = 0;
        if ($result && $result->monthly_count !== null) {
            $monthly_record_count = (int)$result->monthly_count;
        }

        // The next sequential record ID for this month
        $next_monthly_record_id = $monthly_record_count + 1;

        // Format the monthly record ID to include leading zeros (e.g., 1 -> '001', 12 -> '012')
        // Adjust '%03d' if you expect more than 999 records per month (e.g., '%04d' for up to 9999).
        $formatted_record_id = sprintf('%03d', $next_monthly_record_id);

        // Combine into the final reference number format: recordid/Month/Year
        $reference_number = "{$formatted_record_id}/{$current_month}/{$current_year}";

        return $reference_number;
    }

}

class mysqli_Correspondence extends mysql_Correspondence {
    // This class can be empty if no specific MySQL overrides are needed.
}
