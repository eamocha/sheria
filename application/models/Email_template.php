<?php
if (!defined("BASEPATH")) {
    exit("No direct script access allowed");
}


class Email_template extends My_Model_Factory
{

}

class mysql_Email_template extends My_Model
{
    protected $modelName = "email_template";
    protected $_table = "email_templates";
    protected $_listFieldName = "template_name";

    protected $_fieldsNames = [
        "id",
        "template_key",
        "template_name",
        "subject",
        "body_content",
        "is_active",
        "variable_count",
        "last_modified_by",
        "updated_at"
    ];

    public function __construct()
    {
        parent::__construct();

        $this->validate = [
            "template_key" => [
                "required" => true,
                "allowEmpty" => false,
                "rule" => ["maxLength", 100],
                "message" => sprintf($this->ci->lang->line("required__max_length_rule"), $this->ci->lang->line("template_key"), 100)
            ],
            "template_name" => [
                "required" => true,
                "allowEmpty" => false,
                "rule" => ["maxLength", 255],
                "message" => sprintf($this->ci->lang->line("required__max_length_rule"), $this->ci->lang->line("template_name"), 255)
            ],
            "body_content" => ["isRequired" => ["required" => true, "allowEmpty" => false, "rule" => ["minLength", 10], "message" => $this->ci->lang->line("cannot_be_blank_min_30_rule")], "maxLength" => ["rule" => ["maxLength", 1000], "message" => sprintf($this->ci->lang->line("max_characters"), 1000)]],


            "variable_count" => [
                "required" => true,
                "allowEmpty" => false,
                "rule" => "numeric",
                "message" => $this->ci->lang->line("variable_count_invalid")
            ],

            "subject" => [
                "allowEmpty" => true, // Make this optional if a subject isn't always needed
                "rule" => ["maxLength", 255],
                "message" => sprintf($this->ci->lang->line("max_length_rule"), $this->ci->lang->line("subject"), 255)
            ],
        ];


        $this->logged_user_id = $this->ci->is_auth->get_user_id();
        $this->override_privacy = $this->ci->is_auth->get_override_privacy();
    }
    public function load_by_key($template_key)
    {
       // return $this->load(["select" => ["template_key", false], "where" => ["template_key", $template_key]])["template_key"];
        $query = ["where" => ["template_key" ,$template_key]];
        return parent::load($query);
    }
    public function load_all_templates()
    {

        $query = [];
        $response = [];
        $query["select"] = [
            "email_templates.id, template_key, template_name, subject, is_active, variable_count, updated_at, 
             (modifiedUser.firstName + ' ' + modifiedUser.lastName) as last_modified_by_name",
            false
        ];
        $query["join"][] = ["user_profiles modifiedUser", "modifiedUser.user_id = email_templates.last_modified_by", "left"];
        $response = parent::load_all($query);

        return $response;
    }


}

class mysqli_Email_template extends mysql_Email_template
{

}

class sqlsrv_Email_template extends mysql_Email_template
{



}