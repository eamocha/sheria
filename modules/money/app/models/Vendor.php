<?php

if (!defined("BASEPATH")) {
    exit("No direct script access allowed");
}
class Vendor extends My_Model
{
    protected $modelName = "vendor";
    protected $modelCode = "";
    protected $_table = "vendors";
    protected $_listFieldName = "";
    protected $_fieldsNames = ["id", "company_id", "contact_id", "createdOn", "createdBy", "modifiedOn", "modifiedBy"];
    protected $allowedNulls = ["company_id", "contact_id", "createdOn", "createdBy", "modifiedOn", "modifiedBy"];
    protected $builtInLogs = true;
    public function __construct()
    {
        parent::__construct();
        $this->validate = ["company_id" => ["required" => false, "allowEmpty" => true, "rule" => "numeric", "message" => sprintf($this->ci->lang->line("is_numeric_rule"), $this->ci->lang->line("company_id"))], "contact_id" => ["required" => false, "allowEmpty" => true, "rule" => "numeric", "message" => sprintf($this->ci->lang->line("is_numeric_rule"), $this->ci->lang->line("contact_id"))]];
    }
    public function fetch_vendor($id)
    {
        $query = [];
        $this->_table = "clients_view";
        $query["select"] = "clients_view.*, clients_view.name as vendorName";
        $query["where"] = [["id", $id], ["model", "suppliers"]];
        return $this->load($query);
    }
    public function k_load_all_vendors($filter, $sortable)
    {
        $query = [];
        $response = [];
        $this->_table = "clients_view";
        $query["select"] = ["clients_view.*", false];
        if (is_array($filter) && isset($filter["filters"])) {
            foreach ($filter["filters"] as $_filter) {
                $this->prep_k_filter($_filter, $query, $filter["logic"]);
            }
            unset($_filter);
            array_push($query["where"], ["clients_view.model", "suppliers"]);
        } else {
            $query["where"] = ["clients_view.model", "suppliers"];
        }
        $this->prep_query($query);
        $response["totalRows"] = $this->ci->db->count_all_results($this->_table);
        if (is_array($sortable) && !empty($sortable)) {
            foreach ($sortable as $_sort) {
                $query["order_by"][] = [$_sort["field"], $_sort["dir"]];
            }
        } else {
            $query["order_by"] = ["id desc"];
        }
        if ($limit = $this->ci->input->post("take", true)) {
            $query["limit"] = [$this->ci->input->post("take", true), $this->ci->input->post("skip", true)];
        }
        $response["data"] = $this->load_all($query);
        return $response;
    }
    public function get_vendor($model, $id)
    {
        if (!empty($model) && 0 < $id) {
            $query = $this->ci->db->get_where("vendors", [$model . "_id" => $id]);
            if (0 < $query->num_rows()) {
                $row = $query->row();
                return $row->id;
            }
            return $this->insert_vendor($model, $id);
        }
        return false;
    }
    private function insert_vendor($model, $id)
    {
        if (!empty($model) && 0 < $id) {
            $data = [];
            $data[$model . "_id"] = $id;
            $data["createdOn"] = date("Y-m-d H:i:s");
            $data["createdBy"] = $this->ci->is_auth->get_user_id();
            $data["modifiedOn"] = date("Y-m-d H:i:s");
            $data["modifiedBy"] = $this->ci->is_auth->get_user_id();
            $this->ci->db->insert("vendors", $data);
            if (0 < $this->ci->db->affected_rows()) {
                return $this->ci->db->insert_id();
            }
            return false;
        }
        return false;
    }
    public function insert($skipValidation = false)
    {
        return false;
    }
    public function delete($userQueryParts = [])
    {
        if (parent::delete($userQueryParts)) {
            return true;
        }
        return false;
    }
    public function api_lookup($term)
    {
        $table = $this->_table;
        $this->_table = "clients_view as vendor";
        $configList = ["key" => "id", "value" => "name"];
        $escapedTerm = $this->ci->db->escape_like_str($term);
        $configQury = ["select" => ["id, name", false], "WHERE" => [["vendor.model", "suppliers", NULL, false], ["vendor.name LIKE", "%" . $term . "%", NULL, false]]];
        $return = $this->load_all($configQury, $configList);
        $this->_table = $table;
        return $return;
    }
    public function api_get_all_suppliers()
    {
        $table = $this->_table;
        $this->_table = "clients_view as vendor";

        // 1. Query to get all supplier data, including modifiedOn for potential display per item if needed
        $data_query = [
            "select" => ["id, name, email, modifiedOn"],
            "WHERE" => ["vendor.model", "suppliers"]
        ];
        $data = $this->load_all($data_query);

        // 2. Query to get only the latest modifiedOn date for all suppliers
        // This will order by modifiedOn descending and limit to 1 record to get the most recent one.
        $latest_modified_query["select"] = ["modifiedOn"];
        $latest_modified_query["WHERE"] = ["model", "suppliers"];
            $latest_modified_query["order_by"]= ["modifiedOn Desc"]; // Order by last modified date in descending order
        $latest_modified_query["limit"] =[1]; // Select only the first (most recent) record


        $latest_modified_result = $this->load_all($latest_modified_query);

        $last_updated_date = null;

        // Check if a result was returned and extract the modifiedOn date
        if (!empty($latest_modified_result) && isset($latest_modified_result[0]['modifiedOn'])) {
            $last_updated_timestamp = strtotime($latest_modified_result[0]['modifiedOn']);
            if ($last_updated_timestamp !== false) {
                $last_updated_date = date('Y-m-d H:i:s', $last_updated_timestamp);
            }
        }else

        // Restore the original table name for other operations in the model
        $this->_table = $table;

        // Return an associative array containing both the data and the overall last_updated date
        return [
            'data' => $data,
            'last_modified' => $last_updated_date
        ];
    }

}


?>