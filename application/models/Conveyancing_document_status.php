<?php
class Conveyancing_document_status extends My_Model_Factory
{

}
class mysqli_Conveyancing_document_status extends My_model
{
    protected $_table = 'conveyancing_document_status';
    protected $modelName = "conveyancing_document_status";

    protected $_fieldsNames = [              
        'id',
        'name',
        'addedOn'
    ];
    protected $primaryKey = 'id';

    public function __construct()
    {
        $this->ci =& get_instance();
        parent::__construct();

    }
    public function load_list_statuses()
    {
        $language = $lang ?? strtolower(substr($this->ci->session->userdata("AUTH_language"), 0, 2));
        $query = ["select" => "id, name"];
        $config_list = ["key" => "id", "value" => "name", "firstLine" => ["" => $this->ci->lang->line("none")]];
        return $this->load_list($query, $config_list);
    }



}
class mysql_Conveyancing_document_status extends mysqli_Conveyancing_document_status
{

}
class sqlsrv_Conveyancing_document_status extends mysql_Conveyancing_document_status
{

}