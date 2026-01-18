<?php
if (!defined("BASEPATH")) {
    exit("No direct script access allowed");
}
class Correspondence_relationships extends My_Model_Factory
{
}
class mysqli_Correspondence_relationships extends My_Model
{
    protected $modelName = "correspondence_relationships";
    protected $_table = "correspondence_relationships";
    //protected $_listFieldName = "id";
    protected $_fieldsNames = ["id", "correspondence_id1", "correspondence_id2","createdOn","createdBy","comments"];
    public function __construct()
    {
        parent::__construct();
        $this->validate = [];
    }
    public function load_all_records()
    {

        $query = ["select" => "correspondence_relationships.*"];
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
        $query = ["select" => "correspondence_relationships.id, correspondence_relationships_languages.name as name", "join" => [["correspondence_relationships_languages", "correspondence_relationships_languages.correspondence_type_id = correspondence_relationships.id", "left"], ["languages", "languages.id = correspondence_relationships_languages.language_id", "left"]], "where" => [["languages.name", $language]]];
        return $this->load_all($query);
    }
}
class mysql_Correspondence_relationships extends mysqli_Correspondence_relationships
{
}
class sqlsrv_Correspondence_relationships extends mysqli_Correspondence_relationships
{
    public function insert_new_record()
    {
        $this->ci->db->simple_query("INSERT INTO correspondence_relationships DEFAULT VALUES");
        return $this->ci->db->insert_id();
    }
}

?>