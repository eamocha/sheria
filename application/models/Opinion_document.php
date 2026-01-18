<?php

if (!defined("BASEPATH")) {
    exit("No direct script access allowed");
}
class Opinion_document extends My_Model
{
    protected $modelName = "opinion_document";
    protected $_table = "opinions_documents";
    protected $_listFieldName = "";
    protected $_fieldsNames = ["id", "opinion_id", "document_id"];
    public function __construct()
    {
        parent::__construct();
        $this->validate = ["opinion_id" => ["required" => true, "allowEmpty" => false, "rule" => "numeric", "message" => sprintf($this->ci->lang->line("is_numeric_rule"), $this->ci->lang->line("opinion"))], "document_id" => ["required" => true, "allowEmpty" => false, "rule" => "numeric", "message" => sprintf($this->ci->lang->line("is_numeric_rule"), $this->ci->lang->line("document"))]];
    }
    public function get_document_by_opinion_id($opinion_id)
    {
        $query["select"] = ["opinions_documents.id , opinions_documents.opinion_id, opinions_documents.document_id"];
        $query["where"][] = ["opinions_documents.opinion_id", $opinion_id];
        return $this->load_all($query);
    }
    public function delete_opinion_document($opinion_id)
    {
        $query["where"] = ["opinions_documents.opinion_id", $opinion_id];
        return $this->delete($query);
    }
}

?>