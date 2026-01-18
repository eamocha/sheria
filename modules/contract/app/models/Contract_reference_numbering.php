<?php

if (!defined("BASEPATH")) {
    exit("No direct script access allowed");
}
class Contract_reference_numbering extends My_Model_Factory
{
}
class mysqli_Contract_reference_numbering extends My_Model
{
    protected $modelName = "contract_reference_numbering";
    protected $_table = "contract_numbering_formats";
    protected $_listFieldName = "name";
    protected $_fieldsNames = [
        "id",
        "name",
        "description",
        "pattern",
        "example",
        "prefix",
        "suffix",
        "fixed_code",
        "sequence_reset",
        "sequence_length",
        "is_active",
        "last_sequence",
        "last_reset_date",

    ];

    public function __construct()
    {
        parent::__construct();

        $this->validate = [
            "name" => [
                "required" => [
                    "required" => true,
                    "allowEmpty" => false,
                    "rule" => ["minLength", 1],
                    "message" => $this->ci->lang->line("cannot_be_blank_rule")
                ],
                "maxLength" => [
                    "rule" => ["maxLength", 100],
                    "message" => sprintf($this->ci->lang->line("max_characters"), 100)
                ]
            ],
            "pattern" => [
                "required" => [
                    "required" => true,
                    "allowEmpty" => false,
                    "rule" => ["minLength", 1],
                    "message" => $this->ci->lang->line("cannot_be_blank_rule")
                ],
                "maxLength" => [
                    "rule" => ["maxLength", 100],
                    "message" => sprintf($this->ci->lang->line("max_characters"), 100)
                ]
            ],
            "example" => [
                "required" => [
                    "required" => true,
                    "allowEmpty" => false,
                    "rule" => ["minLength", 1],
                    "message" => $this->ci->lang->line("cannot_be_blank_rule")
                ]
            ],
            "sequence_length" => [
                "rule" => "numeric",
                "message" => $this->ci->lang->line("must_be_numeric_rule")
            ]
        ];
    }
}
class mysql_Contract_reference_numbering extends mysqli_Contract_reference_numbering
{
}
class sqlsrv_Contract_reference_numbering extends mysqli_Contract_reference_numbering
{

}

?>