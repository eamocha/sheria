<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Contract_amendment_history_details extends My_Model_Factory
{
}
class mysqli_Contract_amendment_history_details extends My_Model
{
    protected $modelName = 'contract_amendment_history_details';
    protected $_table = 'contract_amendment_history_details';
    protected $_listFieldName = 'field_name';
    protected $_fieldsNames = ['id', 'amendment_history_id', 'contract_id', 'field_name', 'old_value', 'new_value', 'createdOn'];

    public function __construct()
    {
        parent::__construct();
        $this->validate = [
            'amendment_history_id' => [
                'required' => true,
                'allowEmpty' => false,
                'rule' => 'numeric',
                'message' => sprintf($this->ci->lang->line('cannot_be_blank_rule'), $this->ci->lang->line('amendment_history_id'))
            ],
            'contract_id' => [
                'required' => true,
                'allowEmpty' => false,
                'rule' => 'numeric',
                'message' => sprintf($this->ci->lang->line('cannot_be_blank_rule'), $this->ci->lang->line('contract_id'))
            ],
            'field_name' => [
                'required' => true,
                'allowEmpty' => false,
                'rule' => ['minLength', 1],
                'message' => sprintf($this->ci->lang->line('cannot_be_blank_rule'), $this->ci->lang->line('field_name'))
            ]

        ];
    }

    public function load_details_by_amendment($amendment_history_id)
    {
        $query = [
            'select' => 'id, amendment_history_id, contract_id, field_name, old_value, new_value, createdOn',
            'where' => ['amendment_history_id', $amendment_history_id],
            'order_by' => ['id asc']
        ];
        return parent::load_all($query);
    }
}

class mysql_Contract_amendment_history_details extends mysqli_Contract_amendment_history_details
{
}

class sqlsrv_Contract_amendment_history_details extends mysqli_Contract_amendment_history_details
{
    public function load_details_by_amendment($amendment_history_id)
    {
        $query = [
            'select' => 'id, amendment_history_id, contract_id, field_name, old_value, new_value, createdOn',
            'where' => ['amendment_history_id', $amendment_history_id],
            'order_by' => ['id asc']
        ];
        return parent::load_all($query);
    }
}

