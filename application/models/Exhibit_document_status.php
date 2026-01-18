<?php

if (!defined("BASEPATH")) {
    exit("No direct script access allowed");
}

class Exhibit_document_status extends My_Model
{
    protected $modelName = "exhibit_document_status";
    protected $_table = "exhibit_document_statuses";
    protected $_listFieldName = "name";
    protected $_fieldsNames = ["id", "name"];

    public function __construct()
    {
        parent::__construct();
        $this->validate = [
            "name" => [
                "required" => true,
                "allowEmpty" => false,
                "rule" => ["maxLength", 100],
                "message" => sprintf(
                    $this->ci->lang->line("required__max_length_rule"),
                    $this->ci->lang->line("name"),
                    100
                )
            ]
        ];
    }
}