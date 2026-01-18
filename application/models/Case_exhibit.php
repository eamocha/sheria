<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Case_exhibit extends My_Model_Factory {}

class mysql_Case_exhibit extends My_Model {
    protected $modelName = "Case_exhibit";
    protected $modelCode = "EXH";
    protected $_table = "exhibit";
    protected $_listFieldName = "exhibit_label";

    // Full list of table fields
    protected $_fieldsNames = [
        'id'
        ,'case_id'
        ,'exhibit_label'
        ,'description'
        ,'temporary_removals'
        ,'manner_of_disposal'
        ,'date_received'
        ,'date_approved_for_disposal'
        ,'date_disposed'
        ,'createdOn'
        ,'modifiedOn'
        ,'createdBy'
        ,'modifiedBy'
        ,'associated_party_type'
        ,'exhibit_status'
        ,'officer_remarks'
        ,'officers_involved_id'
        ,'associated_party'
        ,'pickup_location_id'
        ,'current_location_id'
        ,'reason_for_temporary'
        ,'disposal_remarks'
        ,'status_on_pickup'
    ];

    protected $allowedNulls = [
        "temporary_removals", "manner_of_disposal", "date_approved_for_disposal",
        "date_disposed", "modifiedOn", "modifiedBy"
    ];

    protected $builtInLogs = false;

    protected $ci = null;

    public function __construct() {
        parent::__construct();
        $this->ci =& get_instance();

        $this->validate = [
            [
                'field' => 'exhibit_label',
                'label' => 'Exhibit Label',
                'rules' => 'required|max_length[255]'
            ],
            [
                'field' => 'description',
                'label' => 'Description',
                'rules' => 'required|max_length[1000]'
            ],
            [
                'field' => 'temporary_removals',
                'label' => 'Temporary Removals',
                'rules' => 'max_length[1000]'
            ],
            [
                'field' => 'manner_of_disposal',
                'label' => 'Manner of Disposal',
                'rules' => 'max_length[1000]'
            ],
            [
                'field' => 'date_received',
                'label' => 'Date Received',
                'rules' => 'required|valid_date'
            ]
        ];

        $this->logged_user_id = $this->ci->session->userdata('user_id');
        $this->override_privacy = false;
    }


    public function get_exhibit_record($id)
    { $id = is_numeric($id) ? (int)$id : $id;
        $query = [];
     $this->_table = 'exhibit ce';
        $query["select"] = [
            "ce.*,
        CONCAT(officer.firstName,' ',officer.lastName) as officer_name, 
        CONCAT(created.firstName, ' ', created.lastName) as createdBy,
        CONCAT(modified.firstName, ' ', modified.lastName) as modifiedByName,
        lc.subject as caseSubject,
        lc.category as caseCategory,
        cl.name AS current_location_name, 
        p_up_location.name AS pickup_location_name,
        lc.internalReference as caseReference",
            false
        ];
        $query["where"][] = ["ce.id", $id];
        $query["join"] = [
            ["legal_cases lc", "lc.id = ce.case_id", "left"],
            ["user_profiles officer", "officer.user_id = ce.officers_involved_id", "left"],
            ["user_profiles created", "created.user_id = ce.createdBy", "left"],
            ["user_profiles modified", "modified.user_id = ce.modifiedBy", "left"],
            ["exhibit_locations cl", "cl.id = ce.current_location_id", "left"],
            ["exhibit_locations p_up_location", "p_up_location.id = ce.pickup_location_id", "left"]
        ];
        return $this->load($query);
    }
    /**
     * Loads all exhibit records based on filters, sorting, and pagination, including detailed exhibit and case information.
     *
     * @param array $filter Kendo UI filter array.
     * @param array $sortable Kendo UI sortable array.
     * @param int $legalCaseID Optional legal case ID to filter by.
     * @param string $page_number Optional page number for pagination.
     * @param bool $language Optional language for localized data.
     * @return array               An array containing 'data' and 'totalRows'.
     */
    public function k_load_all_exhibits($filter, $sortable, $legalCaseID = 0, $page_number = "", $language = false)
    {
        // Disable identifier protection and force string escaping for complex queries.
        $this->ci->db->_protect_identifiers = false;
        $this->ci->db->_force_escape_string_values = true;
        // Ensure the language model is loaded if needed for localized data.
        $this->ci->load->model("language");
        // Initialize the response array.
        $response = [];

        // Set the primary table with the alias. This should be done before any query building logic.
        $this->_table = "exhibit AS ce";
        $table_alias = "ce"; // Use this alias consistently

        // --- PART 1: Get the total count of filtered IDs ---
        // This query is optimized to first get the IDs that match the filter criteria.
        $query_ids = ["select" => ["{$table_alias}.id"]];

        // Apply filters from the Kendo UI filter array.
        if (is_array($filter) && isset($filter["filters"])) {
            foreach ($filter["filters"] as $_filter) {
                $this->prep_k_filter($_filter, $query_ids, $filter["logic"]);
            }
            unset($_filter);
        }

        // Apply the legalCaseID filter if provided.
        if ($legalCaseID) {
            $query_ids["where"][] = ["{$table_alias}.case_id", $legalCaseID];
        }

        // Clean up empty where clause.
        if (empty($query_ids["where"])) {
            unset($query_ids["where"]);
        }

        // Group by ID to ensure distinct records.
        $query_ids["group_by"] = ["{$table_alias}.id"];

        // Execute the query to get a comma-separated string of IDs.
        $ids = $this->load_all($query_ids, "query");

        // --- PART 2: Get the detailed exhibit data using the filtered IDs ---
        // This query retrieves the full details for the filtered IDs.
        $query_data = [];

        // Define the columns to be selected, mapping to the user's requested fields.
        // Note: Some requested fields like 'identifying markings', 'signatures',
        // and specific dates/reasons for removal are not present in the provided schemas.

        $query_data["select"] = [
            "ce.id AS sno,
             ce.exhibit_label AS label_name, 
             ce.status_on_pickup AS status_on_pickup, 
             
             ce.description AS description_of_exhibit,
              ce.date_received, 
              ce.pickup_location_id,
              ce.temporary_removals,
               ce.manner_of_disposal, 
               ce.disposal_remarks,
               ce.current_location_id,
               ce.date_approved_for_disposal,
               ce.date_disposed,
               ce.officers_involved_id,
               lcg.internalReference as caseReference,
             
               lcg.id as case_id,
                               lcg.subject AS case_subject_name, 
              courts.name AS court_name,
             courts.id AS court_number,
                 lcg.opponentNames as opponents, lcg.clientName as clients"
        ];

        // Define joins to retrieve related information.
        $query_data["join"] = [
            ["legal_cases_grid AS lcg", "lcg.id = ce.case_id", "left"],
            ["courts", "courts.id = lcg.court_id", "left"],
        ];

        // Use the IDs from the first query to filter the detailed query.
        $query_data["where"][] = ["ce.id IN (" . $ids . ")"];

        // Apply sorting from the Kendo UI sortable array.
        if (is_array($sortable) && !empty($sortable)) {
            foreach ($sortable as $_sort) {
                $query_data["order_by"][] = [$_sort["field"], $_sort["dir"]];
            }
        } else {
            // Default sort order if no sorting is provided.
            $query_data["order_by"] = ["ce.date_received desc"]; // Sort by date received by default
        }

        // Apply pagination logic using 'take' and 'skip' from Kendo UI.
        if ($limit = $this->ci->input->post("take", true)) {
            $query_data["limit"] = [$this->ci->input->post("take", true), $this->ci->input->post("skip", true)];
        }

        // Execute the detailed query to get the data.
        $response["data"] = $this->load_all($query_data);

        // Get the total count of matching rows from the first query.
        $response["totalRows"] = $this->count_total_matching_rows($query_ids);

        // Restore identifier protection and string escaping to their default state.
        $this->ci->db->_protect_identifiers = true;
        $this->ci->db->_force_escape_string_values = false;

        return $response;
    }



    public function get_count_of_all_exhibit() {
        return $this->ci->db->count_all_results($this->_table);
    }

    public function get_exhibits_by_case_id($case_id) {
        $query = [];
        $query['select'] = [
            "exhibit.*,
            CONCAT(creator.firstName, ' ', creator.lastName) as creator_name,
            CONCAT(modifier.firstName, ' ', modifier.lastName) as modifier_name"
        ];

        $query['join'] = [
            ["user_profiles creator", "creator.user_id = exhibit.createdBy", "left"],
            ["user_profiles modifier", "modifier.user_id = exhibit.modifiedBy", "left"]
        ];

        $query['where'][] = ["exhibit.case_id", $case_id];
        $query['order_by'] = ["exhibit.createdOn DESC"];

        return $this->load_all($query);
    }

    //
    public function get_case_exhibits_with_details($case_id = null)
    {
        $case_id = is_numeric($case_id) ? (int)$case_id : $case_id;
        $query = [];

        $query['select'] = [
            "ce.*,
            lcg.caseID AS case_reference,
            courts.name AS court_name,
            CONCAT(lcg.clientName, '  ', lcg.opponentNames) AS parties"
        ];
        $query['from'] = 'exhibit ce';  // remember not to use AS or repeat table later

        $query['join'] = [
            ['legal_cases_grid lcg', 'lcg.id = ce.case_id', 'left'],
            ['courts', 'courts.id = lcg.court_id', 'left']
        ];

        if ($case_id !== null) {
            $query['where'][] = ['ce.case_id', $case_id];
        }

        $query['order_by'] = ['ce.date_received DESC'];

        return $this->load_all($query);
    }

    ///
//////

}

// SQL Server implementation
class sqlsrv_Case_exhibit extends mysql_Case_exhibit {
    public function lookup($search_term) {
        $this->ci->db->select("id, exhibit_label, description");
        $this->ci->db->like("exhibit_label", $search_term);
        $this->ci->db->or_like("description", $search_term);
        $query = $this->ci->db->get($this->_table);
        return $query->result_array();
    }
}

class mysqli_Case_exhibit extends mysql_Case_exhibit {}
