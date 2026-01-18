<?php



if (!defined("BASEPATH")) {
    exit("No direct script access allowed");
}
class Opinion_type extends My_Model_Factory
{
}
class mysqli_Opinion_type extends My_Model
{
    protected $modelName = "opinion_type";
    protected $_table = "opinion_types";
    protected $_listFieldName = "id";
    protected $_fieldsNames = ["id"];
    public function __construct()
    {
        parent::__construct();
        $this->validate = [];
    }
    public function load_all_records()
    {
        $this->ci->load->model("language");
        $lang_id = $this->ci->language->get_id_by_session_lang();
        $query = ["select" => "opinion_types.*, opinion_types_languages.name as name, opinion_types_languages.applies_to as applies_to,languages.name as langName,order_table.name as orderedName", "join" => [["opinion_types_languages", "opinion_types_languages.opinion_type_id = opinion_types.id", "left"], ["languages", "languages.id = opinion_types_languages.language_id", "left"], ["opinion_types_languages as order_table", "order_table.opinion_type_id = opinion_types.id and order_table.language_id = '" . $lang_id . "'", "left"]], "order_by" => ["orderedName", "asc"]];
        return $this->load_all($query);
    }
    public function load_list_per_language()
    {
        $language = strtolower(substr($this->ci->session->userdata("AUTH_language"), 0, 2));
        $query = ["select" => "opinion_types.id, opinion_types_languages.name as name", "join" => [["opinion_types_languages", "opinion_types_languages.opinion_type_id = opinion_types.id", "left"], ["languages", "languages.id = opinion_types_languages.language_id", "left"]], "where" => ["languages.name", $language]];
        $configList = ["key" => "id", "value" => "name", "firstLine" => ["" => $this->ci->lang->line("choose_type")]];
        return $this->load_list($query, $configList);
    }
    public function load_all_per_language()
    {
        $language = strtolower(substr($this->ci->session->userdata("AUTH_language"), 0, 2));
        $query = ["select" => "opinion_types.id, opinion_types_languages.name as name", "join" => [["opinion_types_languages", "opinion_types_languages.opinion_type_id = opinion_types.id", "left"], ["languages", "languages.id = opinion_types_languages.language_id", "left"]], "where" => ["languages.name", $language]];
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
        $query = ["select" => "opinion_types.id, opinion_types_languages.name as name", "join" => [["opinion_types_languages", "opinion_types_languages.opinion_type_id = opinion_types.id", "left"], ["languages", "languages.id = opinion_types_languages.language_id", "left"]], "where" => [["languages.name", $language]]];
        return $this->load_all($query);
    }
}
class mysql_Opinion_type extends mysqli_Opinion_type
{
}
class sqlsrv_Opinion_type extends mysqli_Opinion_type
{
    public function insert_new_record()
    {
        $this->ci->db->simple_query("INSERT INTO opinion_types DEFAULT VALUES");
        return $this->ci->db->insert_id();
    }
}

?>