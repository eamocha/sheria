<?php
if (!defined("BASEPATH")) {
    exit("No direct script access allowed");
}

class System_relationship extends My_Model_Factory
{
    
}

class mysqli_System_relationship extends My_Model
{
    protected $modelName = "system_relationships";
    protected $_table = "system_relationships";
    
    // Updated field names to support polymorphic links and relationship types
    protected $_fieldsNames = [
        "id", 
        "base_id", 
        "base_type", 
        "target_id", 
        "target_type", 
        "relationship_type", 
        "comments", 
        "createdOn", 
        "createdBy"
    ];

    public function __construct()
    {
        parent::__construct();
        // You can add validation rules here, e.g., base_id and target_id are required
        $this->validate = [];
    }

    /**
     * Load all records with joins if necessary. 
     * In a polymorphic table, you usually join dynamically based on type,
     * but this provides the raw relationship data.
     */
    public function load_all_records()
    {
        $query = ["select" => "system_relationships.*"];
        $query["order_by"] = ["createdOn DESC"];
        return $this->load_all($query);
    }
public function get_related_items($base_id, $base_type) {
    // 1. Fetch all relationships for this record
    $query = [
        "where" => [
            ["base_id", $base_id],
            ["base_type", $base_type]
        ]
    ];

    $query["where"] = "(
    (base_id = $base_id AND base_type = '$base_type') 
    OR 
    (target_id = $base_id AND target_type = '$base_type')
)";
    $links = $this->load_all($query);
    $resolved_items = [];

    foreach ($links as $link) {
        $title = "Unknown Record";
        $ref = "#" . $link->target_id;

        // 2. Resolve the Name/Subject based on the module type
        switch ($link->target_type) {
            case 'correspondence':
                $record = $this->db->select("subject as title, reference_number as ref")
                                   ->get_where("correspondences", ["id" => $link->target_id])->row();
                break;
            case 'cases':
                $record = $this->db->select("case_title as title, case_number as ref")
                                   ->get_where("cases", ["id" => $link->target_id])->row();
                break;
            case 'contracts':
                $record = $this->db->select("contract_name as title, contract_ref as ref")
                                   ->get_where("contracts", ["id" => $link->target_id])->row();
                break;
            // Add other modules here...
        }

        if ($record) {
            $title = $record->title;
            $ref = $record->ref;
        }

        $resolved_items[] = [
            "id"                => $link->id, // Relationship row ID
            "target_id"         => $link->target_id,
            "target_type"       => $link->target_type,
            "display_name"      => "[$ref] $title",
            "relationship_type" => $link->relationship_type,
            "comments"          => $link->comments,
            "date_linked"       => $link->createdOn
        ];
    }

    return $resolved_items;
}
public function resolve_relationship_data($rel, $id, $type) {
    $display = ["title" => "Unknown", "ref" => "#$id", "url" => "#"];
    
    switch ($type) {
        case 'correspondence':
            $row = $this->ci->db->select("subject, reference_number")->get_where("correspondences", ["id" => $id])->row();
            if ($row) {
                $display = ["title" => $row->subject, "ref" => $row->reference_number, "url" => "front_office/view/$id"];
            }
            break;
        case 'cases':
            $row = $this->ci->db->select("case_title, case_number")->get_where("cases", ["id" => $id])->row();
            if ($row) {
                $display = ["title" => $row->case_title, "ref" => $row->case_number, "url" => "cases/view/$id"];
            }
            break;
        case 'contracts':
            $row = $this->db->select("contract_name, contract_ref")->get_where("contracts", ["id" => $id])->row();
            if ($row) {
                $display = ["title" => $row->contract_name, "ref" => $row->contract_ref, "url" => "contracts/view/$id"];
            }
            break;
    }

    return [
        'rel_id'            => $rel->id,
        'module'            => ucfirst($type),
        'display_text'      => $display['ref'] . " - " . $display['title'],
        'link_url'          => getBaseURL() . $display['url'],
        'relationship_type' => $rel->relationship_type,
        'comments'          => $rel->comments,
        'createdOn'         => $rel->createdOn
    ];
}
    /**
     * Check if a specific relationship already exists between two entities
     */
    public function relationship_exists($base_id, $base_type, $target_id, $target_type) 
    {
        $query = [
            "where" => [
                ["base_id", $base_id],
                ["base_type", $base_type],
                ["target_id", $target_id],
                ["target_type", $target_type]
            ]
        ];
        $result = $this->load_all($query);
        return !empty($result);
    }
}

class mysql_System_relationship extends mysqli_System_relationship
{
}

/**
 * SQL Server specific implementation
 */
class sqlsrv_System_relationship extends mysqli_System_relationship
{
    /**
     * Overriding insert to handle SQL Server identity behavior 
     * or custom insert requirements if needed.
     */
    public function insert_new_record()
    {
        // Note: Using DEFAULT VALUES only works if all columns except ID 
        // allow NULLs or have default constraints. 
        // It is safer to use the standard My_Model insert which uses the set_field values.
        $this->ci->db->simple_query("INSERT INTO system_relationships (createdOn) VALUES (GETDATE())");
        return $this->ci->db->insert_id();
    }
}