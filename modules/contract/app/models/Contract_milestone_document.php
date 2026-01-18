<?php
if(!defined("BASEPATH")){
	exit("No direct script access allowed");
}
class Contract_milestone_document extends My_Model_Factory {
}
class mysqli_Contract_milestone_document extends My_Model {
	protected $modelName = "contract_milestone_document";
	protected $_table = "contract_milestone_documents";
	protected $_fieldsNames = ["id","document_id","milestone_id"];
	public function __construct(){
		parent::__construct();
        $this->validate = ["document_id" => ["required" => true, "allowEmpty" => false, "rule" => "numeric", "message" => $this->ci->lang->line("cannot_be_blank_rule")], "milestone_id" => ["required" => true, "allowEmpty" => false, "rule" => "numeric", "message" => $this->ci->lang->line("cannot_be_blank_rule")]];

        	}
	public function load_all_attachments($milestone_id){
		$query["select"] = ["contract_milestone_documents.id, documents.id AS documents_id , documents.type,documents.name, documents.extension , (case when (documents.type = \'file\') then concat(documents.name,\'.\',documents.extension) else name end) AS full_name"];
		$query["join"][] = ["documents_management_system as documents","contract_milestone_documents.document_id = documents.id"];
		$query["where"][] = ["contract_milestone_documents.milestone_id",$milestone_id];
		return $this->load_all($query);
	}
	public function delete_document($document_id){
		$this->ci->load->model("document_management_system", "document_management_systemfactory");
		$this->ci->document_management_system = $this->ci->document_management_systemfactory->get_instance();
		$this->ci->document_management_system->fetch($document_id);
		$parent_id = $this->ci->document_management_system->get_field("parent");
		$query = "Delete From contract_milestone_documents where document_id = ?";
		if($this->ci->db->query($query, [$document_id])){
			return $parent_id;
		}
		return false;
	}
	public function load_attachments_per_contract($milestone_id, $contract_id){
		$this->ci->db->select("id")->from("documents_management_system")->where("module_record_id", $contract_id, false)->where("system_document", "1", false);
		$sub_query = $this->ci->db->get_compiled_select();
		$query["select"] = [" documents.id AS documents_id",false];
		$query["join"][] = ["documents_management_system as documents","contract_milestone_documents.document_id = documents.id"];
		$query["where"][] = ["contract_milestone_documents.milestone_id",$milestone_id,false];
		$query["order_by"][] = ["contract_milestone_documents.id","ASC",false];
		$query["where_not_in"][] = ["documents.parent",$sub_query,false];
		$config_list = ["key"=>"documents_id","value"=>"documents_id"];
		return $this->load_list($query, $config_list);
	}
}
class mysql_Contract_milestone_document extends mysqli_Contract_milestone_document {
}
class sqlsrv_Contract_milestone_document extends mysqli_Contract_milestone_document {
	public function load_all_attachments($milestone_id){
		$query["select"] = ["contract_milestone_documents.id , documents.id AS documents_id , (case when (documents.type = \'file\') then (documents.name + \'.\' + documents.extension) else name end) AS full_name"];
		$query["join"][] = ["documents_management_system as documents","contract_milestone_documents.document_id = documents.id"];
		$query["where"][] = ["contract_milestone_documents.milestone_id",$milestone_id];
		return $this->load_all($query);
	}
}
?>