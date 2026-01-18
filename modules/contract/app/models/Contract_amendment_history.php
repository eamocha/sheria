<?php

if (!defined("BASEPATH")) {
    exit("No direct script access allowed");
}
class Contract_amendment_history extends My_Model_Factory
{
}
class mysqli_Contract_amendment_history extends My_Model
{
    protected $modelName = "contract_amendment_history";
    protected $_table = "contract_amendment_history";
    protected $_listFieldName = "name";
    protected $_fieldsNames = ["id", "amended_on", "amended_by", "comment", "contract_id", "amended_id","amendment_document_id","amendment_approval_status"];
    public function __construct()
    {
        parent::__construct();
        $this->validate = ["amended_on" => ["required" => false, "allowEmpty" => true, "rule" => $this->custom_validation_rules["date"], "message" => sprintf($this->ci->lang->line("required_date_rule"), $this->ci->lang->line("amended_on"))], "amended_by" => ["required" => true, "allowEmpty" => false, "rule" => "numeric", "message" => $this->ci->lang->line("cannot_be_blank_rule")], "contract_id" => ["required" => true, "allowEmpty" => false, "rule" => "numeric", "message" => $this->ci->lang->line("cannot_be_blank_rule")], "amended_id" => ["required" => true, "allowEmpty" => false, "rule" => "numeric", "message" => $this->ci->lang->line("cannot_be_blank_rule")], "comment" => ["required" => ["required" => true, "allowEmpty" => false, "rule" => ["minLength", 1], "message" => $this->ci->lang->line("cannot_be_blank_rule")]]];
    }
    public function load_history($contract_id)
    {
        $query = ["select" => "contract_amendment_history.id,contract_amendment_history.amended_id, contract_amendment_history.amended_on, contract_amendment_history.amendment_approval_status, old_contract.contract_date as previous_end_date, new_contract.contract_date as new_contract_date, new_contract.end_date as new_end_date,\r\n          
           concat( up.firstName, ' ', up.lastName )  as amended_by, contract_amendment_history.comment", "join" => [["user_profiles as up", "up.user_id = contract_amendment_history.amended_by", "left"], ["contract as old_contract", "old_contract.id = contract_amendment_history.contract_id", "left"], ["contract as new_contract", "new_contract.id = contract_amendment_history.amended_id", "left"]], "where" => ["contract_amendment_history.contract_id", $contract_id], "order_by" => ["contract_amendment_history.amended_on desc"]];
        return parent::load_all($query);
    }
}
class mysql_Contract_amendment_history extends mysqli_Contract_amendment_history
{
}
class sqlsrv_Contract_amendment_history extends mysqli_Contract_amendment_history
{
    public function load_history($contract_id)
    {
        $query = ["select" => "contract_amendment_history.id,contract_amendment_history.amended_id, contract_amendment_history.amendment_approval_status, contract_amendment_history.amended_on, old_contract.contract_date as previous_end_date, new_contract.contract_date as new_contract_date, new_contract.end_date as new_end_date,\r\n            ( up.firstName + ' ' + up.lastName )  as amended_by, contract_amendment_history.comment", "join" => [["user_profiles as up", "up.user_id = contract_amendment_history.amended_by", "left"], ["contract as old_contract", "old_contract.id = contract_amendment_history.contract_id", "left"], ["contract as new_contract", "new_contract.id = contract_amendment_history.amended_id", "left"]], "where" => ["contract_amendment_history.contract_id", $contract_id], "order_by" => ["contract_amendment_history.amended_on desc"]];
        return parent::load_all($query);
    }
}

?>