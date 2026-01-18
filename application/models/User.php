<?php

if (!defined("BASEPATH")) {
    exit("No direct script access allowed");
}
class User extends My_Model_Factory
{
}
class mysql_User extends My_Model
{
    protected $modelName = "user";
    protected $modelCode = "U";
    protected $_table = "users";
    protected $_listFieldName = "email";
    protected $_fieldsNames = ["id", "user_group_id", "username", "password", "email", "banned", "ban_reason", "last_ip", "last_login", "created", "modified", "modifiedBy", "isAd", "session_id", "userDirectory", "type", "workthrough", "user_guide","otp_code", "otp_expiry", "last_otp_verified_at", "last_login_device_fingerprint"];
    protected $allowedNulls = ["username", "ban_reason", "last_ip", "last_login", "created", "modified", "modifiedBy", "isAd", "session_id", "userDirectory", "workthrough", "user_guide", "otp_code", "otp_expiry", "last_otp_verified_at", "last_login_device_fingerprint"];
    protected $systemAdministrationGroupId = 0;
    protected $superAdminInfosystaUserGroupId = "";
    protected $isAdminUser = "admin@sheria360.com";
    protected $bannedValues = ["", 1, 0];
    protected $login_banning_fields = ["banned" => 1];
    public $minUsername = 5;
    public $maxUsername = 25;
    public $minPassword = 8;
    public $complexPassword = "no";
    public $maxPassword = 25;
    public function __construct()
    {
        parent::__construct();
        $this->validate = ["user_group_id" => ["required" => true, "allowEmpty" => false, "rule" => "numeric", "message" => "cannot be empty; must have a numeric value"], "username" => ["required" => ["required" => true, "allowEmpty" => false, "rule" => ["maxLength", 255], "message" => sprintf($this->ci->lang->line("required__max_length_rule"), $this->ci->lang->line("username"), 255)], "unique_login" => ["rule" => "unique_login", "message" => sprintf($this->ci->lang->line("field_must_be_unique_rule"), $this->ci->lang->line("user_login"))]], "password" => ["required" => false, "allowEmpty" => true, "rule" => ["minLength", 8], "message" => sprintf($this->ci->lang->line("required__max_length_rule"), $this->ci->lang->line("password"), 8)], "email" => ["required" => ["required" => true, "allowEmpty" => false, "rule" => ["maxLength", 255], "message" => sprintf($this->ci->lang->line("required__max_length_rule"), $this->ci->lang->line("email"), 255)], "unique_login" => ["rule" => "unique_login", "message" => sprintf($this->ci->lang->line("field_must_be_unique_rule"), $this->ci->lang->line("user_login"))]], "banned" => ["required" => true, "allowEmpty" => false, "rule" => "numeric", "message" => sprintf($this->ci->lang->line("is_numeric_rule"), $this->ci->lang->line("banned"))], "ban_reason" => ["required" => false, "allowEmpty" => true, "rule" => ["maxLength", 255], "message" => sprintf($this->ci->lang->line("required__max_length_rule"), $this->ci->lang->line("ban_reason"), 255)], "last_ip" => ["required" => false, "allowEmpty" => true, "rule" => ["maxLength", 45], "message" => "maximum characters allowed 45"], "last_login" => ["required" => false, "allowEmpty" => true, "rule" => "datetime", "message" => "must have a valid date format"], "created" => ["required" => false, "allowEmpty" => true, "rule" => "datetime", "message" => "must have a valid date format"], "modified" => ["required" => false, "allowEmpty" => true, "rule" => "datetime", "message" => "must have a valid date format"], "type" => ["required" => true, "allowEmpty" => false, "rule" => ["minLength", 1], "message" => $this->ci->lang->line("cannot_be_blank_rule")]];
        $systemPreferences = $this->ci->session->userdata("systemPreferences");
        if (isset($systemPreferences["systemAdministrationGroupId"]) && $systemPreferences["systemAdministrationGroupId"]) {
            $this->systemAdministrationGroupId = str_replace(" ", "", $systemPreferences["systemAdministrationGroupId"]);
        }
        $this->ci->load->model("system_preference");
        $minPasswordFromDB = $this->ci->system_preference->get_value("passwordMinimumLength", "PasswordPolicy");
        $this->minPassword = isset($systemPreferences["passwordMinimumLength"]) ? $systemPreferences["passwordMinimumLength"] : ($minPasswordFromDB ? $minPasswordFromDB : $this->minPassword);
        $this->complexPassword = isset($systemPreferences["passwordStrongComplexity"])? $systemPreferences["passwordStrongComplexity"] == "yes":"";
        unset($systemPreferences);
        $this->ci->load->model("user_group", "user_groupfactory");
        $this->ci->user_group = $this->ci->user_groupfactory->get_instance();
        $this->ci->user_group->fetch(["name" => $this->ci->user_group->get("superAdminInfosystaName")]);
        $this->superAdminInfosystaUserGroupId = $this->ci->user_group->get_field("id");
    }
    public function set_session($user_id, $session_id)
    {
        $old_session_id = $this->ci->db->select("session_id")->where(["id" => $user_id])->get($this->_table)->row("session_id");
        if ($old_session_id) {
            $this->ci->db->where(["id" => $old_session_id])->delete("ci_sessions");
        }
        $this->ci->db->where(["id" => $user_id])->update($this->_table, ["session_id" => $session_id]);
    }
    public function k_load_all_users($filter, $sortable)
    {
        $query = [];
        $response = [];
        $table = $this->_table;
        $this->_table = "users_full_details AS users";
        $query["select"] = ["users.id, users.user_group_id, users.seniorityLevel, user_profiles.status as modifiedByStatus, users.username, users.type, \r\n            users.isAd ,users.email, users.banned, users.ban_reason, users.last_ip, users.last_login, users.created, users.modifiedBy, \r\n            users.userModifiedName, users.modified, users.flagChangePassword, users.status, users.gender, users.title, users.firstName, users.lastName, \r\n            users.father, users.mother, users.dateOfBirth, users.jobTitle, users.isLawyer, users.website, users.phone, users.fax, users.mobile, \r\n            users.address1, users.address2, users.city, users.state, users.zip, users.overridePrivacy, users.employeeId, users.department_id, users.department, \r\n            users.ad_userCode, users.user_code, countries_languages.name AS country, users.country_id, countries_languages_nationality.name AS nationality, users.nationality_id, users.userGroupName, \r\n            users.userGroupDescription, users.providerGroup, users.provider_group_id, users.flagNeedApproval, users.userDirectory, up.foreign_first_name, up.foreign_last_name"];
        if (is_array($filter) && isset($filter["filters"])) {
            foreach ($filter["filters"] as $_filter) {
                $this->prep_k_filter($_filter, $query, $filter["logic"]);
            }
            unset($_filter);
        }
        $this->ci->load->model("language");
        $language = $this->ci->language->get_id_by_session_lang();
        $query["join"][] = ["user_profiles", " user_profiles.user_id = users.modifiedBy", "left"];
        $query["join"][] = ["user_profiles up", " up.user_id = users.id", "left"];
        $query["join"][] = ["countries_languages", "countries_languages.country_id = users.country_id AND countries_languages.language_id = " . $language, "left"];
        $query["join"][] = ["countries_languages countries_languages_nationality", "countries_languages_nationality.country_id = users.nationality_id AND countries_languages_nationality.language_id = " . $language, "left"];
        $query["where"][] = ["users.user_group_id != ", $this->get("superAdminInfosystaUserGroupId")];
        $this->prep_query($query);
        $response["totalRows"] = $this->ci->db->get($this->get("_table"))->num_rows();
        if (is_array($sortable) && !empty($sortable)) {
            foreach ($sortable as $_sort) {
                $query["order_by"][] = [$_sort["field"], $_sort["dir"]];
            }
        } else {
            $query["order_by"] = ["users.id desc"];
        }
        if ($limit = $this->ci->input->post("take", true)) {
            $query["limit"] = [$this->ci->input->post("take", true), $this->ci->input->post("skip", true)];
        }
        $response["data"] = $this->load_all($query);
        $this->_table = $table;
        return $response;
    }
    public function load_users($ids)
    {
        $query = [];
        $query["select"] = ["CONCAT(user_profiles.firstName, ' ', user_profiles.lastName) as name", false];
        $query["join"] = [["user_profiles", " user_profiles.user_id = users.id", "left"]];
        $query["where_in"] = ["users.id", $ids];
        return $this->load_all($query);
    }
    public function user_management_report($filter, $sortable)
    {
        $query = [];
        $response = [];
        $table = $this->_table;
        $this->_table = "users_full_details AS users";
        $query["select"] = ["users.id, users.user_group_id, users.authorized_by, users.userGroupDescription, users.username, users.isAd, users.email, \r\n            users.banned, users.ban_reason, users.last_ip, users.last_login, users.created, users.modifiedBy, users.userModifiedName, \r\n            modified.status as userModifiedStatus, users.modified, users.flagChangePassword, users.status, users.gender, users.title, users.firstName, \r\n            users.lastName, users.father, users.mother, users.dateOfBirth, users.jobTitle, users.isLawyer, users.website, users.phone, users.fax, \r\n            users.mobile, users.address1, users.address2, users.city, users.state, users.zip, users.overridePrivacy, users.employeeId, users.department_id, users.department, \r\n            users.ad_userCode, users.user_code, countries_languages.name AS country, users.country_id, countries_languages_nationality.name AS nationality, users.nationality_id, users.userGroupName, \r\n            users.providerGroup, users.provider_group_id, users.flagNeedApproval, user_profiles.comments"];
        if (is_array($filter) && isset($filter["filters"])) {
            foreach ($filter["filters"] as $_filter) {
                $this->prep_k_filter($_filter, $query, $filter["logic"]);
            }
            unset($_filter);
        }
        $this->ci->load->model("language");
        $language = $this->ci->language->get_id_by_session_lang();
        $query["join"] = [["user_profiles", " user_profiles.user_id = users.id", "left"], ["user_profiles modified", " modified.user_id = users.modifiedBy", "left"], ["countries_languages", "countries_languages.country_id = users.country_id AND countries_languages.language_id = " . $language, "left"], ["countries_languages countries_languages_nationality", "countries_languages_nationality.country_id = users.nationality_id AND countries_languages_nationality.language_id = " . $language, "left"]];
        $query["where"][] = ["users.user_group_id != ", $this->get("superAdminInfosystaUserGroupId")];
        $this->prep_query($query);
        $response["totalRows"] = $this->ci->db->get($this->get("_table"))->num_rows();
        if (is_array($sortable) && !empty($sortable)) {
            foreach ($sortable as $_sort) {
                $query["order_by"][] = [$_sort["field"], $_sort["dir"]];
            }
        } else {
            $query["order_by"] = ["users.id desc"];
        }
        if ($limit = $this->ci->input->post("take", true)) {
            $query["limit"] = [$this->ci->input->post("take", true), $this->ci->input->post("skip", true)];
        }
        $response["data"] = $this->load_all($query);
        $this->_table = $table;
        return $response;
    }
    public function count_user_group_users($user_group_id)
    {
        return 0 < $this->ci->db->where("user_group_id", $user_group_id)->count_all_results($this->_table);
    }
    public function user_group_has_users($user_group_id)
    {
        return $this->ci->db->where("user_group_id", $user_group_id)->count_all_results($this->_table);
    }
    public function log_password_change($user_id = false, $created = NULL)
    {
        $this->ci->session->set_userdata("forcePasswordChange", false);
        $this->ci->load->model("system_preference");
        $systemPreferences = $this->ci->system_preference->get_values();
        $id = $user_id ? $user_id : $this->_fields["id"];
        $created = NULL == $created ? date("Y-m-d H:i:s") : date("Y-m-d H:i:s", strtotime($created));
        $this->ci->load->model("user_password");
        $this->ci->user_password->set_fields(["user_id" => $this->_fields["id"], "password" => $this->_fields["password"], "created" => $created]);
        if (isset($systemPreferences["passwordDisallowedPrevious"]) && 0 < $systemPreferences["passwordDisallowedPrevious"]) {
            $this->ci->user_password->prune_user_passwords($this->_fields["id"], $systemPreferences["passwordDisallowedPrevious"]);
        }
        return $this->ci->user_password->insert();
    }
    public function lookup($term, $active)
    {
        $term = $this->ci->db->escape_like_str($term);
        $this->ci->load->model("provider_group");
        $possibleJoins = ["provider_groups_users" => ["provider_groups_users pgu", "pgu.user_id = users.id", "left"], "user_groups" => ["user_groups", "user_groups.id = users.user_group_id"]];
        $possibleSelect = ["user_groups" => ",user_groups.name as user_groups_name"];
        $configList = ["key" => "id", "value" => "name"];
        $configQury["select"] = ["DISTINCT users.id, users.user_group_id, users.email, user_profiles.status, user_profiles.gender, user_profiles.title, user_profiles.firstName, user_profiles.lastName, user_profiles.jobTitle, user_profiles.isLawyer", false];
        $configQury["where"][] = ["(CONCAT(ifnull(user_profiles.foreign_first_name, ''), ' ', ifnull(user_profiles.foreign_last_name, '')) LIKE '%" . $term . "%' OR CONCAT(user_profiles.firstName, ' ', user_profiles.lastName) LIKE '%" . $term . "%')", NULL, false];
        $configQury["join"] = [["user_profiles", " user_profiles.user_id = users.id", "inner"]];
        if ($joins = $this->ci->input->get("join")) {
            foreach ($joins as $join) {
                if (array_key_exists($join, $possibleJoins)) {
                    $configQury["join"][] = $possibleJoins[$join];
                }
                if (array_key_exists($join, $possibleSelect)) {
                    $configQury["select"][0] .= $possibleSelect[$join];
                }
            }
            unset($join);
        }
        if ($moreFilters = $this->ci->input->get("more_filters")) {
            foreach ($moreFilters as $_field => $_term) {
                if ($_field == "excludedProviderGroupUsers") {
                    $configQury["where"][] = ["users.id not in  (select user_id from provider_groups_users where provider_group_id = \"" . $_term . "\")"];
                } else {
                    if ($_field == "provider_group_id") {
                        if (isset($_term)) {
                            $this->ci->provider_group->fetch($_term);
                            $all_users_flag = $this->ci->provider_group->get_field("allUsers");
                            if ($all_users_flag != 1) {
                                $configQury["where"] = [[$_field, $_term]];
                            }
                        }
                    } else {
                        $configQury["where"] = [[$_field, $_term]];
                    }
                }
            }
            unset($_field);
            unset($_term);
        }
        $configQury["where"][] = ["user_group_id NOT IN (" . $this->get("systemAdministrationGroupId") . ")", NULL, false];
        $configQury["where"][] = ["user_group_id != ", $this->get("superAdminInfosystaUserGroupId")];
        if ($active == "active") {
            $configQury["where"][] = ["user_profiles.status", "Active"];
        }
        $configQury["order_by"] = ["user_profiles.firstName asc"];
        return $this->load_all($configQury, $configList);
    }
    public function get_last_password($user_id = false)
    {
        $id = $user_id ? $user_id : $this->_fields["id"];
        $this->ci->load->model("user_password");
        return $this->ci->user_password->load_last_password($id);
    }
    public function get_login($login)
    {
        return $this->load(["like" => ["email", $login, "none"], "or_like" => ["username", $login, "none"]]);
    }
    public function get_user_by_email($email)
    {
        return $this->load(["like" => ["email", $email, "none"]]);
    }
    public function get_user_by_username($username)
    {
        return $this->load(["like" => ["username", $username, "none"]]);
    }
    public function set_user($user_id, $data)
    {
        return $this->ci->db->where("id", $user_id)->update($this->_table, $data);
    }

    ///OTP
    ///
    /**
     * Sets the OTP code and its expiry for the user.
     * @param string $otp_code The generated OTP.
     * @param string $otp_expiry The expiry timestamp for the OTP (Y-m-d H:i:s).
     * @return void
     */
    public function set_otp($user_id, $otp_code, $otp_expiry_timestamp)
    {
        $this->ci->db->set('otp_expiry', $otp_expiry_timestamp);
        $this->ci->db->set('otp_code', $otp_code);
        $this->ci->db->where("users.id", $user_id);
        if ($this->ci->db->update($this->_table)){
            return true;
        }
      return false;
    }


    /**
     * Updates the last_otp_verified_at timestamp to the current time.
     * This is called upon successful OTP verification.
     * @return void
     */
    public function update_last_otp_verified_at()
    {
        $this->set_field('last_otp_verified_at', date('Y-m-d H:i:s'));
    }

    /**
     * Updates the last_login_device_fingerprint for the user.
     * This is called upon successful OTP verification with the current device fingerprint.
     * @param string $fingerprint The device fingerprint to store.
     * @return void
     */
    public function update_last_login_device_fingerprint($fingerprint)
    {
        $this->set_field('last_login_device_fingerprint', $fingerprint);
    }

    public function get_user_groups($user_group_id)
    {
        $query = [];
        $query["select"] = ["users.*, CONCAT ( user_profiles.title, ' ', user_profiles.firstName, ' ', user_profiles.lastName ) as user_full_name,user_profiles.status as userStatus, user_groups.name as user_group_name, user_groups.id as user_group_id", false];
        $query["join"] = [["user_profiles", " user_profiles.user_id = users.id", "inner"], ["user_groups", " user_groups.id = users.user_group_id", "inner"]];
        $query["where"] = ["users.user_group_id", $user_group_id];
        $query["order_by"] = ["users.id asc"];
        return $this->paginate($query, ["uri_segment" => 4]);
    }
    public function load_users_list($providerGroupIds = "", $configList = [], $seniority_level_ids = "")
    {
        if (!$configList) {
            $configList = ["key" => "id", "value" => "name"];
        }
        $this->ci->load->model("provider_group");
        $configQury = ["select" => ["users.id, CONCAT( us.firstName, ' ', us.lastName ) as name", false], "join" => [["user_profiles us", "us.user_id = users.id", "left"]], "where" => [["user_group_id NOT IN (" . $this->systemAdministrationGroupId . ")", NULL, false], ["user_group_id != ", $this->superAdminInfosystaUserGroupId], ["us.status", "Active"]]];
        if ($providerGroupIds) {
            $configQury["join"][] = ["provider_groups_users", " provider_groups_users.user_id = users.id", "left"];
            if (!is_array($providerGroupIds)) {
                $this->ci->provider_group->fetch($providerGroupIds);
                $all_users_flag = $this->ci->provider_group->get_field("allUsers");
                if ($all_users_flag != 1) {
                    $configQury["where_in"][] = ["provider_groups_users.provider_group_id", $providerGroupIds];
                }
            } else {
                $configQury["where_in"][] = ["provider_groups_users.provider_group_id", $providerGroupIds];
            }
        }
        if ($seniority_level_ids) {
            $configQury["where_in"][] = ["us.seniority_level_id", $seniority_level_ids];
        }
        return $this->load_list($configQury, $configList);
    }
    public function load_all_users_list($configList = [])
    {
        if (!$configList) {
            $configList = ["key" => "id", "value" => "name"];
        }
        $configQury = ["select" => ["users.id, CASE WHEN us.status='Inactive' THEN CONCAT( us.firstName, ' ', us.lastName,' (Inactive)' ) ELSE CONCAT( us.firstName, ' ', us.lastName )END as name", false], "join" => [["user_profiles us", "us.user_id = users.id", "left"]], "where" => [["user_group_id NOT IN (" . $this->systemAdministrationGroupId . ")", NULL, false], ["user_group_id != ", $this->superAdminInfosystaUserGroupId]]];
        return $this->load_list($configQury, $configList);
    }
    public function get_users_list_by_ids($ids)
    {
        $configQury = ["select" => ["users.id, CONCAT( us.firstName, ' ', us.lastName ) as name", false], "join" => [["user_profiles us", "us.user_id = users.id", "left"]], "where" => [["user_group_id NOT IN (" . $this->systemAdministrationGroupId . ")", NULL, false], ["user_group_id != ", $this->superAdminInfosystaUserGroupId], ["us.status", "Active"]]];
        $configQury["where_in"][] = ["users.id", $ids];
        return $this->load_list($configQury, ["key" => "id", "value" => "name"]);
    }
    public function count_active_users($type = "core")
    {
        return $this->load(["select" => ["count(0) as totalCount"], "join" => [["user_profiles", "user_profiles.user_id = users.id", "left"]], "where" => [["user_profiles.status", "Active"], ["(users.type = '" . $type . "' OR users.type = 'both')", NULL, false]]]);
    }
    public function destroy_session_user_id($user_id)
    {
        $table = $this->_table;
        $sessionTable = $this->ci->config->item("sess_save_path");
        $this->_table = $sessionTable;
        $sessions = $this->load_all();
        $this->_table = $table;
        foreach ($sessions as $session) {
            $return_data = [];
            $offset = 0;
            while ($offset < strlen($session["data"])) {
                if (!strstr(substr($session["data"], $offset), "|")) {
                    throw new Exception("invalid data, remaining: " . substr($session["data"], $offset));
                }
                $pos = strpos($session["data"], "|", $offset);
                $num = $pos - $offset;
                $varname = substr($session["data"], $offset, $num);
                $offset += $num + 1;
                $data = unserialize(substr($session["data"], $offset));
                $return_data[$varname] = $data;
                $offset += strlen(serialize($data));
                if (isset($return_data["AUTH_user_id"]) && $return_data["AUTH_user_id"] === $user_id) {
                    $this->fetch($user_id);
                    if ($this->get_field("banned")) {
                        $this->ci->load->model("user_autologin");
                        $this->ci->user_autologin->delete(["where" => [["user_id", ltrim($user_id, "0")], ["channel", "A4L"]]]);
                    }
                    $this->ci->db->where("id", $session["id"]);
                    $this->ci->db->delete($sessionTable);
                }
            }
        }
    }
    public function get_old_values($id)
    {
        return $this->load(["select" => ["*"], "where" => ["id", $id], "limit" => "1"]);
    }
    public function touch_logs($action = "update", $oldValues = [], $modified_by = 0)
    {
        $changes = [];
        $notInFieldArr = ["id", "created", "modified", "modifiedBy"];
        if ($action == "insert") {
            $oldValues = $this->_fieldsNames;
            $oldValues = array_fill_keys($oldValues, NULL);
            $user_id = $this->get_field("id");
            $notInFieldArr = ["id", "created", "modified", "banned", "ban_reason", "modifiedBy"];
        } else {
            $user_id = $oldValues["id"];
        }
        $audit_arr = ["user_id" => $user_id, "action" => $action, "fieldName" => "", "beforeData" => NULL, "afterData" => NULL, "modifiedOn" => date("Y-m-d H:i:s"), "modifiedBy" => $modified_by == 0 ? $this->ci->is_auth->get_user_id() : $modified_by];
        foreach ($oldValues as $field => $value) {
            if ($value != $this->_fields[$field] && !in_array($field, $notInFieldArr)) {
              //  $temp_arr = $audit_arr;
                $temp_arr = is_array($audit_arr) ? $audit_arr : [];
                $temp_arr["fieldName"] = $field;
                $temp_arr["beforeData"] = (string) $value;
                $temp_arr["afterData"] = (string) $this->_fields[$field];
                $temp_arr["modifiedBy"] = $modified_by>0?$modified_by:$user_id;
                array_push($changes, $temp_arr);
            }
        }
        if (!empty($changes)) {
            $this->ci->db->insert_batch("user_changes", $changes);
        }
    }
    public function k_load_all_users_audit_reports($filter, $sortable)
    {
        $table = $this->_table;
        $this->_table = "user_changes_full_details as user_changes";
        $query = [];
        $response = [];
        $query["select"] = ["user_changes.id, user_changes.user_id,userStatus.status as userStatus,modified.status as modifiedStatus, user_changes.action, user_changes.fieldName, user_changes.modifiedOn, user_changes.modifiedBy, user_changes.userFullName, user_changes.modifiedFullName, user_changes.beforeData, user_changes.afterData", false];
        if (is_array($filter) && isset($filter["filters"])) {
            foreach ($filter["filters"] as $_filter) {
                $this->prep_k_filter($_filter, $query, $filter["logic"]);
            }
            unset($_filter);
        }
        $query["join"] = [["users userTarget", "userTarget.id = user_changes.user_id", "inner"], ["users userModified", "userModified.id = user_changes.modifiedBy", "inner"], ["user_profiles userStatus", "userStatus.user_id = user_changes.user_id", "left"], ["user_profiles modified", "modified.user_id = user_changes.modifiedBy", "left"]];
        $query["where"][] = ["userTarget.user_group_id != ", $this->get("superAdminInfosystaUserGroupId")];
        $query["where"][] = ["userModified.user_group_id != ", $this->get("superAdminInfosystaUserGroupId")];
        if ($this->ci->cloud_installation_type) {
            $query["where"][] = ["user_changes.fieldName != ", "username"];
        }
        $this->prep_query($query);
        $response["totalRows"] = $this->ci->db->get($this->get("_table"))->num_rows();
        if (is_array($sortable) && !empty($sortable)) {
            foreach ($sortable as $_sort) {
                $query["order_by"][] = [$_sort["field"], $_sort["dir"]];
            }
        } else {
            $query["order_by"] = ["user_changes.id desc"];
        }
        if ($limit = $this->ci->input->post("take", true)) {
            $query["limit"] = [$this->ci->input->post("take", true), $this->ci->input->post("skip", true)];
        }
        $response["data"] = $this->load_all($query);
        $this->_table = $table;
        return $response;
    }
    public function get_super_admin_excempted_users()
    {
        return $this->user_group_has_users($this->superAdminInfosystaUserGroupId);
    }
    public function load_notification_users_list()
    {
        $configQury = ["select" => ["users.*", false], "join" => [["user_profiles us", "us.user_id = users.id", "left"]], "where" => [["users.id !=", $this->ci->is_auth->get_user_id()], ["users.user_group_id != ", $this->get("superAdminInfosystaUserGroupId")], ["user_group_id NOT IN (" . $this->get("systemAdministrationGroupId") . ")", NULL, false], ["us.status", "Active"]]];
        return $this->load_list($configQury);
    }
    public function load_users_ids_in_groups($groupsIds)
    {
        $configQury = ["select" => ["users.id,users.email"], "join" => [["user_profiles us", "us.user_id = users.id", "left"]], "where" => [["users.id !=", $this->ci->is_auth->get_user_id()], ["users.user_group_id != ", $this->get("superAdminInfosystaUserGroupId")], ["user_group_id  IN (" . $groupsIds . ")", NULL, false], ["us.status", "Active"]]];
        return $this->load_list($configQury);
    }
    public function api_lookup($term, $systemAdministrationGroupId)
    {
        $configQury = ["select" => "users.id, users.user_group_id, users.email, user_profiles.status, user_profiles.gender, user_profiles.title, user_profiles.firstName, user_profiles.lastName, user_profiles.jobTitle, user_profiles.isLawyer"];
        if (!empty($term)) {
            $term = $this->ci->db->escape_like_str($term);
            $configQury["where"][] = ["(CONCAT(ifnull(user_profiles.foreign_first_name, ''), ' ', ifnull(user_profiles.foreign_last_name, '')) LIKE '%" . $term . "%' OR CONCAT(user_profiles.firstName, ' ', user_profiles.lastName) LIKE '%" . $term . "%')", NULL, false];
        }
        $configQury["join"] = [["user_profiles", " user_profiles.user_id = users.id", "inner"]];
        $configQury["where"][] = ["user_group_id != ", $this->get("superAdminInfosystaUserGroupId")];
        $configQury["where"][] = ["user_profiles.status != ", "Inactive"];
        $configQury["order_by"] = ["user_profiles.firstName asc"];
        if ($systemAdministrationGroupId != "") {
            $configQury["where"][] = ["user_group_id NOT IN (" . $systemAdministrationGroupId . ")", NULL, false];
        }
        return $this->load_all($configQury);
    }
    public function api_assigned_to_lookup($provider_group_id, $systemAdministrationGroupId)
    {
        $this->ci->load->model("provider_group");
        $query = ["select" => ["DISTINCT users.id, users.user_group_id, users.email, user_profiles.status, user_profiles.gender, user_profiles.title, user_profiles.firstName, user_profiles.lastName, user_profiles.jobTitle, user_profiles.isLawyer", false], "join" => [["user_profiles", " user_profiles.user_id = users.id", "left"], ["provider_groups_users pgu", "pgu.user_id = users.id", "left"]], "order_by" => ["user_profiles.firstName asc"]];
        $this->ci->provider_group->fetch($provider_group_id);
        $all_users_flag = $this->ci->provider_group->get_field("allUsers");
        $query["where"] = [["users.user_group_id != ", $this->get("superAdminInfosystaUserGroupId")], ["user_profiles.status != ", "Inactive"]];
        if ($systemAdministrationGroupId != "") {
            $query["where"][] = ["users.user_group_id NOT IN (" . $systemAdministrationGroupId . ")", NULL, false];
        }
        if ($all_users_flag != 1) {
            $query["where"][] = ["pgu.provider_group_id = ", $provider_group_id];
        }
        return $this->load_all($query);
    }
    public function load_active_users($period)
    {
        $query = [];
        $query["select"] = ["users.id", false];
        $query["join"] = [["user_profiles", "user_profiles.user_id = users.id", "inner"]];
        $query["where"][] = ["user_profiles.status", "Active"];
        $query["where"][] = ["users.user_group_id > 1"];
        $query["where"][] = ["(CURDATE() > users.last_login + INTERVAL " . $period . " DAY)", NULL, false];
        return $this->load_all($query);
    }
    public function deactivate_users($period)
    {
        $usersToDeactivate = $this->load_active_users($period);
        $usersIDs = [];
        if (!empty($usersToDeactivate)) {
            foreach ($usersToDeactivate as $index => $user) {
                $usersIDs[] = $user["id"];
            }
            $this->_table = "user_profiles";
            $this->ci->db->set("user_profiles.status", "Inactive");
            $this->ci->db->where_in("user_profiles.user_id", $usersIDs);
            $this->ci->db->update($this->_table);
        }
        return true;
    }
    public function discard_user_changes($userId)
    {
        $this->ci->load->model("provider_group_user");
        $this->ci->load->model("notification", "notificationfactory");
        $this->ci->notification = $this->ci->notificationfactory->get_instance();
        $this->ci->notification->delete(["where" => [["user_id", $userId]]]);
        $data = ["affectedUserId" => NULL, "columnStatus" => "Rejected", "checkerId" => $this->ci->session->userdata("AUTH_user_id")];
        $this->ci->db->where(["affectedUserId" => $userId, "columnStatus" => "Pending"])->update($this->ci->user_changes_authorization->_table, $data);
        $this->ci->provider_group_user->delete(["where" => [["user_id", $userId]]]);
        $this->ci->user_profile->delete(["where" => [["user_id", $userId]]]);
        $this->ci->db->where("user_id", $userId)->delete("user_changes");
        $this->ci->user->delete($userId);
    }
    public function k_load_all_maker_checker_changes($filter, $sortable)
    {
        $table = $this->_table;
        $this->_table = "maker_checker_user_changes as UCA";
        $query = [];
        $response = [];
        $query["select"] = ["UCA.id,affected.status as affectedStatus,maker.status as makerStatus,checker.status as checkerStatus, UCA.changeType, UCA.columnName, UCA.columnValue, UCA.columnStatus, UCA.columnRequestedValue, UCA.columnType, UCA.createdOn, UCA.authorizedOn, UCA.affectedUserId, UCA.affectedUserProfile, UCA.makerUserProfile, UCA.checkerId, UCA.checkerUserProfile", false];
        if (is_array($filter) && isset($filter["filters"])) {
            foreach ($filter["filters"] as $_filter) {
                $this->prep_k_filter($_filter, $query, $filter["logic"]);
            }
            unset($_filter);
        }
        $query["join"] = [["user_profiles checker", "checker.user_id = UCA.checkerId", "left"], ["user_profiles maker", "maker.user_id = UCA.makerId", "left"], ["user_profiles affected", "affected.user_id = UCA.affectedUserId", "left"]];
        if ($this->ci->cloud_installation_type) {
            $query["where"][] = ["UCA.columnName != ", "username"];
        }
        $this->prep_query($query);
        $response["totalRows"] = $this->ci->db->get($this->get("_table"))->num_rows();
        $response["query"] = $query;
        if (is_array($sortable) && !empty($sortable)) {
            foreach ($sortable as $_sort) {
                $query["order_by"][] = [$_sort["field"], $_sort["dir"]];
            }
        } else {
            $query["order_by"] = ["UCA.id desc"];
        }
        if ($limit = $this->ci->input->post("take", true)) {
            $query["limit"] = [$this->ci->input->post("take", true), $this->ci->input->post("skip", true)];
        }
        $affectedUserChanges = $this->load_all($query);
        $this->_table = $table;
        $this->ci->load->model("language");
        $language = $this->ci->language->get_id_by_session_lang();
        $this->ci->load->model("country_language");
        foreach ($affectedUserChanges as $key => $row) {
            switch ($row["columnName"]) {
                case "user_group_id":
                    $this->ci->load->model("user_group", "user_groupfactory");
                    $this->ci->user_group = $this->ci->user_groupfactory->get_instance();
                    if ($row["columnValue"] && $this->ci->user_group->fetch($row["columnValue"])) {
                        $affectedUserChanges[$key]["columnValue"] = $this->ci->user_group->get_field("name");
                    }
                    $this->ci->user_group->fetch($row["columnRequestedValue"]);
                    $affectedUserChanges[$key]["columnRequestedValue"] = $this->ci->user_group->get_field("name");
                    break;
                case "nationality":
                    if ($row["columnValue"] && $this->ci->country_language->fetch(["country_id" => $row["columnValue"], "language_id" => $language])) {
                        $affectedUserChanges[$key]["columnValue"] = $this->ci->country_language->get_field("name");
                    }
                    $this->ci->country_language->fetch(["country_id" => $row["columnRequestedValue"], "language_id" => $language]);
                    $affectedUserChanges[$key]["columnRequestedValue"] = $this->ci->country_language->get_field("name");
                    break;
                case "country":
                    if ($row["columnValue"] && $this->ci->country_language->fetch(["country_id" => $row["columnValue"], "language_id" => $language])) {
                        $affectedUserChanges[$key]["columnValue"] = $this->ci->country_language->get_field("name");
                    }
                    $this->ci->country_language->fetch(["country_id" => $row["columnRequestedValue"], "language_id" => $language]);
                    $affectedUserChanges[$key]["columnRequestedValue"] = $this->ci->country_language->get_field("name");
                    break;
                case "provider_group":
                    $this->ci->load->model("provider_group");
                    $oldPG = explode(", ", $row["columnValue"]);
                    $affectedUserChanges[$key]["columnValue"] = [];
                    foreach ($oldPG as $oPG) {
                        if ($oPG) {
                            $this->ci->provider_group->fetch($oPG);
                            $affectedUserChanges[$key]["columnValue"][] = $this->ci->provider_group->get_field("name");
                        }
                    }
                    $newPG = explode(", ", $row["columnRequestedValue"]);
                    $affectedUserChanges[$key]["columnRequestedValue"] = [];
                    foreach ($newPG as $nPG) {
                        if ($nPG) {
                            $this->ci->provider_group->fetch($nPG);
                            $affectedUserChanges[$key]["columnRequestedValue"][] = $this->ci->provider_group->get_field("name");
                        }
                    }
                    break;
                case "password":
                    $affectedUserChanges[$key]["columnValue"] = "***";
                    $affectedUserChanges[$key]["columnRequestedValue"] = "***";
                    break;
                case "banned":
                    $affectedUserChanges[$key]["columnValue"] = $affectedUserChanges[$key]["columnValue"] == "yes" ? $this->ci->lang->line("yes") : $this->ci->lang->line("no");
                    $affectedUserChanges[$key]["columnRequestedValue"] = $affectedUserChanges[$key]["columnRequestedValue"] == "yes" ? $this->ci->lang->line("yes") : $this->ci->lang->line("no");
                    break;
                case "isAd":
                    $affectedUserChanges[$key]["columnValue"] = $affectedUserChanges[$key]["columnValue"] == "0" ? $this->ci->lang->line("LocalDirectory") : ($affectedUserChanges[$key]["columnValue"] == "1" ? $this->ci->lang->line("ActiveDirectory") : "");
                    $affectedUserChanges[$key]["columnRequestedValue"] = $affectedUserChanges[$key]["columnRequestedValue"] == "0" ? $this->ci->lang->line("LocalDirectory") : ($affectedUserChanges[$key]["columnRequestedValue"] == "1" ? $this->ci->lang->line("ActiveDirectory") : "");
                    break;
            }
        }
        $response["data"] = $affectedUserChanges;
        return $response;
    }
    public function get_users_by_group_id($group_id)
    {
        $query = [];
        $query["select"] = ["id", false];
        $query["where"] = ["user_group_id", $group_id];
        return $this->load_list($query, ["key" => "id", "value" => "id"]);
    }
    public function load_available_list()
    {
        $query = ["select" => ["users.id, CONCAT( us.firstName, ' ', us.lastName ) as name", false], "join" => [["user_profiles us", "us.user_id = users.id", "left"]], "where" => [["user_group_id NOT IN (" . $this->systemAdministrationGroupId . ")", NULL, false], ["user_group_id != ", $this->superAdminInfosystaUserGroupId], ["us.status", "Active"]]];
        return $this->load_all($query);
    }
    public function load_active_emails()
    {
        $query = [];
        $query["select"] = ["email", false];
        $query["join"] = [["user_profiles", "user_profiles.user_id = users.id", "inner"]];
        $query["where"][] = ["user_profiles.status", "Active"];
        $query["where"][] = ["user_group_id != ", $this->superAdminInfosystaUserGroupId];
        $query["where"][] = ["user_group_id NOT IN (" . $this->systemAdministrationGroupId . ")", NULL, false];
        $result = $this->load_list($query, ["key" => "email", "value" => "email"]);
        return $result;
    }
    public function get_instance_value_by_key($key_name = "")
    {
        $table = $this->_table;
        $this->_table = "instance_data";
        $query = [];
        $query["select"] = ["keyValue", false];
        $query["where"][] = ["keyName", $key_name];
        $result = $this->load($query);
        $this->_table = $table;
        return $result["keyValue"];
    }
    public function validate_complexity_pass($password)
    {
        if (!preg_match_all("\$\\S*(?=\\S*[a-z])(?=\\S*[A-Z])(?=\\S*[\\d])(?=\\S*[\\W])\\S*\$", $password)) {
            return false;
        }
        return true;
    }
    public function relateToCC($newEmail, $oldEmail = false)
    {
        $this->ci->load->library("a4l_cc");
        return $this->ci->a4l_cc->relateInstanceEmail($newEmail, $oldEmail);
    }
    public function unique_login($field)
    {
        if (is_array($field) && 0 < count($field)) {
            $field = array_intersect_key($field, $this->_fields);
            $value = $field[key($field)];
            $system_preferences = $this->ci->session->userdata("systemPreferences");
            if ($value === NULL) {
                $this->ci->db->where("(username IS NULL or email IS NULL)", NULL, true);
            } else {
                if (key($field) == "username" && $system_preferences["loginWithoutDomain"]) {
                    $ad_username = 0 < strpos($value, "@" . $system_preferences["domain"]) ? mb_substr($value, 0, strpos($value, "@" . $system_preferences["domain"])) : $value;
                    if ($value != $ad_username) {
                        $this->ci->db->where("(username = '" . $value . "' OR email = '" . $value . "' OR username = '" . $ad_username . "')", NULL, true);
                    } else {
                        $this->ci->db->where("(username = '" . $value . "' OR email = '" . $value . "' OR (isAd = 1 AND SUBSTRING(username, 1, LOCATE('@" . $system_preferences["domain"] . "', username) - 1) = '" . $value . "'))", NULL, true);
                    }
                } else {
                    $this->ci->db->where("(username = '" . $value . "' OR email = '" . $value . "')", NULL, true);
                }
            }
            if (empty($this->ci->db->qb_where)) {
                return true;
            }
            if (!is_null($this->_fields[$this->_pk])) {
                $this->ci->db->where($this->_pk . " != '" . $this->_fields[$this->_pk] . "'");
            }
        }
        return $this->ci->db->count_all_results($this->_table) == 0;
    }
    public function get_user_id_by_name($name)
    {
        if (!isset($this->ci->user_profile)) {
            $this->ci->load->model("user_profile");
        }
        $response = $this->ci->user_profile->load(["select" => "user_id", "where" => ["CONCAT(firstName, ' ', lastName) = ", (string) $name]]);
        return isset($response["user_id"]) ? $response["user_id"] : NULL;
    }
    public function load_all_list()
    {
        $configQury = ["select" => ["users.id, CASE WHEN us.status='Inactive' THEN CONCAT( us.firstName, ' ', us.lastName,' (Inactive)' ) ELSE CONCAT( us.firstName, ' ', us.lastName )END as name", false], "join" => [["user_profiles us", "us.user_id = users.id", "left"]], "where" => [["user_group_id NOT IN (" . $this->systemAdministrationGroupId . ")", NULL, false], ["user_group_id != ", $this->superAdminInfosystaUserGroupId]]];
        return $this->load_list($configQury, ["key" => "id", "value" => "name"]);
    }
    public function get_name_by_id($user_id)
    {
        $table = $this->_table;
        $this->_table = "user_profiles as up";
        $query = ["select" => ["user_id as id,CONCAT( up.firstName, ' ', up.lastName ) as name", false], "where" => [["up.user_id", $user_id]]];
        $response = $this->load($query);
        $this->_table = $table;
        return $response;
    }
    public function get_username_conflicted_users()
    {
        $system_preferences = $this->ci->session->userdata("systemPreferences");
        $sql = "SELECT u1.id, u1.username AS local_directory_user, u1.email AS local_directory_user_email, u2.username AS active_directory_user FROM users u1 INNER JOIN users u2 ON u1.username = SUBSTRING(u2.username, 1, LOCATE('@" . $system_preferences["domain"] . "', u2.username) - 1) WHERE u2.isAd = 1";
        $conflicting_users = $this->ci->db->query($sql)->result_array();
        return $conflicting_users;
    }
    public function get_users_ids_by_group_id($user_groups)
    {
        $user_gruop_list = implode(", ", $user_groups);
        $ids_arr = $this->ci->db->query("select id from users where user_group_id in (" . $user_gruop_list . ") ")->result_array();
        $ids = [];
        foreach ($ids_arr as $id_item) {
            $ids[] = $id_item["id"];
        }
        return $ids;
    }
    public function set_users_whats_new_flag()
    {
        $users_id = $this->load_all(["select" => "id", "where" => [["username != ", $this->isAdminUser]]]);
        if (!empty($users_id)) {
            $sql = "DELETE FROM user_preferences where keyName = 'whats_new'";
            $this->ci->db->query($sql);
            $sql = "INSERT INTO user_preferences (user_id, keyName, keyValue) VALUES (" . implode(", 'whats_new', 'true'), (", array_column($users_id, "id")) . ", 'whats_new', 'true')";
            $this->ci->db->query($sql);
        }
    }
    public function load_all_users_by_group()
    {
        $query = ["select" => ["users.id, CONCAT( us.firstName, ' ', us.lastName ) as name, provider_groups_users.provider_group_id", false], "join" => [["user_profiles us", "us.user_id = users.id", "left"], ["provider_groups_users", " provider_groups_users.user_id = users.id", "inner"]], "where" => [["user_group_id NOT IN (" . $this->systemAdministrationGroupId . ")", NULL, false], ["user_group_id != ", $this->superAdminInfosystaUserGroupId], ["us.status", "Active"]]];
        return $this->load_all($query);
    }
    public function get_related_group_of_user($user_id)
    {
        $query = [];
        $query["select"] = ["distinct user_group_id", false];
        $query["where"] = ["users.id", $user_id];
        $result = $this->load_all($query);
        return array_column($result, "user_group_id");
    }
    public function get_related_group_of_users($user_ids)
    {
        $group_ids = [];
        if (isset($user_ids) && $user_ids) {
            foreach ($user_ids as $user_id) {
                $related_group = $this->get_related_group_of_user($user_id);
                if (isset($related_group) && $related_group && !in_array($related_group, $group_ids)) {
                    array_push($group_ids, $related_group);
                }
            }
        }
        return $group_ids;
    }
    public function insert_user_changes_authorization($mode, $affectedUserId, $newData, $oldData = [])
    {
        $return = true;
        $templateRequestFields = ["username", "email", "password", "isAd", "user_group_id", "provider_group", "gender", "title", "firstName", "lastName", "father", "mother", "employeeId", "dateOfBirth", "nationality", "department", "jobTitle", "isLawyer", "website", "phone", "fax", "mobile", "address1", "address2", "city", "state", "zip", "country", "comments", "user_code"];
        $columnsMultipleValues = ["provider_group"];
        $this->ci->load->model("user_changes_authorization", "user_changes_authorizationfactory");
        $this->ci->user_changes_authorization = $this->ci->user_changes_authorizationfactory->get_instance();
        if (isset($newData["provider_group"]) && !empty($newData["provider_group"]) && $newData["provider_group"]) {
            $newData["provider_group"] = implode(", ", $newData["provider_group"]);
        } else {
            unset($newData["provider_group"]);
        }
        if ($mode == "add") {
            $userChanges = [];
            foreach ($newData as $userField => $userValue) {
                if ($userField === "isLawyer" && $userValue == "no") {
                    unset($newData["isLawyer"]);
                }
                if (in_array($userField, $templateRequestFields) && $userValue) {
                    array_push($userChanges, ["changeType" => "add", "columnName" => $userField, "columnRequestedValue" => (string) $userValue, "columnStatus" => "Pending", "columnType" => in_array($userField, $columnsMultipleValues) ? "multiple" : "text", "affectedUserId" => $affectedUserId, "makerId" => $this->ci->session->userdata("AUTH_user_id"), "createdOn" => date("Y-m-d H:i:s")]);
                }
            }
            $this->ci->user_changes_authorization->insert_batch($userChanges);
        } else {
            $templateRequestFields[] = "banned";
            $templateRequestFields[] = "ban_reason";
            $templateRequestFields[] = "status";
            $templateRequestFields[] = "overridePrivacy";
            $this->ci->load->model("provider_group_user");
            $oldProviderGroups = $this->ci->provider_group_user->get_user_provider_groups($affectedUserId);
            $oldProviderGroups = array_keys($oldProviderGroups);
            if (!empty($oldProviderGroups)) {
                $oldData["provider_group"] = implode(", ", $oldProviderGroups);
                if (!isset($newData["provider_group"]) && $mode === "editForm") {
                    $newData["provider_group"] = "";
                }
            }
            $newChanges = array_diff_assoc($newData, $oldData);
            $userChanges = [];
            foreach ($newChanges as $userField => $userValue) {
                if (in_array($userField, $templateRequestFields)) {
                    array_push($userChanges, ["changeType" => "edit", "columnName" => $userField, "columnValue" => (string) $oldData[$userField], "columnRequestedValue" => (string) $userValue, "columnStatus" => "Pending", "columnType" => in_array($userField, $columnsMultipleValues) ? "multiple" : "text", "affectedUserId" => $affectedUserId, "makerId" => $this->ci->session->userdata("AUTH_user_id"), "createdOn" => date("Y-m-d H:i:s")]);
                }
            }
            if (!empty($userChanges)) {
                $this->ci->user_changes_authorization->insert_batch($userChanges);
                $this->ci->load->model("user_profile");
                $this->ci->user_profile->fetch(["user_id" => $affectedUserId]);
                $this->ci->user_profile->set_field("flagNeedApproval", "1");
                $this->ci->user_profile->update();
            } else {
                $return = false;
            }
        }
        $profileData = $this->ci->user_profile->get_profile_by_id($affectedUserId);
        $affectedUserName = $profileData["firstName"] . " " . $profileData["lastName"];
        $notificationData = ["status" => "unseen", "message" => sprintf($this->ci->lang->line("changes_user_need_aproval"), $affectedUserName)];
        $this->ci->load->model("notification", "notificationfactory");
        $this->notification = $this->ci->notificationfactory->get_instance();
        $this->ci->notification->notify_checkers_list($notificationData);
        return $return;
    }
    public function duplicate_user_to_contact()
    {
        $this->ci->load->model("contact_company_category");
        $this->ci->contact_company_category->fetch(["keyName" => "Internal"]);
        $contact_category_id = $this->ci->contact_company_category->get_field("id");
        $this->ci->load->model("contact", "contactfactory");
        $this->ci->contact = $this->ci->contactfactory->get_instance();
        $this->ci->contact->set_fields($this->ci->user_profile->get_fields());
        $this->ci->contact->set_field("id", NULL);
        $this->ci->contact->set_field("foreignFirstName", $this->ci->user_profile->get_field("foreign_first_name"));
        $this->ci->contact->set_field("foreignLastName", $this->ci->user_profile->get_field("foreign_last_name"));
        $this->ci->contact->set_field("contact_category_id", $contact_category_id);
        $this->ci->contact->set_field("createdOn", date("Y-m-d H:i:s"));
        $this->ci->contact->set_field("createdBy", $this->ci->is_auth->get_user_id());
        $this->ci->contact->set_field("lawyerForCompany", "no");
        $this->ci->contact->set_field("country_id", $this->ci->user_profile->get_field("country"));
        $this->ci->load->model("titles_language");
        $user_title = $this->ci->titles_language->load(["select" => ["title_id"], "where" => [["language_id", 1], ["name", $this->ci->user_profile->get_field("title")]]]);
        if (isset($user_title["title_id"])) {
            $this->ci->contact->set_field("title_id", $user_title["title_id"]);
        }
        $result = $this->ci->contact->insert();
        if ($result) {
            $this->ci->load->model("contact_emails");
            $this->ci->contact_emails->set_field("id", NULL);
            $this->ci->contact_emails->set_field("contact_id", $this->ci->contact->get_field("id"));
            $this->ci->contact_emails->set_field("email", $this->get_field("email"));
            $this->ci->contact_emails->insert();
            $this->ci->load->model("client");
            $this->ci->client->manage_money_accounts("contact", $this->ci->contact->get_field("id"), $this->ci->is_auth->get_user_id(), "", true);
            if ($this->ci->user_profile->get_field("nationality")) {
                $ContactNationalities["nationalities"] = ["contact_id" => $this->ci->contact->get_field("id"), "nationalities" => [$this->ci->user_profile->get_field("nationality")]];
                $this->ci->contact->insert_contact_nationalities($ContactNationalities);
            }
        }
    }
    private function add_azure_ad_column_if_enabled($select)
    {
        $this->ci->load->model("system_preference");
        $system_preference = $this->ci->system_preference->get_key_groups();
        $is_enabled_azure = $system_preference["AzureDirectory"]["AllowFeatureAzureAd"];
        return $is_enabled_azure ? $select . ", users.userDirectory" : $select;
    }
    public function load_manager_by_id($user_id)
    {
        $_table = $this->_table;
        $this->_table = "user_profiles AS manager";
        $query["select"] = ["manager.id, CONCAT(manager.firstName,' ',manager.lastName) as name"];
        $query["where"][] = ["manager.user_id", $user_id];
        $response = $this->load($query);
        $this->_table = $_table;
        return $response;
    }
    public function return_max_active_users($module)
    {
        $config = $this->ci->licensor->read_license_file($module);
        $key_prefix = APPNAME . "::" . $this->ci->inflector->humanize($module);
        if (isset($config) && isset($config[$key_prefix]["maxActiveUsers"])) {
            return $config[$key_prefix]["maxActiveUsers"];
        }
        return false;
    }
    public function load_users_license_details()
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
        return $data;
    }
    public function get_user_email_by_id($user_id)
    {
        return $this->load(["select" => ["email", false], "where" => ["id", $user_id]])["email"];
    }
    public function load_users_ids_and_groups_in_groups($groupsIds)
    {
        $groupsIds = implode(",", $groupsIds);
        $result = $this->ci->db->query("select id, user_group_id from users where user_group_id in (" . $groupsIds . ") \r\n        AND id != " . $this->ci->is_auth->get_user_id() . " AND user_group_id NOT IN (" . $this->systemAdministrationGroupId . ") \r\n        AND user_group_id != " . $this->superAdminInfosystaUserGroupId)->result_array();
        return $result;
    }
}
class mysqli_User extends mysql_User
{
}
class sqlsrv_User extends mysql_User
{
    public function load_users_list($providerGroupIds = "", $configList = [], $seniority_level_ids = "")
    {
        if (!$configList) {
            $configList = ["key" => "id", "value" => "name"];
        }
        $configQury = ["select" => ["users.id, ( us.firstName + ' ' + us.lastName ) as name", false], "join" => [["user_profiles us", "us.user_id = users.id", "left"]], "where" => [["user_group_id NOT IN (" . $this->systemAdministrationGroupId . ")", NULL, false], ["user_group_id != ", $this->superAdminInfosystaUserGroupId], ["us.status", "Active"]]];
        if ($providerGroupIds) {
            $configQury["join"][] = ["provider_groups_users", " provider_groups_users.user_id = users.id", "left"];
            if (!is_array($providerGroupIds)) {
                $this->ci->provider_group->fetch($providerGroupIds);
                $all_users_flag = $this->ci->provider_group->get_field("allUsers");
                if ($all_users_flag != 1) {
                    $configQury["where_in"][] = ["provider_groups_users.provider_group_id", $providerGroupIds];
                }
            } else {
                $configQury["where_in"][] = ["provider_groups_users.provider_group_id", $providerGroupIds];
            }
        }
        if ($seniority_level_ids) {
            $configQury["where_in"][] = ["us.seniority_level_id", $seniority_level_ids];
        }
        return $this->load_list($configQury, $configList);
    }
    public function load_all_users_list($configList = [])
    {
        if (!$configList) {
            $configList = ["key" => "id", "value" => "name"];
        }
        $configQury = ["select" => ["users.id, CASE WHEN us.status='Inactive' THEN ( us.firstName + ' ' + us.lastName + ' (Inactive)' ) ELSE ( us.firstName + ' ' + us.lastName ) END as name", false], "join" => [["user_profiles us", "us.user_id = users.id", "left"]], "where" => [["user_group_id NOT IN (" . $this->systemAdministrationGroupId . ")", NULL, false], ["user_group_id != ", $this->superAdminInfosystaUserGroupId]]];
        return $this->load_list($configQury, $configList);
    }
    public function get_users_list_by_ids($ids)
    {
        $configQury = ["select" => ["users.id, ( us.firstName + ' ' + us.lastName ) as name", false], "join" => [["user_profiles us", "us.user_id = users.id", "left"]], "where" => [["user_group_id NOT IN (" . $this->systemAdministrationGroupId . ")", NULL, false], ["user_group_id != ", $this->superAdminInfosystaUserGroupId], ["us.status", "Active"]]];
        $configQury["where_in"][] = ["users.id", $ids];
        return $this->load_list($configQury, ["key" => "id", "value" => "name"]);
    }
    public function lookup($term, $active)
    {
        $term = $this->ci->db->escape_like_str($term);
        $this->ci->load->model("provider_group");
        $possibleJoins = ["provider_groups_users" => ["provider_groups_users pgu", "pgu.user_id = users.id", "left"], "user_groups" => ["user_groups", "user_groups.id = users.user_group_id"]];
        $possibleSelect = ["user_groups" => ",user_groups.name as user_groups_name"];
        $configList = ["key" => "id", "value" => "name"];
        $configQury["select"] = ["DISTINCT users.id, users.user_group_id, users.email, user_profiles.status, user_profiles.gender, user_profiles.title, user_profiles.firstName, user_profiles.lastName, user_profiles.jobTitle, user_profiles.isLawyer,", false];
        $configQury["where"][] = ["(isnull(user_profiles.foreign_first_name, '')  + ' ' + isnull(user_profiles.foreign_last_name, '') LIKE '%" . $term . "%'\r\n        OR (user_profiles.firstName + ' ' + user_profiles.lastName) LIKE '%" . $term . "%')", NULL, false];
        $configQury["join"] = [["user_profiles", " user_profiles.user_id = users.id", "inner"]];
        if ($joins = $this->ci->input->get("join")) {
            foreach ($joins as $join) {
                if (array_key_exists($join, $possibleJoins)) {
                    $configQury["join"][] = $possibleJoins[$join];
                }
                if (array_key_exists($join, $possibleSelect)) {
                    $configQury["select"][0] .= $possibleSelect[$join];
                }
            }
            unset($join);
        }
        if ($moreFilters = $this->ci->input->get("more_filters")) {
            foreach ($moreFilters as $_field => $_term) {
                if ($_field == "excludedProviderGroupUsers") {
                    $configQury["where"][] = ["users.id not in  (select provider_groups_users.user_id from provider_groups_users where provider_groups_users.provider_group_id = " . $_term . ")"];
                } else {
                    if ($_field == "provider_group_id") {
                        if (isset($_term)) {
                            $this->ci->provider_group->fetch($_term);
                            $all_users_flag = $this->ci->provider_group->get_field("allUsers");
                            if ($all_users_flag != 1) {
                                $configQury["where"] = [[$_field, $_term]];
                            }
                        }
                    } else {
                        $configQury["where"] = [[$_field, $_term]];
                    }
                }
            }
            unset($_field);
            unset($_term);
        }
        $configQury["where"][] = ["user_group_id NOT IN (" . $this->get("systemAdministrationGroupId") . ")", NULL, false];
        $configQury["where"][] = ["user_group_id != ", $this->get("superAdminInfosystaUserGroupId")];
        if ($active == "active") {
            $configQury["where"][] = ["user_profiles.status", "Active"];
        }
        $configQury["order_by"] = ["user_profiles.firstName asc"];
        return $this->load_all($configQury, $configList);
    }
    public function get_user_groups($user_group_id)
    {
        $query = [];
        $query["select"] = ["users.*, ( user_profiles.title + ' ' + user_profiles.firstName + ' ' + user_profiles.lastName ) as user_full_name,user_profiles.status as userStatus, user_groups.name as user_group_name, user_groups.id as user_group_id", false];
        $query["join"] = [["user_profiles", " user_profiles.user_id = users.id", "inner"], ["user_groups", " user_groups.id = users.user_group_id", "inner"]];
        $query["where"] = ["users.user_group_id", $user_group_id];
        $query["order_by"] = ["users.id asc"];
        return $this->paginate($query, ["uri_segment" => 4]);
    }
    public function api_lookup($term, $systemAdministrationGroupId)
    {
        $term = $this->ci->db->escape_like_str($term);
        $configQury = ["select" => "users.id, users.user_group_id, users.email, user_profiles.status, user_profiles.gender, user_profiles.title, user_profiles.firstName, user_profiles.lastName, user_profiles.jobTitle, user_profiles.isLawyer"];
        if (!empty($term)) {
            $configQury["where"][] = ["(isnull(user_profiles.foreign_first_name, '')  + ' ' + isnull(user_profiles.foreign_last_name, '') LIKE '%" . $term . "%'\r\n            OR (user_profiles.firstName + ' ' + user_profiles.lastName) LIKE '%" . $term . "%')", NULL, false];
        }
        $configQury["join"] = [["user_profiles", " user_profiles.user_id = users.id", "inner"]];
        $configQury["where"][] = ["user_group_id != ", $this->get("superAdminInfosystaUserGroupId")];
        $configQury["where"][] = ["user_profiles.status != ", "Inactive"];
        $configQury["order_by"] = ["user_profiles.firstName asc"];
        if ($systemAdministrationGroupId != "") {
            $configQury["where"][] = ["user_group_id NOT IN (" . $systemAdministrationGroupId . ")", NULL, false];
        }
        return $this->load_all($configQury);
    }
    public function load_active_users($period)
    {
        $query = [];
        $query["select"] = ["users.id", false];
        $query["join"] = [["user_profiles", "user_profiles.user_id = users.id", "inner"]];
        $query["where"][] = ["user_profiles.status", "Active"];
        $query["where"][] = ["users.user_group_id > 1"];
        $query["where"][] = ["(GETDATE() > DATEADD(DAY, " . $period . ", users.last_login) )"];
        return $this->load_all($query);
    }
    public function deactivate_users($period = 0)
    {
        $usersToDeactivate = $this->load_active_users($period);
        $usersIDs = [];
        if (!empty($usersToDeactivate)) {
            foreach ($usersToDeactivate as $index => $user) {
                $usersIDs[] = $user["id"];
            }
            $this->_table = "user_profiles";
            $this->ci->db->set("user_profiles.status", "Inactive");
            $this->ci->db->where_in("user_profiles.user_id", $usersIDs);
            $this->ci->db->update($this->_table);
        }
        return true;
    }
    public function load_available_list()
    {
        $query = ["select" => ["users.id, ( us.firstName + ' ' + us.lastName ) as name", false], "join" => [["user_profiles us", "us.user_id = users.id", "left"]], "where" => [["user_group_id NOT IN (" . $this->systemAdministrationGroupId . ")", NULL, false], ["user_group_id != ", $this->superAdminInfosystaUserGroupId], ["us.status", "Active"]]];
        return $this->load_all($query);
    }
    public function get_user_id_by_name($name)
    {
        if (!isset($this->ci->user_profile)) {
            $this->ci->load->model("user_profile");
        }
        $response = $this->ci->user_profile->load(["select" => "user_id", "where" => ["(firstName + ' ' + lastName) = ", (string) $name]]);
        return isset($response["user_id"]) ? $response["user_id"] : NULL;
    }
    public function load_all_list()
    {
        $configQury = ["select" => ["users.id, CASE WHEN us.status='Inactive' THEN ( us.firstName + ' ' + us.lastName + ' (Inactive)' ) ELSE ( us.firstName + ' ' + us.lastName ) END as name", false], "join" => [["user_profiles us", "us.user_id = users.id", "left"]], "where" => [["user_group_id NOT IN (" . $this->systemAdministrationGroupId . ")", NULL, false], ["user_group_id != ", $this->superAdminInfosystaUserGroupId]]];
        return $this->load_list($configQury, ["key" => "id", "value" => "name"]);
    }
    public function get_username_conflicted_users()
    {
        $system_preferences = $this->ci->session->userdata("systemPreferences");
        $sql = "SELECT u1.id, u1.username AS local_directory_user, u1.email AS local_directory_user_email, u2.username active_directory_user FROM users u1 INNER JOIN users u2 ON u1.username = SUBSTRING(u2.username, 0, CHARINDEX('@" . $system_preferences["domain"] . "', u2.username)) WHERE u2.isAd = 1";
        $conflicting_users = $this->ci->db->query($sql)->result_array();
        return $conflicting_users;
    }
    public function unique_login($field)
    {
        if (is_array($field) && 0 < count($field)) {
            $field = array_intersect_key($field, $this->_fields);
            $value = $field[key($field)];
            $system_preferences = $this->ci->session->userdata("systemPreferences");
            if ($value === NULL) {
                $this->ci->db->where("(username IS NULL or email IS NULL)", NULL, true);
            } else {
                if (key($field) == "username" && $system_preferences["loginWithoutDomain"]) {
                    $ad_username = 0 < strpos($value, "@" . $system_preferences["domain"]) ? mb_substr($value, 0, strpos($value, "@" . $system_preferences["domain"])) : $value;
                    if ($value != $ad_username) {
                        $this->ci->db->where("(username = '" . $value . "' OR email = '" . $value . "' OR username = '" . $ad_username . "')", NULL, true);
                    } else {
                        $this->ci->db->where("(username = '" . $value . "' OR email = '" . $value . "' OR (isAd = 1 AND SUBSTRING(username, 0, CHARINDEX('@" . $system_preferences["domain"] . "', username)) = '" . $value . "'))", NULL, true);
                    }
                } else {
                    $this->ci->db->where("(username = '" . $value . "' OR email = '" . $value . "')", NULL, true);
                }
            }
            if (empty($this->ci->db->qb_where)) {
                return true;
            }
            if (!is_null($this->_fields[$this->_pk])) {
                $this->ci->db->where($this->_pk . " != '" . $this->_fields[$this->_pk] . "'");
            }
        }
        return $this->ci->db->count_all_results($this->_table) == 0;
    }
    public function get_name_by_id($user_id)
    {
        $table = $this->_table;
        $this->_table = "user_profiles as up";
        $query = ["select" => ["user_id as id,( up.firstName + ' ' + up.lastName ) as name", false], "where" => [["up.user_id", $user_id]]];
        $response = $this->load($query);
        $this->_table = $table;
        return $response;
    }
    public function load_all_users_by_group()
    {
        $query = ["select" => ["users.id, ( us.firstName + ' ' + us.lastName ) as name, provider_groups_users.provider_group_id", false], "join" => [["user_profiles us", "us.user_id = users.id", "left"], ["provider_groups_users", " provider_groups_users.user_id = users.id", "inner"]], "where" => [["user_group_id NOT IN (" . $this->systemAdministrationGroupId . ")", NULL, false], ["user_group_id != ", $this->superAdminInfosystaUserGroupId], ["us.status", "Active"]]];
        return $this->load_all($query);
    }
}

?>