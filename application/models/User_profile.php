<?php

if (!defined("BASEPATH")) {
    exit("No direct script access allowed");
}
class User_profile extends My_Model
{
    protected $modelName = "user_profile";
    protected $_table = "user_profiles";
    protected $_listFieldName = "firstName";
    protected $_fieldsNames = ["id", "user_id", "status", "gender", "title", "firstName", "lastName", "father", "mother", "employeeId", "ad_userCode", "dateOfBirth", "nationality", "department", "jobTitle", "overridePrivacy", "isLawyer", "website", "phone", "fax", "mobile", "address1", "address2", "city", "state", "zip", "country", "comments", "flagChangePassword", "flagNeedApproval", "seniority_level_id", "forgetPasswordFlag", "forgetPasswordHashKey", "profilePicture", "user_code", "foreign_first_name", "foreign_last_name", "forgetPasswordUrlCreatedOn","department_id"];
    protected $allowedNulls = ["dateOfBirth", "flagChangePassword", "flagNeedApproval", "seniority_level_id", "profilePicture", "user_code", "foreign_first_name", "foreign_last_name", "forgetPasswordUrlCreatedOn","department_id"];
    protected $statusValues = ["", "Active", "Inactive"];
    protected $genderValues = ["", "Male", "Female"];
    protected $titleValues = ["", "Mr", "Mrs", "Miss", "Dr", "Me", "Judge", "Sen"];
    protected $isLawyerValues = ["", "yes", "no"];
    protected $pendingApprovalValues = ["", "1", "0"];
    public function __construct()
    {
        parent::__construct();
        $this->validate = ["user_id" => ["required" => true, "allowEmpty" => false, "rule" => "numeric", "message" => sprintf($this->ci->lang->line("is_numeric_rule"), $this->ci->lang->line("user"))], "seniority_level_id" => ["required" => false, "allowEmpty" => true, "rule" => "numeric", "message" => sprintf($this->ci->lang->line("is_numeric_rule"), $this->ci->lang->line("seniority_level"))], "status" => ["required" => ["required" => true, "allowEmpty" => false, "rule" => ["inList", $this->statusValues], "message" => sprintf($this->ci->lang->line("required_list_values"), implode(", ", $this->statusValues))], "activeUsers" => ["rule" => "validateActiveUsers", "message" => $this->ci->cloud_installation_type && $this->ci->instance_client_type == "customer" && $this->ci->instance_data_array["instanceID"] ? "display_subscription" : $this->ci->lang->line("maximum_allowed_active_users_exceeded_rule")]], "gender" => ["required" => false, "allowEmpty" => true, "rule" => ["inList", $this->genderValues], "message" => sprintf($this->ci->lang->line("required_list_values"), implode(", ", $this->genderValues))], "title" => ["required" => false, "allowEmpty" => true, "rule" => ["inList", $this->titleValues], "message" => sprintf($this->ci->lang->line("required_list_values"), implode(", ", $this->titleValues))], "firstName" => ["required" => true, "allowEmpty" => false, "rule" => ["maxLength", 255], "message" => sprintf($this->ci->lang->line("required__max_length_rule"), $this->ci->lang->line("first_name"), 255)], "lastName" => ["required" => true, "allowEmpty" => false, "rule" => ["maxLength", 255], "message" => sprintf($this->ci->lang->line("required__max_length_rule"), $this->ci->lang->line("last_name"), 255)], "father" => ["required" => false, "allowEmpty" => true, "rule" => ["maxLength", 255], "message" => sprintf($this->ci->lang->line("max_length_rule"), $this->ci->lang->line("father"), 255)], "mother" => ["required" => false, "allowEmpty" => true, "rule" => ["maxLength", 255], "message" => sprintf($this->ci->lang->line("max_length_rule"), $this->ci->lang->line("mother"), 255)], "employeeId" => ["required" => false, "allowEmpty" => true, "rule" => ["maxLength", 255], "message" => sprintf($this->ci->lang->line("max_length_rule"), $this->ci->lang->line("employee_id"), 255)], "ad_userCode" => ["required" => false, "allowEmpty" => true, "rule" => ["maxLength", 255], "message" => sprintf($this->ci->lang->line("max_length_rule"), $this->ci->lang->line("user_code"), 255)], "profilePicture" => ["required" => false, "allowEmpty" => true, "rule" => ["maxLength", 255], "message" => sprintf($this->ci->lang->line("max_length_rule"), $this->ci->lang->line("profile_picture"), 255)], "department" => ["required" => false, "allowEmpty" => true, "rule" => ["maxLength", 255], "message" => sprintf($this->ci->lang->line("max_length_rule"), $this->ci->lang->line("department"), 255)], "dateOfBirth" => ["required" => false, "allowEmpty" => true, "rule" => "date", "message" => sprintf($this->ci->lang->line("required_date_rule"), $this->ci->lang->line("date_of_birth"))], "nationality" => ["required" => false, "allowEmpty" => true, "rule" => ["maxLength", 255], "message" => sprintf($this->ci->lang->line("max_length_rule"), $this->ci->lang->line("nationality"), 255)], "jobTitle" => ["required" => false, "allowEmpty" => true, "rule" => ["maxLength", 255], "message" => sprintf($this->ci->lang->line("max_length_rule"), $this->ci->lang->line("job_title"), 255)], "isLawyer" => ["required" => true, "allowEmpty" => false, "rule" => ["inList", $this->isLawyerValues], "message" => sprintf($this->ci->lang->line("required_list_values"), implode(", ", $this->isLawyerValues))], "website" => ["required" => false, "allowEmpty" => true, "rule" => ["maxLength", 255], "message" => sprintf($this->ci->lang->line("max_length_rule"), $this->ci->lang->line("website"), 255)], "phone" => ["required" => false, "allowEmpty" => true, "rule" => ["maxLength", 255], "message" => sprintf($this->ci->lang->line("max_length_rule"), $this->ci->lang->line("phone"), 255)], "fax" => ["required" => false, "allowEmpty" => true, "rule" => ["maxLength", 255], "message" => sprintf($this->ci->lang->line("max_length_rule"), $this->ci->lang->line("fax"), 255)], "mobile" => ["required" => false, "allowEmpty" => true, "rule" => ["maxLength", 255], "message" => sprintf($this->ci->lang->line("max_length_rule"), $this->ci->lang->line("mobile"), 255)], "address1" => ["required" => false, "allowEmpty" => true, "rule" => ["maxLength", 255], "message" => sprintf($this->ci->lang->line("max_length_rule"), $this->ci->lang->line("address_1"), 255)], "address2" => ["required" => false, "allowEmpty" => true, "rule" => ["maxLength", 255], "message" => sprintf($this->ci->lang->line("max_length_rule"), $this->ci->lang->line("address_2"), 255)], "city" => ["required" => false, "allowEmpty" => true, "rule" => ["maxLength", 255], "message" => sprintf($this->ci->lang->line("max_length_rule"), $this->ci->lang->line("city"), 255)], "state" => ["required" => false, "allowEmpty" => true, "rule" => ["maxLength", 255], "message" => sprintf($this->ci->lang->line("max_length_rule"), $this->ci->lang->line("state"), 255)], "zip" => ["required" => false, "allowEmpty" => true, "rule" => ["maxLength", 32], "message" => sprintf($this->ci->lang->line("max_length_rule"), $this->ci->lang->line("zip"), 32)], "country" => ["required" => false, "allowEmpty" => true, "rule" => ["maxLength", 255], "message" => sprintf($this->ci->lang->line("max_length_rule"), $this->ci->lang->line("country"), 255)], "comments" => ["required" => false, "allowEmpty" => true, "rule" => ["minLength", 3], "message" => sprintf($this->ci->lang->line("min_length_rule"), $this->ci->lang->line("comments"), 3)], "user_code" => ["maxLength" => ["required" => true, "allowEmpty" => false, "rule" => ["maxLength", 10], "message" => sprintf($this->ci->lang->line("max_length_rule"), $this->ci->lang->line("user_code"), 10)]], "foreign_first_name" => ["required" => false, "allowEmpty" => true, "rule" => ["maxLength", 255], "message" => sprintf($this->ci->lang->line("required__max_length_rule"), $this->ci->lang->line("foreign_first_name"), 255)], "foreign_last_name" => ["required" => false, "allowEmpty" => true, "rule" => ["maxLength", 255], "message" => sprintf($this->ci->lang->line("required__max_length_rule"), $this->ci->lang->line("foreign_last_name"), 255)]];
    }
    protected function validateLicenseAvailability($check = [], $validator = [])
    {
        $result = true;
        $access_type = $this->ci->input->post("access_type", true);
        $super_admin_excempted_users = $this->ci->user->get_super_admin_excempted_users();
        $max_active_users = $this->ci->licensor->get("maxActiveUsers");
        $max_active_contract_users = $this->ci->user->return_max_active_users("contract");
        $license["core"]["maxActiveUsers"] = $max_active_users - $super_admin_excempted_users;
        $license["contract"]["maxActiveUsers"] = $max_active_contract_users - $super_admin_excempted_users;
        if ($access_type) {
            foreach ($access_type as $type) {
                $this->ci->db->where("user_profiles.id !=", (string) $this->_fields[$this->_pk]);
                $this->ci->db->where("user_profiles.status", "Active");
                $this->ci->db->where("user_group_id != ", $this->ci->user->get("superAdminInfosystaUserGroupId"));
                $this->ci->db->where("(users.type = '" . $type . "' OR users.type = 'both')", NULL, false);
                $this->ci->db->join("users", "users.id = user_profiles.user_id", "inner");
                $active_users = $this->ci->db->count_all_results($this->_table);
                $result = $active_users < $license[$type]["maxActiveUsers"] || $check != "Active" && $active_users == $license[$type]["maxActiveUsers"];
                if (!$result) {
                    return false;
                }
            }
        }
        return $result;
    }
    protected function validateActiveUsers($check, $validator)
    {
        return $this->validateLicenseAvailability($check["status"], $validator);
    }
    public function abilityToIncreaseLicenseUsers()
    {
        $super_admin_excempted_users = $this->ci->user->get_super_admin_excempted_users();
        $max_active_users = $this->ci->licensor->get("maxActiveUsers");
        $max_active_contract_users = $this->ci->user->return_max_active_users("contract");
        $data["core"]["maxActiveUsers"] = $max_active_users - $super_admin_excempted_users;
        $data["contract"]["maxActiveUsers"] = $max_active_contract_users - $super_admin_excempted_users;
        $core_active_users = $this->ci->user->count_active_users("core");
        $contract_active_users = $this->ci->user->count_active_users("contract");
        $data["core"]["active_users"] = $core_active_users["totalCount"] ? $core_active_users["totalCount"] - $super_admin_excempted_users : $core_active_users["totalCount"];
        $data["contract"]["active_users"] = $contract_active_users["totalCount"] ? $contract_active_users["totalCount"] - $super_admin_excempted_users : $contract_active_users["totalCount"];
        $can_add = $data["core"]["active_users"] < $data["core"]["maxActiveUsers"] || $data["contract"]["active_users"] < $data["contract"]["maxActiveUsers"];
        return $this->ci->cloud_installation_type && $this->ci->instance_client_type == "customer" && $this->ci->instance_data_array["instanceID"] ? $can_add : true;
    }
    public function create_profile($user_id)
    {
        return $this->ci->db->set("user_id", $user_id)->insert($this->_table);
    }
    public function get_profile_field($user_id, $fields)
    {
        return $this->ci->db->select($fields)->where("user_id", $user_id)->get($this->_table);
    }
    public function get_profile($user_id)
    {
        return $this->ci->db->where("user_id", $user_id)->get($this->_table);
    }
    public function set_profile($user_id, $data)
    {
        return $this->ci->db->where("user_id", $user_id)->update($this->_table, $data);
    }
    public function delete_profile($user_id)
    {
        return $this->ci->db->where("user_id", $user_id)->delete($this->_table);
    }
    public function get_old_values($id, $selectArr = [])
    {
        $select = "*";
        if (!empty($selectArr)) {
            $select = "user_id";
            foreach ($selectArr as $val) {
                $select .= "," . $val;
            }
        }
        return $this->load(["select" => [$select], "where" => ["user_id", $id], "limit" => "1"]);
    }
    public function touch_logs($action = "update", $oldValues = [], $modified_by = 0)
    {
        $changes = [];
        if ($action == "insert") {
            $oldValues = $this->_fieldsNames;
            $oldValues = array_fill_keys($oldValues, NULL);
            $user_id = $this->get_field("user_id");
        } else {
            $user_id = $oldValues["user_id"];
        }
        $audit_arr = ["user_id" => $user_id, "action" => $action, "fieldName" => "", "beforeData" => NULL, "afterData" => NULL, "modifiedOn" => date("Y-m-d H:i:s"), "modifiedBy" => $modified_by == 0 ? $this->ci->is_auth->get_user_id() : $modified_by];
        foreach ($oldValues as $field => $value) {
            if ($value != $this->_fields[$field] && !in_array($field, ["id", "user_id", "flagNeedApproval"])) {
                $temp_arr = $audit_arr;
                $temp_arr["fieldName"] = $field;
                $temp_arr["beforeData"] = $value;
                $temp_arr["afterData"] = $this->_fields[$field];
                array_push($changes, $temp_arr);
            }
        }
        if (!empty($changes)) {
            $this->ci->db->insert_batch("user_changes", $changes);
        }
    }
    public function allow_activate_user()
    {
        $maxActiveUsers = $this->ci->licensor->get("maxActiveUsers");
        $this->ci->db->where("user_profiles.status", "Active");
        $this->ci->db->join("users", "users.id = user_profiles.user_id", "inner");
        $activeUsers = $this->ci->db->count_all_results($this->_table);
        return $activeUsers < $maxActiveUsers ? true : false;
    }
    public function get_profile_by_id($user_id)
    {
        return $this->load(["where" => ["user_id", $user_id]]);
    }
    public function auto_generate_user_code()
    {
        $auto_number = "1";
        $query = $this->ci->db->query("SELECT MAX(id) as max_id FROM users");
        $row = $query->row();
        if (!empty($row->max_id)) {
            $auto_number = $row->max_id;
        }
        return "UC" . $auto_number;
    }
}

?>