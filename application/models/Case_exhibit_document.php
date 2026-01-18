<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Case_exhibit_document extends My_Model_Factory {}

class mysql_Case_exhibit_document extends My_Model {
    protected $_table = 'case_exhibit_document';
    protected $primaryKey = 'id';
    protected $modelName = 'case_exhibit_document';
    protected $_fieldsNames = ['id', 'exhibit_id', 'document'];

    protected $ci = null;

    public function __construct() {
        parent::__construct();
        $this->ci =& get_instance();

        $this->validate = [
            [
                'field' => 'exhibit_id',
                'label' => 'Exhibit ID',
                'rules' => 'required|integer'
            ],
            [
                'field' => 'document',
                'label' => 'Document',
                'rules' => 'required|integer'
            ]
        ];
    }
}

class sqlsrv_Case_exhibit_document extends mysql_Case_exhibit_document {}
class mysqli_Case_exhibit_document extends mysql_Case_exhibit_document {}
