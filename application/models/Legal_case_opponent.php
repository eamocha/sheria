<?php

if (!defined("BASEPATH")) {
    exit("No direct script access allowed");
}
class Legal_case_opponent extends My_Model_Factory
{
}
class mysqli_Legal_case_opponent extends My_Model
{
    protected $modelName = "legal_case_opponent";
    protected $_table = "legal_case_opponents";
    protected $_listFieldName = "customName";
    protected $_fieldsNames = ["id", "case_id", "opponent_id", "opponent_member_type", "opponent_position"];
    public function __construct()
    {
        parent::__construct();
        $this->validate = ["case_id" => ["required" => true, "allowEmpty" => false, "rule" => "numeric", "message" => sprintf($this->ci->lang->line("is_numeric_rule"), $this->ci->lang->line("litigation_case"))], "opponent_id" => ["required" => ["required" => true, "allowEmpty" => false, "rule" => "numeric", "message" => sprintf($this->ci->lang->line("is_numeric_rule"), $this->ci->lang->line("opponents"))], "unique" => ["rule" => ["combinedUnique", ["case_id"]], "message" => sprintf($this->ci->lang->line("fields_must_be_unique_rule"), $this->ci->lang->line("case"), "case and opponent")]], "opponent_member_type" => ["required" => true, "allowEmpty" => false, "rule" => "numeric", "message" => sprintf($this->ci->lang->line("required__max_length_rule"), $this->ci->lang->line("opponent_member_type"), 255)]];
    }
    public function fetch_case_opponents_data($case_id, $lang_code = false)
    {
        if (!$case_id) {
            return false;
        }
        $this->ci->load->model("language");
        $lang_id = $this->ci->language->get_id_by_session_lang($lang_code);
        $query = [];
        $query["select"] = ["legal_case_opponents.opponent_id, legal_case_opponents.opponent_member_type AS opponent_member_type, legal_case_opponents.opponent_position, legal_case_opponent_position_languages.name as position_name, (CASE WHEN legal_case_opponents.opponent_member_type = 'company' THEN comp.name ELSE CASE WHEN con.father!=' ' THEN concat_ws(' ',con.firstName,con.father,con.lastName) ELSE concat_ws(' ',con.firstName,con.lastName) END END ) AS opponentName,(CASE WHEN legal_case_opponents.opponent_member_type = 'company' THEN comp.foreignName ELSE concat_ws(' ',con.foreignFirstName,con.foreignLastName) END ) AS opponentForeignName,(CASE WHEN legal_case_opponents.opponent_member_type = 'company' THEN comp.category ELSE '' END ) AS opponentCompanyCategory,(CASE WHEN legal_case_opponents.opponent_member_type = 'company' THEN opponents.company_id ELSE opponents.contact_id END ) AS opponent_member_id,", false];
        $query["join"] = [["opponents", "opponents.id = legal_case_opponents.opponent_id", "left"], ["companies comp", "comp.id = opponents.company_id and legal_case_opponents.opponent_member_type = 'company'", "left"], ["contacts con", "con.id = opponents.contact_id and legal_case_opponents.opponent_member_type = 'contact'", "left"], ["legal_case_opponent_position_languages", "legal_case_opponent_position_languages.legal_case_opponent_position_id = legal_case_opponents.opponent_position and legal_case_opponent_position_languages.language_id = '" . $lang_id . "'", "left"]];
        $query["where"] = ["legal_case_opponents.case_id", $case_id];
        return $this->load_all($query);
    }
    public function delete_case_opponents($case_id)
    {
        $this->ci->db->where("case_id", $case_id);
        return $this->ci->db->delete($this->_table);
    }
    public function insert_case_opponents($case_id, $opponentsData)
    {
        if ($this->delete_case_opponents($case_id)) {
            $this->insert_on_duplicate_update_batch($opponentsData, ["case_id", "opponent_id", "opponent_member_type", "opponent_position"]);
            return true;
        }
        return false;
    }
    public function insert_case_opponent($opponentsData)
    {
        $this->insert_on_duplicate_update_batch($opponentsData, ["case_id", "opponent_id", "opponent_member_type", "opponent_position"]);
        return true;
    }
    public function delete_case_opponent($case_id, $opponent_id, $opponent_member_type)
    {
        $this->ci->db->where("case_id", $case_id)->where("opponent_id", $opponent_id)->where("opponent_member_type", $opponent_member_type);
        return $this->ci->db->delete($this->_table);
    }
}
class mysql_Legal_case_opponent extends mysqli_Legal_case_opponent
{
}
class sqlsrv_Legal_case_opponent extends mysqli_Legal_case_opponent
{
    public function fetch_case_opponents_data($case_id, $lang_code = false)
    {
        if (!$case_id) {
            return false;
        }
        $this->ci->load->model("language");
        $lang_id = $this->ci->language->get_id_by_session_lang($lang_code);
        $query = [];
        $query["select"] = ["legal_case_opponents.opponent_id, legal_case_opponents.opponent_member_type AS opponent_member_type, legal_case_opponents.opponent_position, legal_case_opponent_position_languages.name as position_name, (CASE WHEN legal_case_opponents.opponent_member_type = 'company' THEN comp.name ELSE (CASE WHEN con.father!='' THEN con.firstName + ' '+ con.father + ' ' + con.lastName ELSE con.firstName+' '+con.lastName END) END ) AS opponentName,(CASE WHEN legal_case_opponents.opponent_member_type = 'company' THEN comp.foreignName ELSE con.foreignFirstName + ' ' + con.foreignLastName END ) AS opponentForeignName,(CASE WHEN legal_case_opponents.opponent_member_type = 'company' THEN comp.category ELSE '' END ) AS opponentCompanyCategory,(CASE WHEN legal_case_opponents.opponent_member_type = 'company' THEN opponents.company_id ELSE opponents.contact_id END ) AS opponent_member_id,", false];
        $query["join"] = [["opponents", "opponents.id = legal_case_opponents.opponent_id", "left"], ["companies comp", "comp.id = opponents.company_id and legal_case_opponents.opponent_member_type = 'company'", "left"], ["contacts con", "con.id = opponents.contact_id and legal_case_opponents.opponent_member_type = 'contact'", "left"], ["legal_case_opponent_position_languages", "legal_case_opponent_position_languages.legal_case_opponent_position_id = legal_case_opponents.opponent_position and legal_case_opponent_position_languages.language_id = '" . $lang_id . "'", "left"]];
        $query["where"] = ["legal_case_opponents.case_id", $case_id];
        return $this->load_all($query);
    }
}

?>