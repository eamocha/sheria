<?php
if (!defined("BASEPATH")) {
    exit("No direct script access allowed");
}
class Conveyancing_instrument extends My_Model_Factory
{
}
class mysqli_Conveyancing_instrument extends My_Model {
    protected $_table = 'conveyancing_instruments';
    protected $modelName = "conveyancing_instrument";
    protected $statuses = ["Pending", "In-progress", "Completed","Delayed","Closed"];
    protected $archivedValues = ["", "yes", "no"];
    protected $modelCode = "CNV-";
    protected $cp_channel = "CP";
    protected $priorityValues = ["critical", "high", "medium", "low"];
    protected $outlook_channel = "MSO";
    protected $apiGmailChannel = "A4G";
    protected $web_channel = "A4L";
    protected $_fieldsNames = ['id', 'title','assignee_team_id', 'assignee_id',"channel", 'instrument_type_id','transaction_type_id', 'parties', 'initiated_by', 'staff_pf_no', 'date_initiated', 'description', 'external_counsel_id', 'property_value', 'amount_requested', 'amount_approved', 'createdOn', 'createdBy', 'modifiedOn', 'modifiedBy', 'archived', 'status','reference_number','transaction_type'];
    protected $primaryKey = 'id';
    protected $ci;
    public function __construct()
    {
        $this->ci =& get_instance();
        parent::__construct();

        $this->validate = [
//            "id" => [
//                "required" => true,
//                "allowEmpty" => false,
//                "rule" => ["minLength", 1],
//                "message" => $this->ci->lang->line("cannot_be_blank_rule")
//            ],
//            "title" => [
//                "required" => true,
//                "allowEmpty" => false,
//                "rule" => ["minLength", 1],
//                "message" => $this->ci->lang->line("cannot_be_blank_rule")
//            ],
//            "transaction_type"=>[
//                "required"=>false,
//                "allowEmpty"=>true,
//                "rule"=>["minLength", 1],
//                "message" => $this->ci->lang->line("cannot_be_blank_rule")
//            ],
//            "instrument_type_id" => [
//                "required" => true,
//                "allowEmpty" => false,
//                "rule" => ["minLength", 1],
//                "message" => $this->ci->lang->line("cannot_be_blank_rule")
//            ],
            "parties" => [
                "required" => true,
                "allowEmpty" => false,
                "rule" => ["minLength", 1],
                "message" => $this->ci->lang->line("cannot_be_blank_rule")
            ],
            "initiated_by" => [
                "required" => true,
                "allowEmpty" => false,
                "rule" => ["minLength", 1],
                "message" => $this->ci->lang->line("cannot_be_blank_rule")
            ],
            "staff_pf_no" => [
                "required" => true,
                "allowEmpty" => false,
                "rule" => ["minLength", 1],
                "message" => $this->ci->lang->line("cannot_be_blank_rule")
            ],
            "date_initiated" => [
                "required" => true,
                "allowEmpty" => false,
                "rule" => ["minLength", 1],
                "message" => $this->ci->lang->line("cannot_be_blank_rule")
            ],
            "description" => [
                "required" => true,
                "allowEmpty" => false,
                "rule" => ["minLength", 1],
                "message" => $this->ci->lang->line("cannot_be_blank_rule")
            ],
//            "external_counsel_id" => [
//                "required" => false,  // Assuming this might be optional
//                "allowEmpty" => true,
//                "rule" => [],
//                "message" => ""
//            ],
            "property_value" => [
                "required" => true,
                "allowEmpty" => false,
                "rule" => ["numeric"],
                "message" => $this->ci->lang->line("must_be_numeric_rule")
            ],
            "amount_requested" => [
                "required" => true,
                "allowEmpty" => false,
                "rule" => ["numeric"],
                "message" => $this->ci->lang->line("must_be_numeric_rule")
            ],
            "amount_approved" => [
                "required" => false,  // Assuming this might be optional until approved
                "allowEmpty" => true,
                "rule" => ["numeric"],
                "message" => $this->ci->lang->line("must_be_numeric_rule")
            ],
//            "createdOn" => [
//                "required" => true,
//                "allowEmpty" => false,
//                "rule" => ["date"],  // Assuming this should be a date
//                "message" => $this->ci->lang->line("must_be_valid_date_rule")
//            ],
//            "createdBy" => [
//                "required" => true,
//                "allowEmpty" => false,
//                "rule" => ["minLength", 1],
//                "message" => $this->ci->lang->line("cannot_be_blank_rule")
//            ],
//            "modifiedOn" => [
//                "required" => false,  // This might be optional until first modification
//                "allowEmpty" => true,
//                "rule" => ["date"],  // Assuming this should be a date when provided
//                "message" => $this->ci->lang->line("must_be_valid_date_rule")
//            ],
//            "modifiedBy" => [
//                "required" => false,  // This might be optional until first modification
//                "allowEmpty" => true,
//                "rule" => ["minLength", 1],
//                "message" => $this->ci->lang->line("cannot_be_blank_rule")
//            ],
//            "archived" => [
//                "required" => false,  // Assuming this might be optional
//                "allowEmpty" => true,
//                "rule" => ["boolean"],  // Assuming this should be true/false
//                "message" => $this->ci->lang->line("must_be_boolean_rule")
//            ],
//            "reference_number"=>[
//                "required" => false,
//                "allowEmpty" => true,
//                "rule" => ["minLength",1],
//                "message" => $this->ci->lang->line("must_be_numeric_rule")
//            ],
            "status" => [
                "required" => false,
                "allowEmpty" => false,
                "rule" => ["minLength", 1],
                "message" => $this->ci->lang->line("cannot_be_blank_rule")
            ]
        ];
    }
    public function load_cp_conveyancing_instruments($user_id)
    {
        $query = [];
        $this->ci->load->model("language");
        $lang_id = $this->ci->language->get_id_by_session_lang() ?? 1;
        $table = $this->_table;
        $this->_table = "conveyancing_instruments";

        $query["select"] = [
            "conveyancing_instruments.*,
    instrument_type.name as instrument_type,
    transaction_type.name as transaction_type_name,
    CONCAT(staff.firstName, ' ', staff.lastName) as staff,
    CONCAT(assignee.firstName, ' ', assignee.lastName) as assignee,
    CONCAT(creator.firstName, ' ', creator.lastName) as creator_name,
    CONCAT(modifier.firstName, ' ', modifier.lastName) as modifier_name,
    CONCAT('CNV-', conveyancing_instruments.id) as conveyancing_id",
        ];
        $query["join"] = [["user_profiles assigned_user", "assigned_user.user_id = conveyancing_instruments.assignee_id", "left"],
            ["customer_portal_users requester", "requester.contact_id = conveyancing_instruments.initiated_by", "left"],
            ["conveyancing_instrument_types as instrument_type", "instrument_type.id = conveyancing_instruments.instrument_type_id", "left"],
            ["conveyancing_transaction_types as transaction_type", "transaction_type.id = conveyancing_instruments.transaction_type_id", "left"],
            ["user_profiles assignee", "assignee.user_id = conveyancing_instruments.assignee_id", "left"],
            ["user_profiles creator", "creator.user_id = conveyancing_instruments.createdBy", "left"],
            ["user_profiles modifier", "modifier.user_id = conveyancing_instruments.modifiedBy", "left"],
            ["contacts staff", "staff.id = conveyancing_instruments.initiated_by", "left"]
        ];
        $this->ci->load->model("customer_portal_users", "customer_portal_usersfactory");
        $this->ci->customer_portal_users = $this->ci->customer_portal_usersfactory->get_instance();
        $this->ci->customer_portal_users->fetch($user_id);
        $requested_contact = $this->ci->customer_portal_users->get_field("contact_id");
        $query["where"] = ["(conveyancing_instruments.createdBy = " . $user_id . " and conveyancing_instruments.channel = '" . $this->cp_channel . "' )"];
        $query["order_by"] = ["conveyancing_instruments.id DESC"];

        // Enable the profiler to see the query
//        $this->ci->db->profile_enable = TRUE;

        $return = $this->load_all($query);

//        // After the query execution, log the query
//        echo "<pre>";
//        var_dump($this->ci->db->last_query());
//        echo "</pre>";

        $this->_table = $table;
        return $return;
    }

    public function k_load_all_instruments($filter = [], $sortable = [], $return_query = false)
    {
        $query = [];

        $query["select"] = [
            "SQL_CALC_FOUND_ROWS conveyancing_instruments.*, 
            instrument_type.name as instrument_type, 
            transaction_type.name as transaction_type_name,
            CONCAT(assignee.firstName, ' ', assignee.lastName) as assignee,
            CONCAT(creator.firstName, ' ', creator.lastName) as creator_name,
            CONCAT(modifier.firstName, ' ', modifier.lastName) as modifier_name,
            concat('" . $this->get("modelCode") . "', conveyancing.id) as conveyancing_id",

        ];

        if (isset($filter) && is_array($filter)) {
            if (isset($filter["filters"])) {
                foreach ($filter["filters"] as $_filter) {
                    $this->prep_k_filter($_filter, $query, $filter["logic"]);
                }
                unset($_filter);
            }
        }

        $query["join"] = [
            ["conveyancing_instrument_types as instrument_type", "instrument_type.id = conveyancing_instruments.instrument_type_id", "left"],
            ["conveyancing_transaction_types as transaction_type", "transaction_type.id = conveyancing_instruments.transaction_type_id", "left"],
            ["user_profiles assignee", "assignee.user_id = conveyancing_instruments.initiated_by", "left"],
            ["user_profiles creator", "creator.user_id = conveyancing_instruments.createdBy", "left"],
            ["user_profiles modifier", "modifier.user_id = conveyancing_instruments.modifiedBy", "left"]
        ];

        //  $query["where"][] = $this->get_conveyancing_privacy_conditions($this->logged_user_id, $this->override_privacy);

        if (is_array($sortable) && !empty($sortable)) {
            foreach ($sortable as $_sort) {
                $query["order_by"][] = [$_sort["field"], $_sort["dir"]];
            }
        } else {
            $query["order_by"] = ["conveyancing_instruments.id desc"];
        }

        if ($return_query) {
            return $query;
        }

        $response["data"] = parent::load_all($query);
        $response["totalRows"] = $this->ci->db->query("SELECT FOUND_ROWS() AS `count`")->row()->count;
        return $response;
    }


    public function get_pending_instruments($user_id, $filter = "all") {
        $this->db->where('initiated_by', $user_id)
            ->where('status', 'pending');

        if ($filter !== "all") {
            $this->db->where('instrument_type', $filter);
        }

        return $this->db->order_by('date_initiated', 'DESC')->get($this->table)->result_array();
    }

    public function get_in_progress_instruments($user_id, $filter = "all") {
        $this->db->where('initiated_by', $user_id)
            ->where('status', 'in_progress');

        if ($filter !== "all") {
            $this->db->where('instrument_type', $filter);
        }

        return $this->db->order_by('date_initiated', 'DESC')->get($this->table)->result_array();
    }

    public function generate_reference_number() {
        $prefix = 'CNV-';
        $year = date('Y');
        $sequence = $this->db->count_all($this->table) + 1;
        return $prefix . $year . '-' . str_pad($sequence, 5, '0', STR_PAD_LEFT);
    }



    // DMS Integration Methods
    public function link_dms_document($instrument_id, $document_data) {
        $dms_data = [
            'entity_type' => 'conveyancing',
            'entity_id' => $instrument_id,
            'document_name' => $document_data['name'],
            'document_path' => $document_data['path'],
            'uploaded_by' => $this->session->userdata('CP_user_id'),
            'upload_date' => date('Y-m-d H:i:s')
        ];

        return $this->dms->save_document($dms_data);
    }

    public function get_dms_documents($instrument_id) {
        return $this->dms->get_documents('conveyancing', $instrument_id);
    }

    // External Counsel Integration
    public function assign_external_counsel($instrument_id, $counsel_id) {
        $update_data = [
            'external_counsel_id' => $counsel_id,
            'modifiedOn' => date('Y-m-d H:i:s')
        ];

        return $this->update($instrument_id, $update_data);
    }

    public function get_counsel_details($instrument_id) {
        $instrument = $this->fetch($instrument_id);
        if ($instrument && $instrument->external_counsel_id) {
            return $this->advisor_users->fetch($instrument->external_counsel_id);
        }
        return null;
    }

    public function assignee_field_value()
    {
        return "CONCAT(assignee.firstName, ' ', assignee.lastName)";
    }

    public function instrument_type_field_value()
    {
        return "instrument_type.name";
    }

    public function transaction_type_field_value()
    {
        return "transaction_type.name";
    }
}

class mysql_Conveyancing_instrument extends mysqli_Conveyancing_instrument
{
}
class sqlsrv_Conveyancing_instrument extends mysqli_Conveyancing_instrument
{
    public function k_load_all_instruments($filter = [], $sortable = [], $return_query = false)
    {
        $query = [];
       // $this->ci->load->model("language");
       // $lang_id = $this->ci->language->get_id_by_session_lang();

        $query["select"] = [
            "conveyancing_instruments.*,
              exLegalFirm.name AS external_counsel_name,    
                instrument_type.name as instrument_type,
    transaction_type.name as transaction_type_name,
    CONCAT(staff.firstName, ' ', staff.lastName) as staff,
    CONCAT(assignee.firstName, ' ', assignee.lastName) as assignee,
    CONCAT(creator.firstName, ' ', creator.lastName) as creator_name,
    CONCAT(modifier.firstName, ' ', modifier.lastName) as modifier_name,
    CONCAT('CNV-', conveyancing_instruments.id) as conveyancing_id",
        ];
        $query["join"] = [
            ["user_profiles assigned_user", "assigned_user.user_id = conveyancing_instruments.assignee_id", "left"],
            ["customer_portal_users requester", "requester.contact_id = conveyancing_instruments.initiated_by", "left"],
            ["conveyancing_instrument_types as instrument_type", "instrument_type.id = conveyancing_instruments.instrument_type_id", "left"],
            ["conveyancing_transaction_types as transaction_type", "transaction_type.id = conveyancing_instruments.transaction_type_id", "left"],
            ["user_profiles assignee", "assignee.user_id = conveyancing_instruments.assignee_id", "left"],
            ["user_profiles creator", "creator.user_id = conveyancing_instruments.createdBy", "left"],
            ["user_profiles modifier", "modifier.user_id = conveyancing_instruments.modifiedBy", "left"],
            ["contacts staff", "staff.id = conveyancing_instruments.initiated_by", "left"],
            ["companies exLegalFirm "," exLegalFirm.id = conveyancing_instruments.external_counsel_id", "left"]

        ];

        // Apply filters
        if (isset($filter) && is_array($filter)) {
            if (isset($filter["filters"])) {
                foreach ($filter["filters"] as $_filter) {
                    $this->prep_k_filter($_filter, $query, $filter["logic"]);
                }
                unset($_filter);
            }
        }


        // Default sorting
        if (is_array($sortable) && !empty($sortable)) {
            foreach ($sortable as $_sort) {
                $query["order_by"][] = [$_sort["field"], $_sort["dir"]];
            }
        } else {
            $query["order_by"] = ["conveyancing_instruments.id DESC"];
        }

        if ($return_query) {
            return $query;
        }

        // Execute query
        $response["data"] = parent::load_all($query);

        // Get total count (SQL Server-compatible)
        $this->ci->db->reset_query(); // Clear previous query
        $count_query = $this->ci->db->select('COUNT(*) as count', false)
            ->from('conveyancing_instruments')
            ->get();
        $response["totalRows"] = $count_query->row()->count;

        return $response;
    }
    public function get_instrument_by_id($instrument_id, $return_query = false)
    {
        $query = [];

        $query["select"] = [
            "conveyancing_instruments.*,
        exLegalFirm.name AS external_counsel_name,    
        instrument_type.name as instrument_type,
        transaction_type.name as transaction_type_name,
        CONCAT(staff.firstName, ' ', staff.lastName) as staff,
        CONCAT(assignee.firstName, ' ', assignee.lastName) as assignee,
        CONCAT(creator.firstName, ' ', creator.lastName) as creator_name,
        CONCAT(modifier.firstName, ' ', modifier.lastName) as modifier_name,
        CONCAT('CNV-', conveyancing_instruments.id) as conveyancing_id"
        ];

        $query["join"] = [
            ["user_profiles assigned_user", "assigned_user.user_id = conveyancing_instruments.assignee_id", "left"],
            ["customer_portal_users requester", "requester.contact_id = conveyancing_instruments.initiated_by", "left"],
            ["conveyancing_instrument_types as instrument_type", "instrument_type.id = conveyancing_instruments.instrument_type_id", "left"],
            ["conveyancing_transaction_types as transaction_type", "transaction_type.id = conveyancing_instruments.transaction_type_id", "left"],
            ["user_profiles assignee", "assignee.user_id = conveyancing_instruments.assignee_id", "left"],
            ["user_profiles creator", "creator.user_id = conveyancing_instruments.createdBy", "left"],
            ["user_profiles modifier", "modifier.user_id = conveyancing_instruments.modifiedBy", "left"],
            ["contacts staff", "staff.id = conveyancing_instruments.initiated_by", "left"],
            ["companies exLegalFirm", "exLegalFirm.id = conveyancing_instruments.external_counsel_id", "left"]
        ];

        $query["where"] = [
            "conveyancing_instruments.id" , $instrument_id
        ];
        $query["limit"] = [1];

        if ($return_query) {
            return $query;
        }

        $result = parent::load_all($query);
        if (!is_array($result) || empty($result)) {
            return null;
        }

       else return $result[0];
    }
    // Get process timeline

        public function get_process_timeline($instrument_id, $return_query = false)
    {
        $this->_table = "conveyancing_process_stages as cps";
        $query["select"] = [
            "cps.id as id,
            cps.name as title,
         COALESCE(sp.comments, cps.description) as details,
         COALESCE(sp.status, 'pending') as status,
         COALESCE(CAST(sp.start_date AS nvarchar(50)), 'Not yet') as date,
         sp.completion_date,
         CONCAT_WS(' ', up.firstName, up.lastName) as updatedBy"
        ];
        $query["join"] = [
            ["conveyancing_stage_progress sp", "sp.stage_id = cps.id AND sp.instrument_id =$instrument_id", "left"],
            ["user_profiles up", "up.user_id = sp.updated_by", "left"],
        ];
        $query["order_by"] = ["cps.sequence_order ASC"];


        if ($return_query) {
            return $query;
        }

        $result = parent::load_all($query);
        if (!is_array($result) || empty($result)) {
            return [];
        }

        return $result;
    }

    public function get_process_timeline1($instrument_id) {
        // Get process instance
        $this->ci->db->where('process_id', $instrument_id);
        $instance = $this->ci->db->get('conveyancing_process_instances')->row_array();

        if (!$instance) return [];

        // Get all stages
        $this->ci->db->order_by('sequence_order', 'ASC');
        $stages = $this->ci->db->get('conveyancing_process_stages')->result_array();

        // Get progress for each stage
        $this->ci->db->where('instrument_id', $instrument_id);
        $progress = $this->ci->db->get('conveyancing_stage_progress')->result_array();

        // Format timeline data
        $timeline = [];
        foreach ($stages as $stage) {
            $stage_progress = array_filter($progress, function($p) use ($stage) {
                return $p['stage_id'] == $stage['id'];
            });

            $stage_progress = reset($stage_progress);

            $timeline[] = [
                'id' => $stage['id'],
                'title' => $stage['name'],
                'status' => $stage_progress ? strtolower($stage_progress['status']) : 'pending',
                'date' => $stage_progress ? date('d/m/Y', strtotime()) : 'Pending',
                'updatedBy' => $stage_progress ? $this->get_user_name($stage_progress['updated_by']) : '',
                'details' => $stage_progress ? $stage_progress['comments'] : $stage['description']
            ];
        }

        return $timeline;
    }

    public function insert_new_record()
    {
        $this->ci->db->simple_query("INSERT INTO conveyancing_instruments DEFAULT VALUES");
        return $this->ci->db->insert_id();
    }

    /**
     * Updates a conveyancing instrument record with external counsel details.
     *
     * @param int $instrument_id The ID of the conveyancing instrument to update.
     * @param array $data An associative array of data to update, e.g., ['external_counsel_id' => 123, 'nomination_notes' => 'Some notes'].
     * @return bool True on success, false on failure.
     */
    public function update_external_counsel_nomination($instrument_id, $data) {
        // Ensure the 'modifiedOn' field is updated automatically
        $data['modifiedOn'] = date('Y-m-d H:i:s');
        // Optionally, if you track who modified it:
        // $data['modifiedBy'] = $this->ci->session->userdata('user_id'); // Or relevant session ID from your auth system

        $this->ci->db->where('id', $instrument_id);
        return $this->ci->db->update($this->_table, $data);
    }
}