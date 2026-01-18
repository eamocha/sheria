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
    protected $statuses = ["Active", "Inactive"];
    protected $archivedValues = ["yes", "no"];
    protected $modelCode = "CNV-";
    protected $cp_channel = "CP";
    protected $outlook_channel = "MSO";
    protected $apiGmailChannel = "A4G";
    protected $web_channel = "A4L";
    protected $_fieldsNames = ['id', 'title', 'assignee_id',"channel", 'instrument_type_id','transaction_type_id', 'parties', 'initiated_by', 'staff_pf_no', 'date_initiated', 'description', 'external_counsel_id', 'property_value', 'amount_requested', 'amount_approved', 'createdOn', 'createdBy', 'modifiedOn', 'modifiedBy', 'archived', 'status','reference_number','transaction_type_id','parties_id','contact_type'];
    protected $primaryKey = 'id';
    protected $ci;
    public function __construct()
    {
        $this->ci =&get_instance();
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
//
            "transaction_type_id"=>[
               "required"=>false,
                "allowEmpty"=>true,
                "rule"=>["minLength", 1],
                "message" => $this->ci->lang->line("cannot_be_blank_rule")
            ],
            "instrument_type_id" => [
                "required" => true,
                "allowEmpty" => false,
                "rule" => ["minLength", 1],
                "message" => $this->ci->lang->line("cannot_be_blank_rule")
            ],
            "title" => [
               "required" => true,
                "allowEmpty" => false,
                "rule" => ["minLength", 1],
                "message" => $this->ci->lang->line("cannot_be_blank_rule")
            ],
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
                "rule" => ["numeric"],  // Assuming this should be a number
                "message" => $this->ci->lang->line("must_be_numeric_rule")
            ],
            "amount_requested" => [
                "required" => true,
                "allowEmpty" => false,
                "rule" => ["numeric"],  // Assuming this should be a number
                "message" => $this->ci->lang->line("must_be_numeric_rule")
            ],
            "amount_approved" => [
                "required" => false,  // Assuming this might be optional until approved
                "allowEmpty" => true,
                "rule" => ["numeric"],  // Assuming this should be a number when provided
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
            "reference_number"=>[
                "required" => false,
                "allowEmpty" => false,
                "rule" => ["minLength", 1],
                "message" => $this->ci->lang->line("cannot_be_blank_rule")
            ],
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
    CONCAT('CNV-', conveyancing_instruments.id) as conveyancing_id", // Added to SELECT
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

        $return = $this->load_all($query);

        $this->_table = $table;
        return $return;
    }
    public function cp_load_conveyancing_instrument_by_id($instrument_id)
    {
        $query = [];
        $table = $this->_table;
        $this->_table = "conveyancing_instruments";

        $query["select"] = [
            "conveyancing_instruments.*,
        instrument_type.name as instrument_type,
        transaction_type.name as transaction_type_name,
        CONCAT(staff.firstName, ' ', staff.lastName) as staff,
        CONCAT(vendor.firstName, ' ', vendor.lastName) as vendor_name,
        CONCAT(assignee.firstName, ' ', assignee.lastName) as assignee,
        CONCAT(creator.firstName, ' ', creator.lastName) as creator_name,
        CONCAT(modifier.firstName, ' ', modifier.lastName) as modifier_name,
        CONCAT('CNV-', conveyancing_instruments.id) as conveyancing_id,
        extCounsel.name as external_counsel,
        CASE 
            WHEN conveyancing_instruments.parties_id IS NULL THEN 'Not available'
            WHEN conveyancing_instruments.contact_type = 'company' THEN company.name
            ELSE CONCAT(
                contact.firstName, 
                CASE WHEN contact.father != '' THEN CONCAT(' ', contact.father) ELSE '' END, 
                ' ', 
                contact.lastName
            )
        END as party_name"
        ];

        $query["join"] = [
            ["user_profiles assigned_user", "assigned_user.user_id = conveyancing_instruments.assignee_id", "left"],
            ["contacts requester", "requester.id = conveyancing_instruments.initiated_by", "left"],
            ["conveyancing_instrument_types as instrument_type", "instrument_type.id = conveyancing_instruments.instrument_type_id", "left"],
            ["conveyancing_transaction_types as transaction_type", "transaction_type.id = conveyancing_instruments.transaction_type_id", "left"],
            ["user_profiles assignee", "assignee.user_id = conveyancing_instruments.assignee_id", "left"],
            ["contacts creator", "creator.id = conveyancing_instruments.createdBy", "left"],
            ["user_profiles modifier", "modifier.user_id = conveyancing_instruments.modifiedBy", "left"],
            ["contacts staff", "staff.id = conveyancing_instruments.initiated_by", "left"],
            ["contacts vendor", "vendor.id = conveyancing_instruments.parties_id", "left"],
            ["companies extCounsel", "extCounsel.id = conveyancing_instruments.external_counsel_id", "left"],
            // New joins for party name resolution
            ["companies company", "company.id = conveyancing_instruments.parties_id AND conveyancing_instruments.contact_type = 'company'", "left"],
            ["contacts contact", "contact.id = conveyancing_instruments.parties_id AND conveyancing_instruments.contact_type != 'company'", "left"]
        ];

        $query["where"] = [
            "(conveyancing_instruments.id = " . $instrument_id . " and conveyancing_instruments.channel = '" . $this->cp_channel . "' )"
        ];

        $return = $this->load($query);
        $this->_table = $table;
        return $return;
    }
    public function k_load_all_instruments($filter = [], $sortable = [], $return_query = false)
    {
        $query = [];
        $this->ci->load->model("language");
        $lang_id = $this->ci->language->get_id_by_session_lang();

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


    public function generate_reference_number() {
        $prefix = 'CNV-';
        $year = date('Y');
        $sequence = $this->db->count_all($this->table) + 1;
        return $prefix . $year . '-' . str_pad($sequence, 5, '0', STR_PAD_LEFT);
    }

}

class mysql_Conveyancing_instrument extends mysqli_Conveyancing_instrument
{
}
class sqlsrv_Conveyancing_instrument extends mysqli_Conveyancing_instrument
{

    public function insert_new_record()
    {
        $this->ci->db->simple_query("INSERT INTO conveyancing_instruments DEFAULT VALUES");
        return $this->ci->db->insert_id();
    }
}