<?php

if (!defined("BASEPATH")) {
    exit("No direct script access allowed");
}
class Opinion_document_status extends My_Model
{
    protected $modelName = "opinion_document_status";
    protected $_table = "opinion_document_status";
    protected $_listFieldName = "id";
    protected $_fieldsNames = ["id"];
    public function __construct()
    {
        parent::__construct();
        $this->validate = [];
    }
}

?>