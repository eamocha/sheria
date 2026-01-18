<?php

if (!defined("BASEPATH")) {
    exit("No direct script access allowed");
}

class Suspect_arrest extends My_Model_Factory
{
}

class mysql_Suspect_arrest extends My_Model
{
    protected $modelName = "Suspect_arrest";
    protected $modelCode = "SA";
    protected $_table = "suspect_arrest";
    protected $_listFieldName = "case_id";
    protected $_fieldsNames = [
        "id",
        "case_id",
        "arrest_date",
        "arrested_contact_id",
        "arrested_gender",
        "arrested_age",
        "arrest_police_station",
        "arrest_ob_number",
        "arrest_case_file_number",
        "arrest_attachments",
        "arrest_remarks",
        "createdBy",
        "createdOn",
        "modifiedBy",
        "modifiedOn",
        "arrest_location",
        "bail_status"
    ];
    protected $allowedNulls = [
            "arrested_age",
        "arrest_case_file_number",
        "arrest_attachments",
        "arrest_remarks",
        "createdBy",
        "createdOn",
        "modifiedBy",
        "modifiedOn"
    ];
    protected $validate = [];

    public function __construct()
    {
        parent::__construct();
       $this->validate = [
            "case_id" => [
                "isRequired" => [
                    "required" => true,
                    "allowEmpty" => false,
                    "rule" => "numeric",
                    "message" => "Case ID is required and must be numeric."
                ]
            ],
           "arrest_date" => ["isRequired" =>
               ["required" => true, "allowEmpty" => false, "rule" => ["minLength", 1],
                   "message" => $this->ci->lang->line("cannot_be_blank_rule")],
               "date" => ["required" => false, "allowEmpty" => true, "rule" => $this->custom_validation_rules["date"],
               "message" => sprintf($this->ci->lang->line("required_date_rule"), $this->ci->lang->line("arrival_date"))]],

            "arrested_contact_id" => [
                "isRequired" => [
                    "required" => true,
                    "allowEmpty" => false,
                    "rule" => "numeric",
                    "message" => "Arrested contact is required and must be valid contact."
                ]
            ],
            "arrested_gender" => [
                "isRequired" => [
                    "required" => true,
                    "allowEmpty" => false,
                    "rule" => ["inList", ["male", "female", "other", ""]],
                    "message" => "Gender must be male, female, other, or empty."
                ]
            ],
            "arrested_age" => [
                "isRequired" => [
                    "required" => false,
                    "allowEmpty" => true,
                    "rule" => "numeric",
                    "message" => "Age must be numeric."
                ]
            ],
            "arrest_police_station" => [
                "isRequired" => [
                    "required" => true,
                    "allowEmpty" => false,
                    "rule" => ["minLength", 1],
                    "message" => "Police station is required."
                ],
                "maxLength" => [
                    "rule" => ["maxLength", 255],
                    "message" => "Police station must be less than 255 characters."
                ]
            ],
           "arrest_ob_number" => [
               "isRequired" => [
                   "required" => true,
                   "allowEmpty" => false,
                   "rule" => ["minLength", 1],
                   "message" => "OB Number is required."
               ],
               "maxLength" => [
                   "rule" => ["maxLength", 100],
                   "message" => "OB Number must be less than 100 characters."
               ]
           ],
           "arrest_location" => [
               "isRequired" => [
                   "required" => true,
                   "allowEmpty" => false,
                   "rule" => ["minLength", 1],
                   "message" => "Location of arrest is required."
               ],
               "maxLength" => [
                   "rule" => ["maxLength", 100],
                   "message" => "Location name must be less than 100 characters."
               ]
           ],
            "arrest_case_file_number" => [
                "maxLength" => [
                    "rule" => ["maxLength", 100],
                    "message" => "Case File Number must be less than 100 characters."
                ]
            ],
            "arrest_remarks" => [
                "maxLength" => [
                    "rule" => ["maxLength", 1000],
                    "message" => "Remarks must be less than 1000 characters."
                ]
            ]
        ];
    }
    public function get_arrest_details_by_case_id($case_id)
    {
        $query = [];
        $query['select'] = [
            "suspect_arrest.*,
            CONCAT( c.firstName, ' ',  c.lastName ) as arrested_name
        "
        ];

        $query['join'] = [
            ["contacts c", "c.id = suspect_arrest.arrested_contact_id", "left"]
        ];

        $query['where'][] = ["suspect_arrest.case_id", $case_id];

        return $this->load_all($query);
    }
}

class mysqli_Suspect_arrest extends mysql_Suspect_arrest
{
}

class sqlsrv_Suspect_arrest extends mysql_Suspect_arrest
{
}