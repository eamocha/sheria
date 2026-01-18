<?php
class Conveyancing_document_type extends My_Model_Factory
{

}
class mysqli_Conveyancing_document_type extends My_model
{
    protected $_table = 'conveyancing_document_type';
    protected $modelName = "conveyancing_document_type";

    protected $_fieldsNames = [              
        'id',
        'name',
        'createdOn',
        'createdBy',
        'modifiedOn',
        'modifiedBy'
    ];
    protected $primaryKey = 'id';

    public function __construct()
    {
        $this->ci =& get_instance();
        parent::__construct();

    }
    public function load_list_types()
    {
        $language = $lang ?? strtolower(substr($this->ci->session->userdata("AUTH_language"), 0, 2));
        $query = ["select" => "id, name"];
        $config_list = ["key" => "id", "value" => "name", "firstLine" => ["" => $this->ci->lang->line("none")]];
        return $this->load_list($query, $config_list);
    }




}
class mysql_Conveyancing_document_type extends mysqli_Conveyancing_document_type
{

}
class sqlsrv_Conveyancing_document_type extends mysql_Conveyancing_document_type
{

}