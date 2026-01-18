<?php

if (!defined("BASEPATH")) {
    exit("No direct script access allowed");
}
class Case_type extends My_Model
{
    protected $modelName = "case_type";
    protected $_table = "case_types";
    protected $_listFieldName = "name";
    protected $_fieldsNames = ["id", "name", "litigation", "corporate","criminal", "litigationSLA", "legalMatterSLA", "isDeleted"];
    protected $litigationValues = ["yes", "no"];
    protected $corporateValues = ["yes", "no"];
    protected $criminalValues = ["yes", "no"];
    protected $caseTypeOfIP = "Case of Intellectual Property";
    public function __construct()
    {
        parent::__construct();
        $this->validate = ["name" => ["required" => ["required" => true, "allowEmpty" => false, "rule" => ["minLength", 1], "message" => $this->ci->lang->line("cannot_be_blank_rule")], "unique" => ["rule" => ["combinedUnique", ["isDeleted"]], "message" => $this->ci->lang->line("already_exists")], "maxLength" => ["rule" => ["maxLength", 255], "message" => sprintf($this->ci->lang->line("max_characters"), 255)]], "litigation" => ["required" => false, "allowEmpty" => true, "rule" => ["inList", $this->litigationValues], "message" => sprintf($this->ci->lang->line("required_list_values"), implode(", ", $this->litigationValues))], "criminal" => ["required" => false, "allowEmpty" => true, "rule" => ["inList", $this->criminalValues], "message" => sprintf($this->ci->lang->line("required_list_values"), implode(", ", $this->criminalValues))], "corporate" => ["required" => false, "allowEmpty" => true, "rule" => ["inList", $this->corporateValues], "message" => sprintf($this->ci->lang->line("required_list_values"), implode(", ", $this->corporateValues))], "litigationSLA" => ["required" => false, "allowEmpty" => true, "rule" => "numeric", "message" => sprintf($this->ci->lang->line("is_numeric_rule"), $this->ci->lang->line("number_of_days"))], "legalMatterSLA" => ["required" => false, "allowEmpty" => true, "rule" => "numeric", "message" => sprintf($this->ci->lang->line("is_numeric_rule"), $this->ci->lang->line("number_of_days"))]];
    }
    public function get_first_litigation_type($litigationOrCriminal="litigation")
    {
        $this->ci->db->select("id");
        $this->ci->db->where($litigationOrCriminal, "yes");
        $this->ci->db->where("isDeleted", 0);
        $query = $this->ci->db->get($this->_table);
        $numRows = $query->num_rows();
        if (0 < $numRows) {
            $row = $query->row_array();
            return $row["id"];
        }
        return "1";
    }

    public function get_all_case_types_with_due_conditions()
    {
        $query = [];
        $query["select"][] = ["case_types.*"];
        $case_types = $this->load_all($query);
        foreach ($case_types as $field => $record) {
            $this->ci->load->model("case_types_due_condition", "case_types_due_conditionfactory");
            $this->ci->case_types_due_condition = $this->ci->case_types_due_conditionfactory->get_instance();
            $case_types[$field]["due_conditions"] = $this->ci->case_types_due_condition->get_conditions_per_case_type($record["id"]);
        }
        return $case_types;
    }
    public function get_case_type_due_value($case_type_id)
    {
    }
    public function get_all_case_types_by_condition($condition)
    {
        return $this->ci->case_type->load_all(["where" => $condition, "order_by" => ["name", "asc"]]);
    }
    public function load_list_case_types_by_condition($condition)
    {
        return $this->ci->case_type->load_list(["where" => $condition, "order_by" => ["name", "asc"]]);
    }
    public function api_load_all_types_per_case_category($caseCategoryFlag = NULL)
    {
        $query = [];
        if ($caseCategoryFlag == "litigation") {
            $query["where"][] = ["litigation", "yes"];
        } else {
            if ($caseCategoryFlag == "corporate") {
                $query["where"][] = ["corporate", "yes"];
            }
            elseif ($caseCategoryFlag == "criminal"){
                $query["where"][] = ["criminal", "yes"];
            }
        }
        $query["where"][] = ["isDeleted", 0];
        $query["where"][] = ["name != '" . $this->get("caseTypeOfIP") . "'"];
        $query["order_by"] = ["name asc"];
        return parent::load_all($query);
    }
    public function get_due_value($case_type_id, $priority = NULL, $client_id = NULL, $user_companies = NULL, $logged_in_user)
    {
        $case_type_data = $this->ci->case_type->load($case_type_id);
        $this->ci->load->model("case_types_due_condition", "case_types_due_conditionfactory");
        $this->ci->case_types_due_condition = $this->ci->case_types_due_conditionfactory->get_instance();
        $case_types_due_conditions = $this->ci->case_types_due_condition->get_conditions_per_case_type($case_type_id);
        foreach ($case_types_due_conditions as $field => $record) {
            if ($priority == $record["priority"] && $logged_in_user == NULL) {
                return [$record["due_in"], NULL];
            }
            if ($priority == $record["priority"] && $logged_in_user == $record["clientData"]["member_id"] && $record["clientData"]["type"] == "Person") {
                return [$record["due_in"], $record["client_id"]];
            }
            if ($priority == "all" && $logged_in_user == $record["clientData"]["member_id"] && $record["clientData"]["type"] == "Person") {
                return [$record["due_in"], $record["client_id"]];
            }
        }
        foreach ($user_companies as $company_field => $company_record) {
            foreach ($case_types_due_conditions as $field => $record) {
                if ($priority == $record["priority"] && $company_field == NULL) {
                    return [$record["due_in"], NULL];
                }
                if ($priority == $record["priority"] && $company_field == $record["clientData"]["member_id"] && $record["clientData"]["type"] == "Company") {
                    return [$record["due_in"], $record["client_id"]];
                }
                if ($priority == "all" && $company_field == $record["clientData"]["member_id"] && $record["clientData"]["type"] == "Company") {
                    return [$record["due_in"], $record["client_id"]];
                }
            }
        }
        $this->ci->load->model("client");
        $temp_table = $this->table;
        $this->_table = "clients";
        $query["select"][] = ["clients.id ", false];
        $query["where"][] = ["clients.contact_id = " . $logged_in_user . "  "];
        $clients_array = $this->load($query);
        foreach ($case_types_due_conditions as $field => $record) {
            if (($priority == $record["priority"] || $record["priority"] == "all") && $record["clientData"]["type"] == "all") {
                return [$record["due_in"], $clients_array["id"]];
            }
        }
        $this->_table = $temp_table;
        return [$case_type_data["litigationSLA"], NULL];
    }
    public function api_get_due_value($case_type_id, $priority = NULL, $client_id = NULL, $client_type = NULL)
    {
        $case_type_data = $this->ci->case_type->load($case_type_id);
        $this->ci->load->model("case_types_due_condition", "case_types_due_conditionfactory");
        $this->ci->case_types_due_condition = $this->ci->case_types_due_conditionfactory->get_instance();
        $case_types_due_conditions = $this->ci->case_types_due_condition->get_conditions_per_case_type($case_type_id);
        foreach ($case_types_due_conditions as $field => $record) {
            if ($priority == $record["priority"] && $client_id == NULL) {
                return $record["due_in"];
            }
            if ($priority == $record["priority"] && $client_id == $record["clientData"]["member_id"] && $record["clientData"]["type"] == $client_type) {
                return $record["due_in"];
            }
            if ($priority == NULL && $client_id == $record["clientData"]["member_id"] && $record["clientData"]["type"] == $client_type) {
                return $record["due_in"];
            }
        }
        return $case_type_data["litigationSLA"];
    }
    public function load_all_data($is_export = false)
    {
        $this->ci->load->model("case_types_due_condition", "case_types_due_conditionfactory");
        $this->ci->case_types_due_condition = $this->ci->case_types_due_conditionfactory->get_instance();
        $query = [];
        $query["where"][] = ["isDeleted", 0];
        $query["where"][] = ["name != '" . $this->get("caseTypeOfIP") . "'"];
        $query["order_by"] = ["name asc"];
        $case_types = $is_export ? parent::load_all($query) : $this->paginate(["where" => $query["where"], "order_by" => $query["order_by"]]);
        foreach ($case_types as $field => $record) {
            $case_types[$field]["case_type_due_conditions"] = $this->ci->case_types_due_condition->get_conditions_per_case_type($record["id"]);
        }
        return $case_types;
    }
    public function count_rows_table($table, $where, $join = NULL)
    {
        $this->_table = $table;
        $query = [];
        $query["select"] = ["COUNT(0) as numRows"];
        if (isset($where) && !empty($where)) {
            foreach ($where as $key => $value) {
                $query["where"][] = [$key, $value];
            }
        }
        if ($join) {
            $query["join"] = [$join];
        }
        $data = $this->load($query);
        $this->_table = "case_types";
        return $data["numRows"];
    }
    public function soft_delete($id)
    {
        $this->ci->db->set("isDeleted", 1);
        $this->ci->db->where("id", $id);
        return $this->ci->db->update($this->_table);
    }
    public function add_case_type($data)
    {
        $data_for_insert = [];
        $data_for_insert["name"] = $data["name"];
        $data_for_insert["corporate"] = $data["corporate"];
        $data_for_insert["litigation"] = $data["litigation"];
        $data_for_insert["criminal"] = $data["criminal"];
        $data_for_insert["isDeleted"] = $data["isDeleted"];
        $data_for_insert["litigationSLA"] = $data["litigationSLA"];
        $this->ci->db->insert($this->_table, $data_for_insert);
        if (0 < $this->ci->db->affected_rows()) {
            return $this->ci->db->insert_id();
        }
    }
}

?>