<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Case_offense_subcategory extends My_Model_Factory {}

class mysql_Case_offense_subcategory extends My_Model {
    protected $modelName = "Case_offense_subcategory";
    protected $modelCode = "COS";
    protected $_table = "case_offense_subcategory";
    protected $_listFieldName = "name";

    // Full list of table fields
    protected $_fieldsNames = [
        "id", "name", "offense_type_id", "is_active",
    ];

    protected $allowedNulls = [];

    protected $builtInLogs = false;

    protected $ci = null;

    public function __construct() {
        parent::__construct();
        $this->ci =& get_instance();

        $this->validate = [
            [
                'field' => 'name',
                'label' => 'Subcategory Name',
                'rules' => 'required|max_length[255]'
            ],
            [
                'field' => 'offense_type_id',
                'label' => 'Offense Type',
                'rules' => 'required|numeric'
            ],
            [
                'field' => 'is_active',
                'label' => 'Active Status',
                'rules' => 'required|in_list[0,1]'
            ]
        ];

        $this->logged_user_id = $this->ci->session->userdata('user_id');
        $this->override_privacy = false;
    }

    public function get_all_active_subcategories() {
        $query = [];
        $query['where'][] = ["is_active", 1];
        $query['order_by'] = ["name ASC"];
        return $this->load_all($query);
    }

    public function get_subcategories_by_type($offense_type_id) {
        $query = [];
        $query['select'] = [
            "cos.*",
            "cot.name as offense_type_name"
        ];
        $query['join'] = [
            ["case_offense_types cot", "cot.id = cos.offense_type_id", "left"]
        ];
        $query['where'][] = ["cos.offense_type_id", $offense_type_id];
        $query['where'][] = ["cos.is_active", 1];
        $query['order_by'] = ["cos.name ASC"];
        return $this->load_all($query);
    }

    public function toggle_active_status($id, $status) {
        return $this->update(
            ['is_active' => $status],
            ['id' => $id]
        );
    }

    public function get_subcategory_dropdown($offense_type_id = null) {
        $query = [];
        $query['select'] = ["id", "name"];
        $query['where'][] = ["is_active", 1];

        if ($offense_type_id) {
            $query['where'][] = ["offense_type_id", $offense_type_id];
        }

        $query['order_by'] = ["name ASC"];
        $results = $this->load_all($query);

        $dropdown = [];
        foreach ($results as $row) {
            $dropdown[$row['id']] = $row['name'];
        }

        return $dropdown;
    }
}

// SQL Server implementation
class sqlsrv_Case_offense_subcategory extends mysql_Case_offense_subcategory {
    public function lookup($search_term) {
        $this->ci->db->select("id, name, offense_type_id");
        $this->ci->db->like("name", $search_term);
        $query = $this->ci->db->get($this->_table);
        return $query->result_array();
    }
}

class mysqli_Case_offense_subcategory extends mysql_Case_offense_subcategory {}