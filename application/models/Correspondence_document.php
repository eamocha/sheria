<?php
if (!defined("BASEPATH")) {
    exit("No direct script access allowed");
}

class Correspondence_document extends My_Model_Factory
{
}

class mysqli_Correspondence_document extends My_Model
{
    protected $modelName = "correspondence_document";
    protected $_table = "correspondence_document"; // Table name as per your specification
    protected $_fieldsNames = ["id", "name", "size", "extension", "correspondence_id", "document_type_id", "document_status_id", "comments", "createdOn", "modifiedOn", "createdBy", "modifiedBy"
    ];

    public function __construct()
    {
        parent::__construct();

        $this->validate = [
            "name" => [
                "isRequired" => ["required" => true, "allowEmpty" => false, "rule" => ["minLength", 1],
                    "message" => $this->ci->lang->line("cannot_be_blank_rule")], "maxLength" => ["rule" => ["maxLength", 255],
                    "message" => sprintf($this->ci->lang->line("max_characters"), 255)],
                "trim" => ["rule" => "trim_white_spaces_validation",
                "message" => sprintf($this->ci->lang->line("cannot_be_blank_rule"), 255)]],
        ];
    }
//fetch documents per correspondence
public function get_documents_per_correspondence($correspondence_id)
{
        $query = [];
        $query["select"] = [
            "correspondence_document.*,     
   
    CONCAT(creator.firstName, ' ', creator.lastName) as creator_name,
    CONCAT(modifier.firstName, ' ', modifier.lastName) as modifier_name,
    doc_type.name as document_type_name",
        ];
        $query["join"] = [
            ["correspondence_document_types as doc_type", "doc_type.id = correspondence_document.document_type_id", "left"],
            ["user_profiles creator", "creator.user_id = correspondence_document.createdBy", "left"],
            ["user_profiles modifier", "modifier.user_id = correspondence_document.modifiedBy", "left"],
        ];
        $query["where"] = ["(correspondence_document.correspondence_id = '" . $correspondence_id . "')"];
        $return = $this->load_all($query);
   
        return $return;
}
    /**
     * Loads all records from the correspondence_document table.
     * @return array
     */
    public function load_all_records()
    {
        $query = ["select" => $this->_table . ".*"];
        return $this->load_all($query);
    }

    public function insert_new_record($data = [])
    {
        $insert_data = [
            "id" => NULL, // Let database handle IDENTITY
            "name" => $data["name"] ?? null, // Will be validated as 'required'
            "correspondence_id" => $data["correspondence_id"] ?? null,
        ];

        $insert_data = array_merge($insert_data, $data);

        $this->ci->db->insert($this->_table, $insert_data);
        if (0 < $this->ci->db->affected_rows()) {
            return $this->ci->db->insert_id();
        }
        return 0;
    }

    /**
     * @param string $language The language code (e.g., 'en', 'es').
     * @return array
     */
    public function api_load_list_per_language($language)
    {
        $query = [
            "select" => $this->_table . ".*",
        ];
        return $this->load_all($query);
    }

    /**
     * Custom function to get document details by specific criteria.
     * This was used in your save_as_pdf function for `get_document_full_details`.
     *
     * @param array $where An associative array of WHERE clause conditions.
     * @return array|null The document details, or null if not found.
     */
    public function get_document_full_details(array $where)
    {
        $query = [
            "select" => $this->_table . ".*",
            "where" => $where
        ];
        return $this->load_one($query);
    }


}


class mysql_Correspondence_document extends mysqli_Correspondence_document
{

}
class sqlsrv_Correspondence_document extends mysqli_Correspondence_document
{
    /**
     * Inserts a new record into the correspondence_document table for SQL Server.
     * SQL Server requires specific handling for IDENTITY columns when inserting
     * with an incomplete column list or `DEFAULT VALUES`.
     *
     * @param array $data An array of data to insert, particularly 'name' and 'correspondence_id'.
     * @return int The ID of the newly inserted record, or 0 on failure.
     */
    public function insert_new_record($data = [])
    {
        $insert_data = [];
        foreach ($this->_fieldsNames as $field) {
            if ($field === 'id' || $field === 'createdOn' || $field === 'modifiedOn') {
                continue;
            }
            if (isset($data[$field])) {
                $insert_data[$field] = $data[$field];
            }
        }
        // Apply default values for NOT NULL fields if not provided in $data
        if (!isset($insert_data['name'])) {
            $insert_data['name'] = 'Default Document Name'; // Fallback for DB,
        }
        if (!isset($insert_data['correspondence_id'])) {
            $insert_data['correspondence_id'] = 0; // Fallback for DB,
        }
        $this->ci->db->insert($this->_table, $insert_data);
        return $this->ci->db->insert_id();
    }
}