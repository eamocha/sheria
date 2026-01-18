<?php
if(!defined("BASEPATH")){
	exit("No direct script access allowed");
}
class Milestone extends My_Model_Factory {
}
class mysql_Milestone extends My_Model {
	protected $modelName = "milestone";
	protected $_table = "contract_milestone";
	protected $builtInLogs = true;
	protected $_fieldsNames = ["id","contract_id","title","serial_number","deliverables","status","amount","currency_id","percentage","start_date","due_date","createdBy","createdOn","modifiedBy","modifiedOn","channel"];
	protected $allowedNulls = ["deliverables","status","serial_number","amount","currency_id","percentage","start_date","due_date","createdBy","createdOn","modifiedBy","modifiedOn"];
	public function __construct(){
		parent::__construct();
		$this->validate = ["title"=>["required"=>["required"=>true,"allowEmpty"=>false,"rule"=>["minLength",1],"message"=>$this->ci->lang->line("cannot_be_blank_rule")],"unique"=>["rule"=>["combinedUnique",["contract_id"]],"message"=>sprintf($this->ci->lang->line("is_unique_rule"), $this->ci->lang->line("contract"))]],"percentage"=>["required"=>false,"allowEmpty"=>true,"rule"=>"numeric","message"=>sprintf($this->ci->lang->line("is_numeric_rule"), $this->ci->lang->line("notify_before"))],"amount"=>["numeric"=>["required"=>false,"allowEmpty"=>true,"rule"=>"numeric","message"=>sprintf($this->ci->lang->line("is_numeric_rule"), $this->ci->lang->line("amount"))],"maxLength"=>["required"=>false,"allowEmpty"=>true,"rule"=>["maxLengthDecimal",13,2],"message"=>sprintf($this->ci->lang->line("decimal_allowed"))]],"due_date"=>["required"=>false,"allowEmpty"=>true,"rule"=>"date","message"=>sprintf($this->ci->lang->line("date_rule"), $this->ci->lang->line("due_date"))],"start_date"=>["required"=>false,"allowEmpty"=>true,"rule"=>"date","message"=>sprintf($this->ci->lang->line("date_rule"), $this->ci->lang->line("start_date"))]];
		}
	public function load_milestones_per_contract($contract_id = 0){
		$query = [];
		$query["select"] = ["Distinct contract_milestone.*, iso_currencies.code as currency, \r\n        (select count(*) from contract_milestone_documents left join documents_management_system as documents on \r\n        contract_milestone_documents.document_id = documents.id where contract_milestone_documents.milestone_id = contract_milestone.id) \r\n        as milestone_docs_count",false];
		$query["join"][] = ["contract_milestone_documents","contract_milestone.id = contract_milestone_documents.milestone_id","left"];
		$query["join"][] = ["iso_currencies","iso_currencies.id = contract_milestone.currency_id","left"];
		$query["where"] = ["contract_milestone.contract_id",$contract_id];
		$query["order_by"] = ["contract_milestone.start_date asc"];
		return $this->load_all($query);
	}
	public function load_milestone_data($milestone_id = 0){
		$query["select"] = ["contract_milestone.*"];
		$query["where"][] = ["contract_milestone.id",$milestone_id];
		return $this->load($query);
	}
	public function delete_milestone_documents($milestone_id){
		$result = false;
		$query = "Delete contract_milestone_documents From contract_milestone_documents left join\r\n        documents_management_system on documents_management_system.id = contract_milestone_documents.document_id\r\n        where contract_milestone_documents.milestone_id = ?";
		$documents_deleted = $this->ci->db->query($query, [$milestone_id]);
		if($documents_deleted){
			$query = "Delete From contract_milestone \r\n            where contract_milestone.id = ?";
			$result = $this->ci->db->query($query, [$milestone_id]);
		}
		return $result;
	}
	public function delete_contract_milestones($contract_id){
		$result = false;
		$query = "Delete contract_milestone_documents,documents_management_system From contract_milestone_documents left join\r\n        documents_management_system on documents_management_system.id = contract_milestone_documents.document_id\r\n        left join contract_milestone on contract_milestone.id = contract_milestone_documents.milestone_id\r\n        where contract_milestone.contract_id = ?";
		$documents_deleted = $this->ci->db->query($query, [$contract_id]);
		if($documents_deleted){
			$query = "Delete From contract_milestone \r\n            where contract_milestone.contract_id = ?";
			$result = $this->ci->db->query($query, [$contract_id]);
		}
		return $result;
	}
	public function change_status($milestone_id, $status){
		$this->ci->db->set(["status"=>$status], false);
		$this->ci->db->where("id", $milestone_id);
		return $this->ci->db->update("contract_milestone");
	}
	public function change_financial_status($milestone_id, $status){
		$this->ci->db->set(["financial_status"=>$status], false);
		$this->ci->db->where("id", $milestone_id);
		return $this->ci->db->update("contract_milestone");
	}
}
class mysqli_Milestone extends mysql_Milestone {
}
class sqlsrv_Milestone extends mysql_Milestone {
	public function load_milestones_per_contract($contract_id = 0){
		$query = [];
		$query["select"] = ["distinct contract_milestone.id,contract_milestone.contract_id,\r\n        contract_milestone.title, contract_milestone.serial_number, CAST(contract_milestone.deliverables as varchar(max)) as deliverables,\r\n        contract_milestone.status, contract_milestone.financial_status, contract_milestone.amount, contract_milestone.currency_id, \r\n        contract_milestone.percentage, contract_milestone.start_date,contract_milestone.due_date,iso_currencies.code as currency, \r\n        (select count(*) from contract_milestone_documents left join documents_management_system as documents on \r\n        contract_milestone_documents.document_id = documents.id where contract_milestone_documents.milestone_id = contract_milestone.id) \r\n        as milestone_docs_count",false];
		$query["join"][] = ["contract_milestone_documents","contract_milestone.id = contract_milestone_documents.milestone_id","left"];
		$query["join"][] = ["iso_currencies","iso_currencies.id = contract_milestone.currency_id","left"];
		$query["where"] = ["contract_milestone.contract_id",$contract_id];
		$query["order_by"] = ["contract_milestone.start_date asc"];
		return $this->load_all($query);
	}
	public function delete_milestone_documents($milestone_id){
		$result = false;
		$query = "Delete contract_milestone_documents From contract_milestone_documents\r\n            where contract_milestone_documents.milestone_id = ?";
		$documents_deleted = $this->ci->db->query($query, [$milestone_id]);
		if($documents_deleted){
			$query = "Delete From contract_milestone \r\n            where contract_milestone.id = ?";
			$result = $this->ci->db->query($query, [$milestone_id]);
		}
		return $result;
	}
	public function delete_contract_milestones($contract_id){
		$result = false;
		$query = "Select contract_milestone_documents.document_id from contract_milestone \r\n        inner join contract_milestone_documents on contract_milestone_documents.milestone_id = contract_milestone.id \r\n        where contract_milestone.contract_id = ?";
		$documents_ids = $this->ci->db->query($query, [$contract_id])->result_array();
		$query = "Delete contract_milestone_documents From contract_milestone_documents\r\n                left join contract_milestone on contract_milestone.id = contract_milestone_documents.milestone_id\r\n                where contract_milestone.contract_id = ?";
		$documents_deleted = $this->ci->db->query($query, [$contract_id]);
		if($documents_deleted){
			foreach($documents_ids as $document_id){
				$query = "Delete documents_management_system From documents_management_system\r\n                where id = ?";
				$this->ci->db->query($query, [$document_id["document_id"]]);
			}
			$query = "Delete from contract_milestone\r\n            where contract_milestone.contract_id = " . $contract_id;
			$result = $this->ci->db->query($query);
		}
		return $result;
	}
}
?>