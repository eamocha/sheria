<?php
defined('BASEPATH') or exit('No direct script access allowed');
class Conveyancing_activity extends My_Model_Factory
{

}
class mysqli_Conveyancing_activity_type extends My_model
{

    protected $_table = 'conveyancing_activity_type';
    protected $_primary_key = 'id';
    protected $_fieldsNames = [
        'id',
        'name',
        'description',
        'createdOn',
        'createdBy',
        'modifiedOn',
        'modifiedBy'
    ];

    public function __construct()
    {
        parent::__construct();

    }



}


class mysql_Conveyancing_activity_type extends mysqli_Conveyancing_activity_type
{

}
class sqlsrv_Conveyancing_activity_type extends mysql_Conveyancing_activity_type
{

}