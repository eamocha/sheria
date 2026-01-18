<?php

if (!defined("BASEPATH")) {
    exit("No direct script access allowed");
}
class Court extends My_Model
{
    protected $modelName = "court";
    protected $_table = "courts";
    protected $_listFieldName = "name";
    protected $_fieldsNames = ["id", "name","court_rank_id","court_region_id","court_type_id","court_hierarchy"];
    public function __construct()
    {
        parent::__construct();
        $this->validate = ["name" => ["required" => true, "allowEmpty" => false, "rule" => ["maxLength", 255], "message" => sprintf($this->ci->lang->line("required__max_length_rule"), $this->ci->lang->line("name"), 255)]];
    }
    public function k_load_all_courts($filter, $sortable)
    {
        $_table = $this->_table;
        $query = [];
        $response = [];

        // Select courts and related fields
        $query["select"] = [
            "courts.*,
            court_degrees.name AS rank_name,
            court_regions.name AS region_name,
            court_types.name AS type_name,
            courts.court_hierarchy",
            false
        ];

        // Add joins for rank, region, and type
        $query["join"][] = ["court_degrees", "court_degrees.id = courts.court_rank_id", "left"];
        $query["join"][] = ["court_regions", "court_regions.id = courts.court_region_id", "left"];
        $query["join"][] = ["court_types", "court_types.id = courts.court_type_id", "left"];

        // Apply filters
        if (is_array($filter) && isset($filter["filters"])) {
            foreach ($filter["filters"] as $_filter) {
                $this->prep_k_filter($_filter, $query, $filter["logic"]);
            }
            unset($_filter);
        }

        $this->prep_query($query);

        // Count total rows
        $response["totalRows"] = $this->ci->db->get($this->get("_table"))->num_rows();

        // Handle sorting
        if (is_array($sortable) && !empty($sortable)) {
            foreach ($sortable as $_sort) {
                $query["order_by"][] = [$_sort["field"], $_sort["dir"]];
            }
        } else {
            $query["order_by"] = ["courts.name asc"];
        }

        // Handle pagination
        if ($limit = $this->ci->input->post("take", true)) {
            $query["limit"] = [$limit, $this->ci->input->post("skip", true)];
        }

        $response["data"] = parent::load_all($query);
        $this->_table = $_table;
        return $response;
    }

    public function load_degrees_list($configList = NULL)
    {
        $configList = $configList ?? ["key" => "id", "value" => "name"];
        $table = $this->_table;
        $this->_table = "court_degrees";
        $configQury = ["select" => "court_degrees.id as id, court_degrees.name as name"];
        $result = $this->load_list($configQury, $configList);
        $this->_table = $table;
        return $result;
    }
    public function load_regions_list($configList = NULL)
    {
        $configList = $configList ?? ["key" => "id", "value" => "name"];
        $table = $this->_table;
        $this->_table = "court_regions";
        $configQury = ["select" => "court_regions.id as id, court_regions.name as name"];
        $result = $this->load_list($configQury, $configList);
        $this->_table = $table;
        return $result;
    }
    public function load_courts_list($configList = NULL)
    {
        $configList = $configList ?? ["key" => "id", "value" => "name"];
        $configQury = ["select" => "id, name"];
        return $this->load_list($configQury, $configList);
    }
}

?>