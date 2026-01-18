<?php
if (!defined("BASEPATH")) {
    exit("No direct script access allowed");
}
class Correspondence_type extends My_Model_Factory
{
}
class mysqli_Correspondence_type extends My_Model
{
    protected $modelName = "correspondence_type";
    protected $_table = "correspondence_types";
      protected $builtInLogs = true;
    //protected $_listFieldName = "id";
    protected $_fieldsNames = ["id", "name","createdOn","modifiedOn","createdBy","modifiedBy"];
    public function __construct()
    {
        parent::__construct();
        $this->validate = [];
    }
    public function load_all_records()
    {

        $query = ["select" => "correspondence_types.*"];
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
        $query = ["select" => "correspondence_types.id, correspondence_types_languages.name as name", "join" => [["correspondence_types_languages", "correspondence_types_languages.correspondence_type_id = correspondence_types.id", "left"], ["languages", "languages.id = correspondence_types_languages.language_id", "left"]], "where" => [["languages.name", $language]]];
        return $this->load_all($query);
    }
}
class mysql_Correspondence_type extends mysqli_Correspondence_type
{
}
class sqlsrv_Correspondence_type extends mysqli_Correspondence_type
{
    public function insert_new_record()
    {
        $this->ci->db->simple_query("INSERT INTO correspondence_types DEFAULT VALUES");
        return $this->ci->db->insert_id();
    }
}

?>