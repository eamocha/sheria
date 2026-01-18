<?php

if (!defined("BASEPATH")) {
    exit("No direct script access allowed");
}
class Opinion_document_type extends My_Model
{
    protected $modelName = "opinion_document_type";
    protected $_table = "opinion_document_type";
    protected $_listFieldName = "";
    protected $_fieldsNames = ["id"];
    public function __construct()
    {
        parent::__construct();
        $this->validate = [];
    }
}

?>