<?php
if (!defined("BASEPATH")) {
    exit("No direct script access allowed");
}
class Correspondence_status extends My_Model_Factory
{
}
class mysqli_Correspondence_status extends My_Model
{
    protected $modelName = "correspondence_status";
    protected $_table = "correspondence_statuses";
    protected $_primaryKey = "id";
    protected $builtInLogs = true;
    protected $_fieldsNames = ["id","name"];
    public function __construct()
    {
        parent::__construct();
        $this->validate = [];
    }
    public function load_all_records()
    {

        $query = ["select" => "correspondence_statuses.*"];
        return $this->load_all($query);
    }

    public function insert_new_record()
    {
        $data = ["id" => NULL];
        $this->ci->db->insert($this->_table, $data);
        if (0 < $this->ci->db->affected_rows()) {
            return $this->ci->db->insert_id();
        }
        return 0;
    }
    public function api_load_list_per_language($language)
    {
        $query = ["select" => "correspondence_statuses.id, correspondence_statuses_languages.name as name", "join" => [["correspondence_statuses_languages", "correspondence_statuses_languages.correspondence_type_id = correspondence_statuses.id", "left"], ["languages", "languages.id = correspondence_statuses_languages.language_id", "left"]], "where" => [["languages.name", $language]]];
        return $this->load_all($query);
    }
}
class mysql_Correspondence_status extends mysqli_Correspondence_status
{
}
class sqlsrv_Correspondence_status extends mysqli_Correspondence_status
{
    public function insert_new_record()
    {
        $this->ci->db->simple_query("INSERT INTO correspondence_statuses DEFAULT VALUES");
        return $this->ci->db->insert_id();
    }
}

?>