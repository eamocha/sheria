<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Criminal_case_detail extends My_Model_Factory {}

class mysql_Criminal_case_detail extends My_Model {
    protected $modelName = "Criminal_case_detail";
    protected $modelCode = "CCD";
    protected $_table = "criminal_case_details";
    protected $_listFieldName = "origin_of_case";

    // Full list of table fields
    protected $_fieldsNames = ["id", "case_id", "origin_of_case", "offence_subcategory_id", "status_of_case", "initial_entry_document_id", "authorization_document_id", "date_investigation_authorized","police_station_reported", "police_station_ob_number","police_case_file_number"];

    protected $allowedNulls = ["offence_subcategory_id", "initial_entry_document_id", "authorization_document_id", "date_investigation_authorized"];
    protected $criminalCaseStatusValues = ["Pending Approval", "Ongoing", "Closed", "Acquitted", "Convicted", "Dismissed", "PBC", "PUI", "PAKA","Withdrawn"];
    protected $criminalCaseStatusGroups = [
        'pre_trial' => ['Pending Approval', 'PUI', 'PAKA'],
        'active' => ['Ongoing',"PBC"],
        'concluded' => ['Closed', 'Acquitted', 'Convicted', 'Dismissed','Withdrawn']
    ];

    protected $builtInLogs = false;

    protected $ci = null;

    public function __construct() {
        parent::__construct();
        $this->ci =& get_instance();

        $this->validate = [
            [
                'field' => 'case_id',
                'label' => 'Case ID',
                'rules' => 'required|numeric'
            ],
            [
                'field' => 'origin_of_case',
                'label' => 'Origin of Case',
                'rules' => 'required|max_length[255]'
            ],
            [
                'field' => 'status_of_case',
                'label' => 'Status of Case',
                'rules' => 'required|max_length[100]'
            ],
            [
                'field' => 'date_investigation_authorized',
                'label' => 'Date Investigation Authorized',
                'rules' => 'valid_date'
            ]
        ];

        $this->logged_user_id = $this->ci->session->userdata('user_id');
        $this->override_privacy = false;
    }

    public function get_details_by_case_id($case_id) {

        $query = [];
        $query['select'] = [
            "criminal_case_details.*,           
            cos.name as offence_subcategory_name"
        ];

        $query['join'] = [
            ["legal_cases lc", "lc.id = criminal_case_details.case_id", "left"],
            ["case_offense_subcategory cos", "cos.id = criminal_case_details.offence_subcategory_id", "left"],
          
        ];

        $query['where'][] = ["criminal_case_details.case_id", $case_id];


        return $this->load($query);
    }

    public function get_case_status_history($case_id) {
        $query = [];
        $query['select'] = [
            "ccd.status_of_case",
            "ccd.date_investigation_authorized",
            "CONCAT(u.firstName, ' ', u.lastName) as updated_by"
        ];

        $query['join'] = [
            ["user_profiles u", "u.user_id = ccd.modifiedBy", "left"]
        ];

        $query['where'][] = ["ccd.case_id", $case_id];
        $query['order_by'] = ["ccd.modifiedOn DESC"];

        return $this->load_all($query);
    }

    public function update_case_status($case_id, $new_status, $auth_date = null) {
        $data = [
            'status_of_case' => $new_status,
            'date_investigation_authorized' => $auth_date,
            'modifiedOn' => date('Y-m-d H:i:s'),
            'modifiedBy' => $this->logged_user_id
        ];

        return $this->update($data, ['case_id' => $case_id]);
    }
}

// SQL Server implementation
class sqlsrv_Criminal_case_detail extends mysql_Criminal_case_detail {
    public function lookup($search_term) {
        $this->ci->db->select("id, origin_of_case, status_of_case");
        $this->ci->db->like("origin_of_case", $search_term);
        $this->ci->db->or_like("status_of_case", $search_term);
        $query = $this->ci->db->get($this->_table);
        return $query->result_array();
    }
}

class mysqli_Criminal_case_detail extends mysql_Criminal_case_detail {}