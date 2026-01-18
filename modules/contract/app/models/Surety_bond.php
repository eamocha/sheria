<?php

if (!defined("BASEPATH")) {
    exit("No direct script access allowed");
}
class Surety_bond extends My_Model_Factory
{
}
class mysql_Surety_bond extends My_Model
{
    protected $modelName = "surety_bond";
    protected $modelCode = "SB";
    protected $_table = "surety_bonds";
    protected $_listFieldName = "bond_number";
    protected $_fieldsNames = [
        "id",
        "contract_id",
        "bond_type",
        "bond_amount",
        "currency_id",
        "surety_provider",
        "bond_number",
        "effective_date",
        "expiry_date",
        "released_date",
        "bond_status",
        "document_id",
        "remarks",
        "createdOn",
        "createdBy",
        "modifiedOn",
        "modifiedBy",
        "archived"
    ];

    protected $allowedNulls = [
        "expiry_date",
        "released_date",
        "document_id",
        "remarks",
        "createdBy",
        "modifiedBy"
    ];
    protected $archivedValues = ["", "yes", "no"];
    protected $builtInLogs = false;
    public function __construct()
    {
        parent::__construct();

        $this->validate = [
            "contract_id" => [
                "required" => true,
                "allowEmpty" => false,
                "rule" => "numeric",
                "message" => sprintf($this->ci->lang->line("is_numeric_rule"), $this->ci->lang->line("contract_id"))
            ],
            "bond_type" => [
                "required" => true,
                "allowEmpty" => false,
                "rule" => ["maxLength", 50],
                "message" => sprintf($this->ci->lang->line("cannot_be_blank_rule"), $this->ci->lang->line("bond_type"))
            ],
            "bond_amount" => [
                "required" => true,
                "allowEmpty" => false,
                "rule" => ["maxLengthDecimal", 18, 2], // Max 18 digits total, 2 after decimal
                "message" => sprintf($this->ci->lang->line("is_numeric_rule"), $this->ci->lang->line("bond_amount"))
            ],
            "currency_id" => [
                "required" => true,
                "allowEmpty" => false,
                "rule" => "numeric",
                "message" => sprintf($this->ci->lang->line("is_numeric_rule"), $this->ci->lang->line("currency"))
            ],
            "surety_provider" => [
                "required" => true,
                "allowEmpty" => false,
                "rule" => ["maxLength", 255],
                "message" => sprintf($this->ci->lang->line("cannot_be_blank_rule"), $this->ci->lang->line("surety_provider"))
            ],
            "bond_number" => [
                "required" => true,
                "allowEmpty" => false,
                "rule" => ["maxLength", 100],
                "message" => sprintf($this->ci->lang->line("cannot_be_blank_rule"), $this->ci->lang->line("bond_number")),
                "unique" => [
                    "rule" => ["isUnique", ["surety_bonds", "bond_number"]], // Checks uniqueness in the table
                    "message" => $this->ci->lang->line("already_exists_rule")
                ]
            ],
            "effective_date" => [
                "required" => true,
                "allowEmpty" => false,
                "rule" => "date", // Assuming a 'date' validation rule exists in your custom rules
                "message" => sprintf($this->ci->lang->line("invalid_date_rule"), $this->ci->lang->line("effective_date"))
            ],
            "expiry_date" => [
                "required" => false, // Optional field
                "allowEmpty" => true,
                "rule" => "date",
                "message" => sprintf($this->ci->lang->line("invalid_date_rule"), $this->ci->lang->line("expiry_date"))
            ],
            "released_date" => [
                "required" => false, // Optional field
                "allowEmpty" => true,
                "rule" => "date",
                "message" => sprintf($this->ci->lang->line("invalid_date_rule"), $this->ci->lang->line("released_date"))
            ],
            "bond_status" => [
                "required" => true,
                "allowEmpty" => false,
                "rule" => ["maxLength", 50],
                "message" => sprintf($this->ci->lang->line("cannot_be_blank_rule"), $this->ci->lang->line("bond_status"))
            ],
            "document_id" => [
                "required" => false,
                "allowEmpty" => true,
                "rule" => "numeric",
                "message" => sprintf($this->ci->lang->line("is_numeric_rule"), $this->ci->lang->line("document_id"))
            ],
            "remarks" => [
                "required" => false,
                "allowEmpty" => true,
                "rule" => ["maxLength", 65535], // For NVARCHAR(MAX) or TEXT, a large max length
                "message" => sprintf($this->ci->lang->line("max_characters"), $this->ci->lang->line("remarks"), 65535)
            ],

        ];
    }
    public function validateDates($data)
    {
        $errors = [];

        $effective = isset($data['effective_date']) ? strtotime($data['effective_date']) : false;
        $expiry    = isset($data['expiry_date']) ? strtotime($data['expiry_date']) : false;
        $released  = isset($data['released_date']) ? strtotime($data['released_date']) : false;

        // If dates are provided but invalid format
        if ($data['effective_date'] && !$effective) {
            $errors['effective_date'] = 'Effective date is invalid.';
        }
        if ($data['expiry_date'] && !$expiry) {
            $errors['expiry_date'] = 'Expiry date is invalid.';
        }
        if ($data['released_date'] && !$released) {
            $errors['released_date'] = 'Released date is invalid.';
        }

        // Date logic
        if ($expiry && $effective && $expiry < $effective) {
            $errors['expiry_date'] = 'Expiry date must be after or equal to the effective date.';
        }

        if ($released && $effective && $released < $effective) {
            $errors['released_date'] = 'Released date must be after or equal to the effective date.';
        }

        if ($released && $expiry && $released < $expiry) {
            $errors['released_date'] = 'Released date must be after or equal to the expiry date.';
        }

        // ✅ Attach to model errors for controller to access
        if (!empty($errors)) {
            $this->set("validationErrors", $errors); // Make sure `set()` is inherited from My_Model
            return false;
        }

        return true;
    }



    /**
     * Adds a new surety bond record to the database.
     *
     * @param array $bond_data An associative array containing the bond details.
     * @return array An array with 'result' (boolean for success) and 'validationErrors' (if any).
     */
    public function add_surety_bond($bond_data)
    {

        if (!isset($bond_data['createdBy'])) {
            $bond_data['createdBy'] = $this->ci->session->userdata('user_id') ?? 'System';
        }
        if (!isset($bond_data['modifiedBy'])) {
            $bond_data['modifiedBy'] = $this->ci->session->userdata('user_id') ?? 'System';
        }

        $this->set_fields($bond_data); // Sets the data to the model's internal fields
        $response["result"] = $this->insert(); // Calls the insert method from My_Model
        $response["validationErrors"] = $this->get("validationErrors"); // Retrieves validation errors

        return $response;
    }

    /**
     * Loads all surety bonds associated with a specific contract ID.
     *
     * @param int $contract_id The ID of the contract.
     * @param array $sortable An array of sorting parameters (e.g., [['field' => 'effective_date', 'dir' => 'desc']]).
     * @return array An array containing 'totalRows' and 'data' (the list of bonds).
     */
    public function load_all_surety_bonds_by_contract($contract_id, $sortable = [])
    {
        $query = [];
        $response = [];

        $query["select"] = [
            "surety_bonds.id, surety_bonds.bond_type, surety_bonds.surety_provider, surety_bonds.bond_amount, surety_bonds.bond_number,
             surety_bonds.effective_date, surety_bonds.expiry_date, surety_bonds.released_date,
             surety_bonds.bond_status, surety_bonds.remarks,
             iso_currencies.code,
             documents_management_system.name AS associated_document_name"
        ];
        $query["join"] = [
            ["iso_currencies", "iso_currencies.id = surety_bonds.currency_id", "inner"],
            ["documents_management_system", "documents_management_system.id = surety_bonds.document_id", "left"] // LEFT JOIN as document_id is nullable
        ];
        $query["where"][] = ["surety_bonds.contract_id =" . $contract_id, NULL, false];

        $this->prep_query($query);
        $response["totalRows"] = $this->ci->db->get($this->get("_table"))->num_rows();

        if (is_array($sortable) && !empty($sortable)) {
            foreach ($sortable as $_sort) {
                $query["order_by"][] = [$_sort["field"], $_sort["dir"]];
            }
        } else {
            // Default sorting if none provided
            $query["order_by"][] = ["surety_bonds.effective_date", "desc"];
        }

        if ($limit = $this->ci->input->post("take", true)) {
            $query["limit"] = [$this->ci->input->post("take", true), $this->ci->input->post("skip", true)];
        }

        $response["data"] = $this->load_all($query);
        return $response;
    }

    /**
     * Retrieves a single surety bond by its ID.
     *
     * @param int $bond_id The ID of the surety bond.
     * @return array|null The surety bond data or NULL if not found.
     */
    public function get_surety_bond_by_id($bond_id)
    {
        $query["select"] = [
            "surety_bonds.*, iso_currencies.currencyCode, iso_currencies.currencyName,
             documents_management_system.document_name AS associated_document_name"
        ];
        $query["join"] = [
            ["iso_currencies", "iso_currencies.id = surety_bonds.currency_id", "inner"],
            ["documents_management_system", "documents_management_system.id = surety_bonds.document_id", "left"]
        ];
        $query["where"][] = ["surety_bonds.id =" . $bond_id, NULL, false];
        $result = $this->load($query);
        return isset($result) ? $result : NULL;
    }

    /**
     * Updates an existing surety bond record.
     *
     * @param array $bond_data An associative array containing the bond details, including 'id'.
     * @return array An array with 'result' (boolean for success) and 'validationErrors' (if any).
     */
    public function update_surety_bond($bond_data)
    {
        // Set modifiedBy using current user from session/auth system
        $bond_data['modifiedBy'] = $this->ci->session->userdata('user_id') ?? 'System'; // Adjust based on your session

        $this->set_fields($bond_data);
        $response["result"] = $this->update(); // Calls the update method from My_Model
        $response["validationErrors"] = $this->get("validationErrors");
        return $response;
    }

    /**
     * Deletes a surety bond record by its ID.
     *
     * @param int $bond_id The ID of the surety bond to delete.
     * @return bool True on success, false on failure.
     */
    public function delete_surety_bond($bond_id)
    {
        return $this->delete($bond_id);
    }
}

class mysqli_Surety_bond extends mysql_Surety_bond
{

}
class sqlsrv_Surety_bond extends mysql_Surety_bond
{
    public function k_load_all($filter, $sortable, $return_query = false)
    {
        $query = [];
        $this->ci->load->model("language");
        $lang_id = $this->ci->language->get_id_by_session_lang();

        // Define the SELECT clause
        $query["select"][] = [" COUNT(*) OVER() AS total_rows, 
                      ISNULL(surety_bonds.id, 0) AS suretyId,
                      ct.id,
                      ct.name AS name,
                      ct.reference_number AS reference_number,
                      ct.start_date AS start_date,
                      ct.end_date AS end_date,
                      ct.value AS value,
                      iso_currencies.code AS currency,
                      surety_bonds.bond_type AS bond_type,
                      surety_bonds.surety_provider AS surety_provider,
                      surety_bonds.bond_amount AS bond_amount,
                      surety_bonds.bond_number AS bond_number,
                      surety_bonds.effective_date AS effective_date,
                      surety_bonds.expiry_date AS expiry_date,
                      surety_bonds.bond_status AS bond_status,
                      surety_bonds.remarks AS remarks,
                      departments.name AS user_department,
                      YEAR(ct.start_date) AS year,
                      CASE 
                          WHEN ct.start_date IS NULL OR ct.end_date IS NULL OR ct.start_date > ct.end_date THEN ''
                          WHEN ct.start_date = ct.end_date THEN '0 days'
                          ELSE (
                              SELECT CONCAT(
                                  CASE WHEN years > 0 
                                       THEN CONCAT(years, ' year', CASE WHEN years > 1 THEN 's' ELSE '' END) 
                                       ELSE '' END,
                                  CASE WHEN years > 0 AND (months > 0 OR days > 0) THEN ', ' ELSE '' END,
                                  CASE WHEN months > 0 
                                       THEN CONCAT(months, ' month', CASE WHEN months > 1 THEN 's' ELSE '' END) 
                                       ELSE '' END,
                                  CASE WHEN months > 0 AND days > 0 THEN ', ' ELSE '' END,
                                  CASE WHEN days > 0 
                                       THEN CONCAT(days, ' day', CASE WHEN days > 1 THEN 's' ELSE '' END) 
                                       ELSE '' END
                              )
                              FROM (
                                  SELECT 
                                      DATEDIFF(YEAR, ct.start_date, ct.end_date) AS years,
                                      DATEDIFF(MONTH, DATEADD(YEAR, DATEDIFF(YEAR, ct.start_date, ct.end_date), ct.start_date), ct.end_date) AS months,
                                      DATEDIFF(DAY, DATEADD(MONTH, DATEDIFF(MONTH, DATEADD(YEAR, DATEDIFF(YEAR, ct.start_date, ct.end_date), ct.start_date), ct.end_date), DATEADD(YEAR, DATEDIFF(YEAR, ct.start_date, ct.end_date), ct.start_date)), ct.end_date) AS days
                              ) AS duration
                          )
                      END AS contract_period,
                      parties = STUFF(
                        (SELECT ', ' + (CASE WHEN contract_party.party_member_type IS NULL THEN NULL
                                          ELSE (CASE WHEN party_category_language.name != '' THEN 
                                                  (CASE WHEN contract_party.party_member_type = 'company'
                                                     THEN (CONCAT(party_company.name, ' - ', party_category_language.name))
                                                     ELSE (CASE WHEN party_contact.father != '' 
                                                           THEN (CONCAT(party_contact.firstName, ' ', party_contact.father, ' ', party_contact.lastName, ' - ', party_category_language.name))
                                                           ELSE (CONCAT(party_contact.firstName, ' ', party_contact.lastName, ' - ', party_category_language.name))
                                                           END)
                                                     END)
                                                ELSE (CASE WHEN contract_party.party_member_type = 'company'
                                                     THEN party_company.name
                                                     ELSE (CASE WHEN party_contact.father != '' 
                                                           THEN (CONCAT(party_contact.firstName, ' ', party_contact.father, ' ', party_contact.lastName))
                                                           ELSE (CONCAT(party_contact.firstName, ' ', party_contact.lastName))
                                                           END)
                                                     END)
                                                END)
                                          END)
                        FROM contract_party
                        LEFT JOIN party ON party.id = contract_party.party_id
                        LEFT JOIN companies AS party_company
                            ON party_company.id = party.company_id AND contract_party.party_member_type = 'company'
                        LEFT JOIN contacts AS party_contact
                            ON party_contact.id = party.contact_id AND contract_party.party_member_type = 'contact'
                        LEFT JOIN party_category_language
                            ON party_category_language.category_id = contract_party.party_category_id 
                            AND party_category_language.language_id = '" . $lang_id . "'
                        WHERE contract_party.contract_id = ct.id
                        FOR XML PATH('')), 1, 1, '')", false];

        // Add filters if provided
        if (isset($filter) && is_array($filter)) {
            if (isset($filter["filters"])) {
                foreach ($filter["filters"] as $_filter) {
                    $this->prep_k_filter($_filter, $query, $filter["logic"]);
                }
                unset($_filter);
            }
            if (isset($filter["customFields"])) {
                $this->ci->load->model("custom_field", "custom_fieldfactory");
                $this->ci->custom_field = $this->ci->custom_fieldfactory->get_instance("custom_fieldfactory");
                $this->ci->custom_field->prep_custom_field_filters($this->modelName, $filter["customFields"], $query);
            }
        }

       $query["join"] = [
            ["contract ct", "ct.id = surety_bonds.contract_id", "left"],
            ["iso_currencies", "iso_currencies.id = surety_bonds.currency_id", "left"],
            ["departments", "departments.id = ct.department_id", "left"]
        ];

        // Define WHERE clause
        $query["where"][] = ["('" . $this->override_privacy . "' = 'yes' or ct.private IS NULL OR ct.private = '0' OR 
                     (ct.private = 1 AND (ct.createdBy = '" . $this->logged_user_id . "' OR 
                      ct.assignee_id = '" . $this->logged_user_id . "' OR 
                      ct.id IN (SELECT contract_id FROM contract_users WHERE user_id = '" . $this->logged_user_id . "'))))", NULL, false];

        // Define ORDER BY
        if (is_array($sortable) && !empty($sortable)) {
            foreach ($sortable as $_sort) {
                $query["order_by"][] = [$_sort["field"], $_sort["dir"]];
            }
        } else {
            $query["order_by"] = ["ct.id desc"];
        }

        // Define LIMIT and OFFSET
        if ($limit = $this->ci->input->post("take", true)) {
            $query["limit"] = [$this->ci->input->post("take", true), $this->ci->input->post("skip", true)];
        }

        // Add custom fields if applicable
        if (isset($filter["customFields"])) {
            $query["select"][] = [$this->ci->custom_field->load_grid_custom_fields($this->modelName, $this->_table)];
        }

        // Return query if requested
        if ($return_query) {
            return $query;
        }

        // Execute query
        $response["data"] = parent::load_all($query);
        $response["totalRows"] = $response["data"][0]["total_rows"] ?? false;
        return $response;
    }
}

?>
