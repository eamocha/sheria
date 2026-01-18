<?php

if (!defined("BASEPATH")) {
    exit("No direct script access allowed");
}
class Grid_saved_filter extends My_Model_Factory
{
}
class mysql_Grid_saved_filter extends My_Model
{
    protected $modelName = "grid_saved_filter";
    protected $_table = "grid_saved_filters";
    protected $_listFieldName = "";
    protected $_fieldsNames = ["id", "model", "user_id", "filterName", "formData", "isGlobalFilter", "createdOn", "createdBy", "modifiedOn", "modifiedBy"];
    protected $allowedNulls = ["user_id", "createdOn", "createdBy", "modifiedOn", "modifiedBy"];
    protected $builtInLogs = true;
    protected $models = ["Company", "Contact", "Matter", "Litigation", "IP", "Criminal", "Expense", "Invoice_Header", "Bill_Header", "User_Activity_Log_Money_Module", "Account", "Quote_Header", "Matter_Container", "Legal_Case_Hearing", "contract", "awaiting_approvals", "awaiting_signatures"];
    public function __construct()
    {
        parent::__construct();
        $this->validate = ["filterName" => ["required" => ["required" => true, "allowEmpty" => false, "rule" => ["maxLength", 255], "message" => sprintf($this->ci->lang->line("required__max_length_rule"), $this->ci->lang->line("name"), 255)], "unique" => ["rule" => ["combinedUnique", ["model", "user_id"]], "message" => sprintf($this->ci->lang->line("field_must_be_unique_rule"), $this->ci->lang->line("filter_name"))]], "model" => ["required" => true, "allowEmpty" => false, "rule" => ["inList", $this->models], "message" => sprintf($this->ci->lang->line("required_list_values"), implode(", ", $this->models))]];
    }
    public function add($data)
    {
        $this->removeFlagDefaultFilters($data["user_id"], $data["model"]);
        $this->set_fields($data);
        $this->set_field("formData", serialize($data["formData"]));
        $return = $this->insert();
        if ($return) {
            $filterId = $this->get_field("id");
            $this->setUserDefaultFilter($filterId, $data["user_id"], $data["model"]);
        }
        return $return;
    }
    public function edit($id, $data)
    {
        $id = trim($id);
        $this->fetch($id);
        $this->set_fields($data);
        if (isset($data["formData"])) {
            $this->set_field("formData", serialize($data["formData"]));
        }
        return $this->update();
    }
    public function removeFlagDefaultFilters($userId, $model)
    {
        $this->ci->db->where("user_id", $userId)->where("model", $model);
        $this->ci->db->delete("grid_saved_filters_users");
    }
    public function removeFilterUsersRelations($filterId)
    {
        $this->ci->db->where("filter_id", $filterId);
        $this->ci->db->delete("grid_saved_filters_users");
        $this->ci->db->where("grid_saved_filter_id", $filterId);
        $this->ci->db->delete("grid_saved_columns");
    }
    public function delete_filter($filterId)
    {
        $this->removeFilterUsersRelations($filterId);
        return $this->delete($filterId);
    }
    public function setUserDefaultFilter($filterId, $userId, $model)
    {
        $data = [];
        $data["filter_id"] = $filterId;
        $data["user_id"] = $userId;
        $data["model"] = $model;
        $this->ci->db->insert("grid_saved_filters_users", $data);
    }
    public function loadFiltersList($model, $userId)
    {
        $query = ["select" => ["id, filterName as name, isGlobalFilter", false], "where" => ["model = '" . $model . "' AND ((user_id = " . $userId . " AND isGlobalFilter = 0) OR (isGlobalFilter = 1))"], "order_by" => ["filterName Asc"]];
        $response = $this->load_all($query);
        return $response;
    }
    public function loadAllFilters($model, $userId)
    {
        $query = ["where" => ["model = '" . $model . "' AND ((user_id = " . $userId . " AND isGlobalFilter = 0) OR (isGlobalFilter = 1))"], "order_by" => ["filterName Asc"]];
        $response = $this->load_all($query);
        return $response;
    }
    public function getDefaultFilter($model, $userId)
    {
        $query = ["select" => ["grid_saved_filters.id, grid_saved_filters.filterName as name", false], "where" => ["grid_saved_filters.model = '" . $model . "' AND sfUsers.model = '" . $model . "' AND sfUsers.user_id = " . $userId], "join" => ["grid_saved_filters_users sfUsers", "sfUsers.filter_id = grid_saved_filters.id", "left"]];
        $response = $this->load($query);
        return $response;
    }
    public function load_data($filterId)
    {
        $this->fetch($filterId);
        return $this->get_fields();
    }
    public function get_all_filters_by_model($model)
    {
        $query = ["select" => ["*", false], "where" => "grid_saved_filters.model = '" . $model . "'"];
        $response = $this->load_all($query);
        return $response;
    }
}
class mysqli_Grid_saved_filter extends mysql_Grid_saved_filter
{
}
class sqlsrv_Grid_saved_filter extends mysql_Grid_saved_filter
{
}

?>